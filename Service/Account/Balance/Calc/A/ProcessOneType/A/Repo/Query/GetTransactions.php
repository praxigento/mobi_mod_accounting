<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Accounting\Service\Account\Balance\Calc\A\ProcessOneType\A\Repo\Query;

use Praxigento\Accounting\Repo\Data\Account as EAccount;
use Praxigento\Accounting\Repo\Data\Transaction as ETran;

class GetTransactions
    extends \Praxigento\Core\App\Repo\Query\Builder
{
    /** Tables aliases for external usage ('camelCase' naming) */
    const AS_ACC = 'acc';
    const AS_TRN = 'trn';

    /** Columns/expressions aliases for external usage ('camelCase' naming) */
    const A_CREDIT_ACC_ID = ETran::A_CREDIT_ACC_ID;
    const A_DATE_APPLIED = ETran::A_DATE_APPLIED;
    const A_DEBIT_ACC_ID = ETran::A_DEBIT_ACC_ID;
    const A_ID = ETran::A_ID;
    const A_NOTE = ETran::A_NOTE;
    const A_OPERATION_ID = ETran::A_OPERATION_ID;
    const A_VALUE = ETran::A_VALUE;

    /** Bound variables names ('camelCase' naming) */
    const BND_ASSET_TYPE = 'assetType';
    const BND_DATE_APPL = 'dateApplied';

    /** Entities are used in the query */
    const E_ACC = EAccount::ENTITY_NAME;
    const E_TRN = ETran::ENTITY_NAME;

    public function build(\Magento\Framework\DB\Select $source = null)
    {
        /* this is root query builder (started from SELECT) */
        $result = $this->conn->select();

        /* define tables aliases for internal usage (in this method) */
        $asAcc = self::AS_ACC;
        $asTrn = self::AS_TRN;

        /* FROM prxgt_acc_transaction */
        $tbl = $this->resource->getTableName(self::E_TRN);    // name with prefix
        $as = $asTrn;    // alias for 'current table' (currently processed in this block of code)
        $cols = [
            self::A_CREDIT_ACC_ID => ETran::A_CREDIT_ACC_ID,
            self::A_DATE_APPLIED => ETran::A_DATE_APPLIED,
            self::A_DEBIT_ACC_ID => ETran::A_DEBIT_ACC_ID,
            self::A_ID => ETran::A_ID,
            self::A_NOTE => ETran::A_NOTE,
            self::A_OPERATION_ID => ETran::A_OPERATION_ID,
            self::A_VALUE => ETran::A_VALUE
        ];
        $result->from([$as => $tbl], $cols);    // standard names for the variables

        /* LEFT JOIN prxgt_acc_account */
        $tbl = $this->resource->getTableName(self::E_ACC);
        $as = $asAcc;
        $cols = [];
        $cond = "$as." . EAccount::A_ID . "=$asTrn." . ETran::A_DEBIT_ACC_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* WHERE */
        $byDate = "$asTrn." . ETran::A_DATE_APPLIED . ">=:" . self::BND_DATE_APPL;
        $byAssetType = "$asAcc." . EAccount::A_ASSET_TYPE_ID . "=:" . self::BND_ASSET_TYPE;
        $result->where("($byDate) AND ($byAssetType)");

        /* ORDER */
        $byDateApplAsc = ETran::A_DATE_APPLIED . ' ASC';
        $result->order($byDateApplAsc);

        return $result;
    }

}