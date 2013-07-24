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
        $query = $this->db->createInsertQuery()
            ->insertInto('orders');

        foreach ($order as $key => $value) $query->set($key, $query->bindValue($value));

        $query->prepare()->execute();

        return $this->db->lastInsertId();
    }

    // Return true or false
    public function update($id, array $order)
    {
        $query = $this->db->createUpdateQuery()
            ->update('orders');
        
        foreach ($order as $key => $value) {  
            $query->set($key, $query->bindValue($value));
        }
        
        return $query->where($query->expr->eq('id', $id))
            ->prepare()
            ->execute();
    }

    // Return true or false
    public function updateStatus($id, $status)
    {
        $query = $this->db->createUpdateQuery()
            ->update('orders');
            
        return $query->set('status', $query->bindValue($status))
            ->where($query->expr->eq('id', $id))
            ->prepare()
            ->execute();
    }

    // Return true or false
    // This should also delete items
    public function delete($id)
    {

    }

    // Return the item ID
    public function insertItem($id, $item)
    {
        $query = $this->db->createInsertQuery()
            ->insertInto('orders_items');

        foreach ($item as $key => $value) {
            if ($key != 'id') $query->set($key, $query->bindValue($value));
        }

        $query->set('order_id', $query->bindValue($id));
        $query->set('total', $query->bindValue($item['price'] * $item['quantity']));

        $query->prepare()->execute();

        return $this->db->lastInsertId();
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