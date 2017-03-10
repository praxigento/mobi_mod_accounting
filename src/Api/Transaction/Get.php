<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Api\Transaction;


class Get
    implements \Praxigento\Accounting\Api\Transaction\GetInterface
{

    public function __construct()
    {

    }

    public function exec(\Praxigento\Accounting\Api\Transaction\Get\Request $data)
    {
        $result = new \Praxigento\Accounting\Api\Transaction\Get\Response();
        return $result;
    }

}