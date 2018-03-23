<?php
/**
 * User: makhovdmitrii@inbox.ru
 */

namespace Praxigento\Accounting\Service;

use Praxigento\Accounting\Repo\Data\Transaction as ETransaction;
use Praxigento\Accounting\Service\Transaction\Request as ARequest;
use Praxigento\Accounting\Service\Transaction\Response as AResponse;


class Transaction
{

    /** @var  \Praxigento\Core\Api\App\Repo\Transaction\Manager */
    private $manTrans;
    /** @var  \Praxigento\Accounting\Repo\Dao\Account */
    private $daoAcc;
    /** @var  \Praxigento\Accounting\Repo\Dao\Transaction */
    private $daoTrans;

    public function __construct(
        \Praxigento\Core\Api\App\Repo\Transaction\Manager $manTrans,
        \Praxigento\Accounting\Repo\Dao\Account $daoAcc,
        \Praxigento\Accounting\Repo\Dao\Transaction $daoTrans
    ) {
        $this->manTrans = $manTrans;
        $this->daoAcc = $daoAcc;
        $this->daoTrans = $daoTrans;
    }

    /**
     * Add new transaction and update current balances.
     * @param ARequest $request
     * @return AResponse
     * @throws \Exception
     */
    public function exec($request)
    {
        assert($request instanceof ARequest);
        $result = new AResponse();
        $debitAccId = $request->getDebitAccId();
        $creditAccId = $request->getCreditAccId();
        $operationId = $request->getOperationId();
        $dateApplied = $request->getDateApplied();
        $value = $request->getValue();
        $note = $request->getNote();
        $def = $this->manTrans->begin();
        try {
            /* get account type for debit account */
            $debitAcc = $this->daoAcc->getById($debitAccId);
            $debitAssetTypeId = $debitAcc->getAssetTypeId();
            /* get account type for credit account */
            $creditAcc = $this->daoAcc->getById($creditAccId);
            $creditAssetTypeId = $creditAcc->getAssetTypeId();
            /* asset types should be equals */
            if (
                !is_null($debitAssetTypeId) &&
                ($debitAssetTypeId == $creditAssetTypeId)
            ) {
                /* add transaction */
                $toAdd = [
                    ETransaction::A_OPERATION_ID => $operationId,
                    ETransaction::A_DEBIT_ACC_ID => $debitAccId,
                    ETransaction::A_CREDIT_ACC_ID => $creditAccId,
                    ETransaction::A_VALUE => $value,
                    ETransaction::A_DATE_APPLIED => $dateApplied
                ];
                if (!is_null($note)) {
                    $toAdd[ETransaction::A_NOTE] = $note;
                }
                $idCreated = $this->daoTrans->create($toAdd);
                $result->setTransactionId($idCreated);
            } else {
                throw new \Exception("Asset type (#$debitAssetTypeId) for debit account #$debitAccId is not equal to "
                    . "asset type (#$creditAssetTypeId) for credit account $creditAccId.");
            }
            $this->manTrans->commit($def);
            $result->markSucceed();
        } finally {
            $this->manTrans->end($def);

        }
        return $result;
    }

}
