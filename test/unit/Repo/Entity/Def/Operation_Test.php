<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Repo\Entity\Def;

use Praxigento\Accounting\Data\Entity\Operation as Entity;
use Praxigento\Accounting\Repo\Entity\IOperation;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class Operation_UnitTest
    extends \Praxigento\Core\Test\BaseCase\Repo\Entity
{
    /** @var  Operation */
    private $obj;

    public function setUp()
    {
        parent::setUp();
        $this->obj = new Operation(
            $this->mResource,
            $this->mRepoGeneric,
            Entity::class
        );
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(IOperation::class, $this->obj);
    }

}