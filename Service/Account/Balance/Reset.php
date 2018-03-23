<?php
/**
 * File creator: makhovdmitrii@inbox.ru
 */

namespace Praxigento\Accounting\Service\Account\Balance;

use Praxigento\Accounting\Repo\Data\Balance as ABalance;
use Praxigento\Accounting\Service\Account\Balance\Reset\Request as ARequest;
use Praxigento\Accounting\Service\Account\Balance\Reset\Response as AResponse;


class Reset
{
    /** @var \Praxigento\Accounting\Repo\Dao\Balance */
    private $daoBalance;

    public function __construct(
        \Praxigento\Accounting\Repo\Dao\Balance $daoBalance
    ) {
        $this->daoBalance = $daoBalance;
    }

    /**
     * Reset balance history for all accounts on dates after requested.
     * @param ARequest $request
     * @return AResponse
     */
    public function exec($request)
    {
        assert($request instanceof ARequest);
        $result = new AResponse();
        $dateFrom = $request->getDateFrom();
        $conn = $this->daoBalance->getConnection();
        $quoted = $conn->quote($dateFrom);
        $where = ABalance::A_DATE . '>=' . $quoted;
        $rows = $this->daoBalance->delete($where);
        if ($rows !== false) {
            $result->setRowsDeleted($rows);
            $result->markSucceed();
        }
        return $result;
    }
}