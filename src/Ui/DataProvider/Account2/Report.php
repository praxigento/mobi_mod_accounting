<?php
/**
 * Created by PhpStorm.
 * User: dm
 * Date: 07.09.17
 * Time: 9:21
 */

namespace Praxigento\Accounting\Ui\DataProvider\Account2;


use Magento\Framework\Api\Search\SearchCriteriaInterface;

class Report
    implements \Magento\Framework\Api\Search\ReportingInterface
{

    public function search(SearchCriteriaInterface $searchCriteria)
    {
        $result = new \Magento\Framework\Api\Search\SearchResult();
        $items = [
            ['Id' => 1, 'CustName' => 'name', 'CustEmail' => 'name@mail.com', 'Balance' => 47.9287, 'Asset' => '2'],
            ['Id' => 2, 'CustName' => 'nam2e', 'CustEmail' => 'name2@mail.com', 'Balance' => 12.6562, 'Asset' => '3']
        ];
        $result->setItems($items);
        $result->setTotalCount(2);
        return $result;
    }
}