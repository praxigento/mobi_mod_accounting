<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Service\Balance;

class Get
    extends \Praxigento\Core\Service\Base\Call
    implements \Praxigento\Accounting\Service\Balance\IGet
{
    /** @var \Magento\Framework\DB\Adapter\AdapterInterface */
    protected $conn;
    /** @var \Praxigento\Accounting\Repo\Query\Balance\OnDate\Closing\ByAsset\Builder */
    protected $qbldBalClose;
    /** @var \Praxigento\Accounting\Repo\Entity\Type\IAsset */
    protected $repoTypeAsset;
    /** @var \Magento\Framework\App\ResourceConnection */
    protected $resource;
    /** @var  \Praxigento\Core\Tool\IPeriod */
    protected $toolPeriod;

    public function __construct(
        \Praxigento\Core\Fw\Logger\App $logger,
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Core\Tool\IPeriod $toolPeriod,
        \Praxigento\Accounting\Repo\Query\Balance\OnDate\Closing\ByAsset\Builder $qbldBalClose,
        \Praxigento\Accounting\Repo\Entity\Type\IAsset $repoTypeAsset
    ) {
        parent::__construct($logger, $manObj);
        $this->resource = $resource;
        $this->conn = $this->resource->getConnection();
        $this->toolPeriod = $toolPeriod;
        $this->qbldBalClose = $qbldBalClose;
        $this->repoTypeAsset = $repoTypeAsset;

    }

    public function exec(\Praxigento\Accounting\Service\Balance\Get\Request $request)
    {
        $result = new \Praxigento\Accounting\Service\Balance\Get\Response();

        /* TODO: This was a initial version of the \Praxigento\Accounting\Service\Balance\Get\Turnover */

//        $assetTypeId = $request->assetTypeId;
//        $assetTypeCode = $request->assetTypeCode;
//        $dateFrom = $request->dateFrom;
//        $dateTo = $request->dateTo;
//
//        /* analyze conditions */
//        if (is_null($assetTypeId)) {
//            $assetTypeId = $this->repoTypeAsset->getIdByCode($assetTypeCode);
//        }
//        $dateFromBefore = $this->toolPeriod->getPeriodPrev($dateFrom);
//
//        /* perform action */
//
//        /* get balances on the end of the previous period */
//        $qCloseBegin = $this->qbldBalClose->getSelectQuery();
//        $bind = [
//            QBalanceClose::BIND_ASSET_TYPE_ID => $assetTypeId,
//            QBalanceClose::BIND_MAX_DATE => $dateFromBefore
//        ];
//        $rowsBegin = $this->conn->fetchAll($qCloseBegin, $bind);
//
//        /* get balances on the end of this period */
//        $qCloseBegin = $this->qbldBalClose->getSelectQuery();
//        $bind = [
//            QBalanceClose::BIND_ASSET_TYPE_ID => $assetTypeId,
//            QBalanceClose::BIND_MAX_DATE => $dateTo
//        ];
//        $rowsEnd = $this->conn->fetchAll($qCloseBegin, $bind);
//
//        /* compose asset delta for period */
//        $entries = [];
//        $sum = 0;
//        foreach ($rowsEnd as $row) {
//            $customerId = $row[QBalanceClose::A_CUST_ID];
//            $accId = $row[QBalanceClose::A_ACC_ID];
//            $balanceClose = $row[QBalanceClose::A_BALANCE];
//            $data = new \Praxigento\Accounting\Service\Balance\Get\Data\Entry();
//            $data->accountId = $accId;
//            $data->customerId = $customerId;
//            $data->balanceClose = $balanceClose;
//            $data->balanceOpen = 0;
//            $data->turnover = $balanceClose;
//            $entries[$customerId] = $data;
//        }
//        /* add opening balance and delta */
//        foreach ($rowsBegin as $row) {
//            $customerId = $row[QBalanceClose::A_CUST_ID];
//            $balanceOpen = $row[QBalanceClose::A_BALANCE];
//            /** @var \Praxigento\Accounting\Service\Balance\Get\Data\Entry $data */
//            $data = $entries[$customerId];
//            $balanceClose = $data->balanceClose;
//            $turnover = ($balanceClose - $balanceOpen);
//            $data->balanceOpen = $balanceOpen;
//            $data->turnover = $turnover;
//            $entries[$customerId] = $data;
//            $sum += $turnover;
//        }
//        if (abs($sum) > Cfg::DEF_ZERO) {
//            throw new \Exception("Balances are not consistent. Total turnover should be equal to zero.");
//        }
//        $result->entries = $entries;
        return $result;
    }
}