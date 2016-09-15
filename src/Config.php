<?php
/**
 * Module's configuration (hard-coded).
 *
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting;

class Config extends \Praxigento\Core\Config
{
    const ACL_ACCOUNTS_ACCOUNTS = 'catalog_lots';
    const MENU_ACCOUNTS_ACCOUNTS = self::ACL_ACCOUNTS_ACCOUNTS;
    const MODULE = 'Praxigento_Accounting';
    const ROUTE_NAME_ADMIN_ACCOUNTS = 'accounts';
}