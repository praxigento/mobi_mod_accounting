<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Repo\Data;

class Transaction
    extends \Praxigento\Core\App\Repo\Data\Entity\Base
{
    const A_CREDIT_ACC_ID = 'credit_acc_id';
    /* date when asset transaction should change balances (can be in the past) */
    const A_DATE_APPLIED = 'date_applied';
    const A_DEBIT_ACC_ID = 'debit_acc_id';
    const A_ID = 'id';
    const A_NOTE = 'note';
    const A_OPERATION_ID = 'operation_id';
    const A_VALUE = 'value';
    const ENTITY_NAME = 'prxgt_acc_transaction';

    /** @return int */
    public function getCreditAccId()
    {
        $result = parent::get(self::A_CREDIT_ACC_ID);
        return $result;
    }

    /** @return string */
    public function getDateApplied()
    {
        $result = parent::get(self::A_DATE_APPLIED);
        return $result;
    }

    /** @return int */
    public function getDebitAccId()
    {
        $result = parent::get(self::A_DEBIT_ACC_ID);
        return $result;
    }

    /** @return int */
    public function getId()
    {
        $result = parent::get(self::A_ID);
        return $result;
    }

    /** @return string */
    public function getNote()
    {
        $result = parent::get(self::A_NOTE);
        return $result;
    }

    /** @return int */
    public function getOperationId()
    {
        $result = parent::get(self::A_OPERATION_ID);
        return $result;
    }

    public static function getPrimaryKeyAttrs()
    {
        return [self::A_ID];
    }

    /** @return double */
    public function getValue()
    {
        $result = parent::get(self::A_VALUE);
        return $result;
    }

    /** @param int $data */
    public function setCreditAccId($data)
    {
        parent::set(self::A_CREDIT_ACC_ID, $data);
    }

    /** @param string $data */
    public function setDateApplied($data)
    {
        parent::set(self::A_DATE_APPLIED, $data);
    }

    /** @param int $data */
    public function setDebitAccId($data)
    {
        parent::set(self::A_DEBIT_ACC_ID, $data);
    }

    /** @param int $data */
    public function setId($data)
    {
        parent::set(self::A_ID, $data);
    }

    /** @param string $data */
    public function setNote($data)
    {
        parent::set(self::A_NOTE, $data);
    }

    /** @param int $data */
    public function setOperationId($data)
    {
        parent::set(self::A_OPERATION_ID, $data);
    }

    /** @param double $data */
    public function setValue($data)
    {
        parent::set(self::A_VALUE, $data);
    }
}