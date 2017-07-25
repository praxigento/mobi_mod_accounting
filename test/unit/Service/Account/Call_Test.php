<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Service\Account;

use Praxigento\Accounting\Data\Entity\Account as Account;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

/**
 * @SuppressWarnings(PHPMD.CamelCaseClassName)
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class Call_UnitTest
    extends \Praxigento\Core\Test\BaseCase\Service\Call
{
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
        $this->mRepoAccount = $this->_mock(\Praxigento\Accounting\Repo\Entity\Def\Account::class);
        $this->mRepoTypeAsset = $this->_mock(\Praxigento\Accounting\Repo\Entity\Type\Def\Asset::class);
        $this->mRepoMod = $this->_mock(\Praxigento\Accounting\Repo\IModule::class);
        /** setup mocks for constructor */
        /** create object to test */
        $this->obj = new Call(
            $this->mLogger,
            $this->mManObj,
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
        $custId = 32;
        $assetTypeCode = 'code';
        $accId = 39;
        $assetTypeId = 98;
        $balance = 54.65;
        $data = new Account([
            Account::ATTR_ASSET_TYPE_ID => $assetTypeId,
            Account::ATTR_BALANCE => $balance,
            Account::ATTR_CUST_ID => $custId,
            Account::ATTR_ID => $accId
        ]);
        /** === Setup Mocks === */
        // $typeId = $this->_repoTypeAsset->getIdByCode($typeCode);
        $this->mRepoTypeAsset
            ->shouldReceive('getIdByCode')->once()
            ->andReturn($assetTypeId);
        // $customerId = $this->_repoMod->getRepresentativeCustomerId();
        $this->mRepoMod
            ->shouldReceive('getRepresentativeCustomerId')->once()
            ->andReturn($custId);
        // accounts = $this->_repoAccount->getAllByCustomerId($customerId);
        $this->mRepoAccount
            ->shouldReceive('getAllByCustomerId')->once()
            ->andReturn(null);
        // $resp = $this->get($req);
        // $data = $this->_repoAccount->getByCustomerId($customerId, $assetTypeId);
        $this->mRepoAccount
            ->shouldReceive('getByCustomerId')->once()
            ->andReturn($data);
        /** === Call and asserts  === */
        $req = new Request\GetRepresentative();
        $req->setAssetTypeCode($assetTypeCode);
        $resp = $this->obj->getRepresentative($req);
        $this->assertTrue($resp->isSucceed());
        $this->assertEquals($assetTypeId, $resp->getAssetTypeId());
        $this->assertEquals($balance, $resp->getBalance());
        $this->assertEquals($custId, $resp->getCustomerId());
        $this->assertEquals($accId, $resp->getId());
    }

    public function test_getRepresentative_byAssetCode_idNotFound()
    {
        /** === Test Data === */
        $assetTypeCode = 'code';
        /** === Setup Mocks === */
        // $typeId = $this->_repoTypeAsset->getIdByCode($typeCode);
        $this->mRepoTypeAsset
            ->shouldReceive('getIdByCode')->once()
            ->andReturn(null);
        /** === Call and asserts  === */
        $req = new Request\GetRepresentative();
        $req->setAssetTypeCode($assetTypeCode);
        $resp = $this->obj->getRepresentative($req);
        $this->assertFalse($resp->isSucceed());
    }

    public function test_getRepresentative_byAssetId_accountsFound()
    {
        /** === Test Data === */
        $custId = 32;
        $accId1 = 34;
        $accId2 = 43;
        $assetTypeId1 = 45;
        $assetTypeId2 = 65;
        $data = [
            new Account([
                Account::ATTR_ID => $accId1,
                Account::ATTR_ASSET_TYPE_ID => $assetTypeId1
            ]),
            new Account([
                Account::ATTR_ID => $accId2,
                Account::ATTR_ASSET_TYPE_ID => $assetTypeId2
            ])
        ];
        /** === Setup Mocks === */
        // $customerId = $this->_repoMod->getRepresentativeCustomerId();
        $this->mRepoMod
            ->shouldReceive('getRepresentativeCustomerId')->once()
            ->andReturn($custId);
        // accounts = $this->_repoAccount->getAllByCustomerId($customerId);
        $this->mRepoAccount
            ->shouldReceive('getAllByCustomerId')->once()
            ->andReturn($data);
        /** === Call and asserts  === */
        $req = new Request\GetRepresentative();
        $req->setAssetTypeId($assetTypeId1);
        $resp = $this->obj->getRepresentative($req);
        $this->assertTrue($resp->isSucceed());
        $this->assertEquals($accId1, $resp->get(Account::ATTR_ID));
        /* secondary request to use cache */
        $resp = $this->obj->getRepresentative($req);
        $this->assertTrue($resp->isSucceed());
        $this->assertEquals($accId1, $resp->get(Account::ATTR_ID));
    }

    public function test_get_byAccountId()
    {
        /** === Test Data === */
        $accId = '34';
        $data = [Account::ATTR_ID => $accId];
        /** === Setup Mocks === */
        // $data = $this->_repoAccount->getById($accountId);
        $this->mRepoAccount
            ->shouldReceive('getById')->once()
            ->andReturn(new Account($data));
        /** === Call and asserts  === */
        $req = new Request\Get();
        $req->setAccountId($accId);
        $resp = $this->obj->get($req);
        $this->assertTrue($resp->isSucceed());
    }

    public function test_get_byAssetCode()
    {
        /** === Test Data === */
        $assetTypeId = '12';
        $assetTypeCode = 'ASSET';
        $custId = '21';
        $accountId = '34';
        $data = [Account::ATTR_ID => $accountId];
        /** === Setup Mocks === */
        // $assetTypeId = $this->_repoTypeAsset->getIdByCode($assetTypeCode);
        $this->mRepoTypeAsset
            ->shouldReceive('getIdByCode')->once()
            ->andReturn($assetTypeId);
        // $data = $this->_repoAccount->getByCustomerId($customerId, $assetTypeId);
        $this->mRepoAccount
            ->shouldReceive('getByCustomerId')->once()
            ->andReturn(new Account($data));
        /** === Call and asserts  === */
        $req = new Request\Get();
        $req->setCustomerId($custId);
        $req->setAssetTypeCode($assetTypeCode);
        $resp = $this->obj->get($req);
        $this->assertTrue($resp->isSucceed());


    }

    public function test_get_byAssetId()
    {
        /** === Test Data === */
        $assetTypeId = '12';
        $custId = '21';
        $accId = '34';
        $data = [Account::ATTR_ID => $accId];
        /** === Setup Mocks === */
        // $data = $this->_repoAccount->getByCustomerId($customerId, $assetTypeId);
        $this->mRepoAccount
            ->shouldReceive('getByCustomerId')->once()
            ->andReturn(new Account($data));
        /** === Call and asserts  === */
        $req = new Request\Get();
        $req->setCustomerId($custId);
        $req->setAssetTypeId($assetTypeId);
        $resp = $this->obj->get($req);
        $this->assertTrue($resp->isSucceed());
    }

    public function test_get_createNewAccountIfMissed()
    {
        /** === Test Data === */
        $assetTypeId = '12';
        $custId = '21';
        $accId = '34';
        /** === Setup Mocks === */
        // $data = $this->_repoAccount->getByCustomerId($customerId, $assetTypeId);
        $this->mRepoAccount
            ->shouldReceive('getByCustomerId')->once()
            ->andReturn([]);
        // $accId = $this->_repoAccount->create($data);
        $this->mRepoAccount
            ->shouldReceive('create')->once()
            ->andReturn($accId);
        /** === Call and asserts  === */
        $req = new Request\Get();
        $req->setCustomerId($custId);
        $req->setAssetTypeId($assetTypeId);
        $req->setCreateNewAccountIfMissed(true);
        $resp = $this->obj->get($req);
        $this->assertTrue($resp->isSucceed());
    }

}