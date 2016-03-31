<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Lib\Service\Operation\Sub;

use Praxigento\Accounting\Lib\Entity\Transaction;
use Praxigento\Accounting\Lib\Service\Transaction\Response\Add as AddResponse;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class Add_UnitTest extends \Praxigento\Core\Lib\Test\BaseMockeryCase {
    /** @var  Add */
    private $obj;
    /** @var  \Mockery\MockInterface */
    private $mCallTransaction;

    protected function setUp() {
        parent::setUp();
        $this->mCallTransaction = $this->_mock(\Praxigento\Accounting\Lib\Service\ITransaction::class);
        $this->obj = new Add(
            $this->mCallTransaction
        );
    }

    public function test_transactions_ref() {
        /** === Test Data === */
        $OPER_ID = 2;
        $DATE_PERFORMED = 'date';
        $AS_REF = 'ref';
        $REF = 16;
        $TRANS = [
            [
                $AS_REF                         => $REF,
                Transaction::ATTR_DEBIT_ACC_ID  => 'debit',
                Transaction::ATTR_CREDIT_ACC_ID => 'credit',
                Transaction::ATTR_VALUE         => 'value'
            ]
        ];
        $TRAN_ID = 4;
        /** === Setup Mocks === */

        // $respTransAdd = $this->_callTransaction->add($reqTransAdd);
        $mResp = new AddResponse();
        $this->mCallTransaction
            ->shouldReceive('add')
            ->andReturn($mResp);
        // if(!$resp->isSucceed()) {
        $mResp->setAsSucceed();
        // $tranId = $resp->getTransactionId();
        $mResp->setTransactionId($TRAN_ID);

        /** === Call and asserts  === */

        $resp = $this->obj->transactions($OPER_ID, $TRANS, $DATE_PERFORMED, $AS_REF);
        $this->assertTrue(is_array($resp));
        $this->assertEquals($REF, $resp[$TRAN_ID]);
    }

    public function test_transactions_noRef() {
        /** === Test Data === */
        $OPER_ID = 2;
        $DATE_PERFORMED = 'date';
        $TRANS = [
            [
                Transaction::ATTR_DEBIT_ACC_ID  => 'debit',
                Transaction::ATTR_CREDIT_ACC_ID => 'credit',
                Transaction::ATTR_VALUE         => 'value'
            ]
        ];
        $TRAN_ID = 4;
        /** === Setup Mocks === */

        // $respTransAdd = $this->_callTransaction->add($reqTransAdd);
        $mResp = new AddResponse();
        $this->mCallTransaction
            ->shouldReceive('add')
            ->andReturn($mResp);
        // if(!$resp->isSucceed()) {
        $mResp->setAsSucceed();
        // $tranId = $resp->getTransactionId();
        $mResp->setTransactionId($TRAN_ID);

        /** === Call and asserts  === */

        $resp = $this->obj->transactions($OPER_ID, $TRANS, $DATE_PERFORMED);
        $this->assertTrue(is_array($resp));
        $this->assertEquals($TRAN_ID, reset($resp));
    }

    /**
     * @expectedException \Exception
     */
    public function test_transactions_exception() {
        /** === Test Data === */
        $OPER_ID = 2;
        $DATE_PERFORMED = 'date';
        $TRANS = [
            [
                Transaction::ATTR_DEBIT_ACC_ID  => 'debit',
                Transaction::ATTR_CREDIT_ACC_ID => 'credit',
                Transaction::ATTR_VALUE         => 'value',
            ]
        ];
        $TRAN_ID = 4;
        /** === Setup Mocks === */

        // $respTransAdd = $this->_callTransaction->add($reqTransAdd);
        $mResp = new AddResponse();
        $this->mCallTransaction
            ->shouldReceive('add')
            ->andReturn($mResp);

        /** === Call and asserts  === */

        $this->obj->transactions($OPER_ID, $TRANS, $DATE_PERFORMED);
    }

}