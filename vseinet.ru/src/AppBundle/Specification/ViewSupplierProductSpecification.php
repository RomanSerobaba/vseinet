<?php

namespace AppBundle\Specification;


use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ViewSupplierProductSpecification
{
    /**
     * @param null $baseProductId
     * @param null $supplierId
     * @param null $code
     * @param null $name
     *
     * @return string
     */
    public function buildLeftJoin($baseProductId = null, $supplierId = null, $code = null, $name= null)
    {
        return 'LEFT ' . $this->_getJoin($this->_buildFilter($baseProductId, $supplierId, $code, $name));
    }

    /**
     * @param null $baseProductId
     * @param null $supplierId
     * @param null $code
     * @param null $name
     *
     * @return string
     */
    public function buildInnerJoin($baseProductId = null, $supplierId = null, $code = null, $name= null)
    {
        return 'INNER ' . $this->_getJoin($this->_buildFilter($baseProductId, $supplierId, $code, $name));
    }

    /**
     * @param bool $isAnd
     *
     * @return string
     */
    public function buildWhere($isAnd = true)
    {
        return ($isAnd ? ' AND' : '') . ' sp2.id IS NULL';
    }

    /**
     * @param null $baseProductId
     * @param null $supplierId
     * @param null $code
     * @param null $name
     *
     * @return string
     */
    private function _buildFilter($baseProductId = null, $supplierId = null, $code = null, $name= null)
    {
        if (empty($baseProductId) && empty($supplierId) && empty($code) && empty($name)) {
            throw new \BadFunctionCallException('Не указаны обязательные параметры');
        }

        $filter = [];

        if (!empty($baseProductId)) {
            $filter[] = 'sp.base_product_id = ' . $baseProductId;
        }

        if (!empty($supplierId)) {
            $filter[] = 'sp.supplier_id = ' . $supplierId;
        }

        if (!empty($code)) {
            $filter[] = 'sp.code = ' . $code;
        }

        if (!empty($name)) {
            $filter[] = 'sp.name = ' . $name;
        }

        return implode(' AND ', $filter);
    }

    /**
     * @param string $filter
     *
     * @return string
     */
    private function _getJoin(string $filter)
    {
        return 'JOIN supplier_product sp ON '.$filter.'  
			LEFT JOIN supplier_product sp2 ON sp2.base_product_id = sp.base_product_id AND sp2.supplier_id = sp.supplier_id AND (sp2.product_availability_code > sp.product_availability_code 
			    OR sp2.product_availability_code = sp.product_availability_code AND sp2.price < sp.price 
			    OR sp2.product_availability_code = sp.product_availability_code AND sp2.price = sp.price AND sp2.id > sp.id)'
//            .'LEFT JOIN supplier_category_path scp ON scp.id = sp.supplier_category_id AND scp.level = 1 AND scp.id = scp.pid
//            LEFT JOIN supplier_category sc ON sc.id = scp.id'
        ;
    }
}