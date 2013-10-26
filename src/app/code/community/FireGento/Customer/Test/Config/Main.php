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
class FireGento_Customer_Test_Config_Main extends EcomDev_PHPUnit_Test_Case_Config
{
    /**
     * Check it the installed module has the correct module version
     */
    public function testModuleConfig()
    {
        $this->assertModuleVersion($this->expected('module')->getVersion());
        $this->assertModuleCodePool($this->expected('module')->getCodePool());

        foreach ($this->expected('module')->getDepends() as $depend) {
            $this->assertModuleIsActive('', $depend);
            $this->assertModuleDepends($depend);
        }
    }

    /**
     * Check if the block aliases are returning the correct class names
     */
    public function testBlockAliases()
    {
        $this->assertBlockAlias('firegento_customer/password', 'FireGento_Customer_Block_Password');
    }

    /**
     * Check if the helper aliases are returning the correct class names
     */
    public function testHelperAliases()
    {
        $this->assertHelperAlias('firegento_customer', 'FireGento_Customer_Helper_Data');
        $this->assertHelperAlias('firegento_customer/redirect', 'FireGento_Customer_Helper_Redirect');
    }

    /**
     * Check if the helper aliases are returning the correct class names
     */
    public function testModelAliases()
    {
        $this->assertModelAlias('firegento_customer/observer', 'FireGento_Customer_Model_Observer');
    }
}
