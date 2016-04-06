<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Setup;

use Flancer32\Lib\DataObject;
use Praxigento\Accounting\Data\Entity\Account as Account;
use Praxigento\Accounting\Data\Entity\Balance as Balance;
use Praxigento\Accounting\Data\Entity\Operation as Operation;
use Praxigento\Accounting\Data\Entity\Transaction as Transaction;
use Praxigento\Accounting\Data\Entity\Type\Asset as TypeAsset;
use Praxigento\Accounting\Data\Entity\Type\Operation as TypeOperation;


include_once(__DIR__ . '/../phpunit_bootstrap.php');

class InstallSchema_UnitTest extends \Praxigento\Core\Lib\Test\BaseMockeryCase
{
    /** @var  \Praxigento\Accounting\Setup\InstallSchema */
    private $obj;
    /** @var  \Mockery\MockInterface */
    private $mDem;
    /** @var  \Mockery\MockInterface */
    private $mSetup;
    /** @var  \Mockery\MockInterface */
    private $mContext;

    public function setUp()
    {
        parent::setUp();
        $this->mSetup = $this->_mock(\Magento\Framework\Setup\SchemaSetupInterface::class);
        $this->mContext = $this->_mock(\Magento\Framework\Setup\ModuleContextInterface::class);
        $this->mDem = $this->_mock(\Praxigento\Core\Setup\Dem\Tool::class);
        $this->obj = new \Praxigento\Accounting\Setup\InstallSchema($this->mDem);
    }

    public function test_install()
    {
        /* === Test Data === */
        /* === Setup Mocks === */
        // $setup->startSetup();
        $this->mSetup
            ->shouldReceive('startSetup')->once();
        // $demPackage = $this->_toolDem->readDemPackage($pathToFile, $pathToNode);
        $mDemPackage = $this->_mock(DataObject::class);
        $this->mDem
            ->shouldReceive('readDemPackage')->once()
            ->withArgs([anything(), '/dBEAR/package/Praxigento/package/Accounting'])
            ->andReturn($mDemPackage);
        // $demEntity = $demPackage->getData('package/Type/entity/Asset');
        $mDemPackage->shouldReceive('getData');
        //
        // $this->_toolDem->createEntity($entityAlias, $demEntity);
        //
        $this->mDem->shouldReceive('createEntity')->withArgs([TypeAsset::ENTITY_NAME, anything()]);
        $this->mDem->shouldReceive('createEntity')->withArgs([TypeOperation::ENTITY_NAME, anything()]);
        $this->mDem->shouldReceive('createEntity')->withArgs([Account::ENTITY_NAME, anything()]);
        $this->mDem->shouldReceive('createEntity')->withArgs([Operation::ENTITY_NAME, anything()]);
        $this->mDem->shouldReceive('createEntity')->withArgs([Transaction::ENTITY_NAME, anything()]);
        $this->mDem->shouldReceive('createEntity')->withArgs([Balance::ENTITY_NAME, anything()]);
        // $setup->endSetup();
        $this->mSetup
            ->shouldReceive('endSetup')->once();
        /* === Call and asserts  === */
        $this->obj->install($this->mSetup, $this->mContext);
    }
}