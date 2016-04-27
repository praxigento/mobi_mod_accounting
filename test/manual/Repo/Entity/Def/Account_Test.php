<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Repo\Entity\Def;

use Magento\Framework\App\ObjectManager;
use Praxigento\Accounting\Data\Entity\Account as Entity;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class Account_ManualTest extends \Praxigento\Core\Test\BaseMockeryCase
{
    const DEF_ACCOUNT_ID = 283;
    const DEF_ASSET_TYPE_ID = 1;
    const DEF_CUSTOMER_ID = 504;
    /** @var  \Praxigento\Accounting\Repo\Entity\Def\Account */
    private $_obj;

    public function setUp()
    {
        parent::setUp();
        $this->_obj = ObjectManager::getInstance()->create(\Praxigento\Accounting\Repo\Entity\IAccount::class);
    }

    public function test_create()
    {
        $bind = [
            Entity::ATTR_CUST_ID => static::DEF_CUSTOMER_ID,
            Entity::ATTR_ASSET_TYPE_ID => static::DEF_ASSET_TYPE_ID
        ];
        $res = $this->_obj->create($bind);
        $this->assertTrue($res > 0);
    }

    public function test_getByCustomerId()
    {
        $res = $this->_obj->getByCustomerId(static::DEF_CUSTOMER_ID, static::DEF_ASSET_TYPE_ID);
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
        $res = $this->_obj->updateBalance(static::DEF_ACCOUNT_ID, $DELTA);
        $this->assertEquals(1, $res);
    }

}