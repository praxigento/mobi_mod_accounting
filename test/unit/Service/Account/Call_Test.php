<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Service\Account;

use Praxigento\Accounting\Data\Entity\Account as Account;
use Praxigento\Accounting\Data\Entity\Type\Asset as TypeAsset;
use Praxigento\Accounting\Service\Type\Asset\Response\GetByCode as TypeAssetResponseGetByCode;
use Praxigento\Core\Lib\Service\Repo\Response\GetEntities as GetEntitiesResponse;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class Call_UnitTest extends \Praxigento\Core\Test\BaseMockeryCase
{
    /**
     * Prepare mocks and object to test.
     *
     * @return array
     */
    private function _prepareMocks()
    {
        $mLogger = $this->_mockLogger();
        $mConn = $this->_mockConnection();
        $mDba = $this->_mockDbAdapter(null, $mConn);
        $mToolbox = $this->_mockToolbox();
        $mCallRepo = $this->_mockCallRepo();
        $mCallTypeAsset = $this->_mockFor('Praxigento\Accounting\Service\ITypeAsset');
        $mRepoMod = $this->_mockFor('\Praxigento\Accounting\Repo\IModule');
        $call = new Call($mLogger, $mDba, $mToolbox, $mCallRepo, $mCallTypeAsset, $mRepoMod);
        $result = [
            'mLogger' => $mLogger,
            'mDba' => $mDba,
            'mConn' => $mConn,
            'mToolbox' => $mToolbox,
            'mCallRepo' => $mCallRepo,
            'mCallTypeAsset' => $mCallTypeAsset,
            'mRepoMod' => $mRepoMod,
            'call' => $call
        ];
        return $result;
    }

    protected function setUp()
    {
        parent::setUp();
        $this->markTestSkipped('Test is deprecated after M1 & M2 merge is done.');
    }

    public function test_cacheReset()
    {
        /** === Test Data === */

        /** === Extract mocks === */
        /**
         * @var $mLogger \PHPUnit_Framework_MockObject_MockObject for \Psr\Log\LoggerInterface
         * @var $mDba \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Core\Lib\Context\IDbAdapter
         * @var $mConn \PHPUnit_Framework_MockObject_MockObject for \Zend_Db_Adapter_Pdo_Abstract
         * @var $mToolbox \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Core\Lib\IToolbox
         * @var $mCallRepo \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Core\Lib\Service\IRepo
         * @var $mCallTypeAsset \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Accounting\Service\ITypeAsset
         * @var $mRepoMod \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Accounting\Repo\IModule
         * @var $call Call
         */
        extract($this->_prepareMocks());

        /** === Setup Mocks === */

        /** === Call and asserts  === */
        $call->cacheReset();
    }

    public function test_getRepresentative_byAssetCode_accountCreated()
    {
        /** === Test Data === */
        $CUST_ID = '21';
        $ASSET_TYPE_ID = '12';
        $ASSET_CODE = 'TEST_ASSET';

        /** === Extract mocks === */
        /**
         * @var $mLogger \PHPUnit_Framework_MockObject_MockObject for \Psr\Log\LoggerInterface
         * @var $mDba \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Core\Lib\Context\IDbAdapter
         * @var $mConn \PHPUnit_Framework_MockObject_MockObject for \Zend_Db_Adapter_Pdo_Abstract
         * @var $mToolbox \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Core\Lib\IToolbox
         * @var $mCallRepo \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Core\Lib\Service\IRepo
         * @var $mCallTypeAsset \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Accounting\Service\ITypeAsset
         * @var $mRepoMod \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Accounting\Repo\IModule
         * @var $call Call
         */
        extract($this->_prepareMocks());

        // $respCode = $this->_callTypeAsset->getByCode($reqCode);
        $mRespCode = new \Praxigento\Accounting\Service\Type\Asset\Response\GetByCode();
        $mRespCode->markSucceed();
        $mRespCode->setData([TypeAsset::ATTR_ID => $ASSET_TYPE_ID]);
        $mCallTypeAsset
            ->expects($this->once())
            ->method('getByCode')
            ->willReturn($mRespCode);
        // $customerId = $this->_subDb->getRepresentativeCustomerId();
        $mRepoMod
            ->expects($this->once())
            ->method('getRepresentativeCustomerId')
            ->willReturn($CUST_ID);
        // $resp = $this->_callRepo->getEntities($req);
        $mResp = new GetEntitiesResponse();
        $mCallRepo
            ->expects($this->once())
            ->method('getEntities')
            ->willReturn($mResp);

        /** === Call and asserts  === */
        /** mock some methods in the service  */
        $call = $this
            ->getMockBuilder('Praxigento\Accounting\Service\Account\Call')
            ->setMethods(['get'])
            ->setConstructorArgs([$mLogger, $mDba, $mToolbox, $mCallRepo, $mCallTypeAsset, $mRepoMod])
            ->getMock();
        // $resp = $this->get($req);
        $mRespGet = new Response\Get();
        $mRespGet->markSucceed();
        $mRespGet->setData(
            [Account::ATTR_ASSET_TYPE_ID => $ASSET_TYPE_ID, Account::ATTR_CUST_ID => $CUST_ID]
        );
        $call->expects($this->once())
            ->method('get')
            ->willReturn($mRespGet);
        /** tests  */
        $req = new Request\GetRepresentative();
        $req->setAssetTypeCode($ASSET_CODE);
        $resp = $call->getRepresentative($req);
        $this->assertTrue($resp->isSucceed());
        $this->assertEquals($CUST_ID, $resp->getData(Account::ATTR_CUST_ID));
    }

    public function test_getRepresentative_byAssetCode_idNotFound()
    {
        /** === Test Data === */
        $ASSET_CODE = 'TEST_ASSET';

        /** === Extract mocks === */
        /**
         * @var $mLogger \PHPUnit_Framework_MockObject_MockObject for \Psr\Log\LoggerInterface
         * @var $mDba \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Core\Lib\Context\IDbAdapter
         * @var $mConn \PHPUnit_Framework_MockObject_MockObject for \Zend_Db_Adapter_Pdo_Abstract
         * @var $mToolbox \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Core\Lib\IToolbox
         * @var $mCallRepo \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Core\Lib\Service\IRepo
         * @var $mCallTypeAsset \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Accounting\Service\ITypeAsset
         * @var $mRepoMod \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Accounting\Repo\IModule
         * @var $call Call
         */
        extract($this->_prepareMocks());


        // $respCode = $this->_callTypeAsset->getByCode($reqCode);
        $mRespCode = new \Praxigento\Accounting\Service\Type\Asset\Response\GetByCode();
        $mCallTypeAsset
            ->expects($this->once())
            ->method('getByCode')
            ->willReturn($mRespCode);

        /** === Call and asserts  === */
        $req = new Request\GetRepresentative();
        $req->setAssetTypeCode($ASSET_CODE);
        $resp = $call->getRepresentative($req);
        $this->assertFalse($resp->isSucceed());
    }

    public function test_getRepresentative_byAssetId_accountsFound()
    {
        /** === Test Data === */
        $CUST_ID = '21';
        $ASSET_TYPE_ID = '12';
        $ACCOUNT_ID = '34';

        /** === Extract mocks === */
        /**
         * @var $mLogger \PHPUnit_Framework_MockObject_MockObject for \Psr\Log\LoggerInterface
         * @var $mDba \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Core\Lib\Context\IDbAdapter
         * @var $mConn \PHPUnit_Framework_MockObject_MockObject for \Zend_Db_Adapter_Pdo_Abstract
         * @var $mToolbox \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Core\Lib\IToolbox
         * @var $mCallRepo \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Core\Lib\Service\IRepo
         * @var $mCallTypeAsset \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Accounting\Service\ITypeAsset
         * @var $mRepoMod \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Accounting\Repo\IModule
         * @var $call Call
         */
        extract($this->_prepareMocks());


        // $customerId = $this->_subDb->getRepresentativeCustomerId();
        $mRepoMod
            ->expects($this->once())
            ->method('getRepresentativeCustomerId')
            ->willReturn($CUST_ID);
        // $resp = $this->_callRepo->getEntities($req);
        $mResp = new GetEntitiesResponse();
        $mResp->markSucceed();
        $mResp->setData([
            [
                Account::ATTR_ID => $ACCOUNT_ID,
                Account::ATTR_ASSET_TYPE_ID => $ASSET_TYPE_ID
            ]
        ]);
        $mCallRepo
            ->expects($this->once())
            ->method('getEntities')
            ->willReturn($mResp);

        /** === Call and asserts  === */
        $req = new Request\GetRepresentative();
        $req->setAssetTypeId($ASSET_TYPE_ID);
        $resp = $call->getRepresentative($req);
        $this->assertTrue($resp->isSucceed());
        $this->assertEquals($ACCOUNT_ID, $resp->getData(Account::ATTR_ID));
        /* secondary request to use cache */
        $resp = $call->getRepresentative($req);
        $this->assertTrue($resp->isSucceed());
        $this->assertEquals($ACCOUNT_ID, $resp->getData(Account::ATTR_ID));
    }

    public function test_get_byAccountId()
    {
        /** === Test Data === */
        $CUST_ID = '21';
        $ASSET_TYPE_ID = '12';
        $ACCOUNT_ID = '34';
        $TABLE = 'ACCOUNT_TABLE';

        /** === Extract mocks === */
        /**
         * @var $mLogger \PHPUnit_Framework_MockObject_MockObject for \Psr\Log\LoggerInterface
         * @var $mDba \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Core\Lib\Context\IDbAdapter
         * @var $mConn \PHPUnit_Framework_MockObject_MockObject for \Zend_Db_Adapter_Pdo_Abstract
         * @var $mToolbox \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Core\Lib\IToolbox
         * @var $mCallRepo \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Core\Lib\Service\IRepo
         * @var $mCallTypeAsset \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Accounting\Service\ITypeAsset
         * @var $mRepoMod \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Accounting\Repo\IModule
         * @var $call Call
         */
        extract($this->_prepareMocks());
        // $tbl = $this->_getTableName(Account::ENTITY_NAME);
        $mDba
            ->expects($this->once())
            ->method('getTableName')
            ->will($this->returnValue($TABLE));
        // $query = $this->_conn->select();
        $mQuery = $this->_mockDbSelect();
        $mConn
            ->expects($this->once())
            ->method('select')
            ->will($this->returnValue($mQuery));
        $mQuery
            ->expects($this->once())
            ->method('from')
            ->with($this->equalTo($TABLE));
        // $data = $this->_conn->fetchRow($query, [ 'customerId' => $customerId, 'assetTypeId' => $assetTypeId ]);
        $mData = [
            Account::ATTR_ID => $ACCOUNT_ID,
            Account::ATTR_CUST_ID => $CUST_ID,
            Account::ATTR_ASSET_TYPE_ID => $ASSET_TYPE_ID
        ];
        $mConn->expects($this->once())
            ->method('fetchRow')
            ->will($this->returnValue($mData));

        /** === Call and asserts  === */
        $req = new Request\Get();
        $req->setAccountId($ACCOUNT_ID);
        $resp = $call->get($req);
        $this->assertTrue($resp->isSucceed());
    }

    public function test_get_byAssetCode()
    {
        /** === Test Data === */
        $CUST_ID = '21';
        $ASSET_TYPE_ID = '12';
        $ASSET_CODE = 'ASSET';
        $ACCOUNT_ID = '34';
        $TABLE = 'ACCOUNT_TABLE';

        /** === Extract mocks === */
        /**
         * @var $mLogger \PHPUnit_Framework_MockObject_MockObject for \Psr\Log\LoggerInterface
         * @var $mDba \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Core\Lib\Context\IDbAdapter
         * @var $mConn \PHPUnit_Framework_MockObject_MockObject for \Zend_Db_Adapter_Pdo_Abstract
         * @var $mToolbox \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Core\Lib\IToolbox
         * @var $mCallRepo \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Core\Lib\Service\IRepo
         * @var $mCallTypeAsset \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Accounting\Service\ITypeAsset
         * @var $mRepoMod \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Accounting\Repo\IModule
         * @var $call Call
         */
        extract($this->_prepareMocks());

        // $respTypeAsset = $this->_callTypeAsset->getByCode($reqTypeAsset);
        $mRespGetByCode = new TypeAssetResponseGetByCode();
        $mRespGetByCode->setData([
            TypeAsset::ATTR_ID => $ASSET_TYPE_ID
        ]);
        $mRespGetByCode->setErrorCode(TypeAssetResponseGetByCode::ERR_NO_ERROR);
        $mCallTypeAsset
            ->expects($this->any())
            ->method('getByCode')
            ->will($this->returnValue($mRespGetByCode));
        // $tbl = $this->_getTableName(Account::ENTITY_NAME);
        $mDba
            ->expects($this->once())
            ->method('getTableName')
            ->will($this->returnValue($TABLE));
        // $query = $this->_conn->select();
        $mQuery = $this->_mockDbSelect();
        $mConn
            ->expects($this->once())
            ->method('select')
            ->will($this->returnValue($mQuery));
        $mQuery
            ->expects($this->once())
            ->method('from')
            ->with($this->equalTo($TABLE));
        // $data = $this->_conn->fetchRow($query, [ 'customerId' => $customerId, 'assetTypeId' => $assetTypeId ]);
        $mData = [
            Account::ATTR_ID => $ACCOUNT_ID,
            Account::ATTR_CUST_ID => $CUST_ID,
            Account::ATTR_ASSET_TYPE_ID => $ASSET_TYPE_ID
        ];
        $mConn->expects($this->once())
            ->method('fetchRow')
            ->will($this->returnValue($mData));

        /** === Call and asserts  === */
        $req = new Request\Get();
        $req->setCustomerId($CUST_ID);
        $req->setAssetTypeCode($ASSET_CODE);
        $resp = $call->get($req);
        $this->assertTrue($resp->isSucceed());
    }

    public function test_get_byAssetId()
    {
        /** === Test Data === */
        $CUST_ID = '21';
        $ASSET_TYPE_ID = '12';
        $ACCOUNT_ID = '34';
        $TABLE = 'ACCOUNT_TABLE';

        /** === Extract mocks === */
        /**
         * @var $mLogger \PHPUnit_Framework_MockObject_MockObject for \Psr\Log\LoggerInterface
         * @var $mDba \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Core\Lib\Context\IDbAdapter
         * @var $mConn \PHPUnit_Framework_MockObject_MockObject for \Zend_Db_Adapter_Pdo_Abstract
         * @var $mToolbox \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Core\Lib\IToolbox
         * @var $mCallRepo \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Core\Lib\Service\IRepo
         * @var $mCallTypeAsset \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Accounting\Service\ITypeAsset
         * @var $mRepoMod \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Accounting\Repo\IModule
         * @var $call Call
         */
        extract($this->_prepareMocks());

        // $tbl = $this->_resource->getTableName(Asset::ENTITY_NAME);
        $mDba
            ->expects($this->once())
            ->method('getTableName')
            ->will($this->returnValue($TABLE));
        // $query = $this->_conn->select();
        $mQuery = $this->_mockDbSelect();
        $mConn
            ->expects($this->once())
            ->method('select')
            ->will($this->returnValue($mQuery));
        $mQuery
            ->expects($this->once())
            ->method('from')
            ->with($this->equalTo($TABLE));
        // $data = $this->_conn->fetchRow($query, [ 'customerId' => $customerId, 'assetTypeId' => $assetTypeId ]);
        $mData = [
            Account::ATTR_ID => $ACCOUNT_ID,
            Account::ATTR_CUST_ID => $CUST_ID,
            Account::ATTR_ASSET_TYPE_ID => $ASSET_TYPE_ID
        ];
        $mConn->expects($this->once())
            ->method('fetchRow')
            ->will($this->returnValue($mData));

        /** === Call and asserts  === */
        $req = new Request\Get();
        $req->setCustomerId($CUST_ID);
        $req->setAssetTypeId($ASSET_TYPE_ID);
        $resp = $call->get($req);
        $this->assertTrue($resp->isSucceed());
    }

    public function test_get_createNewAccountIfMissed()
    {
        /** === Test Data === */
        $CUST_ID = '21';
        $ASSET_TYPE_ID = '12';
        $ACCOUNT_ID = '34';
        $TABLE = 'ACCOUNT_TABLE';

        /** === Extract mocks === */
        /**
         * @var $mLogger \PHPUnit_Framework_MockObject_MockObject for \Psr\Log\LoggerInterface
         * @var $mDba \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Core\Lib\Context\IDbAdapter
         * @var $mConn \PHPUnit_Framework_MockObject_MockObject for \Zend_Db_Adapter_Pdo_Abstract
         * @var $mToolbox \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Core\Lib\IToolbox
         * @var $mCallRepo \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Core\Lib\Service\IRepo
         * @var $mCallTypeAsset \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Accounting\Service\ITypeAsset
         * @var $mRepoMod \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Accounting\Repo\IModule
         * @var $call Call
         */
        extract($this->_prepareMocks());


        // $tbl = $this->_getTableName(Account::ENTITY_NAME);
        $mDba
            ->expects($this->once())
            ->method('getTableName')
            ->will($this->returnValue($TABLE));
        // $query = $this->_conn->select();
        $mQuery = $this->_mockDbSelect();
        $mConn
            ->expects($this->once())
            ->method('select')
            ->will($this->returnValue($mQuery));
        $mQuery
            ->expects($this->once())
            ->method('from')
            ->with($this->equalTo($TABLE));
        // $data = $this->_conn->fetchRow($query, [ 'customerId' => $customerId, 'assetTypeId' => $assetTypeId ]);
        $mConn->expects($this->once())
            ->method('fetchRow')
            ->will($this->returnValue(false));
        // $this->_conn->insert($tbl, $data);
        $mConn->expects($this->once())
            ->method('insert');
        // $accId = $this->_conn->lastInsertId($tbl);
        $mConn->expects($this->once())
            ->method('lastInsertId')
            ->will($this->returnValue($ACCOUNT_ID));

        /** === Call and asserts  === */
        $req = new Request\Get();
        $req->setCustomerId($CUST_ID);
        $req->setAssetTypeId($ASSET_TYPE_ID);
        $req->setCreateNewAccountIfMissed(true);
        $resp = $call->get($req);
        $this->assertTrue($resp->isSucceed());
    }

    public function test_updateBalance_negative()
    {
        /** === Test Data === */
        $ACCOUNT_ID = '34';
        $CHANGE_VALUE = -21;
        $TABLE = 'ACCOUNT_TABLE';

        /** === Extract mocks === */
        /**
         * @var $mLogger \PHPUnit_Framework_MockObject_MockObject for \Psr\Log\LoggerInterface
         * @var $mDba \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Core\Lib\Context\IDbAdapter
         * @var $mConn \PHPUnit_Framework_MockObject_MockObject for \Zend_Db_Adapter_Pdo_Abstract
         * @var $mToolbox \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Core\Lib\IToolbox
         * @var $mCallRepo \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Core\Lib\Service\IRepo
         * @var $mCallTypeAsset \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Accounting\Service\ITypeAsset
         * @var $mRepoMod \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Accounting\Repo\IModule
         * @var $call Call
         */
        extract($this->_prepareMocks());


        // $accId = $this->_conn->quote($request->accountId, \Zend_Db::INT_TYPE);
        $mConn
            ->expects($this->at(0))
            ->method('quote')
            ->will($this->returnValue((int)$ACCOUNT_ID));
        // $value = $this->_conn->quote($request->changeValue, \Zend_Db::FLOAT_TYPE);
        $mConn
            ->expects($this->at(1))
            ->method('quote')
            ->will($this->returnValue($CHANGE_VALUE));
        // $tbl = $this->_resource->getTableName(Account::ENTITY_NAME);
        $mDba
            ->expects($this->once())
            ->method('getTableName')
            ->will($this->returnValue($TABLE));
        // $rowsUpdated = $this->_conn->update($tbl, $bind, $where);
        $mConn
            ->expects($this->once())
            ->method('update')
            ->will($this->returnValue(1));

        /** === Call and asserts  === */
        $req = new Request\UpdateBalance();
        $req->setAccountId($ACCOUNT_ID);
        $req->setChangeValue($CHANGE_VALUE);
        $resp = $call->updateBalance($req);
        $this->assertTrue($resp->isSucceed());
    }

    public function test_updateBalance_positive()
    {
        /** === Test Data === */
        $ACCOUNT_ID = '34';
        $CHANGE_VALUE = 21;
        $TABLE = 'ACCOUNT_TABLE';

        /** === Extract mocks === */
        /**
         * @var $mLogger \PHPUnit_Framework_MockObject_MockObject for \Psr\Log\LoggerInterface
         * @var $mDba \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Core\Lib\Context\IDbAdapter
         * @var $mConn \PHPUnit_Framework_MockObject_MockObject for \Zend_Db_Adapter_Pdo_Abstract
         * @var $mToolbox \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Core\Lib\IToolbox
         * @var $mCallRepo \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Core\Lib\Service\IRepo
         * @var $mCallTypeAsset \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Accounting\Service\ITypeAsset
         * @var $mRepoMod \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Accounting\Repo\IModule
         * @var $call Call
         */
        extract($this->_prepareMocks());

        // $accId = $this->_conn->quote($request->accountId, \Zend_Db::INT_TYPE);
        $mConn
            ->expects($this->at(0))
            ->method('quote')
            ->will($this->returnValue((int)$ACCOUNT_ID));
        // $value = $this->_conn->quote($request->changeValue, \Zend_Db::FLOAT_TYPE);
        $mConn
            ->expects($this->at(1))
            ->method('quote')
            ->will($this->returnValue($CHANGE_VALUE));
        // $tbl = $this->_resource->getTableName(Account::ENTITY_NAME);
        $mDba
            ->expects($this->once())
            ->method('getTableName')
            ->will($this->returnValue($TABLE));
        // $rowsUpdated = $this->_conn->update($tbl, $bind, $where);
        $mConn
            ->expects($this->once())
            ->method('update')
            ->will($this->returnValue(1));

        /** === Call and asserts  === */
        $req = new Request\UpdateBalance();
        $req->setAccountId($ACCOUNT_ID);
        $req->setChangeValue($CHANGE_VALUE);
        $resp = $call->updateBalance($req);
        $this->assertTrue($resp->isSucceed());
    }
}