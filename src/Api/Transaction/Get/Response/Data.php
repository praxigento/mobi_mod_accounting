<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Api\Transaction\Get\Response;

/**
 * This is API response data, getters only are defined.
 */
interface Data
{
    /**
     * @return \Praxigento\Accounting\Api\Transaction\Get\Response\Data\Entry[]
     */
    public function getEntries();

}