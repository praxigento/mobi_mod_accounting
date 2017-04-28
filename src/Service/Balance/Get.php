<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\BonusHybrid\Service\Calc;

use Praxigento\Accounting\Config as Cfg;
use Praxigento\Accounting\Repo\Query\Balance\OnDate\Closing\ByAsset\Builder as QBalanceClose;

class Get
    extends \Praxigento\Core\Service\Base\Call
    implements \Praxigento\Accounting\Service\Balance\IGet
{
    /** @var \Magento\Framework\DB\Adapter\AdapterInterface */
    protected $conn;
    /** @var \Praxigento\Accounting\Repo\Query\Balance\OnDate\Closing\ByAsset\Builder */
    protected $qbldBalClose;
    /** @var \Magento\Framework\App\ResourceConnection */
    protected $resource;
    /** @var  \Praxigento\Core\Tool\IPeriod */
    protected $toolPeriod;

    public function __construct(
        \Praxigento\Core\Fw\Logger\App $logger,
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Core\Tool\IPeriod $toolPeriod,
        \Praxigento\Accounting\Repo\Query\Balance\OnDate\Closing\ByAsset\Builder $qbldBalClose
    ) {
        parent::__construct($logger, $manObj);
        $this->resource = $resource;
        $this->conn = $this->resource->getConnection();
        $this->toolPeriod = $toolPeriod;
        $this->qbldBalClose = $qbldBalClose;

    }

    public function getBalancesOnDate(\Praxigento\Accounting\Service\Balance\Get\Request $request)
    {
        $result = new \Praxigento\Accounting\Service\Balance\Get\Response();
        $assetTypeId = $request->assetTypeId;
        $assetTypeCode = $request->assetTypeCode;
        $dateFrom = $request->dateFrom;
        $dateTo = $request->dateTo;

        /* analyze conditions */
        $dateFromBefore = $dateFrom;

        /* perform action */

        /* get balances on the end of the previous period */
        $qCloseBegin = $this->qbldBalClose->getSelectQuery();
        $bind = [
            QBalanceClose::BIND_ASSET_TYPE_ID => $assetTypeId,
            QBalanceClose::BIND_MAX_DATE => $dateFromBefore
        ];
        $rowsBegin = $this->conn->fetchAll($qCloseBegin, $bind);

        /* get balances on the end of this period */
        $qCloseBegin = $this->qbldBalClose->getSelectQuery();
        $bind = [
            QBalanceClose::BIND_ASSET_TYPE_ID => $assetTypeId,
            QBalanceClose::BIND_MAX_DATE => $dateTo
        ];
        $rowsEnd = $this->conn->fetchAll($qCloseBegin, $bind);

        /* compose PV delta for period */
        $bal = [];
        $sum = 0;
        foreach ($rowsEnd as $row) {
            $customerId = $row[QBalanceClose::A_CUST_ID];
            $accId = $row[QBalanceClose::A_ACC_ID];
            $balanceClose = $row[QBalanceClose::A_BALANCE];
            $data = new \Flancer32\Lib\Data();
            $data->set('accountId', $accId);
            $data->set('customerId', $customerId);
            $data->set('balanceClose', $balanceClose);
            $data->set('balanceOpen', 0);
            $data->set('turnover', $balanceClose);
            $bal[$customerId] = $data;
        }
        /* add opening balance and delta */
        foreach ($rowsBegin as $row) {
            $customerId = $row[QBalanceClose::A_CUST_ID];
            $balanceOpen = $row[QBalanceClose::A_BALANCE];
            $data = $bal[$customerId];
            $balanceClose = $data->get('balanceClose');
            $turnover = ($balanceClose - $balanceOpen);
            $data->set('balanceOpen', $balanceOpen);
            $data->set('turnover', $turnover);
            $bal[$customerId] = $data;
            $sum += $turnover;
        }
        if (abs($sum) > Cfg::DEF_ZERO) {
            throw new \Exception("Balances are not consistent. Total turnover should be equal to zero.");
        }

        return $result;
    }
}