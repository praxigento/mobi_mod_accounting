<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Service\Account;

use Praxigento\Accounting\Service\Account\Request\GetRepresentative as GetRepresentativeRequest;


include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class Call_ManualTest extends \Praxigento\Core\Test\BaseMockeryCase
{

    public function test_get()
    {
        $obm = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var  $call \Praxigento\Accounting\Service\Account\Call */
        $call = $obm->get('Praxigento\Accounting\Service\Account\Call');
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
        /** @var  $call \Praxigento\Accounting\Service\Account\Call */
        $call = $obm->get('Praxigento\Accounting\Service\Account\Call');
        $request = new Request\GetRepresentative();
        $request->setData(GetRepresentativeRequest::ASSET_TYPE_ID, 1);
        $response = $call->getRepresentative($request);
        $this->assertTrue($response->isSucceed());
    }

}