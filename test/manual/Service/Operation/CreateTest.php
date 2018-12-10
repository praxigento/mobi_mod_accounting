<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Test\Praxigento\Accounting\Service\Operation;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

use Praxigento\Accounting\Api\Service\Operation\Create as AService;
use Praxigento\Accounting\Api\Service\Operation\Create\Request as ARequest;
use Praxigento\Accounting\Api\Service\Operation\Create\Response as AResponse;
use Praxigento\Accounting\Config as Cfg;

class CreateTest
    extends \Praxigento\Core\Test\BaseCase\Manual
{

    private function getTransactions()
    {
        /* prepare transaction */
        $tran = new \Praxigento\Accounting\Repo\Data\Transaction();
        $tran->setDebitAccId(5430);
        $tran->setCreditAccId(2393);
        $tran->setCreditAccId(5430);
        $tran->setValue(1.4321);
        $tran->setNote('manual test');
        $result = [$tran];
        return $result;
    }

    public function test_exec()
    {
        /** @var AService $obj */
        $obj = $this->manObj->create(AService::class);
        $trans = $this->getTransactions();
        $req = new ARequest();
        $req->setTransactions($trans);
        $req->setOperationTypeCode(Cfg::CODE_TYPE_OPER_CHANGE_BALANCE);
        $res = $obj->exec($req);
        $this->assertInstanceOf(AResponse::class, $res);
    }
}