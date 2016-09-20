<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Repo\Agg\Def\Account;

use Praxigento\Accounting\Config as Cfg;
use Praxigento\Accounting\Data\Agg\Account as Agg;
use Praxigento\Accounting\Data\Entity\Account;
use Praxigento\Accounting\Data\Entity\Type\Asset as TypeAsset;
use Praxigento\Accounting\Repo\Agg\IAccount as Repo;

class Mapper
    implements \Praxigento\Core\Repo\Query\Criteria\IMapper
{
    /** @var array */
    protected $_map = [];

    public function __construct()
    {
        $first = Repo::AS_CUSTOMER . '.' . Cfg::E_CUSTOMER_A_FIRSTNAME;
        $last = Repo::AS_CUSTOMER . '.' . Cfg::E_CUSTOMER_A_LASTNAME;
        $expValue = "CONCAT($first, ' ', $last)";
        $exp = new \Praxigento\Core\Repo\Query\Expression($expValue);
        $this->_map = [
            Agg::AS_ASSET => Repo::AS_TYPE_ASSET . '.' . TypeAsset::ATTR_CODE,
            Agg::AS_CUST_NAME => $exp,
            Agg::AS_CUST_EMAIL => Repo::AS_CUSTOMER . '.' . Cfg::E_CUSTOMER_A_EMAIL,
            Agg::AS_BALANCE => Repo::AS_ACCOUNT . '.' . Account::ATTR_ID,
            Agg::AS_ID => Repo::AS_ACCOUNT . '.' . Account::ATTR_ID
        ];
    }

    public function get($key)
    {
        $result = $this->_map[$key]??$key;
        return $result;
    }
}