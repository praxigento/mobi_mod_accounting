<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Repo\Entity\Def;

use Praxigento\Accounting\Data\Entity\Account as Entity;
use Praxigento\Accounting\Repo\Entity\IAccount;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class Account_UnitTest
    extends \Praxigento\Core\Test\BaseCase\Repo\Entity
{
    /** @var  Account */
    private $obj;

    public function setUp()
    {
        parent::setUp();
        $this->obj = new Account(
            $this->mResource,
            $this->mRepoGeneric,
            Entity::class
        );
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(IAccount::class, $this->obj);
    }

    public function test_getByCustomerId_withAssetType()
    {
        /** === Test Data === */
        $CUST_ID = 32;
        $ASSET_TYPE_ID = 45;
        $RESULT = [['some data' => 'here']];
        /** === Setup Mocks === */
        // $result = $this->get($where);
        // $result = $this->_repoGeneric->getEntities($this->_entityName, null, $where, $order, $limit, $offset);
        $this->mRepoGeneric
            ->shouldReceive('getEntities')
            ->andReturn($RESULT);
        /** === Call and asserts  === */
        $res = $this->obj->getByCustomerId($CUST_ID, $ASSET_TYPE_ID);
        $this->assertInstanceOf(\Praxigento\Accounting\Data\Entity\Account::class, $res);
    }

    public function test_getByCustomerId_woAssetType()
    {
        /** === Test Data === */
        $CUST_ID = 32;
        $RESULT = [['some data' => 'here']];
        /** === Setup Mocks === */
        // $result = $this->get($where);
        // $result = $this->_repoGeneric->getEntities($this->_entityName, null, $where, $order, $limit, $offset);
        $this->mRepoGeneric
            ->shouldReceive('getEntities')
            ->andReturn($RESULT);
        /** === Call and asserts  === */
        $res = $this->obj->getByCustomerId($CUST_ID);
        $this->assertTrue(is_array($res));
    }

    public function test_updateBalance_negative()
    {
        /** === Test Data === */
        $ACC_ID = 32;
        $DELTA = -45;
        $ROWS_UPDATED = 1;
        /** === Setup Mocks === */
        // $rowsUpdated = $this->updateById($accountId, $bind);
        // $result = $this->_repoGeneric->updateEntity($this->_entityName, $data, $where);
        $this->mRepoGeneric
            ->shouldReceive('updateEntity')
            ->andReturn($ROWS_UPDATED);
        /** === Call and asserts  === */
        $res = $this->obj->updateBalance($ACC_ID, $DELTA);
        $this->assertEquals($ROWS_UPDATED, $res);
    }

    public function test_updateBalance_positive()
    {
        /** === Test Data === */
        $ACC_ID = 32;
        $DELTA = 45;
        $ROWS_UPDATED = 1;
        /** === Setup Mocks === */
        // $rowsUpdated = $this->updateById($accountId, $bind);
        // $result = $this->_repoGeneric->updateEntity($this->_entityName, $data, $where);
        $this->mRepoGeneric
            ->shouldReceive('updateEntity')
            ->andReturn($ROWS_UPDATED);
        /** === Call and asserts  === */
        $res = $this->obj->updateBalance($ACC_ID, $DELTA);
        $this->assertEquals($ROWS_UPDATED, $res);
    }
}