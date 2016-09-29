<?php

/*...
 *@team: supernova
  @email: supernova.cps@gmail.com
  @phone: +84989302850
  @package: filer product
 ...*/

class Supernova_Quote_Block_Quote extends Mage_Catalog_Block_Product_List
{       

    public function __construct() {
         parent::__construct(); 
        $this->setTemplate('catalog/product/list-quote.phtml');
    }

	 protected function _getProductCollection() {
        if (is_null($this->_productCollection)) {
            $layer = Mage::getSingleton('catalog/layer');
            /* @var $layer Mage_Catalog_Model_Layer */
            if ($this->getShowRootCategory()) {
                $this->setCategoryId(Mage::app()->getStore()->getRootCategoryId());
            }
            // if this is a product view page
            if (Mage::registry('product')) {
                // get collection of categories this product is associated with
                $categories = Mage::registry('product')->getCategoryCollection()
                        ->setPage(1, 1)
                        ->load();
                // if the product is associated with any category
                if ($categories->count()) {
                    // show products from this category
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
            $layerFilter = $layer->getProductCollection() ->addAttributeToFilter('type_id', array('eq' => 'grouped'));;
            $this->_productCollection = $layerFilter;

            $this->prepareSortableFieldsByCategory($layer->getCurrentCategory());

            if ($origCategory) {
                $layer->setCurrentCategory($origCategory);
            }
        }
        return $this->_productCollection;
    }
    public function getColorToFilter(){
        $resource = Mage::getModel('quote/attribute');
        return $resource-> getColorToFilter();
    }

    public function getColorHtmlSelect()
    {
        $select = $this->getLayout()->createBlock('core/html_select')
                ->setName('color')
                ->setId('color:dropdown')
                ->setTitle(Mage::helper('core')->__('color'))
                ->setExtraParams('onchange="getProductFilter(this.value)"')
                ->setOptions($this->getColorToFilter());
        return $select->getHtml();
    }
}