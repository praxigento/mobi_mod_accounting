<?php

namespace Praxigento\Accounting\Ui\DataProvider\Grid\Account;

use Praxigento\Accounting\Config as Cfg;
use Praxigento\Accounting\Repo\Data\Account as EAccount;
use Praxigento\Accounting\Repo\Data\Type\Asset as ETypeAsset;


class Query
    extends \Praxigento\Core\App\Ui\DataProvider\Grid\Query\Builder
{

    /**#@+ Tables aliases for external usage ('camelCase' naming) */
    const AS_ACCOUNT = 'paa';
    const AS_CUSTOMER = 'ce';
    const AS_GROUP = 'cg';
    const AS_TYPE_ASSET = 'pata';
    /**#@- */

    /**#@+ Columns/expressions aliases for external usage */
    const A_ASSET = 'asset';
    const A_BALANCE = 'balance';
    const A_CUST_EMAIL = 'custEmail';
    const A_CUST_GROUP = 'custGroup';
    const A_CUST_ID = 'custId';
    const A_CUST_NAME = 'custName';
    const A_ID = 'id';
    /**#@- */

    /**
     * Construct expression for customer name ("firstName lastName").
     */
    public function getExpForCustName()
    {
        $value = 'CONCAT(' . Cfg::E_CUSTOMER_A_FIRSTNAME . ", ' ', " . Cfg::E_CUSTOMER_A_LASTNAME . ')';
        $result = new \Praxigento\Core\App\Repo\Query\Expression($value);
        return $result;
    }

    protected function getMapper()
    {
        if (is_null($this->mapper)) {
            $map = [
                self::A_ASSET => self::AS_TYPE_ASSET . '.' . ETypeAsset::A_CODE,
                self::A_BALANCE => self::AS_ACCOUNT . '.' . EAccount::A_BALANCE,
                self::A_CUST_EMAIL => self::AS_CUSTOMER . '.' . Cfg::E_CUSTOMER_A_EMAIL,
                self::A_CUST_GROUP => self::AS_GROUP . '.' . Cfg::E_CUSTGROUP_A_CODE,
                self::A_CUST_NAME => $this->getExpForCustName(),
                self::A_ID => self::AS_ACCOUNT . '.' . EAccount::A_ID
            ];
            $this->mapper = new \Praxigento\Core\App\Repo\Query\Criteria\Def\Mapper($map);
        }
        $result = $this->mapper;
        return $result;
    }

    protected function getQueryItems()
    {
        $result = $this->conn->select();

        /* define tables aliases for internal usage (in this method) */
        $asAcc = self::AS_ACCOUNT;
        $asAsset = self::AS_TYPE_ASSET;
        $asCust = self::AS_CUSTOMER;
        $asGroup = self::AS_GROUP;

        /* SELECT FROM prxgt_acc_account */
        $tbl = $this->resource->getTableName(EAccount::ENTITY_NAME);
        $as = $asAcc;
        $cols = [
            self::A_ID => EAccount::A_ID,
            self::A_BALANCE => EAccount::A_BALANCE
        ];
        $result->from([$as => $tbl], $cols);

        /* LEFT JOIN prxgt_acc_type_asset */
        $tbl = $this->resource->getTableName(ETypeAsset::ENTITY_NAME);
        $as = $asAsset;
        $cols = [
            self::A_ASSET => ETypeAsset::A_CODE
        ];
        $cond = $as . '.' . ETypeAsset::A_ID . '=' . $asAcc . '.' . EAccount::A_ASSET_TYPE_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* LEFT JOIN customer_entity */
        $tbl = $this->resource->getTableName(Cfg::ENTITY_MAGE_CUSTOMER);
        $as = $asCust;
        $exp = $this->getExpForCustName();
        $cols = [
            self::A_CUST_NAME => $exp,
            self::A_CUST_EMAIL => Cfg::E_CUSTOMER_A_EMAIL,
            self::A_CUST_ID => Cfg::E_CUSTOMER_A_ENTITY_ID
        ];
        $cond = $as . '.' . Cfg::E_CUSTOMER_A_ENTITY_ID . '=' . $asAcc . '.' . EAccount::A_CUST_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* LEFT JOIN customer_group */
        $tbl = $this->resource->getTableName(Cfg::ENTITY_MAGE_CUSTOMER_GROUP);
        $as = $asGroup;
        $cols = [
            self::A_CUST_GROUP => Cfg::E_CUSTGROUP_A_CODE
        ];
        $cond = $as . '.' . Cfg::E_CUSTGROUP_A_ID . '=' . $asCust . '.' . Cfg::E_CUSTOMER_A_GROUP_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* return  result */
        return $result;
    }

    protected function getQueryTotal()
    {
        /* get query to select items */
        /** @var \Magento\Framework\DB\Select $result */
        $result = $this->getQueryItems();
        /* ... then replace "columns" part with own expression */
        $value = 'COUNT(' . self::AS_ACCOUNT . '.' . EAccount::A_ID . ')';

        /**
         * See method \Magento\Framework\DB\Select\ColumnsRenderer::render:
         */
        /**
         * if ($column instanceof \Zend_Db_Expr) {...}
         */
        $exp = new \Praxigento\Core\App\Repo\Query\Expression($value);
        /**
         *  list($correlationName, $column, $alias) = $columnEntry;
         */
        $entry = [null, $exp, null];
        $cols = [$entry];
        $result->setPart('columns', $cols);
        return $result;
    }
}