<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Api\Rest\Transaction;

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
        $this->obj = ObjectManager::getInstance()->create(\Praxigento\Accounting\Api\Rest\Transaction\GetInterface::class);
    }

    public function test_exec()
    {
        $req = new \Praxigento\Accounting\Api\Rest\Transaction\Get\Request();
        $data = $this->obj->exec($req);
        $this->assertNotNull($data);
    }


}