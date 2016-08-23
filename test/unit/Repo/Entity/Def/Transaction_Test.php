<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Repo\Entity\Def;

use Praxigento\Accounting\Data\Entity\Transaction as Entity;
use Praxigento\Accounting\Repo\Entity\ITransaction;


include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class Transaction_UnitTest
    extends \Praxigento\Core\Test\BaseCase\Repo\Entity
{
    /** @var  Transaction */
    private $obj;

    public function setUp()
    {
        parent::setUp();
        $this->obj = new Transaction(
            $this->mResource,
            $this->mRepoGeneric,
            Entity::class
        );
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(ITransaction::class, $this->obj);
    }

}