<?php
/**
 * Setup schema (create tables in DB).
 *
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Lib\Setup;

use Praxigento\Accounting\Lib\Entity\Account as Account;
use Praxigento\Accounting\Lib\Entity\Balance as Balance;
use Praxigento\Accounting\Lib\Entity\Operation as Operation;
use Praxigento\Accounting\Lib\Entity\Transaction as Transaction;
use Praxigento\Accounting\Lib\Entity\Type\Asset as TypeAsset;
use Praxigento\Accounting\Lib\Entity\Type\Operation as TypeOperation;
use Praxigento\Core\Lib\Setup\Db as Db;

class Schema extends \Praxigento\Core\Lib\Setup\Schema\Base {

    public function setup() {
        /**
         * Read and parse JSON schema.
         */
        $pathToFile = __DIR__ . '/../etc/dem.json';
        $pathToNode = '/dBEAR/package/Praxigento/package/Accounting';
        $demPackage = $this->_readDemPackage($pathToFile, $pathToNode);

        /* Type Asset */
        $entityAlias = TypeAsset::ENTITY_NAME;
        $demEntity = $demPackage['package']['Type']['entity']['Asset'];
        $this->_demDb->createEntity($entityAlias, $demEntity);

        /* Type Operation */
        $entityAlias = TypeOperation::ENTITY_NAME;
        $demEntity = $demPackage['package']['Type']['entity']['Operation'];
        $this->_demDb->createEntity($entityAlias, $demEntity);

        /* Account */
        $entityAlias = Account::ENTITY_NAME;
        $demEntity = $demPackage['entity']['Account'];
        $this->_demDb->createEntity($entityAlias, $demEntity);

        /* Operation */
        $entityAlias = Operation::ENTITY_NAME;
        $demEntity = $demPackage['entity']['Operation'];
        $this->_demDb->createEntity($entityAlias, $demEntity);

        /* Transaction */
        $entityAlias = Transaction::ENTITY_NAME;
        $demEntity = $demPackage['entity']['Transaction'];
        $this->_demDb->createEntity($entityAlias, $demEntity);

        /* Balance */
        $entityAlias = Balance::ENTITY_NAME;
        $demEntity = $demPackage['entity']['Balance'];
        $this->_demDb->createEntity($entityAlias, $demEntity);
    }
}