<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Accounting\Helper;

/**
 * Account balance related functionality.
 */
class Balance
    implements \Praxigento\Accounting\Api\Helper\Balance
{
    /** @var \Praxigento\Accounting\Repo\Dao\Account */
    private $daoAcc;
    /** @var \Praxigento\Accounting\Repo\Dao\Type\Asset */
    private $daoTypeAsset;

    public function __construct(
        \Praxigento\Accounting\Repo\Dao\Account $daoAcc,
        \Praxigento\Accounting\Repo\Dao\Type\Asset $daoTypeAsset
    ) {
        $this->daoAcc = $daoAcc;
        $this->daoTypeAsset = $daoTypeAsset;
    }


    public function get($custId, $assetTypeCode)
    {
        $result = 0;
        $assetTypeId = $this->daoTypeAsset->getIdByCode($assetTypeCode);
        $account = $this->daoAcc->getByCustomerId($custId, $assetTypeId);
        if ($account) {
            $result = $account->getBalance();
        }
        return $result;
    }

}