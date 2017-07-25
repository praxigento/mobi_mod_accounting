<?php
/**
 * Facade for current module for dependent modules repos.
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Repo\Def;

use Praxigento\Accounting\Config as Cfg;
use Praxigento\Accounting\Data\Entity\Account;
use Praxigento\Accounting\Data\Entity\Balance;
use Praxigento\Accounting\Data\Entity\Transaction;
use Praxigento\Accounting\Repo\IModule;
use Praxigento\Core\Repo\Def\Db;

class Module extends Db implements IModule
{

    const ADMIN_WEBSITE_ID = 0;
    const CUSTOMER_REPRESENTATIVE_EMAIL = Cfg::CUSTOMER_REPRESENTATIVE_EMAIL;
    /**
     * Cache for ID of the representative customer.
     * @var int
     */
    protected $_cachedRepresCustId;
    /** @var \Praxigento\Accounting\Repo\Entity\Def\Account */
    protected $_repoAccount;
    /** @var  \Praxigento\Core\Repo\IGeneric */
    protected $_repoGeneric;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Core\Repo\IGeneric $repoGeneric,
        \Praxigento\Accounting\Repo\Entity\Def\Account $repoAccount
    ) {
        parent::__construct($resource);
        $this->_repoGeneric = $repoGeneric;
        $this->_repoAccount = $repoAccount;
    }

    /**
     * Use this method in the integration tests.
     */
    public function cacheReset()
    {
        $this->_cachedRepresCustId = null;
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
        $tblAccount = $this->resource->getTableName(Account::ENTITY_NAME);
        $tblBalance = $this->resource->getTableName(Balance::ENTITY_NAME);
        /* select from account */
        $query = $this->conn->select();
        $query->from([$asAccount => $tblAccount], []);
        /* join balance */
        $on = $asAccount . '.' . Account::ATTR_ID . '=' . $asBalance . '.' . Balance::ATTR_ACCOUNT_ID;
        $query->joinLeft([$asBalance => $tblBalance], $on, [Balance::ATTR_DATE]);
        /* where */
        $query->where($asAccount . '.' . Account::ATTR_ASSET_TYPE_ID . '=:typeId');
        $bind = ['typeId' => $assetTypeId];
        /* order by */
        $query->order([$asBalance . '.' . Balance::ATTR_DATE . ' DESC']);
        /* perform query */
        // $sql = (string)$query;
        $result = $this->conn->fetchOne($query, $bind);
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
        $conn = $this->conn;
        $bind = [];
        /* see MOBI-112 */
        $asAccount = 'acc';
        $asBal4Max = 'bal4Max';
        $asMax = 'balMax';
        $asBal = 'bal';
        $tblAccount = $this->resource->getTableName(Account::ENTITY_NAME);
        $tblBalance = $this->resource->getTableName(Balance::ENTITY_NAME);
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
        $whereByAssetType = $asAccount . '.' . Account::ATTR_ASSET_TYPE_ID . '=:asset_type_id';
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

    public function getRepresentativeAccountId($assetTypeId)
    {
        /* TODO: add cache for accounts ids */
        $result = null;
        $custId = $this->getRepresentativeCustomerId();
        if ($custId) {
            $found = $this->_repoAccount->getByCustomerId($custId, $assetTypeId);
            if ($found) {
                $result = $found->getId();
            }
        }
        return $result;
    }

    public function getRepresentativeCustomerId()
    {
        if (is_null($this->_cachedRepresCustId)) {
            $conn = $this->conn;
            /* there is no cached value for the customer ID, select data from DB */
            $where = Cfg::E_CUSTOMER_A_EMAIL . '=' . $conn->quote(self::CUSTOMER_REPRESENTATIVE_EMAIL);
            $data = $this->_repoGeneric->getEntities(Cfg::ENTITY_MAGE_CUSTOMER, Cfg::E_CUSTOMER_A_ENTITY_ID,
                $where);
            if (count($data) == 0) {
                $bind = [
                    Cfg::E_CUSTOMER_A_WEBSITE_ID => self::ADMIN_WEBSITE_ID,
                    Cfg::E_CUSTOMER_A_EMAIL => self::CUSTOMER_REPRESENTATIVE_EMAIL
                ];
                $id = $this->_repoGeneric->addEntity(Cfg::ENTITY_MAGE_CUSTOMER, $bind);
                if ($id > 0) {
                    $this->_cachedRepresCustId = $id;
                }
            } else {
                $first = reset($data);
                $this->_cachedRepresCustId = $first[Cfg::E_CUSTOMER_A_ENTITY_ID];
            }
        }
        return $this->_cachedRepresCustId;
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
        $tblAccount = $this->resource->getTableName(Account::ENTITY_NAME);
        $tblTrans = $this->resource->getTableName(Transaction::ENTITY_NAME);
        /* select from account */
        $query = $this->conn->select();
        $query->from([$asAccount => $tblAccount], []);
        /* join transactions on debit account */
        $on = $asAccount . '.' . Account::ATTR_ID . '=' . $asTrans . '.' . Transaction::ATTR_DEBIT_ACC_ID;
        $query->joinLeft([$asTrans => $tblTrans], $on, [Transaction::ATTR_DATE_APPLIED]);
        /* where */
        $query->where($asAccount . '.' . Account::ATTR_ASSET_TYPE_ID . '=:typeId');
        $bind = ['typeId' => $assetTypeId];
        $query->where($asTrans . '.' . Transaction::ATTR_DATE_APPLIED . ' IS NOT NULL');
        /* order by */
        $query->order([$asTrans . '.' . Transaction::ATTR_DATE_APPLIED . ' ASC']);
        /* perform query */
        // $sql = (string)$query;
        $result = $this->conn->fetchOne($query, $bind);
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
        $paramAssetType = $this->conn->quote($assetTypeId, \Zend_Db::INT_TYPE);
        $asAccount = 'acc';
        $asTrans = 'trn';
        $tblAccount = $this->resource->getTableName(Account::ENTITY_NAME);
        $tblTrans = $this->resource->getTableName(Transaction::ENTITY_NAME);
        /* select from prxgt_acc_account  */
        $query = $this->conn->select();
        $query->from([$asAccount => $tblAccount], []);
        /* join prxgt_acc_transaction  */
        $on = $asAccount . '.' . Account::ATTR_ID . '=' . $asTrans . '.' . Transaction::ATTR_DEBIT_ACC_ID;
        $query->join([$asTrans => $tblTrans], $on);
        /* where */
        $query->where($asAccount . '.' . Account::ATTR_ASSET_TYPE_ID . '=:asset_type_id');
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
        $result = $this->conn->fetchAll($query, $bind);
        return $result;
    }

    public function updateBalances($updateData)
    {
        $this->conn->beginTransaction();
        $tbl = $this->resource->getTableName(Balance::ENTITY_NAME);
        foreach ($updateData as $accountId => $byDate) {
            foreach ($byDate as $date => $data) {
                $this->conn->insert($tbl, $data);
            }
        }
        $this->conn->commit();
    }
}