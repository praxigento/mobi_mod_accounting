<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Lib\Entity;

use Praxigento\Core\Data\Entity\Base as EntityBase;

class Transaction extends EntityBase
{
    const ATTR_CREDIT_ACC_ID = 'credit_acc_id';
    /* date when asset transaction should change balances (can be in the past) */
    const ATTR_DATE_APPLIED = 'date_applied';
    const ATTR_DEBIT_ACC_ID = 'debit_acc_id';
    const ATTR_ID = 'id';
    const ATTR_OPERATION_ID = 'operation_id';
    const ATTR_VALUE = 'value';
    const ENTITY_NAME = 'prxgt_acc_transaction';

    /**
     * @return int
     */
    public function getCreditAccId()
    {
        $result = parent::getData(self::ATTR_CREDIT_ACC_ID);
        return $result;
    }

    /**
     * @return string
     */
    public function getDateApplied()
    {
        $result = parent::getData(self::ATTR_DATE_APPLIED);
        return $result;
    }

    /**
     * @return int
     */
    public function getDebitAccId()
    {
        $result = parent::getData(self::ATTR_DEBIT_ACC_ID);
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getEntityName()
    {
        return self::ENTITY_NAME;
    }

    /**
     * @return int
     */
    public function getId()
    {
        $result = parent::getData(self::ATTR_ID);
        return $result;
    }

    /**
     * @return int
     */
    public function getOperationId()
    {
        $result = parent::getData(self::ATTR_OPERATION_ID);
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getPrimaryKeyAttrs()
    {
        return [self::ATTR_ID];
    }

    /**
     * @return double
     */
    public function getValue()
    {
        $result = parent::getData(self::ATTR_VALUE);
        return $result;
    }

    /**
     * @param int $data
     */
    public function setCreditAccId($data)
    {
        parent::setData(self::ATTR_CREDIT_ACC_ID, $data);
    }

    /**
     * @param string $data
     */
    public function setDateApplied($data)
    {
        parent::setData(self::ATTR_DATE_APPLIED, $data);
    }

    /**
     * @param int $data
     */
    public function setDebitAccId($data)
    {
        parent::setData(self::ATTR_DEBIT_ACC_ID, $data);
    }

    /**
     * @param int $data
     */
    public function setId($data)
    {
        parent::setData(self::ATTR_ID, $data);
    }

    /**
     * @param int $data
     */
    public function setOperationId($data)
    {
        parent::setData(self::ATTR_OPERATION_ID, $data);
    }

    /**
     * @param double $data
     */
    public function setValue($data)
    {
        parent::setData(self::ATTR_VALUE, $data);
    }
}