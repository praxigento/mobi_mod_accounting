<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Lib\Repo\Def;

use Praxigento\Accounting\Config as Cfg;
use Praxigento\Accounting\Lib\Entity\Account;
use Praxigento\Accounting\Lib\Entity\Balance;
use Praxigento\Accounting\Lib\Entity\Transaction;
use Praxigento\Core\Lib\Entity\Type\Base as TypeBase;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class Module_UnitTest extends \Praxigento\Core\Lib\Test\BaseMockeryCase {
    /** @var  \Mockery\MockInterface */
    private $mConn;
    /** @var  \Mockery\MockInterface */
    private $mDba;
    /** @var  \Mockery\MockInterface */
    private $mRepoBasic;
    /** @var  Module */
    private $repo;

    protected function setUp() {
        parent::setUp();
        $this->mLogger = $this->_mockLogger();
        $this->mConn = $this->_mockDba();
        $this->mDba = $this->_mockRsrcConnOld($this->mConn);
        $this->mRepoBasic = $this->_mockRepoBasic($this->mDba);
        $this->repo = new Module($this->mRepoBasic);
    }


    public function test_cacheReset() {
        /** === Test Data === */

        /** === Setup Mocks === */

        /** === Call and asserts  === */
        $this->repo->cacheReset();
    }

    public function test_getBalanceMaxDate_byAssetId() {
        /** === Test Data === */
        $ASSET_TYPE_ID = '12';
        $DATE_FOUND = '20150810';

        /** === Setup Mocks === */
        // $tbl = $this->_resource->getTableName(Asset::ENTITY_NAME);
        $this->mDba
            ->shouldReceive('getTableName');
        // $query = $this->_conn->select();
        $mQuery = $this->_mockDbSelect();
        $this->mConn
            ->shouldReceive('select')
            ->andReturn($mQuery);
        $mQuery
            ->shouldReceive('from');
        $mQuery
            ->shouldReceive('joinLeft');
        $mQuery
            ->shouldReceive('where');
        $mQuery
            ->shouldReceive('order');
        // $row = $this->_conn->fetchOne($query, $bind);
        $this->mConn
            ->shouldReceive('fetchOne')
            ->andReturn($DATE_FOUND);

        /** === Call and asserts  === */
        $resp = $this->repo->getBalanceMaxDate($ASSET_TYPE_ID);
        $this->assertEquals($DATE_FOUND, $resp);
    }

    public function test_getBalancesOnDate() {
        /** === Test Data === */
        $ASSET_TYPE_ID = '21';
        $DATE = '20150810';

        /** === Extract mocks === */

        /** === Setup Mocks === */
        // $tblAccount = $this->_getTableName(Account::ENTITY_NAME);
        $this->mDba
            ->shouldReceive('getTableName')
            ->andReturn('ACCOUNT');
        // $tblBalance = $this->_getTableName(Balance::ENTITY_NAME);
        $this->mDba
            ->shouldReceive('getTableName')
            ->andReturn('BALANCE');
        // $q4Max = $conn->select();
        $mQ4Max = $this->_mockDbSelect();
        $this->mConn
            ->shouldReceive('select')
            ->once()
            ->andReturn($mQ4Max);
        // $q4Max->from([ $asBal4Max => $tblBalance ], [ Balance::ATTR_ACCOUNT_ID, $expMaxDate ]);
        $mQ4Max->shouldReceive('from');
        // $q4Max->group($asBal4Max . '.' . Balance::ATTR_ACCOUNT_ID);
        $mQ4Max->shouldReceive('group');
        // $q4Max->where($asBal4Max . '.' . Balance::ATTR_DATE . '<=:date');
        $mQ4Max->shouldReceive('where');
        // $query = $conn->select();
        $mQuery = $this->_mockDbSelect();
        $this->mConn
            ->shouldReceive('select')
            ->once()
            ->andReturn($mQuery);
        // $query->from([ $asAccount => $tblAccount ], [ Account::ATTR_ID, Account::ATTR_CUST_ID ]);
        $mQuery->shouldReceive('from');
        // $query->joinLeft([ $asMax => $q4Max ], $on, $cols);
        // $query->joinLeft([ $asBal => $tblBalance ], $on, $cols);
        $mQuery->shouldReceive('joinLeft');
        // $query->where("$whereByAssetType AND $whereByDate");
        $mQuery->shouldReceive('where');
        // $rows = $conn->fetchAll($query, $bind);
        $this->mConn
            ->shouldReceive('fetchAll')
            ->andReturn([ Account::ATTR_ID => 1024 ]);

        /** === Call and asserts  === */
        $resp = $this->repo->getBalancesOnDate($ASSET_TYPE_ID, $DATE);
        $this->assertTrue(is_array($resp));
    }

    public function test_getRepresentativeCustomerId_found() {
        /** === Test Data === */
        $EMAIL = Module::CUSTOMER_REPRESENTATIVE_EMAIL;
        $CUST_ID = '12';

        /** === Setup Mocks === */

        // $where = Cfg::E_CUSTOMER_A_EMAIL . '=' . $conn->quote(self::CUSTOMER_REPRESENTATIVE_EMAIL);
        $this->mConn
            ->shouldReceive('quote')
            ->with($EMAIL)
            ->andReturn("'$EMAIL'");
        // $data = $this->_repoBasic->getEntities(Cfg::ENTITY_MAGE_CUSTOMER, Cfg::E_CUSTOMER_A_ENTITY_ID, $where);
        $this->mRepoBasic
            ->shouldReceive('getEntities')
            ->andReturn([ [ Cfg::E_CUSTOMER_A_ENTITY_ID => $CUST_ID ] ]);

        /** === Call and asserts  === */
        $resp = $this->repo->getRepresentativeCustomerId();
        $this->assertEquals($CUST_ID, $resp);
    }

    public function test_getRepresentativeCustomerId_notFound() {
        /** === Test Data === */
        $EMAIL = Module::CUSTOMER_REPRESENTATIVE_EMAIL;
        $CUST_ID = '12';

        /** === Setup Mocks === */

        // $where = self::ATTR_CUST_EMAIL . '=' . $this->_conn->quote(self::CUSTOMER_REPRESENTATIVE_EMAIL);
        $this->mConn
            ->shouldReceive('quote')
            ->with($EMAIL)
            ->andReturn("'$EMAIL'");
        // $data = $this->_repoCore->getEntities(Cfg::ENTITY_MAGE_CUSTOMER, Cfg::E_CUSTOMER_A_ENTITY_ID, $where);
        $this->mRepoBasic
            ->shouldReceive('getEntities')
            ->andReturn([ ]);
        // $id = $this->_repoCore->addEntity(Cfg::ENTITY_MAGE_CUSTOMER, $bind);
        $this->mRepoBasic
            ->shouldReceive('addEntity')
            ->andReturn($CUST_ID);

        /** === Call and asserts  === */
        $data = $this->repo->getRepresentativeCustomerId();
        $this->assertEquals($CUST_ID, $data);
    }

    public function test_getTransactionMinDateApplied() {
        /** === Test Data === */
        $ASSET_TYPE_ID = '12';
        $DATE_FOUND = '20150810';

        /** === Setup Mocks === */

        // $tbl = $this->_resource->getTableName(Asset::ENTITY_NAME);
        $this->mDba
            ->shouldReceive('getTableName');
        // $query = $this->_conn->select();
        $mQuery = $this->_mockDbSelect();
        $this->mConn
            ->shouldReceive('select')
            ->andReturn($mQuery);
        $mQuery
            ->shouldReceive('from');
        $mQuery
            ->shouldReceive('joinLeft');
        $mQuery
            ->shouldReceive('where');
        $mQuery
            ->shouldReceive('order');
        // $row = $this->_conn->fetchOne($query, $bind);
        $this->mConn
            ->shouldReceive('fetchOne')
            ->andReturn($DATE_FOUND);
        /** === Call and asserts  === */
        $resp = $this->repo->getTransactionMinDateApplied($ASSET_TYPE_ID);
        $this->assertEquals($DATE_FOUND, $resp);
    }

    public function test_getTransactionsForPeriod() {
        /** === Test Data === */
        $ASSET_TYPE_ID = '21';
        $DATE_FROM = '20150810';
        $DATE_TO = '20150815';

        /** === Setup Mocks === */

        // $paramAssetType = $this->_conn->quote($assetTypeId, \Zend_Db::INT_TYPE);
        $this->mConn
            ->shouldReceive('quote')
            ->andReturn((int)$ASSET_TYPE_ID);
        // $tblAccount = $this->_resource->getTableName(Account::ENTITY_NAME);
        $this->mDba
            ->shouldReceive('getTableName')
            ->andReturn('ACCOUNT');
        // $tblTrans = $this->_resource->getTableName(Transaction::ENTITY_NAME);
        $this->mDba
            ->shouldReceive('getTableName')
            ->andReturn('TRANSACTION');
        // $query = $this->_conn->select();
        $mQuery = $this->_mockDbSelect();
        $this->mConn
            ->shouldReceive('select')
            ->andReturn($mQuery);
        //  $query->from([ $asAccount => $tblAccount ], [ ]);
        $mQuery
            ->shouldReceive('from');
        // $query->join([ $asTrans => $tblTrans ], $on);
        $mQuery
            ->shouldReceive('join');
        //$query->where($asAccount . '.' . Account::ATTR_ASSET_TYPE_ID . '=:asset_type_id');
        //$query->where($asTrans . '.' . Transaction::ATTR_ID . ' IS NOT NULL');
        //$query->where($asTrans . '.' . Transaction::ATTR_DATE_APPLIED . '>=:date_from');
        //$query->where($asTrans . '.' . Transaction::ATTR_DATE_APPLIED . '<=:date_to');
        $mQuery
            ->shouldReceive('where');
        // $query->order($asTrans . '.' . Transaction::ATTR_DATE_APPLIED . ' ASC');
        $mQuery
            ->shouldReceive('order');
        // $result = $this->_conn->fetchAll($query, $bind);
        $this->mConn
            ->shouldReceive('fetchAll')
            ->andReturn([ Transaction::ATTR_ID => 1024 ]);

        /** === Call and asserts  === */
        $resp = $this->repo->getTransactionsForPeriod($ASSET_TYPE_ID, $DATE_FROM, $DATE_TO);
        $this->assertTrue(is_array($resp));
    }

    public function test_getTypeAssetIdByCode() {
        /** === Test Data === */
        $ASSET_TYPE_CODE = 'code';
        $ASSET_TYPE_ID = 2;

        /** === Setup Mocks === */

        // $tbl = $this->_getTableName(TypeAsset::ENTITY_NAME);
        $this->mDba
            ->shouldReceive('getTableName')
            ->andReturn('ACCOUNT');
        // $query = $this->_getConn()->select();
        $mQuery = $this->_mockDbSelect();
        $this->mConn
            ->shouldReceive('select')
            ->andReturn($mQuery);
        // $query->from($tbl);
        $mQuery->shouldReceive('from');
        // $query->where(TypeBase::ATTR_CODE . '=:code');
        $mQuery->shouldReceive('where');
        //  $data = $this->_getConn()->fetchRow($query, [ 'code' => $assetTypeCode ]);
        $this->mConn
            ->shouldReceive('fetchRow')
            ->andReturn([ TypeBase::ATTR_ID => $ASSET_TYPE_ID ]);

        /** === Call and asserts  === */
        $resp = $this->repo->getTypeAssetIdByCode($ASSET_TYPE_CODE);
        $this->assertEquals($ASSET_TYPE_ID, $resp);
    }

    public function test_getTypeOperationIdByCode() {
        /** === Test Data === */
        $OPER_TYPE_CODE = 'code';
        $OPER_TYPE_ID = 2;

        /** === Setup Mocks === */

        // $tbl = $this->_getTableName(TypeAsset::ENTITY_NAME);
        $this->mDba
            ->shouldReceive('getTableName')
            ->andReturn('TABLE');
        // $query = $this->_getConn()->select();
        $mQuery = $this->_mockDbSelect();
        $this->mConn
            ->shouldReceive('select')
            ->andReturn($mQuery);
        // $query->from($tbl);
        $mQuery->shouldReceive('from');
        // $query->where(TypeBase::ATTR_CODE . '=:code');
        $mQuery->shouldReceive('where');
        //  $data = $this->_getConn()->fetchRow($query, [ 'code' => $assetTypeCode ]);
        $this->mConn
            ->shouldReceive('fetchRow')
            ->andReturn([ TypeBase::ATTR_ID => $OPER_TYPE_ID ]);

        /** === Call and asserts  === */
        $resp = $this->repo->getTypeOperationIdByCode($OPER_TYPE_CODE);
        $this->assertEquals($OPER_TYPE_ID, $resp);
    }

    public function test_updateBalances() {
        /** === Test Data === */

        /** === Setup Mocks === */

        // $this->_conn->beginTransaction();
        $this->mConn
            ->shouldReceive('beginTransaction');
        // $tbl = $this->_resource->getTableName(Balance::ENTITY_NAME);
        $this->mDba
            ->shouldReceive('getTableName')
            ->andReturn('BALANCE');
        // $this->_conn->insert($tbl, $data);
        $this->mConn
            ->shouldReceive('insert');
        // $this->_conn->commit();
        $this->mConn
            ->shouldReceive('commit');

        /** === Call and asserts  === */
        $updateData = [
            21 => [
                '20151123' => [
                    Balance::ATTR_ACCOUNT_ID => '1',
                    Balance::ATTR_DATE       => '20151123'
                ]
            ]
        ];
        $this->repo->updateBalances($updateData);
    }
}