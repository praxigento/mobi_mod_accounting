<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Lib\Service\Type\Operation;

use Praxigento\Accounting\Lib\Entity\Type\Operation as TypeOperation;

include_once(__DIR__ . '/../../../../phpunit_bootstrap.php');

class Call_UnitTest extends \Praxigento\Core\Lib\Test\BaseTestCase {

    public function test_getByCode() {
        /** === Test Data === */
        $OPERATION_ID = '21';
        $OPERATION_CODE = 'CODE';
        $OPERATION_NOTE = 'NOTE';
        $TABLE = 'OPERATION_TABLE';
        /** === Mocks === */
        $mLogger = $this->_mockLogger();
        $mConn = $this->_mockConnection();
        $mDba = $this->_mockDbAdapter(null, $mConn);
        $mToolbox = $this->_mockToolbox();
        $mCallRepo = $this->_mockCallRepo();

        // $tbl = $this->_resource->getTableName(Asset::ENTITY_NAME);
        $mDba
            ->expects($this->once())
            ->method('getTableName')
            ->will($this->returnValue($TABLE));
        // $query = $this->_conn->select();
        $mQuery = $this->_mockDbSelect();
        $mConn
            ->expects($this->once())
            ->method('select')
            ->will($this->returnValue($mQuery));
        $mQuery
            ->expects($this->once())
            ->method('from')
            ->with($this->equalTo($TABLE));
        // $data = $this->_conn->fetchRow($query, [ 'code' => $operationCode ]);
        $mData = [
            TypeOperation::ATTR_ID   => $OPERATION_ID,
            TypeOperation::ATTR_CODE => $OPERATION_CODE,
            TypeOperation::ATTR_NOTE => $OPERATION_NOTE
        ];
        $mConn
            ->expects($this->once())
            ->method('fetchRow')
            ->will($this->returnValue($mData));
        /**
         * Prepare request and perform call.
         */
        /** @var  $call Call */
        $call = new Call($mLogger, $mDba, $mToolbox, $mCallRepo);
        $req = new Request\GetByCode($OPERATION_CODE);
        $resp = $call->getByCode($req);
        $this->assertTrue($resp->isSucceed());
    }

}