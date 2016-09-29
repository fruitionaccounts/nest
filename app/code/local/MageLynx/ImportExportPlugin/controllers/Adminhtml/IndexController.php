<?php
class MageLynx_ImportExportPlugin_Adminhtml_IndexController  extends Mage_Adminhtml_Controller_Action{
    public function indexAction(){
        $this->loadLayout();
        $this->getLayout()->getBlock('pu_ajax')->setSlug($this->getRequest()->getParam('type','export'));
        if( $this->getLayout()->getBlock('pu_ajax')->getSlug()== 'export'){
            $this->getLayout()->getBlock('pu_ajax')
                                            ->setUrlSlug('importexportplugin/adminhtml_product/exportAjax')
                                            ->setPrefix( Mage::helper('importexportplugin')->__('Export: '));
        }else{
            $this->getLayout()->getBlock('pu_ajax')                                            
                                            ->setUrlSlug('importexportplugin/adminhtml_product/importAjax')
                                            ->setPrefix( Mage::helper('importexportplugin')->__('Import: '));

        }
        $this->renderLayout();
    }
    public function gridAction(){
        $this->loadLayout(false);
        $this->renderLayout();
    }
    public function updateImagesAction(){
//        var_dump($this->getRequest()->getParams()); die();
        //$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        require_once Mage::getBaseDir().str_replace("/", DS,'/magelynx_importexportplugin/magmi/inc/magmi_defs.php');
        require_once Mage::getBaseDir().str_replace("/", DS,'/magelynx_importexportplugin/magmi/integration/inc/magmi_datapump.php');

        $dp=Magmi_DataPumpFactory::getDataPumpInstance("productimport");
        $dp->beginImportSession("default","update");
        Mage::helper('importexportplugin')->checkMagmiDbAccess();
        $images_to_unlink = array();
        foreach($this->getRequest()->getParams() as $k=>$param){            
            if(strpos($k, 'product_') === 0 && ($product_sku = substr($k, strlen('product_')))){
                $images = array();
                foreach(explode(",", $param) as $image){
                    $full_path = Mage::getBaseDir().$image;
                    $master_path =Mage::helper('importexportplugin')->getImageFolder('mediabuffer').DS.basename($image);
                    $images_to_unlink[]=$master_path;
                    copy($full_path, $master_path);                            
                    $images[]='mediabuffer'.DS.basename($image);
                }
                $med_gal = join(";",$images);
//                $param = str_replace(",",";", $param);
                if(!empty($images)){
//                    var_dump($images);die();
                }
                if(!Mage::getStoreConfig('importexportplugin/images/exclude_main')){
                    //$images[0] = '+'.$images[0];
                }

                //$product_data = array(                    
                //);  
  


                $product_data = array(              
                    'sku' => $product_sku,          
                    'image' => $images[0],          
                    'small_image' => $images[0],    
                    'thumbnail' => $images[0],
                    'media_gallery' => $med_gal
                );
//                var_dump($product_data);die();

                $dp->ingest($product_data);

            }
        }
        $dp->endImportSession();
                foreach($images_to_unlink as $fname){
                    //unlink($fname);
                }

    }

}

