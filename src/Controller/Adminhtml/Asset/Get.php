<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Controller\Adminhtml\Asset;

use Praxigento\Accounting\Api\Web\Account\Asset\Get\Request as ARequest;
use Praxigento\Accounting\Api\Web\Account\Asset\Get\Response as AResponse;

class Get
    extends \Praxigento\Core\App\Action\Back\Api\Base
{

    /** @var \Praxigento\Core\App\Api\Web\IAuthenticator */
    private $authenticator;
    /** @var \Praxigento\Accounting\Service\Account\Asset\Get */
    private $servAssetGet;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Praxigento\Core\App\Api\Web\IAuthenticator $authenticator,
        \Praxigento\Accounting\Service\Account\Asset\Get $servAssetGet
    ) {
        parent::__construct($context);
        $this->authenticator = $authenticator;
        $this->servAssetGet = $servAssetGet;
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
        $customerId = $data->getCustomerId();

        /* get currently logged in users */

        /* analyze logged in users */

        /** perform processing */
        $req = new \Praxigento\Accounting\Service\Account\Asset\Get\Request();
        $req->setCustomerId($customerId);
        $resp = $this->servAssetGet->exec($req);

        /** compose result */
        $result = new AResponse();
        $result->setData($resp);
        return $result;
    }
}