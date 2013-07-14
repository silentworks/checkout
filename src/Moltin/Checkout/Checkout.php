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

use StdClass;
use InvalidArgumentException;
use BadMethodCallException;
use Moltin\Cart\Cart;
use Moltin\Checkout\Exception\InvalidGatewayException;
use Moltin\Checkout\Storage\Order\OrderInterface;
use Moltin\Checkout\Storage\Transaction\TransactionInterface;
use Omnipay\Common\GatewayFactory;
use Omnipay\Common\CreditCard;

class Checkout
{
    private $cart;
    private $options = array(
        'token'         => null,
        'description'   => null,
        'transactionId' => null,
        'clientIp'      => null,
        'returnUrl'     => null,
        'cancelUrl'     => null
    );
    private $exclude = array(
        'number',
        'expiryMonth',
        'expiryYear',
        'startMonth',
        'startYear',
        'cvv',
        'issueNumber',
        'type'
    );

    protected $store;
    protected $order = false;
    protected $gateway = false;
    protected $data = array();

    /**
     * The constructor
     * 
     * @param Cart $cart The cart you want to create an order from
     */
    public function __construct(Cart $cart, OrderInterface $orderStore, TransactionInterface $transStore)
    {
        $this->cart = $cart;

        $this->store = new StdClass;
        $this->store->order = $orderStore;
        $this->store->transaction = $transStore;
    }

    /**
     * Create an order from the current object
     * 
     * @return int The order id
     */
    public function createOrder()
    {
        
    }

    /**
     * Set card/billing/shipping data
     * 
     * @param string|array $key A key or array of key-value pairs
     * @param mixed $value The value you want to set $key to
     */
    public function setData($key, $value = null)
    {
        if (is_array($key)) {

            foreach ($key as $set => $to) $this->setData($set, $to);

        } else {

            $this->data[$key] = $value;

        }

        return $this;
    }

    /**
     * Set a gateway option
     * 
     * @param string $option The option
     * @param string $value  The value
     */
    public function setOption($option, $value)
    {
        if (in_array($option, $this->options)) {
            $this->options[$option] = $value;

            return $this;
        } else {
            throw new InvalidArgumentException("'{$option}' is not a valid option");
        }
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
    public function setGateway($gateway, array $options)
    {
        $this->gateway = GatewayFactory::create($gateway);

        foreach ($options as $option => $value) {
            $method = 'set'.ucfirst($option);

            if (method_exists($this->gateway, $method)) $this->gateway->$method($value);
        }
    }

    /**
     * Submit an authorize request to the gateway
     * 
     * @param  array  $data The card/billing/shipping data
     * @return Omnipay\Common\RequestInterface
     */
    public function authorize(array $data = array())
    {
        return $this->gatewayRequest('authorize', $data, 'authorized');
    }

    /**
     * Submit a complete authorize request to the gateway
     * 
     * @param  array  $data The card/billing/shipping data
     * @return Omnipay\Common\RequestInterface
     */
    public function completeAuthorize(array $data = array())
    {
        return $this->gatewayRequest('completeAuthorize', $data, 'authorized');
    }

    /**
     * Submit a capture request to the gateway
     * 
     * @param  array  $data The card/billing/shipping data
     * @return Omnipay\Common\RequestInterface
     */
    public function capture(array $data = array())
    {
        $this->gatewayRequest('capture', $data, 'paid');
    }

    /**
     * Submit a purchase request to the gateway
     * 
     * @param  array  $data The card/billing/shipping data
     * @return Omnipay\Common\RequestInterface
     */
    public function purchase(array $data = array())
    {
        return $this->gatewayRequest('purchase', $data, 'paid');
    }

    /**
     * Submit a complete purchase request to the gateway
     * 
     * @param  array  $data The card/billing/shipping data
     * @return Omnipay\Common\RequestInterface
     */
    public function completePurchase(array $data = array())
    {
        return $this->gatewayRequest('completePurchase', $data, 'paid');
    }

    /**
     * Submit a refund request to the gateway
     * 
     * @param  array  $data The card/billing/shipping data
     * @return Omnipay\Common\RequestInterface
     */
    public function refund(array $data = array())
    {
        return $this->gatewayRequest('refund', $data, 'refund');
    }

    /**
     * Submit a void request to the gateway
     * 
     * @param  array  $data The card/billing/shipping data
     * @return Omnipay\Common\RequestInterface
     */
    public function void(array $data = array())
    {
        return $this->gatewayRequest('void', $data, 'void');
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
     * Check if there is an order attached to this checkout
     * 
     * @return void
     */
    protected function checkOrder()
    {
        if ( ! $this->order) $this->createOrder();
    }

    /**
     * Submit a request to the gateway
     * 
     * @param  string $method The method to submit
     * @param  array  $data The card/billing/shipping data
     * @param  string $status The order status if this request is a success
     * @return Omnipay\Common\RequestInterface
     */
    protected function gatewayRequest($method, array $data, $status)
    {
        $this->checkGateway();

        if (method_exists($this->gateway, $method)) {

            $this->setData($data);

            $this->checkOrder();

            $request = $this->gateway->$method(array(
                'amount'        => number_format($this->cart->total(), 2, '', ''),
                'currency'      => $this->cart->currency()->code,
                'card'          => new CreditCard($this->data),
                'token'         => $this->options['token'],
                'description'   => $this->options['description'],
                'transactionId' => $this->options['transactionId'],
                'clientIp'      => $this->options['clientIp'],
                'returnUrl'     => $this->options['returnUrl'],
                'cancelUrl'     => $this->options['cancelUrl']
            ));

            $response = $request->send();

            if ($response->isSuccessful()) {
                // Update the order status to $status
            } elseif ( ! $response->isRedirect()) {
                // There was an error, mark the order as failed
            }

            return $response;

        } else {

            throw new BadMethodCallException("'{$this->gateway->getName()}' does not support the '{$method}' method");

        }
    }

    /**
     * Set a data property
     * 
     * @param string $property
     * @param mixed $value
     */
    public function __set($property, $value)
    {
        $this->data[$property] = $value;
    }
}
