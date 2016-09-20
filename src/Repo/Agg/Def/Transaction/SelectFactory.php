<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Repo\Agg\Def\Transaction;

use Praxigento\Accounting\Config as Cfg;
use Praxigento\Accounting\Data\Agg\Transaction as AggEntity;
use Praxigento\Accounting\Data\Entity\Account as EntityAccount;
use Praxigento\Accounting\Data\Entity\Transaction as EntityTrans;
use Praxigento\Accounting\Data\Entity\Type\Asset as EntityTypeAsset;
use Praxigento\Accounting\Repo\Agg\Def\Transaction as Repo;

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

    public function getQueryToSelectCount()
    {
        $result = $this->_conn->select();
        /* aliases and tables */
        $asAsset = Repo::AS_ASSET;
        $asAccCredit = Repo::AS_ACC_CREDIT;
        $asAccDebit = Repo::AS_ACC_DEBIT;
        $asCustCredit = Repo::AS_CUST_CREDIT;
        $asCustDebit = Repo::AS_CUST_DEBIT;

        $asTrans = Repo::AS_TRANS;
        //
        $tblAsset = [$asAsset => $this->_resource->getTableName(EntityTypeAsset::ENTITY_NAME)];
        $tblAccCredit = [$asAccCredit => $this->_resource->getTableName(EntityAccount::ENTITY_NAME)];
        $tblAccDebit = [$asAccDebit => $this->_resource->getTableName(EntityAccount::ENTITY_NAME)];
        $tblCustCredit = [$asCustCredit => $this->_resource->getTableName(Cfg::ENTITY_MAGE_CUSTOMER)];
        $tblCustDebit = [$asCustDebit => $this->_resource->getTableName(Cfg::ENTITY_MAGE_CUSTOMER)];
        $tblTrans = [$asTrans => $this->_resource->getTableName(EntityTrans::ENTITY_NAME)];
        /* SELECT FROM prxgt_acc_transaction */
        $expValue = 'COUNT(' . $asTrans . '.' . EntityTrans::ATTR_ID . ')';
        $cols = new \Praxigento\Core\Repo\Query\Expression($expValue);
        $result->from($tblTrans, $cols);
        /* LEFT JOIN prxgt_acc_account DEBIT */
        $on = $asAccDebit . '.' . EntityAccount::ATTR_ID . '=' . $asTrans . '.' . EntityTrans::ATTR_DEBIT_ACC_ID;
        $cols = [];
        $result->joinLeft($tblAccDebit, $on, $cols);
        /* LEFT JOIN customer_entity */
        $on = $asCustDebit . '.' . Cfg::E_CUSTOMER_A_ENTITY_ID . '=' . $asAccDebit . '.' . EntityAccount::ATTR_CUST_ID;
        $result->joinLeft($tblCustDebit, $on, $cols);
        /* LEFT JOIN prxgt_acc_account CREDIT */
        $on = $asAccCredit . '.' . EntityAccount::ATTR_ID . '=' . $asTrans . '.' . EntityTrans::ATTR_CREDIT_ACC_ID;
        $result->joinLeft($tblAccCredit, $on, $cols);
        /* LEFT JOIN customer_entity */
        $on = $asCustCredit . '.' . Cfg::E_CUSTOMER_A_ENTITY_ID . '=' . $asAccCredit . '.' . EntityAccount::ATTR_CUST_ID;
        $result->joinLeft($tblCustCredit, $on, $cols);
        /* LEFT JOIN prxgt_acc_type_asset */
        $on = $asAsset . '.' . EntityTypeAsset::ATTR_ID . '=' . $asAccDebit . '.' . EntityAccount::ATTR_ASSET_TYPE_ID;
        $result->joinLeft($tblAsset, $on, $cols);
        return $result;
    }

    public function getQueryToSelect()
    {
        $result = $this->_conn->select();
        /* aliases and tables */
        $asAsset = Repo::AS_ASSET;
        $asAccCredit = Repo::AS_ACC_CREDIT;
        $asAccDebit = Repo::AS_ACC_DEBIT;
        $asCustCredit = Repo::AS_CUST_CREDIT;
        $asCustDebit = Repo::AS_CUST_DEBIT;

        $asTrans = Repo::AS_TRANS;
        //
        $tblAsset = [$asAsset => $this->_resource->getTableName(EntityTypeAsset::ENTITY_NAME)];
        $tblAccCredit = [$asAccCredit => $this->_resource->getTableName(EntityAccount::ENTITY_NAME)];
        $tblAccDebit = [$asAccDebit => $this->_resource->getTableName(EntityAccount::ENTITY_NAME)];
        $tblCustCredit = [$asCustCredit => $this->_resource->getTableName(Cfg::ENTITY_MAGE_CUSTOMER)];
        $tblCustDebit = [$asCustDebit => $this->_resource->getTableName(Cfg::ENTITY_MAGE_CUSTOMER)];
        $tblTrans = [$asTrans => $this->_resource->getTableName(EntityTrans::ENTITY_NAME)];
        /* SELECT FROM prxgt_acc_transaction */
        $cols = [
            AggEntity::AS_ID_TRANS => EntityTrans::ATTR_ID,
            AggEntity::AS_ID_OPER => EntityTrans::ATTR_OPERATION_ID,
            AggEntity::AS_DATE_APPLIED => EntityTrans::ATTR_DATE_APPLIED,
            AggEntity::AS_VALUE => EntityTrans::ATTR_VALUE,
            AggEntity::AS_NOTE => EntityTrans::ATTR_NOTE
        ];
        $result->from($tblTrans, $cols);
        /* LEFT JOIN prxgt_acc_account DEBIT */
        $on = $asAccDebit . '.' . EntityAccount::ATTR_ID . '=' . $asTrans . '.' . EntityTrans::ATTR_DEBIT_ACC_ID;
        $cols = [];
        $result->joinLeft($tblAccDebit, $on, $cols);
        /* LEFT JOIN customer_entity */
        $on = $asCustDebit . '.' . Cfg::E_CUSTOMER_A_ENTITY_ID . '=' . $asAccDebit . '.' . EntityAccount::ATTR_CUST_ID;
        $cols = [
            AggEntity::AS_DEBIT => Cfg::E_CUSTOMER_A_EMAIL
        ];
        $result->joinLeft($tblCustDebit, $on, $cols);
        /* LEFT JOIN prxgt_acc_account CREDIT */
        $on = $asAccCredit . '.' . EntityAccount::ATTR_ID . '=' . $asTrans . '.' . EntityTrans::ATTR_CREDIT_ACC_ID;
        $cols = [];
        $result->joinLeft($tblAccCredit, $on, $cols);
        /* LEFT JOIN customer_entity */
        $on = $asCustCredit . '.' . Cfg::E_CUSTOMER_A_ENTITY_ID . '=' . $asAccCredit . '.' . EntityAccount::ATTR_CUST_ID;
        $cols = [
            AggEntity::AS_CREDIT => Cfg::E_CUSTOMER_A_EMAIL
        ];
        $result->joinLeft($tblCustCredit, $on, $cols);
        /* LEFT JOIN prxgt_acc_type_asset */
        $on = $asAsset . '.' . EntityTypeAsset::ATTR_ID . '=' . $asAccDebit . '.' . EntityAccount::ATTR_ASSET_TYPE_ID;
        $cols = [
            AggEntity::AS_ASSET => EntityTypeAsset::ATTR_CODE
        ];
        $result->joinLeft($tblAsset, $on, $cols);
        return $result;
    }
}