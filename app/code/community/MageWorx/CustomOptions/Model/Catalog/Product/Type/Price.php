<?php

/**
 * MageWorx
 * CustomOptions Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_CustomOptions
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomOptions_Model_Catalog_Product_Type_Price extends MageWorx_CustomOptions_Model_Catalog_Product_Type_Price_Abstract {

    /**
     * Apply options price
     *
     * @param Mage_Catalog_Model_Product $product
     * @param int $qty
     * @param double $finalPrice
     * @return double
     */         
     
    protected function _applyOptionsPrice($product, $qty, $finalPrice) {
        if ($optionIds = $product->getCustomOption('option_ids')) {
            $helper = Mage::helper('mageworx_customoptions');
            $basePrice = $finalPrice;
            $product->setActualPrice($basePrice);
            $finalPrice = 0;
            $post = $helper->getInfoBuyRequest($product);
            
            foreach (explode(',', $optionIds->getValue()) as $optionId) {
                if ($option = $product->getOptionById($optionId)) {
                    $option->setProduct($product);
                                     
                    switch ($option->getType()) {
                        case 'checkbox':
                        case 'multiswatch':
                        case 'hidden':
                            if (isset($post['options'][$optionId])) {                                                                
                                $optionValues = array();
                                $optionQtyArr = array();
                                foreach ($option->getValues() as $key=>$itemV) {                                    
                                    $optionQty = $helper->getPostCustomoptionQty($product, $option, $itemV, $post);
                                    $optionQtyArr[$itemV->getOptionTypeId()] = $optionQty;
                                }
                                $optionQty = $optionQtyArr;                                
                            }
                            break;
                        case 'drop_down':
                        case 'radio':
                        case 'swatch': 
                            $optionQty = $helper->getPostCustomoptionQty($product, $option, null, $post);
                            break;
                    }

                    if ($option->getGroupByType()==Mage_Catalog_Model_Product_Option::OPTION_GROUP_SELECT) {
                        $quoteItemOption = $product->getCustomOption('option_' . $option->getId());
                        $group = $option->groupFactory($option->getType())->setOption($option)->setQuoteItemOption($quoteItemOption);
                        $finalPrice += $group->getOptionPrice($quoteItemOption->getValue(), $basePrice, $qty, $optionQty, $product);
                    } else {
                        $price = $helper->getOptionPriceByQty($option, $qty, $product);
                        if ($price!=0) $price = $price / $qty;
                        $finalPrice += $price;
                    }
                }
            }
            $product->setBaseCustomoptionsPrice($finalPrice); // for additional info
            if (!$helper->getProductAbsolutePrice($product) || $finalPrice==0) $finalPrice += $basePrice;
        }        
        if (method_exists($this, '_applyOptionsPriceFME')) $finalPrice = $this->_applyOptionsPriceFME($product, $qty, $finalPrice);
        return $finalPrice;
    }

}