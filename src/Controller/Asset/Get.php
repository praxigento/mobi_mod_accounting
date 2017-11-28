<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Controller\Asset;

use Praxigento\Accounting\Api\Data\Asset as DAsset;
use Praxigento\Accounting\Repo\Query\Asset\Get as QBGetAssets;

/**
 * Web API action to get assets for the customer.
 */
class Get
    extends \Praxigento\Core\App\Action\Front\Api\Base
{
    private $qbGet;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Webapi\ServiceInputProcessor $inputProcessor,
        \Magento\Framework\Webapi\ServiceOutputProcessor $outputProcessor,
        \Praxigento\Core\Fw\Logger\App $logger,
        \Praxigento\Core\App\WebApi\IAuthenticator $authenticator,
        \Praxigento\Accounting\Repo\Query\Asset\Get $qbGet
    )
    {
        parent::__construct($context, $inputProcessor, $outputProcessor, $logger, $authenticator);
        $this->qbGet = $qbGet;
    }

    protected function getInDataType(): string
    {
        return \Praxigento\Accounting\Api\Ctrl\Account\Asset\Get\Request::class;
    }

    protected function getOutDataType(): string
    {
        return \Praxigento\Accounting\Api\Ctrl\Account\Asset\Get\Response::class;
    }

    /**
     * Load assets data from DB and compose API result component.
     *
     * @param int $custId
     * @return \Praxigento\Accounting\Api\Service\Asset\Transfer\Init\Response\Data\Asset[]
     */
    private function loadAssetsData($custId)
    {
        $query = $this->qbGet->build();
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

            /* TODO: skip hidden types, like WALLET_HOLD */

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

    protected function process($request)
    {
        /* define local working data */
        assert($request instanceof \Praxigento\Accounting\Api\Ctrl\Account\Asset\Get\Request);
        $customerId = $request->getCustomerId();

        /* perform processing */
        $customerId = $this->authenticator->getCurrentCustomerId($customerId);
        $items = $this->loadAssetsData($customerId);

        /* compose result */
        $result = new \Praxigento\Accounting\Api\Ctrl\Account\Asset\Get\Response();
        $data = new \Praxigento\Accounting\Api\Ctrl\Account\Asset\Get\Response\Data();
        $data->setItems($items);
        $result->setData($data);
        return $result;
    }


}