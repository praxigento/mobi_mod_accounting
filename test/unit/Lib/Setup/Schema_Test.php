<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Lib\Setup;

use Praxigento\Accounting\Lib\Entity\Account as Account;
use Praxigento\Accounting\Lib\Entity\Balance as Balance;
use Praxigento\Accounting\Lib\Entity\Operation as Operation;
use Praxigento\Accounting\Lib\Entity\Transaction as Transaction;
use Praxigento\Accounting\Lib\Entity\Type\Asset as TypeAsset;
use Praxigento\Accounting\Lib\Entity\Type\Operation as TypeOperation;

include_once(__DIR__ . '/../phpunit_bootstrap.php');

class Schema_UnitTest extends \Praxigento\Core\Lib\Test\BaseTestCase {

    public function test_install() {
        /** === Test Data === */
        /** === Mocks === */
        $mLogger = $this->_mockLogger();
        $mSetupDb = $this->_mockDemSetupDb();

        // $this->setupDb->createEntity($entityAlias, $demEntity);
        $mSetupDb
            ->expects($this->at(0))
            ->method('createEntity')
            ->with($this->equalTo(TypeAsset::ENTITY_NAME), $this->anything());
        $mSetupDb
            ->expects($this->at(1))
            ->method('createEntity')
            ->with($this->equalTo(TypeOperation::ENTITY_NAME), $this->anything());
        $mSetupDb
            ->expects($this->at(2))
            ->method('createEntity')
            ->with($this->equalTo(Account::ENTITY_NAME), $this->anything());
        $mSetupDb
            ->expects($this->at(3))
            ->method('createEntity')
            ->with($this->equalTo(Operation::ENTITY_NAME), $this->anything());
        $mSetupDb
            ->expects($this->at(4))
            ->method('createEntity')
            ->with($this->equalTo(Transaction::ENTITY_NAME), $this->anything());
        $mSetupDb
            ->expects($this->at(5))
            ->method('createEntity')
            ->with($this->equalTo(Balance::ENTITY_NAME), $this->anything());

        /**
         * Prepare request and perform call.
         */
        $obj = new Schema($mLogger, $mSetupDb);
        $obj->setup();
    }
}