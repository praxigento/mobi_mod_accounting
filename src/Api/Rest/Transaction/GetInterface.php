<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Api\Rest\Transaction;

/**
 * Get all transactions according to some selection conditions (search criteria).
 *
 * @deprecated TODO: use it or remove it.
 */
interface GetInterface
{
    /**
     * @param \Praxigento\Accounting\Api\Rest\Transaction\Get\Request $data
     * @return \Praxigento\Accounting\Api\Rest\Transaction\Get\Response
     */
    public function exec(\Praxigento\Accounting\Api\Rest\Transaction\Get\Request $data);
}