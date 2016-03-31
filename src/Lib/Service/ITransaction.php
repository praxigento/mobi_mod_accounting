<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Lib\Service;

use Praxigento\Accounting\Lib\Service\Transaction\Request;
use Praxigento\Accounting\Lib\Service\Transaction\Response;

interface ITransaction {
    /**
     * Add new transaction and update current balances.
     *
     * @param Request\Add $request
     *
     * @return Response\Add
     */
    public function add(Request\Add $request);
}