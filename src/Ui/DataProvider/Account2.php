<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Ui\DataProvider;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
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

    public function __construct($name,
                                \Magento\Framework\UrlInterface $url,
                                ReportingInterface $reporting,
                                SearchCriteriaBuilder $searchCriteriaBuilder,
                                RequestInterface $request,
                                FilterBuilder $filterBuilder,
                                array $meta = [],
                                array $data = [])
    {
        /* add default Update URL */
        if (!isset($data[static::UIC_CONFIG][static::UIC_UPDATE_URL])) {
            $val = $url->getRouteUrl(static::UICD_UPDATE_URL);
            $data[static::UIC_CONFIG][static::UIC_UPDATE_URL] = $val;
        }
        parent::__construct($name, 'id', 'Id', $reporting, $searchCriteriaBuilder, $request, $filterBuilder, $meta, $data);
    }


    public function getData()
    {
        $items = [
            ['Id' => 1, 'CustName' => 'name'],
            ['Id' => 2, 'CustName' => 'nam2e']
        ];

        $result = [
            static::JSON_ATTR_TOTAL_RECORDS => 2,
            static::JSON_ATTR_ITEMS => $items
        ];
        return $result;
    }

}