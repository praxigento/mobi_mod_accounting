<?php

namespace Praxigento\Accounting\Ui\DataProvider\Grid\Transaction;

use Praxigento\Accounting\Config as Cfg;
use Praxigento\Accounting\Repo\Entity\Data\Account as EAccount;
use Praxigento\Accounting\Repo\Entity\Data\Transaction as ETransaction;
use Praxigento\Accounting\Repo\Entity\Data\Type\Asset as ETypeAsset;

class QueryBuilder
    extends \Praxigento\Core\Ui\DataProvider\Grid\Query\Builder
{

    /**#@+ Tables aliases for external usage ('camelCase' naming) */
    const AS_ACC_CREDIT = 'paa_cr';
    const AS_ACC_DEBIT = 'paa_db';
    const AS_ASSET = 'pata';
    const AS_CUST_CREDIT = 'ce_cr';
    const AS_CUST_DEBIT = 'ce_db';
    const AS_TRANS = 'pat';
    /**#@- */

    /**#@+
     * Aliases for data attributes.
     */
    const A_ASSET = 'asset';
    const A_CREDIT = 'credit';
    const A_DATE_APPLIED = 'dateApplied';
    const A_DEBIT = 'debit';
    const A_ID_OPER = 'operId';
    const A_ID_TRANS = 'transId';
    const A_NOTE = 'note';
    const A_VALUE = 'value';
    /**#@- */

    protected function getMapper()
    {
        if (is_null($this->mapper)) {
            $map = [
                self::A_ASSET => self::AS_ASSET . '.' . ETypeAsset::ATTR_CODE,
                self::A_CREDIT => self::AS_CUST_CREDIT . '.' . Cfg::E_CUSTOMER_A_EMAIL,
                self::A_DATE_APPLIED => self::AS_TRANS . '.' . ETransaction::ATTR_DATE_APPLIED,
                self::A_DEBIT => self::AS_CUST_DEBIT . '.' . Cfg::E_CUSTOMER_A_EMAIL,
                self::A_ID_OPER => self::AS_TRANS . '.' . ETransaction::ATTR_OPERATION_ID,
                self::A_ID_TRANS => self::AS_TRANS . '.' . ETransaction::ATTR_ID,
                self::A_NOTE => self::AS_TRANS . '.' . ETransaction::ATTR_NOTE,
                self::A_VALUE => self::AS_TRANS . '.' . ETransaction::ATTR_VALUE
            ];
            $this->mapper = new \Praxigento\Core\Repo\Query\Criteria\Def\Mapper($map);
        }
        $result = $this->mapper;
        return $result;
    }

    protected function getQueryItems()
    {
        $result = $this->conn->select();
        /* define tables aliases for internal usage (in this method) */
        $asAsset = self::AS_ASSET;
        $asAccCredit = self::AS_ACC_CREDIT;
        $asAccDebit = self::AS_ACC_DEBIT;
        $asCustCredit = self::AS_CUST_CREDIT;
        $asCustDebit = self::AS_CUST_DEBIT;
        $asTrans = self::AS_TRANS;

        /* SELECT FROM prxgt_acc_transaction */
        $tbl = $this->resource->getTableName(ETransaction::ENTITY_NAME);
        $as = $asTrans;
        $cols = [
            self::A_ID_TRANS => ETransaction::ATTR_ID,
            self::A_ID_OPER => ETransaction::ATTR_OPERATION_ID,
            self::A_DATE_APPLIED => ETransaction::ATTR_DATE_APPLIED,
            self::A_VALUE => ETransaction::ATTR_VALUE,
            self::A_NOTE => ETransaction::ATTR_NOTE
        ];
        $result->from([$as => $tbl], $cols);

        /* LEFT JOIN prxgt_acc_account DEBIT */
        $tbl = $this->resource->getTableName(EAccount::ENTITY_NAME);
        $as = $asAccDebit;
        $cond = $asAccDebit . '.' . EAccount::ATTR_ID . '=' . $asTrans . '.' . ETransaction::ATTR_DEBIT_ACC_ID;
        $cols = [];
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* LEFT JOIN customer_entity */
        $tbl = $this->resource->getTableName(Cfg::ENTITY_MAGE_CUSTOMER);
        $as = $asCustDebit;
        $cond = $asCustDebit . '.' . Cfg::E_CUSTOMER_A_ENTITY_ID . '=' . $asAccDebit . '.' . EAccount::ATTR_CUST_ID;
        $cols = [
            self::A_DEBIT => Cfg::E_CUSTOMER_A_EMAIL
        ];
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* LEFT JOIN prxgt_acc_account CREDIT */
        $tbl = $this->resource->getTableName(EAccount::ENTITY_NAME);
        $as = $asAccCredit;
        $cond = $asAccCredit . '.' . EAccount::ATTR_ID . '=' . $asTrans . '.' . ETransaction::ATTR_CREDIT_ACC_ID;
        $cols = [];
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* LEFT JOIN customer_entity */
        $tbl = $this->resource->getTableName(Cfg::ENTITY_MAGE_CUSTOMER);
        $as = $asCustCredit;
        $cond = $asCustCredit . '.' . Cfg::E_CUSTOMER_A_ENTITY_ID . '=' . $asAccCredit . '.' . EAccount::ATTR_CUST_ID;
        $cols = [
            self::A_CREDIT => Cfg::E_CUSTOMER_A_EMAIL
        ];
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* LEFT JOIN prxgt_acc_type_asset */
        $tbl = $this->resource->getTableName(ETypeAsset::ENTITY_NAME);
        $as = $asAsset;
        $cond = $asAsset . '.' . ETypeAsset::ATTR_ID . '=' . $asAccDebit . '.' . EAccount::ATTR_ASSET_TYPE_ID;
        $cols = [
            self::A_ASSET => ETypeAsset::ATTR_CODE
        ];
        $result->joinLeft([$as => $tbl], $cond, $cols);
        return $result;
    }

    protected function getQueryTotal()
    {
        /* get query to select items */
        /** @var \Magento\Framework\DB\Select $result */
        $result = $this->getQueryItems();
        /* ... then replace "columns" part with own expression */
        $value = 'COUNT(' . self::AS_TRANS . '.' . ETransaction::ATTR_ID . ')';

        /**
         * See method \Magento\Framework\DB\Select\ColumnsRenderer::render:
         */
        /**
         * if ($column instanceof \Zend_Db_Expr) {...}
         */
        $exp = new \Praxigento\Core\Repo\Query\Expression($value);
        /**
         *  list($correlationName, $column, $alias) = $columnEntry;
         */
        $entry = [null, $exp, null];
        $cols = [$entry];
        $result->setPart('columns', $cols);
        return $result;
    }
}