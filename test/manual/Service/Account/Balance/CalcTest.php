<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Test\Praxigento\Accounting\Service\Account\Balance;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

use Praxigento\Accounting\Service\Account\Balance\Calc as AService;
use Praxigento\Accounting\Service\Account\Balance\Calc\Request as ARequest;
use Praxigento\Accounting\Service\Account\Balance\Calc\Response as AResponse;

class CalcTest
    extends \Praxigento\Core\Test\BaseCase\Manual
{

    public function test_exec()
    {
        /** @var AService $obj */
        $obj = $this->manObj->create(AService::class);
        $req = new ARequest();
        $req->setDaysToReset(31);
        $res = $obj->exec($req);
        $this->assertInstanceOf(AResponse::class, $res);
    }

}