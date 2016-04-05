<?php
/**
 * Create DB schema.
 *
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Setup;

use Praxigento\Accounting\Data\Entity\Account as Account;
use Praxigento\Accounting\Data\Entity\Balance as Balance;
use Praxigento\Accounting\Data\Entity\Operation as Operation;
use Praxigento\Accounting\Data\Entity\Transaction as Transaction;
use Praxigento\Accounting\Data\Entity\Type\Asset as TypeAsset;
use Praxigento\Accounting\Data\Entity\Type\Operation as TypeOperation;
use Praxigento\Core\Lib\Setup\Db as Db;

class InstallSchema extends \Praxigento\Core\Setup\Schema\Base
{

    protected function _setup()
    {
        /** Read and parse JSON schema. */
        $pathToFile = __DIR__ . '/../etc/dem.json';
        $pathToNode = '/dBEAR/package/Praxigento/package/Accounting';
        $demPackage = $this->_toolDem->readDemPackage($pathToFile, $pathToNode);

        /* Type Asset */
        $entityAlias = TypeAsset::ENTITY_NAME;
        $demEntity = $demPackage->getData('package/Type/entity/Asset');
        $this->_toolDem->createEntity($entityAlias, $demEntity);

        /* Type Operation */
        $entityAlias = TypeOperation::ENTITY_NAME;
        $demEntity = $demPackage->getData('package/Type/entity/Operation');
        $this->_toolDem->createEntity($entityAlias, $demEntity);

        /* Account */
        $entityAlias = Account::ENTITY_NAME;
        $demEntity = $demPackage->getData('entity/Account');
        $this->_toolDem->createEntity($entityAlias, $demEntity);

        /* Operation */
        $entityAlias = Operation::ENTITY_NAME;
        $demEntity = $demPackage->getData('entity/Operation');
        $this->_toolDem->createEntity($entityAlias, $demEntity);

        /* Transaction */
        $entityAlias = Transaction::ENTITY_NAME;
        $demEntity = $demPackage->getData('entity/Transaction');
        $this->_toolDem->createEntity($entityAlias, $demEntity);

        /* Balance */
        $entityAlias = Balance::ENTITY_NAME;
        $demEntity = $demPackage->getData('entity/Balance');
        $this->_toolDem->createEntity($entityAlias, $demEntity);
    }
}