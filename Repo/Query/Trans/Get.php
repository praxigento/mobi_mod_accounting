<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Accounting\Repo\Query\Trans;

use Praxigento\Accounting\Repo\Data\Account as EAccount;
use Praxigento\Accounting\Repo\Data\Operation as EOperation;
use Praxigento\Accounting\Repo\Data\Transaction as ETransaction;
use Praxigento\Accounting\Repo\Data\Type\Asset as ETypeAsset;
use Praxigento\Accounting\Repo\Data\Type\Operation as ETypeOper;

/**
 * Get row transactions data.
 */
class Get
    extends \Praxigento\Core\App\Repo\Query\Builder
{
    /** Tables aliases for external usage ('camelCase' naming) */
    const AS_ACC_CREDIT = 'accCred';
    const AS_ACC_DEBIT = 'accDebt';
    const AS_OPER = 'oper';
    const AS_TRANS = 'trans';
    const AS_TYPE_ASSET = 'typeAsset';
    const AS_TYPE_OPER = 'typeOper';

    /** Columns/expressions aliases for external usage ('camelCase' naming) */
    const A_ASSET_TYPE_CODE = 'assetTypeCode';
    const A_CREDIT_ACC_ID = 'creditAccId';
    const A_DATE_APPLIED = 'dateApplied';
    const A_DATE_PERFORMED = 'datePerformed';
    const A_DEBIT_ACC_ID = 'debitAccId';
    const A_OPER_ID = 'operId';
    const A_OPER_NOTE = 'operNote';
    const A_OPER_TYPE_CODE = 'operTypeCode';
    const A_TRANS_AMOUNT = 'transAmount';
    const A_TRANS_ID = 'transId';
    const A_TRANS_NOTE = 'transNote';

    /** Entities are used in the query */
    const E_ACC_CREDIT = EAccount::ENTITY_NAME;
    const E_ACC_DEBIT = EAccount::ENTITY_NAME;
    const E_OPER = EOperation::ENTITY_NAME;
    const E_TRANS = ETransaction::ENTITY_NAME;
    const E_TYPE_ASSET = ETypeAsset::ENTITY_NAME;
    const E_TYPE_OPER = ETypeOper::ENTITY_NAME;

    public function build(\Magento\Framework\DB\Select $source = null)
    {
        /* this is root query builder (started from SELECT) */
        $result = $this->conn->select();

        /* define tables aliases for internal usage (in this method) */
        $asAccCred = self::AS_ACC_CREDIT;
        $asAccDebt = self::AS_ACC_DEBIT;
        $asOper = self::AS_OPER;
        $asTrans = self::AS_TRANS;
        $asTypeAsset = self::AS_TYPE_ASSET;
        $asTypeOper = self::AS_TYPE_OPER;

        /* FROM prxgt_acc_transaction */
        $tbl = $this->resource->getTableName(self::E_TRANS);    // name with prefix
        $as = $asTrans;    // alias for 'current table' (currently processed in this block of code)
        $cols = [
            self::A_TRANS_ID => ETransaction::A_ID,
            self::A_DATE_APPLIED => ETransaction::A_DATE_APPLIED,
            self::A_DEBIT_ACC_ID => ETransaction::A_DEBIT_ACC_ID,
            self::A_CREDIT_ACC_ID => ETransaction::A_CREDIT_ACC_ID,
            self::A_TRANS_AMOUNT => ETransaction::A_VALUE,
            self::A_TRANS_NOTE => ETransaction::A_NOTE
        ];
        $result->from([$as => $tbl], $cols);    // standard names for the variables

        /* LEFT JOIN prxgt_acc_operation */
        $tbl = $this->resource->getTableName(self::E_OPER);
        $as = $asOper;
        $cols = [
            self::A_OPER_ID => EOperation::A_ID,
            self::A_DATE_PERFORMED => EOperation::A_DATE_PREFORMED,
            self::A_OPER_NOTE => EOperation::A_NOTE
        ];
        $cond = "$as." . EOperation::A_ID . "=$asTrans." . ETransaction::A_OPERATION_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* LEFT JOIN prxgt_acc_type_operation */
        $tbl = $this->resource->getTableName(self::E_TYPE_OPER);
        $as = $asTypeOper;
        $cols = [
            self::A_OPER_TYPE_CODE => ETypeOper::A_CODE
        ];
        $cond = "$as." . ETypeOper::A_ID . "=$asOper." . EOperation::A_TYPE_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* LEFT JOIN prxgt_acc_account (debit) */
        $tbl = $this->resource->getTableName(self::E_ACC_DEBIT);
        $as = $asAccDebt;
        $cols = [];
        $cond = "$as." . EAccount::A_ID . "=$asTrans." . ETransaction::A_DEBIT_ACC_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* LEFT JOIN prxgt_acc_type_asset */
        $tbl = $this->resource->getTableName(self::E_TYPE_ASSET);
        $as = $asTypeAsset;
        $cols = [
            self::A_ASSET_TYPE_CODE => ETypeAsset::A_CODE
        ];
        $cond = "$as." . ETypeAsset::A_ID . "=$asAccDebt." . EAccount::A_ASSET_TYPE_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* LEFT JOIN prxgt_acc_account (credit) */
        $tbl = $this->resource->getTableName(self::E_ACC_CREDIT);
        $as = $asAccCred;
        $cols = [];
        $cond = "$as." . EAccount::A_ID . "=$asTrans." . ETransaction::A_CREDIT_ACC_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        return $result;
    }

}