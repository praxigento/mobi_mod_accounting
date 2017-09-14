<?php

namespace Praxigento\Accounting\Ui\DataProvider\Grid\Account;

use Praxigento\Accounting\Config as Cfg;
use Praxigento\Accounting\Repo\Entity\Data\Account as EAccount;
use Praxigento\Accounting\Repo\Entity\Data\Type\Asset as ETypeAsset;


class QueryBuilder
    extends \Praxigento\Core\Ui\DataProvider\Grid\Query\Builder
{

    /**#@+ Tables aliases for external usage ('camelCase' naming) */
    const AS_ACCOUNT = 'paa';
    const AS_CUSTOMER = 'ce';
    const AS_TYPE_ASSET = 'pata';
    /**#@- */

    /**#@+ Columns/expressions aliases for external usage */
    const A_ASSET = 'asset';
    const A_BALANCE = 'balance';
    const A_CUST_EMAIL = 'custEmail';
    const A_CUST_NAME = 'custName';
    const A_ID = 'id';

    /**#@- */

    /**
     * Construct expression for customer name ("firstName lastName").
     */
    public function getExpForCustName()
    {
        $value = 'CONCAT(' . Cfg::E_CUSTOMER_A_FIRSTNAME . ", ' ', " . Cfg::E_CUSTOMER_A_LASTNAME . ')';
        $result = new \Praxigento\Core\Repo\Query\Expression($value);
        return $result;
    }

    protected function getMapper()
    {
        if (is_null($this->mapper)) {
            $map = [
                self::A_ASSET => self::AS_TYPE_ASSET . '.' . ETypeAsset::ATTR_CODE,
                self::A_CUST_NAME => $this->getExpForCustName(),
                self::A_CUST_EMAIL => self::AS_CUSTOMER . '.' . Cfg::E_CUSTOMER_A_EMAIL,
                self::A_BALANCE => self::AS_ACCOUNT . '.' . EAccount::ATTR_BALANCE,
                self::A_ID => self::AS_ACCOUNT . '.' . EAccount::ATTR_ID
            ];
            $this->mapper = new \Praxigento\Core\Repo\Query\Criteria\Def\Mapper($map);
        }
        $result = $this->mapper;
        return $result;
    }

    /**
     * SELECT
     * `paa`.`id`,
     * `paa`.`balance`,
     * `prxgt_acc_type_asset`.`code` AS `asset`,
     * (CONCAT(firstname, ' ', lastname)) AS `custName`,
     * `ce`.`email` AS `custEmail`
     * FROM `prxgt_acc_account` AS `paa`
     * LEFT JOIN `prxgt_acc_type_asset`
     * ON pata.id = paa.asset_type_id
     * LEFT JOIN `customer_entity` AS `ce`
     * ON ce.entity_id = paa.customer_id
     *
     * @inheritdoc
     */
    protected function getQueryItems()
    {
        /* this is primary query builder, not extender */
        $result = $this->conn->select();

        /* define tables aliases for internal usage (in this method) */
        $asAcc = self::AS_ACCOUNT;
        $asCust = self::AS_CUSTOMER;
        $asAsset = self::AS_TYPE_ASSET;

        /* SELECT FROM prxgt_acc_account */
        $tbl = $this->resource->getTableName(EAccount::ENTITY_NAME);
        $as = $asAcc;
        $cols = [
            self::A_ID => EAccount::ATTR_ID,
            self::A_BALANCE => EAccount::ATTR_BALANCE
        ];
        $result->from([$as => $tbl], $cols);

        /* LEFT JOIN prxgt_acc_type_asset */
        $tbl = $this->resource->getTableName(ETypeAsset::ENTITY_NAME);
        $as = $asAsset;
        $cols = [
            self::A_ASSET => ETypeAsset::ATTR_CODE
        ];
        $cond = $as . '.' . ETypeAsset::ATTR_ID . '=' . $asAcc . '.' . EAccount::ATTR_ASSET_TYPE_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* LEFT JOIN customer_entity */
        $tbl = $this->resource->getTableName(Cfg::ENTITY_MAGE_CUSTOMER);
        $as = $asCust;
        $exp = $this->getExpForCustName();
        $cols = [
            self::A_CUST_NAME => $exp,
            self::A_CUST_EMAIL => Cfg::E_CUSTOMER_A_EMAIL
        ];
        $cond = $as . '.' . Cfg::E_CUSTOMER_A_ENTITY_ID . '=' . $asAcc . '.' . EAccount::ATTR_CUST_ID;
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
        $value = 'COUNT(' . self::AS_ACCOUNT . '.' . EAccount::ATTR_ID . ')';

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