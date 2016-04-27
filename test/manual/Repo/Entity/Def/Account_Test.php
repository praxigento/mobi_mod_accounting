<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Repo\Entity\Def;

use Magento\Framework\App\ObjectManager;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class Account_ManualTest extends \Praxigento\Core\Test\BaseMockeryCase
{
    /** @var  \Praxigento\Accounting\Repo\Entity\Def\Account */
    private $_obj;

    public function setUp()
    {
        parent::setUp();
        $this->_obj = ObjectManager::getInstance()->create(\Praxigento\Accounting\Repo\Entity\IAccount::class);
    }

    public function test_create()
    {
        $bind = [];
        $res = $this->_obj->create();
        $this->assertTrue(is_int($res));
        $this->assertTrue($res > 0);
    }

    public function test_getByCustomerId()
    {
        $data = $this->_obj->getByCustomerId(1, 1);
        $this->assertTrue($data > 0);
    }

    public function test_getIdByCode()
    {
        $data = $this->_obj->getById(147);
        $this->assertTrue($data > 0);
    }

}