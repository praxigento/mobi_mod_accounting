<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Lib\Service\Balance;

use Praxigento\Accounting\Lib\Entity\Balance;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class Call_UnitTest extends \Praxigento\Core\Lib\Test\BaseTestCase {

    /**
     * Prepare mocks and object to test.
     *
     * @return array
     */
    private function _prepareMocks() {
        $mLogger = $this->_mockLogger();
        $mConn = $this->_mockConnection();
        $mDba = $this->_mockDbAdapter(null, $mConn);
        $mToolPeriod = $this->_mockFor('Praxigento\Core\Lib\Tool\Period');
        $mToolbox = $this->_mockToolbox(null, null, null, $mToolPeriod);
        $mCallRepo = $this->_mockCallRepo();
        $mRepoMod = $this->_mockFor('\Praxigento\Accounting\Lib\Repo\IModule');
        $mSubCalcSimple = $this->_mockFor('Praxigento\Accounting\Lib\Service\Balance\Sub\CalcSimple');
        $call = new Call($mLogger, $mDba, $mToolbox, $mCallRepo, $mRepoMod, $mSubCalcSimple);
        $result = [
            'mLogger'        => $mLogger,
            'mDba'           => $mDba,
            'mConn'          => $mConn,
            'mToolbox'       => $mToolbox,
            'mToolPeriod'    => $mToolPeriod,
            'mCallRepo'      => $mCallRepo,
            'mRepoMod'       => $mRepoMod,
            'mSubCalcSimple' => $mSubCalcSimple,
            'call'           => $call
        ];
        return $result;
    }

    public function test_calc() {
        /** === Test Data === */
        $ASSET_TYPE_ID = 21;
        $DATESTAMP_TO = '20151123';

        /** === Extract mocks === */
        /**
         * @var $mLogger \PHPUnit_Framework_MockObject_MockObject for \Psr\Log\LoggerInterface
         * @var $mDba \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Core\Lib\Context\IDbAdapter
         * @var $mConn \PHPUnit_Framework_MockObject_MockObject for \Zend_Db_Adapter_Pdo_Abstract
         * @var $mToolbox \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Core\Lib\IToolbox
         * @var $mToolPeriod \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Core\Lib\Tool\Period
         * @var $mCallRepo \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Core\Lib\Service\IRepo
         * @var $mRepoMod \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Accounting\Lib\Repo\IModule
         * @var $mSubCalcSimple \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Accounting\Lib\Service\Balance\Sub\CalcSimple
         * @var $call Call
         */
        extract($this->_prepareMocks());

        /** === Setup Mocks === */

        /**
         * Prepare request and perform call.
         */
        /** @var  $call Call */
        $call = new Call($mLogger, $mDba, $mToolbox, $mCallRepo, $mRepoMod, $mSubCalcSimple);
        $req = new Request\Calc();
        $req->assetTypeId = $ASSET_TYPE_ID;
        $req->dateTo = $DATESTAMP_TO;
        $resp = $call->calc($req);
        $this->assertTrue($resp->isSucceed());
    }

    public function test_getBalancesOnDate_fail() {
        /** === Test Data === */
        $ASSET_TYPE_ID = 21;
        $DATESTAMP = '20151123';

        /** === Extract mocks === */
        /**
         * @var $mLogger \PHPUnit_Framework_MockObject_MockObject for \Psr\Log\LoggerInterface
         * @var $mDba \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Core\Lib\Context\IDbAdapter
         * @var $mConn \PHPUnit_Framework_MockObject_MockObject for \Zend_Db_Adapter_Pdo_Abstract
         * @var $mToolbox \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Core\Lib\IToolbox
         * @var $mToolPeriod \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Core\Lib\Tool\Period
         * @var $mCallRepo \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Core\Lib\Service\IRepo
         * @var $mRepoMod \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Accounting\Lib\Repo\IModule
         * @var $mSubCalcSimple \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Accounting\Lib\Service\Balance\Sub\CalcSimple
         * @var $call Call
         */
        extract($this->_prepareMocks());

        /** === Setup Mocks === */
        // $rows = $this->_subDb->getBalancesOnDate($assetTypeId, $dateOn);
        $mRepoMod
            ->expects($this->once())
            ->method('getBalancesOnDate')
            ->will($this->returnValue([ ]));
        /**
         * Prepare request and perform call.
         */
        /** @var  $call Call */
        $call = new Call($mLogger, $mDba, $mToolbox, $mCallRepo, $mRepoMod, $mSubCalcSimple);
        $req = new Request\GetBalancesOnDate();
        $req->setData(Request\GetBalancesOnDate::ASSET_TYPE_ID, $ASSET_TYPE_ID);
        $req->setData(Request\GetBalancesOnDate::DATE, $DATESTAMP);
        $resp = $call->getBalancesOnDate($req);
        $this->assertFalse($resp->isSucceed());
    }

    public function test_getBalancesOnDate_success() {
        /** === Test Data === */
        $ASSET_TYPE_ID = 21;
        $DATESTAMP = '20151123';

        /** === Extract mocks === */
        /**
         * @var $mLogger \PHPUnit_Framework_MockObject_MockObject for \Psr\Log\LoggerInterface
         * @var $mDba \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Core\Lib\Context\IDbAdapter
         * @var $mConn \PHPUnit_Framework_MockObject_MockObject for \Zend_Db_Adapter_Pdo_Abstract
         * @var $mToolbox \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Core\Lib\IToolbox
         * @var $mToolPeriod \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Core\Lib\Tool\Period
         * @var $mCallRepo \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Core\Lib\Service\IRepo
         * @var $mRepoMod \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Accounting\Lib\Repo\IModule
         * @var $mSubCalcSimple \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Accounting\Lib\Service\Balance\Sub\CalcSimple
         * @var $call Call
         */
        extract($this->_prepareMocks());

        /** === Setup Mocks === */
        // $rows = $this->_subDb->getBalancesOnDate($assetTypeId, $dateOn);
        $mRepoMod
            ->expects($this->once())
            ->method('getBalancesOnDate')
            ->will($this->returnValue(10));
        /**
         * Prepare request and perform call.
         */
        /** @var  $call Call */
        $call = new Call($mLogger, $mDba, $mToolbox, $mCallRepo, $mRepoMod, $mSubCalcSimple);
        $req = new Request\GetBalancesOnDate();
        $req->setData(Request\GetBalancesOnDate::ASSET_TYPE_ID, $ASSET_TYPE_ID);
        $req->setData(Request\GetBalancesOnDate::DATE, $DATESTAMP);
        $resp = $call->getBalancesOnDate($req);
        $this->assertTrue($resp->isSucceed());
    }

    public function test_getLastDate_withBalanceMaxDate() {
        /** === Test Data === */
        $ASSET_TYPE_ID = 21;
        $DATESTAMP_MAX = '20151123';
        $DATESTAMP_LAST = '20151122';

        /** === Extract mocks === */
        /**
         * @var $mLogger \PHPUnit_Framework_MockObject_MockObject for \Psr\Log\LoggerInterface
         * @var $mDba \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Core\Lib\Context\IDbAdapter
         * @var $mConn \PHPUnit_Framework_MockObject_MockObject for \Zend_Db_Adapter_Pdo_Abstract
         * @var $mToolbox \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Core\Lib\IToolbox
         * @var $mToolPeriod \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Core\Lib\Tool\Period
         * @var $mCallRepo \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Core\Lib\Service\IRepo
         * @var $mRepoMod \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Accounting\Lib\Repo\IModule
         * @var $mSubCalcSimple \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Accounting\Lib\Service\Balance\Sub\CalcSimple
         * @var $call Call
         */
        extract($this->_prepareMocks());

        /** === Setup Mocks === */
        //  $balanceMaxDate = $this->_subDb->getBalanceMaxDate(...)
        $mRepoMod
            ->expects($this->once())
            ->method('getBalanceMaxDate')
            ->will($this->returnValue($DATESTAMP_MAX));
        // $dayBefore = $this->_toolPeriod->getPeriodPrev($balanceMaxDate, Period::TYPE_DAY);
        $mToolPeriod
            ->expects($this->once())
            ->method('getPeriodPrev')
            ->will($this->returnValue($DATESTAMP_LAST));
        /**
         * Prepare request and perform call.
         */
        /** @var  $call Call */
        $call = new Call($mLogger, $mDba, $mToolbox, $mCallRepo, $mRepoMod, $mSubCalcSimple);
        $req = new Request\GetLastDate();
        $req->assetTypeId = $ASSET_TYPE_ID;
        $resp = $call->getLastDate($req);
        $this->assertTrue($resp->isSucceed());
        $this->assertEquals($DATESTAMP_LAST, $resp->getLastDate());
    }

    public function test_getLastDate_withTransactionMinDate() {
        /** === Test Data === */
        $ASSET_TYPE_ID = 21;
        $TIMESTAMP_TRN_MIN = '2015-11-22 13:34:45';
        $DATESTAMP_TRN_MIN = '20151122';
        $DATESTAMP_LAST = '20151121';

        /** === Extract mocks === */
        /**
         * @var $mLogger \PHPUnit_Framework_MockObject_MockObject for \Psr\Log\LoggerInterface
         * @var $mDba \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Core\Lib\Context\IDbAdapter
         * @var $mConn \PHPUnit_Framework_MockObject_MockObject for \Zend_Db_Adapter_Pdo_Abstract
         * @var $mToolbox \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Core\Lib\IToolbox
         * @var $mToolPeriod \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Core\Lib\Tool\Period
         * @var $mCallRepo \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Core\Lib\Service\IRepo
         * @var $mRepoMod \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Accounting\Lib\Repo\IModule
         * @var $mSubCalcSimple \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Accounting\Lib\Service\Balance\Sub\CalcSimple
         * @var $call Call
         */
        extract($this->_prepareMocks());

        /** === Setup Mocks === */
        //  $balanceMaxDate = $this->_subDb->getBalanceMaxDate(...)
        $mRepoMod
            ->expects($this->once())
            ->method('getBalanceMaxDate')
            ->will($this->returnValue(false));
        // $transactionMinDate = $this->_subDb->getTransactionMinDateApplied(...)
        $mRepoMod
            ->expects($this->once())
            ->method('getTransactionMinDateApplied')
            ->will($this->returnValue($TIMESTAMP_TRN_MIN));
        // $period = $this->_toolPeriod->getPeriodCurrent($transactionMinDate);
        $mToolPeriod
            ->expects($this->once())
            ->method('getPeriodCurrent')
            ->will($this->returnValue($DATESTAMP_TRN_MIN));
        // $dayBefore = $this->_toolPeriod->getPeriodPrev($period, Period::TYPE_DAY);
        $mToolPeriod
            ->expects($this->once())
            ->method('getPeriodPrev')
            ->will($this->returnValue($DATESTAMP_LAST));
        /**
         * Prepare request and perform call.
         */
        /** @var  $call Call */
        $call = new Call($mLogger, $mDba, $mToolbox, $mCallRepo, $mRepoMod, $mSubCalcSimple);
        $req = new Request\GetLastDate();
        $req->assetTypeId = $ASSET_TYPE_ID;
        $resp = $call->getLastDate($req);
        $this->assertTrue($resp->isSucceed());
        $this->assertEquals($DATESTAMP_LAST, $resp->getLastDate());
    }

    public function test_reset_fail() {
        /** === Test Data === */
        $DATESTAMP_FROM = '20151123';

        /** === Extract mocks === */
        /**
         * @var $mLogger \PHPUnit_Framework_MockObject_MockObject for \Psr\Log\LoggerInterface
         * @var $mDba \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Core\Lib\Context\IDbAdapter
         * @var $mConn \PHPUnit_Framework_MockObject_MockObject for \Zend_Db_Adapter_Pdo_Abstract
         * @var $mToolbox \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Core\Lib\IToolbox
         * @var $mToolPeriod \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Core\Lib\Tool\Period
         * @var $mCallRepo \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Core\Lib\Service\IRepo
         * @var $mRepoMod \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Accounting\Lib\Repo\IModule
         * @var $mSubCalcSimple \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Accounting\Lib\Service\Balance\Sub\CalcSimple
         * @var $call Call
         */
        extract($this->_prepareMocks());

        /** === Setup Mocks === */

        // $tbl = $this->_getTableName(Balance::ENTITY_NAME);
        $mDba
            ->expects($this->once())
            ->method('getTableName')
            ->willReturn('BALANCE');
        // $where = Balance::ATTR_DATE . '>=' . $this->_conn->quote($request->dateFrom);
        $mConn
            ->expects($this->once())
            ->method('quote')
            ->with($this->equalTo($DATESTAMP_FROM))
            ->will($this->returnValue('WHERE'));
        // $rows = $this->_conn->delete($tbl, $where);
        $mConn
            ->expects($this->once())
            ->method('delete')
            ->with(
                $this->equalTo('BALANCE'),
                $this->equalTo(Balance::ATTR_DATE . '>=WHERE')
            )
            ->will($this->returnValue(false));
        /**
         * Prepare request and perform call.
         */
        /** @var  $call Call */
        $call = new Call($mLogger, $mDba, $mToolbox, $mCallRepo, $mRepoMod, $mSubCalcSimple);
        $req = new Request\Reset();
        $req->setData(Request\Reset::DATE_FROM, $DATESTAMP_FROM);
        $resp = $call->reset($req);
        $this->assertFalse($resp->isSucceed());
    }

    public function test_reset_success() {
        /** === Test Data === */
        $DATESTAMP_FROM = '20151123';

        /** === Extract mocks === */
        /**
         * @var $mLogger \PHPUnit_Framework_MockObject_MockObject for \Psr\Log\LoggerInterface
         * @var $mDba \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Core\Lib\Context\IDbAdapter
         * @var $mConn \PHPUnit_Framework_MockObject_MockObject for \Zend_Db_Adapter_Pdo_Abstract
         * @var $mToolbox \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Core\Lib\IToolbox
         * @var $mToolPeriod \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Core\Lib\Tool\Period
         * @var $mCallRepo \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Core\Lib\Service\IRepo
         * @var $mRepoMod \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Accounting\Lib\Repo\IModule
         * @var $mSubCalcSimple \PHPUnit_Framework_MockObject_MockObject for \Praxigento\Accounting\Lib\Service\Balance\Sub\CalcSimple
         * @var $call Call
         */
        extract($this->_prepareMocks());

        /** === Setup Mocks === */

        // $tbl = $this->_resource->getTableName(Balance::ENTITY_NAME);
        $mDba
            ->expects($this->once())
            ->method('getTableName')
            ->will($this->returnValue('BALANCE'));
        // $where = Balance::ATTR_DATE . '>=' . $this->_conn->quote($request->dateFrom);
        $mConn
            ->expects($this->once())
            ->method('quote')
            ->with($this->equalTo($DATESTAMP_FROM))
            ->will($this->returnValue('WHERE'));
        // $rows = $this->_getConn()->delete($tbl, $where);
        $mConn
            ->expects($this->once())
            ->method('delete')
            ->with(
                $this->equalTo('BALANCE'),
                $this->equalTo(Balance::ATTR_DATE . '>=WHERE')
            )
            ->will($this->returnValue(10));

        /** === Call and asserts  === */
        $req = new Request\Reset();
        $req->setData(Request\Reset::DATE_FROM, $DATESTAMP_FROM);
        $resp = $call->reset($req);
        $this->assertTrue($resp->isSucceed());
        $this->assertTrue($resp->getRowsDeleted() > 0);
    }
}