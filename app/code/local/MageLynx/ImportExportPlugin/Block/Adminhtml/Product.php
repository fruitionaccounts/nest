<?php
class MageLynx_ImportExportPlugin_Block_Adminhtml_Product extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    protected $_blockGroup = 'importexportplugin';
    public function isSingleStoreMode()
    {
        if (!Mage::app()->isSingleStoreMode()) {
               return false;
        }
        return true;
    }

    public function __construct()
    {
        $this->_controller = 'adminhtml_product';

        parent::__construct();                      

        $this->_headerText = Mage::helper('importexportplugin')->__('Products');
        $this->_removeButton('add');
    }

}
