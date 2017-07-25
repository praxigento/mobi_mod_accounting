<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Service\Operation;

use Praxigento\Accounting\Data\Entity\Transaction;

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
    private $mRepoOper;
    /** @var  \Mockery\MockInterface */
    private $mRepoTypeOper;
    /** @var  \Mockery\MockInterface */
    private $mSubAdd;
    /** @var  Call */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        $this->mManTrans = $this->_mockTransactionManager();
        $this->mRepoOper = $this->_mock(\Praxigento\Accounting\Repo\Entity\Def\Operation::class);
        $this->mRepoTypeOper = $this->_mock(\Praxigento\Accounting\Repo\Entity\Type\Def\Operation::class);
        $this->mSubAdd = $this->_mock(Sub\Add::class);
        $this->obj = new Call(
            $this->mLogger,
            $this->mManObj,
            $this->mManTrans,
            $this->mRepoOper,
            $this->mRepoTypeOper,
            $this->mSubAdd
        );
    }

    public function test_add_commit()
    {
        /** === Test Data === */
        $datePerformed = '2015-11-23 12:23:34';
        $operTypeId = 2;
        $trans = [
            [
                Transaction::ATTR_DEBIT_ACC_ID => '12',
                Transaction::ATTR_CREDIT_ACC_ID => '23',
                Transaction::ATTR_VALUE => 12.32,
            ]
        ];
        $operId = 42;
        /** === Setup Mocks === */
        // $def = $this->_manTrans->begin();
        $mDef = $this->_mockTransactionDefinition();
        $this->mManTrans
            ->shouldReceive('begin')->once()
            ->andReturn($mDef);
        // $idCreated = $this->_repoOper->create($bindToAdd);
        $this->mRepoOper
            ->shouldReceive('create')->once()
            ->andReturn($operId);
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
        $req->setDatePerformed($datePerformed);
        $req->setOperationTypeId($operTypeId);
        $req->setTransactions($trans);
        $resp = $this->obj->add($req);
        $this->assertTrue($resp->isSucceed());
        $this->assertEquals($operId, $resp->getOperationId());
    }

    /**
     * @expectedException \Exception
     */
    public function test_add_rollback()
    {
        /** === Test Data === */
        $datePerformed = '2015-11-23 12:23:34';
        $operTypeCode = 'code';
        $trans = [];

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
        $req->setDatePerformed($datePerformed);
        $req->setOperationTypeCode($operTypeCode);
        $req->setTransactions($trans);
        $resp = $this->obj->add($req);
        $this->assertFalse($resp->isSucceed());
    }

}