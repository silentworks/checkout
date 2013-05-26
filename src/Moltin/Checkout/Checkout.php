<?php

namespace Moltin\Checkout;

use Moltin\Cart\Cart;
use Moltin\Checkout\Exception\InvalidGatewayException;
use Omnipay\Common\GatewayFactory;

class Checkout
{
	private $cart;
	protected $gateway = false;
	protected $card = array();

	public function __construct(Cart $cart)
	{
		$this->cart = $cart;
	}

	public function gateway()
	{
		$this->checkGateway();
		
		return $this->gateway;
	}

	public function setGateway($gateway)
	{
		$this->gateway = GatewayFactory::create($gateway);
	}

	public function processPayment(array $data = array())
	{
		$this->checkGateway();

		return $this->gateway->purchase(array(
			'amount'   => $this->cart->total(),
			'currency' => $this->cart->currency()->code,
			'card'     => $this->card
		));
	}

	protected function checkGateway()
	{
		if ( ! $this->gateway) {
			throw new InvalidGatewayException('No gateway specified');
		}
	}

	public function __set($property, $value)
	{
		$this->card[$property] = $value;
	}
}