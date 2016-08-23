<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Data\Entity;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');
use Praxigento\Accounting\Data\Entity\Account as DataEntity;

class Account_UnitTest extends \Praxigento\Core\Test\BaseCase\Mockery
{
    /** @var  DataEntity */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        $this->obj = new DataEntity();
    }

    public function test_accessors()
    {
        /** === Test Data === */
        $ASSET_TYPE_ID = 'asset_type_id';
        $BALANCE = 'balance';
        $CUSTOMER_REF = 'customer_id';
        $ID = 'id';
        /** === Call and asserts  === */
        $this->obj->setAssetTypeId($ASSET_TYPE_ID);
        $this->obj->setBalance($BALANCE);
        $this->obj->setCustomerId($CUSTOMER_REF);
        $this->obj->setId($ID);
        $this->assertEquals($ASSET_TYPE_ID, $this->obj->getAssetTypeId());
        $this->assertEquals($BALANCE, $this->obj->getBalance());
        $this->assertEquals($CUSTOMER_REF, $this->obj->getCustomerId());
        $this->assertEquals($ID, $this->obj->getId());
    }

    public function test_pk()
    {
        /** === Call and asserts  === */
        $pk = $this->obj->getPrimaryKeyAttrs();
        $this->assertEquals([DataEntity::ATTR_ID], $pk);
    }
}