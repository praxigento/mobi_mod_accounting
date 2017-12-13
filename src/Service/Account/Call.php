<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Service\Account;

use Praxigento\Accounting\Repo\Entity\Data\Account as Account;

/**
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class Call
    extends \Praxigento\Core\App\Service\Base\Call
    implements \Praxigento\Accounting\Service\IAccount
{
    /** @var array save accounts data for representative customer. */
    protected $cachedRepresentAccs = [];
    /** @var  \Praxigento\Accounting\Repo\Entity\Account */
    protected $repoAccount;
    /** @var \Praxigento\Accounting\Repo\Entity\Type\Asset */
    protected $repoTypeAsset;

    /**
     * Call constructor.
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Praxigento\Accounting\Repo\Entity\Account $repoAccount,
        \Praxigento\Accounting\Repo\Entity\Type\Asset $repoTypeAsset
    ) {
        parent::__construct($logger, $manObj);
        $this->repoAccount = $repoAccount;
        $this->repoTypeAsset = $repoTypeAsset;
    }

    public function cacheReset()
    {
        $this->cachedRepresentAccs = [];
    }

    /**
     * Get account data or create new account if customer has no account for the requested asset.
     *
     * @param Request\Get $request
     *
     * @return Response\Get
     */
    public function get(Request\Get $request)
    {
        $result = new Response\Get();
        $this->logger->info("'Get account' operation is called.");
        $accountId = $request->getAccountId();
        $customerId = $request->getCustomerId();
        $assetTypeId = $request->getAssetTypeId();
        $assetTypeCode = $request->getAssetTypeCode();
        $createNewAccIfMissed = $request->getCreateNewAccountIfMissed();
        /* accountId has the highest priority */
        if ($accountId) {
            $data = $this->repoAccount->getById($accountId);
        } else {
            /* try to look up by customer id & asset type id */
            if (!$assetTypeId) {
                /* get asset type ID by asset code */
                $assetTypeId = $this->repoTypeAsset->getIdByCode($assetTypeCode);
            }
            /* get account by customerId & assetTypeId */
            $data = $this->repoAccount->getByCustomerId($customerId, $assetTypeId);
        }
        /* analyze found data */
        if ($data) {
            $result->set($data);
            $result->markSucceed();
        } else {
            if ($createNewAccIfMissed) {
                /* not found - add new account */
                $data = [
                    Account::ATTR_CUST_ID => $customerId,
                    Account::ATTR_ASSET_TYPE_ID => $assetTypeId,
                    Account::ATTR_BALANCE => 0
                ];
                $accId = $this->repoAccount->create($data);
                $data[Account::ATTR_ID] = $accId;
                $result->set($data);
                $result->markSucceed();
                $this->logger->info("There is no account for customer #{$customerId} and asset type #$assetTypeId. New account #$accId is created.");
            }
        }
        $this->logger->info("'Get account' operation is completed.");
        return $result;
    }

    /**
     * @param Request\GetRepresentative $request
     *
     * @return Response\GetRepresentative
     */
    public function getRepresentative(Request\GetRepresentative $request)
    {
        $result = new Response\GetRepresentative();
        $typeId = $request->getAssetTypeId();
        $typeCode = $request->getAssetTypeCode();
        $this->logger->info("'Get representative account' operation is called.");
        if (is_null($typeId)) {
            $typeId = $this->repoTypeAsset->getIdByCode($typeCode);
        }
        if (!is_null($typeId)) {
            if (isset($this->cachedRepresentAccs[$typeId])) {
                $result->set($this->cachedRepresentAccs[$typeId]);
                $result->markSucceed();
            } else {
                /* there is no cached data yet */
                /* get representative customer ID */
                $customerId = $this->repoAccount->getRepresentativeCustomerId();
                /* get all accounts for the representative customer */
                $accounts = $this->repoAccount->getAllByCustomerId($customerId);
                if ($accounts) {
                    $mapped = [];
                    foreach ($accounts as $one) {
                        $mapped[$one->getAssetTypeId()] = $one;
                    }
                    $this->cachedRepresentAccs = $mapped;
                }
                /* check selected accounts */
                if (isset($this->cachedRepresentAccs[$typeId])) {
                    $result->set($this->cachedRepresentAccs[$typeId]);
                    $result->markSucceed();
                } else {
                    /* there is no accounts yet */
                    $req = new Request\Get();
                    $req->setCustomerId($customerId);
                    $req->setAssetTypeId($typeId);
                    $req->setCreateNewAccountIfMissed();
                    $resp = $this->get($req);
                    $accData = $resp->get();
                    $this->cachedRepresentAccs[$accData[Account::ATTR_ASSET_TYPE_ID]] = new Account($accData);
                    $result->set($accData);
                    $result->markSucceed();
                }
            }
        } else {
            $this->logger->error("Asset type is not defined (asset code: $typeCode).");
        }
        if ($result->isSucceed()) {
            $repAccId = $result->getId();
            $this->logger->info("Representative account #$repAccId is found.");
        }
        $this->logger->info("'Get representative account' operation is completed.");
        return $result;
    }

}