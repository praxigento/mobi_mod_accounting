<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Ui\DataProvider;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;

/**
 * Data provider for "Accounting / Accounts" grid.
 */
class Account2
    extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    const JSON_ATTR_ITEMS = 'items';
    const JSON_ATTR_TOTAL_RECORDS = 'totalRecords';
    /**#@+
     * UI XML arguments and default values to configure this component.
     */
    const UICD_UPDATE_URL = 'mui/index/render';
    const UIC_CONFIG = 'config';
    const UIC_UPDATE_URL = 'update_url';

    /**#@- */
    /** @var \Praxigento\Accounting\Repo\Query\Account\Grid\ItemsBuilder */
    private $qbItems;
    /** @var \Praxigento\Accounting\Repo\Query\Account\Grid\TotalBuilder */
    private $qbTotal;

    public function __construct($name,
                                \Magento\Framework\UrlInterface $url,
                                SearchCriteriaBuilder $searchCriteriaBuilder,
                                RequestInterface $request,
                                FilterBuilder $filterBuilder,
                                \Praxigento\Accounting\Repo\Query\Account\Grid\ItemsBuilder $qbItems,
                                \Praxigento\Accounting\Repo\Query\Account\Grid\TotalBuilder $qbTotal,

                                array $meta = [],
                                array $data = [])
    {
        /* add default Update URL */
        if (!isset($data[static::UIC_CONFIG][static::UIC_UPDATE_URL])) {
            $val = $url->getRouteUrl(static::UICD_UPDATE_URL);
            $data[static::UIC_CONFIG][static::UIC_UPDATE_URL] = $val;
        }
        $reporting = new Account2\Report();
        $this->qbItems = $qbItems;
        $this->qbTotal = $qbTotal;
        parent::__construct($name, 'entity_id', 'id', $reporting, $searchCriteriaBuilder, $request, $filterBuilder, $meta, $data);
    }


    public function getData()
    {
        $searchCriteria = $this->getSearchCriteria();
        $items = [];
        $this->qbItems->build();
        $this->qbTotal->build();

        $result = [
            static::JSON_ATTR_TOTAL_RECORDS => 0,
            static::JSON_ATTR_ITEMS => $items
        ];
        return $result;
    }

}