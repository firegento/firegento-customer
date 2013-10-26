<?php
/**
 * This file is part of a FireGento e.V. module.
 *
 * This FireGento e.V. module is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License version 3 as
 * published by the Free Software Foundation.
 *
 * This script is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * PHP version 5
 *
 * @category  FireGento
 * @package   FireGento_Customer
 * @author    FireGento Team <team@firegento.com>
 * @copyright 2013 FireGento Team (http://www.firegento.com)
 * @license   http://opensource.org/licenses/gpl-3.0 GNU General Public License, version 3 (GPLv3)
 */
/**
 * PHPUnit Test Class
 *
 * @category FireGento
 * @package  FireGento_Customer
 * @author   FireGento Team <team@firegento.com>
 */
class FireGento_Customer_Test_Block_Password extends EcomDev_PHPUnit_Test_Case_Config
{
    /**
     * @var FireGento_Customer_Block_Password
     */
    protected $_block;

    /**
     * Test constructor
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_block = Mage::getSingleton('core/layout')->createBlock('firegento_customer/password');
    }

    /**
     * @test
     * @loadFixture
     * @loadExpectations
     */
    public function getMinimumPasswordLength()
    {
        $this->assertEquals(
            $this->expected('result')->getData('min_password_length'),
            $this->_block->getMinimumPasswordLength()
        );
    }

    /**
     * @test
     * @loadFixture
     * @loadExpectations
     */
    public function getMinimumPasswordLengthWithWrongEntry()
    {
        $this->assertEquals(
            $this->expected('result')->getData('min_password_length'),
            $this->_block->getMinimumPasswordLength()
        );
    }
}
