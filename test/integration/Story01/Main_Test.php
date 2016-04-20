<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Lib\Test\Story01;

use Praxigento\Accounting\Data\Entity\Account;
use Praxigento\Accounting\Data\Entity\Balance;
use Praxigento\Accounting\Data\Entity\Transaction;
use Praxigento\Accounting\Data\Entity\Type\Asset as TypeAsset;
use Praxigento\Accounting\Data\Entity\Type\Operation as TypeOperation;
use Praxigento\Accounting\Lib\Service\Account\Request\Get as AccountGetRequest;
use Praxigento\Accounting\Lib\Service\Balance\Request\Calc as BalanceCalcRequest;
use Praxigento\Accounting\Lib\Service\Operation\Request\Add as OperationAddRequest;
use Praxigento\Accounting\Lib\Service\Operation\Response\Add as OperationAddResponse;
use Praxigento\Core\Lib\Test\BaseIntegrationTest;

include_once(__DIR__ . '/../phpunit_bootstrap.php');

class Main_IntegrationTest extends BaseIntegrationTest
{
    const ATTR_CUST_EMAIL = 'email';
    const ATTR_CUST_ID = 'entity_id';
    const DATA_DATE_BALANCE_CHECK = '20151111';
    const DATA_DATE_BALANCE_UP_TO = '20151112';
    const DATA_DATE_PERFORMED = '2015-11-10 18:43:57';
    const DATA_TYPE_ASSET_CODE = 'test_asset';
    const DATA_TYPE_OPER_CODE = 'test_oper';
    const ENTITY_CUSTOMER = 'customer_entity';
    /**
     * Total amount of the transactions in operation (to check accounts balances).
     * @var decimal
     */
    private $_amount = 0;
    /** @var  \Praxigento\Accounting\Lib\Service\Account\Call */
    private $_callAccount;
    /** @var  \Praxigento\Accounting\Lib\Service\Balance\Call */
    private $_callBalance;
    /** @var  \Praxigento\Accounting\Lib\Service\Operation\Call */
    private $_callOperation;
    /** @var \Praxigento\Core\Repo\IGeneric */
    private $_repoBasic;
    private $acc1 = [];
    private $acc2 = [];
    private $cust1 = [];
    private $cust2 = [];
    private $typeAssetId;
    private $typeOperId;

