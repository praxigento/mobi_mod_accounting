<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Test\Praxigento\Accounting\Service\Account\Balance;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

use Praxigento\Accounting\Service\Account\Balance\Reset as AService;
use Praxigento\Accounting\Service\Account\Balance\Reset\Request as ARequest;
use Praxigento\Accounting\Service\Account\Balance\Reset\Response as AResponse;

class ResetTest
    extends \Praxigento\Core\Test\BaseCase\Manual
{

    public function test_exec_byAccounts()
    {
        /** @var AService $obj */
        $obj = $this->manObj->create(AService::class);
        $req = new ARequest();
        $req->setDateFrom('20181114');
        $req->setAccounts([1770, 4, 8]);
        $res = $obj->exec($req);
        $this->assertInstanceOf(AResponse::class, $res);
    }

    public function test_exec_byAssets()
    {
        /** @var AService $obj */
        $obj = $this->manObj->create(AService::class);
        $req = new ARequest();
        $req->setDateFrom('20181110');
        $req->setAssetTypes([1, 2]);
        $res = $obj->exec($req);
        $this->assertInstanceOf(AResponse::class, $res);
    }

}