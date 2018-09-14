<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Accounting\Helper;

use Praxigento\Accounting\Config as Cfg;

/**
 * System customer is a representative of the application owner in accounting.
 */
class SysCustomer
    implements \Praxigento\Accounting\Api\Helper\SysCustomer
{
    private $cacheId = null;
    /** @var \Praxigento\Core\Api\App\Repo\Generic */
    private $daoGeneric;

    public function __construct(
        \Praxigento\Core\Api\App\Repo\Generic $daoGeneric
    ) {
        $this->daoGeneric = $daoGeneric;
    }

    /**
     * Return ID of the system customer.
     *
     * @return int
     */
    public function getId()
    {
        if (is_null($this->cacheId)) {
            $conn = $this->daoGeneric->getConnection();
            $entity = Cfg::ENTITY_MAGE_CUSTOMER;
            $cols = [Cfg::E_CUSTOMER_A_ENTITY_ID];
            $quoted = $conn->quote(Cfg::SYS_CUSTOMER_EMAIL);
            $where = Cfg::E_CUSTOMER_A_EMAIL . '=' . $quoted;
            $rs = $this->daoGeneric->getEntities($entity, $cols, $where);
            $found = reset($rs);
            $this->cacheId = $found[Cfg::E_CUSTOMER_A_ENTITY_ID];
        }
        return $this->cacheId;
    }

}