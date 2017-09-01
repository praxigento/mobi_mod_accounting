<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Setup;

use Praxigento\Accounting\Repo\Entity\Data\Account as Account;
use Praxigento\Accounting\Repo\Entity\Data\Balance as Balance;
use Praxigento\Accounting\Repo\Entity\Data\Log\Change\Admin as LogChangeAdmin;
use Praxigento\Accounting\Repo\Entity\Data\Log\Change\Customer as LogChangeCustomer;
use Praxigento\Accounting\Repo\Entity\Data\Operation as Operation;
use Praxigento\Accounting\Repo\Entity\Data\Transaction as Transaction;
use Praxigento\Accounting\Repo\Entity\Data\Type\Asset as TypeAsset;
use Praxigento\Accounting\Repo\Entity\Data\Type\Operation as TypeOperation;
use Praxigento\Core\Data as DataObject;

include_once(__DIR__ . '/../phpunit_bootstrap.php');

class InstallSchema_UnitTest
    extends \Praxigento\Core\Test\BaseCase\Setup\Schema
{

    /** @var  InstallSchema */
    private $obj;

    public function setUp()
    {
        parent::setUp();
        /** create object to test */
        $this->obj = new InstallSchema(
            $this->mResource,
            $this->mToolDem
        );
    }

    public function test_install()
    {
        /** === Test Data === */
        /** === Setup Mocks === */
        // $setup->startSetup();
        $this->mSetup
            ->shouldReceive('startSetup')->once();
        // $demPackage = $this->_toolDem->readDemPackage($pathToFile, $pathToNode);
        $mDemPackage = $this->_mock(DataObject::class);
        $this->mToolDem
            ->shouldReceive('readDemPackage')->once()
            ->withArgs([\Mockery::any(), '/dBEAR/package/Praxigento/package/Accounting'])
            ->andReturn($mDemPackage);
        // $demEntity = $demPackage->get('package/Type/entity/Asset');
        $mDemPackage->shouldReceive('getData');
        //
        // $this->_toolDem->createEntity($entityAlias, $demEntity);
        //
        $this->mToolDem->shouldReceive('createEntity')->withArgs([TypeAsset::ENTITY_NAME, \Mockery::any()]);
        $this->mToolDem->shouldReceive('createEntity')->withArgs([TypeOperation::ENTITY_NAME, \Mockery::any()]);
        $this->mToolDem->shouldReceive('createEntity')->withArgs([Account::ENTITY_NAME, \Mockery::any()]);
        $this->mToolDem->shouldReceive('createEntity')->withArgs([Operation::ENTITY_NAME, \Mockery::any()]);
        $this->mToolDem->shouldReceive('createEntity')->withArgs([Transaction::ENTITY_NAME, \Mockery::any()]);
        $this->mToolDem->shouldReceive('createEntity')->withArgs([Balance::ENTITY_NAME, \Mockery::any()]);
        $this->mToolDem->shouldReceive('createEntity')->withArgs([LogChangeAdmin::ENTITY_NAME, \Mockery::any()]);
        $this->mToolDem->shouldReceive('createEntity')->withArgs([LogChangeCustomer::ENTITY_NAME, \Mockery::any()]);
        // $setup->endSetup();
        $this->mSetup
            ->shouldReceive('endSetup')->once();
        /** === Call and asserts  === */
        $this->obj->install($this->mSetup, $this->mContext);
    }
}