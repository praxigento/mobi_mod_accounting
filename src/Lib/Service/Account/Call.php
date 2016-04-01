<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Lib\Service\Account;

use Praxigento\Accounting\Data\Entity\Account as Account;
use Praxigento\Accounting\Lib\Service\IAccount;
use Praxigento\Accounting\Lib\Service\Type\Asset\Request\GetByCode as TypeAssetRequestGetByCode;
use Praxigento\Core\Lib\Service\Base\Call as BaseCall;
use Praxigento\Core\Lib\Service\Repo\Request\GetEntities as GetEntitiesRequest;

class Call extends BaseCall implements IAccount
{
    /** @var \Praxigento\Accounting\Repo\Type\IAsset */
    protected $_repoTypeAsset;
    /** @var \Praxigento\Accounting\Lib\Repo\IModule */
    protected $_repoMod;
    /** @var array save accounts data for representative customer. */
    protected $_cachedRepresentativeAccs = [];
    /** @var  \Praxigento\Accounting\Repo\IAccount */
    protected $_repoAccount;

    /**
     * Call constructor.
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Praxigento\Accounting\Repo\IAccount $repoAccount,
        \Praxigento\Accounting\Repo\Type\IAsset $repoTypeAsset,
        \Praxigento\Accounting\Lib\Repo\IModule $repoMod
    ) {
        parent::__construct($logger);
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
            $result->setAsSucceed();
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
                $result->setAsSucceed();
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
            $reqCode = new TypeAssetRequestGetByCode($typeCode);
            $respCode = $this->_repoTypeAsset->getByCode($reqCode);
            $typeId = $respCode->getId();
        }
        if (!is_null($typeId)) {
            if (isset($this->_cachedRepresentativeAccs[$typeId])) {
                $result->setData($this->_cachedRepresentativeAccs[$typeId]);
                $result->setAsSucceed();
            } else {
                /* there is no cached data yet */
                /* get representative customer ID */
                $customerId = $this->_repoMod->getRepresentativeCustomerId();
                /* get all accounts for the representative customer */
                $where = Account::ATTR_CUST_ID . '=' . $customerId;
                $req = new GetEntitiesRequest(Account::ENTITY_NAME, $where);
                $resp = $this->_callRepo->getEntities($req);
                if ($resp->isSucceed()) {
                    $mapped = [];
                    foreach ($resp->getData() as $one) {
                        $mapped[$one[Account::ATTR_ASSET_TYPE_ID]] = $one;
                    }
                    $this->_cachedRepresentativeAccs = $mapped;
                }
                /* check selected accounts */
                if (isset($this->_cachedRepresentativeAccs[$typeId])) {
                    $result->setData($this->_cachedRepresentativeAccs[$typeId]);
                    $result->setAsSucceed();
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
                    $result->setAsSucceed();
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
     */
    public function updateBalance(Request\UpdateBalance $request)
    {
        $result = new Response\UpdateBalance();
        $accountId = $request->getAccountId();
        $changeValue = $request->getChangeValue();
        $accId = $this->_getConn()->quote($accountId, \Zend_Db::INT_TYPE);
        $value = $this->_getConn()->quote($changeValue, \Zend_Db::FLOAT_TYPE);
        if ($accId) {
            $tbl = $this->_getTableName(Account::ENTITY_NAME);
            if ($value < 0) {
                $exp = new \Zend_Db_Expr(Account::ATTR_BALANCE . '-' . abs($value));
            } else {
                $exp = new \Zend_Db_Expr(Account::ATTR_BALANCE . '+' . abs($value));
            }
            $bind = [Account::ATTR_BALANCE => $exp];
            $where = Account::ATTR_ID . '=' . $accId;
            $rowsUpdated = $this->_getConn()->update($tbl, $bind, $where);
            if ($rowsUpdated) {
                $result->setData(['rows_updated' => $rowsUpdated]);
                $result->setAsSucceed();
            }
        }
        return $result;
    }
}