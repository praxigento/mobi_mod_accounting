<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Service\Balance;

use Praxigento\Accounting\Repo\Entity\Data\Balance;
use Praxigento\Core\Tool\IPeriod;

/**
 * TODO: split this service (one operation per class)
 *
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Call
    extends \Praxigento\Core\App\Service\Base\Call
    implements \Praxigento\Accounting\Service\IBalance
{
    /** @var \Praxigento\Core\App\Transaction\Database\IManager */
    protected $manTrans;
    /** @var \Praxigento\Accounting\Repo\Entity\Account */
    protected $repoAccount;
    /** @var \Praxigento\Accounting\Repo\Entity\Balance */
    protected $repoBalance;
    /** @var \Praxigento\Accounting\Repo\Entity\Log\Change\Admin */
    protected $repoLogChangeAdmin;
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
    /** @var \Praxigento\Core\Tool\IDate */
    protected $toolDate;
    /** @var  \Praxigento\Core\Tool\IPeriod */
    protected $toolPeriod;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Core\App\Transaction\Database\IManager $manTrans,
        \Praxigento\Core\Tool\IDate $toolDate,
        \Praxigento\Core\Tool\IPeriod $toolPeriod,
        \Praxigento\Accounting\Repo\Entity\Account $repoAccount,
        \Praxigento\Accounting\Repo\Entity\Balance $repoBalance,
        \Praxigento\Accounting\Repo\Entity\Operation $repoOperation,
        \Praxigento\Accounting\Repo\Entity\Transaction $repoTransaction,
        \Praxigento\Accounting\Repo\Entity\Type\Asset $repoTypeAsset,
        \Praxigento\Accounting\Repo\Entity\Type\Operation $repoTypeOper,
        \Praxigento\Accounting\Repo\Entity\Log\Change\Admin $repoLogChangeAdmin
    ) {
        parent::__construct($logger, $manObj);
        $this->resource = $resource;
        $this->manTrans = $manTrans;
        $this->toolDate = $toolDate;
        $this->toolPeriod = $toolPeriod;
        $this->repoAccount = $repoAccount;
        $this->repoBalance = $repoBalance;
        $this->repoOperation = $repoOperation;
        $this->repoTransaction = $repoTransaction;
        $this->repoTypeAsset = $repoTypeAsset;
        $this->repoTypeOper = $repoTypeOper;
        $this->repoLogChangeAdmin = $repoLogChangeAdmin;
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