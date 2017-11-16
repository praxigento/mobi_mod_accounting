<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Service\Asset\Transfer;

use Praxigento\Accounting\Api\Asset\Transfer\Init\Request as ARequest;
use Praxigento\Accounting\Api\Asset\Transfer\Init\Response as AResponse;
use Praxigento\Accounting\Api\Asset\Transfer\Init\Response\Data as DRespData;
use Praxigento\Accounting\Service\Asset\Transfer\Init\Db\Query\GetCustomer as QBGetCustomer;

class Init
    implements \Praxigento\Accounting\Api\Asset\Transfer\InitInterface
{
    private $qbGetCustomer;

    public function __construct(
        QBGetCustomer $qbGetCustomer
    )
    {
        $this->qbGetCustomer = $qbGetCustomer;
    }

    public function exec(ARequest $data)
    {
        /* define local working data */
        $customerId = $data->getCustomerId();

        $respData = new DRespData();
        $this->loadCustomerData($respData, $customerId);

        $result = new AResponse();
        $result->setData($respData);
        return $result;
    }

    private function loadCustomerData(DRespData $data, $custId)
    {
        $query = $this->qbGetCustomer->build();
        $conn = $query->getConnection();
        $bind = [
            QBGetCustomer::BND_CUST_ID => $custId
        ];
        $rs = $conn->fetchRow($query, $bind);

        $custId = $rs[QBGetCustomer::A_ID];

        /* populate response data */
        $data->setCustomerId($custId);

        return $data;
    }
}