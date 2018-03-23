<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Repo\Data;

class Balance
    extends \Praxigento\Core\App\Repo\Data\Entity\Base
{
    const A_ACCOUNT_ID = 'account_id';
    const A_BALANCE_CLOSE = 'closing_balance';
    const A_BALANCE_OPEN = 'opening_balance';
    const A_DATE = 'date';
    const A_TOTAL_CREDIT = 'total_credit';
    const A_TOTAL_DEBIT = 'total_debit';
    const ENTITY_NAME = 'prxgt_acc_balance';

    /**
     * @return int
     */
    public function getAccountId()
    {
        $result = parent::get(self::A_ACCOUNT_ID);
        return $result;
    }

    /**
     * @return double
     */
    public function getBalanceClose()
    {
        $result = parent::get(self::A_BALANCE_CLOSE);
        return $result;
    }

    /**
     * @return double
     */
    public function getBalanceOpen()
    {
        $result = parent::get(self::A_BALANCE_OPEN);
        return $result;
    }

    /**
     * @return string
     */
    public function getDate()
    {
        $result = parent::get(self::A_DATE);
        return $result;
    }

    /** @inheritdoc */
    public static function getPrimaryKeyAttrs()
    {
        return [self::A_ACCOUNT_ID, self::A_DATE];
    }

    /**
     * @return double
     */
    public function getTotalCredit()
    {
        $result = parent::get(self::A_TOTAL_CREDIT);
        return $result;
    }

    /**
     * @return double
     */
    public function getTotalDebit()
    {
        $result = parent::get(self::A_TOTAL_DEBIT);
        return $result;
    }

    /**
     * @param int $data
     */
    public function setAccountId($data)
    {
        parent::set(self::A_ACCOUNT_ID, $data);
    }

    /**
     * @param double $data
     */
    public function setBalanceClose($data)
    {
        parent::set(self::A_BALANCE_CLOSE, $data);
    }

    /**
     * @param double $data
     */
    public function setBalanceOpen($data)
    {
        parent::set(self::A_BALANCE_OPEN, $data);
    }

    /**
     * @param string $data
     */
    public function setDate($data)
    {
        parent::set(self::A_DATE, $data);
    }

    /**
     * @param double $data
     */
    public function setTotalCredit($data)
    {
        parent::set(self::A_TOTAL_CREDIT, $data);
    }

    /**
     * @param double $data
     */
    public function setTotalDebit($data)
    {
        parent::set(self::A_TOTAL_DEBIT, $data);
    }
}