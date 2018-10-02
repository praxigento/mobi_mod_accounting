<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Accounting\Service\Transaction;

use Praxigento\Accounting\Api\Service\Transaction\Get\Request as ARequest;
use Praxigento\Accounting\Api\Service\Transaction\Get\Response as AResponse;

class Get
    implements \Praxigento\Accounting\Api\Service\Transaction\Get
{
    public function __construct()
    {

    }

    public function exec($request)
    {
        assert($request instanceof ARequest);
        $result = new AResponse();

        return $result;
    }

}
