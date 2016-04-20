<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Lib\Setup;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class Data_UnitTest extends \Praxigento\Core\Lib\Test\BaseMockeryCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->markTestSkipped('Test is deprecated after M1 & M2 merge is done.');
    }

    public function test_install()
    {
        /** === Test Data === */
        /** === Mocks === */
        $mConn = $this->_mockConnection();
        $mDba = $this->_mockDbAdapter(null, $mConn);
        /**
         * Prepare request and perform call.
         */
        $obj = new Data($mDba);
        $obj->install();
    }
}