<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Service;

use Praxigento\Accounting\Service\Account\Request;
use Praxigento\Accounting\Service\Account\Response;
use Praxigento\Core\App\ICached;

interface IAccount extends ICached
{
    
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
     *
     * @deprecated use \Praxigento\Accounting\Repo\Entity\Account::getRepresentativeAccountId
     */
    public function getRepresentative(Request\GetRepresentative $request);
}