<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Lib\Service\Account;

use Praxigento\Accounting\Lib\Service\Account\Request\GetRepresentative as GetRepresentativeRequest;


include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class Call_ManualTest extends \Praxigento\Core\Lib\Test\BaseTestCase
{

    public function test_get()
    {
        $obm = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var  $call \Praxigento\Accounting\Lib\Service\Account\Call */
        $call = $obm->get('Praxigento\Accounting\Lib\Service\Account\Call');
        $request = new Request\Get();
        $request->customerId = 1;
        $request->assetTypeCode = 'asset';
        $request->createNewAccountIfMissed = true;
        $response = $call->get($request);
        $this->assertTrue($response->isSucceed());
    }

    public function test_getRepresentative()
    {
        $obm = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var  $call \Praxigento\Accounting\Lib\Service\Account\Call */
        $call = $obm->get('Praxigento\Accounting\Lib\Service\Account\Call');
        $request = new Request\GetRepresentative();
        $request->setData(GetRepresentativeRequest::ASSET_TYPE_ID, 1);
        $response = $call->getRepresentative($request);
        $this->assertTrue($response->isSucceed());
    }

    public function test_updateBalance()
    {
        $obm = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var  $call \Praxigento\Accounting\Lib\Service\Account\Call */
        $call = $obm->get('Praxigento\Accounting\Lib\Service\Account\Call');
        $request = new Request\UpdateBalance();
        $request->accountId = 1;
        $request->changeValue = -12.21;
        $response = $call->updateBalance($request);
        $this->assertTrue($response->isSucceed());
    }
}