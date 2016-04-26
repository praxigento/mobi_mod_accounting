<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Data\Entity;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');
use Praxigento\Accounting\Data\Entity\Operation as DataEntity;

class Operation_UnitTest extends \Praxigento\Core\Test\BaseMockeryCase
{
    /** @var  DataEntity */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        $this->obj = new DataEntity();
    }

    public function test_accessors()
    {
        /* === Test Data === */
        $DATE_PERFORMED = 'date_performed';
        $ID = 'id';
        $TYPE_ID = 'type_id';
        /* === Call and asserts  === */
        $this->obj->setDatePerformed($DATE_PERFORMED);
        $this->obj->setId($ID);
        $this->obj->setTypeId($TYPE_ID);
        $this->assertEquals($DATE_PERFORMED, $this->obj->getDatePerformed());
        $this->assertEquals($ID, $this->obj->getId());
        $this->assertEquals($TYPE_ID, $this->obj->getTypeId());
    }

    public function test_pk()
    {
        /* === Call and asserts  === */
        $pk = $this->obj->getPrimaryKeyAttrs();
        $this->assertEquals([DataEntity::ATTR_ID], $pk);
    }
}