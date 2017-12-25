<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Web\Account\Asset;

use Praxigento\Accounting\Api\Web\Account\Asset\Get\Request as ARequest;
use Praxigento\Accounting\Api\Web\Account\Asset\Get\Response as AResponse;

class Get
    implements \Praxigento\Accounting\Api\Web\Account\Asset\GetInterface
{
    /** @var \Praxigento\Core\App\Api\Web\IAuthenticator */
    private $auth;
    /** @var \Praxigento\Accounting\Service\Account\Asset\Get */
    private $servAssetGet;

    public function __construct(
        \Praxigento\Core\App\Api\Web\IAuthenticator $auth,
        \Praxigento\Accounting\Service\Account\Asset\Get $servAssetGet
    ) {
        $this->auth = $auth;
        $this->servAssetGet = $servAssetGet;
    }


    public function exec($request) {
        assert($request instanceof ARequest);
        /** define local working data */
        $data = $request->getData();
        $custId = $data->getCustomerId();

        /** TODO: add access rights validation */
        $reqCustId = $this->auth->getCurrentUserId($request);

        /** perform processing */
        $items = $this->getAssets($reqCustId);

        /** compose result */
        $result = new AResponse();
        $result->setData($items);
        return $result;
    }

    private function getAssets($custId) {
        $req = new \Praxigento\Accounting\Service\Account\Asset\Get\Request();
        $req->setCustomerId($custId);
        $resp = $this->servAssetGet->exec($req);
        return $resp;
    }
}