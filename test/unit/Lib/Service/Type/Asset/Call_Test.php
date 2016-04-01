<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Lib\Service\Type\Asset;

use Praxigento\Accounting\Data\Entity\Type\Asset as TypeAsset;

include_once(__DIR__ . '/../../../../phpunit_bootstrap.php');

class Call_UnitTest extends \Praxigento\Core\Lib\Test\BaseMockeryCase
{

    /** @var  \Mockery\MockInterface */
    private $mLogger;
    /** @var  Call */
    private $call;

    protected function setUp()
    {
        parent::setUp();
        $this->mLogger = $this->_mockLogger();
        $this->call = new Call($this->mLogger);
    }

    public function test_getByCode()
    {
        /** === Test Data === */
        $ASSET_TYPE_ID = '21';
        $ASSET_CODE = 'CODE';
        $ASSET_NOTE = 'NOTE';
        $TABLE = 'ASSET_TABLE';
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
        // $data = $this->_conn->fetchRow($query, [ 'code' => $assetCode ]);
        $mData = [
            TypeAsset::ATTR_ID => $ASSET_TYPE_ID,
            TypeAsset::ATTR_CODE => $ASSET_CODE,
            TypeAsset::ATTR_NOTE => $ASSET_NOTE
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
        $req = new Request\GetByCode($ASSET_CODE);
        $resp = $call->getByCode($req);
        $this->assertTrue($resp->isSucceed());
    }

}