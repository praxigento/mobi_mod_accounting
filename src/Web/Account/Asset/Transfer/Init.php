<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Web\Account\Asset\Transfer;

use Praxigento\Core\Api\Web\Customer\Search\ByKey\Request as ARequest;
use Praxigento\Core\Api\Web\Customer\Search\ByKey\Response as AResponse;

class Init
    implements \Praxigento\Accounting\Api\Web\Account\Asset\Transfer\InitInterface
{
    /** @var \Praxigento\Accounting\Api\Service\Asset\Transfer\Init */
    private $servInit;

    public function __construct(
        \Praxigento\Accounting\Api\Service\Asset\Transfer\Init $servInit
    ) {
        $this->servInit = $servInit;
    }

    public function exec($request) {
        assert($request instanceof ARequest);
        /** define local working data */
        $data = $request->getData();
        $dev = $request->getDev();
        $adminId = $dev->getAdminId();
        $custId = $dev->getCustId();

        /** perform processing */

        /** compose result */
        $result = new AResponse();
        return $result;
    }

}