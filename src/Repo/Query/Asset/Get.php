<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Repo\Query\Asset;

use Praxigento\Accounting\Repo\Entity\Data\Account as EAcc;
use Praxigento\Accounting\Repo\Entity\Data\Type\Asset as ETypeAss;

class Get
    extends \Praxigento\Core\App\Repo\Query\Builder
{
    /** Tables aliases for external usage */
    const AS_ACC = 'acc';
    const AS_TYPE = 'type';

    /** Columns/expressions aliases for external usage */
    const A_ACC_BALANCE = 'accBalance';
    const A_ACC_ID = 'accId';
    const A_ASSET_CODE = 'assetCode';
    const A_ASSET_ID = 'assetId';
    const A_IS_VISIBLE = 'isVisible';

    /** Bound variables names */
    const BND_CUST_ID = 'custId';

    /** Entities are used in the query */
    const E_ACC = EAcc::ENTITY_NAME;
    const E_TYPE = ETypeAss::ENTITY_NAME;

    public function build(\Magento\Framework\DB\Select $source = null)
    {
        /* this is root query builder (started from SELECT) */
        $result = $this->conn->select();

        /* define tables aliases for internal usage (in this method) */
        $asAcc = self::AS_ACC;
        $asType = self::AS_TYPE;

        /* FROM prxgt_acc_type_asset */
        $tbl = $this->resource->getTableName(ETypeAss::ENTITY_NAME);
        $as = $asType;
        $cols = [
            self::A_ASSET_ID => ETypeAss::ATTR_ID,
            self::A_ASSET_CODE => ETypeAss::ATTR_CODE,
            self::A_IS_VISIBLE => ETypeAss::ATTR_IS_VISIBLE
        ];
        $result->from([$as => $tbl], $cols);

        /* LEFT JOIN prxgt_acc_account */
        $tbl = $this->resource->getTableName(EAcc::ENTITY_NAME);
        $as = $asAcc;
        $cols = [
            self::A_ACC_ID => EAcc::ATTR_ID,
            self::A_ACC_BALANCE => EAcc::ATTR_BALANCE
        ];
        $onTypeId = $as . '.' . EAcc::ATTR_ASSET_TYPE_ID . '=' . $asType . '.' . ETypeAss::ATTR_ID;
        $onCustId = $as . '.' . EAcc::ATTR_CUST_ID . '=:' . self::BND_CUST_ID;
        $cond = "($onTypeId) AND ($onCustId)";
        $result->joinLeft([$as => $tbl], $cond, $cols);

        return $result;
    }

}