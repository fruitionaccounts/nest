<?php
class MageLynx_ImportExportPlugin_Block_Adminhtml_Product_Grid extends Mage_Adminhtml_Block_Catalog_Product_Grid
{
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('product');
        $this->getMassactionBlock()->addItem('export_product', array(
             'label'=> Mage::helper('importexportplugin')->__('Export into product spreadsheet'),
             'url'  => Mage::getUrl('importexportplugin/adminhtml_product/export',array('_secure'=>true)),
             'selected' => true
        ));
        return $this;
    }

    protected function _toHtml(){
        if(Mage::getStoreConfig('importexportplugin/products_grid/enable_mediatool')){
            //die('tst');
            $this->setTemplate('magelynx_importexportplugin/grid.phtml');
        }
        return parent::_toHtml() . $this->getLayout()->createBlock('adminhtml/template', 'additional_javascript', array(
                                                    'parent_block' => $this,
                                                    'template' => 'magelynx_importexportplugin/product/grid/additional_javascript.phtml',
                                            ))->toHtml();
    }

    public function getGridUrl()
    {
        return $this->getUrl('importexportplugin/adminhtml_index/grid');
    }

    public function getRowUrl($row){
        return "javascript:$$('input[name=product][value=".$row->getId()."]')[0].click();";
    }

    public function addCustomColumn($column){
        $type = 'options';
        if($column['value'] == 'images'){
            $type = 'number';            
        }elseif($column['value'] == 'categories'){
            $type = 'text';            
        }

        $column_data = array(
            'header' => $column['name'],
            'index'  => $column['value'],
            'type' => $type,
            'width' => '50px'
        );
        $column_data['filter_condition_callback'] = array($this, '_customFieldFilter');
        $column_data['renderer'] = 'importexportplugin/adminhtml_product_column';

        if($type == 'options'){
            $column_data['options'] = array(
                                            1 => Mage::helper('importexportplugin')->__('Yes'),
                                            2 => Mage::helper('importexportplugin')->__('No'),
                                                );
        }

        $column = $this->addColumn($column['value'], $column_data);

    }

    protected function _prepareColumns(){
        parent::_prepareColumns();
        $this->_rssLists = array();
        $this->addColumn('action',
            array(
                'header'    => Mage::helper('catalog')->__('Action'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'     => 'getId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('catalog')->__('Edit'),
                        'url'     => array(
                            'base'=>'adminhtml/catalog_product/edit',
                            'params'=>array('store'=>$this->getRequest()->getParam('store'))
                        ),
                        'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
        ));

        foreach(Mage::getModel('importexportplugin/source_column')->getNativeColumns() as $native_column){
            if(!in_array($native_column['value'], explode(',', Mage::getStoreConfig('importexportplugin/products_grid/columns')))){
                unset($this->_columns[($native_column['value'])]);
            }
        }

        $custom_columns= array_unique(array_merge(array('images'), explode(',', Mage::getStoreConfig('importexportplugin/products_grid/columns'))));
        foreach(Mage::getModel('importexportplugin/source_column')->getCustomColumns() as $custom_column){
            if(in_array($custom_column['value'], $custom_columns)){
                $this->addCustomColumn($custom_column);
            }
        }

        return $this;
    }
    protected function _prepareLayout()
    { 
         $this->setChild('store_switcher', $this->getLayout()
                                                ->createBlock('adminhtml/store_switcher', 'store_switcher')
                                                    ->setUseConfirm(0));

        parent::_prepareLayout();
        $this->setChild('reset_filter_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('adminhtml')->__('Refresh / Reset'),
                    'onclick'   => $this->getJsObjectName().'.resetFilter()',
                ))
        );

        return $this;
    }



    public function __construct()
    {
        parent::__construct();
        $this->setId('importexportplugin_product_grid');
        $this->setUseAjax(true);
        $this->setDefaultSort('entity_d');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }
    protected function _prepareCollection()
    {
        $store = $this->_getStore();
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('attribute_set_id')
            ->addAttributeToSelect('type_id');

        if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
            $collection->joinField('qty',
                'cataloginventory/stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left');
        }
        if ($store->getId()) {
            $adminStore = Mage_Core_Model_App::ADMIN_STORE_ID;
            $collection->addStoreFilter($store);
            $collection->joinAttribute(
                'name',
                'catalog_product/name',
                'entity_id',
                null,
                'inner',
                $adminStore
            );
            $collection->joinAttribute(
                'custom_name',
                'catalog_product/name',
                'entity_id',
                null,
                'inner',
                $store->getId()
            );
            $collection->joinAttribute(
                'status',
                'catalog_product/status',
                'entity_id',
                null,
                'inner',
                $store->getId()
            );
            $collection->joinAttribute(
                'visibility',
                'catalog_product/visibility',
                'entity_id',
                null,
                'inner',
                $store->getId()
            );
            $collection->joinAttribute(
                'price',
                'catalog_product/price',
                'entity_id',
                null,
                'left',
                $store->getId()
            );
        }
        else {
            $collection->addAttributeToSelect('price');
            $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
            $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');
        }

        $this->setCollection($collection);
        ///end old logic, new logic below

        foreach(Mage::getModel('importexportplugin/source_column')->getCustomColumns() as $custom_column){
            if(in_array($custom_column['value'], explode(',', Mage::getStoreConfig('importexportplugin/products_grid/columns')))){

                $this->addExpr($custom_column['value']);
                
            }
        };

        //end new logic, old logic (of ~_Widget class, copypasted) below

        $this->sortColumnsByOrder();

        if ($this->getCollection()) {

            $this->_preparePage();

            $columnId = $this->getParam($this->getVarNameSort(), $this->_defaultSort);
            $dir      = $this->getParam($this->getVarNameDir(), $this->_defaultDir);
            $filter   = $this->getParam($this->getVarNameFilter(), null);

            if (is_null($filter)) {
                $filter = $this->_defaultFilter;
            }

            if (is_string($filter)) {
                $data = $this->helper('adminhtml')->prepareFilterString($filter);
                $this->_setFilterValues($data);
            }
            else if ($filter && is_array($filter)) {
                $this->_setFilterValues($filter);
            }
            else if(0 !== sizeof($this->_defaultFilter)) {
                $this->_setFilterValues($this->_defaultFilter);
            }

            if (isset($this->_columns[$columnId]) && $this->_columns[$columnId]->getIndex()) {

                $dir = (strtolower($dir)=='desc') ? 'desc' : 'asc';
                $this->_columns[$columnId]->setDir($dir);
 
                if(strpos(Mage::getVersion(),'1.4.') === 0){
                    $column = $this->_columns[$columnId]->getFilterIndex() ?
                        $this->_columns[$columnId]->getFilterIndex() : $this->_columns[$columnId]->getIndex();
                        $this->getCollection()->setOrder($column , $dir);
                }else{               
                    $this->_setCollectionOrder($this->_columns[$columnId]);
                }

            }

            if (!$this->_isExport) {
                $this->getCollection()->load();
                $this->_afterLoadCollection();
            }
        }



        return $this;
    }


    public function addExpr($column_index){
        $collection = $this->getCollection();
        
        $columns = $collection->getSelect()->getPart(Zend_Db_Select::COLUMNS);
        foreach($columns as $group){
            if(in_array($column_index, $group)){
                return $this;
            }
        }
        if($column_index =='images'){
            $exp =  new Zend_Db_Expr('cast( (select count(*) from '.
                     Mage::getSingleton('core/resource')->getTableName('catalog/product_attribute_media_gallery').
                ' where entity_id = e.entity_id) as unsigned) ');                  

        }elseif($column_index =='has_children_configurable'){
            $exp =  new Zend_Db_Expr('cast( (select IF(count(*) > 0, 1, 2)  from '.
                     Mage::getSingleton('core/resource')->getTableName('catalog/product_super_link').
                ' where parent_id = e.entity_id AND e.type_id = "configurable") as unsigned) ');                  
        }elseif($column_index =='is_children_configurable'){
            $exp =  new Zend_Db_Expr('cast( (select IF(count(*) > 0, 1, 2)  from '.
                     Mage::getSingleton('core/resource')->getTableName('catalog/product_super_link'). ' as sl '.
                ' inner join '.Mage::getSingleton('core/resource')->getTableName('catalog/product').' as par_ent on par_ent.entity_id = sl.parent_id and par_ent.type_id = "configurable"'.
                ' where product_id = e.entity_id) as unsigned) ');                  



        }elseif($column_index =='has_children_grouped'){//grouped
            $grouped_link_id = Mage_Catalog_Model_Product_Link::LINK_TYPE_GROUPED;
            $exp =  new Zend_Db_Expr('cast( (select IF(count(*) > 0, 1, 2)  from '.
                    Mage::getResourceModel('catalog/product_link')->getMainTable().
                ' where product_id = e.entity_id and link_type_id = '.$grouped_link_id.') as unsigned) ');                 
            //die($exp) ;
        }elseif($column_index =='is_children_grouped'){           
            $grouped_link_id = Mage_Catalog_Model_Product_Link::LINK_TYPE_GROUPED;
            $exp =  new Zend_Db_Expr('cast( (select IF(count(*) > 0, 1, 2)  from '.
                    Mage::getResourceModel('catalog/product_link')->getMainTable().
                ' where linked_product_id = e.entity_id and link_type_id = '.$grouped_link_id.') as unsigned) ');
//            die($exp);

        }elseif($column_index =='has_related'){//related
            $link_id = Mage_Catalog_Model_Product_Link::LINK_TYPE_RELATED;
            $exp =  new Zend_Db_Expr('cast( (select IF(count(*) > 0, 1, 2)  from '.
                    Mage::getResourceModel('catalog/product_link')->getMainTable().
                ' where product_id = e.entity_id and link_type_id = '.$link_id.') as unsigned) ');                 
        }elseif($column_index =='has_crosssell'){//crosssell
            $link_id = Mage_Catalog_Model_Product_Link::LINK_TYPE_CROSSSELL;
            $exp =  new Zend_Db_Expr('cast( (select IF(count(*) > 0, 1, 2)  from '.
                    Mage::getResourceModel('catalog/product_link')->getMainTable().
                ' where product_id = e.entity_id and link_type_id = '.$link_id.') as unsigned) ');                 
        }elseif($column_index =='has_upsell'){//upsell
            $link_id = Mage_Catalog_Model_Product_Link::LINK_TYPE_UPSELL;
            $exp =  new Zend_Db_Expr('cast( (select IF(count(*) > 0, 1, 2)  from '.
                    Mage::getResourceModel('catalog/product_link')->getMainTable().
                ' where product_id = e.entity_id and link_type_id = '.$link_id.') as unsigned) ');                 
        }elseif($column_index =='has_custom_options'){//upsell           
            $exp =  new Zend_Db_Expr('cast( (select IF(count(*) > 0, 1, 2)  from '.
                     Mage::getSingleton('core/resource')->getTableName('catalog/product_option').
                ' where product_id = e.entity_id) as unsigned) ');                 

        }elseif($column_index =='categories'){
            $__collection = Mage::getResourceModel('catalog/category_attribute_collection');

            $name_attribute_id = $__collection->addFieldToFilter('attribute_code', 'name')
                            ->getFirstItem()->getId();
            $category_product = Mage::getSingleton('core/resource')->getTableName('catalog/category_product');
            $category =Mage::getSingleton('core/resource')->getTableName('catalog/category');
            $name_table = Mage::getSingleton('core/resource')->getTableName('catalog_category_entity_varchar');

            $select = ''.
                                        ' select outer_cat.entity_id as p_category_id, group_concat(_cat_name.value order by outer_cat.entity_id, _outer_cat.path separator "/" ) as _name_path '.
                                        ' from '.$category.' as outer_cat '.
                                        ' inner join '.$category.' as _outer_cat  on concat("/",outer_cat.path,"/") like  concat("%/",_outer_cat.entity_id, "/%")  and _outer_cat.level>=2'.

                                        ' inner join '.$name_table.' as _cat_name on _cat_name.entity_id = _outer_cat.entity_id '.
                                                            ' and  attribute_id = '.$name_attribute_id.' '.

                                        ' group by outer_cat.entity_id '.
                                        ' order by outer_cat.entity_id, _outer_cat.path ';


            $exp =  new Zend_Db_Expr('( select group_concat(_name_path separator "\n") from ('.$select.') as _inner_select  where p_category_id in (select category_id from '.$category_product.' where product_id = e.entity_id ) )');                


        }else{
            var_dump($column_index. ' '.get_class($this) . ' '. __LINE__);die();

        }

try{
        $collection->addExpressionAttributeToSelect($column_index, $exp, array());        
        $collection->addFilterToMap($column_index, $exp); 

}catch(Exception $e){ die($e->getMessage() . ' ' .__LINE__); }
        $this->setCollection($collection);

    }
    protected function _customFieldFilter($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
        $index = $column->getIndex();
        $val = $column->getFilter()->getValue();

        $this->addExpr($index);

        $collection = $this->getCollection();
        if($index =='images'){

            if(isset($val['from']) && (string)$val['from'] != ''){
                $collection->addFieldToFilter('images', array('from' => $val['from']));
            }
            if(isset($val['to']) && (string)$val['to'] != ''){
                $collection->addFieldToFilter('images', array('to' => $val['to']));
            }

        }elseif(in_array($index,array(
                                        'has_children_configurable',    
                                        'is_children_configurable',
                                           'has_children_grouped',
                                           'is_children_grouped',
                                           'has_related',
                                           'has_upsell',
                                           'has_crosssell',
                                           'has_custom_options',

                                    ))){
            if(strlen($val)!=0) {
                $collection->addFieldToFilter($index, array('eq' => $val));
            }
        }elseif($index=='categories'){
           $collection->addFieldToFilter($index, array('like' => '%'.$val.'%'));
        }

        $this->setCollection($collection);

        return $this;
    }

}
