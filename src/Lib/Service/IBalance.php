<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Lib\Service;

use Praxigento\Accounting\Lib\Service\Balance\Request;
use Praxigento\Accounting\Lib\Service\Balance\Response;

interface IBalance {
    /**
     * Calculate asset balances up to given date (including).
     *
     * @param Request\Calc $request
     *
     * @return Response\Calc
     */
    public function calc(Request\Calc $request);

    /**
     * Calculate the last date for the balance of the asset.
     *
     * @param Request\GetLastDate $request
     *
     * @return Response\GetLastDate
     */
    public function getLastDate(Request\GetLastDate $request);

    /**
     * Get asset balances on the requested date.
     *
     * @param Request\GetBalancesOnDate $request
     *
     * @return Response\GetBalancesOnDate
     */
    public function getBalancesOnDate(Request\GetBalancesOnDate $request);

    /**
     * Reset balance history for all accounts on dates after requested.
     *
     * @param Request\Reset $request
     *
     * @return Response\Reset
     */
    public function reset(Request\Reset $request);
}