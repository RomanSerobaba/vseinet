<?php

namespace FinanseBundle\Bus\BankOperationDoc\Command;

use AppBundle\Bus\Message\MessageHandler;
use Doctrine\ORM\Query\ResultSetMapping;
use FinanseBundle\Entity\BankOperationDoc;
use FinanseBundle\Bus\BankOperationDoc\BankOperationDocRegistration;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use ReservesBundle\AddOn\TempStorage;

class ImportCommandHandler extends MessageHandler
{
    public function handle(ImportCommand $command) 
    {

        $currentUser = $this->get('user.identity')->getUser();
        $em = $this->getDoctrine()->getManager();

        $uploadFile = $command->uploadFile->openFile('r');

        if ($uploadFile->eof())
            throw new BadRequestHttpException('Пустой файл');

        $formatFile = chop($uploadFile->fgets());

        if ('1CClientBankExchange' != $formatFile)
            throw new BadRequestHttpException('Неверный формат файла');

        $formatVersion = chop($uploadFile->fgets());
        if (false === strpos($formatVersion, "=1.01"))
            throw new BadRequestHttpException('Неизвестная версия формата файла');

        $codePage = chop($uploadFile->fgets());

        if (false === strpos($codePage, "indows") and false === strpos($codePage, "INDOWS")) {
            $codePage = 'CP866';
        }else{
            $codePage = 'CP1251';
        }

        // общие параметры загрузки
        $fromDate = "";
        $toDate = "";
        $account = "";
        $financialResourceId = "";
        $startSumma = "";

        $currentDocument = [];

        while (!$uploadFile->eof()) {

            $inData = explode('=', iconv($codePage, 'UTF-8', chop($uploadFile->fgets())));

            switch ($inData[0]) {
                case "ДатаНачала":
                    if ("" == $fromDate) 
                        $fromDate = substr($inData[1], 6, 4).'-'.substr($inData[1], 3, 2).'-'.substr($inData[1], 0, 2);
                    break;

                case "ДатаКонца":
                    if ("" == $toDate) 
                        $toDate = substr($inData[1], 6, 4).'-'.substr($inData[1], 3, 2).'-'.substr($inData[1], 0, 2);
                    break;

                case "НачальныйОстаток":
                    if ("" == $startSumma)
                        $startSumma = $inData[1] * 100;
                    break;

                case "РасчСчет":
                    if ("" == $account) 
                        $account = $inData[1];
                    break;

                case "СекцияДокумент":
                    $currentDocument['type'] = $inData[1];
                    break;

                case "Номер":
                    $currentDocument['number'] = $inData[1];
                    break;

                case "Дата":
                    $currentDocument['strDate'] = $inData[1];
                    $currentDocument['date'] = substr($inData[1], 6, 4).'-'.substr($inData[1], 3, 2).'-'.substr($inData[1], 0, 2);
                    break;

                case "Сумма":
                    $currentDocument['amount'] = $inData[1] * 100;
                    break;

                case "НазначениеПлатежа":
                    $currentDocument['description'] = $inData[1];
                    break;

                case "ПлательщикИНН":
                    $currentDocument['fromINN'] = $inData[1];
                    break;

                case "ПолучательИНН":
                    $currentDocument['toINN'] = $inData[1];
                    break;

                case "ПлательщикРасчСчет":
                    $currentDocument['fromAccount'] = $inData[1];
                    break;

                case "ПолучательРасчСчет":
                    $currentDocument['toAccount'] = $inData[1];
                    break;

                case "КонецДокумента":

                    if ("" == $financialResourceId) {
                        
                        $financialResourceId = $this->getFinancialResourceId($account, $em);

                    }
                    
                    $createdAt = new \DateTime($currentDocument['date']." 00:00:00");
                    
                    // Поиск ранее загруженного документа
                    
                    $document = $em->getRepository(BankOperationDoc::class)->findOneBy([
                        'number' => $currentDocument['number'],
                        'financialResourceId' => $financialResourceId,
                        'createdAt' => $createdAt]);
                    if (!$document instanceof BankOperationDoc) {
                        
                        // Создание нового документа

                        $document = new BankOperationDoc();

                        $document->setNumber($currentDocument['number']);
                        $document->setTitle($currentDocument['type'] ." №". $currentDocument['number'] ." от ". $currentDocument['strDate']);
                        $document->setCreatedBy($currentUser->getId());
                        $document->setCreatedAt($createdAt);
                        $document->setFinancialResourceId($financialResourceId);
                        $document->setStatusCode('new');

                        if ('Банковский ордер' == $currentDocument['type']) {

                            $document->setBancDocType('bankOrder');

                        }elseif ('Платежное поручение' == $currentDocument['type']) {

                            $document->setBancDocType('paymentOrder');

                        }

                        if ($account == $currentDocument['fromAccount']) {

                            $document->setAmount(-$currentDocument['amount']);
                            $document->setFinancialCounteragent($this->getFinancialCounterAgentId($currentDocument['toINN'], $em));

                        }else{

                            $document->setAmount($currentDocument['amount']);
                            $document->setFinancialCounteragent($this->getFinancialCounterAgentId($currentDocument['fromINN'], $em));

                        }

                        $document->setDescription($currentDocument['description']);

                        $em->persist($document);
                        $em->flush();

                        BankOperationDocRegistration::Registration($document, $em, $currentUser);

                    }else{

                        if (abs($document->getAmount()) != abs($currentDocument['amount']))
                            throw new BadRequestHttpException('Сумма документа №'. $currentDocument['number'] .' изменилась');

                    }

                    $currentDocument = [];
                    break;

            }

        }

        $tempStorage = new TempStorage();
        $tempStorage->setData(json_encode([
            'fromDate'            => $fromDate,
            'toDate'              => $toDate,
            'inStatuses'          => ['new'],
            'inFinancialResourcesCodes' => ['settlement_account'],
            'inFinancialResourcesIds' => [$financialResourceId],
        ]), $command->uuid);
        
    }
    
    private function getFinancialCounterAgentId($inn, $em)
    {
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('id', 'id', 'internet');
        $result = $em->createNativeQuery("
            select
                fc.id as id
            from counteragent ca
            inner join financial_counteragent fc on fc.counteragent_id = ca.id
            where ca.tin='{$inn}';",
            $rsm)
                    ->getOneOrNullResult();

        if (empty($result)) {
            return null;
        }else{
            return $result['id'];
        }
    }

    private function getFinancialResourceId($account, $em)
    {
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('id', 'id', 'integer');
        $result = $em->createNativeQuery("
            select
                i.id
            from
                settlement_account i
            where
                i.number = '{$account}';", $rsm)
                        ->getOneOrNullResult();

        if (empty($result)) {
            return null;
        }else{
            return $result['id'];
        }
    }

}
