<?php
/**
 * Create DB schema.
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Setup;

use Magento\Framework\DB\Ddl\Table as Ddl;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Praxigento\Accounting\Lib\Setup\Schema as ModuleSchema;
use Praxigento\Core\Lib\Context;
use Praxigento\Core\Lib\Setup\Db as Db;

class InstallSchema implements InstallSchemaInterface {

    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        /** save setup object to context */
        Context::get()->setSetupSchema($setup);
        /** start M2 setup*/
        $setup->startSetup();
        /** Module schema installation. */
        $moduleSchema = new  ModuleSchema();
        $moduleSchema->setup();
        /** complete M2 setup*/
        $setup->endSetup();
    }

}