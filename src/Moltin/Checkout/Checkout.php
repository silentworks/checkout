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

	/**
	 * The constructor
	 * 
	 * @param Cart $cart The cart you want to create an order from
	 */
	public function __construct(Cart $cart)
	{
		$this->cart = $cart;
	}

	/**
	 * Set card/billing/shipping data
	 * 
	 * @param string|array $key A key or array of key-value pairs
	 * @param mixed $value The value you want to set $key to
	 */
	public function setCard($key, $value = null)
	{
		if (is_array($key)) {

			foreach ($key as $set => $to) $this->setCard($set, $to);

		} else {

			$this->card[$key] = $value;

		}

		return $this;
	}

	/**
	 * Return the gateway instance
	 * 
	 * @return Omnipay\Common\AbstractGateway The gateway instance
	 */
	public function gateway()
	{
		$this->checkGateway();

		return $this->gateway;
	}

	/**
	 * Set the gateway which will be used for payment
	 * 
	 * @param string $gateway The name of the gateway
	 */
	public function setGateway($gateway)
	{
		$this->gateway = GatewayFactory::create($gateway);
	}

	/**
	 * Submit an authorize request to the gateway
	 * 
	 * @param  array  $data The card/billing/shipping data
	 * @return Omnipay\Common\RequestInterface
	 */
	public function authorize(array $data = array())
	{
		return $this->gatewayRequest('authorize', $data);
	}

	/**
	 * Submit a complete authorize request to the gateway
	 * 
	 * @param  array  $data The card/billing/shipping data
	 * @return Omnipay\Common\RequestInterface
	 */
	public function completeAuthorize(array $data = array())
	{
		return $this->gatewayRequest('completeAuthorize', $data);
	}

	/**
	 * Submit a capture request to the gateway
	 * 
	 * @param  array  $data The card/billing/shipping data
	 * @return Omnipay\Common\RequestInterface
	 */
	public function capture(array $data = array())
	{
		$this->gatewayRequest('capture', $data);
	}

	/**
	 * Submit a purchase request to the gateway
	 * 
	 * @param  array  $data The card/billing/shipping data
	 * @return Omnipay\Common\RequestInterface
	 */
	public function purchase(array $data = array())
	{
		return $this->gatewayRequest('purchase', $data);
	}

	/**
	 * Submit a complete purchase request to the gateway
	 * 
	 * @param  array  $data The card/billing/shipping data
	 * @return Omnipay\Common\RequestInterface
	 */
	public function completePurchase(array $data = array())
	{
		return $this->gatewayRequest('completePurchase', $data);
	}

	/**
	 * Submit a refund request to the gateway
	 * 
	 * @param  array  $data The card/billing/shipping data
	 * @return Omnipay\Common\RequestInterface
	 */
	public function refund(array $data = array())
	{
		return $this->gatewayRequest('refund', $data);
	}

	/**
	 * Submit a void request to the gateway
	 * 
	 * @param  array  $data The card/billing/shipping data
	 * @return Omnipay\Common\RequestInterface
	 */
	public function void(array $data = array())
	{
		return $this->gatewayRequest('void', $data);
	}

	/**
	 * Check if the gateway has been set
	 * 
	 * @return void
	 */
	protected function checkGateway()
	{
		if ( ! $this->gateway) {
			throw new InvalidGatewayException('No gateway specified');
		}
	}

	/**
	 * Submit a request to the gateway
	 * 
	 * @param  string $method The method to submit
	 * @param  array  $data The card/billing/shipping data
	 * @return Omnipay\Common\RequestInterface
	 */
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

	/**
	 * Set a card property
	 * 
	 * @param string $property
	 * @param mixed $value
	 */
	public function __set($property, $value)
	{
		$this->card[$property] = $value;
	}
}