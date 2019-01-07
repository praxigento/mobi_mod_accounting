<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Praxigento\Accounting\Service\Account\Balance;

use Praxigento\Accounting\Repo\Data\Type\Asset as ETypeAsset;
use Praxigento\Accounting\Service\Account\Balance\Validate\Request as ARequest;
use Praxigento\Accounting\Service\Account\Balance\Validate\Response as AResponse;

/**
 * Validate current balances for customers accounts.
 */
class Validate
{
    /** @var \Praxigento\Accounting\Repo\Dao\Type\Asset */
    private $daoTypeAsset;
    /** @var \Psr\Log\LoggerInterface */
    private $logger;
    /** @var \Praxigento\Accounting\Service\Account\Balance\Validate\A\ProcessOneAsset */
    private $ownOneAsset;

    public function __construct(
        \Praxigento\Core\Api\App\Logger\Main $logger,
        \Praxigento\Accounting\Repo\Dao\Type\Asset $daoTypeAsset,
        \Praxigento\Accounting\Service\Account\Balance\Validate\A\ProcessOneAsset $ownOneAsset
    ) {
        $this->logger = $logger;
        $this->daoTypeAsset = $daoTypeAsset;
        $this->ownOneAsset = $ownOneAsset;
    }

    public function exec($request)
    {
        assert($request instanceof ARequest);
        $result = new AResponse();
        /** define local working data */
        $assets = $this->getAssetTypes();

        /** perform processing */
        $total = count($assets);
        $this->logger->info("Total $total asset types are available for balances validation.");
        foreach ($assets as $typeId => $typeCode) {
            $this->logger->info("Validate balances for asset $typeCode/$typeId.");
            $this->ownOneAsset->exec($typeId);
        }

        $result->markSucceed();
        return $result;
    }

    /**
     * Get all asset types.
     *
     * @return array [id => code]
     */
    private function getAssetTypes()
    {
        $result = [];
        $types = $this->daoTypeAsset->get();
        /** @var ETypeAsset $type */
        foreach ($types as $type) {
            $typeId = $type->getId();
            $typeCode = $type->getCode();
            $result[$typeId] = $typeCode;
        }
        return $result;
    }
}