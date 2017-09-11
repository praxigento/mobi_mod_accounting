<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Repo\Entity;


include_once(__DIR__ . '/../../../../../phpunit_bootstrap.php');

class Builder_ManualTest
    extends \Praxigento\Core\Test\BaseCase\Manual
{

    public function test_build()
    {
        /** @var \Praxigento\Accounting\Ui\DataProvider\Grid\Account\Items\Builder $obj */
        $obj = $this->manObj->get(\Praxigento\Accounting\Ui\DataProvider\Grid\Account\Items\Builder::class);
        /** @var \Magento\Framework\Api\Search\SearchCriteriaInterface $search */
        $search = $this->manObj->get(\Magento\Framework\Api\Search\SearchCriteriaInterface::class);
        $res = $obj->getTotal($search);
        $this->assertTrue((bool)$res);
    }

}