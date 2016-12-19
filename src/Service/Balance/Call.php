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
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Call
    extends \Praxigento\Core\Service\Base\Call
    implements \Praxigento\Accounting\Service\IBalance
{
    /** @var \Praxigento\Core\Transaction\Database\IManager */
    protected $_manTrans;
    /** @var \Praxigento\Accounting\Repo\Entity\IAccount */
    protected $_repoAccount;
    /** @var \Praxigento\Accounting\Repo\Entity\IBalance */
    protected $_repoBalance;
    /** @var \Praxigento\Accounting\Repo\IModule */
    protected $_repoMod;
    /** @var \Praxigento\Accounting\Repo\Entity\IOperation */
    protected $_repoOperation;
    /** @var \Praxigento\Accounting\Repo\Entity\ITransaction */
    protected $_repoTransaction;
    /** @var \Praxigento\Accounting\Repo\Entity\Type\IAsset */
    protected $_repoTypeAsset;
    /** @var \Praxigento\Accounting\Repo\Entity\Type\IOperation */
    protected $_repoTypeOper;
    /** @var \Praxigento\Accounting\Repo\Entity\Log\Change\IAdmin */
    protected $_repoLogChangeAdmin;
    /** @var Sub\CalcSimple Simple balance calculator. */
    protected $_subCalcSimple;
    /** @var  \Praxigento\Core\Tool\IPeriod */
    protected $_toolPeriod;
    /** @var \Praxigento\Core\Tool\IDate */
    protected $_toolDate;

    public function __construct(
        \Praxigento\Core\Fw\Logger\App $logger,
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Praxigento\Core\Transaction\Database\IManager $manTrans,
        \Praxigento\Core\Tool\IDate $toolDate,
        \Praxigento\Core\Tool\IPeriod $toolPeriod,
        \Praxigento\Accounting\Repo\IModule $repoMod,
        \Praxigento\Accounting\Repo\Entity\IAccount $repoAccount,
        \Praxigento\Accounting\Repo\Entity\IBalance $repoBalance,
        \Praxigento\Accounting\Repo\Entity\IOperation $repoOperation,
        \Praxigento\Accounting\Repo\Entity\ITransaction $repoTransaction,
        \Praxigento\Accounting\Repo\Entity\Type\IAsset $repoTypeAsset,
        \Praxigento\Accounting\Repo\Entity\Type\IOperation $repoTypeOper,
        \Praxigento\Accounting\Repo\Entity\Log\Change\IAdmin $repoLogChangeAdmin,
        Sub\CalcSimple $subCalcSimple
    ) {
        parent::__construct($logger, $manObj);
        $this->_manTrans = $manTrans;
        $this->_toolDate = $toolDate;
        $this->_toolPeriod = $toolPeriod;
        $this->_repoMod = $repoMod;
        $this->_repoAccount = $repoAccount;
        $this->_repoBalance = $repoBalance;
        $this->_repoOperation = $repoOperation;
        $this->_repoTransaction = $repoTransaction;
        $this->_repoTypeAsset = $repoTypeAsset;
        $this->_repoTypeOper = $repoTypeOper;
        $this->_repoLogChangeAdmin = $repoLogChangeAdmin;
        $this->_subCalcSimple = $subCalcSimple;
    }

    public function calc(Request\Calc $request)
    {
        $result = new Response\Calc();
        $assetTypeId = $request->getAssetTypeId();
        $dateTo = $request->getDateTo();
        /* get the last balance date */
        $reqLastDate = new Request\GetLastDate();
        $reqLastDate->setData(Request\GetLastDate::ASSET_TYPE_ID, $assetTypeId);
        $respLastDate = $this->getLastDate($reqLastDate);
        $lastDate = $respLastDate->getLastDate();
        $balances = $this->_repoBalance->getOnDate($assetTypeId, $lastDate);
        /* get transactions for period */
        $dtFrom = $this->_toolPeriod->getTimestampFrom($lastDate, IPeriod::TYPE_DAY);
        $dtTo = $this->_toolPeriod->getTimestampTo($dateTo, IPeriod::TYPE_DAY);
        $trans = $this->_repoTransaction->getForPeriod($assetTypeId, $dtFrom, $dtTo);
        $updates = $this->_subCalcSimple->calcBalances($balances, $trans);
        $this->_repoBalance->updateBalances($updates);
        $result->markSucceed();
        return $result;
    }

    public function change(Request\Change $request)
    {
        $result = new Response\Reset();
        $accCustId = $request->getCustomerAccountId();
        $adminUserId = $request->getAdminUserId();
        $value = $request->getChangeValue();
        $def = $this->_manTrans->begin();
        try {
            /* get account's asset type by ID */
            $assetTypeId = $this->_repoAccount->getAssetTypeId($accCustId);
            /* get representative account id for given asset type */
            $accRepresId = $this->_repoAccount->getRepresentativeAccountId($assetTypeId);
            /* get operation type by code and date performed */
            $operTypeId = $this->_repoTypeOper->getIdByCode(Cfg::CODE_TYPE_OPER_CHANGE_BALANCE);
            $dateNow = $this->_toolDate->getUtcNowForDb();
            /* create operation */
            $operation = new \Praxigento\Accounting\Data\Entity\Operation();
            $operation->setTypeId($operTypeId);
            $operation->setDatePerformed($dateNow);
            $operId = $this->_repoOperation->create($operation);
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
            $this->_repoTransaction->create($trans);
            /* log details (operator name who performs the operation) */
            $log = new ELogChangeAdmin();
            $log->setOperationRef($operId);
            $log->setUserRef($adminUserId);
            $this->_repoLogChangeAdmin->create($log);
            $this->_manTrans->commit($def);
            $result->markSucceed();
        } finally {
            $this->_manTrans->end($def);
        }
        return $result;
    }

    public function getBalancesOnDate(Request\GetBalancesOnDate $request)
    {
        $result = new Response\GetBalancesOnDate();
        $dateOn = $request->getDate();
        $assetTypeId = $request->getAssetTypeId();
        $rows = $this->_repoBalance->getOnDate($assetTypeId, $dateOn);
        if (count($rows) > 0) {
            $result->setData($rows);
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
            $assetTypeId = $this->_repoTypeAsset->getIdByCode($assetTypeCode);
        }
        /* get the maximal date for balance */
        $balanceMaxDate = $this->_repoBalance->getMaxDate($assetTypeId);
        if ($balanceMaxDate) {
            /* there is balance data */
            $dayBefore = $this->_toolPeriod->getPeriodPrev($balanceMaxDate, IPeriod::TYPE_DAY);
            $result->setData([Response\GetLastDate::LAST_DATE => $dayBefore]);
            $result->markSucceed();
        } else {
            /* there is no balance data yet, get transaction with minimal date */
            $transactionMinDate = $this->_repoTransaction->getMinDateApplied($assetTypeId);
            if ($transactionMinDate) {
                $period = $this->_toolPeriod->getPeriodCurrent($transactionMinDate);
                $dayBefore = $this->_toolPeriod->getPeriodPrev($period, IPeriod::TYPE_DAY);
                $result->setData([Response\GetLastDate::LAST_DATE => $dayBefore]);
                $result->markSucceed();
            }
        }
        return $result;
    }

    public function reset(Request\Reset $request)
    {
        $result = new Response\Reset();
        $dateFrom = $request->getDateFrom();
        /* TODO: quote $dateFrom to prevent SQL injects */
        $where = Balance::ATTR_DATE . '>=' . $dateFrom;
        $rows = $this->_repoBalance->delete($where);
        if ($rows !== false) {
            $result->setRowsDeleted($rows);
            $result->markSucceed();
        }
        return $result;
    }
}