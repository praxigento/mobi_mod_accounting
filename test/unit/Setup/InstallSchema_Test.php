<?php
/**
 * Empty class to get stub for tests
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Setup;

use Praxigento\Core\Lib\Context;

include_once(__DIR__ . '/../phpunit_bootstrap.php');

class InstallSchema_UnitTest extends \Praxigento\Core\Lib\Test\BaseTestCase {

    public function test_constructor() {
        $obj = new InstallSchema();
        $this->assertInstanceOf('Praxigento\Accounting\Setup\InstallSchema', $obj);
    }

    public function test_install() {
        /** === Test Data === */
        /** === Mocks === */
        $mockCtx = $this
            ->getMockBuilder('Praxigento\Core\Lib\Context')
            ->disableOriginalConstructor()
            ->getMock();
        //  $obm = Context::instance()->getObjectManager();
        $mockObm = $this
            ->getMockBuilder('Praxigento\Core\Lib\Context\IObjectManager')
            ->disableOriginalConstructor()
            ->getMock();
        $mockCtx
            ->expects($this->any())
            ->method('getObjectManager')
            ->willReturn($mockObm);
        // $setupDb = $obm->get('Praxigento\Core\Lib\Setup\Db');
        $mockSetupDb = $this
            ->getMockBuilder('Praxigento\Core\Lib\Setup\Db')
            ->disableOriginalConstructor()
            ->getMock();
        $mockObm
            ->expects($this->once())
            ->method('get')
            ->with('Praxigento\Core\Lib\Setup\Db')
            ->willReturn($mockSetupDb);
        // parameters for install(...)
        $mockSetup = $this
            ->getMockBuilder('Magento\Framework\Setup\SchemaSetupInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $mockMageCtx = $this
            ->getMockBuilder('Magento\Framework\Setup\ModuleContextInterface')
            ->disableOriginalConstructor()
            ->getMock();
        Context::set($mockCtx);
        /** === Test itself === */
        $obj = new InstallSchema();
        $obj->install($mockSetup, $mockMageCtx);
        $this->assertInstanceOf('Praxigento\Accounting\Setup\InstallSchema', $obj);
    }
}