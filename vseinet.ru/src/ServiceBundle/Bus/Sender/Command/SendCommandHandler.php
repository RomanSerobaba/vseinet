<?php

namespace ServiceBundle\Bus\Sender\Command;

use AppBundle\Bus\Message\MessageHandler;
use Doctrine\ORM\EntityManager;
use ServiceBundle\Components\Email;
use ServiceBundle\Components\Sms;
use ServiceBundle\Services\AbstractSender;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class SendCommandHandler extends MessageHandler
{
    public function handle(SendCommand $command)
    {
        /**
         * Entity Manager
         *
         * @var EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $senderType = $command->type;
        $messageData = $command->data;

        if ($messageData['type'] === AbstractSender::QUEUE_TYPE_EMAIL) {
            $transport = new Email($senderType, $messageData);
        } elseif ($messageData['type'] === AbstractSender::QUEUE_TYPE_SMS) {
            $transport = new Sms($senderType, $messageData);
            $transport->setEm($em);
            $transport->setRabbitService($this->get('service.sender'));
        } else {
            throw new BadRequestHttpException('Unknown message type: '.$messageData['type']);
        }

        $result = $transport->run();

        file_put_contents('./command.log', 'type = '.$command->type.PHP_EOL.print_r($command->data, true).PHP_EOL, FILE_APPEND);
        file_put_contents('./command.log', 'result = '.print_r($result, true).PHP_EOL, FILE_APPEND);
    }
}