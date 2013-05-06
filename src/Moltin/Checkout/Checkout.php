<?php

namespace Moltin\Checkout;

use Moltin\Cart\Cart;

class Checkout
{
	public function __construct(Cart $cart)
	{
		$this->cart = $cart;
	}
}