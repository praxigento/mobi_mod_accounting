<?php
/**
 * Aggregate for account data for accounts grid.
 *
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Data\Agg;


class Account
    extends \Praxigento\Core\Data
{
    /**#@+
     * Aliases for data attributes.
     */
    const AS_ASSET = 'Asset';
    const AS_BALANCE = 'Balance';
    const AS_CUST_EMAIL = 'CustEmail';
    const AS_CUST_NAME = 'CustName';
    const AS_ID = 'Id';
    /**#@- */

    /** @return string */
    public function getAsset()
    {
        $result = parent::get(self::AS_ASSET);
        return $result;
    }

    /** @return double */
    public function getBalance()
    {
        $result = parent::get(self::AS_BALANCE);
        return $result;
    }

    /** @return string */
    public function getCustomerEmail()
    {
        $result = parent::get(self::AS_CUST_EMAIL);
        return $result;
    }

    /** @return string */
    public function getCustomerName()
    {
        $result = parent::get(self::AS_CUST_NAME);
        return $result;
    }

    /** @return int */
    public function getId()
    {
        $result = parent::get(self::AS_ID);
        return $result;
    }

    public function setAsset($data)
    {
        parent::set(self::AS_ASSET, $data);
    }

    public function setBalance($data)
    {
        parent::set(self::AS_BALANCE, $data);
    }

    public function setCustomerEmail($data)
    {
        parent::set(self::AS_CUST_EMAIL, $data);
    }

    public function setCustomerName($data)
    {
        parent::set(self::AS_CUST_NAME, $data);
    }

    public function setId($data)
    {
        parent::set(self::AS_ID, $data);
    }

}