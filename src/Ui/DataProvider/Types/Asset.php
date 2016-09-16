<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Ui\DataProvider\Types;

/**
 * Data provider for "Accounting / Asset Types" grid.
 */
class Asset
    extends \Praxigento\Core\Ui\DataProvider\Base
{

    public function __construct(
        \Magento\Framework\UrlInterface $url,
        \Praxigento\Core\Repo\Query\Criteria\IAdapter $criteriaAdapter,
        \Praxigento\Accounting\Repo\Entity\Type\IAsset $repo,
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