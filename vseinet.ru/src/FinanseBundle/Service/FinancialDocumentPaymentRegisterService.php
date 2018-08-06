<?php
/*
 * Регистр оплаты документов.
 *
 * Показывает суммы к оплате по документам.
 *
 * #Движения по регистру
 *
 * Документ, требующий оплаты создаёт запись с указанием:
 *   - получателя платежа (financialCounteragentId);
 *   - ссылки на себя (settlementDocumentDId);
 *   - полной суммы платежа (+delta).
 *
 * Документ, оплаты создаёт запись с указанием:
 *   - получателя платежа (financialCounteragentId);
 *   - ссылки на документ-основание платежа (settlementDocumentDId);
 *   - суммы платежа (-delta).
 *
 * # Анализ остатка регистра
 *
 * Если свернутый остаток (sum(delta)) по документу-основанию платежа (group by settlementDocumentDId)
 * больше 0, то докмуент оплачен не полностью.
 *
 * Если свернутый остаток (sum(delta)) по документу-основанию платежа (group by settlementDocumentDId)
 * равен 0, то докмуент оплачен полностью.
 *
 * Если свернутый остаток (sum(delta)) по документу-основанию платежа (group by settlementDocumentDId)
 * больше 0 - зовите фиксиков.
 */
namespace FinanseBundle\Service;

use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\EntityManager;

class FinancialDocumentPaymentRegisterService
{

    private $em;


    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function dropByRegistratorDId(int $documentId)
    {
        $queryList = "
            delete from financial_document_payment_register as fer
            where fer.registrator_did = {$documentId}
            ";

        $this->em->createNativeQuery($queryList, new ResultSetMapping())
                ->execute();
    }

    public function appendRecord(int $registratorDId, \DateTime $registeredAt, FinancialDocumentPaymentRegisterKeys $keys, FinancialDocumentPaymentRegisterResources $resources)
    {

        $setParams = [];

        $setParams['registratorDId'] = $registratorDId;
        $setParams['registeredAt'] = $registeredAt;

        $setParams['financialCounteragentId'] = $keys->getFinancialCounteragentId();
        $setParams['settlementDocumentDId'] = $keys->getSettlementDocumentDId();

        $setParams['delta'] = $resources->getDelta();

        $queryList = "
            insert into financial_document_payment_register as fer
            (registrator_did, registered_at, financial_counteragent_id, settlement_document_did, delta) values
            (:registratorDId, :registeredAt, :financialCounteragentId, :settlementDocumentDId, :delta)";

        $this->em->createNativeQuery($queryList, new ResultSetMapping())
                ->execute($setParams);
    }

}
