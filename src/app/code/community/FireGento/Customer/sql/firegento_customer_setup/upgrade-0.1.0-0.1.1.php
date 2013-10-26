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
 * Setup Script
 *
 * @category FireGento
 * @package  FireGento_Customer
 * @author   FireGento Team <team@firegento.com>
 */

/* @var $installer Mage_Eav_Model_Entity_Setup */
$installer = $this;
$installer->startSetup();

/*
 * Set all existing customers as active
 */

/* @var $collection Mage_Customer_Model_Resource_Customer_Collection */
$collection = Mage::getResourceModel('customer/customer_collection');
foreach ($collection as $customer) {
    Mage::getModel('customer/customer')->load($customer->getId())
        ->setCustomerActive(1)
        ->save();
}

$installer->endSetup();
