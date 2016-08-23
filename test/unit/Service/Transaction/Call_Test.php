<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Service\Transaction;

use Praxigento\Accounting\Data\Entity\Account;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class Call_UnitTest extends \Praxigento\Core\Test\BaseCase\Mockery
{
    /** @var  \Mockery\MockInterface */
    private $mCallAcc;
    /** @var  \Mockery\MockInterface */
    private $mConn;
    /** @var  \Mockery\MockInterface */
    private $mLogger;
    /** @var  \Mockery\MockInterface */
    private $mManTrans;
    /** @var  \Mockery\MockInterface */
    private $mRepoAcc;
    /** @var  \Mockery\MockInterface */
    private $mRepoTrans;
    /** @var  Call */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        $this->mLogger = $this->_mockLogger();
        $this->mManTrans = $this->_mockTransactionManager();
        $this->mRepoAcc = $this->_mock(\Praxigento\Accounting\Repo\Entity\IAccount::class);
        $this->mRepoTrans = $this->_mock(\Praxigento\Accounting\Repo\Entity\ITransaction::class);
        $this->obj = new Call(
            $this->mLogger,
            $this->mManTrans,
            $this->mRepoAcc,
            $this->mRepoTrans
        );
    }

    public function test_add_commit()
    {
        /** === Test Data === */
        $ASSET_TYPE_ID = 3;
        $ACC_ID_DEBIT = 12;
        $ACC_ID_CREDIT = 23;
        $OPERATION_ID = 543;
        $VALUE = 32.54;
        $DATE = 'date applied';
        $TRANS_ID = 654;
        /** === Setup Mocks === */
        // $def = $this->_manTrans->begin();
        $mDef = $this->_mockTransactionDefinition();
        $this->mManTrans
            ->shouldReceive('begin')->once()
            ->andReturn($mDef);
        // $debitAcc = $this->_repoAcc->getById($debitAccId);
        $this->mRepoAcc
            ->shouldReceive('getById')->once()
            ->andReturn(new Account([Account::ATTR_ASSET_TYPE_ID => $ASSET_TYPE_ID]));
        // $creditAcc = $this->_repoAcc->getById($creditAccId);
        $this->mRepoAcc
            ->shouldReceive('getById')->once()
            ->andReturn(new Account([Account::ATTR_ASSET_TYPE_ID => $ASSET_TYPE_ID]));
        // $idCreated = $this->_repoTrans->create($toAdd);
        $this->mRepoTrans
            ->shouldReceive('create')->once()
            ->andReturn($TRANS_ID);
        // $this->_repoAcc->updateBalance($debitAccId, 0 - $value);
        $this->mRepoAcc
            ->shouldReceive('updateBalance')->once();
        // $this->_repoAcc->updateBalance($creditAccId, 0 + $value);
        $this->mRepoAcc
            ->shouldReceive('updateBalance')->once();
        // $this->_manTrans->commit($def);
        $this->mManTrans
            ->shouldReceive('commit')->once();
        // $this->_manTrans->end($def);
        $this->mManTrans
            ->shouldReceive('end')->once();
        /** === Call and asserts  === */
        $req = new Request\Add();
        $req->setDebitAccId($ACC_ID_DEBIT);
        $req->setCreditAccId($ACC_ID_CREDIT);
        $req->setOperationId($OPERATION_ID);
        $req->setDateApplied($DATE);
        $req->setValue($VALUE);
        $resp = $this->obj->add($req);
        $this->assertTrue($resp->isSucceed());
    }

    /**
     * @expectedException \Exception
     */
    public function test_add_rollback()
    {
        /** === Test Data === */
        $ASSET_TYPE_ID_DEBIT = 3;
        $ASSET_TYPE_ID_CREDIT = 4;
        $ACC_ID_DEBIT = 12;
        $ACC_ID_CREDIT = 23;
        $OPERATION_ID = 543;
        $VALUE = 32.54;
        $DATE = 'date applied';
        $TRANS_ID = 654;
        /** === Mocks === */
        // $def = $this->_manTrans->begin();
        $mDef = $this->_mockTransactionDefinition();
        $this->mManTrans
            ->shouldReceive('begin')->once()
            ->andReturn($mDef);
        // $debitAcc = $this->_repoAcc->getById($debitAccId);
        $this->mRepoAcc
            ->shouldReceive('getById')->once()
            ->andReturn(new Account([Account::ATTR_ASSET_TYPE_ID => $ASSET_TYPE_ID_DEBIT]));
        // $creditAcc = $this->_repoAcc->getById($creditAccId);
        $this->mRepoAcc
            ->shouldReceive('getById')->once()
            ->andReturn(new Account([Account::ATTR_ASSET_TYPE_ID => $ASSET_TYPE_ID_CREDIT]));
        /** === Call and asserts  === */
        $req = new Request\Add();
        $req->setDebitAccId($ACC_ID_DEBIT);
        $req->setCreditAccId($ACC_ID_CREDIT);
        $req->setOperationId($OPERATION_ID);
        $req->setDateApplied($DATE);
        $req->setValue($VALUE);
        $resp = $this->obj->add($req);
    }
}