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
     * Get all customer accounts.
     *
     * @param int $customerId
     * @return \Praxigento\Accounting\Data\Entity\Account[]|null
     */
    public function getAllByCustomerId($customerId);

    /**
     * Get asset type ID for the given account.
     *
     * @param int $accountId
     * @return int
     */
    public function getAssetTypeId($accountId);

    /**
     * Get account for the $customerId by $assetTypeId.
     *
     * @param int $customerId
     * @param int $assetTypeId
     * @return \Praxigento\Accounting\Data\Entity\Account|null
     */
    public function getByCustomerId($customerId, $assetTypeId);

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