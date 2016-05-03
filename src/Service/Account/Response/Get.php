<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Service\Account\Response;

use Praxigento\Accounting\Data\Entity\Account;

class Get extends \Praxigento\Core\Service\Base\Response
{
    /**
     * @return int
     */
    public function getAssetTypeId()
    {
        $result = parent::getData(Account::ATTR_ASSET_TYPE_ID);
        return $result;
    }

    /**
     * @return double
     */
    public function getBalance()
    {
        $result = parent::getData(Account::ATTR_BALANCE);
        return $result;
    }

    /**
     * @return int
     */
    public function getCustomerId()
    {
        $result = parent::getData(Account::ATTR_CUST_ID);
        return $result;
    }

    /**
     * @return int
     */
    public function getId()
    {
        $result = parent::getData(Account::ATTR_ID);
        return $result;
    }

    /**
     * @param int $data
     */
    public function setAssetTypeId($data)
    {
        parent::setData(Account::ATTR_ASSET_TYPE_ID, $data);
    }

    /**
     * @param double $data
     */
    public function setBalance($data)
    {
        parent::setData(Account::ATTR_BALANCE, $data);
    }

    /**
     * @param int $data
     */
    public function setCustomerId($data)
    {
        parent::setData(Account::ATTR_CUST_ID, $data);
    }

    /**
     * @param int $data
     */
    public function setId($data)
    {
        parent::setData(Account::ATTR_ID, $data);
    }
}