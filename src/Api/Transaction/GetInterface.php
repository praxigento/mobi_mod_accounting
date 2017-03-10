<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Api\Transaction;

/**
 * Get all transactions according to some selection conditions (search criteria).
 */
interface GetInterface
{
    /**
     * @param \Praxigento\Accounting\Api\Transaction\Get\Request $data
     * @return \Praxigento\Accounting\Api\Transaction\Get\Response
     */
    public function exec(\Praxigento\Accounting\Api\Transaction\Get\Request $data);
}