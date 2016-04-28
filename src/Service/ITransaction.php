<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Service;

use Praxigento\Accounting\Service\Transaction\Request;
use Praxigento\Accounting\Service\Transaction\Response;

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