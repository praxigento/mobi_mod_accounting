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
    const AS_CUSTOMER = 'Customer';
    const AS_ID = 'Id';
    const AS_REF = 'Reference';
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
    public function getCustomer()
    {
        $result = parent::getData(self::AS_CUSTOMER);
        return $result;
    }

    /** @return int */
    public function getId()
    {
        $result = parent::getData(self::AS_ID);
        return $result;
    }

    /** @return string */
    public function getReference()
    {
        $result = parent::getData(self::AS_REF);
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

    public function setCustomer($data)
    {
        parent::setData(self::AS_CUSTOMER, $data);
    }

    public function setId($data)
    {
        parent::setData(self::AS_ID, $data);
    }

}