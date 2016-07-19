<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Service\Operation;

use Praxigento\Accounting\Data\Entity\Transaction;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class Call_UnitTest extends \Praxigento\Core\Test\BaseMockeryCase
{
    /** @var  Call */
    private $obj;
    /** @var  \Mockery\MockInterface */
    private $mConn;
    /** @var  \Mockery\MockInterface */
    private $mLogger;
    /** @var  \Mockery\MockInterface */
    private $mManTrans;
    /** @var  \Mockery\MockInterface */
    private $mRepoOper;
    /** @var  \Mockery\MockInterface */
    private $mRepoTypeOper;
    /** @var  \Mockery\MockInterface */
    private $mSubAdd;

    protected function setUp()
    {
        parent::setUp();
        $this->mLogger = $this->_mockLogger();
        $this->mManTrans = $this->_mockTransactionManager();
        $this->mRepoOper = $this->_mock(\Praxigento\Accounting\Repo\Entity\IOperation::class);
        $this->mRepoTypeOper = $this->_mock(\Praxigento\Accounting\Repo\Entity\Type\IOperation::class);
        $this->mSubAdd = $this->_mock(Sub\Add::class);
        $this->obj = new Call(
            $this->mLogger,
            $this->mManTrans,
            $this->mRepoOper,
            $this->mRepoTypeOper,
            $this->mSubAdd
        );
    }

    public function test_add_commit()
    {
        /** === Test Data === */
        $DATE_PERFORMED = '2015-11-23 12:23:34';
        $OPER_TYPE_ID = 2;
        $TRANSACTIONS = [
            [
                Transaction::ATTR_DEBIT_ACC_ID => '12',
                Transaction::ATTR_CREDIT_ACC_ID => '23',
                Transaction::ATTR_VALUE => 12.32,
            ]
        ];
        $OPERATION_ID = 42;
        /** === Setup Mocks === */
        // $def = $this->_manTrans->begin();
        $mDef = $this->_mockTransactionDefinition();
        $this->mManTrans
            ->shouldReceive('begin')->once()
            ->andReturn($mDef);
        // $idCreated = $this->_repoOper->create($bindToAdd);
        $this->mRepoOper
            ->shouldReceive('create')->once()
            ->andReturn($OPERATION_ID);
        // $transIds = $this->_subAdd->transactions($idCreated, $transactions, $datePerformed, $asRef);
        $this->mSubAdd
            ->shouldReceive('transactions')->once()
            ->andReturn([]);
        // $this->_manTrans->commit($def);
        $this->mManTrans
            ->shouldReceive('commit')->once();
        // $this->_manTrans->end($def);
        $this->mManTrans
            ->shouldReceive('end')->once();
        /** === Call and asserts  === */
        $req = new Request\Add();
        $req->setDatePerformed($DATE_PERFORMED);
        $req->setOperationTypeId($OPER_TYPE_ID);
        $req->setTransactions($TRANSACTIONS);
        $resp = $this->obj->add($req);
        $this->assertTrue($resp->isSucceed());
        $this->assertEquals($OPERATION_ID, $resp->getOperationId());
    }

    /**
     * @expectedException \Exception
     */
    public function test_add_rollback()
    {
        /** === Test Data === */
        $DATE_PERFORMED = '2015-11-23 12:23:34';
        $OPER_TYPE_CODE = 'code';
        $TRANSACTIONS = [];

        /** === Setup Mocks === */
        // $def = $this->_manTrans->begin();
        $mDef = $this->_mockTransactionDefinition();
        $this->mManTrans
            ->shouldReceive('begin')->once()
            ->andReturn($mDef);
        // $operationTypeId = $this->_repoTypeOper->getIdByCode($operationTypeCode);
        $this->mRepoTypeOper
            ->shouldReceive('getIdByCode')->once()
            ->andThrow(\Exception::class);
        // $this->_manTrans->end($def);
        $this->mManTrans
            ->shouldReceive('end')->once();
        /** === Call and asserts  === */
        $req = new Request\Add();
        $req->setDatePerformed($DATE_PERFORMED);
        $req->setOperationTypeCode($OPER_TYPE_CODE);
        $req->setTransactions($TRANSACTIONS);
        $resp = $this->obj->add($req);
        $this->assertFalse($resp->isSucceed());
    }

}