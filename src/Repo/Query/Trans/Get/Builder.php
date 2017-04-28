<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Repo\Query\Trans\Get;

use Praxigento\Accounting\Data\Entity\Account as Acc;
use Praxigento\Accounting\Data\Entity\Operation as Oper;
use Praxigento\Accounting\Data\Entity\Transaction as Trans;

/**
 * Build query to get transactions for the customer.
 */
class Builder
    extends \Praxigento\Core\Repo\Query\Def\Builder
{
    /**
     * Tables aliases.
     */
    const AS_ACC_CRD = 'accCrd';
    const AS_ACC_DBT = 'accDbt';
    const AS_OPER = 'oper';
    const AS_TRANS = 'trans';

    /**
     * Attributes aliases.
     */
    const A_AMOUNT = 'amount';
    const A_ASSET_TYPE_ID = 'assetTypeId';
    const A_CREDIT_ACC_ID = 'creditAccId';
    const A_CREDIT_CUST_ID = 'creditCustId';
    const A_DATE_APPLIED = 'dateApplied';
    const A_DATE_PERFORMED = 'datePerformed';
    const A_DEBIT_ACC_ID = 'debitAccId';
    const A_DEBIT_CUST_ID = 'debitCustId';
    const A_OPER_ID = 'operId';
    const A_OPER_NOTE = 'operNote';
    const A_OPER_TYPE_ID = 'assetTypeId';
    const A_TRANS_ID = 'transId';
    const A_TRANS_NOTE = 'transNote';

    /**
     * @inheritdoc
     *
     * @SuppressWarnings(PHPMD.ShortVariable)
     */
    public function getSelectQuery(\Praxigento\Core\Repo\Query\IBuilder $qbuild = null)
    {
        $result = $this->conn->select();    // this is independent query, ignore input query builder
        /* create shortcuts for table aliases */
        $asTrans = self::AS_TRANS;
        $asOper = self::AS_OPER;
        $asAccCrd = self::AS_ACC_CRD;
        $asAccDbt = self::AS_ACC_DBT;

        /* SELECT FROM customer_entity */
        $tbl = $this->resource->getTableName(Trans::ENTITY_NAME);
        $cols = [
            self::A_TRANS_ID => Trans::ATTR_ID,
            self::A_TRANS_NOTE => Trans::ATTR_NOTE,
            self::A_OPER_ID => Trans::ATTR_OPERATION_ID,
            self::A_DEBIT_ACC_ID => Trans::ATTR_DEBIT_ACC_ID,
            self::A_CREDIT_ACC_ID => Trans::ATTR_CREDIT_ACC_ID,
            self::A_AMOUNT => Trans::ATTR_VALUE,
            self::A_DATE_APPLIED => Trans::ATTR_DATE_APPLIED
        ];
        $result->from([$asTrans => $tbl], $cols);
        /* LEFT JOIN prxgt_acc_operation */
        $tbl = $this->resource->getTableName(Oper::ENTITY_NAME);
        $on = $asOper . '.' . Oper::ATTR_ID . '=' . $asTrans . '.' . Trans::ATTR_OPERATION_ID;
        $cols = [
            self::A_OPER_ID => Oper::ATTR_ID,
            self::A_OPER_TYPE_ID => Oper::ATTR_TYPE_ID,
            self::A_DATE_PERFORMED => Oper::ATTR_DATE_PREFORMED,
            self::A_OPER_NOTE => Oper::ATTR_NOTE
        ];
        $result->joinLeft([$asOper => $tbl], $on, $cols);
        /* LEFT JOIN prxgt_acc_account as debit */
        $tbl = $this->resource->getTableName(Acc::ENTITY_NAME);
        $on = $asAccDbt . '.' . Acc::ATTR_ID . '=' . $asTrans . '.' . Trans::ATTR_DEBIT_ACC_ID;
        $cols = [self::A_DEBIT_CUST_ID => Acc::ATTR_CUST_ID];
        $result->joinLeft([$asAccDbt => $tbl], $on, $cols);
        /* LEFT JOIN prxgt_acc_account as credit */
        $tbl = $this->resource->getTableName(Acc::ENTITY_NAME);
        $on = $asAccCrd . '.' . Acc::ATTR_ID . '=' . $asTrans . '.' . Trans::ATTR_CREDIT_ACC_ID;
        $cols = [self::A_CREDIT_CUST_ID => Acc::ATTR_CUST_ID];
        $result->joinLeft([$asAccCrd => $tbl], $on, $cols);
        /* result */
        return $result;
    }
}