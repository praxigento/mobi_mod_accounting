<?php
/**
 * Module's configuration (hard-coded).
 *
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting;

class Config extends \Praxigento\Core\Config
{
    const ACL_ACCOUNTS_ACCOUNTS = 'admin_accounts_accounts';
    const ACL_ACCOUNTS_OPERATIONS = 'admin_accounts_operations';
    const ACL_ACCOUNTS_TRANSACTIONS = 'admin_accounts_transactions';
    const ACL_ACCOUNTS_TYPES_ASSET = 'admin_accounts_types_asset';
    const ACL_ACCOUNTS_TYPES_OPER = 'admin_accounts_types_oper';
    const MENU_ACCOUNTS_ACCOUNTS = self::ACL_ACCOUNTS_ACCOUNTS;
    const MENU_ACCOUNTS_OPERATIONS = self::ACL_ACCOUNTS_OPERATIONS;
    const MENU_ACCOUNTS_TRANSACTIONS = self::ACL_ACCOUNTS_TRANSACTIONS;
    const MENU_ACCOUNTS_TYPES_ASSET = self::ACL_ACCOUNTS_TYPES_ASSET;
    const MENU_ACCOUNTS_TYPES_OPER = self::ACL_ACCOUNTS_TYPES_OPER;
    const MODULE = 'Praxigento_Accounting';
    const ROUTE_NAME_ADMIN_ACCOUNTS = 'accounts';
}