<?php
class Fruition_Countertops_Block_Product extends Mage_Core_Block_Template
{
    public function getProduct()
    {
    	$_sku = 'vanity-countertops';
 		$_product = Mage::getModel('catalog/product')->loadByAttribute('sku',$_sku);
 		$product = Mage::getModel('catalog/product')->load($_product->getId());
        return $product;
    }


}