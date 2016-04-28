<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Service;

use Praxigento\Accounting\Service\Account\Request;
use Praxigento\Accounting\Service\Account\Response;

interface IAccount
{
    /**
     * Reset cached data.
     */
    public function cacheReset();

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
}