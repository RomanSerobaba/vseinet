<?php
/*
 * Регистр расходов.
 *
 * Показывает расходы/доходы организации.
 * ! Кроме работы с заказами клиентов.
 *
 * #Движения по регистру
 *
 * Документ, создающий расход/доход, создаёт запись с указанием:
 *   - статьи расхода/дохода (itemOfExpensesId);
 *   - подразделение к которому относится расход/доход (orgDepartmentId);
 *   - финансовый контрагент приведший к расходу/доходу (financialCounteragentId);
 *   - оборудование, обслуживание которого привело к расходу (equipmentId);
 *   - полной суммы расхода/дохода (-/+delta).
 *
 * # Анализ остатка регистра
 *
 * Никогда не сворачивается. Надо подумать о переполнении разрядной сетки и периодичности её очистки.
 *
 */
namespace FinanseBundle\Service;

use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\EntityManager;

class FinancialExpensesRegisterService
{

    private $em;


    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function dropByRegistratorDId(int $documentId)
    {
        $queryList = "
            delete from financial_expenses_register as fer
            where fer.registrator_did = {$documentId}
            ";

        $this->em->createNativeQuery($queryList, new ResultSetMapping())
                ->execute();
    }

    public function appendRecord(int $registratorDId, \DateTime $registeredAt, FinancialExpensesRegisterKeys $keys, FinancialExpensesRegisterResources $resources)
    {

        $setParams = [];

        $setParams['registratorDId'] = $registratorDId;
        $setParams['registeredAt'] = $registeredAt;

        $setParams['itemOfExpensesId'] = $keys->getItemOfExpensesId();
        $setParams['orgDepartmentId'] = $keys->getOrgDepartmentId();
        $setParams['financialCounteragentId'] = $keys->getFinancialCounteragentId();
        $setParams['equipmentId'] = $keys->getEquipmentId();

        $setParams['delta'] = $resources->getDelta();

        $queryList = "
            insert into financial_expenses_register as fer
            (registrator_did, registered_at, item_of_expenses_id, org_department_id, financial_counteragent_id, equipment_id, delta) values
            (:registratorDId, :registeredAt, :itemOfExpensesId, :orgDepartmentId, :financialCounteragentId, :equipmentId, :delta)";

        $this->em->createNativeQuery($queryList, new ResultSetMapping())
                ->execute($setParams);
    }

}
