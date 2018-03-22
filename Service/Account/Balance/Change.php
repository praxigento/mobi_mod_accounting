<?php
/**
 * File creator: makhovdmitrii@inbox.ru
 */

namespace Praxigento\Accounting\Service\Account\Balance;

use Praxigento\Accounting\Config as Cfg;
use Praxigento\Accounting\Service\Account\Balance\Change\Request as ARequest;
use Praxigento\Accounting\Service\Account\Balance\Change\Response as AResponse;

class Change
{
    /** @var \Praxigento\Core\Api\Helper\Date */
    private $hlpDate;
    /** @var \Praxigento\Core\Api\App\Repo\Transaction\Manager */
    private $manTrans;
    /** @var \Praxigento\Accounting\Repo\Dao\Account */
    private $repoAccount;
    /** @var \Praxigento\Accounting\Repo\Dao\Log\Change\Admin */
    private $repoLogChangeAdmin;
    /** @var \Praxigento\Accounting\Repo\Dao\Operation */
    private $repoOperation;
    /** @var \Praxigento\Accounting\Repo\Dao\Transaction */
    private $repoTransaction;
    /** @var \Praxigento\Accounting\Repo\Dao\Type\Operation */
    private $repoTypeOper;

    public function __construct(
        \Praxigento\Core\Api\App\Logger\Main $logger,
        \Praxigento\Core\Api\App\Repo\Transaction\Manager $manTrans,
        \Praxigento\Core\Api\Helper\Date $hlpDate,
        \Praxigento\Accounting\Repo\Dao\Account $repoAccount,
        \Praxigento\Accounting\Repo\Dao\Operation $repoOperation,
        \Praxigento\Accounting\Repo\Dao\Transaction $repoTransaction,
        \Praxigento\Accounting\Repo\Dao\Type\Operation $repoTypeOper,
        \Praxigento\Accounting\Repo\Dao\Log\Change\Admin $repoLogChangeAdmin
    ) {
        $this->manTrans = $manTrans;
        $this->hlpDate = $hlpDate;
        $this->repoAccount = $repoAccount;
        $this->repoOperation = $repoOperation;
        $this->repoTransaction = $repoTransaction;
        $this->repoTypeOper = $repoTypeOper;
        $this->repoLogChangeAdmin = $repoLogChangeAdmin;
    }

    /**
     * @param ARequest $request
     * @return AResponse
     */
    public function exec($request)
    {
        assert($request instanceof ARequest);
        $result = new AResponse();
        $accCustId = $request->getCustomerAccountId();
        $adminUserId = $request->getAdminUserId();
        $value = $request->getChangeValue();
        $def = $this->manTrans->begin();
        try {
            /* get account's asset type by ID */
            $assetTypeId = $this->repoAccount->getAssetTypeId($accCustId);
            /* get system account id for given asset type */
            $accSysId = $this->repoAccount->getSystemAccountId($assetTypeId);
            /* get operation type by code and date performed */
            $operTypeId = $this->repoTypeOper->getIdByCode(Cfg::CODE_TYPE_OPER_CHANGE_BALANCE);
            $dateNow = $this->hlpDate->getUtcNowForDb();
            /* create operation */
            $operation = new \Praxigento\Accounting\Repo\Data\Operation();
            $operation->setTypeId($operTypeId);
            $operation->setDatePerformed($dateNow);
            $operId = $this->repoOperation->create($operation);
            /* create transaction */
            $trans = new \Praxigento\Accounting\Repo\Data\Transaction();
            $trans->setOperationId($operId);
            $trans->setDateApplied($dateNow);
            if ($value > 0) {
                $trans->setDebitAccId($accSysId);
                $trans->setCreditAccId($accCustId);
            } else {
                $trans->setDebitAccId($accCustId);
                $trans->setCreditAccId($accSysId);
            }
            $trans->setValue(abs($value));
            $this->repoTransaction->create($trans);
            /* log details (operator name who performs the operation) */
            $log = new \Praxigento\Accounting\Repo\Data\Log\Change\Admin();
            $log->setOperationRef($operId);
            $log->setUserRef($adminUserId);
            $this->repoLogChangeAdmin->create($log);
            $this->manTrans->commit($def);
            $result->markSucceed();
        } finally {
            $this->manTrans->end($def);
        }
        return $result;
    }
}