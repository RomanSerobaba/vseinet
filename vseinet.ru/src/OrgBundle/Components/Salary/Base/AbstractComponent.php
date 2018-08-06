<?php

namespace OrgBundle\Components\Salary\Base;

use Doctrine\ORM\EntityManager;
use OrgBundle\Entity\Activity;
use OrgBundle\Entity\ActivityIndex;

abstract class AbstractComponent
{
    /**@var EntityManager $em */
    protected $em;

    protected $select;
    protected $from;
    protected $clause;
    protected $params;
    protected $group;
    protected $order;

    /**
     * SalaryComponent constructor.
     * @param EntityManager $em
     */
    public function __construct($em)
    {
        $this->em = $em;
    }

    protected function init() {
        $this->select = $this->from = $this->clause = $this->params = $this->group = $this->order = [];
    }

    /**
     * @param ActivityIndex $activityIndex
     */
    protected function constructIndexQuery(ActivityIndex $activityIndex) {}

    /**
     * @param string $since
     * @param string $till
     */
    protected function constructDateQuery($since, $till) {}

    /**
     * @param int[] $employeeId
     */
    protected function constructEmployeeQuery($employeeId) {}

    /**
     * @param int[] $departmentId
     */
    protected function constructDepartmentQuery($departmentId) {}

    /**
     * @param int[] $pointId
     */
    protected function constructPointQuery($pointId) {}

    /**
     * @param int[] $cityId
     */
    protected function constructCityQuery($cityId) {}

    /**
     * @param int[] $areaId
     */
    protected function constructAreaQuery($areaId) {}

    /**
     * @param int[] $categoryId
     */
    protected function constructCategoryQuery($categoryId) {}

    /**
     * @param int $interval
     */
    protected function constructIntervalQuery($interval) {}

    /**
     * @return array|null
     */
    protected function executeQuery()
    {
        if ($this->select && $this->from) {
            $query = $this->em->createQuery('
                SELECT ' . implode(', ', $this->select) . '
                FROM ' . implode(' ', $this->from) .
                ($this->clause ? ' WHERE (' . implode(') AND (', $this->clause) . ')' : '') .
                ($this->group ? ' GROUP BY ' . implode(', ', $this->group) : '') .
                ($this->order ? ' ORDER BY ' . implode(', ', $this->order) : '')
            );

            if ($this->params) {
                $query->setParameters($this->params);
            }

            return $query->getArrayResult();
        }
        return null;
    }

    /**
     * @param Activity $activity
     * @param string $since
     * @param string $till
     * @return array|null
     */
    public function getFact(Activity $activity, $since, $till)
    {
        $this->init();
        $this->constructIndexQuery($activity->getActivityIndex());
        $this->constructDateQuery($since, $till);

        if ($activity->getActivityAreaValue()) {
            switch ($activity->getActivityArea()->getCode()) {
                case 'employee':
                    $this->constructEmployeeQuery([$activity->getActivityAreaValue()]);
                    break;
                case 'department':
                    $this->constructDepartmentQuery([$activity->getActivityAreaValue()]);
                    break;
                case 'point':
                    $this->constructPointQuery([$activity->getActivityAreaValue()]);
                    break;
                case 'city':
                    $this->constructCityQuery([$activity->getActivityAreaValue()]);
                    break;
                case 'area':
                    $this->constructAreaQuery([$activity->getActivityAreaValue()]);
                    break;
            }
        }

        if ($activity->getCategoryId()) {
            $this->constructCategoryQuery([$activity->getCategoryId()]);
        }

        if ($activity->getActivityObject()->getHasInterval()) {
            $this->constructIntervalQuery($activity->getIntervalMonth());
        }

        return $this->executeQuery();
    }
}