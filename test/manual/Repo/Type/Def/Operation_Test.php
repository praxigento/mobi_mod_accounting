<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Repo\Type\Def;

use Magento\Framework\App\ObjectManager;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class Operation_ManualTest extends \Praxigento\Core\Lib\Test\BaseMockeryCase
{
    /** @var  \Praxigento\Accounting\Repo\Entity\Type\Def\Operation */
    private $_obj;

    public function setUp()
    {
        parent::setUp();
        $this->_obj = ObjectManager::getInstance()->create(\Praxigento\Accounting\Repo\Entity\Type\Def\Operation::class);
    }

    public function test_getIdByCode()
    {
        $data = $this->_obj->getIdByCode('PV_SALE_PAID');
        $this->assertTrue($data > 0);
    }

}