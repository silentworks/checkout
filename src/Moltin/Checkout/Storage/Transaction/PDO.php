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

use PDO as DB;

class PDO implements \Moltin\Checkout\Storage\TransactionInterface
{
    protected $pdo;
    protected $table;

    public function __construct(DB $pdo, $table = 'transactions')
    {
        $this->pdo = $pdo;
        $this->table = $table;
    }

    public function getTransaction($transId)
    {

    }
}