<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Lib\Service\Transaction;

use Praxigento\Accounting\Lib\Entity\Account;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class Call_UnitTest extends \Praxigento\Core\Lib\Test\BaseTestCase {

    public function test_add_commit() {
        /** === Test Data === */
        $ASSET_TYPE_ID = 3;
        $ACC_ID_DEBIT = 12;
        $ACC_ID_CREDIT = 23;
        /** === Mocks === */
        $mLogger = $this->_mockLogger();
        $mConn = $this->_mockConnection();
        $mDba = $this->_mockDbAdapter(null, $mConn);
        $mToolbox = $this->_mockToolbox();
        $mCallRepo = $this->_mockCallRepo();
        $mCallAccount = $this->_mockFor('Praxigento\Accounting\Lib\Service\Account\Call');

        // $this->_conn->beginTransaction();
        $mConn
            ->expects($this->once())
            ->method('beginTransaction');
        // $respByPk = $this->_callRepo->getEntityByPk($reqByPk);
        $mRespByPk = $this->_mockFor('Praxigento\Core\Lib\Service\Repo\Response\GetEntityByPk');
        $mCallRepo
            ->expects($this->any())
            ->method('getEntityByPk')
            ->will($this->returnValue($mRespByPk));
        // $debitAccId = $respByPk->getData(Account::ATTR_ID);
        $mRespByPk
            ->expects($this->at(0))
            ->method('getData')
            ->with($this->equalTo(Account::ATTR_ID))
            ->will($this->returnValue($ACC_ID_DEBIT));
        // $debitAssetTypeId = $respByPk->getData(Account::ATTR_ASSET_TYPE_ID);
        $mRespByPk
            ->expects($this->at(1))
            ->method('getData')
            ->with($this->equalTo(Account::ATTR_ASSET_TYPE__ID))
            ->will($this->returnValue($ASSET_TYPE_ID));
        // $creditAccId = $respByPk->getData(Account::ATTR_ID);
        $mRespByPk
            ->expects($this->at(2))
            ->method('getData')
            ->with($this->equalTo(Account::ATTR_ID))
            ->will($this->returnValue($ACC_ID_CREDIT));
        // $creditAssetTypeId = $respByPk->getData(Account::ATTR_ASSET_TYPE_ID);
        $mRespByPk
            ->expects($this->at(3))
            ->method('getData')
            ->with($this->equalTo(Account::ATTR_ASSET_TYPE__ID))
            ->will($this->returnValue($ASSET_TYPE_ID));
        // $respAdd = $this->_callRepo->addEntity($reqAdd);
        $mRespAdd = $this->_mockFor('\Praxigento\Core\Lib\Service\Repo\Response\AddEntity');
        $mCallRepo
            ->expects($this->once())
            ->method('addEntity')
            ->will($this->returnValue($mRespAdd));
        // if($respAdd->isSucceed()) {
        $mRespAdd
            ->expects($this->once())
            ->method('isSucceed')
            ->will($this->returnValue(true));
        // $this->_conn->commit();
        $mConn
            ->expects($this->once())
            ->method('commit');
        /**
         * Prepare request and perform call.
         */
        /** @var  $call Call */
        $call = new Call($mLogger, $mDba, $mToolbox, $mCallRepo, $mCallAccount);
        $req = new Request\Add();
        $resp = $call->add($req);
        $this->assertTrue($resp->isSucceed());
    }

    /**
     * @expectedException \Exception
     */
    public function test_add_rollback() {
        /** === Test Data === */
        $ASSET_TYPE_ID_DEBIT = 3;
        $ASSET_TYPE_ID_CREDIT = 4;
        $ACC_ID_DEBIT = 12;
        $ACC_ID_CREDIT = 23;
        /** === Mocks === */
        $mLogger = $this->_mockLogger();
        $mConn = $this->_mockConnection();
        $mDba = $this->_mockDbAdapter(null, $mConn);
        $mToolbox = $this->_mockToolbox();
        $mCallRepo = $this->_mockCallRepo();
        $mCallAccount = $this->_mockFor('Praxigento\Accounting\Lib\Service\Account\Call');

        // $this->_conn->beginTransaction();
        $mConn
            ->expects($this->once())
            ->method('beginTransaction');
        // $respByPk = $this->_callRepo->getEntityByPk($reqByPk);
        $mRespByPk = $this->_mockFor('Praxigento\Core\Lib\Service\Repo\Response\GetEntityByPk');
        $mCallRepo
            ->expects($this->any())
            ->method('getEntityByPk')
            ->will($this->returnValue($mRespByPk));
        // $debitAccId = $respByPk->getData(Account::ATTR_ID);
        $mRespByPk
            ->expects($this->at(0))
            ->method('getData')
            ->with($this->equalTo(Account::ATTR_ID))
            ->will($this->returnValue($ACC_ID_DEBIT));
        // $debitAssetTypeId = $respByPk->getData(Account::ATTR_ASSET_TYPE_ID);
        $mRespByPk
            ->expects($this->at(1))
            ->method('getData')
            ->with($this->equalTo(Account::ATTR_ASSET_TYPE__ID))
            ->will($this->returnValue($ASSET_TYPE_ID_DEBIT));
        // $creditAccId = $respByPk->getData(Account::ATTR_ID);
        $mRespByPk
            ->expects($this->at(2))
            ->method('getData')
            ->with($this->equalTo(Account::ATTR_ID))
            ->will($this->returnValue($ACC_ID_CREDIT));
        // $creditAssetTypeId = $respByPk->getData(Account::ATTR_ASSET_TYPE_ID);
        $mRespByPk
            ->expects($this->at(3))
            ->method('getData')
            ->with($this->equalTo(Account::ATTR_ASSET_TYPE__ID))
            ->will($this->returnValue($ASSET_TYPE_ID_CREDIT));
        // $this->_conn->rollBack();
        $mConn
            ->expects($this->once())
            ->method('rollBack');
        /**
         * Prepare request and perform call.
         */
        /** @var  $call Call */
        $call = new Call($mLogger, $mDba, $mToolbox, $mCallRepo, $mCallAccount);
        $req = new Request\Add();
        $resp = $call->add($req);
        $this->assertFalse($resp->isSucceed());
    }
}