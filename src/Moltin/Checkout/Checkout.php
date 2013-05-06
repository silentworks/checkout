<?php

namespace Moltin\Checkout;

use Moltin\Cart\Cart;
use Omnipay\Common\GatewayFactory;

class Checkout
{
	public function __construct(Cart $cart)
	{
		$this->cart = $cart;

		// Set the default gateway to Dummy
		$this->gateway = GatewayFactory::create('Dummy');
	}

	public function setGateway($gateway)
	{
		$this->gateway = GatewayFactory::create($gateway);
	}

	public function processPayment(array $data)
	{
		
	}
}