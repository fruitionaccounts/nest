<?php 

/*...
 *@team: supernova
  @email: supernova.cps@gmail.com
  @phone: +84989302850
  @package: filer product
 ...*/

class Supernova_Quote_Block_Products extends Mage_Catalog_Block_Product_List
{

        const COLOR = 'color';

        public function __construct() {
             parent::__construct();
            $this->setTemplate('catalog/product/product-ajax-list.phtml');
        }
	 protected function _getProductCollection() {

         $attribute = $this->getRequest()->getParam('attributeID');
        if (is_null($this->_productCollection)) {

            $layer = Mage::getSingleton('catalog/layer');
          
            if ($this->getShowRootCategory()) {
                $this->setCategoryId(Mage::app()->getStore()->getRootCategoryId());
            }
            if (Mage::registry('product')) {
                $categories = Mage::registry('product')->getCategoryCollection()
                        ->setPage(1, 1)
                        ->load();
                if ($categories->count()) {
                    $this->setCategoryId(current($categories->getIterator()));
                }
            }

            $origCategory = null;
            if ($this->getCategoryId()) {
                $category = Mage::getModel('catalog/category')->load($this->getCategoryId());
                if ($category->getId()) {
                    $origCategory = $layer->getCurrentCategory();
                    $layer->setCurrentCategory($category);
                }
            }
            $layerFilter = $layer->getProductCollection() 
                    ->addAttributeToFilter('type_id', array('eq' => 'grouped'))
                    ->addAttributeToFilter(self::COLOR, $attribute, 'left');
            $this->_productCollection = $layerFilter;

            $this->prepareSortableFieldsByCategory($layer->getCurrentCategory());

            if ($origCategory) {
                $layer->setCurrentCategory($origCategory);
            }
        }
        return $this->_productCollection;
    }
    
}