    /**
     * Main_FunctionalTest constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->_callAccount = $this->_manObj->get(\Praxigento\Accounting\Lib\Service\IAccount::class);
        $this->_callBalance = $this->_manObj->get(\Praxigento\Accounting\Lib\Service\Balance\Call::class);
        $this->_callOperation = $this->_manObj->get(\Praxigento\Accounting\Lib\Service\Operation\Call::class);
        $this->_repoBasic = $this->_manObj->get(\Praxigento\Core\Repo\IGeneric::class);
    }

    private function _calculateBalances()
    {
        $req = new BalanceCalcRequest();
        $req->setData(BalanceCalcRequest::ASSET_TYPE_ID, $this->typeAssetId);
        $req->setData(BalanceCalcRequest::DATE_TO, self::DATA_DATE_BALANCE_UP_TO);
        $resp = $this->_callBalance->calc($req);
        $this->assertTrue($resp->isSucceed());
        $this->_logger->debug("Balances are calculated up to '" . $req->getData(BalanceCalcRequest::DATE_TO) . "'.");
    }

    private function _checkBalancesCurrent()
    {
        $req = new AccountGetRequest();
        $req->setData(AccountGetRequest::ACCOUNT_ID, $this->acc1[Account::ATTR_ID]);
        $resp = $this->_callAccount->get($req);
        $this->assertTrue($resp->isSucceed());
        $this->assertEquals(0 - $this->_amount, $resp->getData(Account::ATTR_BALANCE));
        $req->setData(AccountGetRequest::ACCOUNT_ID, $this->acc2[Account::ATTR_ID]);
        $resp = $this->_callAccount->get($req);
        $this->assertTrue($resp->isSucceed());
        $this->assertEquals(0 + $this->_amount, $resp->getData(Account::ATTR_BALANCE));
        $this->_logger->debug("Current balance (in 'accounts' table) is checked.");
    }

    private function _checkBalancesHistory()
    {
        /* check first account balance*/
        $data = $this->_repoBasic->getEntityByPk(
            Balance::ENTITY_NAME,
            [
                Balance::ATTR_ACCOUNT_ID => $this->acc1[Account::ATTR_ID],
                Balance::ATTR_DATE => self::DATA_DATE_BALANCE_CHECK
            ]);
        $this->assertEquals(0, $data[Balance::ATTR_BALANCE_OPEN]);
        $this->assertEquals(30, $data[Balance::ATTR_TOTAL_DEBIT]);
        $this->assertEquals(0, $data[Balance::ATTR_TOTAL_CREDIT]);
        $this->assertEquals(-30, $data[Balance::ATTR_BALANCE_CLOSE]);
        /* check second account balance*/
        $data = $this->_repoBasic->getEntityByPk(
            Balance::ENTITY_NAME,
            [
                Balance::ATTR_ACCOUNT_ID => $this->acc2[Account::ATTR_ID],
                Balance::ATTR_DATE => self::DATA_DATE_BALANCE_CHECK
            ]);
        $this->assertEquals(0, $data[Balance::ATTR_BALANCE_OPEN]);
        $this->assertEquals(0, $data[Balance::ATTR_TOTAL_DEBIT]);
        $this->assertEquals(30, $data[Balance::ATTR_TOTAL_CREDIT]);
        $this->assertEquals(30, $data[Balance::ATTR_BALANCE_CLOSE]);
        $this->_logger->debug("Balance history (in 'balance' table) is checked.");
    }

    private function _createAccounts()
    {
        /* create account for Customer 1 */
        $custId = $this->_mapCustomerMageIdByIndex[1];
        $req = new AccountGetRequest();
        $req->setData(AccountGetRequest::CUSTOMER_ID, $custId);
        $req->setData(AccountGetRequest::ASSET_TYPE_ID, $this->typeAssetId);
        $req->setData(AccountGetRequest::CREATE_NEW_ACCOUNT_IF_MISSED, true);
        $resp = $this->_callAccount->get($req);
        $this->assertTrue($resp->isSucceed());
        $this->acc1 = $resp->getData();
        /* create account for Customer 2 */
        $custId = $this->_mapCustomerMageIdByIndex[2];
        $req->setData(AccountGetRequest::CUSTOMER_ID, $custId);
        $resp = $this->_callAccount->get($req);
        $this->assertTrue($resp->isSucceed());
        $this->acc2 = $resp->getData();
        $accFirstId = $this->acc1[Account::ATTR_ID];
        $accSecondId = $this->acc2[Account::ATTR_ID];
        $this->_logger->debug("2 customer accounts are created (#$accFirstId, #$accSecondId).");
    }

    private function _createCustomers()
    {
        $tbl = $this->_resource->getTableName(self::ENTITY_CUSTOMER);
        /* create first customer */
        $this->_conn->insert(
            $tbl,
            [self::ATTR_CUST_EMAIL => 'customer1@test.com']
        );
        $id1 = $this->_conn->lastInsertId($tbl);
        $this->assertTrue($id1 > 0);
        $this->cust1[self::ATTR_CUST_ID] = $id1;
        /* create second customer */
        $this->_conn->insert(
            $tbl,
            [self::ATTR_CUST_EMAIL => 'customer2@test.com']
        );
        $id2 = $this->_conn->lastInsertId($tbl);
        $this->assertTrue($id2 > 0);
        $this->cust2[self::ATTR_CUST_ID] = $id2;
        $this->_logger->debug("Two customers are created (#$id1 & #$id2).");
    }

    private function _createOperation()
    {
        $req = new OperationAddRequest();
        $req->setOperationTypeId($this->typeOperId);
        $req->setDatePerformed(self::DATA_DATE_PERFORMED);
        $req->setTransactions([
            [
                Transaction::ATTR_DEBIT_ACC_ID => $this->acc1[Account::ATTR_ID],
                Transaction::ATTR_CREDIT_ACC_ID => $this->acc2[Account::ATTR_ID],
                Transaction::ATTR_VALUE => 5
            ],
            [
                Transaction::ATTR_DEBIT_ACC_ID => $this->acc1[Account::ATTR_ID],
                Transaction::ATTR_CREDIT_ACC_ID => $this->acc2[Account::ATTR_ID],
                Transaction::ATTR_VALUE => 10
            ],
            [
                Transaction::ATTR_DEBIT_ACC_ID => $this->acc1[Account::ATTR_ID],
                Transaction::ATTR_CREDIT_ACC_ID => $this->acc2[Account::ATTR_ID],
                Transaction::ATTR_VALUE => 15
            ]
        ]);
        $this->_amount = 5 + 10 + 15;
        /** @var  $resp OperationAddResponse */
        $resp = $this->_callOperation->add($req);
        $this->assertTrue($resp->isSucceed());
        $this->_logger->debug("New operation with 3 transactions is created.");
    }

    private function _createTypeAsset()
    {
        $bind = [
            TypeAsset::ATTR_CODE => self::DATA_TYPE_ASSET_CODE,
            TypeAsset::ATTR_NOTE => 'Asset for integration tests.'
        ];
        $id = $this->_repoBasic->addEntity(TypeAsset::ENTITY_NAME, $bind);
        $this->assertTrue($id > 0);
        $this->typeAssetId = $id;
        $this->_logger->debug("Asset type is created (#$id).");
    }

    private function _createTypeOperation()
    {
        $bind = [
            TypeOperation::ATTR_CODE => self::DATA_TYPE_OPER_CODE,
            TypeOperation::ATTR_NOTE => 'Operation for integration tests.'
        ];
        $id = $this->_repoBasic->addEntity(TypeOperation::ENTITY_NAME, $bind);
        $this->assertTrue($id > 0);
        $this->typeOperId = $id;
        $this->_logger->debug("Operation type is created (#$id).");
    }

    public function test_main()
    {
        $this->_logger->debug('Story01 in Accounting Integration tests is started.');
        $this->_conn->beginTransaction();
        try {
            /** create 2 customers */
            $this->_createMageCustomers(2);
            /** create 1 type of the assets */
            $this->_createTypeAsset();
            /** create 1 type of the operations */
            $this->_createTypeOperation();
            /** create 2 accounts */
            $this->_createAccounts();
            /** create 1 operation with 3 transactions from customer 1 to customer 2 accounts */
            $this->_createOperation();
            /** validate account balances (current) */
            $this->_checkBalancesCurrent();
            /** calculate historical balances */
            $this->_calculateBalances();
            /** validate history of the account balances */
            $this->_checkBalancesHistory();
        } finally {
            // $this->_conn->commit();
            $this->_conn->rollBack();
        }
        $this->_logger->debug('Story01 in Accounting Integration test is completed, all transactions are rolled back.');
    }
}