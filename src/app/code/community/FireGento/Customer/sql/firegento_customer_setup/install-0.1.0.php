<?php

/* @var $installer Mage_Eav_Model_Entity_Setup */
$installer = $this;
$installer->startSetup();

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
        'default'          => '1',
        'visible_on_front' => true,
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

$installer->endSetup();