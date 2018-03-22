<?php
/**
 * Create DB schema.
 *
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Setup;

use Praxigento\Accounting\Repo\Data\Account as Account;
use Praxigento\Accounting\Repo\Data\Balance as Balance;
use Praxigento\Accounting\Repo\Data\Log\Change\Admin as LogChangeAdmin;
use Praxigento\Accounting\Repo\Data\Log\Change\Customer as LogChangeCustomer;
use Praxigento\Accounting\Repo\Data\Operation as Operation;
use Praxigento\Accounting\Repo\Data\Transaction as Transaction;
use Praxigento\Accounting\Repo\Data\Type\Asset as TypeAsset;
use Praxigento\Accounting\Repo\Data\Type\Operation as TypeOperation;

/**
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class InstallSchema
    extends \Praxigento\Core\App\Setup\Schema\Base
{

    protected function setup()
    {
        /** Read and parse JSON schema. */
        $pathToFile = __DIR__ . '/../etc/dem.json';
        $pathToNode = '/dBEAR/package/Praxigento/package/Accounting';
        $demPackage = $this->toolDem->readDemPackage($pathToFile, $pathToNode);

        /* Type / Asset */
        $demEntity = $demPackage->get('package/Type/entity/Asset');
        $this->toolDem->createEntity(TypeAsset::ENTITY_NAME, $demEntity);

        /* Type / Operation */
        $demEntity = $demPackage->get('package/Type/entity/Operation');
        $this->toolDem->createEntity(TypeOperation::ENTITY_NAME, $demEntity);

        /* Account */
        $demEntity = $demPackage->get('entity/Account');
        $this->toolDem->createEntity(Account::ENTITY_NAME, $demEntity);

        /* Operation */
        $demEntity = $demPackage->get('entity/Operation');
        $this->toolDem->createEntity(Operation::ENTITY_NAME, $demEntity);

        /* Transaction */
        $demEntity = $demPackage->get('entity/Transaction');
        $this->toolDem->createEntity(Transaction::ENTITY_NAME, $demEntity);

        /* Balance */
        $demEntity = $demPackage->get('entity/Balance');
        $this->toolDem->createEntity(Balance::ENTITY_NAME, $demEntity);

        /* Log / Change / Admin  */
        $demEntity = $demPackage->get('package/Log/package/Change/entity/Admin');
        $this->toolDem->createEntity(LogChangeAdmin::ENTITY_NAME, $demEntity);

        /* Log / Change / Customer */
        $demEntity = $demPackage->get('package/Log/package/Change/entity/Customer');
        $this->toolDem->createEntity(LogChangeCustomer::ENTITY_NAME, $demEntity);


    }
}