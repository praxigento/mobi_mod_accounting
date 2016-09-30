<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Repo\Agg\Def\Account;

include_once(__DIR__ . '/../../../../phpunit_bootstrap.php');
use Praxigento\Accounting\Config as Cfg;
use Praxigento\Accounting\Data\Entity\Account as EAccount;
use Praxigento\Accounting\Data\Entity\Type\Asset as ETypeAsset;

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
        $sql = "SELECT `paa`.`id` AS `Id`, `paa`.`balance` AS `Balance`, `pata`.`code` AS `Asset`, (CONCAT(firstname, ' ', lastname)) AS `CustName`, `ce`.`email` AS `CustEmail` FROM `prxgt_acc_account` AS `paa`
 LEFT JOIN `prxgt_acc_type_asset` AS `pata` ON pata.id=paa.asset_type_id
 LEFT JOIN `customer_entity` AS `ce` ON ce.entity_id=paa.customer_id";
        /** === Setup Mocks === */
        $this->_setTableNames([
            EAccount::ENTITY_NAME,
            Cfg::ENTITY_MAGE_CUSTOMER,
            ETypeAsset::ENTITY_NAME
        ]);
        /** === Call and asserts  === */
        $res = $this->obj->getQueryToSelect();
        $this->assertEquals($sql, (string)$res);
    }

    public function test_getQueryToSelectCount()
    {
        /** === Test Data === */
        $sql = 'SELECT (COUNT(paa.id)) FROM `prxgt_acc_account` AS `paa`
 LEFT JOIN `prxgt_acc_type_asset` AS `pata` ON pata.id=paa.asset_type_id
 LEFT JOIN `customer_entity` AS `ce` ON ce.entity_id=paa.customer_id';
        /** === Setup Mocks === */
        $this->_setTableNames([
            EAccount::ENTITY_NAME,
            Cfg::ENTITY_MAGE_CUSTOMER,
            ETypeAsset::ENTITY_NAME
        ]);
        /** === Call and asserts  === */
        $res = $this->obj->getQueryToSelectCount();
        $this->assertEquals($sql, (string)$res);
    }
}