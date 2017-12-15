<?php
/**
 *
 */

namespace Praxigento\Accounting\Api\Service\Account\Balance\Calc;

class Response
    extends \Praxigento\Core\Data
    implements \Praxigento\Core\App\Service\IResponse
{
    /** @var string */
    private $errorCode = \Praxigento\Core\App\Service\IResponse::ERR_UNDEFINED;
    /** @var  string */
    private $errorMessage;

    /**
     * @return string (required for Magento API transformations)
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * @return string (required for Magento API transformations)
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * @return bool
     */
    public function isSucceed()
    {
        $result = ($this->errorCode == self::ERR_NO_ERROR);
        return $result;
    }

    public function markSucceed()
    {
        $this->errorCode = \Praxigento\Core\App\Service\IResponse::ERR_NO_ERROR;
    }

    public function setErrorCode($code)
    {
        $this->errorCode = $code;
    }

    public function setErrorMessage($message)
    {
        $this->errorMessage = $message;
    }
}