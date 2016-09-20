<?php
/**
 * Aggregate for account data for accounts grid.
 *
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Data\Agg;


class Account
    extends \Flancer32\Lib\DataObject
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
        $result = parent::getData(self::AS_ASSET);
        return $result;
    }

    /** @return double */
    public function getBalance()
    {
        $result = parent::getData(self::AS_BALANCE);
        return $result;
    }

    /** @return string */
    public function getCustomerEmail()
    {
        $result = parent::getData(self::AS_CUST_EMAIL);
        return $result;
    }

    /** @return string */
    public function getCustomerName()
    {
        $result = parent::getData(self::AS_CUST_NAME);
        return $result;
    }

    /** @return int */
    public function getId()
    {
        $result = parent::getData(self::AS_ID);
        return $result;
    }

    public function setAsset($data)
    {
        parent::setData(self::AS_ASSET, $data);
    }

    public function setBalance($data)
    {
        parent::setData(self::AS_BALANCE, $data);
    }

    public function setCustomerEmail($data)
    {
        parent::setData(self::AS_CUST_EMAIL, $data);
    }

    public function setCustomerName($data)
    {
        parent::setData(self::AS_CUST_NAME, $data);
    }

    public function setId($data)
    {
        parent::setData(self::AS_ID, $data);
    }

}