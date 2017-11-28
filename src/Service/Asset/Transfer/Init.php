<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Service\Asset\Transfer;

use Praxigento\Accounting\Api\Service\Asset\Transfer\Init\Request as ARequest;
use Praxigento\Accounting\Api\Service\Asset\Transfer\Init\Response as AResponse;
use Praxigento\Accounting\Api\Service\Asset\Transfer\Init\Response\Data as DRespData;
use Praxigento\Accounting\Api\Service\Asset\Transfer\Init\Response\Data\Asset as DAsset;
use Praxigento\Accounting\Api\Service\Asset\Transfer\Init\Response\Data\Customer as DCustomer;
use Praxigento\Accounting\Service\Asset\Transfer\Init\Db\Query\GetAssets as QBGetAssets;
use Praxigento\Core\Api\Service\Customer\Get as ServCustGet;

/**
 * Get initialization data for asset transfer modal slider in adminhtml.
 */
class Init
    implements \Praxigento\Accounting\Api\Service\Asset\Transfer\Init
{
    /** @var \Praxigento\Accounting\Service\Asset\Transfer\Init\Db\Query\GetAssets */
    private $qbGetAssets;
    /** @var \Praxigento\Core\Api\Service\Customer\Get */
    private $servCustGet;

    public function __construct(
        QBGetAssets $qbGetAssets,
        ServCustGet $servCustGet
    )
    {
        $this->qbGetAssets = $qbGetAssets;
        $this->servCustGet = $servCustGet;
    }

    public function exec(ARequest $data)
    {
        /* define local working data */
        $customerId = $data->getCustomerId();

        /* perform processing */
        $assets = $this->loadAssetsData($customerId);
        $customer = $this->loadCustomerData($customerId);

        /* compose result */
        $respData = new DRespData();
        $respData->setAssets($assets);
        $respData->setCustomer($customer);
        $result = new AResponse();
        $result->setData($respData);
        return $result;
    }


    /**
     * Load assets data from DB and compose API result component.
     *
     * @param int $custId
     * @return \Praxigento\Accounting\Api\Service\Asset\Transfer\Init\Response\Data\Asset[]
     */
    private function loadAssetsData($custId)
    {
        $query = $this->qbGetAssets->build();
        $conn = $query->getConnection();
        $bind = [
            QBGetAssets::BND_CUST_ID => $custId
        ];
        $rs = $conn->fetchAll($query, $bind);

        $result = [];
        foreach ($rs as $db) {
            /* extract DB data */
            $assetId = $db[QBGetAssets::A_ASSET_ID];
            $assetCode = $db[QBGetAssets::A_ASSET_CODE];
            $accId = $db[QBGetAssets::A_ACC_ID];
            $accBalance = $db[QBGetAssets::A_ACC_BALANCE];

            /* compose API data */
            $api = new DAsset();
            $api->setAccBalance($accBalance);
            $api->setAccId($accId);
            $api->setAssetCode($assetCode);
            $api->setAssetId($assetId);
            $result[] = $api;
        }
        return $result;
    }

    /**
     * Load customer data from DB and compose API result component.
     *
     * @param int $custId
     * @return \Praxigento\Accounting\Api\Service\Asset\Transfer\Init\Response\Data\Customer
     */
    private function loadCustomerData($custId)
    {
        $request = new \Praxigento\Core\Api\Service\Customer\Get\Request();
        $request->setCustomerId($custId);
        $response = $this->servCustGet->exec($request);
        $data = $response->get();

        /* compose API data */
        $result = new DCustomer($data);
        return $result;
    }
}