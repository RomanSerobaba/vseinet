<?php

namespace AppBundle\Bus\Main\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\ErrorReport;

class ErrorReportCommandHandler extends MessageHandler
{
    public function handle(ErrorReportCommand $command)
    {
        $request = $this->get('request_stack')->getMasterRequest();

        $report = new ErrorReport();
        if ($user = $this->getUser()) {
            $report->setSentedBy($user->getId());
        }
        $report->setIp($request->getClientIp());
        $report->setSentedAt(new \DateTime());
        $report->setUrl($command->url);
        $report->setNode($command->node);
        $report->setText($command->text);

        $em = $this->getDoctrine()->getManager();

        $em->persist($report);
        $em->flush();
    }
}
