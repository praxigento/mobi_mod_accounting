<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Api\Rest\Transaction;

use Praxigento\Accounting\Repo\Entity\Data\Account as Acc;
use Praxigento\Accounting\Repo\Query\Trans\Get\Builder as Builder;

/**
 * @deprecated TODO: use it or remove it.
 */
class Get
    extends \Praxigento\Core\App\WebApi\Processor\WithQuery
    implements \Praxigento\Accounting\Api\Rest\Transaction\GetInterface
{
    const BIND_CUST_ID = 'custId';

    const VAR_CUST_ID = 'cust_id';

    /** @var \Praxigento\Core\App\WebApi\IAuthenticator */
    protected $authenticator;
    /** @var \Praxigento\Accounting\Repo\Query\Trans\Get\Builder */
    protected $qbld;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Praxigento\Accounting\Repo\Query\Trans\Get\Builder $qbld,
        \Praxigento\Core\Helper\Config $hlpCfg,
        \Praxigento\Core\App\WebApi\IAuthenticator $authenticator
    ) {
        parent::__construct($manObj, $qbld, $hlpCfg);
        $this->authenticator = $authenticator;
    }

    protected function authorize(\Praxigento\Core\Data $ctx)
    {
        /* get working vars from context */
        $vars = $ctx->get(self::CTX_VARS);
        $custId = $vars->get(self::VAR_CUST_ID);

        /* only currently logged in  customer can get account statement */
        $currentCustId = $this->authenticator->getCurrentCustomerId();
        if (
            is_null($custId) ||
            ($custId != $currentCustId)
        ) {
            $msg = __('You are not authorized to perform this operation.');
            throw new \Magento\Framework\Exception\AuthorizationException($msg);
        }
    }

    public function exec(\Praxigento\Accounting\Api\Rest\Transaction\Get\Request $data)
    {
        $result = parent::process($data);
        return $result;
    }

    protected function populateQuery(\Praxigento\Core\Data $ctx)
    {
        /* get working vars from context */
        /** @var \Praxigento\Core\Data $bind */
        $bind = $ctx->get(self::CTX_BIND);
        /** @var \Praxigento\Core\Data $vars */
        $vars = $ctx->get(self::CTX_VARS);
        /** @var \Magento\Framework\DB\Select $query */
        $query = $ctx->get(self::CTX_QUERY);

        /* compose filters and add bindings */

        /* by customer ID */
        $custId = $vars->get(self::VAR_CUST_ID);
        $whereCrd = Builder::AS_ACC_CRD . '.' . Acc::ATTR_CUST_ID . '=:' . self::BIND_CUST_ID;
        $whereDbt = Builder::AS_ACC_DBT . '.' . Acc::ATTR_CUST_ID . '=:' . self::BIND_CUST_ID;
        $query->where("$whereCrd OR $whereDbt");
        $bind->set(self::BIND_CUST_ID, $custId);
    }

    /**
     * @param \Praxigento\Core\Data $ctx
     * @return array
     */
    protected function prepareQueryParameters(\Praxigento\Core\Data $ctx)
    {
        /* get working vars from context */
        $vars = $ctx->get(self::CTX_VARS);
        /** @var \Praxigento\BonusHybrid\Api\Stats\Plain\Request $req */
        $req = $ctx->get(self::CTX_REQ);

        /* root customer id */
        $rootCustId = $req->getRootCustId();
        $currentCustId = $this->authenticator->getCurrentCustomerId($rootCustId);

        /* save to context */
        $vars->set(self::VAR_CUST_ID, $currentCustId);
    }
}