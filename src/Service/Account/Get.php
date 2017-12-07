<?php
/*
 * Get account data or create new account if customer has no account for the requested asset.
 */


namespace Praxigento\Accounting\Service\Account;

use Praxigento\Accounting\Api\Service\Account\Get\Request as ARequest;
use Praxigento\Accounting\Api\Service\Account\Get\Response as AResponse;
use Praxigento\Accounting\Repo\Entity\Data\Account as Account;

class Get
    implements \Praxigento\Accounting\Api\Service\Account\Get
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
        \Praxigento\Accounting\Repo\Entity\Account $repoAccount,
        \Praxigento\Accounting\Repo\Entity\Type\Asset $repoTypeAsset
    )
    {
        $this->repoAccount = $repoAccount;
        $this->repoTypeAsset = $repoTypeAsset;
    }

    /**
     * Get account data or create new account if customer has no account for the requested asset.
     *
     * @param ARequest $request
     *
     * @return AResponse
     */
    public function exec(\Praxigento\Accounting\Api\Service\Account\Get\Request $data)
    {
        if ($data->getIsRepresentative() === TRUE) {
            $result = new AResponse();
            $typeId = $data->getAssetTypeId();
            $typeCode = $data->getAssetTypeCode();
            if (is_null($typeId)) {
                $typeId = $this->repoTypeAsset->getIdByCode($typeCode);
            }
            if (!is_null($typeId)) {
                if (isset($this->cachedRepresentAccs[$typeId])) {
                    $result->set($this->cachedRepresentAccs[$typeId]);
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
                    } else {
                        /* there is no accounts yet */
                        $req = new ARequest();
                        $req->setCustomerId($customerId);
                        $req->setAssetTypeId($typeId);
                        $req->setCreateNewAccountIfMissed();
                        $resp = $this->get($req);
                        $accData = $resp->get();
                        $this->cachedRepresentAccs[$accData[Account::ATTR_ASSET_TYPE_ID]] = new Account($accData);
                        $result->set($accData);
                    }
                }
            } else {
                // "Asset type is not defined (asset code: $typeCode)."
            }
            return $result;
        } else {
            return $this->get($data);
        }
    }

    private function get(\Praxigento\Accounting\Api\Service\Account\Get\Request $data)
    {
        $result = new AResponse();
        $accountId = $data->getAccountId();
        $customerId = $data->getCustomerId();
        $assetTypeId = $data->getAssetTypeId();
        $assetTypeCode = $data->getAssetTypeCode();
        $createNewAccIfMissed = $data->getCreateNewAccountIfMissed();
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
            }
        }
        return $result;
    }

}