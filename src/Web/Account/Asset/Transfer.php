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
        \Praxigento\Core\App\Api\Web\Authenticator\Front $auth,
        \Praxigento\Accounting\Service\Account\Asset\Transfer $servAssetTransfer
    ) {
        $this->auth = $auth;
        $this->servAssetTransfer = $servAssetTransfer;
    }


    public function exec($request)
    {
        assert($request instanceof ARequest);
        /** define local working data */
        $data = $request->getData();
        $amount = $data->getAmount();
        $assetTypeId = $data->getAssetId();
        $counterPartyId = $data->getCounterPartyId();

        /* input data filters */
        $amount = abs($amount); // customer cannot transfer TO his account
        $isDirect = false; // customer cannot initiate direct transfer
        $custId = $this->auth->getCurrentUserId($request); // customer can transfer FROM his account only

        /** perform processing */
        $req = new \Praxigento\Accounting\Service\Account\Asset\Transfer\Request();
        $req->setAmount($amount);
        $req->setAssetId($assetTypeId);
        $req->setCounterPartyId($counterPartyId);
        $req->setCustomerId($custId);
        $req->setIsDirect($isDirect);
        $resp = $this->servAssetTransfer->exec($req);

        /** compose result */
        $result = new AResponse();
        $result->setData($resp);
        return $result;
    }

}