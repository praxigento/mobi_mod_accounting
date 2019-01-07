<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Praxigento\Accounting\Service\Account\Balance\Validate\A\ProcessOneAsset\A\Repo\Query;

use Praxigento\Accounting\Repo\Data\Account as EAcc;
use Praxigento\Accounting\Repo\Data\Transaction as ETran;

class GetTransactions
    extends \Praxigento\Core\App\Repo\Query\Builder
{
    /** Tables aliases for external usage ('camelCase' naming) */
    const AS_ACC = 'acc';
    const AS_TRAN = 'trn';

    /** Columns/expressions aliases for external usage ('camelCase' naming) */
    const A_ACC_ID_CREDIT = 'accIdCredit';
    const A_ACC_ID_DEBIT = 'accIdDebit';
    const A_AMOUNT = 'amount';


    /** Bound variables names ('camelCase' naming) */
    const BND_ASSET_TYPE_ID = 'assetTypeId';
    const BND_DATE_FROM = 'dateFrom';

    /** Entities are used in the query */
    const E_ACC = EAcc::ENTITY_NAME;
    const E_TRAN = ETran::ENTITY_NAME;

    /**
     *SELECT
     * `trn`.`debit_acc_id` AS `accIdDebit`,
     * `trn`.`credit_acc_id` AS `accIdCredit`,
     * `trn`.`value` AS `amount`
     * FROM
     * `prxgt_acc_transaction` AS `trn`
     * LEFT JOIN `prxgt_acc_account` AS `acc` ON
     * acc.id = trn.debit_acc_id
     * WHERE
     * ((acc.asset_type_id =:assetTypeId)
     * AND (trn.date_applied >=:dateFrom))
     */
    public function build(\Magento\Framework\DB\Select $source = null)
    {
        /* this is root query builder (started from SELECT) */
        $result = $this->conn->select();

        /* define tables aliases for internal usage (in this method) */
        $asAcc = self::AS_ACC;
        $asTran = self::AS_TRAN;

        /* FROM prxgt_acc_transaction */
        $tbl = $this->resource->getTableName(self::E_TRAN);    // name with prefix
        $as = $asTran;
        $cols = [
            self::A_ACC_ID_DEBIT => ETran::A_DEBIT_ACC_ID,
            self::A_ACC_ID_CREDIT => ETran::A_CREDIT_ACC_ID,
            self::A_AMOUNT => ETran::A_VALUE
        ];
        $result->from([$as => $tbl], $cols);    // standard names for the variables

        /* LEFT JOIN prxgt_acc_account */
        $tbl = $this->resource->getTableName(self::E_ACC);
        $as = $asAcc;
        $cols = [];
        $cond = "$as." . EAcc::A_ID . "=$asTran." . ETran::A_DEBIT_ACC_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* query tuning */
        $byAsset = "$asAcc." . EAcc::A_ASSET_TYPE_ID . "=:" . self::BND_ASSET_TYPE_ID;
        $byDate = "$asTran." . ETran::A_DATE_APPLIED . ">=:" . self::BND_DATE_FROM;
        $result->where("($byAsset) AND ($byDate)");

        return $result;
    }

}