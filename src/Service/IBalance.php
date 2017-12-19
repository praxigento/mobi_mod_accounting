<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Service;

use Praxigento\Accounting\Service\Balance\Request;
use Praxigento\Accounting\Service\Balance\Response;

interface IBalance
{

    /**
     * Get asset balances on the requested date.
     *
     * @param Request\GetBalancesOnDate $request
     *
     * @return Response\GetBalancesOnDate
     */
    public function getBalancesOnDate(Request\GetBalancesOnDate $request);

    /**
     * Calculate the last date for the balance of the asset.
     *
     * @param Request\GetLastDate $request
     *
     * @return Response\GetLastDate
     */
    public function getLastDate(Request\GetLastDate $request);

    /**
     * Reset balance history for all accounts on dates after requested.
     *
     * @param Request\Reset $request
     *
     * @return Response\Reset
     */
    public function reset(Request\Reset $request);
}