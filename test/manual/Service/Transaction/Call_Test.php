<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Service\Transaction;

use Praxigento\Accounting\Lib\Context;
use Praxigento\Accounting\Service\Transaction\Request\Add as AddTransactionRequest;
use Praxigento\Accounting\Service\Transaction\Response\Add as AddTransactionResponse;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class Call_ManualTest extends \Praxigento\Core\Test\BaseMockeryCase {

    public function test_addTransaction() {
        $obm = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var  $call \Praxigento\Accounting\Service\Transaction\Call */
        $call = $obm->get('Praxigento\Accounting\Service\Transaction\Call');
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