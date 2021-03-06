<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Repo\Data\Log\Change;

/**
 * Log for change balance operations performed by customers.
 */
class Customer
    extends \Praxigento\Core\App\Repo\Data\Entity\Base
{
    const A_CUST_REF = 'customer_ref';
    const A_OPER_REF = 'operation_ref';
    const ENTITY_NAME = 'prxgt_acc_log_change_customer';

    /** @return int */
    public function getCustomerRef()
    {
        $result = parent::get(self::A_CUST_REF);
        return $result;
    }

    /** @return int */
    public function getOperationRef()
    {
        $result = parent::get(self::A_OPER_REF);
        return $result;
    }

    public static function getPrimaryKeyAttrs()
    {
        return [self::A_OPER_REF];
    }

    /** @param int $data */
    public function setCustomerRef($data)
    {
        parent::set(self::A_CUST_REF, $data);
    }

    /** @param int $data */
    public function setOperationRef($data)
    {
        parent::set(self::A_OPER_REF, $data);
    }

}