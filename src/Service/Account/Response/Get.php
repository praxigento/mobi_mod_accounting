<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Service\Account\Response;

use Praxigento\Accounting\Repo\Entity\Data\Account;

class Get extends \Praxigento\Core\App\Service\Base\Response
{
    /**
     * @return int
     */
    public function getAssetTypeId()
    {
        $result = parent::get(Account::ATTR_ASSET_TYPE_ID);
        return $result;
    }

    /**
     * @return double
     */
    public function getBalance()
    {
        $result = parent::get(Account::ATTR_BALANCE);
        return $result;
    }

    /**
     * @return int
     */
    public function getCustomerId()
    {
        $result = parent::get(Account::ATTR_CUST_ID);
        return $result;
    }

    /**
     * @return int
     */
    public function getId()
    {
        $result = parent::get(Account::ATTR_ID);
        return $result;
    }

}