<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Lib\Service\Operation;

use Praxigento\Accounting\Data\Entity\Transaction;
use Praxigento\Core\Lib\Service\Repo\Response\AddEntity as RepoAddEntityResponse;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class Call_UnitTest extends \Praxigento\Core\Test\BaseMockeryCase
{
    /** @var  Call */
    private $call;
    /** @var  \Mockery\MockInterface */
    private $mCallTypeOperation;
    /** @var  \Mockery\MockInterface */
    private $mConn;
    /** @var  \Mockery\MockInterface */
    private $mLogger;
    /** @var  \Mockery\MockInterface */
    private $mManTrans;
    /** @var  \Mockery\MockInterface */
    private $mRepoMod;
    /** @var  \Mockery\MockInterface */
    private $mSubAdd;

    protected function setUp()
    {
        parent::setUp();
        $this->markTestSkipped('Test is deprecated after M1 & M2 merge is done.');
        $this->mLogger = $this->_mock(\Psr\Log\LoggerInterface::class);
        $this->mManTrans = $this->_mockTransactionManager();
        $this->mCallTypeOperation = $this->_mock(\Praxigento\Accounting\Lib\Service\Type\Operation\Call::class);
        $this->mRepoMod = $this->_mock(\Praxigento\Accounting\Lib\Repo\IModule ::class);
        $this->mSubAdd = $this->_mock(\Praxigento\Accounting\Lib\Service\Operation\Sub\Add::class);
        $this->call = new Call(
            $this->mLogger,
            $this->mManTrans,
            $this->mCallTypeOperation,
            $this->mRepoMod,
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

        // $conn->beginTransaction();
        $this->mConn
            ->shouldReceive('beginTransaction');
        // $respAdd = $this->_callRepo->addEntity($reqAdd);
        $mAddEntityResp = new RepoAddEntityResponse();
        $this->mCallRepo
            ->shouldReceive('addEntity')
            ->andReturn($mAddEntityResp);
        // if($respAdd->isSucceed()) {
        $mAddEntityResp->markSucceed();
        $mAddEntityResp->setData(RepoAddEntityResponse::ID_INSERTED, $OPERATION_ID);
        // $transIds = $this->_subAdd->transactions($operId, $transactions, $datePerformed, $asRef);
        $this->mSubAdd
            ->shouldReceive('transactions')
            ->andReturn([]);
        // $conn->commit();
        $this->mConn
            ->shouldReceive('commit');

        /** === Call and asserts  === */
        $req = new Request\Add();
        $req->setDatePerformed($DATE_PERFORMED);
        $req->setOperationTypeId($OPER_TYPE_ID);
        $req->setTransactions($TRANSACTIONS);
        $resp = $this->call->add($req);
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
        $OPER_TYPE_ID = 4;
        $OPER_TYPE_CODE = 'code';
        $TRANSACTIONS = [
            [
                Transaction::ATTR_DEBIT_ACC_ID => '12',
                Transaction::ATTR_CREDIT_ACC_ID => '23',
                Transaction::ATTR_VALUE => 12.32,
            ]
        ];

        /** === Setup Mocks === */

        // $conn->beginTransaction();
        $this->mConn
            ->shouldReceive('beginTransaction');
        // $operationTypeId = $this->_repoMod->getTypeOperationIdByCode($operationTypeCode);
        $this->mRepoMod
            ->shouldReceive('getTypeOperationIdByCode')
            ->andReturn($OPER_TYPE_ID);
        // $respAdd = $this->_callRepo->addEntity($reqAdd);
        $this->mCallRepo
            ->shouldReceive('addEntity')
            ->andThrow(new \Exception('From Mockery FW'));
        // $conn->rollback();
        $this->mConn
            ->shouldReceive('rollBack');

        /** === Call and asserts  === */
        $req = new Request\Add();
        $req->setDatePerformed($DATE_PERFORMED);
        $req->setOperationTypeCode($OPER_TYPE_CODE);
        $req->setTransactions($TRANSACTIONS);
        $resp = $this->call->add($req);
        $this->assertFalse($resp->isSucceed());
    }

}