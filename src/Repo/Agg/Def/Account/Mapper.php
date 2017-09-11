<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Repo\Agg\Def\Account;

use Praxigento\Accounting\Config as Cfg;
use Praxigento\Accounting\Data\Agg\Account as Agg;
use Praxigento\Accounting\Repo\Agg\IAccount as Repo;
use Praxigento\Accounting\Repo\Entity\Data\Account;
use Praxigento\Accounting\Repo\Entity\Data\Type\Asset as TypeAsset;

class Mapper
    extends \Praxigento\Core\Repo\Query\Criteria\Def\Mapper
{

    public function __construct()
    {
        $first = Repo::AS_CUSTOMER . '.' . Cfg::E_CUSTOMER_A_FIRSTNAME;
        $last = Repo::AS_CUSTOMER . '.' . Cfg::E_CUSTOMER_A_LASTNAME;
        $expValue = "CONCAT($first, ' ', $last)";
        $exp = new \Praxigento\Core\Repo\Query\Expression($expValue);
        $this->map = [
            Agg::AS_ASSET => Repo::AS_TYPE_ASSET . '.' . TypeAsset::ATTR_CODE,
            Agg::AS_CUST_NAME => $exp,
            Agg::AS_CUST_EMAIL => Repo::AS_CUSTOMER . '.' . Cfg::E_CUSTOMER_A_EMAIL,
            Agg::AS_BALANCE => Repo::AS_ACCOUNT . '.' . Account::ATTR_BALANCE,
            Agg::AS_ID => Repo::AS_ACCOUNT . '.' . Account::ATTR_ID
        ];
    }

}