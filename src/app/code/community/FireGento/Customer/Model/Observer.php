<?php
/**
 * This file is part of the FIREGENTO project.
 *
 * FireGento_Core is free software; you can redistribute it and/or
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
 * @copyright 2011 FireGento Team (http://www.firegento.de). All rights served.
 * @license   http://opensource.org/licenses/gpl-3.0 GNU General Public License, version 3 (GPLv3)
 * @version   $$Id$$
 */
/**
 * Observer
 *
 * @category  FireGento
 * @package   FireGento_Customer
 * @author    FireGento Team <team@firegento.com>
 * @copyright 2011 FireGento Team (http://www.firegento.de). All rights served.
 * @license   http://opensource.org/licenses/gpl-3.0 GNU General Public License, version 3 (GPLv3)
 * @version   $$Id$$
 */
class FireGento_Customer_Model_Observer
{
    const XML_PATH_LOGIN_ATTEMPTS     = 'customer/password/login_attempts';
    const XML_PATH_LOGIN_ATTEMPT_SPAN = 'customer/password/login_attempt_span';
    const XML_PATH_LOGIN_LOCK_TIME    = 'customer/password/login_lock_time';

    /**
     * Retrieve the helper class
     * 
     * @return FireGento_Customer_Helper_Data Helper
     */
    protected function _getHelper()
    {
        return Mage::helper('firegento_customer');
    }

    /**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }

    /**
     * Retrieve the current website ID
     * 
     * @return int Website ID
     */
    public function _getWebsiteId()
    {
        return Mage::app()->getWebsite()->getId();
    }

    /**
     * Validates the customer password.
     * 
     * @throws Exception
     * @param Varien_Event_Observer $observer Observer
     * @return FireGento_Customer_Model_Observer Self.
     */
    public function customerSaveBefore(Varien_Event_Observer $observer)
    {
        $customer = $observer->getCustomer();
        $this->_validatePassword($customer->getEmail(), $customer->getPassword());
        return $this;
    }

    /**
     * Validate the controller preDispatch action of customer_account_loginPost
     *
     * @param Varien_Event_Observer $observer Observer
     * @return FireGento_Customer_Model_Observer Self.
     */
    public function customerPreLogin(Varien_Event_Observer $observer)
    {
        $controller = $observer->getControllerAction();
        try {
            $loginParams = $controller->getRequest()->getParams();
            if (isset($loginParams['login'])) {
                $loginParams = $loginParams['login'];
                $validator = new Zend_Validate_EmailAddress();
                if ($validator->isValid($loginParams['username'])) {
                    // Load Customer
                    $customer = Mage::getModel('customer/customer')
                        ->setWebsiteId($this->_getWebsiteId())
                        ->loadByEmail($loginParams['username']);
                    // If user doesn't exist, throw exception
                    if (!$customer->getId()) {
                        throw new Exception(
                            $this->_getHelper()->__('Login failed.')
                        );
                    }

                    $this->_validateCustomerActivationStatus($customer);
                } else {
                    throw new Exception(
                        $this->_getHelper()->__('The email address you entered is invalid.')
                    );
                }
            }
        } catch (Exception $e) {
            $this->_getSession()->addError($this->_getHelper()->__($e->getMessage()));

            // Set no-dispatch flag
            $controller->setFlag(
                $controller->getRequest()->getActionName(),
                Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH,
                true
            );

            // Redirect to login page
            $loginUrl = Mage::helper('customer')->getLoginUrl();
            $controller->getResponse()->setRedirect($loginUrl);
            $controller->getResponse()->sendResponse();
        }
        return $this;
    }

    /**
     * Validates the active status of a customer
     * 
     * @throws Exception
     * @param Mage_Customer_Model_Customer $customer Customer Instance
     * @return void
     */
    protected function _validateCustomerActivationStatus($customer)
    {
        $customerActive = $customer->getData('customer_active');

        /*
         * Check if the last failed login ban is over
         */
        $now         = time();
        $lockTime    = Mage::getStoreConfig(self::XML_PATH_LOGIN_LOCK_TIME);
        $lastAttempt = $customer->getData('customer_last_login_failed');
        $loginAllowed = ($now - $lastAttempt > $lockTime);
        if ($loginAllowed) {
            $customer->setData('customer_logins_failed', 0)
                ->setData('customer_last_login_failed', 0)
                ->save();
        }

        /*
         * Check if the login attempts reached the ban limit
         */
        $loginAttempts = $customer->getData('customer_logins_failed');
        $attemptLock   = $loginAttempts >= Mage::getStoreConfig(self::XML_PATH_LOGIN_ATTEMPTS);
        $timeLock      = ($now - $lastAttempt < $lockTime);

        if (($attemptLock && $timeLock) || !$customerActive) {
            throw new Exception(
                $this->_getHelper()->__('Your account is locked due to too many failed login attempts.')
            );
        }
    }

