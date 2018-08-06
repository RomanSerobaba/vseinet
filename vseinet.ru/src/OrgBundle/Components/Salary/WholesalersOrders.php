<?php

namespace OrgBundle\Components\Salary;

use AppBundle\Entity\Role;

class WholesalersOrders extends AllOrders
{
    protected function init()
    {
        parent::init();

        $this->from['oi'] = 'INNER JOIN OrderBundle:OrderItem AS oi WITH sr.orderItemId = oi.id';
        $this->from['co'] = 'INNER JOIN OrderBundle:ClientOrder AS co WITH oi.orderId = co.orderId';

        $this->from['uts']= 'INNER JOIN AppBundle:UserToSubrole AS uts WITH co.userId = uts.userId';
        $this->from['sr'] = 'INNER JOIN AppBundle:Subrole AS sr WITH uts.subroleId = sr.id';
        $this->from['ur'] = 'INNER JOIN AppBundle:Role AS ur WITH sr.roleId = ur.id';

        $this->clause[] = 'ur.code IN (:wholesaler, :franchiser)';
        $this->params['wholesaler'] = Role::CODE_WHOLESALER;
        $this->params['franchiser'] = Role::CODE_FRANCHISER;
    }
}