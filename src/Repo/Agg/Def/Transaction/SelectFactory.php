<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Repo\Agg\Def\Transaction;

use Praxigento\Accounting\Config as Cfg;
use Praxigento\Accounting\Data\Agg\Transaction as AggEntity;
use Praxigento\Accounting\Data\Entity\Account as EAccount;
use Praxigento\Accounting\Data\Entity\Transaction as ETransaction;
use Praxigento\Accounting\Data\Entity\Type\Asset as ETypeAsset;
use Praxigento\Accounting\Repo\Agg\Def\Transaction as Repo;

class SelectFactory
    extends \Praxigento\Core\Repo\Agg\BaseSelectFactory
{

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
        $tblAsset = [$asAsset => $this->_resource->getTableName(ETypeAsset::ENTITY_NAME)];
        $tblAccCredit = [$asAccCredit => $this->_resource->getTableName(EAccount::ENTITY_NAME)];
        $tblAccDebit = [$asAccDebit => $this->_resource->getTableName(EAccount::ENTITY_NAME)];
        $tblCustCredit = [$asCustCredit => $this->_resource->getTableName(Cfg::ENTITY_MAGE_CUSTOMER)];
        $tblCustDebit = [$asCustDebit => $this->_resource->getTableName(Cfg::ENTITY_MAGE_CUSTOMER)];
        $tblTrans = [$asTrans => $this->_resource->getTableName(ETransaction::ENTITY_NAME)];
        /* SELECT FROM prxgt_acc_transaction */
        $cols = [
            AggEntity::AS_ID_TRANS => ETransaction::ATTR_ID,
            AggEntity::AS_ID_OPER => ETransaction::ATTR_OPERATION_ID,
            AggEntity::AS_DATE_APPLIED => ETransaction::ATTR_DATE_APPLIED,
            AggEntity::AS_VALUE => ETransaction::ATTR_VALUE,
            AggEntity::AS_NOTE => ETransaction::ATTR_NOTE
        ];
        $result->from($tblTrans, $cols);
        /* LEFT JOIN prxgt_acc_account DEBIT */
        $cond = $asAccDebit . '.' . EAccount::ATTR_ID . '=' . $asTrans . '.' . ETransaction::ATTR_DEBIT_ACC_ID;
        $cols = [];
        $result->joinLeft($tblAccDebit, $cond, $cols);
        /* LEFT JOIN customer_entity */
        $cond = $asCustDebit . '.' . Cfg::E_CUSTOMER_A_ENTITY_ID . '=' . $asAccDebit . '.' . EAccount::ATTR_CUST_ID;
        $cols = [
            AggEntity::AS_DEBIT => Cfg::E_CUSTOMER_A_EMAIL
        ];
        $result->joinLeft($tblCustDebit, $cond, $cols);
        /* LEFT JOIN prxgt_acc_account CREDIT */
        $cond = $asAccCredit . '.' . EAccount::ATTR_ID . '=' . $asTrans . '.' . ETransaction::ATTR_CREDIT_ACC_ID;
        $cols = [];
        $result->joinLeft($tblAccCredit, $cond, $cols);
        /* LEFT JOIN customer_entity */
        $cond = $asCustCredit . '.' . Cfg::E_CUSTOMER_A_ENTITY_ID . '=' . $asAccCredit . '.' . EAccount::ATTR_CUST_ID;
        $cols = [
            AggEntity::AS_CREDIT => Cfg::E_CUSTOMER_A_EMAIL
        ];
        $result->joinLeft($tblCustCredit, $cond, $cols);
        /* LEFT JOIN prxgt_acc_type_asset */
        $cond = $asAsset . '.' . ETypeAsset::ATTR_ID . '=' . $asAccDebit . '.' . EAccount::ATTR_ASSET_TYPE_ID;
        $cols = [
            AggEntity::AS_ASSET => ETypeAsset::ATTR_CODE
        ];
        $result->joinLeft($tblAsset, $cond, $cols);
        return $result;
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
        $tblAsset = [$asAsset => $this->_resource->getTableName(ETypeAsset::ENTITY_NAME)];
        $tblAccCredit = [$asAccCredit => $this->_resource->getTableName(EAccount::ENTITY_NAME)];
        $tblAccDebit = [$asAccDebit => $this->_resource->getTableName(EAccount::ENTITY_NAME)];
        $tblCustCredit = [$asCustCredit => $this->_resource->getTableName(Cfg::ENTITY_MAGE_CUSTOMER)];
        $tblCustDebit = [$asCustDebit => $this->_resource->getTableName(Cfg::ENTITY_MAGE_CUSTOMER)];
        $tblTrans = [$asTrans => $this->_resource->getTableName(ETransaction::ENTITY_NAME)];
        /* SELECT FROM prxgt_acc_transaction */
        $expValue = 'COUNT(' . $asTrans . '.' . ETransaction::ATTR_ID . ')';
        $cols = new \Praxigento\Core\Repo\Query\Expression($expValue);
        $result->from($tblTrans, $cols);
        /* LEFT JOIN prxgt_acc_account DEBIT */
        $cond = $asAccDebit . '.' . EAccount::ATTR_ID . '=' . $asTrans . '.' . ETransaction::ATTR_DEBIT_ACC_ID;
        $cols = [];
        $result->joinLeft($tblAccDebit, $cond, $cols);
        /* LEFT JOIN customer_entity */
        $cond = $asCustDebit . '.' . Cfg::E_CUSTOMER_A_ENTITY_ID . '=' . $asAccDebit . '.' . EAccount::ATTR_CUST_ID;
        $result->joinLeft($tblCustDebit, $cond, $cols);
        /* LEFT JOIN prxgt_acc_account CREDIT */
        $cond = $asAccCredit . '.' . EAccount::ATTR_ID . '=' . $asTrans . '.' . ETransaction::ATTR_CREDIT_ACC_ID;
        $result->joinLeft($tblAccCredit, $cond, $cols);
        /* LEFT JOIN customer_entity */
        $cond = $asCustCredit . '.' . Cfg::E_CUSTOMER_A_ENTITY_ID . '=' . $asAccCredit . '.' . EAccount::ATTR_CUST_ID;
        $result->joinLeft($tblCustCredit, $cond, $cols);
        /* LEFT JOIN prxgt_acc_type_asset */
        $cond = $asAsset . '.' . ETypeAsset::ATTR_ID . '=' . $asAccDebit . '.' . EAccount::ATTR_ASSET_TYPE_ID;
        $result->joinLeft($tblAsset, $cond, $cols);
        return $result;
    }
}