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
    /** @var  \Praxigento\Core\Repo\Query\Criteria\IMapper */
    protected $api2sqlMapper;
    /** @var  \Praxigento\Core\Repo\Query\Criteria\IAdapter */
    protected $criteriaAdapter;
    /** @var \Praxigento\Accounting\Ui\DataProvider\Account2\Query\ItemsBuilder */
    private $qbItems;
    /** @var \Praxigento\Accounting\Ui\DataProvider\Account2\Query\TotalBuilder */
    private $qbTotal;

    public function __construct($name,
                                \Magento\Framework\UrlInterface $url,
                                SearchCriteriaBuilder $searchCriteriaBuilder,
                                RequestInterface $request,
                                FilterBuilder $filterBuilder,
                                \Praxigento\Accounting\Ui\DataProvider\Account2\Query\ItemsBuilder $qbItems,
                                \Praxigento\Accounting\Ui\DataProvider\Account2\Query\TotalBuilder $qbTotal,
                                \Praxigento\Core\Repo\Query\Criteria\IAdapter $critAdapter,
                                \Praxigento\Accounting\Ui\DataProvider\Account2\Query\Mapper $api2sqlMapper,
                                \Praxigento\Accounting\Ui\DataProvider\Account2\Report $reporting,
                                array $meta = [],
                                array $data = [])
    {
        /* add default Update URL */
        if (!isset($data[static::UIC_CONFIG][static::UIC_UPDATE_URL])) {
            $val = $url->getRouteUrl(static::UICD_UPDATE_URL);
            $data[static::UIC_CONFIG][static::UIC_UPDATE_URL] = $val;
        }

        $this->qbItems = $qbItems;
        $this->qbTotal = $qbTotal;
        /* post construction setup */
        $this->criteriaAdapter = $critAdapter;
        $this->api2sqlMapper = $api2sqlMapper;
        parent::__construct($name, 'entity_id', 'id', $reporting, $searchCriteriaBuilder, $request, $filterBuilder, $meta, $data);
    }


    public function getData()
    {
        $searchCriteria = $this->getSearchCriteria();
        $where = $this->criteriaAdapter->getWhereFromApiCriteria($searchCriteria, $this->api2sqlMapper);
        /* get query to select data */
        $queryItems = $this->qbItems->build();
        $queryItems->where($where);
        /* set order */
        $order = $this->criteriaAdapter->getOrderFromApiCriteria($searchCriteria);
        $queryItems->order($order);
        /* limit pages */
        $pageSize = $searchCriteria->getPageSize();
        $pageIndx = $searchCriteria->getCurrentPage();
        $queryItems->limitPage($pageIndx, $pageSize);
        /* get query for total count */
        $conn = $queryItems->getConnection();
        $items = $conn->fetchAll($queryItems);
        $queryTotal = $this->qbTotal->build();
        $queryTotal->where($where);
        $total = $conn->fetchOne($queryTotal);

        $result = [
            static::JSON_ATTR_TOTAL_RECORDS => $total,
            static::JSON_ATTR_ITEMS => $items
        ];
        return $result;
    }

}