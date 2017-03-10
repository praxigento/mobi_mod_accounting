<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Api\Transaction;


class Get
    implements \Praxigento\Accounting\Api\Transaction\GetInterface
{
    /** @var \Praxigento\Core\Api\IAuthenticator */
    protected $authenticator;

    public function __construct(
        \Praxigento\Core\Api\IAuthenticator $authenticator
    )
    {
        $this->authenticator = $authenticator;
    }

    public function exec(\Praxigento\Accounting\Api\Transaction\Get\Request $data)
    {
        $result = new \Praxigento\Accounting\Api\Transaction\Get\Response();
        $isAuthenticated = $this->authenticator->isAuthenticated();
        $user = $this->authenticator->getCurrentUserData();
        $arr = (array)$user->get();
        return $result;
    }

}