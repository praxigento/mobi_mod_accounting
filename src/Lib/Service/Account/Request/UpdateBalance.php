<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Lib\Service\Account\Request;

class UpdateBalance extends \Praxigento\Core\Service\Base\Request {
    const ACCOUNT_ID = 'account_id';
    const CHANGE_VALUE = 'change_value';

    public function getAccountId() {
        $result = $this->getData(self::ACCOUNT_ID);
        return $result;
    }

    public function getChangeValue() {
        $result = $this->getData(self::CHANGE_VALUE);
        return $result;
    }

    public function setAccountId($data) {
        $this->setData(self::ACCOUNT_ID, $data);
    }

    public function setChangeValue($data) {
        $this->setData(self::CHANGE_VALUE, $data);
    }

}