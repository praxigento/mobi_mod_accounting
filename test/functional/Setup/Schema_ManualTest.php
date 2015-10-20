<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Setup;

use Praxigento\Core\Lib\Context;

include_once(__DIR__ . '/../phpunit_bootstrap.php');

class Schema_ManualTest extends \PHPUnit_Framework_TestCase {

    function test_install() {
        $obm = Context::get()->getObjectManager();
        $setup = $obm->get('\Magento\Setup\Module\Setup');
        $context = $obm->create('\Magento\Setup\Model\ModuleContext', [ 'version' => '' ]);
        $obj = $obm->get('\Praxigento\Accounting\Setup\Schema');
        $obj->install($setup, $context);
    }
}