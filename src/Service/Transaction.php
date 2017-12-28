<?php
/**
 * User: makhovdmitrii@inbox.ru
 */

namespace Praxigento\Accounting\Service;

use Praxigento\Accounting\Repo\Entity\Data\Transaction as ETransaction;
use Praxigento\Accounting\Service\Transaction\Request as ARequest;
use Praxigento\Accounting\Service\Transaction\Response as AResponse;


class Transaction
{

    /** @var  \Praxigento\Core\App\Transaction\Database\IManager */
    private $manTrans;
    /** @var  \Praxigento\Accounting\Repo\Entity\Account */
    private $repoAcc;
    /** @var  \Praxigento\Accounting\Repo\Entity\Transaction */
    private $repoTrans;

    public function __construct(
        \Praxigento\Core\App\Transaction\Database\IManager $manTrans,
        \Praxigento\Accounting\Repo\Entity\Account $repoAcc,
        \Praxigento\Accounting\Repo\Entity\Transaction $repoTrans
    )
    {
        $this->manTrans = $manTrans;
        $this->repoAcc = $repoAcc;
        $this->repoTrans = $repoTrans;
    }

    /**
     * Add new transaction and update current balances.
     * @param ARequest $request
     * @return AResponse
     * @throws \Exception
     */
    public function exec($request)
    {
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
            $debitAcc = $this->repoAcc->getById($debitAccId);
            $debitAssetTypeId = $debitAcc->getAssetTypeId();
            /* get account type for credit account */
            $creditAcc = $this->repoAcc->getById($creditAccId);
            $creditAssetTypeId = $creditAcc->getAssetTypeId();
            /* asset types should be equals */
            if (
                !is_null($debitAssetTypeId) &&
                ($debitAssetTypeId == $creditAssetTypeId)
            ) {
                /* add transaction */
                $toAdd = [
                    ETransaction::ATTR_OPERATION_ID => $operationId,
                    ETransaction::ATTR_DEBIT_ACC_ID => $debitAccId,
                    ETransaction::ATTR_CREDIT_ACC_ID => $creditAccId,
                    ETransaction::ATTR_VALUE => $value,
                    ETransaction::ATTR_DATE_APPLIED => $dateApplied
                ];
                if (!is_null($note)) {
                    $toAdd[ETransaction::ATTR_NOTE] = $note;
                }
                $idCreated = $this->repoTrans->create($toAdd);
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
