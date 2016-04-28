<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Service;

interface IOperation {
    /**
     * @param Operation\Request\Add $req
     *
     * @return Operation\Response\Add
     */
    public function add(Operation\Request\Add $req);

}