    /**
     * Validate the controller postDispatch action of customer_account_loginPost
     *
     * @param Varien_Event_Observer $observer Observer
     * @return FireGento_Customer_Model_Observer Self.
     */
    public function customerPostLogin(Varien_Event_Observer $observer)
    {
        if (!$this->_getSession()->isLoggedIn()) {
            $loginParams = $observer->getControllerAction()->getRequest()->getParams();
            if (isset($loginParams['login']) && isset($loginParams['login']['username'])) {
                $loginParams = $loginParams['login'];
                $validator = new Zend_Validate_EmailAddress();
                if ($validator->isValid($loginParams['username'])) {
                    // Load Customer
                    $customer = Mage::getModel('customer/customer')
                        ->setWebsiteId($this->_getWebsiteId())
                        ->loadByEmail($loginParams['username']);
                    // If customer exists, set new values..
                    if ($customer->getId()) {
                        $attempts    = $customer->getData('customer_logins_failed');
                        $lastAttempt = $customer->getData('customer_last_login_failed');
                        $now         = time();

                        if (!is_numeric($attempts)) {
                            $attempts = 1;
                        } else {
                            if ($now - $lastAttempt > Mage::getStoreConfig(self::XML_PATH_LOGIN_ATTEMPT_SPAN)) {
                                $attempts = 0;
                            }
                            $attempts++;
                        }

                        $customer->setData('customer_logins_failed', $attempts)
                            ->setData('customer_last_login_failed', $now)
                            ->save();
                    }
                }
            }
        } else {
            $customer = $this->_getSession()->getCustomer();
            $customer->setData('customer_logins_failed', 0)
                ->setData('customer_last_login_failed', 0)
                ->save();
        }
        return $this;
    }

    /**
     * Checks the password in checkout register mode
     * 
     * @param Varien_Event_Observer $observer Observer
     * @return FireGento_Customer_Model_Observer Self.
     */
    public function checkOnepageRegistration(Varien_Event_Observer $observer)
    {
        $controllerAction = $observer->getControllerAction();

        /** @var Mage_Checkout_Model_Type_Onepage $onepage */
        $onepage = $controllerAction->getOnepage();

        // Check if customer wants to register
        $method = $onepage->getCheckoutMethod();
        if ($method != Mage_Checkout_Model_Type_Onepage::METHOD_REGISTER) {
            return $this;
        }

        // Get email and password from request params
        $params   = $observer->getControllerAction()->getRequest()->getParam('billing');
        $email    = $params['email'];
        $password = $params['customer_password'];

        // Validate password and return error if invalid
        try {
            $this->_validatePassword($email, $password);
        } catch (FireGento_Customer_Exception $e) {
            $error = array(
                'error'   => -1,
                'message' => $e->getMessage(),
            );
            $controllerAction->getResponse()->setBody(
                Mage::helper('core')->jsonEncode($error)
            );
        }
        return $this;
    }

    /**
     * Validates the password against some common criterias..
     * 
     * @throws FireGento_Customer_Exception
     * @param string $email    Email Address
     * @param string $password Password
     * @return FireGento_Customer_Model_Observer Self.
     */
    protected function _validatePassword($email, $password)
    {
        // Check if password is not empty
        if (!$password) {
            return $this;
        }

        /*
         * VALIDATIONS
         */

        if ($email == $password) {
            throw new FireGento_Customer_Exception(
                $this->_getHelper()->__('Your email address and your password can not be equal.')
            );
        }
        if (strlen($password) < 8) {
            throw new FireGento_Customer_Exception(
                $this->_getHelper()->__('Your password lenght must be greater than 8.')
            );
        }
        if (preg_match('/[A-Z]/',$password) == 0
            || preg_match('/[a-z]/',$password) == 0
            || preg_match('/[0-9]/',$password) == 0
        ) {
            throw new FireGento_Customer_Exception(
                $this->_getHelper()->__('Your password must contain at leas one uppercase character, one lowercase character and one digit.')
            );
        }
        return $this;
    }
}