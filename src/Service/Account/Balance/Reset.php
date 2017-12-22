<?php
/**
 * File creator: makhovdmitrii@inbox.ru
 */

namespace Praxigento\Accounting\Service\Account\Balance;

use Praxigento\Accounting\Repo\Entity\Data\Balance as ABalance;
use Praxigento\Accounting\Service\Account\Balance\Reset\Request as ARequest;
use Praxigento\Accounting\Service\Account\Balance\Reset\Response as AResponse;


class Reset
{
    /** @var \Praxigento\Accounting\Repo\Entity\Balance */
    protected $repoBalance;
    /** @var \Magento\Framework\App\ResourceConnection */
    protected $resource;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Accounting\Repo\Entity\Balance $repoBalance
    )
    {
        $this->resource = $resource;
        $this->repoBalance = $repoBalance;
    }

    /**
     * Reset balance history for all accounts on dates after requested.
     * @param ARequest $request
     * @return AResponse
     */
    public function exec($request)
    {
        $result = new AResponse();
        $dateFrom = $request->getDateFrom();
        $conn = $this->resource->getConnection();
        $quoted = $conn->quote($dateFrom);
        $where = ABalance::ATTR_DATE . '>=' . $quoted;
        $rows = $this->repoBalance->delete($where);
        if ($rows !== false) {
            $result->setRowsDeleted($rows);
            $result->markSucceed();
        }
        return $result;
    }
}