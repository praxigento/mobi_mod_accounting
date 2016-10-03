<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Service\Balance;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class Call_UnitTest
    extends \Praxigento\Core\Test\BaseCase\Service\Call
{
    /** @var  \Mockery\MockInterface */
    private $mRepoBalance;
    /** @var  \Mockery\MockInterface */
    private $mRepoMod;
    /** @var  \Mockery\MockInterface */
    private $mRepoTypeAsset;
    /** @var  \Mockery\MockInterface */
    private $mSubCalcSimple;
    /** @var  \Mockery\MockInterface */
    private $mToolPeriod;
    /** @var  Call */
    private $obj;
    /** @var array Constructor arguments for object mocking */
    private $objArgs = [];

    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mToolPeriod = $this->_mock(\Praxigento\Core\Tool\IPeriod::class);
        $this->mRepoMod = $this->_mock(\Praxigento\Accounting\Repo\IModule::class);
        $this->mRepoBalance = $this->_mock(\Praxigento\Accounting\Repo\Entity\IBalance::class);
        $this->mRepoTypeAsset = $this->_mock(\Praxigento\Accounting\Repo\Entity\Type\IAsset::class);
        $this->mSubCalcSimple = $this->_mock(Sub\CalcSimple::class);
        /** reset args. to create mock of the tested object */
        $this->objArgs = [
            $this->mLogger,
            $this->mManObj,
            $this->mToolPeriod,
            $this->mRepoMod,
            $this->mRepoBalance,
            $this->mRepoTypeAsset,
            $this->mSubCalcSimple
        ];
        /** create object to test */
        $this->obj = new Call (
            $this->mLogger,
            $this->mManObj,
            $this->mToolPeriod,
            $this->mRepoMod,
            $this->mRepoBalance,
            $this->mRepoTypeAsset,
            $this->mSubCalcSimple
        );
    }

    public function test_calc()
    {
        /** === Test Data === */
        $ASSET_TYPE_ID = 21;
        $DATESTAMP_TO = '20151123';
        $DS = 'datestamp';
        $DS_FROM = 'datestamp from';
        $DS_TO = 'datestamp to';
        $TRANS = 'transactions';
        $UPDATES = 'updates';
        $BALANCES = ['some balances data'];
        /** === Mock object itself === */
        $this->obj = \Mockery::mock(Call::class . '[getLastDate]', $this->objArgs);
        /** === Setup Mocks === */
        // $respLastDate = $this->getLastDate($reqLastDate);
        $this->obj
            ->shouldReceive('getLastDate')->once()
            ->andReturn(new Response\GetLastDate([Response\GetLastDate::LAST_DATE => $DS]));
        // $balances = $this->_repoMod->getBalancesOnDate($assetTypeId, $lastDate);
        $this->mRepoMod
            ->shouldReceive('getBalancesOnDate')->once()
            ->andReturn($BALANCES);
        // $dtFrom = $this->_toolPeriod->getTimestampFrom($lastDate, IPeriod::TYPE_DAY);
        $this->mToolPeriod
            ->shouldReceive('getTimestampFrom')->once()
            ->andReturn($DS_FROM);
        // $dtTo = $this->_toolPeriod->getTimestampTo($dateTo, IPeriod::TYPE_DAY);
        $this->mToolPeriod
            ->shouldReceive('getTimestampTo')->once()
            ->andReturn($DS_TO);
        // $trans = $this->_repoMod->getTransactionsForPeriod($assetTypeId, $dtFrom, $dtTo);
        $this->mRepoMod
            ->shouldReceive('getTransactionsForPeriod')->once()
            ->andReturn($TRANS);
        // $updates = $this->_subCalcSimple->calcBalances($balances, $trans);
        $this->mSubCalcSimple
            ->shouldReceive('calcBalances')->once()
            ->andReturn($UPDATES);
        // $this->_repoMod->updateBalances($updates);
        $this->mRepoMod
            ->shouldReceive('updateBalances')->once();
        /** === Call and asserts  === */
        $req = new Request\Calc();
        $req->setAssetTypeId($ASSET_TYPE_ID);
        $req->setDateTo($DATESTAMP_TO);
        $res = $this->obj->calc($req);
        $this->assertTrue($res->isSucceed());
    }

    public function test_getBalancesOnDate()
    {
        /** === Test Data === */
        $ASSET_TYPE_ID = 21;
        $DATESTAMP = '20151123';
        $ROWS = ['data'];
        /** === Setup Mocks === */
        // $rows = $this->_repoMod->getBalancesOnDate($assetTypeId, $dateOn);
        $this->mRepoMod->
        shouldReceive('getBalancesOnDate')->once()
            ->andReturn($ROWS);
        /** === Call and asserts  === */
        $req = new Request\GetBalancesOnDate();
        $req->setAssetTypeId($ASSET_TYPE_ID);
        $req->setDate($DATESTAMP);
        $res = $this->obj->getBalancesOnDate($req);
        $this->assertTrue($res->isSucceed());
    }

    public function test_getLastDate_withBalanceMaxDate()
    {
        /** === Test Data === */
        $ASSET_TYPE_CODE = 'code';
        $ASSET_TYPE_ID = 21;
        $DATESTAMP_MAX = '20151123';
        $DATESTAMP_LAST = '20151122';
        /** === Setup Mocks === */
        // $assetTypeId = $this->_repoTypeAsset->getIdByCode($assetTypeCode);
        $this->mRepoTypeAsset
            ->shouldReceive('getIdByCode')->once()
            ->andReturn($ASSET_TYPE_ID);
        // $balanceMaxDate = $this->_repoMod->getBalanceMaxDate($assetTypeId);
        $this->mRepoMod
            ->shouldReceive('getBalanceMaxDate')->once()
            ->andReturn($DATESTAMP_MAX);
        // $dayBefore = $this->_toolPeriod->getPeriodPrev($balanceMaxDate, IPeriod::TYPE_DAY);
        $this->mToolPeriod
            ->shouldReceive('getPeriodPrev')->once()
            ->andReturn($DATESTAMP_LAST);
        /** === Call and asserts  === */
        $req = new Request\GetLastDate();
        $req->setAssetTypeCode($ASSET_TYPE_CODE);
        $res = $this->obj->getLastDate($req);
        $this->assertTrue($res->isSucceed());
        $this->assertEquals($DATESTAMP_LAST, $res->getLastDate());
    }

    public function test_getLastDate_withTransactionMinDate()
    {
        /** === Test Data === */
        $ASSET_TYPE_ID = 21;
        $DS_MIN_DATE = '20151123';
        $PERIOD = 'period';
        $DATESTAMP_LAST = '20151122';
        /** === Setup Mocks === */
        // $balanceMaxDate = $this->_repoMod->getBalanceMaxDate($assetTypeId);
        $this->mRepoMod
            ->shouldReceive('getBalanceMaxDate')->once()
            ->andReturn(null);
        // $transactionMinDate = $this->_repoMod->getTransactionMinDateApplied($assetTypeId);
        $this->mRepoMod
            ->shouldReceive('getTransactionMinDateApplied')->once()
            ->andReturn($DS_MIN_DATE);
        // $period = $this->_toolPeriod->getPeriodCurrent($transactionMinDate);
        $this->mToolPeriod
            ->shouldReceive('getPeriodCurrent')->once()
            ->andReturn($PERIOD);
        // $dayBefore = $this->_toolPeriod->getPeriodPrev($period, IPeriod::TYPE_DAY);
        $this->mToolPeriod
            ->shouldReceive('getPeriodPrev')->once()
            ->andReturn($DATESTAMP_LAST);
        /** === Call and asserts  === */
        $req = new Request\GetLastDate();
        $req->setAssetTypeId($ASSET_TYPE_ID);
        $res = $this->obj->getLastDate($req);
        $this->assertTrue($res->isSucceed());
        $this->assertEquals($DATESTAMP_LAST, $res->getLastDate());
    }

    public function test_reset()
    {
        /** === Test Data === */
        $DATESTAMP_FROM = '20151123';
        $ROWS_DELETED = 5;
        /** === Setup Mocks === */
        // $rows = $this->_repoBalance->delete($where);
        $this->mRepoBalance
            ->shouldReceive('delete')->once()
            ->andReturn($ROWS_DELETED);
        /** === Call and asserts  === */
        $req = new Request\Reset();
        $req->setDateFrom($DATESTAMP_FROM);
        $res = $this->obj->reset($req);
        $this->assertTrue($res->isSucceed());
        $this->assertEquals($ROWS_DELETED, $res->getRowsDeleted());
    }

}