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
 * Redirect url helper for redirecting the customer if validations returned an error.
 *
 * @category FireGento
 * @package  FireGento_Customer
 * @author   FireGento Team <team@firegento.com>
 */
class FireGento_Customer_Helper_Redirect extends FireGento_Customer_Helper_Data
{
    /**
     * Define target URL and redirect customer after logging in
     */
    public function _loginPostRedirect()
    {
        $session = $this->_getSession();

        if (!$session->getBeforeAuthUrl() || $session->getBeforeAuthUrl() == Mage::getBaseUrl()) {
            // Set default URL to redirect customer to
            $session->setBeforeAuthUrl(Mage::helper('customer')->getAccountUrl());
            // Redirect customer to the last page visited after logging in
            if ($session->isLoggedIn()) {
                if (!Mage::getStoreConfigFlag(
                    Mage_Customer_Helper_Data::XML_PATH_CUSTOMER_STARTUP_REDIRECT_TO_DASHBOARD
                )) {
                    $referer = $this->getRequest()->getParam(Mage_Customer_Helper_Data::REFERER_QUERY_PARAM_NAME);
                    if ($referer) {
                        // Rebuild referer URL to handle the case when SID was changed
                        $referer = Mage::getModel('core/url')
                            ->getRebuiltUrl(Mage::helper('core')->urlDecode($referer));
                        if ($this->_isUrlInternal($referer)) {
                            $session->setBeforeAuthUrl($referer);
                        }
                    }
                } else if ($session->getAfterAuthUrl()) {
                    $session->setBeforeAuthUrl($session->getAfterAuthUrl(true));
                }
            } else {
                $session->setBeforeAuthUrl(Mage::helper('customer')->getLoginUrl());
            }
        } else if ($session->getBeforeAuthUrl() == Mage::helper('customer')->getLogoutUrl()) {
            $session->setBeforeAuthUrl(Mage::helper('customer')->getDashboardUrl());
        } else {
            if (!$session->getAfterAuthUrl()) {
                $session->setAfterAuthUrl($session->getBeforeAuthUrl());
            }
            if ($session->isLoggedIn()) {
                $session->setBeforeAuthUrl($session->getAfterAuthUrl(true));
            }
        }

        $this->_redirectUrl($session->getBeforeAuthUrl(true));
    }

    /**
     * Retrieve the current request
     *
     * @return Mage_Core_Controller_Request_Http Request Object
     */
    public function getRequest()
    {
        return Mage::app()->getRequest();
    }

    /**
     * Retrieve the current response
     *
     * @return Mage_Core_Controller_Response_Http Response Object
     */
    public function getResponse()
    {
        return Mage::app()->getResponse();
    }

    /**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session Customer Session
     */
    public function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }

    /**
     * Check url to be used as internal
     *
     * @param  string $url Url to check
     * @return bool Flag if url is internal
     */
    public function _isUrlInternal($url)
    {
        if (strpos($url, 'http') !== false) {

            /*
             * Url must start from base secure or base unsecure url
             */

            if ((strpos($url, Mage::app()->getStore()->getBaseUrl()) === 0)
                || (strpos($url, Mage::app()->getStore()->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK, true)) === 0)
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Set redirect url into response
     *
     * @param  string $url Redirect URL
     * @return Mage_Core_Controller_Varien_Action
     */
    public function _redirectUrl($url)
    {
        $this->getResponse()->setRedirect($url);
        return $this;
    }
}
