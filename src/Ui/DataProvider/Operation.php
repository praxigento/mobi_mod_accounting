<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Ui\DataProvider;

/**
 * Data provider for "Accounting / Operations" grid.
 */
class Operation
    extends \Praxigento\Core\Ui\DataProvider\Base
{

    public function __construct(
        \Magento\Framework\UrlInterface $url,
        \Praxigento\Core\Repo\Query\Criteria\IAdapter $criteriaAdapter,
        \Praxigento\Accounting\Repo\Agg\Def\Operation\Mapper $mapperApi2Sql,
        \Praxigento\Accounting\Repo\Agg\IOperation $repo,
        \Magento\Framework\View\Element\UiComponent\DataProvider\Reporting $reporting,
        \Magento\Framework\Api\Search\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        $name,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $url,
            $criteriaAdapter,
            $mapperApi2Sql,
            $repo,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $name,
            $meta,
            $data
        );
    }

}