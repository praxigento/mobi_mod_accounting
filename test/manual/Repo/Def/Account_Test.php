<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Repo\Def;

use Magento\Framework\App\ObjectManager;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class Account_ManualTest extends \Praxigento\Core\Lib\Test\BaseMockeryCase
{
    /** @var  \Praxigento\Accounting\Repo\Def\Account */
    private $_obj;

    public function setUp()
    {
        parent::setUp();
        $this->_obj = ObjectManager::getInstance()->create(\Praxigento\Accounting\Repo\Def\Account::class);
    }

    public function test_getByCustomerId()
    {
        $data = $this->_obj->getByCustomerId(252, 1);
        $this->assertTrue($data > 0);
    }

    public function test_getIdByCode()
    {
        $data = $this->_obj->getById(147);
        $this->assertTrue($data > 0);
    }

}