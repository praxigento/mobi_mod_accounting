<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Data\Entity\Log\Change;

/**
 * Log for change balance operations performed by customers.
 */
class Customer
    extends \Praxigento\Core\Data\Entity\Base
{
    const ATTR_CUST_REF = 'customer_ref';
    const ATTR_OPER_REF = 'operation_ref';
    const ENTITY_NAME = 'prxgt_acc_log_change_customer';

    /** @return int */
    public function getCustomerRef()
    {
        $result = parent::getData(self::ATTR_CUST_REF);
        return $result;
    }

    /** @return int */
    public function getOperationRef()
    {
        $result = parent::getData(self::ATTR_OPER_REF);
        return $result;
    }

    /** @param int $data */
    public function setCustomerRef($data)
    {
        parent::setData(self::ATTR_CUST_REF, $data);
    }

    /** @param int $data */
    public function setOperationRef($data)
    {
        parent::setData(self::ATTR_OPER_REF, $data);
    }

}