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

	public function authorize(array $data = array())
	{
		return $this->gatewayRequest('authorize', $data);
	}

	public function completeAuthorize(array $data = array())
	{
		return $this->gatewayRequest('completeAuthorize', $data);
	}

	public function capture(array $data = array())
	{
		$this->gatewayRequest('capture', $data);
	}

	public function purchase(array $data = array())
	{
		return $this->gatewayRequest('purchase', $data);
	}

	public function completePurchase(array $data = array())
	{
		return $this->gatewayRequest('completePurchase', $data);
	}

	public function refund(array $data = array())
	{
		return $this->gatewayRequest('refund', $data);
	}

	protected function checkGateway()
	{
		if ( ! $this->gateway) {
			throw new InvalidGatewayException('No gateway specified');
		}
	}

	protected function gatewayRequest($method, array $data = array())
	{
		$this->checkGateway();

		if (method_exists($this->gateway, $method)) {

			$this->setCard($data);

			return call_user_func_array(array($this->gateway, $method), array(
				array(
					'amount'   => $this->cart->total(),
					'currency' => $this->cart->currency()->code,
					'card'     => new CreditCard($this->card)
				)
			));

		}
	}

	public function __set($property, $value)
	{
		$this->card[$property] = $value;
	}
}