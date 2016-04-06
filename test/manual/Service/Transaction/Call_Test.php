<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Lib\Service\Transaction;

use Praxigento\Accounting\Lib\Context;
use Praxigento\Accounting\Lib\Service\Transaction\Request\Add as AddTransactionRequest;
use Praxigento\Accounting\Lib\Service\Transaction\Response\Add as AddTransactionResponse;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class Call_ManualTest extends \Praxigento\Core\Lib\Test\BaseTestCase {

    public function test_addTransaction() {
        $obm = Context::instance()->getObjectManager();
        /** @var  $call \Praxigento\Accounting\Lib\Service\Transaction\Call */
        $call = $obm->get('Praxigento\Accounting\Lib\Service\Transaction\Call');
        $request = new AddTransactionRequest();
        $request->operationId = 1;
        $request->value = 0.42;
        $request->debitAccId = 1;
        $request->creditAccId = 2;
        $request->dateApplied = '2015-11-10 14:29:44';
        /** @var  $response AddTransactionResponse */
        $response = $call->add($request);
        $this->assertTrue($response->isSucceed());
    }
}