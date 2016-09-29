<?php

/**
 * MageWorx
 * CustomOptions Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_CustomOptions
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomOptions_Helper_Product_Configuration extends MageWorx_CustomOptions_Helper_Product_Configuration_Abstract {
    
    public function getCustomOptions(Mage_Catalog_Model_Product_Configuration_Item_Interface $item) {
        $this->setCustomOptionsDetails($item);
        return parent::getCustomOptions($item);
    }
  
    // $model => $option or $value model
    public function getOptionFormatPrice($model, $optionTotalQty = 1, $product, $quote) {
        $helper = Mage::helper('mageworx_customoptions');
        $price = $helper->getOptionPriceByQty($model, $optionTotalQty, $product);
        
        if ($price!=0) {
            $store = $product->getStore();
            
            // option taxClassId
            $taxClassId = ($model->getTaxClassId() ? $model->getTaxClassId() : $product->getTaxClassId());
            
            // calculate tax
            if ($price>0) {
                if (Mage::helper('tax')->priceIncludesTax($store)) {
                    // Exclude Default Tax
                    $price = $helper->getPriceExcludeTax($price, $quote, $taxClassId);
                }
                $priceInclTax = $price + $helper->getTaxPrice($price, $quote, $taxClassId);
            } else {    
                $priceInclTax = $price;
            }
            
            // show exclude tax
            if (Mage::helper('tax')->displayCartPriceExclTax($store)) {
                return ' - ' . $helper->currencyByStore($price, $store, true, false);
            }
            
            // show exclude and include tax
            if (Mage::helper('tax')->displayCartBothPrices($store)) {                                
                return ' - '  . $helper->currencyByStore($price, $store, true, false) . ' ' . $helper->__('(Incl. Tax %s)', $helper->currencyByStore($priceInclTax, $store, true, false));
            }
            
            // show include tax
            if (Mage::helper('tax')->displayCartPriceInclTax($store)) {
                return ' - ' . $helper->currencyByStore($priceInclTax, $store, true, false);
            }
        }
        return '';        
    }
    
    
    public function setCustomOptionsDetails($item) {
        $helper = Mage::helper('mageworx_customoptions');
        if (!$helper->canShowQtyPerOptionInCart()) return $this;        
        $product = $item->getProduct();
        // if bad magento))
        if (is_null($product->getHasOptions())) $product->load($product->getId());        
        if (!$product->getHasOptions()) return $this;
        $store = $product->getStore();
        
        $optionIds = $item->getOptionByCode('option_ids');
        if ($optionIds) {
            
            $filter = new Zend_Filter();
            $filter->addFilter(new Zend_Filter_StripTags());
            
            $post = $helper->getInfoBuyRequest($product);
            
            foreach (explode(',', $optionIds->getValue()) as $optionId) {
                $option = $product->getOptionById($optionId);
                if ($option) {
                    $option->setProduct($product);
                    $optionQty = null;
                    $qty = $item->getQty();
                    if ($qty==0) $qty = 1;
                    switch ($option->getType()) {
                        case 'checkbox':
                        case 'multiswatch':
                        case 'hidden':
                            if (isset($post['options'][$optionId])) {                                                                
                                $optionValues = array();
                                foreach ($option->getValues() as $key=>$value) {
                                    $optionQty = $helper->getPostCustomoptionQty($product, $option, $value, $post);
                                    if (!isset($post['options'][$optionId]) || in_array($value->getOptionTypeId(), $post['options'][$optionId])) {
                                        $optionTotalQty = ($option->getCustomoptionsIsOnetime()?$optionQty:$optionQty*$qty);
                                        if ($value->getOrigTitle()) $value->setTitle($value->getOrigTitle()); else $value->setOrigTitle($value->getTitle());
                                    	$value->setTitle($filter->filter(($optionTotalQty>1?$optionTotalQty.' x ':'') . $value->getTitle() . $this->getOptionFormatPrice($value, $optionTotalQty, $product, $item->getQuote())));
                                    }
                                    $optionValues[$key]=$value;
                                }
                                $option->setValues($optionValues);
                                break;                                
                            }
                            break;
                        case 'drop_down':
                        case 'swatch':
                        case 'radio':  
                            $optionQty = $helper->getPostCustomoptionQty($product, $option, null, $post);
                        case 'multiple':
                            if (!isset($optionQty)) $optionQty = 1;
                            $optionValues = array();                            
                            $optionTotalQty = ($option->getCustomoptionsIsOnetime()?$optionQty:$optionQty*$qty);
                            foreach ($option->getValues() as $key=>$value) {                                
                                if (!isset($post['options'][$optionId]) || $value->getOptionTypeId()==$post['options'][$optionId]) {
                                    if ($value->getOrigTitle()) $value->setTitle($value->getOrigTitle()); else $value->setOrigTitle($value->getTitle());
                                    $value->setTitle($filter->filter(($optionTotalQty>1?$optionTotalQty.' x ':'') . $value->getTitle() . $this->getOptionFormatPrice($value, $optionTotalQty, $product, $item->getQuote())));
                                }
                                $optionValues[$key]=$value;
                            }
                            $option->setValues($optionValues);
                            break;
                        case 'field':
                        case 'area':
                        case 'file':
                        case 'date':
                        case 'date_time':
                        case 'time':
                            $optionTotalQty = ($option->getCustomoptionsIsOnetime()?1:$qty);
                            if ($option->getOrigTitle()) $option->setTitle($option->getOrigTitle()); else $option->setOrigTitle($option->getTitle());                            
                            $option->setTitle($filter->filter(($optionTotalQty>1?$optionTotalQty.' x ':'') . $option->getTitle() . $this->getOptionFormatPrice($option, $optionTotalQty, $product, $item->getQuote())));
                            break;
                    }
                }
            }
        }
    }
    
}
