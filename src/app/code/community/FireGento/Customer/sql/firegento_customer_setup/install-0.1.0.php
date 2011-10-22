<?php


/* @var $installer Mage_Eav_Model_Entity_Setup */
$installer = $this;
$installer->startSetup();

/*
 * ATTRIBUTE: customer_active
 */
$attributeName = 'customer_active';
$installer->addAttribute('customer', $attributeName,
    array(
        'type'             => 'int',
        'input'            => 'select',
        'label'            => 'Active',
        'global'           => true,
        'visible'          => true,
        'required'         => false,
        'user_defined'     => false,
        'default'          => 1,
        'visible_on_front' => false,
        'source'           => 'eav/entity_attribute_source_boolean'
    )
);

$customer  = Mage::getModel('customer/customer');
$attrSetId = $customer->getResource()->getEntityType()->getDefaultAttributeSetId();
$installer->addAttributeToSet('customer', $attrSetId, 'General', $attributeName);

Mage::getSingleton('eav/config')
    ->getAttribute('customer', $attributeName)
	->setData('used_in_forms', array('adminhtml_customer'))
	->save();

/*
 * ATTRIBUTE: customer_logins_failed
 */
$attributeName = 'customer_logins_failed';
$installer->addAttribute('customer', $attributeName,
    array(
        'type'             => 'int',
        'input'            => 'text',
        'label'            => 'Last failed login',
        'global'           => true,
        'visible'          => false,
        'required'         => false,
        'user_defined'     => false,
        'default'          => '1',
        'visible_on_front' => false
    )
);

/*
 * ATTRIBUTE: customer_last_login_failed
 */
$attributeName = 'customer_last_login_failed';
$installer->addAttribute('customer', $attributeName,
    array(
        'type'             => 'int',
        'input'            => 'text',
        'label'            => 'Failed logins',
        'global'           => true,
        'visible'          => false,
        'required'         => false,
        'user_defined'     => false,
        'default'          => '1',
        'visible_on_front' => false
    )
);

$installer->endSetup();