<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Repo\Entity\Data;

class Account
    extends \Praxigento\Core\Data\Entity\Base
{
    const ATTR_ASSET_TYPE_ID = 'asset_type_id';
    const ATTR_BALANCE = 'balance';
    const ATTR_CUST_ID = 'customer_id';
    const ATTR_ID = 'id';
    const ENTITY_NAME = 'prxgt_acc_account';

    /**
     * @return int
     */
    public function getAssetTypeId()
    {
        $result = parent::get(self::ATTR_ASSET_TYPE_ID);
        return $result;
    }

    /**
     * @return double
     */
    public function getBalance()
    {
        $result = parent::get(self::ATTR_BALANCE);
        return $result;
    }

    /**
     * @return int
     */
    public function getCustomerId()
    {
        $result = parent::get(self::ATTR_CUST_ID);
        return $result;
    }

    /**
     * @return int
     */
    public function getId()
    {
        $result = parent::get(self::ATTR_ID);
        return $result;
    }

    /**
     * @inheritdoc
     */
    public static function getPrimaryKeyAttrs()
    {
        return [self::ATTR_ID];
    }

    /**
     * @param int $data
     */
    public function setAssetTypeId($data)
    {
        parent::set(self::ATTR_ASSET_TYPE_ID, $data);
    }

    /**
     * @param double $data
     */
    public function setBalance($data)
    {
        parent::set(self::ATTR_BALANCE, $data);
    }

    /**
     * @param int $data
     */
    public function setCustomerId($data)
    {
        parent::set(self::ATTR_CUST_ID, $data);
    }

    /**
     * @param int $data
     */
    public function setId($data)
    {
        parent::set(self::ATTR_ID, $data);
    }

}