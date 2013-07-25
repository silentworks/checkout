<?php

/**
 * This file is part of Moltin Checkout, a PHP package to
 * convert your Moltin\Cart object into an order and take
 * payments.
 *
 * Copyright (c) 2013 Moltin Ltd.
 * http://github.com/moltin/checkout
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package moltin/checkout
 * @author Chris Harvey <chris@molt.in>
 * @copyright 2013 Moltin Ltd.
 * @version dev
 * @link http://github.com/moltin/checkout
 *
 */

namespace Moltin\Checkout\Storage\Transaction;

class PDO implements \Moltin\Checkout\Storage\Transaction\TransactionInterface
{
    private $db;
    protected $table;

    public function __construct($table = 'transactions')
    {
        $this->db = \ezcDbInstance::get();
        $this->table = $table;
    }

    public function getTransaction($transId)
    {

    }
}