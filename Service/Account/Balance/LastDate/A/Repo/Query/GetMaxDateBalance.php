<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Accounting\Service\Account\Balance\LastDate\A\Repo\Query;

use Praxigento\Accounting\Repo\Data\Account as EAcc;
use Praxigento\Accounting\Repo\Data\Balance as EBal;

/**
 * Get base query to select maximal date for existing balances by asset type or account IDs.
 * Add appropriate 'where' clauses after build.
 */
class GetMaxDateBalance
    extends \Praxigento\Core\App\Repo\Query\Builder
{
    /** Tables aliases for external usage ('camelCase' naming) */
    const AS_ACC = 'acc';
    const AS_BAL = 'bal';

    /** Columns/expressions aliases for external usage ('camelCase' naming) */
    const A_MAX_DATE = 'maxDate';

    /** Entities are used in the query */
    const E_ACC = EAcc::ENTITY_NAME;
    const E_BAL = EBal::ENTITY_NAME;

    /**
     * Get base query to select maximal date for existing balances by asset type or account IDs.
     * Add appropriate 'where' clauses after build.
     *
     * SELECT
     * MAX(b.`date`) as maxDate
     * FROM
     * prxgt_acc_balance as b
     * LEFT JOIN prxgt_acc_account AS a ON
     * a.id = b.account_id
     *
     * @inheritdoc
     */
    public function build(\Magento\Framework\DB\Select $source = null)
    {
        /* this is root query builder (started from SELECT) */
        $result = $this->conn->select();

        /* define tables aliases for internal usage (in this method) */
        $asAcc = self::AS_ACC;
        $asBal = self::AS_BAL;

        /* FROM prxgt_acc_balance */
        $tbl = $this->resource->getTableName(self::E_BAL);    // name with prefix
        $as = $asBal;    // alias for 'current table' (currently processed in this block of code)
        $exp = $this->expMax();
        $cols = [
            self::A_MAX_DATE => $exp
        ];
        $result->from([$as => $tbl], $cols);    // standard names for the variables

        /* LEFT JOIN prxgt_acc_account */
        $tbl = $this->resource->getTableName(self::E_ACC);
        $as = $asAcc;
        $cols = [];
        /* bind account to transaction as debit account to bind asset type below (credit acc. has the same type) */
        $cond = "$as." . EAcc::A_ID . "=$asBal." . EBal::A_ACCOUNT_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        return $result;
    }

    private function expMax()
    {
        $value = 'MAX(' . self::AS_BAL . '.' . EBal::A_DATE . ")";
        $result = new \Praxigento\Core\App\Repo\Query\Expression($value);
        return $result;
    }
}