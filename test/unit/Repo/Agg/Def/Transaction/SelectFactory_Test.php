<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Repo\Agg\Def\Transaction;

include_once(__DIR__ . '/../../../../phpunit_bootstrap.php');

use Praxigento\Accounting\Config as Cfg;
use Praxigento\Accounting\Repo\Entity\Data\Account as EAccount;
use Praxigento\Accounting\Repo\Entity\Data\Transaction as ETransaction;
use Praxigento\Accounting\Repo\Entity\Data\Type\Asset as ETypeAsset;

/**
 * @SuppressWarnings(PHPMD.CamelCaseClassName)
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class SelectFactory_UnitTest
    extends \Praxigento\Core\Test\BaseCase\Repo\Agg\SelectFactory
{
    /** @var  SelectFactory */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create object to test */
        $this->obj = new SelectFactory(
            $this->mResource
        );
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(SelectFactory::class, $this->obj);
    }

    public function test_getQueryToSelect()
    {
        /** === Test Data === */
        $sql = 'SELECT `pat`.`id` AS `TransId`, `pat`.`operation_id` AS `OperId`, `pat`.`date_applied` AS `DateApplied`, `pat`.`value` AS `Value`, `pat`.`note` AS `Note`, `ce_db`.`email` AS `Debit`, `ce_cr`.`email` AS `Credit`, `pata`.`code` AS `Asset` FROM `prxgt_acc_transaction` AS `pat`
 LEFT JOIN `prxgt_acc_account` AS `paa_db` ON paa_db.id=pat.debit_acc_id
 LEFT JOIN `customer_entity` AS `ce_db` ON ce_db.entity_id=paa_db.customer_id
 LEFT JOIN `prxgt_acc_account` AS `paa_cr` ON paa_cr.id=pat.credit_acc_id
 LEFT JOIN `customer_entity` AS `ce_cr` ON ce_cr.entity_id=paa_cr.customer_id
 LEFT JOIN `prxgt_acc_type_asset` AS `pata` ON pata.id=paa_db.asset_type_id';
        /** === Setup Mocks === */
        $this->_setTableNames([
            ETypeAsset::ENTITY_NAME,
            EAccount::ENTITY_NAME,
            EAccount::ENTITY_NAME,
            Cfg::ENTITY_MAGE_CUSTOMER,
            Cfg::ENTITY_MAGE_CUSTOMER,
            ETransaction::ENTITY_NAME
        ]);
        /** === Call and asserts  === */
        /** @var \Magento\Framework\DB\Select $res */
        $res = $this->obj->getQueryToSelect();
        $this->assertEquals($sql, (string)$res);
    }

    public function test_getQueryToSelectCount()
    {
        /** === Test Data === */
        $sql = 'SELECT (COUNT(pat.id)) FROM `prxgt_acc_transaction` AS `pat`
 LEFT JOIN `prxgt_acc_account` AS `paa_db` ON paa_db.id=pat.debit_acc_id
 LEFT JOIN `customer_entity` AS `ce_db` ON ce_db.entity_id=paa_db.customer_id
 LEFT JOIN `prxgt_acc_account` AS `paa_cr` ON paa_cr.id=pat.credit_acc_id
 LEFT JOIN `customer_entity` AS `ce_cr` ON ce_cr.entity_id=paa_cr.customer_id
 LEFT JOIN `prxgt_acc_type_asset` AS `pata` ON pata.id=paa_db.asset_type_id';
        /** === Setup Mocks === */
        $this->_setTableNames([
            ETypeAsset::ENTITY_NAME,
            EAccount::ENTITY_NAME,
            EAccount::ENTITY_NAME,
            Cfg::ENTITY_MAGE_CUSTOMER,
            Cfg::ENTITY_MAGE_CUSTOMER,
            ETransaction::ENTITY_NAME
        ]);
        /** === Call and asserts  === */
        /** @var \Magento\Framework\DB\Select $res */
        $res = $this->obj->getQueryToSelectCount();
        $this->assertEquals($sql, (string)$res);
    }
}