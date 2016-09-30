<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Ui\DataProvider;

/**
 * Data provider for "Accounting / Accounts" grid.
 */
class Account
    extends \Praxigento\Core\Ui\DataProvider\Base
{
    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\UrlInterface $url,
        \Praxigento\Core\Repo\Query\Criteria\IAdapter $critAdapter,
        \Praxigento\Accounting\Repo\Agg\Def\Account\Mapper $mapperApi2Sql,
        \Praxigento\Accounting\Repo\Agg\IAccount $repo,
        \Magento\Framework\View\Element\UiComponent\DataProvider\Reporting $reporting,
        \Magento\Framework\Api\Search\SearchCriteriaBuilder $searchCritBuilder,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        $name,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $url,
            $critAdapter,
            $mapperApi2Sql,
            $repo,
            $reporting,
            $searchCritBuilder,
            $request,
            $filterBuilder,
            $name,
            $meta,
            $data
        );
    }

}