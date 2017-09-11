<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Repo\Agg\Def\Transaction;

use Praxigento\Accounting\Config as Cfg;
use Praxigento\Accounting\Data\Agg\Transaction as Agg;
use Praxigento\Accounting\Repo\Agg\Def\Transaction as Repo;
use Praxigento\Accounting\Repo\Entity\Data\Transaction;
use Praxigento\Accounting\Repo\Entity\Data\Type\Asset as TypeAsset;

class Mapper
    extends \Praxigento\Core\Repo\Query\Criteria\Def\Mapper
{
    public function __construct()
    {
        $this->map = [
            Agg::AS_ASSET => Repo::AS_ASSET . '.' . TypeAsset::ATTR_CODE,
            Agg::AS_CREDIT => Repo::AS_CUST_CREDIT . '.' . Cfg::E_CUSTOMER_A_EMAIL,
            Agg::AS_DATE_APPLIED => Repo::AS_TRANS . '.' . Transaction::ATTR_DATE_APPLIED,
            Agg::AS_DEBIT => Repo::AS_CUST_DEBIT . '.' . Cfg::E_CUSTOMER_A_EMAIL,
            Agg::AS_ID_OPER => Repo::AS_TRANS . '.' . Transaction::ATTR_OPERATION_ID,
            Agg::AS_ID_TRANS => Repo::AS_TRANS . '.' . Transaction::ATTR_ID,
            Agg::AS_NOTE => Repo::AS_TRANS . '.' . Transaction::ATTR_NOTE,
            Agg::AS_VALUE => Repo::AS_TRANS . '.' . Transaction::ATTR_VALUE
        ];
    }

}