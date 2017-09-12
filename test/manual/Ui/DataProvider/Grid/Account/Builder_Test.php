<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Ui\DataProvider\Grid\Account;

include_once(__DIR__ . '/../../../../phpunit_bootstrap.php');

class Builder_ManualTest
    extends \Praxigento\Core\Test\BaseCase\Manual
{

    public function test_getTotal()
    {
        /** @var \Praxigento\Accounting\Ui\DataProvider\Grid\Account\QueryBuilder $obj */
        $obj = $this->manObj->get(\Praxigento\Accounting\Ui\DataProvider\Grid\Account\QueryBuilder::class);
        /** @var \Magento\Framework\Api\Search\SearchCriteriaInterface $search */
        $search = $this->manObj->get(\Magento\Framework\Api\Search\SearchCriteriaInterface::class);
        $res = $obj->getTotal($search);
        $this->assertTrue((bool)$res);
    }

    public function test_getItems()
    {
        /** @var \Praxigento\Accounting\Ui\DataProvider\Grid\Account\QueryBuilder $obj */
        $obj = $this->manObj->get(\Praxigento\Accounting\Ui\DataProvider\Grid\Account\QueryBuilder::class);
        /** @var \Magento\Framework\Api\Search\SearchCriteriaInterface $search */
        $search = $this->manObj->get(\Magento\Framework\Api\Search\SearchCriteriaInterface::class);
        $res = $obj->getItems($search);
        $this->assertTrue((bool)$res);
    }

}