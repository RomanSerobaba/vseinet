<?php

/*
 * Регистр остатков финансовых резервов.
 *
 * Показывает остаток финансов на источниках финансов.
 *
 * #Движения по регистру
 *
 * Документ, создающий движение делег, создаёт запись с указанием:
 *   - Источник финансов (financialResourceId);
 *   - сумма посткпления/выемки денежных средств (+/-delta).
 */

namespace FinanseBundle\Service;

use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\EntityManager;

class FinancialReserveRegisterService
{

    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function dropByRegistratorDId(int $documentId)
    {
        $queryList = "
            delete from financial_reserve_register as fer
            where fer.registrator_did = {$documentId}
            ";

        $this->em->createNativeQuery($queryList, new ResultSetMapping())
                ->execute();
    }

    public function appendRecord(int $registratorDId, \DateTime $registeredAt, FinancialReserveRegisterKeys $keys, FinancialReserveRegisterResources $resources)
    {

        $setParams = [];

        $setParams['registratorDId'] = $registratorDId;
        $setParams['registeredAt'] = $registeredAt;

        $setParams['financialResourceId'] = $keys->getFinancialResourceId();

        $setParams['delta'] = $resources->getDelta();

        $queryList = "
            insert into financial_reserve_register as fer
            (registrator_did, registered_at, financial_resource_id, delta) values
            (:registratorDId, :registeredAt, :financialResourceId, :delta)";

        $this->em->createNativeQuery($queryList, new ResultSetMapping())
                ->execute($setParams);
    }

}
