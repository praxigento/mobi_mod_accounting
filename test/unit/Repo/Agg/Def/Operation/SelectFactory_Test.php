<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Repo\Agg\Def\Operation;

include_once(__DIR__ . '/../../../../phpunit_bootstrap.php');
use Praxigento\Accounting\Data\Entity\Operation as EOperation;
use Praxigento\Accounting\Data\Entity\Type\Operation as ETypeOper;

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
        $sql = 'SELECT `pao`.`id` AS `Id`, `pao`.`date_performed` AS `DatePerformed`, `pao`.`note` AS `Note`, `pato`.`code` AS `Type` FROM `prxgt_acc_operation` AS `pao`
 LEFT JOIN `prxgt_acc_type_operation` AS `pato` ON pato.id=pao.type_id';
        /** === Setup Mocks === */
        $this->_setTableNames([
            EOperation::ENTITY_NAME,
            ETypeOper::ENTITY_NAME
        ]);
        /** === Call and asserts  === */
        /** @var \Magento\Framework\DB\Select $res */
        $res = $this->obj->getQueryToSelect();
        $this->assertEquals($sql, (string)$res);
    }

    public function test_getQueryToSelectCount()
    {
        /** === Test Data === */
        $sql = 'SELECT (COUNT(pao.id)) FROM `prxgt_acc_operation` AS `pao`
 LEFT JOIN `prxgt_acc_type_operation` AS `pato` ON pato.id=pao.type_id';
        /** === Setup Mocks === */
        $this->_setTableNames([
            EOperation::ENTITY_NAME,
            ETypeOper::ENTITY_NAME
        ]);
        /** === Call and asserts  === */
        /** @var \Magento\Framework\DB\Select $res */
        $res = $this->obj->getQueryToSelectCount();
        $this->assertEquals($sql, (string)$res);
    }
}