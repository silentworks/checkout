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

use Moltin\Cart\Cart;
use Moltin\Cart\Storage\Runtime as RuntimeStore;
use Moltin\Cart\Identifier\Runtime as RuntimeIdentifier;
use Moltin\Checkout\Checkout;

class CheckoutTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->cart = new Cart(new RuntimeStore, new RuntimeIdentifier);
    }

    public function tearDown()
    {
        $this->cart->destroy();
    }
}