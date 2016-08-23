<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Service\Account;

use Praxigento\Accounting\Data\Entity\Account as Account;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class Call_UnitTest extends \Praxigento\Core\Test\BaseCase\Mockery
{
    /** @var  \Mockery\MockInterface */
    private $mLogger;
    /** @var  \Mockery\MockInterface */
    private $mRepoAccount;
    /** @var  \Mockery\MockInterface */
    private $mRepoMod;
    /** @var  \Mockery\MockInterface */
    private $mRepoTypeAsset;
    /** @var  Call */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mLogger = $this->_mockLogger();
        $this->mRepoAccount = $this->_mock(\Praxigento\Accounting\Repo\Entity\IAccount::class);
        $this->mRepoTypeAsset = $this->_mock(\Praxigento\Accounting\Repo\Entity\Type\IAsset::class);
        $this->mRepoMod = $this->_mock(\Praxigento\Accounting\Repo\IModule::class);
        /** setup mocks for constructor */
        /** create object to test */
        $this->obj = new Call(
            $this->mLogger,
            $this->mRepoAccount,
            $this->mRepoTypeAsset,
            $this->mRepoMod
        );
    }

    public function test_cacheReset()
    {
        /** === Setup Mocks === */
        // $this->_repoMod->cacheReset();
        $this->mRepoMod
            ->shouldReceive('cacheReset')->once();
        /** === Call and asserts  === */
        $this->obj->cacheReset();
    }

    public function test_getRepresentative_byAssetCode_accountCreated()
    {
        /** === Test Data === */
        $CUST_ID = 32;
        $ASSET_TYPE_CODE = 'code';
        $ACCOUNT_ID = 39;
        $ASSET_TYPE_ID = 98;
        $BALANCE = 54.65;
        $DATA = new Account([
            Account::ATTR_ASSET_TYPE_ID => $ASSET_TYPE_ID,
            Account::ATTR_BALANCE => $BALANCE,
            Account::ATTR_CUST_ID => $CUST_ID,
            Account::ATTR_ID => $ACCOUNT_ID
        ]);
        /** === Setup Mocks === */
        // $typeId = $this->_repoTypeAsset->getIdByCode($typeCode);
        $this->mRepoTypeAsset
            ->shouldReceive('getIdByCode')->once()
            ->andReturn($ASSET_TYPE_ID);
        // $customerId = $this->_repoMod->getRepresentativeCustomerId();
        $this->mRepoMod
            ->shouldReceive('getRepresentativeCustomerId')->once()
            ->andReturn($CUST_ID);
        // accounts = $this->_repoAccount->getByCustomerId($customerId);
        $this->mRepoAccount
            ->shouldReceive('getByCustomerId')->once()
            ->andReturn([]);
        // $resp = $this->get($req);
        // $data = $this->_repoAccount->getByCustomerId($customerId, $assetTypeId);
        $this->mRepoAccount
            ->shouldReceive('getByCustomerId')->once()
            ->andReturn($DATA);
        /** === Call and asserts  === */
        $req = new Request\GetRepresentative();
        $req->setAssetTypeCode($ASSET_TYPE_CODE);
        $resp = $this->obj->getRepresentative($req);
        $this->assertTrue($resp->isSucceed());
        $this->assertEquals($ASSET_TYPE_ID, $resp->getAssetTypeId());
        $this->assertEquals($BALANCE, $resp->getBalance());
        $this->assertEquals($CUST_ID, $resp->getCustomerId());
        $this->assertEquals($ACCOUNT_ID, $resp->getId());
    }

    public function test_getRepresentative_byAssetCode_idNotFound()
    {
        /** === Test Data === */
        $ASSET_TYPE_CODE = 'code';
        /** === Setup Mocks === */
        // $typeId = $this->_repoTypeAsset->getIdByCode($typeCode);
        $this->mRepoTypeAsset
            ->shouldReceive('getIdByCode')->once()
            ->andReturn(null);
        /** === Call and asserts  === */
        $req = new Request\GetRepresentative();
        $req->setAssetTypeCode($ASSET_TYPE_CODE);
        $resp = $this->obj->getRepresentative($req);
        $this->assertFalse($resp->isSucceed());
    }

    public function test_getRepresentative_byAssetId_accountsFound()
    {
        /** === Test Data === */
        $CUST_ID = 32;
        $ACCOUNT_ID_1 = 34;
        $ACCOUNT_ID_2 = 43;
        $ASSET_TYPE_ID_1 = 45;
        $ASSET_TYPE_ID_2 = 65;
        $DATA = [
            new Account([
                Account::ATTR_ID => $ACCOUNT_ID_1,
                Account::ATTR_ASSET_TYPE_ID => $ASSET_TYPE_ID_1
            ]),
            new Account([
                Account::ATTR_ID => $ACCOUNT_ID_2,
                Account::ATTR_ASSET_TYPE_ID => $ASSET_TYPE_ID_2
            ])
        ];
        /** === Setup Mocks === */
        // $customerId = $this->_repoMod->getRepresentativeCustomerId();
        $this->mRepoMod
            ->shouldReceive('getRepresentativeCustomerId')->once()
            ->andReturn($CUST_ID);
        // accounts = $this->_repoAccount->getByCustomerId($customerId);
        $this->mRepoAccount
            ->shouldReceive('getByCustomerId')->once()
            ->andReturn($DATA);
        /** === Call and asserts  === */
        $req = new Request\GetRepresentative();
        $req->setAssetTypeId($ASSET_TYPE_ID_1);
        $resp = $this->obj->getRepresentative($req);
        $this->assertTrue($resp->isSucceed());
        $this->assertEquals($ACCOUNT_ID_1, $resp->getData(Account::ATTR_ID));
        /* secondary request to use cache */
        $resp = $this->obj->getRepresentative($req);
        $this->assertTrue($resp->isSucceed());
        $this->assertEquals($ACCOUNT_ID_1, $resp->getData(Account::ATTR_ID));
    }

    public function test_get_byAccountId()
    {
        /** === Test Data === */
        $ACCOUNT_ID = '34';
        $DATA = [Account::ATTR_ID => $ACCOUNT_ID];
        /** === Setup Mocks === */
        // $data = $this->_repoAccount->getById($accountId);
        $this->mRepoAccount
            ->shouldReceive('getById')->once()
            ->andReturn(new Account($DATA));
        /** === Call and asserts  === */
        $req = new Request\Get();
        $req->setAccountId($ACCOUNT_ID);
        $resp = $this->obj->get($req);
        $this->assertTrue($resp->isSucceed());
    }

    public function test_get_byAssetCode()
    {
        /** === Test Data === */
        $ASSET_TYPE_ID = '12';
        $ASSET_TYPE_CODE = 'ASSET';
        $CUST_ID = '21';
        $ACCOUNT_ID = '34';
        $DATA = [Account::ATTR_ID => $ACCOUNT_ID];
        /** === Setup Mocks === */
        // $assetTypeId = $this->_repoTypeAsset->getIdByCode($assetTypeCode);
        $this->mRepoTypeAsset
            ->shouldReceive('getIdByCode')->once()
            ->andReturn($ASSET_TYPE_ID);
        // $data = $this->_repoAccount->getByCustomerId($customerId, $assetTypeId);
        $this->mRepoAccount
            ->shouldReceive('getByCustomerId')->once()
            ->andReturn(new Account($DATA));
        /** === Call and asserts  === */
        $req = new Request\Get();
        $req->setCustomerId($CUST_ID);
        $req->setAssetTypeCode($ASSET_TYPE_CODE);
        $resp = $this->obj->get($req);
        $this->assertTrue($resp->isSucceed());


    }

    public function test_get_byAssetId()
    {
        /** === Test Data === */
        $ASSET_TYPE_ID = '12';
        $CUST_ID = '21';
        $ACCOUNT_ID = '34';
        $DATA = [Account::ATTR_ID => $ACCOUNT_ID];
        /** === Setup Mocks === */
        // $data = $this->_repoAccount->getByCustomerId($customerId, $assetTypeId);
        $this->mRepoAccount
            ->shouldReceive('getByCustomerId')->once()
            ->andReturn(new Account($DATA));
        /** === Call and asserts  === */
        $req = new Request\Get();
        $req->setCustomerId($CUST_ID);
        $req->setAssetTypeId($ASSET_TYPE_ID);
        $resp = $this->obj->get($req);
        $this->assertTrue($resp->isSucceed());
    }

    public function test_get_createNewAccountIfMissed()
    {
        /** === Test Data === */
        $ASSET_TYPE_ID = '12';
        $CUST_ID = '21';
        $ACCOUNT_ID = '34';
        $DATA = [Account::ATTR_ID => $ACCOUNT_ID];
        /** === Setup Mocks === */
        // $data = $this->_repoAccount->getByCustomerId($customerId, $assetTypeId);
        $this->mRepoAccount
            ->shouldReceive('getByCustomerId')->once()
            ->andReturn([]);
        // $accId = $this->_repoAccount->create($data);
        $this->mRepoAccount
            ->shouldReceive('create')->once()
            ->andReturn($ACCOUNT_ID);
        /** === Call and asserts  === */
        $req = new Request\Get();
        $req->setCustomerId($CUST_ID);
        $req->setAssetTypeId($ASSET_TYPE_ID);
        $req->setCreateNewAccountIfMissed(true);
        $resp = $this->obj->get($req);
        $this->assertTrue($resp->isSucceed());

    }

}