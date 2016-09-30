<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Repo\Agg\Def\Account;

use Praxigento\Accounting\Config as Cfg;
use Praxigento\Accounting\Data\Agg\Account as AggEntity;
use Praxigento\Accounting\Data\Entity\Account as EAccount;
use Praxigento\Accounting\Data\Entity\Type\Asset as ETypeAsset;
use Praxigento\Accounting\Repo\Agg\IAccount as AggRepo;

/**
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
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
        $asAcc = AggRepo::AS_ACCOUNT;
        $asCust = AggRepo::AS_CUSTOMER;
        $asAsset = AggRepo::AS_TYPE_ASSET;
        //
        $tblAcc = [$asAcc => $this->_resource->getTableName(EAccount::ENTITY_NAME)];
        $tblCust = [$asCust => $this->_resource->getTableName(Cfg::ENTITY_MAGE_CUSTOMER)];
        $tblAsset = [$asAsset => $this->_resource->getTableName(ETypeAsset::ENTITY_NAME)];
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
        $tblAcc = [$asAcc => $this->_resource->getTableName(EAccount::ENTITY_NAME)];
        $tblCust = [$asCust => $this->_resource->getTableName(Cfg::ENTITY_MAGE_CUSTOMER)];
        $tblAsset = [$asAsset => $this->_resource->getTableName(ETypeAsset::ENTITY_NAME)];
        /* SELECT FROM prxgt_acc_account */
        $cols = [
            AggEntity::AS_ID => EAccount::ATTR_ID,
            AggEntity::AS_BALANCE => EAccount::ATTR_BALANCE
        ];
        $result->from($tblAcc, $cols);
        /* LEFT JOIN prxgt_acc_type_asset */
        $cond = $asAsset . '.' . ETypeAsset::ATTR_ID . '=' . $asAcc . '.' . EAccount::ATTR_ASSET_TYPE_ID;
        $cols = [
            AggEntity::AS_ASSET => ETypeAsset::ATTR_CODE
        ];
        $result->joinLeft($tblAsset, $cond, $cols);
        /* LEFT JOIN customer_entity */
        $cond = $asCust . '.' . Cfg::E_CUSTOMER_A_ENTITY_ID . '=' . $asAcc . '.' . EAccount::ATTR_CUST_ID;
        $expValue = 'CONCAT(' . Cfg::E_CUSTOMER_A_FIRSTNAME . ", ' ', " . Cfg::E_CUSTOMER_A_LASTNAME . ')';
        $exp = new \Praxigento\Core\Repo\Query\Expression($expValue);
        $cols = [
            AggEntity::AS_CUST_NAME => $exp,
            AggEntity::AS_CUST_EMAIL => Cfg::E_CUSTOMER_A_EMAIL
        ];
        $result->joinLeft($tblCust, $cond, $cols);
        return $result;
    }
}