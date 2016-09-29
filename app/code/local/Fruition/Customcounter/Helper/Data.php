<?php
/**
 * Created by PhpStorm.
 * User: scotttaylor
 * Date: 2/2/16
 * Time: 1:47 PM
 */ 
class Fruition_Customcounter_Helper_Data extends Mage_Core_Helper_Abstract {

    public function getAttributeSetId($attributeSetName){

        $attributeSetId = Mage::getModel('eav/entity_attribute_set')
            ->load($attributeSetName, 'attribute_set_name')
            ->getAttributeSetId();

        return $attributeSetId;

    }

    public function getAttributeSetName($attributeSetId){

        $attributeSetName = Mage::getModel('eav/entity_attribute_set')
            ->load($attributeSetId)
            ->getAttributeSetName();

        return $attributeSetName;

    }

    public function prepareAttribute($attributeCode) {

        $attribute = Mage::getModel('eav/entity_attribute')->loadByCode(Mage_Catalog_Model_Product::ENTITY, $attributeCode);
        return $attribute->getSource()->getAllOptions();

    }

}