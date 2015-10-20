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
use Praxigento\Accounting\Lib\Entity\Account as Account;
use Praxigento\Accounting\Lib\Entity\Type\Asset as TypeAsset;
use Praxigento\Accounting\Lib\Entity\Type\Operation as TypeOperation;
use Praxigento\Accounting\Lib\Entity\Type\Period as TypePeriod;
use Praxigento\Core\Lib\Context;
use Praxigento\Core\Lib\Setup\Db as Db;

class InstallSchema implements InstallSchemaInterface {

    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;
        $installer->startSetup();
        Context::get()->setSchemaSetup($setup);

        /**
         * DEM processor.
         */
        /* TODO: add DEM processing */
        $json = file_get_contents(BP . '/../vendor/praxigento/mobi_mod_common_accounting/src/etc/dem.json');
        $decoded = json_decode($json, true);
        $demPackage = $decoded['dBEAR']['package']['Praxigento']['package']['Accounting'];
        $setup = new Db();

        /** =============================================================================================
         * Create Tables.
         * ============================================================================================= */

        /* Type Asset */
        $demEntity = $demPackage['package']['Type']['entity']['Asset'];
        $entityAlias = TypeAsset::NAME;
        $setup->createEntity($entityAlias, $demEntity);

        /* Type Operation */
        $demEntity = $demPackage['package']['Type']['entity']['Operation'];
        $entityAlias = TypeOperation::NAME;
        $setup->createEntity($entityAlias, $demEntity);

        /* Type Period  */
        $demEntity = $demPackage['package']['Type']['entity']['Period'];
        $entityAlias = TypePeriod::NAME;
        $setup->createEntity($entityAlias, $demEntity);

        /* Account */
        $demEntity = $demPackage['entity']['Account'];
        $entityAlias = Account::NAME;
        $setup->createEntity($entityAlias, $demEntity);

        $installer->endSetup();
    }

}