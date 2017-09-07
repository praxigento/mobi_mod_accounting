<?php
/**
 * Created by PhpStorm.
 * User: dm
 * Date: 07.09.17
 * Time: 9:57
 */

namespace Praxigento\Accounting\Ui\DataProvider\Account2\Query;

use Praxigento\Accounting\Config as Cfg;
use Praxigento\Accounting\Repo\Entity\Data\Account as EAccount;
use Praxigento\Accounting\Repo\Entity\Data\Type\Asset as ETypeAsset;


class ItemsBuilder
    extends \Praxigento\Core\Repo\Query\Builder
{

    /**#@+
     * Aliases for data attributes.
     */
    const AS_ACCOUNT = 'paa';
    const AS_ASSET = 'Asset';
    const AS_BALANCE = 'Balance';
    const AS_CUSTOMER = 'ce';
    const AS_CUST_EMAIL = 'CustEmail';
    const AS_CUST_NAME = 'CustName';
    const AS_ID = 'Id';
    const AS_TYPE_ASSET = 'pata';

    /**#@- */

    public function build(\Magento\Framework\DB\Select $source = null)
    {
        $result = $this->conn->select();
        /* aliases and tables */
        $asAcc = self::AS_ACCOUNT;
        $asCust = self::AS_CUSTOMER;
        $asAsset = self::AS_TYPE_ASSET;
        //
        $tblAcc = [$asAcc => $this->resource->getTableName(EAccount::ENTITY_NAME)];
        $tblCust = [$asCust => $this->resource->getTableName(Cfg::ENTITY_MAGE_CUSTOMER)];
        $tblAsset = [$asAsset => $this->resource->getTableName(ETypeAsset::ENTITY_NAME)];
        /* SELECT FROM prxgt_acc_account */
        $cols = [
            self::AS_ID => EAccount::ATTR_ID,
            self::AS_BALANCE => EAccount::ATTR_BALANCE
        ];
        $result->from($tblAcc, $cols);
        /* LEFT JOIN prxgt_acc_type_asset */
        $cond = $asAsset . '.' . ETypeAsset::ATTR_ID . '=' . $asAcc . '.' . EAccount::ATTR_ASSET_TYPE_ID;
        $cols = [
            self::AS_ASSET => ETypeAsset::ATTR_CODE
        ];
        $result->joinLeft($tblAsset, $cond, $cols);
        /* LEFT JOIN customer_entity */
        $cond = $asCust . '.' . Cfg::E_CUSTOMER_A_ENTITY_ID . '=' . $asAcc . '.' . EAccount::ATTR_CUST_ID;
        $expValue = 'CONCAT(' . Cfg::E_CUSTOMER_A_FIRSTNAME . ", ' ', " . Cfg::E_CUSTOMER_A_LASTNAME . ')';
        $exp = new \Praxigento\Core\Repo\Query\Expression($expValue);
        $cols = [
            self::AS_CUST_NAME => $exp,
            self::AS_CUST_EMAIL => Cfg::E_CUSTOMER_A_EMAIL
        ];
        $result->joinLeft($tblCust, $cond, $cols);
        $sql = (string)$result;
        return $result;
    }
}