<?php
/**
 * from \Praxigento\Accounting\Api\Rest\Transaction\Get_ManualTest
 */

namespace Praxigento\Accounting\Service\Account;

use Magento\Framework\App\ObjectManager;


include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class Get_ManualTest
    extends \Praxigento\Core\Test\BaseCase\Manual
{
    /** @var  Get */
    private $obj;

    public function setUp()
    {
        parent::setUp();
        $this->obj = ObjectManager::getInstance()->create(\Praxigento\Accounting\Service\Account\Get::class);
    }

    public function test_exec()
    {
        $def = $this->manTrans->begin();
        $req = new \Praxigento\Accounting\Api\Service\Account\Get\Request();
        $req->setAccountId('345');
        $data = $this->obj->exec($req);
        $this->assertNotNull($data);
        $this->manTrans->rollback($def);
    }


}