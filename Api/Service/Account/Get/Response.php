<?php
/**
 *
 */

namespace Praxigento\Accounting\Api\Service\Account\Get;

use Praxigento\Accounting\Repo\Data\Account as EAccount;

class Response
    extends \Praxigento\Core\Data
{
    const ASSET_TYPE_ID = EAccount::A_ASSET_TYPE_ID;
    const BALANCE = EAccount::A_BALANCE;
    const CUSTOMER_ID = EAccount::A_CUST_ID;
    const ID = EAccount::A_ID;

    /** @return int */
    public function getAssetTypeId()
    {
        $result = parent::get(self::ASSET_TYPE_ID);
        return $result;
    }

    /** @return float */
    public function getBalance()
    {
        $result = parent::get(self::BALANCE);
        return $result;
    }

    /** @return int */
    public function getCustomerId()
    {
        $result = parent::get(self::CUSTOMER_ID);
        return $result;
    }

    /** @return int */
    public function getId()
    {
        $result = parent::get(self::ID);
        return $result;
    }

    /** @param int $data */
    public function setAssetTypeId($data)
    {
        parent::set(self::ASSET_TYPE_ID, $data);
    }

    /** @param float $data */
    public function setBalance($data)
    {
        parent::set(self::BALANCE, $data);
    }

    /** @param int $data */
    public function setCustomerId($data)
    {
        parent::set(self::CUSTOMER_ID, $data);
    }

    /** @param int $data */
    public function setId($data)
    {
        parent::set(self::ID, $data);
    }


}