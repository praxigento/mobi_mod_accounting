<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Service\Operation;

use Praxigento\Accounting\Lib\Context;
use Praxigento\Accounting\Repo\Entity\Data\Transaction;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class Call_ManualTest extends \Praxigento\Core\Test\BaseCase\Mockery {

    public function test_add() {
        $obm = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var  $call \Praxigento\Accounting\Service\Operation\Call */
        $call = $obm->get('Praxigento\Accounting\Service\Operation\Call');
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