<?php
/**
 * File creator: makhovdmitrii@inbox.ru
 */

namespace Praxigento\Accounting\Service\Account\Balance;

use Praxigento\Accounting\Service\Account\Balance\Change\Request as CRequest;
use Praxigento\Accounting\Service\Account\Balance\Change\Response as CResponse;

class Change
{
    /** @var \Praxigento\Accounting\Repo\Entity\Account */
    protected $repoAccount;
    /** @var \Praxigento\Accounting\Repo\Entity\Log\Change\Admin */
    protected $repoLogChangeAdmin;
    /** @var \Praxigento\Accounting\Repo\Entity\Operation */
    protected $repoOperation;
    /** @var \Praxigento\Accounting\Repo\Entity\Transaction */
    protected $repoTransaction;
    /** @var \Praxigento\Accounting\Repo\Entity\Type\Operation */
    protected $repoTypeOper;
    /** @var \Praxigento\Core\Tool\IDate */
    protected $toolDate;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Praxigento\Core\App\Transaction\Database\IManager $manTrans,
        \Praxigento\Core\Tool\IDate $toolDate,
        \Praxigento\Accounting\Repo\Entity\Account $repoAccount,
        \Praxigento\Accounting\Repo\Entity\Operation $repoOperation,
        \Praxigento\Accounting\Repo\Entity\Transaction $repoTransaction,
        \Praxigento\Accounting\Repo\Entity\Type\Operation $repoTypeOper,
        \Praxigento\Accounting\Repo\Entity\Log\Change\Admin $repoLogChangeAdmin
    )
    {
        $this->manTrans = $manTrans;
        $this->toolDate = $toolDate;
        $this->repoAccount = $repoAccount;
        $this->repoOperation = $repoOperation;
        $this->repoTransaction = $repoTransaction;
        $this->repoTypeOper = $repoTypeOper;
        $this->repoLogChangeAdmin = $repoLogChangeAdmin;
    }

    /**
     * @param CRequest $request
     * @return CResponse
     */
    public function exec($request)
    {
        $result = new CResponse();
        $accCustId = $request->getCustomerAccountId();
        $adminUserId = $request->getAdminUserId();
        $value = $request->getChangeValue();
        $def = $this->manTrans->begin();
        try {
            /* get account's asset type by ID */
            $assetTypeId = $this->repoAccount->getAssetTypeId($accCustId);
            /* get representative account id for given asset type */
            $accRepresId = $this->repoAccount->getRepresentativeAccountId($assetTypeId);
            /* get operation type by code and date performed */
            $operTypeId = $this->repoTypeOper->getIdByCode(\Praxigento\Accounting\Config::CODE_TYPE_OPER_CHANGE_BALANCE);
            $dateNow = $this->toolDate->getUtcNowForDb();
            /* create operation */
            $operation = new \Praxigento\Accounting\Repo\Entity\Data\Operation();
            $operation->setTypeId($operTypeId);
            $operation->setDatePerformed($dateNow);
            $operId = $this->repoOperation->create($operation);
            /* create transaction */
            $trans = new \Praxigento\Accounting\Repo\Entity\Data\Transaction();
            $trans->setOperationId($operId);
            $trans->setDateApplied($dateNow);
            if ($value > 0) {
                $trans->setDebitAccId($accRepresId);
                $trans->setCreditAccId($accCustId);
            } else {
                $trans->setDebitAccId($accCustId);
                $trans->setCreditAccId($accRepresId);
            }
            $trans->setValue(abs($value));
            $this->repoTransaction->create($trans);
            /* log details (operator name who performs the operation) */
            $log = new \Praxigento\Accounting\Repo\Entity\Data\Log\Change\Admin();
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