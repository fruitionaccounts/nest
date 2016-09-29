<?php

class MageLynx_ImportExportPlugin_Model_Observer{
    public function toHtmlBefore($event){
        $block = $event->getBlock();
        if($block instanceof Mage_Adminhtml_Block_System_Config_Edit ){
//&& Mage::app()->getRequest()->getControllerName() =='catalog_product'
            //Mage::log($block->getData());
        }
    }
    
    public function adminPreDispatch(){
        if(Mage::app()->getRequest()->getParam('section') == 'importexportplugin' && Mage::app()->getRequest()->getActionName() == 'edit'){

            Mage::getSingleton('adminhtml/session')->setData('active-settings', true);
            Mage::app()->getResponse()->setRedirect(Mage::helper("adminhtml")->getUrl('importexportplugin/adminhtml_index/index'))->sendResponse();
                die();
        }
    }
}
