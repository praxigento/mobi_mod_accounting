<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Repo;


interface IAccount
{
    public function create($data);

    public function getByCustomerId($customerId, $assetTypeId);

    public function getById($accountId);
}