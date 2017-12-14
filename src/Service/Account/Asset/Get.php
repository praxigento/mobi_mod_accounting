<?php
/**
 *
 */

namespace Praxigento\Accounting\Service\Account\Asset;

use Praxigento\Accounting\Service\Account\Asset\Get\Db\Query\GetAssets as QBGetAssets;
use Praxigento\Accounting\Service\Account\Asset\Get\Request as ARequest;
use Praxigento\Accounting\Service\Account\Asset\Get\Response as AResponse;
use Praxigento\Accounting\Service\Account\Asset\Get\Response\Item as DResponseItem;

/**
 * Internal service to get asset data for a customer (available asset types, existing accounts & balances).
 *
 * This service is not used outside this module.
 */
class Get
{
    /** @var \Praxigento\Accounting\Service\Account\Asset\Get\Db\Query\GetAssets */
    private $qbGetAssets;

    public function __construct(
        QBGetAssets $qbGetAssets
    ) {
        $this->qbGetAssets = $qbGetAssets;
    }

    /**
     * Get asset data for a customer (available asset types, existing accounts & balances).
     *
     * @param ARequest $req
     * @return AResponse
     */
    public function exec($req) {
        assert($req instanceof ARequest);
        /** define local working data */
        $customerId = $req->getCustomerId();

        /** perform processing */
        $items = $this->loadAssetsData($customerId);

        /** compose result */
        $result = new AResponse();
        $result->setItems($items);
        return $result;
    }

    /**
     * @param int $custId
     * @return array
     */
    private function loadAssetsData($custId) {
        $query = $this->qbGetAssets->build();
        $conn = $query->getConnection();
        $bind = [
            QBGetAssets::BND_CUST_ID => $custId
        ];
        $rs = $conn->fetchAll($query, $bind);

        $result = [];
        foreach ($rs as $db) {
            /* extract DB data */
            $accBalance = $db[QBGetAssets::A_ACC_BALANCE];
            $accId = $db[QBGetAssets::A_ACC_ID];
            $assetCode = $db[QBGetAssets::A_ASSET_CODE];
            $assetId = $db[QBGetAssets::A_ASSET_ID];
            $isVisible = $db[QBGetAssets::A_IS_VISIBLE];

            /* compose API data for visible items */
            if ($isVisible) {
                $api = new DResponseItem();
                $api->setAccBalance($accBalance);
                $api->setAccId($accId);
                $api->setAssetCode($assetCode);
                $api->setAssetId($assetId);
                $result[] = $api;
            }
        }
        return $result;
    }
}