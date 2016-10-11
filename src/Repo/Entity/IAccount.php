<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Repo\Entity;

interface IAccount
    extends \Praxigento\Core\Repo\ICrud
{
    /**
     * @param \Praxigento\Accounting\Data\Entity\Account|array $data
     * @return int
     */
    public function create($data);

    /**
     * Get asset type ID for the given account.
     *
     * @param int $accountId
     * @return int
     */
    public function getAssetTypeId($accountId);

    /**
     * Get account for the $customerId by $assetTypeId or all accounts for the customer (if $assetTypeId is null).
     * @param int $customerId
     * @param int $assetTypeId
     * @return false|\Praxigento\Accounting\Data\Entity\Account|\Praxigento\Accounting\Data\Entity\Account[]
     */
    public function getByCustomerId($customerId, $assetTypeId = null);

    /**
     * @param int $id
     * @return \Praxigento\Accounting\Data\Entity\Account|bool
     */
    public function getById($id);

    /**
     * Add/subtract value to/from account current balance.
     *
     * @param int $accountId
     * @param float $delta change value (negative or positive)
     * @return int number of updated rows in DB
     */
    public function updateBalance($accountId, $delta);
}