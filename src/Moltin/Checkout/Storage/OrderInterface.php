<?php

namespace Moltin\Checkout\Storage;

interface OrderInterface
{
	// Return order id
	public function createOrder(array $order)
	{

	}

	// Return true or false
	public function updateOrder($id, array $order)
	{

	}

	// Return true or false
	// This should also delete items
	public function deleteOrder($id)
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