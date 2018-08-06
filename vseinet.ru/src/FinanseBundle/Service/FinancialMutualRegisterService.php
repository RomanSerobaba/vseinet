<?php
/*
 * Регистр взаиморасчетов.
 *
 * Показывает суммы задолженностей финансовых контрагентов/финансовым контрагентам.
 *
 * #Движения по регистру
 *
 * Документ, проводящий взаиморасчет, создаёт запись с указанием:
 *   - финансовый контрагент (financialCounteragentId);
 *   - документ расчета (settlementDocumentDId);
 *   - сумма (+/-delta. '+' - дньги приходят в организацию; '-' - деньги приходят контрагенту).
 *
 * # Анализ остатка регистра
 *
 * Если свернутый остаток (sum(delta)) по контрагенту (group by financialCounteragentId)
 * больше 0, то организация должна контрагенту.
 *
 * Если свернутый остаток (sum(delta)) по контрагенту (group by financialCounteragentId)
 * равен 0, то задолженностей нет.
 *
 * Если свернутый остаток (sum(delta)) по контрагенту (group by financialCounteragentId)
 * больше 0, контрагент должен организации.
 */

namespace FinanseBundle\Service;

use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\EntityManager;

class FinancialMutualRegisterService
{

    private $em;


    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function dropByRegistratorDId(int $documentId)
    {
        $queryList = "
            delete from financial_mutual_register as fer
            where fer.registrator_did = {$documentId}
            ";

        $this->em->createNativeQuery($queryList, new ResultSetMapping())
                ->execute();
    }

    public function appendRecord(int $registratorDId, \DateTime $registeredAt, FinancialMutualRegisterKeys $keys, FinancialMutualRegisterResources $resources)
    {

        $setParams = [];

        $setParams['registratorDId'] = $registratorDId;
        $setParams['registeredAt'] = $registeredAt;

        $setParams['financialCounteragentId'] = $keys->getFinancialCounteragentId();
        $setParams['settlementDocumentDId'] = $keys->getSettlementDocumentDId();

        $setParams['delta'] = $resources->getDelta();

        $queryList = "
            insert into financial_mutual_register as fer
            (registrator_did, registered_at, financial_counteragent_id, settlement_document_did, delta) values
            (:registratorDId, :registeredAt, :financialCounteragentId, :settlementDocumentDId, :delta)";

        $this->em->createNativeQuery($queryList, new ResultSetMapping())
                ->execute($setParams);
    }

}
