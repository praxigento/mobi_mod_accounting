<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Repo\Dao;

use Magento\Framework\App\ObjectManager;
use Praxigento\Accounting\Repo\Data\Account as Entity;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class Account_ManualTest extends \Praxigento\Core\Test\BaseCase\Mockery
{
    const DEF_ACCOUNT_ID = 283;
    const DEF_ASSET_TYPE_ID = 1;
    const DEF_CUSTOMER_ID = 504;
    /** @var  \Praxigento\Accounting\Repo\Dao\Account */
    private $_obj;

    public function setUp()
    {
        parent::setUp();
        $this->_obj = ObjectManager::getInstance()->create(\Praxigento\Accounting\Repo\Dao\Account::class);
    }

    public function test_create()
    {
        $bind = [
            Entity::A_CUST_ID => self::DEF_CUSTOMER_ID,
            Entity::A_ASSET_TYPE_ID => self::DEF_ASSET_TYPE_ID
        ];
        $res = $this->_obj->create($bind);
        $this->assertTrue($res > 0);
    }

    public function test_getByCustomerId()
    {
        $res = $this->_obj->getByCustomerId(self::DEF_CUSTOMER_ID, self::DEF_ASSET_TYPE_ID);
        $this->assertInstanceOf(Entity::class, $res);
    }

    public function test_getIdByCode()
    {
        $data = $this->_obj->getById(147);
        $this->assertTrue($data > 0);
    }

    public function test_updateBalance()
    {
        $DELTA = 2;
        $res = $this->_obj->updateBalance(self::DEF_ACCOUNT_ID, $DELTA);
        $this->assertEquals(1, $res);
    }

}