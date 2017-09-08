<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Ui\DataProvider\Account2\Query;

use Praxigento\Accounting\Config as Cfg;
use Praxigento\Accounting\Repo\Entity\Data\Account;
use Praxigento\Accounting\Repo\Entity\Data\Type\Asset as TypeAsset;
use Praxigento\Accounting\Ui\DataProvider\Account2\Query\ItemsBuilder as ItemsBuilder;

class Mapper
    extends \Praxigento\Core\Repo\Query\Criteria\Def\Mapper
{

    public function __construct()
    {
        $first = ItemsBuilder::AS_CUSTOMER . '.' . Cfg::E_CUSTOMER_A_FIRSTNAME;
        $last = ItemsBuilder::AS_CUSTOMER . '.' . Cfg::E_CUSTOMER_A_LASTNAME;
        $expValue = "CONCAT($first, ' ', $last)";
        $exp = new \Praxigento\Core\Repo\Query\Expression($expValue);
        $this->_map = [
            ItemsBuilder::AS_ASSET => ItemsBuilder::AS_TYPE_ASSET . '.' . TypeAsset::ATTR_CODE,
            ItemsBuilder::AS_CUST_NAME => $exp,
            ItemsBuilder::AS_CUST_EMAIL => ItemsBuilder::AS_CUSTOMER . '.' . Cfg::E_CUSTOMER_A_EMAIL,
            ItemsBuilder::AS_BALANCE => ItemsBuilder::AS_ACCOUNT . '.' . Account::ATTR_BALANCE,
            ItemsBuilder::AS_ID => ItemsBuilder::AS_ACCOUNT . '.' . Account::ATTR_ID
        ];
    }

}