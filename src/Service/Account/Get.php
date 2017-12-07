<?php
/**
 *
 */

namespace Praxigento\Accounting\Service\Account;

use Praxigento\Accounting\Api\Service\Account\Get\Request as ARequest;
use Praxigento\Accounting\Api\Service\Account\Get\Response as AResponse;
use Praxigento\Accounting\Repo\Entity\Data\Account as EAccount;

/**
 * Get account data or create new account if customer has no account for the requested asset.
 */
class Get
    implements \Praxigento\Accounting\Api\Service\Account\Get
{
    /** @var array save accounts data for representative customer. */
    private $cachedRepresentAccs = [];
    /** @var  \Praxigento\Accounting\Repo\Entity\Account */
    private $repoAccount;
    /** @var \Praxigento\Accounting\Repo\Entity\Type\Asset */
    private $repoTypeAsset;

    public function __construct(
        \Praxigento\Accounting\Repo\Entity\Account $repoAccount,
        \Praxigento\Accounting\Repo\Entity\Type\Asset $repoTypeAsset
    )
    {
        $this->repoAccount = $repoAccount;
        $this->repoTypeAsset = $repoTypeAsset;
    }

    /**
     * Perform DB to API data conversion directly.
     *
     * @param \Praxigento\Accounting\Repo\Entity\Data\Account $acc
     * @return \Praxigento\Accounting\Api\Service\Account\Get\Response
     */
    private function composeResult(EAccount $acc)
    {
        /** define local working data */
        $accId = $acc->getId();
        $custId = $acc->getCustomerId();
        $balance = $acc->getBalance();
        $typeId = $acc->getAssetTypeId();

        /** compose result */
        $result = new AResponse();
        $result->setId($accId);
        $result->setCustomerId($custId);
        $result->setBalance($balance);
        $result->setAssetTypeId($typeId);
        return $result;
    }

    /**
     * Get account data or create new account if customer has no account for the requested asset.
     *
     * @param ARequest $req
     *
     * @return AResponse
     */
    public function exec($req)
    {
        assert($req instanceof ARequest);
        /** define local working data */
        $typeCode = $req->getAssetTypeCode();
        $typeId = $req->getAssetTypeId();
        $custId = $req->getCustomerId();
        $isRepres = $req->getIsRepresentative();

        /** perform processing */
        if (is_null($typeId)) {
            /* get type ID by code if missed */
            $typeId = $this->repoTypeAsset->getIdByCode($typeCode);
        }

        /* return cached data for representative customer if exists */
        if (
            $isRepres &&
            isset($this->cachedRepresentAccs[$typeId])
        ) {
            $result = $this->cachedRepresentAccs[$typeId];
        } else {
            /* ... or get data from DB */
            if ($isRepres) {
                /* get representative customer ID */
                $custId = $this->repoAccount->getRepresentativeCustomerId();
            }
            $account = $this->getAccount($custId, $typeId);
            $result = $this->composeResult($account);
            /* cache data for representative customer */
            if ($isRepres) {
                $this->cachedRepresentAccs[$typeId] = $result;
            }
        }

        /** compose result */
        return $result;
    }

    /**
     * Get account by $custId & asset $typeId or create new one if not found.
     *
     * @param int $custId
     * @param int $typeId
     * @return EAccount
     */
    private function getAccount($custId, $typeId)
    {
        /** perform processing & compose result */
        /* get account by customerId & assetTypeId */
        $result = $this->repoAccount->getByCustomerId($custId, $typeId);
        if (!$result) {
            /* create new account */
            $result = new EAccount();
            $result->setCustomerId($custId);
            $result->setAssetTypeId($typeId);
            $result->setBalance(0);
            $accId = $this->repoAccount->create($result);
            $result->setId($accId);
        }
        return $result;
    }

}