/*
return;
class MageLynx_ImportExportPlugin_Adminhtml_IndexController extends MageLynx_ImportExportPlugin_Adminhtml_AbstractController {          
//    protected $_publicActions = array('import');

    public function imagesAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function updateImagesAction(){
//        var_dump($this->getRequest()->getParams()); die();
        //$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        require_once Mage::getBaseDir().str_replace("/", DS,'/magelynx_importexportplugin/magmi/inc/magmi_defs.php');
        require_once Mage::getBaseDir().str_replace("/", DS,'/magelynx_importexportplugin/magmi/integration/inc/magmi_datapump.php');

        $dp=Magmi_DataPumpFactory::getDataPumpInstance("productimport");
        $dp->beginImportSession("default","update");
        Mage::helper('importexportplugin')->checkMagmiDbAccess();
        $images_to_unlink = array();
        foreach($this->getRequest()->getParams() as $k=>$param){            
            if(substr_count($k, 'product_') && ($product_sku = str_replace('product_', '', $k))){
                $images = array();
                foreach(explode(",", $param) as $image){
                    $full_path = Mage::getBaseDir().$image;
                    $master_path =Mage::helper('importexportplugin')->getImageFolder('mediabuffer').DS.basename($image);
                    $images_to_unlink[]=$master_path;
                    copy($full_path, $master_path);                            
                    $images[]=DS.'mediabuffer'.DS.basename($image);
                }
//                $param = str_replace(",",";", $param);
                if(!empty($images)){
//                    var_dump($images);die();
                }
                if(!Mage::getStoreConfig('importexportplugin/images/exclude_main')){
                    $images[0] = '+'.$images[0];
                }
                //$product_data = array(                    
                //);  
  


                $product_data = array(              
                    'sku' => $product_sku,          
                    'image' => $images[0],          
                    'small_image' => $images[0],    
                    'thumbnail' => $images[0],
                    'media_gallery' => join(";",$images)
                );
                //var_dump($product_data);die();
                $dp->ingest($product_data);
                foreach($images_to_unlink as $fname){
                    unlink($fname);
                }

            }
        }
        $dp->endImportSession();
    }
    public function imagesGridAction()
    {
        //false means no "default" layout update handler
        $this->loadLayout(false);
        $this->renderLayout();
    }

    public function exportAction(){
        $helper = Mage::helper('importexportplugin');
        $session = Mage::getSingleton('adminhtml/session');
        $type = $this->getRequest()->getParam('export_type');
        $login = Mage::getStoreConfig('importexportplugin/settings/google_login');
        $password =Mage::helper('core')->decrypt( Mage::getStoreConfig('importexportplugin/settings/google_password'));
//        var_dump($login . " " . $password);die();
        $url =  Mage::getStoreConfig('importexportplugin/'.$type.'/url');
   
        if($httpClient = $this->_getGoogleHttpClient()){
            $service = new Zend_Gdata_Spreadsheets($httpClient); 
            try{

                $query = new Zend_Gdata_Spreadsheets_DocumentQuery();
                preg_match_all('/key=(([^&#])+)/', $url, $matches);
                $key = $matches[1][0];            
                $query->setSpreadsheetKey($key);

                $feed = $service->getWorksheetFeed($query);
                if(count($feed->entries) > 1 && !$this->getRequest()->getParam('ws_id', false)){
                    $ul='';
                    foreach($feed->entries as $entry){
                        $ul .= "<li><a href='".$this->getUrl("c/c/c", array('_current' => true, 'ws_id' => basename($entry->id)))."'>".$entry->title->getText()."</a></li>";
                    }
                    $ul = "<ul id='ws-list'>$ul</ul><style>#ws-list {list-style-type:disc; list-style-position:inside;} #ws-list li {padding:0!important; margin:0!important; height:auto!important; min-height:0!important;}</style>";
                    $session->addNotice( $helper->__('There are multiple worksheets. Please select one for inserting the items which are not present yet in the spreadsheet: %s',$ul ));
                    $this->_redirect('adminhtml/system_config/edit', array('section' => 'importexportplugin'));
                }else{
                    $result = $helper->export($service, $url, $this->getRequest()->getParam('ws_id', false));
                    if($result['error']){
                        $session->addError($result['message']);
                    }else{
                        $session->addSuccess($result['message']);
                    }
                    $this->_redirect('adminhtml/system_config/edit', array('section' => 'importexportplugin'));
                }

            }catch(Exception $e){
                $session->addError($e->getMessage());
                $this->_redirect('adminhtml/system_config/edit', array('section' => 'importexportplugin'));                  
            }
        }


//        var_dump($this->getRequest()->getParams());die();
    }

    public function importAction(){
        $session = Mage::getSingleton('adminhtml/session');
        $type = $this->getRequest()->getParam('import_type');
        $url =  Mage::getStoreConfig('importexportplugin/'.$type.'/url');
        if($httpClient = $this->_getGoogleHttpClient()){
            $service = new Zend_Gdata_Spreadsheets($httpClient); 
            try{
                $result = Mage::helper('importexportplugin')->import($service, $url);
                if($result['error']){
                    $session->addError($result['message']);
                }else{
                    $session->addSuccess($result['message']);
                }
                $this->_redirect('adminhtml/system_config/edit', array('section' => 'importexportplugin'));

            }catch(Exception $e){
                $session->addError($e->getMessage());
                $this->_redirect('adminhtml/system_config/edit', array('section' => 'importexportplugin'));                  
            }
        }


    } 
}    
*/
