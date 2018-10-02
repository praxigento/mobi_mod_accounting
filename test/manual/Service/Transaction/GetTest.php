<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Test\Praxigento\Accounting\Service\Transaction;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

use Praxigento\Accounting\Api\Service\Transaction\Get as AService;
use Praxigento\Accounting\Api\Service\Transaction\Get\Request as ARequest;
use Praxigento\Accounting\Api\Service\Transaction\Get\Response as AResponse;

class GetTest
    extends \Praxigento\Core\Test\BaseCase\Manual
{

    public function test_exec()
    {
        /** @var AService $obj */
        $obj = $this->manObj->create(AService::class);
        $req = new ARequest();
        $res = $obj->exec($req);
        $this->assertInstanceOf(AResponse::class, $res);
    }

}