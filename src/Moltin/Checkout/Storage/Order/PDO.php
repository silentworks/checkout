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

namespace Moltin\Checkout\Storage\Order;

use PDO as DB;

class PDO implements \Moltin\Checkout\Storage\Order\OrderInterface
{
    private $db;
    protected $table;

    public function __construct($table = 'orders', $itemTable = 'order_items')
    {
        $this->db = \ezcDbInstance::get();
        $this->table = $table;
        $this->itemTable = $itemTable;
    }

    // Return order id
    public function create(array $order)
    {

    }

    // Return true or false
    public function update($id, array $order)
    {

    }

    // Return true or false
    public function updateStatus($id, $status)
    {

    }

    // Return true or false
    // This should also delete items
    public function delete($id)
    {

    }

    // Return the item ID
    public function insertItem($id, $item)
    {

    }

    // Return true or false
    public function updateItem($itemId, $item)
    {

    }

    // Return true or false
    public function deleteItem($itemId)
    {

    }
}