<?php
/**
 * Facade for current module for dependent modules repos.
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Lib\Repo\Def;

use Praxigento\Accounting\Config as Cfg;
use Praxigento\Accounting\Lib\Entity\Account;
use Praxigento\Accounting\Lib\Entity\Balance;
use Praxigento\Accounting\Lib\Entity\Transaction;
use Praxigento\Accounting\Lib\Entity\Type\Asset as TypeAsset;
use Praxigento\Accounting\Lib\Entity\Type\Operation as TypeOperation;
use Praxigento\Accounting\Lib\Repo\IModule;
use Praxigento\Core\Lib\Entity\Type\Base as TypeBase;
use Praxigento\Core\Lib\Repo\Def\Base;

class Module extends Base implements IModule
{

    const ADMIN_WEBSITE_ID = 0;
    const CUSTOMER_REPRESENTATIVE_EMAIL = 'MOBI_REPRESENTATIVE';
    /**
     * Cache for ID of the representative customer.
     * @var int
     */
    protected $_cachedRepresentativeCustomerId;
    /** @var  \Praxigento\Core\Lib\Repo\IBasic */
    protected $_repoBasic;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $rsrcConn,
        \Praxigento\Core\Lib\Repo\IBasic $repoBasic
    ) {
        parent::__construct($rsrcConn);
        $this->_repoBasic = $repoBasic;
    }

    /**
     * Use this method in the integration tests.
     */
    public function cacheReset()
    {
        $this->_cachedRepresentativeCustomerId = null;
    }

    /**
     * SELECT
     * `b`.`date`
     * FROM `prxgt_acc_account` AS `a`
     * LEFT JOIN `prxgt_acc_balance` AS `b`
     * ON a.id = b.account_id
     * WHERE (a.id = :typeId)
     * ORDER BY `b`.`date` DESC
     *
     * @param int $assetTypeId
     *
     * @return string YYYYMMDD
     */
    public function getBalanceMaxDate($assetTypeId = null)
    {
        $asAccount = 'a';
        $asBalance = 'b';
        $tblAccount = $this->_dba->getTableName(Account::ENTITY_NAME);
        $tblBalance = $this->_dba->getTableName(Balance::ENTITY_NAME);
        /* select from account */
        $query = $this->_dba->select();
        $query->from([$asAccount => $tblAccount], []);
        /* join balance */
        $on = $asAccount . '.' . Account::ATTR_ID . '=' . $asBalance . '.' . Balance::ATTR_ACCOUNT_ID;
        $query->joinLeft([$asBalance => $tblBalance], $on, [Balance::ATTR_DATE]);
        /* where */
        $query->where($asAccount . '.' . Account::ATTR_ASSET_TYPE__ID . '=:typeId');
        $bind = ['typeId' => $assetTypeId];
        /* order by */
        $query->order([$asBalance . '.' . Balance::ATTR_DATE . ' DESC']);
        /* perform query */
        // $sql = (string)$query;
        $result = $this->_dba->fetchOne($query, $bind);
        return $result;
    }

    /**
     * Select balances on date by asset type.
     *
     * SELECT
     * `acc`.`id`,
     * `acc`.`customer_id`,
     * `bal`.`date`,
     * `bal`.`opening_balance`,
     * `bal`.`total_debit`,
     * `bal`.`total_credit`,
     * `bal`.`closing_balance`
     * FROM `prxgt_acc_account` AS `acc`
     * LEFT JOIN (SELECT
     * `bal4Max`.`account_id`,
     * MAX(`bal4Max`.`date`) AS date_max
     * FROM `prxgt_acc_balance` AS `bal4Max`
     * WHERE (bal4Max.date <= :date)
     * GROUP BY `bal4Max`.`account_id`) AS `balMax`
     * ON balMax.account_id = acc.id
     * INNER JOIN `prxgt_acc_balance` AS `bal`
     * ON balMax.account_id = bal.account_id
     * AND balMax.date_max = bal.date
     * WHERE (acc.asset_type_id = :asset_type_id)
     * AND (bal.date IS NOT NULL)
     *
     * @param $assetTypeId
     * @param $yyyymmdd
     */
    public function getBalancesOnDate($assetTypeId, $yyyymmdd)
    {
        $result = [];
        $conn = $this->_dba;
        $bind = [];
        /* see MOBI-112 */
        $asAccount = 'acc';
        $asBal4Max = 'bal4Max';
        $asMax = 'balMax';
        $asBal = 'bal';
        $tblAccount = $this->_dba->getTableName(Account::ENTITY_NAME);
        $tblBalance = $this->_dba->getTableName(Balance::ENTITY_NAME);
        /* select MAX(date) from prxgt_acc_balance (internal select) */
        $q4Max = $conn->select();
        $colDateMax = 'date_max';
        $expMaxDate = 'MAX(`' . $asBal4Max . '`.`' . Balance::ATTR_DATE . '`) as ' . $colDateMax;
        $q4Max->from([$asBal4Max => $tblBalance], [Balance::ATTR_ACCOUNT_ID, $expMaxDate]);
        $q4Max->group($asBal4Max . '.' . Balance::ATTR_ACCOUNT_ID);
        $q4Max->where($asBal4Max . '.' . Balance::ATTR_DATE . '<=:date');
        $bind['date'] = $yyyymmdd;
        //        $sql4Max = (string)$q4Max;
        /* select from prxgt_acc_account */
        $query = $conn->select();
        $query->from([$asAccount => $tblAccount], [Account::ATTR_ID, Account::ATTR_CUST_ID]);
        /* left join $q4Max */
        $on = $asMax . '.' . Balance::ATTR_ACCOUNT_ID . '=' . $asAccount . '.' . Account::ATTR_ID;
        $cols = [];
        $query->joinLeft([$asMax => $q4Max], $on, $cols);
        /* join prxgt_acc_balance again (ON pab.account_id = m.account_id AND pab.date = m.date_max) */
        $on = $asMax . '.' . Balance::ATTR_ACCOUNT_ID . '=' . $asBal . '.' . Balance::ATTR_ACCOUNT_ID;
        $on .= ' AND ' . $asMax . '.' . $colDateMax . '=' . $asBal . '.' . Balance::ATTR_DATE;
        $cols = [
            Balance::ATTR_DATE,
            Balance::ATTR_BALANCE_OPEN,
            Balance::ATTR_TOTAL_DEBIT,
            Balance::ATTR_TOTAL_CREDIT,
            Balance::ATTR_BALANCE_CLOSE
        ];
        $query->joinLeft([$asBal => $tblBalance], $on, $cols);
        /* where */
        $whereByAssetType = $asAccount . '.' . Account::ATTR_ASSET_TYPE__ID . '=:asset_type_id';
        $whereByDate = $asBal . '.' . Balance::ATTR_DATE . ' IS NOT NULL';
        $query->where("$whereByAssetType AND $whereByDate");
        $bind['asset_type_id'] = (int)$assetTypeId;
        // $sql = (string)$qMain;
        $rows = $conn->fetchAll($query, $bind);
        foreach ($rows as $one) {
            $result[$one[Account::ATTR_ID]] = $one;
        }
        return $result;
    }

    public function getRepresentativeCustomerId()
    {
        if (is_null($this->_cachedRepresentativeCustomerId)) {
            $conn = $this->_dba;
            /* there is no cached value for the customer ID, select data from DB */
            $where = Cfg::E_CUSTOMER_A_EMAIL . '=' . $conn->quote(self::CUSTOMER_REPRESENTATIVE_EMAIL);
            $data = $this->_repoBasic->getEntities(Cfg::ENTITY_MAGE_CUSTOMER, Cfg::E_CUSTOMER_A_ENTITY_ID,
                $where);
            if (count($data) == 0) {
                $bind = [
                    Cfg::E_CUSTOMER_A_WEBSITE_ID => self::ADMIN_WEBSITE_ID,
                    Cfg::E_CUSTOMER_A_EMAIL => self::CUSTOMER_REPRESENTATIVE_EMAIL
                ];
                $id = $this->_repoBasic->addEntity(Cfg::ENTITY_MAGE_CUSTOMER, $bind);
                if ($id > 0) {
                    $this->_cachedRepresentativeCustomerId = $id;
                }
            } else {
                $first = reset($data);
                $this->_cachedRepresentativeCustomerId = $first[Cfg::E_CUSTOMER_A_ENTITY_ID];
            }
        }
        return $this->_cachedRepresentativeCustomerId;
    }

    /**
     * SELECT pat.date_applied
     * FROM prxgt_acc_type_asset pata
     * LEFT JOIN prxgt_acc_account paa
     * ON pata.id = paa.asset_type_id
     * LEFT JOIN prxgt_acc_transaction pat
     * ON paa.id = pat.debit_acc_id
     * WHERE pata.id = :assetTypeId
     * ORDER BY pat.date_applied ASC
     *
     * @param null $assetTypeId
     *
     * @return string
     */
    public function getTransactionMinDateApplied($assetTypeId = null)
    {
        $asAccount = 'a';
        $asTrans = 'trn';
        $tblAccount = $this->_dba->getTableName(Account::ENTITY_NAME);
        $tblTrans = $this->_dba->getTableName(Transaction::ENTITY_NAME);
        /* select from account */
        $query = $this->_dba->select();
        $query->from([$asAccount => $tblAccount], []);
        /* join transactions on debit account */
        $on = $asAccount . '.' . Account::ATTR_ID . '=' . $asTrans . '.' . Transaction::ATTR_DEBIT_ACC_ID;
        $query->joinLeft([$asTrans => $tblTrans], $on, [Transaction::ATTR_DATE_APPLIED]);
        /* where */
        $query->where($asAccount . '.' . Account::ATTR_ASSET_TYPE__ID . '=:typeId');
        $bind = ['typeId' => $assetTypeId];
        $query->where($asTrans . '.' . Transaction::ATTR_DATE_APPLIED . ' IS NOT NULL');
        /* order by */
        $query->order([$asTrans . '.' . Transaction::ATTR_DATE_APPLIED . ' ASC']);
        /* perform query */
        // $sql = (string)$query;
        $result = $this->_dba->fetchOne($query, $bind);
        return $result;
    }

    /**
     * SELECT
     * `trn`.*
     * FROM `prxgt_acc_account` AS `acc`
     * INNER JOIN `prxgt_acc_transaction` AS `trn`
     * ON acc.id = trn.debit_acc_id
     * WHERE (acc.asset_type_id = :asset_type_id)
     * AND (trn.id IS NOT NULL)
     * AND (trn.date_applied >= :date_from)
     * AND (trn.date_applied <= :date_to)
     * ORDER BY `trn`.`date_applied` ASC
     *
     * @param $assetTypeId
     * @param $timestampFrom
     * @param $timestampTo
     *
     * @return array
     */
    public function getTransactionsForPeriod($assetTypeId, $timestampFrom, $timestampTo)
    {
        $paramAssetType = $this->_dba->quote($assetTypeId, \Zend_Db::INT_TYPE);
        $asAccount = 'acc';
        $asTrans = 'trn';
        $tblAccount = $this->_dba->getTableName(Account::ENTITY_NAME);
        $tblTrans = $this->_dba->getTableName(Transaction::ENTITY_NAME);
        /* select from prxgt_acc_account  */
        $query = $this->_dba->select();
        $query->from([$asAccount => $tblAccount], []);
        /* join prxgt_acc_transaction  */
        $on = $asAccount . '.' . Account::ATTR_ID . '=' . $asTrans . '.' . Transaction::ATTR_DEBIT_ACC_ID;
        $query->join([$asTrans => $tblTrans], $on);
        /* where */
        $query->where($asAccount . '.' . Account::ATTR_ASSET_TYPE__ID . '=:asset_type_id');
        $query->where($asTrans . '.' . Transaction::ATTR_ID . ' IS NOT NULL');
        $query->where($asTrans . '.' . Transaction::ATTR_DATE_APPLIED . '>=:date_from');
        $query->where($asTrans . '.' . Transaction::ATTR_DATE_APPLIED . '<=:date_to');
        $bind = [
            'asset_type_id' => $paramAssetType,
            'date_from' => $timestampFrom,
            'date_to' => $timestampTo
        ];
        /* order by */
        $query->order($asTrans . '.' . Transaction::ATTR_DATE_APPLIED . ' ASC');
        // $sql = (string)$query;
        $result = $this->_dba->fetchAll($query, $bind);
        return $result;
    }

    public function getTypeAssetIdByCode($code)
    {
        $tbl = $this->_dba->getTableName(TypeAsset::ENTITY_NAME);
        /** @var  $query \Zend_Db_Select */
        $query = $this->_dba->select();
        $query->from($tbl);
        $query->where(TypeBase::ATTR_CODE . '=:code');
        // $sql = (string)$query;
        $data = $this->_dba->fetchRow($query, ['code' => $code]);
        $result = isset($data[TypeBase::ATTR_ID]) ? $data[TypeBase::ATTR_ID] : null;
        return $result;
    }

    public function getTypeOperationIdByCode($code)
    {
        $tbl = $this->_dba->getTableName(TypeOperation::ENTITY_NAME);
        /** @var  $query \Zend_Db_Select */
        $query = $this->_dba->select();
        $query->from($tbl);
        $query->where(TypeBase::ATTR_CODE . '=:code');
        // $sql = (string)$query;
        $data = $this->_dba->fetchRow($query, ['code' => $code]);
        $result = isset($data[TypeBase::ATTR_ID]) ? $data[TypeBase::ATTR_ID] : null;
        return $result;
    }

    public function updateBalances($updateData)
    {
        $this->_dba->beginTransaction();
        $tbl = $this->_dba->getTableName(Balance::ENTITY_NAME);
        foreach ($updateData as $accountId => $byDate) {
            foreach ($byDate as $date => $data) {
                $this->_dba->insert($tbl, $data);
            }
        }
        $this->_dba->commit();
    }
}