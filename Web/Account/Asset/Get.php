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
        \Praxigento\Core\App\Api\Web\Authenticator\Front $auth,
        \Praxigento\Accounting\Service\Account\Asset\Get $servAssetGet
    ) {
        $this->auth = $auth;
        $this->servAssetGet = $servAssetGet;
    }


    public function exec($request)
    {
        assert($request instanceof ARequest);
        /** define local working data */

        /* customer can get assets data for itself only */
        $custId = $this->auth->getCurrentUserId($request);

        /** perform processing */
        $req = new \Praxigento\Accounting\Service\Account\Asset\Get\Request();
        $req->setCustomerId($custId);
        $resp = $this->servAssetGet->exec($req);

        /** compose result */
        $result = new AResponse();
        $result->setData($resp);
        return $result;
    }

}