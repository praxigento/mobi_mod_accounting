<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Setup;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\ObjectManagerInterface;
use Praxigento\Accounting\Lib\Context;

include_once(__DIR__ . '/../phpunit_bootstrap.php');

class InstallSchema_ManualTest extends \Praxigento\Core\Lib\Test\BaseTestCase
{

    public function test_install()
    {
        /** @var  $manObj ObjectManagerInterface */
        $manObj = ObjectManager::getInstance();
        /** @var  $setup \Magento\Framework\Setup\SchemaSetupInterface */
        $setup = $manObj->get(\Magento\Setup\Module\Setup::class);
        /** @var  $context \Magento\Framework\Setup\ModuleContextInterface */
        $context = $manObj->create(\Magento\Setup\Model\ModuleContext::class, ['version' => '123']);
        /** @var $obj \Praxigento\Accounting\Setup\InstallSchema */
        $obj = $manObj->create(\Praxigento\Accounting\Setup\InstallSchema::class);
        $obj->install($setup, $context);

    }
}