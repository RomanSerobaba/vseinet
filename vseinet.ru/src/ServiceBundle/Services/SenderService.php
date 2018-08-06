<?php

namespace ServiceBundle\Services;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class SenderService extends MessageHandler
{
    /**
     * @param string $method
     * @param array  $params
     * @param bool   $isEmulate
     *
     * @return array
     */
    public function send(string $method, array $params, $isEmulate = true) : array
    {
        $sender = $this->_factory($method);

        if (!($sender instanceof AbstractSender)) {
            throw new BadRequestHttpException('Класс отправителя сообщений не найден ('.$method.')');
        }

        $sender->configure($this->getDoctrine()->getManager(), $this->get('twig'), $this->get('old_sound_rabbit_mq.execute.command_producer'));
        $sender->setIsEmulate($isEmulate);
        $sender->process($params);

        return $sender->publish($method);
    }

    /**
     * @param string $serviceName
     *
     * @return AbstractSender
     */
    private function _factory(string $serviceName) : AbstractSender
    {
        $serviceName = "ServiceBundle\\Services\\Senders\\" . $this->_convertName($serviceName);

        return new $serviceName();
    }

    /**
     * @param string $serviceName
     *
     * @return string
     */
    private function _convertName(string $serviceName) : string
    {
        $names = explode('_', $serviceName);

        foreach ($names as $i => $name) {
            $names[$i] = ucfirst($name);
        }

        return implode('', $names);
    }
}