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
 * Adds the custom password validation rules to the page if activated
 *
 * @category FireGento
 * @package  FireGento_Customer
 * @author   FireGento Team <team@firegento.com>
 */
class FireGento_Customer_Block_Password extends Mage_Core_Block_Template
{
    /**
     * Set the block template
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('firegento/customer/password.phtml');
    }

    /**
     * Retrieve the minimum password length
     *
     * @return int Minimum Password length
     */
    public function getMinimumPasswordLength()
    {
        $minLength = (int) Mage::getStoreConfig('customer/password/password_min_length');
        if (!$minLength || $minLength <= 0) {
            $minLength = 8;
        }

        return $minLength;
    }

    /**
     * Deactivate output if not activated
     *
     * @return string Rendered output
     */
    protected function _toHtml()
    {
        if (!Mage::getStoreConfigFlag('customer/password/check_password_min_length')) {
            return '';
        }

        return parent::_toHtml();
    }
}
