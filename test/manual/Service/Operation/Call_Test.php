<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Service\Operation;

use Praxigento\Accounting\Lib\Context;
use Praxigento\Accounting\Repo\Data\Transaction;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class Call_ManualTest extends \Praxigento\Core\Test\BaseCase\Mockery {

    public function test_add() {
        $obm = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var  $call \Praxigento\Accounting\Service\Operation */
        $call = $obm->get('\Praxigento\Accounting\Service\Operation');
        $req = new \Praxigento\Accounting\Api\Service\Operation\Request();
        $req->operationTypeId = 1;
        $req->datePerformed = '2015-11-10 18:43:57';
        $req->transactions = [
            [
                Transaction::A_DEBIT_ACC_ID  => 1,
                Transaction::A_CREDIT_ACC_ID => 2,
                Transaction::A_VALUE         => 5
            ], [
                Transaction::A_DEBIT_ACC_ID  => 1,
                Transaction::A_CREDIT_ACC_ID => 2,
                Transaction::A_VALUE         => 10
            ], [
                Transaction::A_DEBIT_ACC_ID  => 1,
                Transaction::A_CREDIT_ACC_ID => 2,
                Transaction::A_VALUE         => 15
            ]
        ];
        /** @var  $resp \Praxigento\Accounting\Api\Service\Operation\Response */
        $resp = $call->exec($req);
        $this->assertTrue($resp->isSucceed());
    }
}