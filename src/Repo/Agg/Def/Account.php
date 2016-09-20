<?php
/**
 * Repository to operate with 'Account" aggregate in this module.
 *
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Repo\Agg\Def;


class Account
    extends \Praxigento\Core\Repo\Def\Crud
    implements \Praxigento\Accounting\Repo\Agg\IAccount
{

    /** @var Account\SelectFactory */
    protected $_factorySelect;


    public function __construct(
        Account\SelectFactory $factorySelect
    ) {
        $this->_factorySelect = $factorySelect;
    }

    public function getQueryToSelect()
    {
        $result = $this->_factorySelect->getQueryToSelect();
        return $result;
    }

    public function getQueryToSelectCount()
    {
        $result = $this->_factorySelect->getQueryToSelectCount();
        return $result;
    }

}