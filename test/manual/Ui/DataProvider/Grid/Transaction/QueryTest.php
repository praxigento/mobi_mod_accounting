<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Test\Praxigento\Accounting\Ui\DataProvider\Grid\Transaction;

include_once(__DIR__ . '/../../../../phpunit_bootstrap.php');

class QueryTest
    extends \Praxigento\Core\Test\BaseCase\Manual
{

    /** @var \Praxigento\Accounting\Ui\DataProvider\Grid\Transaction\Query */
    private $obj;

    protected function setUp(): void
    {
        $this->obj = $this->manObj->create(\Praxigento\Accounting\Ui\DataProvider\Grid\Transaction\Query::class);
    }

    public function test_getItems()
    {
        /** @var \Magento\Framework\Api\Search\SearchCriteriaInterface $search */
        $search = $this->manObj->get(\Magento\Framework\Api\Search\SearchCriteriaInterface::class);
        $res = $this->obj->getItems($search);
        $this->assertTrue((bool)$res);
    }

    public function test_getTotal()
    {
        /** @var \Magento\Framework\Api\Search\SearchCriteriaInterface $search */
        $search = $this->manObj->get(\Magento\Framework\Api\Search\SearchCriteriaInterface::class);
        $res = $this->obj->getTotal($search);
        $this->assertTrue((bool)$res);
    }

}
