<?php

namespace Moltin\Checkout;

use Moltin\Cart\Cart;
use Omnipay\Common\GatewayFactory;

class Checkout
{
	private $cart;
	protected $gateway;

	public function __construct(Cart $cart)
	{
		$this->cart = $cart;

		// Set the default gateway to Dummy
		$this->gateway = GatewayFactory::create('Dummy');
	}

	public function gateway()
	{
		return $this->gateway;
	}

	public function setGateway($gateway)
	{
		$this->gateway = GatewayFactory::create($gateway);
	}

	public function processPayment(array $data)
	{
		$this->gateway->purchase(array(
			'amount'   => $this->cart->total(),
			'currency' => $this->cart->currency->code,
			'card'     => $data
		));
	}
}