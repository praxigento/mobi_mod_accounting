<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Lib\Service\Account;

use Praxigento\Accounting\Data\Entity\Account as Account;
use Praxigento\Accounting\Lib\Service\IAccount;
use Praxigento\Core\Service\Base\Call as BaseCall;

class Call extends BaseCall implements IAccount
{
    /** @var \Praxigento\Accounting\Repo\Entity\Type\IAsset */
    protected $_repoTypeAsset;
    /** @var \Praxigento\Accounting\Lib\Repo\IModule */
    protected $_repoMod;
    /** @var array save accounts data for representative customer. */
    protected $_cachedRepresentativeAccs = [];
    /** @var  \Praxigento\Accounting\Repo\Entity\IAccount */
    protected $_repoAccount;
    /** @var \Praxigento\Core\Repo\IGeneric */
    protected $_repoBasic;

    /**
     * Call constructor.
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Praxigento\Core\Repo\IGeneric $repoBasic,
        \Praxigento\Accounting\Repo\Entity\IAccount $repoAccount,
        \Praxigento\Accounting\Repo\Entity\Type\IAsset $repoTypeAsset,
        \Praxigento\Accounting\Lib\Repo\IModule $repoMod
    ) {
        parent::__construct($logger);
        $this->_repoBasic = $repoBasic;
        $this->_repoAccount = $repoAccount;
        $this->_repoTypeAsset = $repoTypeAsset;
        $this->_repoMod = $repoMod;
    }

    public function cacheReset()
    {
        $this->_cachedRepresentativeAccs = [];
        $this->_repoMod->cacheReset();
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
        $this->_logger->info("'Get account' operation is called.");
        $accountId = $request->getAccountId();
        $customerId = $request->getCustomerId();
        $assetTypeId = $request->getAssetTypeId();
        $assetTypeCode = $request->getAssetTypeCode();
        $createNewAccountIfMissed = $request->getCreateNewAccountIfMissed();
        /* accountId has the highest priority */
        if ($accountId) {
            $data = $this->_repoAccount->getById($accountId);
        } else {
            /* try to look up by customer id & asset type id */
            if (!$assetTypeId) {
                /* get asset type ID by asset code */
                $assetTypeId = $this->_repoTypeAsset->getIdByCode($assetTypeCode);
            }
            /* get account by customerId & assetTypeId */
            $data = $this->_repoAccount->getByCustomerId($customerId, $assetTypeId);
        }
        /* analyze found data */
        if (is_array($data)) {
            $result->setData($data);
            $result->markSucceed();
        } else {
            if ($createNewAccountIfMissed) {
                /* not found - add new account */
                $data = [
                    Account::ATTR_CUST_ID => $customerId,
                    Account::ATTR_ASSET_TYPE_ID => $assetTypeId,
                    Account::ATTR_BALANCE => 0
                ];
                $created = $this->_repoAccount->create($data);
                $accId = $created[Account::ATTR_ID];
                $result->setData($created);
                $result->markSucceed();
                $this->_logger->info("There is no account for customer #{$customerId} and asset type #$assetTypeId. New account #$accId is created.");
            }
        }
        $this->_logger->info("'Get account' operation is completed.");
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
        $this->_logger->info("'Get representative account' operation is called.");
        if (is_null($typeId)) {
            $typeId = $this->_repoTypeAsset->getIdByCode($typeCode);
        }
        if (!is_null($typeId)) {
            if (isset($this->_cachedRepresentativeAccs[$typeId])) {
                $result->setData($this->_cachedRepresentativeAccs[$typeId]);
                $result->markSucceed();
            } else {
                /* there is no cached data yet */
                /* get representative customer ID */
                $customerId = $this->_repoMod->getRepresentativeCustomerId();
                /* get all accounts for the representative customer */
                $where = Account::ATTR_CUST_ID . '=' . (int)$customerId;
                $data = $this->_repoBasic->getEntities(Account::ENTITY_NAME, null, $where);
                if ($data) {
                    $mapped = [];
                    foreach ($data as $one) {
                        $mapped[$one[Account::ATTR_ASSET_TYPE_ID]] = $one;
                    }
                    $this->_cachedRepresentativeAccs = $mapped;
                }
                /* check selected accounts */
                if (isset($this->_cachedRepresentativeAccs[$typeId])) {
                    $result->setData($this->_cachedRepresentativeAccs[$typeId]);
                    $result->markSucceed();
                } else {
                    /* there is no accounts yet */
                    $req = new Request\Get();
                    $req->setCustomerId($customerId);
                    $req->setAssetTypeId($typeId);
                    $req->setCreateNewAccountIfMissed();
                    $resp = $this->get($req);
                    $accData = $resp->getData();
                    $this->_cachedRepresentativeAccs[$accData[Account::ATTR_ASSET_TYPE_ID]] = $accData;
                    $result->setData($accData);
                    $result->markSucceed();
                }
            }
        } else {
            $this->_logger->error("Asset type is not defined (asset code: $typeCode).");
        }
        if ($result->isSucceed()) {
            $repAccId = $result->getData(Account::ATTR_ID);
            $this->_logger->info("Representative account #$repAccId is found.");
        }
        $this->_logger->info("'Get representative account' operation is completed.");
        return $result;
    }

    /**
     * @param Request\UpdateBalance $request
     *
     * @return Response\UpdateBalance
     * @deprecated This operation is used inside Accounting module only (use Account repo inside module).
     */
    public function updateBalance(Request\UpdateBalance $request)
    {
        $result = new Response\UpdateBalance();
        $accountId = $request->getAccountId();
        $changeValue = $request->getChangeValue();

        $rowsUpdated = $this->_repoAccount->updateBalance($accountId, $changeValue);
        if ($rowsUpdated) {
            $result->setData(['rows_updated' => $rowsUpdated]);
            $result->markSucceed();
        }
        return $result;
    }
}