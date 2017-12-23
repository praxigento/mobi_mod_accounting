<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Controller\Adminhtml\Asset;

use Praxigento\Accounting\Api\Web\Account\Asset\Transfer\Request as ARequest;
use Praxigento\Accounting\Api\Web\Account\Asset\Transfer\Response as AResponse;

class Transfer
    extends \Praxigento\Core\App\Action\Back\Api\Base
{

    /** @var \Praxigento\Core\App\Api\Web\IAuthenticator */
    private $authenticator;
    /** @var \Praxigento\Accounting\Service\Account\Asset\Transfer */
    private $servAssetTransfer;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Praxigento\Core\App\Api\Web\IAuthenticator $authenticator,
        \Praxigento\Accounting\Service\Account\Asset\Transfer $servAssetTransfer
    ) {
        parent::__construct($context);
        $this->authenticator = $authenticator;
        $this->servAssetTransfer = $servAssetTransfer;
    }

    protected function getInDataType(): string
    {
        return ARequest::class;
    }

    protected function getOutDataType(): string
    {
        return AResponse::class;
    }

    protected function process($request)
    {
        assert($request instanceof ARequest);
        /** define local working data */
        $data = $request->getData();
        $amount = $data->getAmount();
        $assetTypeId = $data->getAssetId();
        $counterPartyId = $data->getCounterPartyId();
        $custId = $data->getCustomerId();
        $isDirect = $data->getIsDirect();

        /* get currently logged in users */
        $userId = 1; // TODO: get from current session

        /* analyze logged in users */

        /** perform processing */
        $req = new \Praxigento\Accounting\Service\Account\Asset\Transfer\Request();
        $req->setAmount($amount);
        $req->setAssetId($assetTypeId);
        $req->setCounterPartyId($counterPartyId);
        $req->setCustomerId($custId);
        $req->setIsDirect($isDirect);
        $req->setUserId($userId);
        $resp = $this->servAssetTransfer->exec($req);

        /** compose result */
        $result = new AResponse();
        $result->setData($resp);
        return $result;
    }
}