<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Lib\Service;

use Praxigento\Accounting\Lib\Service\Account\Request;
use Praxigento\Accounting\Lib\Service\Account\Response;

interface IAccount {
    /**
     * @param Request\Get $request
     *
     * @return Response\Get
     */
    public function get(Request\Get $request);

    /**
     * @param Request\GetRepresentative $request
     *
     * @return Response\GetRepresentative
     */
    public function getRepresentative(Request\GetRepresentative $request);

    /**
     * @param Request\UpdateBalance $request
     *
     * @return Response\UpdateBalance
     */
    public function updateBalance(Request\UpdateBalance $request);

    /**
     * Reset cached data.
     */
    public function cacheReset();
}