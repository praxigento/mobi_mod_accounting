<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Api\Transaction;

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
        $this->obj = ObjectManager::getInstance()->create(\Praxigento\Accounting\Api\Transaction\GetInterface::class);
    }

    public function test_exec()
    {
        $req = new \Praxigento\Accounting\Api\Transaction\Get\Request();
        $data = $this->obj->exec($req);
        $this->assertNotNull($data);
    }


}