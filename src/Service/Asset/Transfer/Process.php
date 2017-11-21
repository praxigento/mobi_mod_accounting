<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Service\Asset\Transfer;

use Praxigento\Accounting\Api\Asset\Transfer\Process\Request as ARequest;
use Praxigento\Accounting\Api\Asset\Transfer\Process\Response as AResponse;

class Process
    implements \Praxigento\Accounting\Api\Asset\Transfer\ProcessInterface
{


    public function __construct()
    {

    }

    public function exec(ARequest $data)
    {
        /* define local working data */

        /* perform processing */

        /* compose result */
        $result = new AResponse();
        return $result;
    }
}