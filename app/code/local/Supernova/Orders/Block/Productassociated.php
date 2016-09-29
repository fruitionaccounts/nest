<?php

/*...
 *@team: supernova
  @email: supernova.cps@gmail.com
  @phone: +84989302850
  @package: filer product
 ...*/

class Supernova_Orders_Block_Productassociated extends Mage_Catalog_Block_Product_View_Type_Grouped
{

    protected $_product;
    
     public function getProduct()
    {
        $productId = $this->_product;
        $product = Mage::getModel('catalog/product')->load($productId);
        if (is_null($product->getTypeInstance(true)->getStoreFilter($product))) {
            $product->getTypeInstance(true)->setStoreFilter(Mage::app()->getStore(), $product);
        }

        return $product;
    }
    public function getGallery($productId){
          $this->_product =  $productId;
    }

     public function getAddToCartUrl($product, $additional = array()) {
        if ($this->getRequest()->getParam('wishlist_next')) {
            $additional['wishlist_next'] = 1;
        }

        return $this->helper('checkout/cart')->getAddUrl($product, $additional);
    }
}