<?php

namespace Moltin\Checkout;

use Moltin\Cart\Cart;
use Moltin\Checkout\Exception\InvalidGatewayException;
use Omnipay\Common\GatewayFactory;
use Omnipay\Common\CreditCard;

class Checkout
{
	private $cart;
	protected $gateway = false;
	protected $card = array();

	public function __construct(Cart $cart)
	{
		$this->cart = $cart;
	}

	public function setCard($key, $value = null)
	{
		if (is_array($key)) {

			foreach ($key as $set => $to) $this->setCard($set, $to);

		} else {

			$this->card[$key] = $value;

		}
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

		$this->setCard($data);

		return $this->gateway->purchase(array(
			'amount'   => $this->cart->total(),
			'currency' => $this->cart->currency()->code,
			'card'     => new CreditCard($this->card)
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