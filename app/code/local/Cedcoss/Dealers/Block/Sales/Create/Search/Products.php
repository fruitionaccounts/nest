<?php
class Cedcoss_Dealers_Block_Sales_Create_Search_Products extends Mage_Catalog_Block_Product_Abstract
{
	protected $_collection;
	public $leafNode;
	public function __construct()
    {   
		parent::__construct();
		$this->setTemplate('dealers/products.phtml');		
    }
    
    public function getToolbarHtml($id)
    { 
	
		$toolbar = $this->getLayout()->createBlock('dealers/html_pagination', 'dealers_products.toolbar')
				   ->setCollection($this->_getCollection($id), $id);	
		$name = 'toolbar_'.$id;	
		$this->setChild($name, $toolbar);
		
        return $this->getChildHtml($name);
	
		/* return ''; */
    }
   
	protected function _getCollection($id)
    { 		
		
        $attributes = Mage::getSingleton('catalog/config')->getProductAttributes();
        /* @var $collection Mage_Catalog_Model_Resource_Product_Collection */
		$collection = array();
		$currentPage = 1;
		$page_size = 10;
		$page_id = Mage::getSingleton('core/session')->getData($name);
		$name = 'page_'.$id;
		
		if(isset($_GET['id']))
		{
			$p_id = $_GET['id'];
			if($p_id == $id)
			{
				$currentPage = $_GET['p'];
			}
			
		}
		else if(isset($page_id))
			$currentPage = Mage::getSingleton('core/session')->getData($name);
			
			
		if(isset($_GET['limit_id']))
		{
			$l_id = $_GET['limit_id'];
			if($l_id == $id)
			{
				$page_size = $_GET['limit'];
			}
			
		}
	
		
        $collection = Mage::getModel('catalog/category')->load($id)->getProductCollection();/* Mage::getModel('catalog/product')->getCollection()
		->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id = entity_id', null, 'left')
		->addAttributeToSelect('*')
		->addAttributeToFilter('category_id', array('eq' => $id)); */
        $collection
            ->setStore($this->getStore())
            ->addAttributeToSelect($attributes)
            ->addAttributeToSelect('sku')
            ->addStoreFilter()
            ->addAttributeToFilter('type_id', array_keys(
                Mage::getConfig()->getNode('adminhtml/sales/order/create/available_product_types')->asArray()
            ))
            ->setOrder('sku', 'ASC')
            ->addAttributeToSelect('gift_message_available')->setOrder('position');
			/* ->setCurPage($currentPage)->setPageSize($page_size); */
            
		
									 
        Mage::getSingleton('catalog/product_status')->addSaleableFilterToCollection($collection);
		
        return $collection;
    }

    public function getCollection()
    {
        $collection = array();
		$categories = $this->getStoreCategories();
		
		foreach($categories as $category){
		
			$collection[$category->getId().'|'.$category->getName()] = $this->_getCollection($category->getId());
		}
		
		return $collection;
    }

    public function getStoreCategories(){
		$code=Mage::app()->getStore()->getRootCategoryId();
		$arr = array();
		$category_model=Mage::getModel('catalog/category');
		$cat=Mage::getModel('catalog/category')->load($code);
		/* $all_child_categories = $category_model->getResource()->getAllChildren($cat);
		$collection = Mage::getModel('catalog/category')->getCollection()
		->addAttributeToFilter('entity_id', array('in' => $all_child_categories))
		
		->addAttributeToSelect('*')
		->setOrder('position', 'asc');

		$i=0;
		foreach($collection as $subcat )
		{
			
			//$sub=Mage::getModel('catalog/category')->load($subcat->getId());
		 if($subcat->getChildrenCount()==0)
			$arr[]=$subcat;
			
		} */
		$this->leafNode=array();
		$this->getLeafNodes($cat);
		return $this->leafNode;
	}
	public function getLeafNodes($category)
	{
		/*
		$children = Mage::getModel('catalog/category')->getCategories($category->getId(),0,false,true,true)
			->addAttributeToFilter('include_in_menu',0)
		;*/
		//
		$children = Mage::getResourceModel('catalog/category_collection')
    ->addAttributeToSelect('*')
    ->addAttributeToFilter('is_active', 1) //only active categories
    ->addAttributeToFilter('include_in_menu', array('gteq'=>0)) //also categories not included in menu (FLJ, 10/23/15)
    ->addAttributeToFilter('parent_id', $category->getId())//get only subcategories of the category with id 10
    ->addAttributeToSort('position')
			;
		foreach ($children as $category) 
		{
			if($category->hasChildren()){
				$this->getLeafNodes($category,$leaf);
			}
			else
			{
				$cat=Mage::getModel('catalog/category')->load($category->getId());
				if(Mage::getModel('catalog/layer')->setCurrentCategory( $cat)->getProductCollection()->getSize())
				$this->leafNode[]=$cat;
			
				
			}
		}
		return $leaf;
	}
}