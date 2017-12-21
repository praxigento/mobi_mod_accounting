<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Web\Account\Asset;

use Praxigento\Accounting\Api\Web\Account\Asset\Transfer\Request as ARequest;
use Praxigento\Accounting\Api\Web\Account\Asset\Transfer\Response as AResponse;

class Transfer
    implements \Praxigento\Accounting\Api\Web\Account\Asset\TransferInterface
{
    /** @var \Praxigento\Core\App\Api\Web\IAuthenticator */
    private $auth;
    /** @var \Praxigento\Accounting\Service\Account\Asset\Transfer */
    private $servAssetTransfer;

    public function __construct(
        \Praxigento\Core\App\Api\Web\IAuthenticator $auth,
        \Praxigento\Accounting\Service\Account\Asset\Transfer $servAssetTransfer
    ) {
        $this->auth = $auth;
        $this->servAssetTransfer = $servAssetTransfer;
    }


    public function exec($request) {
        assert($request instanceof ARequest);
        /** define local working data */
        $data = $request->getData();
        $amount = $data->getAmount();
        $assetTypeId = $data->getAssetId();
        $counterPartyId = $data->getCounterPartyId();
        $custId = $data->getCustomerId();
        $isDirect = $data->getIsDirect();

        /** TODO: add access rights validation */
        $reqAdminId = $this->auth->getCurrentAdminId($request);
        $reqCustId = $this->auth->getCurrentCustomerId($request);

        /** perform processing */
        $resp = $this->transfer($amount, $assetTypeId, $counterPartyId, $custId, $isDirect, $reqAdminId);

        /** compose result */
        $result = new AResponse();
        $result->setData($resp);
        return $result;
    }

    private function transfer($amount, $assetTypeId, $cPartyId, $custId, $isDirect, $userId) {
        $req = new \Praxigento\Accounting\Service\Account\Asset\Transfer\Request();
        $req->setAmount($amount);
        $req->setAssetId($assetTypeId);
        $req->setCounterPartyId($cPartyId);
        $req->setCustomerId($custId);
        $req->setIsDirect($isDirect);
        $req->setUserId($userId);
        $resp = $this->servAssetTransfer->exec($req);
        return $resp;
    }
}