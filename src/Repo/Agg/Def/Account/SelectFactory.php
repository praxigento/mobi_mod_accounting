<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Repo\Agg\Def\Account;

use Praxigento\Accounting\Config as Cfg;
use Praxigento\Accounting\Data\Agg\Account as AggEntity;
use Praxigento\Accounting\Data\Entity\Account as EntityAccount;
use Praxigento\Accounting\Data\Entity\Type\Asset as EntityTypeAsset;
use Praxigento\Accounting\Repo\Agg\IAccount as AggRepo;
use Praxigento\Core\Repo\Query\Expression;

class SelectFactory
    implements \Praxigento\Core\Repo\Query\IHasSelect
{
    /** @var  \Magento\Framework\DB\Adapter\AdapterInterface */
    protected $_conn;
    /** @var \Magento\Framework\App\ResourceConnection */
    protected $_resource;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->_resource = $resource;
        $this->_conn = $resource->getConnection();
    }

    /** @inheritdoc */
    public function getQueryToSelectCount()
    {
        $result = $this->_conn->select();
        /* aliases and tables */
        $asAcc = AggRepo::AS_ACCOUNT;
        //
        $tblAcc = [$asAcc => $this->_resource->getTableName(EntityAccount::ENTITY_NAME)];
        /* SELECT FROM prxgt_acc_account */
        $expValue = 'COUNT(' . EntityAccount::ATTR_ID . ')';
        $cols = new Expression($expValue);
        $result->from($tblAcc, $cols);
        return $result;
    }

    /** @inheritdoc */
    public function getQueryToSelect()
    {
        $result = $this->_conn->select();
        /* aliases and tables */
        $asAcc = AggRepo::AS_ACCOUNT;
        $asCust = AggRepo::AS_CUSTOMER;
        $asAsset = AggRepo::AS_TYPE_ASSET;
        //
        $tblAcc = [$asAcc => $this->_resource->getTableName(EntityAccount::ENTITY_NAME)];
        $tblCust = [$asCust => $this->_resource->getTableName(Cfg::ENTITY_MAGE_CUSTOMER)];
        $tblAsset = [$asAsset => $this->_resource->getTableName(EntityTypeAsset::ENTITY_NAME)];
        /* SELECT FROM prxgt_acc_account */
        $cols = [
            AggEntity::AS_ID => EntityAccount::ATTR_ID,
            AggEntity::AS_BALANCE => EntityAccount::ATTR_BALANCE
        ];
        $result->from($tblAcc, $cols);
        /* LEFT JOIN prxgt_acc_type_asset */
        $on = $asAsset . '.' . EntityTypeAsset::ATTR_ID . '=' . $asAcc . '.' . EntityAccount::ATTR_ASSET_TYPE_ID;
        $cols = [
            AggEntity::AS_ASSET => EntityTypeAsset::ATTR_CODE
        ];
        $result->joinLeft($tblAsset, $on, $cols);
        /* LEFT JOIN customer_entity */
        $on = $asCust . '.' . Cfg::E_CUSTOMER_A_ENTITY_ID . '=' . $asAcc . '.' . EntityAccount::ATTR_CUST_ID;
        $expValue = 'CONCAT(' . Cfg::E_CUSTOMER_A_FIRSTNAME . ", ' ', " . Cfg::E_CUSTOMER_A_LASTNAME . ')';
        $exp = new Expression($expValue);
        $cols = [
            AggEntity::AS_CUST_NAME => $exp,
            AggEntity::AS_CUST_EMAIL => Cfg::E_CUSTOMER_A_EMAIL
        ];
        $result->joinLeft($tblCust, $on, $cols);
        return $result;
    }
}