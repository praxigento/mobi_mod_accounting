<?php
/**
 * File creator: makhovdmitrii@inbox.ru
 */

namespace Praxigento\Accounting\Service\Account\Balance;

use Praxigento\Accounting\Service\Account\Balance\OnDate\Request as ARequest;
use Praxigento\Accounting\Service\Account\Balance\OnDate\Response as AResponse;

class OnDate
{
    /** @var \Praxigento\Accounting\Repo\Entity\Balance */
    protected $repoBalance;

    public function __construct(
        \Praxigento\Accounting\Repo\Entity\Balance $repoBalance
    )
    {
        $this->repoBalance = $repoBalance;
    }

    /**
     * Get asset balances on the requested date.
     * @param ARequest $request
     * @return AResponse
     */
    public function exec($request)
    {
        $result = new AResponse();
        $dateOn = $request->getDate();
        $assetTypeId = $request->getAssetTypeId();
        $rows = $this->repoBalance->getOnDate($assetTypeId, $dateOn);
        if (count($rows) > 0) {
            $result->set($rows);
            $result->markSucceed();
        }
        return $result;
    }
}