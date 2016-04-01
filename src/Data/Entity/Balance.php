<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Data\Entity;

use Praxigento\Core\Data\Entity\Base as EntityBase;

class Balance extends EntityBase
{
    const ATTR_ACCOUNT_ID = 'account_id';
    const ATTR_BALANCE_CLOSE = 'closing_balance';
    const ATTR_BALANCE_OPEN = 'opening_balance';
    const ATTR_DATE = 'date';
    const ATTR_TOTAL_CREDIT = 'total_credit';
    const ATTR_TOTAL_DEBIT = 'total_debit';
    const ENTITY_NAME = 'prxgt_acc_balance';

    /**
     * @return int
     */
    public function getAccountId()
    {
        $result = parent::getData(self::ATTR_ACCOUNT_ID);
        return $result;
    }

    /**
     * @return double
     */
    public function getBalanceClose()
    {
        $result = parent::getData(self::ATTR_BALANCE_CLOSE);
        return $result;
    }

    /**
     * @return double
     */
    public function getBalanceOpen()
    {
        $result = parent::getData(self::ATTR_BALANCE_OPEN);
        return $result;
    }

    /**
     * @return string
     */
    public function getDate()
    {
        $result = parent::getData(self::ATTR_DATE);
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
     * @inheritdoc
     */
    public function getPrimaryKeyAttrs()
    {
        return [self::ATTR_ACCOUNT_ID, self::ATTR_DATE];
    }

    /**
     * @return double
     */
    public function getTotalCredit()
    {
        $result = parent::getData(self::ATTR_TOTAL_CREDIT);
        return $result;
    }

    /**
     * @return double
     */
    public function getTotalDebit()
    {
        $result = parent::getData(self::ATTR_TOTAL_DEBIT);
        return $result;
    }

    /**
     * @param int $data
     */
    public function setAccountId($data)
    {
        parent::getData(self::ATTR_ACCOUNT_ID, $data);
    }

    /**
     * @param double $data
     */
    public function setBalanceClose($data)
    {
        parent::getData(self::ATTR_BALANCE_CLOSE, $data);
    }

    /**
     * @param double $data
     */
    public function setBalanceOpen($data)
    {
        parent::getData(self::ATTR_BALANCE_OPEN, $data);
    }

    /**
     * @param string $data
     */
    public function setDate($data)
    {
        parent::getData(self::ATTR_DATE, $data);
    }

    /**
     * @param double $data
     */
    public function setTotalCredit($data)
    {
        parent::getData(self::ATTR_TOTAL_CREDIT, $data);
    }

    /**
     * @param double $data
     */
    public function setTotalDebit($data)
    {
        parent::getData(self::ATTR_TOTAL_DEBIT, $data);
    }
}