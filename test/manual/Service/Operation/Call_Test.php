<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Lib\Service\Operation;

use Praxigento\Accounting\Lib\Context;
use Praxigento\Accounting\Lib\Entity\Transaction;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class Call_ManualTest extends \Praxigento\Core\Lib\Test\BaseTestCase {

    public function test_add() {
        $obm = Context::instance()->getObjectManager();
        /** @var  $call \Praxigento\Accounting\Lib\Service\Operation\Call */
        $call = $obm->get('Praxigento\Accounting\Lib\Service\Operation\Call');
        $req = new Request\Add();
        $req->operationTypeId = 1;
        $req->datePerformed = '2015-11-10 18:43:57';
        $req->transactions = [
            [
                Transaction::ATTR_DEBIT_ACC_ID  => 1,
                Transaction::ATTR_CREDIT_ACC_ID => 2,
                Transaction::ATTR_VALUE         => 5
            ], [
                Transaction::ATTR_DEBIT_ACC_ID  => 1,
                Transaction::ATTR_CREDIT_ACC_ID => 2,
                Transaction::ATTR_VALUE         => 10
            ], [
                Transaction::ATTR_DEBIT_ACC_ID  => 1,
                Transaction::ATTR_CREDIT_ACC_ID => 2,
                Transaction::ATTR_VALUE         => 15
            ]
        ];
        /** @var  $resp Response\Add */
        $resp = $call->add($req);
        $this->assertTrue($resp->isSucceed());
    }
}