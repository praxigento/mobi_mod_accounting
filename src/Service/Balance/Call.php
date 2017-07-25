<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Service\Balance;

use Praxigento\Accounting\Config as Cfg;
use Praxigento\Accounting\Data\Entity\Balance;
use Praxigento\Accounting\Data\Entity\Log\Change\Admin as ELogChangeAdmin;
use Praxigento\Core\Tool\IPeriod;

/**
 * TODO: split this service (one operation per class)
 *
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Call
    extends \Praxigento\Core\Service\Base\Call
    implements \Praxigento\Accounting\Service\IBalance
{
    /** @var \Praxigento\Core\Transaction\Database\IManager */
    protected $manTrans;
    /** @var \Praxigento\Accounting\Repo\Entity\Account */
    protected $repoAccount;
    /** @var \Praxigento\Accounting\Repo\Entity\Balance */
    protected $repoBalance;
    /** @var \Praxigento\Accounting\Repo\Entity\Log\Change\Admin */
    protected $repoLogChangeAdmin;
    /** @var \Praxigento\Accounting\Repo\IModule */
    protected $repoMod;
    /** @var \Praxigento\Accounting\Repo\Entity\Operation */
    protected $repoOperation;
    /** @var \Praxigento\Accounting\Repo\Entity\Transaction */
    protected $repoTransaction;
    /** @var \Praxigento\Accounting\Repo\Entity\Type\Asset */
    protected $repoTypeAsset;
    /** @var \Praxigento\Accounting\Repo\Entity\Type\Operation */
    protected $repoTypeOper;
    /** @var \Magento\Framework\App\ResourceConnection */
    protected $resource;
    /** @var Sub\CalcSimple Simple balance calculator. */
    protected $subCalcSimple;
    /** @var \Praxigento\Core\Tool\IDate */
    protected $toolDate;
    /** @var  \Praxigento\Core\Tool\IPeriod */
    protected $toolPeriod;

    public function __construct(
        \Praxigento\Core\Fw\Logger\App $logger,
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Core\Transaction\Database\IManager $manTrans,
        \Praxigento\Core\Tool\IDate $toolDate,
        \Praxigento\Core\Tool\IPeriod $toolPeriod,
        \Praxigento\Accounting\Repo\IModule $repoMod,
        \Praxigento\Accounting\Repo\Entity\Account $repoAccount,
        \Praxigento\Accounting\Repo\Entity\Balance $repoBalance,
        \Praxigento\Accounting\Repo\Entity\Operation $repoOperation,
        \Praxigento\Accounting\Repo\Entity\Transaction $repoTransaction,
        \Praxigento\Accounting\Repo\Entity\Type\Asset $repoTypeAsset,
        \Praxigento\Accounting\Repo\Entity\Type\Operation $repoTypeOper,
        \Praxigento\Accounting\Repo\Entity\Log\Change\Admin $repoLogChangeAdmin,
        Sub\CalcSimple $subCalcSimple
    ) {
        parent::__construct($logger, $manObj);
        $this->resource = $resource;
        $this->manTrans = $manTrans;
        $this->toolDate = $toolDate;
        $this->toolPeriod = $toolPeriod;
        $this->repoMod = $repoMod;
        $this->repoAccount = $repoAccount;
        $this->repoBalance = $repoBalance;
        $this->repoOperation = $repoOperation;
        $this->repoTransaction = $repoTransaction;
        $this->repoTypeAsset = $repoTypeAsset;
        $this->repoTypeOper = $repoTypeOper;
        $this->repoLogChangeAdmin = $repoLogChangeAdmin;
        $this->subCalcSimple = $subCalcSimple;
    }

    public function calc(Request\Calc $request)
    {
        $result = new Response\Calc();
        $assetTypeId = $request->getAssetTypeId();
        $assetTypeCode = $request->getAssetTypeCode();
        $dateTo = $request->getDateTo();
        /* get the last balance date */
        $reqLastDate = new Request\GetLastDate();
        $reqLastDate->setAssetTypeId($assetTypeId);
        $reqLastDate->setAssetTypeCode($assetTypeCode);
        $respLastDate = $this->getLastDate($reqLastDate);
        $lastDate = $respLastDate->getLastDate();
        $balances = $this->repoBalance->getOnDate($assetTypeId, $lastDate);
        /* check date to */
        if (is_null($dateTo)) {
            /* use 'yesterday' */
            $dtMageNow = $this->toolDate->getMageNow();
            $today = $this->toolPeriod->getPeriodCurrent($dtMageNow);
            $dateTo = $this->toolPeriod->getPeriodPrev($today);
        }
        /* get transactions for period */
        if ($lastDate) {
            /* first date should be after balance last date */
            $dtFrom = $this->toolPeriod->getTimestampNextFrom($lastDate);
            $dtTo = $this->toolPeriod->getTimestampTo($dateTo);
            $trans = $this->repoTransaction->getForPeriod($assetTypeId, $dtFrom, $dtTo);
            $updates = $this->subCalcSimple->calcBalances($balances, $trans);
            $this->repoBalance->updateBalances($updates);
            $result->markSucceed();
        }
        return $result;
    }

    public function change(Request\Change $request)
    {
        $result = new Response\Reset();
        $accCustId = $request->getCustomerAccountId();
        $adminUserId = $request->getAdminUserId();
        $value = $request->getChangeValue();
        $def = $this->manTrans->begin();
        try {
            /* get account's asset type by ID */
            $assetTypeId = $this->repoAccount->getAssetTypeId($accCustId);
            /* get representative account id for given asset type */
            $accRepresId = $this->repoAccount->getRepresentativeAccountId($assetTypeId);
            /* get operation type by code and date performed */
            $operTypeId = $this->repoTypeOper->getIdByCode(Cfg::CODE_TYPE_OPER_CHANGE_BALANCE);
            $dateNow = $this->toolDate->getUtcNowForDb();
            /* create operation */
            $operation = new \Praxigento\Accounting\Data\Entity\Operation();
            $operation->setTypeId($operTypeId);
            $operation->setDatePerformed($dateNow);
            $operId = $this->repoOperation->create($operation);
            /* create transaction */
            $trans = new \Praxigento\Accounting\Data\Entity\Transaction();
            $trans->setOperationId($operId);
            $trans->setDateApplied($dateNow);
            if ($value > 0) {
                $trans->setDebitAccId($accRepresId);
                $trans->setCreditAccId($accCustId);
            } else {
                $trans->setDebitAccId($accCustId);
                $trans->setCreditAccId($accRepresId);
            }
            $trans->setValue(abs($value));
            $this->repoTransaction->create($trans);
            /* log details (operator name who performs the operation) */
            $log = new ELogChangeAdmin();
            $log->setOperationRef($operId);
            $log->setUserRef($adminUserId);
            $this->repoLogChangeAdmin->create($log);
            $this->manTrans->commit($def);
            $result->markSucceed();
        } finally {
            $this->manTrans->end($def);
        }
        return $result;
    }

    public function getBalancesOnDate(Request\GetBalancesOnDate $request)
    {
        $result = new Response\GetBalancesOnDate();
        $dateOn = $request->getDate();
        $assetTypeId = $request->getAssetTypeId();
        $rows = $this->repoBalance->getOnDate($assetTypeId, $dateOn);
        if (count($rows) > 0) {
            $result->set($rows);
            $result->markSucceed();
        }
        return $result;
    }

    public function getLastDate(Request\GetLastDate $request)
    {
        $result = new Response\GetLastDate();
        $assetTypeId = $request->getAssetTypeId();
        $assetTypeCode = $request->getAssetTypeCode();
        if (is_null($assetTypeId)) {
            $assetTypeId = $this->repoTypeAsset->getIdByCode($assetTypeCode);
        }
        /* get the maximal date for balance */
        $balanceMaxDate = $this->repoBalance->getMaxDate($assetTypeId);
        if ($balanceMaxDate) {
            /* there is balance data */
            //$dayBefore = $this->_toolPeriod->getPeriodPrev($balanceMaxDate, IPeriod::TYPE_DAY);
            $result->set([Response\GetLastDate::LAST_DATE => $balanceMaxDate]);
            $result->markSucceed();
        } else {
            /* there is no balance data yet, get transaction with minimal date */
            $transactionMinDate = $this->repoTransaction->getMinDateApplied($assetTypeId);
            if ($transactionMinDate) {
                $period = $this->toolPeriod->getPeriodCurrentOld($transactionMinDate);
                $dayBefore = $this->toolPeriod->getPeriodPrev($period, IPeriod::TYPE_DAY);
                $result->set([Response\GetLastDate::LAST_DATE => $dayBefore]);
                $result->markSucceed();
            }
        }
        return $result;
    }

    public function reset(Request\Reset $request)
    {
        $result = new Response\Reset();
        $dateFrom = $request->getDateFrom();
        $conn = $this->resource->getConnection();
        $quoted = $conn->quote($dateFrom);
        $where = Balance::ATTR_DATE . '>=' . $quoted;
        $rows = $this->repoBalance->delete($where);
        if ($rows !== false) {
            $result->setRowsDeleted($rows);
            $result->markSucceed();
        }
        return $result;
    }
}