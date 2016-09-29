<?php

/*...
 *@team: supernova
  @email: supernova.cps@gmail.com
  @phone: +84989302850
  @package: filer product
 ...*/

class Supernova_Quote_Block_Product extends Mage_Catalog_Block_Product_View_Type_Grouped 
{

        public function __construct() {
             parent::__construct();
            $this->setTemplate('catalog/product/product-ajax-quote.phtml');
        }

     public function getProduct()
    {
        $productId = $this->getRequest()->getParam('productId');
        $product = Mage::getModel('catalog/product')->load($productId);
        if (is_null($product->getTypeInstance(true)->getStoreFilter($product))) {
            $product->getTypeInstance(true)->setStoreFilter(Mage::app()->getStore(), $product);
        }

        return $product;
    }

     public function getAddToCartUrl($product, $additional = array()) {
        if ($this->getRequest()->getParam('wishlist_next')) {
            $additional['wishlist_next'] = 1;
        }

        return $this->helper('checkoutcart')->getAddUrl($product, $additional);
    }
}