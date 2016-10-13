<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Service\Transaction;

use Praxigento\Accounting\Data\Entity\Account;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

/**
 * @SuppressWarnings(PHPMD.CamelCaseClassName)
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class Call_UnitTest
    extends \Praxigento\Core\Test\BaseCase\Service\Call
{
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
        $this->mManTrans = $this->_mockTransactionManager();
        $this->mRepoAcc = $this->_mock(\Praxigento\Accounting\Repo\Entity\IAccount::class);
        $this->mRepoTrans = $this->_mock(\Praxigento\Accounting\Repo\Entity\ITransaction::class);
        $this->obj = new Call(
            $this->mLogger,
            $this->mManObj,
            $this->mManTrans,
            $this->mRepoAcc,
            $this->mRepoTrans
        );
    }

    public function test_add_commit()
    {
        /** === Test Data === */
        $assetTypeId = 3;
        $accIdDebit = 12;
        $accIdCredit = 23;
        $operId = 543;
        $value = 32.54;
        $date = 'date applied';
        $transId = 654;
        /** === Setup Mocks === */
        // $def = $this->_manTrans->begin();
        $mDef = $this->_mockTransactionDefinition();
        $this->mManTrans
            ->shouldReceive('begin')->once()
            ->andReturn($mDef);
        // $debitAcc = $this->_repoAcc->getById($debitAccId);
        $this->mRepoAcc
            ->shouldReceive('getById')->once()
            ->andReturn(new Account([Account::ATTR_ASSET_TYPE_ID => $assetTypeId]));
        // $creditAcc = $this->_repoAcc->getById($creditAccId);
        $this->mRepoAcc
            ->shouldReceive('getById')->once()
            ->andReturn(new Account([Account::ATTR_ASSET_TYPE_ID => $assetTypeId]));
        // $idCreated = $this->_repoTrans->create($toAdd);
        $this->mRepoTrans
            ->shouldReceive('create')->once()
            ->andReturn($transId);
        // $this->_manTrans->commit($def);
        $this->mManTrans
            ->shouldReceive('commit')->once();
        // $this->_manTrans->end($def);
        $this->mManTrans
            ->shouldReceive('end')->once();
        /** === Call and asserts  === */
        $req = new Request\Add();
        $req->setDebitAccId($accIdDebit);
        $req->setCreditAccId($accIdCredit);
        $req->setOperationId($operId);
        $req->setDateApplied($date);
        $req->setValue($value);
        $resp = $this->obj->add($req);
        $this->assertTrue($resp->isSucceed());
    }

    /**
     * @expectedException \Exception
     */
    public function test_add_rollback()
    {
        /** === Test Data === */
        $assetIdDebit = 3;
        $assetIdCredit = 4;
        $accIdDebit = 12;
        $accIdCredit = 23;
        $operId = 543;
        $value = 32.54;
        $date = 'date applied';
        /** === Mocks === */
        // $def = $this->_manTrans->begin();
        $mDef = $this->_mockTransactionDefinition();
        $this->mManTrans
            ->shouldReceive('begin')->once()
            ->andReturn($mDef);
        // $debitAcc = $this->_repoAcc->getById($debitAccId);
        $this->mRepoAcc
            ->shouldReceive('getById')->once()
            ->andReturn(new Account([Account::ATTR_ASSET_TYPE_ID => $assetIdDebit]));
        // $creditAcc = $this->_repoAcc->getById($creditAccId);
        $this->mRepoAcc
            ->shouldReceive('getById')->once()
            ->andReturn(new Account([Account::ATTR_ASSET_TYPE_ID => $assetIdCredit]));
        /** === Call and asserts  === */
        $req = new Request\Add();
        $req->setDebitAccId($accIdDebit);
        $req->setCreditAccId($accIdCredit);
        $req->setOperationId($operId);
        $req->setDateApplied($date);
        $req->setValue($value);
        $this->obj->add($req);
    }
}