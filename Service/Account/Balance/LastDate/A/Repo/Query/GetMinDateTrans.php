<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Accounting\Service\Account\Balance\LastDate\A\Repo\Query;

use Praxigento\Accounting\Repo\Data\Account as EAcc;
use Praxigento\Accounting\Repo\Data\Transaction as ETran;

/**
 * Get base query to select minimal date_applied for transactions by asset type or account IDs.
 * Add appropriate 'where' clauses after build.
 */
class GetMinDateTrans
    extends \Praxigento\Core\App\Repo\Query\Builder
{
    /** Tables aliases for external usage ('camelCase' naming) */
    const AS_ACC = 'acc';
    const AS_TRAN = 'trn';

    /** Columns/expressions aliases for external usage ('camelCase' naming) */
    const A_MIN_DATE = 'minDate';

    /** Entities are used in the query */
    const E_ACC = EAcc::ENTITY_NAME;
    const E_TRAN = ETran::ENTITY_NAME;

    /**
     * Get base query to select minimal date_applied for transactions by asset type or account IDs.
     * Add appropriate 'where' clauses after build.
     *
     * SELECT
     * MIN(trn.date_applied)
     * FROM
     * prxgt_acc_transaction as trn
     * LEFT JOIN prxgt_acc_account as acc ON
     * acc.id = trn.debit_acc_id
     *
     * @inheritdoc
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
        $as = $asTran;    // alias for 'current table' (currently processed in this block of code)
        $exp = $this->expMin();
        $cols = [
            self::A_MIN_DATE => $exp
        ];
        $result->from([$as => $tbl], $cols);    // standard names for the variables

        /* LEFT JOIN prxgt_acc_account */
        $tbl = $this->resource->getTableName(self::E_ACC);
        $as = $asAcc;
        $cols = [];
        /* bind account to transaction as debit account to bind asset type below (credit acc. has the same type) */
        $cond = "$as." . EAcc::A_ID . "=$asTran." . ETran::A_DEBIT_ACC_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        return $result;
    }

    private function expMin()
    {
        $value = 'MIN(' . self::AS_TRAN . '.' . ETran::A_DATE_APPLIED . ")";
        $result = new \Praxigento\Core\App\Repo\Query\Expression($value);
        return $result;
    }
}