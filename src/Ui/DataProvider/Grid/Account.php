<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Ui\DataProvider\Grid;


class Account
    extends \Praxigento\Core\Ui\DataProvider\Grid\Base
{
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Magento\Framework\Api\Search\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\App\RequestInterface $request,
        \Praxigento\Accounting\Ui\DataProvider\Grid\Account\QueryBuilder $gridQueryBuilder,
        array $meta = [],
        array $data = []
    )
    {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $searchCriteriaBuilder,
            $request,
            $gridQueryBuilder,
            $meta,
            $data
        );
    }

}