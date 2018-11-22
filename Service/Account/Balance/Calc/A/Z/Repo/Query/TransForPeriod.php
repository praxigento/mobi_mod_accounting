<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Accounting\Service\Account\Balance\Calc\A\Z\Repo\Query;

use Praxigento\Accounting\Repo\Data\Account as EAcc;
use Praxigento\Accounting\Repo\Data\Transaction as ETrn;

class TransForPeriod
    extends \Praxigento\Core\App\Repo\Query\Builder
{
    /** Tables aliases for external usage ('camelCase' naming) */
    const AS_CRD = 'crd';
    const AS_DBT = 'dbt';
    const AS_TRN = 'trn';

    /** Columns/expressions aliases for external usage ('camelCase' naming) */
    const A_CREDIT_ACC_ID = 'creditAccId';
    const A_DATE_APPLIED = 'dateApplied';
    const A_DEBIT_ACC_ID = 'debitAccId';
    const A_ID = 'id';
    const A_NOTE = 'note';
    const A_OPER_ID = 'operId';
    const A_VALUE = 'value';

    /** Bound variables names ('camelCase' naming) */
    const BND_DATE_FROM = 'dateFrom';
    const BND_DATE_TO = 'dateTo';

    /** Entities are used in the query */
    const E_ACC = EAcc::ENTITY_NAME;
    const E_TRN = ETrn::ENTITY_NAME;

    public function build(\Magento\Framework\DB\Select $source = null)
    {
        /* this is root query builder (started from SELECT) */
        $result = $this->conn->select();

        /* define tables aliases for internal usage (in this method) */
        $asCrd = self::AS_CRD;
        $asDbt = self::AS_DBT;
        $asTrn = self::AS_TRN;

        /* FROM prxgt_acc_transaction */
        $tbl = $this->resource->getTableName(self::E_TRN);    // name with prefix
        $as = $asTrn;    // alias for 'current table' (currently processed in this block of code)
        $cols = [
            self::A_CREDIT_ACC_ID => ETrn::A_CREDIT_ACC_ID,
            self::A_DATE_APPLIED => ETrn::A_DATE_APPLIED,
            self::A_DEBIT_ACC_ID => ETrn::A_DEBIT_ACC_ID,
            self::A_ID => ETrn::A_ID,
            self::A_NOTE => ETrn::A_NOTE,
            self::A_OPER_ID => ETrn::A_OPERATION_ID,
            self::A_VALUE => ETrn::A_VALUE
        ];
        $result->from([$as => $tbl], $cols);    // standard names for the variables

        /* LEFT JOIN prxgt_acc_account AS debit */
        $tbl = $this->resource->getTableName(self::E_ACC);
        $as = $asDbt;
        $cols = [];
        $cond = "$as." . EAcc::A_ID . "=$asTrn." . ETrn::A_DEBIT_ACC_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* LEFT JOIN prxgt_acc_account AS credit */
        $tbl = $this->resource->getTableName(self::E_ACC);
        $as = $asCrd;
        $cols = [];
        $cond = "$as." . EAcc::A_ID . "=$asTrn." . ETrn::A_CREDIT_ACC_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* WHERE */
        $byFrom = "$asTrn." . ETrn::A_DATE_APPLIED . ">=:" . self::BND_DATE_FROM;
        $byTo = "$asTrn." . ETrn::A_DATE_APPLIED . "<:" . self::BND_DATE_TO;
        $result->where("($byFrom) AND ($byTo)");

        /* ORDER */
        $result->order($asTrn . '.' . ETrn::A_DATE_APPLIED . ' ASC');

        return $result;
    }

}