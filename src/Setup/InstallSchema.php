<?php
/**
 * Create DB schema.
 *
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Setup;

use Praxigento\Accounting\Repo\Entity\Data\Account as Account;
use Praxigento\Accounting\Repo\Entity\Data\Balance as Balance;
use Praxigento\Accounting\Repo\Entity\Data\Log\Change\Admin as LogChangeAdmin;
use Praxigento\Accounting\Repo\Entity\Data\Log\Change\Customer as LogChangeCustomer;
use Praxigento\Accounting\Repo\Entity\Data\Operation as Operation;
use Praxigento\Accounting\Repo\Entity\Data\Transaction as Transaction;
use Praxigento\Accounting\Repo\Entity\Data\Type\Asset as TypeAsset;
use Praxigento\Accounting\Repo\Entity\Data\Type\Operation as TypeOperation;

/**
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class InstallSchema
    extends \Praxigento\Core\Setup\Schema\Base
{

    protected function _setup()
    {
        /** Read and parse JSON schema. */
        $pathToFile = __DIR__ . '/../etc/dem.json';
        $pathToNode = '/dBEAR/package/Praxigento/package/Accounting';
        $demPackage = $this->_toolDem->readDemPackage($pathToFile, $pathToNode);

        /* Type / Asset */
        $entityAlias = TypeAsset::ENTITY_NAME;
        $demEntity = $demPackage->get('package/Type/entity/Asset');
        $this->_toolDem->createEntity($entityAlias, $demEntity);

        /* Type / Operation */
        $entityAlias = TypeOperation::ENTITY_NAME;
        $demEntity = $demPackage->get('package/Type/entity/Operation');
        $this->_toolDem->createEntity($entityAlias, $demEntity);

        /* Account */
        $entityAlias = Account::ENTITY_NAME;
        $demEntity = $demPackage->get('entity/Account');
        $this->_toolDem->createEntity($entityAlias, $demEntity);

        /* Operation */
        $entityAlias = Operation::ENTITY_NAME;
        $demEntity = $demPackage->get('entity/Operation');
        $this->_toolDem->createEntity($entityAlias, $demEntity);

        /* Transaction */
        $entityAlias = Transaction::ENTITY_NAME;
        $demEntity = $demPackage->get('entity/Transaction');
        $this->_toolDem->createEntity($entityAlias, $demEntity);

        /* Balance */
        $entityAlias = Balance::ENTITY_NAME;
        $demEntity = $demPackage->get('entity/Balance');
        $this->_toolDem->createEntity($entityAlias, $demEntity);

        /* Log / Change / Admin  */
        $entityAlias = LogChangeAdmin::ENTITY_NAME;
        $demEntity = $demPackage->get('package/Log/package/Change/entity/Admin');
        $this->_toolDem->createEntity($entityAlias, $demEntity);

        /* Log / Change / Customer */
        $entityAlias = LogChangeCustomer::ENTITY_NAME;
        $demEntity = $demPackage->get('package/Log/package/Change/entity/Customer');
        $this->_toolDem->createEntity($entityAlias, $demEntity);


    }
}