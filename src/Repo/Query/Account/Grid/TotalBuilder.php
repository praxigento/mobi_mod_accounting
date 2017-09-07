<?php
/**
 * Created by PhpStorm.
 * User: dm
 * Date: 07.09.17
 * Time: 9:57
 */

namespace Praxigento\Accounting\Repo\Query\Account\Grid;

use Praxigento\Accounting\Config as Cfg;
use Praxigento\Accounting\Repo\Entity\Data\Account as EAccount;
use Praxigento\Accounting\Repo\Entity\Data\Type\Asset as ETypeAsset;

class TotalBuilder
    extends \Praxigento\Accounting\Repo\Query\Account\Grid\ItemsBuilder
{

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
        $expValue = 'COUNT(' . $asAcc . '.' . EAccount::ATTR_ID . ')';
        $cols = new \Praxigento\Core\Repo\Query\Expression($expValue);
        $result->from($tblAcc, $cols);
        /* LEFT JOIN prxgt_acc_type_asset */
        $cond = $asAsset . '.' . ETypeAsset::ATTR_ID . '=' . $asAcc . '.' . EAccount::ATTR_ASSET_TYPE_ID;
        $cols = [];
        $result->joinLeft($tblAsset, $cond, $cols);
        /* LEFT JOIN customer_entity */
        $cond = $asCust . '.' . Cfg::E_CUSTOMER_A_ENTITY_ID . '=' . $asAcc . '.' . EAccount::ATTR_CUST_ID;
        $cols = [];
        $result->joinLeft($tblCust, $cond, $cols);
        $sql = (string)$result;
        return $result;
    }
}