<?php

class MageLynx_ImportExportPlugin_Adminhtml_ProductController extends Mage_Adminhtml_Controller_Action{         

	public $export_sheet_name = '#export_copy#';

    public function exportAction(){
        Mage::getSingleton('adminhtml/session')->setData('ids',  $this->getRequest()->getParam('product') );        
        $this->_redirect('importexportplugin/adminhtml_index/index', array('type' => 'export'));
    }

    public function importAction(){        
        Mage::getSingleton('adminhtml/session')->setData('ids',  array('detect') );        
        $this->_redirect('importexportplugin/adminhtml_index/index', array('type' => 'import'));
    }

    public function importAjaxAction(){
        $helper = Mage::helper('importexportplugin');
        $dialog = '';
        $out = '';
        $ids = '';
        $error_flag =false;
        try{
            $validation_result = $this->_validate();
		
            if( isset($validation_result) && $validation_result !== true ){
                $collection = Mage::helper('importexportplugin/gdata')->getSessionWorksheetCollection();
					//$collection = Mage::helper('importexportplugin/gdata')->getWorksheetCollection();
                $ids = array();
                foreach($collection->getItems() as $sheet){
                    try{
                        foreach($sheet->getRows() as $row_id => $row){
                            $ids []= $sheet->getId() . '-' . $row_id;
                        }
                    }catch(Exception $e){
                        // no need to catch. here we just filter out what is valid
                    }
                }

                $validation_result['reset_ids'] = $ids;

                Mage::getSingleton('adminhtml/session')->setData('just_validated',true);
                echo Zend_Json::encode($validation_result);
                die();
            }else{
                $product_ids = $this->getRequest()->getParam('product');
                if(!is_array($product_ids)){
                    $product_ids = explode(",", $product_ids);
                }
                ob_start();
                if(Mage::getSingleton('adminhtml/session')->getData('just_validated')){
                    Mage::getSingleton('adminhtml/session')->setData('just_validated',false);
					
					$collection = Mage::helper('importexportplugin/gdata')->setSessionWorksheetCollection();
					//$collection = Mage::helper('importexportplugin/gdata')->getWorksheetCollection();
                    $all_ids = array();
                    foreach($collection->getItems() as $sheet){
                        if( in_array($sheet->getId(), explode(',', Mage::app()->getRequest()->getParam('ws', '')))){
                            try{
                                foreach($sheet->getRows() as $row_id => $row){
                                    $all_ids []= $sheet->getId() . '-' . $row_id;
                                }
                            }catch(Exception $e){
                                echo $e->getMessage();
                            }                   
                        }
                    }

                    echo "<div>". $helper->__('Processing only selected spreadsheets...')."</div>";
                    $out = ob_get_contents();       
                    ob_end_clean();
                    echo Zend_Json::encode(array( 
                        'out' => $out, 
                        'session' => Mage::helper('importexportplugin/gdata')->getSession(),
                        'reset_ids' => $all_ids
                    ));
                    die();

                }



                //prepare for import

                $rows_by_sheet = array();
                $skus = array();
                foreach($product_ids as $complex_id){
                    list($sheet_id, $row_id) =  explode('-', $complex_id);
                    $collection = Mage::helper('importexportplugin/gdata')->getSessionWorksheetCollection();
					//$collection = Mage::helper('importexportplugin/gdata')->getWorksheetCollection();
                    foreach($collection->getItems() as $sheet){
                        if($sheet->getId() == $sheet_id){
                            $rows = $sheet->getRows();
                            $product_data = $rows[$row_id];
                            if(!isset($rows_by_sheet[$sheet_id])){
                                $rows_by_sheet[$sheet_id] = array();
                            }
                            $rows_by_sheet[$sheet_id][$row_id]=$product_data;
                        }
                    }
                }
                foreach($rows_by_sheet as $sheet_id => $products_data){
                    try{
                        Mage::helper('importexportplugin/data')->import($products_data);
                        foreach($products_data as $product_data){
                            $skus[]=$product_data['sku'];
                        }
                    }catch(Exception $e){
                        echo $e->getMessage();
                    }
                }

                //reporting
                echo "<div>";
                echo $helper->__('Processed skus: %s', join(',', array_unique($skus)));
                echo "</div>";
                $info_flag= false;
                foreach($helper->get_registry_key('images')  as $item){
                    if(!empty($item['images'])){
                        $info_flag = true;
                    }
                }
                if($info_flag){
                    echo "<div>";
                    echo "<u>".$helper->__("Found images for SKUs:")."</u> ";
                    $lines = array();
                    
                    foreach($helper->get_registry_key('images') as $sku_images){

                        if(!empty($sku_images['images']['ok'])){

                            $div_key = md5($sku_images['sku'].'okimage'.join("<br>",$sku_images['images']['ok']));
                            $lines []= "<b>".$sku_images['sku'] . "</b> ". $helper->__('<a onclick="%s" href="javascript:void(0);">[show/hide]</a>','if($(\''.$div_key.'\').visible()){$(\''.$div_key.'\').hide();}else{$(\''.$div_key.'\').show();}' ) ."<div style='display:none; border:1px solid #777; padding:5px; margin:5px;' id='".$div_key."'>". join("<br>",$sku_images['images']['ok']) . "</div>";

                        }
                    }
                    echo $lines ?  join(", ", $lines) : 'n/a';
                    echo "</div>";
                    echo "<div>";
                    echo "<u>".$helper->__("Not found images for SKUs:")."</u> ";
                    $lines = array();
                    foreach($helper->get_registry_key('images') as $sku_images){
                        if(!empty($sku_images['images']['nok'])){
                            $div_key = md5($sku_images['sku'].'nokimage'.join("<br>",$sku_images['images']['nok']));
                            $lines []= "<b>".$sku_images['sku'] . "</b> ". $helper->__('<a onclick="%s" href="javascript:void(0);">[show/hide]</a>','if($(\''.$div_key.'\').visible()){$(\''.$div_key.'\').hide();}else{$(\''.$div_key.'\').show();}' ) ."<div style='display:none; border:1px solid #777; padding:5px; margin:5px;' id='".$div_key."'>". join("<br>",$sku_images['images']['nok']) . "</div>";

                        }
                    }
                    echo $lines ? join(", ", $lines) : 'n/a';
                    echo "</div>";

                }
                $out = ob_get_contents();        
                ob_end_clean();

            }

            echo Zend_Json::encode(array( 
                    'out' => $out, 
                    'dialog' => $dialog, 
                    'stop'=> (bool)$helper->get_registry_key('stop'), 
                    'pause'=> (bool)$helper->get_registry_key('pause'),   
                    'session' => Mage::helper('importexportplugin/gdata')->getSession(),
                    'reset_ids' => $ids
                ));
            die();
        }catch(Exception $e){           
            echo Zend_Json::encode(array(
                    'dialog' => '<div class="ml-dialog-heading">'.$helper->__('Error')."</div>". $e->getMessage(),
                    'stop'  => 1,
                    'session' => Mage::helper('importexportplugin/gdata')->getSession()
                ));
        }


    }

    private function _validate(){
        $helper = Mage::helper('importexportplugin');
        $helper->checkMagmiDbAccess();
        $helper->checkLicense();

        $out = '';
        if(!$this->getRequest()->getParam('session', false)){
            //validation and dialog if needed
			
            $collection = Mage::helper('importexportplugin/gdata')->setSessionWorksheetCollection();
			
            if($collection->count() > 1 ){
                $dialog =   '<div class="ml-dialog-heading">'.
                                $helper->__('Validation complete.').
                             '</div><div>'.
                                $helper->__('Your spreadsheet contains multiple worksheets. After validation the following sheets can be processed:').
                                '</div>';
                $dialog .= '<ul id="ml-worksheets-list">';
                $has_valid = false;
                foreach($collection->getItems()  as $worksheet ){
					if (strpos($worksheet->getTitle(), $this->export_sheet_name) !== FALSE && $this->getRequest()->getActionName() == 'exportAjax')
						continue;
						
                    //var_dump($worksheet->getCsv());
                    try{
                        $worksheet->getRows();
                        $dialog .= '<li class="enabled"><input type="checkbox" name="worksheets" id="ws'.$worksheet->getId().'" value="'.$worksheet->getId().'" checked="checked">'.
                                       '<label for="ws'.$worksheet->getId().'">'. $helper->__("%s (%s rows)", $worksheet->getTitle(), count($worksheet->getRows())).'</label>'.
                                    '</li>';
                        $has_valid = true;
 
                    }catch(Exception $e){
                        $dialog .= '<li class="disabled"><input type="checkbox" name="worksheets" value="'.$worksheet->getId().'" disabled="disabled">'.
                                        $helper->__("%s", $worksheet->getTitle()). ' - ERROR: ' . $e->getMessage() . 
                                    '</li>';
                    }                   
                }
                $dialog .= '</ul>';

                if($has_valid){

                    $dialog .= '<div class="ml-dialog ml-button" onclick="mlAssignWs();">'.$helper->__('Continue').'</div>';
                    $helper->set_registry_key('pause', 1) ;
                }else{
                    $helper->set_registry_key('stop', 1) ;
                }

            }else{
                try{
                    $worksheet = $collection->getFirstItem();
                    $worksheet->getRows();
                    $dialog = '<div class="ml-dialog-heading">'. $helper->__('Validation complete').'</div>'. 
                                $helper->__('The worksheet %s (%d rows) is valid and ready to be processed. Avoid any external modifications of the worksheet until it ends', $worksheet->getTitle(), count($worksheet->getRows()));
                    $dialog .= '<input style="display:none" type="checkbox" name="worksheets" value="'.$worksheet->getId().'" checked="checked">';

                    $dialog .= '<div class="ml-dialog ml-button" onclick="mlAssignWs();">'.$helper->__('Continue').'</div>';

                }catch(Exception $e){
                    $dialog = '<div class="ml-dialog-heading">'.$helper->__('Validation complete') ."</div>".$e->getMessage();
                    $helper->set_registry_key('stop', 1) ;
                }
            }

            $out .= $helper->__("Worksheets validation completed. Select the worksheets for export.");
            return array( 
                'out' => $out, 
                'dialog' => $dialog, 
                'stop'=> (bool)$helper->get_registry_key('stop'), 
                'pause'=> (bool)$helper->get_registry_key('pause'),   
                'session' => Mage::helper('importexportplugin/gdata')->getSession()
            );
        }
        return true;
    }

    public function exportAjaxAction(){
        $helper = Mage::helper('importexportplugin');
        $dialog = '';
        $out = '';
        $ids = '';
        $error_flag =false;
        try{
            $validation_result = $this->_validate();
            if( isset($validation_result) && $validation_result !== true ){
				Mage::getSingleton('adminhtml/session')->setData('just_validated',true);
                echo Zend_Json::encode($validation_result);
                die();
            }else{
				
                $product_ids = $this->getRequest()->getParam('product');
                if(!is_array($product_ids)){
                    $product_ids = explode(",", $product_ids);
                }
                ob_start();

                try{
					if(Mage::getSingleton('adminhtml/session')->getData('just_validated')){
						Mage::getSingleton('adminhtml/session')->setData('just_validated',false);
						$collection = Mage::helper('importexportplugin/gdata')->setSessionWorksheetCollection();
						
					}else{
						$collection = Mage::helper('importexportplugin/gdata')->getSessionWorksheetCollection();
						
					}
					
					if($collection->count()>1){
                        $ws = explode(',', $this->getRequest()->getParam('ws'));                    
                    }else{                                        
                        $ws = array($collection->getFirstItem()->getId());
                    }
                    
                    foreach($collection as $worksheet){
                        if(in_array($worksheet->getId(),  $ws)){
                            $worksheet->getRows();
                            $helper->set_registry_key('inserted', array());
                            $helper->set_registry_key('not_changed', array());
                            $helper->set_registry_key('updated', array());

                            foreach($product_ids as $product_id){
                                $product = Mage::getModel('catalog/product')->load($product_id);
                                $worksheet->sendProduct($product);
                            }
							
							/**********************/// writing a worksheet to CSV and sending an update request
							if(Mage::getStoreConfig('importexportplugin/advanced/export_type')=='type_fast'){
								
								$toWrite = $worksheet->getRows();
								$filename = $worksheet->_generateCacheFilename();
								
								if( !is_dir(dirname($filename)) && !mkdir(dirname($filename), 0777, true) || !touch($filename)){
									throw new Exception( Mage::helper('importexportplugin')->__("Can't create file %s, check your permissions to the folder", $filename) );
								}
								
								$fp = fopen($filename, "w");
								
								$headers=array_flip($worksheet->_getMapping());
								fputcsv($fp, $headers);
								foreach ($toWrite as $row) {
									fputcsv($fp, $row);
								}

								fclose($fp);
								
								
								if(count($product_ids) < intval(Mage::getStoreConfig('importexportplugin/performance/export_per_portion'))){
									
									$service = new Zend_Gdata_Spreadsheets(  Mage::helper('importexportplugin/gdata')->getHttpClient() );
									
									// Get worksheet feed
									$query = new Zend_Gdata_Spreadsheets_DocumentQuery();
									$query->setSpreadsheetKey($worksheet->getKey());
									$feed = $service->getWorksheetFeed($query);
									
									$newWorksheetTitle = $worksheet->getTitle().$this->export_sheet_name;
									
									//remove worksheet copy if exists
									foreach($feed as $sheet){
										if($sheet->getTitle()==$newWorksheetTitle){
											$service->delete($sheet);
										}
									}

									// Create new worksheet structure
									$newWorksheet = new Zend_Gdata_Spreadsheets_WorksheetEntry();
									$newWorksheet->setTitle(new Zend_Gdata_App_Extension_Title($newWorksheetTitle, null));
									$newWorksheet->setRowCount(new Zend_Gdata_Spreadsheets_Extension_RowCount(count($toWrite)+3));
									$newWorksheet->setColumnCount(new Zend_Gdata_Spreadsheets_Extension_ColCount(count($headers)+2));

									// Add new worksheet to worksheets feed
									$newEntry = $service->insertEntry($newWorksheet->saveXML(), $feed->getLink('self')->getHref());
									$newId = basename($newEntry->getId());
									
									$service->updateCell(1,1,'=IMPORTDATA("'.$worksheet->_getCsvUrl().'")',$worksheet->getKey(),$newId);
									
								}
							}
							/**********************/
							
                            if(count($ws) > 1){
                                echo "<br>".str_pad( $worksheet->getTitle().": ", 30, '-')."<br/>";

                            }
                            if($helper->get_registry_key('errors')){
                                echo "<div>";
                                echo $helper->__('<b>Errors</b>:<br>%s', join("<br>",$helper->get_registry_key('errors')))."<br>";
                                echo "</div>";
                           }

                            foreach($helper->get_registry_key('updated') as $_row){
                                $sku = $_row['sku'];
                                foreach($_row['fields']as $__row){
                                    $csv_header = $__row['csv_header'];
                                    $prepared1 = $__row['prepared1'];
                                    $prepared2 = $__row['prepared2'];
                                    $div_key = md5($sku.$csv_header);
                                    echo $helper->__('Sku [%s] updated column <a onclick="%s" href="javascript:void(0);">%s</a>', $sku,  'if($(\''.$div_key.'\').visible()){$(\''.$div_key.'\').hide();}else{$(\''.$div_key.'\').show();}', $csv_header)."\n";
                                    echo "<div style='display:none; border:1px solid #777; padding:5px; margin:5px;' id='".$div_key."'>";
                                    echo "<div class='old'>";
                                    echo $helper->__('old value:')."<br/>";
                                    echo $prepared1;    
                                    echo "</div>";

                                    echo "<div class='new'>";
                                    echo $helper->__('new value:')."<br/>";
                                    echo empty($prepared2) ? " " : $prepared2;
                                    echo "</div>";

                                    echo "</div><br/>";
                                }

                            }

                            if($helper->get_registry_key('inserted')){
                                echo $helper->__('Inserted SKUs: %s', join(", ",$helper->get_registry_key('inserted')))."<br>";                            
                            }
                            if($helper->get_registry_key('not_changed')){
                                echo $helper->__('Not changed SKUs: %s',join(", ",$helper->get_registry_key('not_changed')) )."<br>";
                            }


                        }
                    }

                }catch(Exception $e){
                    echo $e->getMessage();
                }
                $out = ob_get_contents();        
                ob_end_clean();
            }
            echo Zend_Json::encode(array( 
                    'out' => $out, 
                    'dialog' => $dialog, 
                    'stop'=> (bool)$helper->get_registry_key('stop'), 
                    'pause'=> (bool)$helper->get_registry_key('pause'),   
                    'session' => Mage::helper('importexportplugin/gdata')->getSession()
                ));
            die();
        }catch(Exception $e){
            echo Zend_Json::encode(array(
                    'dialog' => '<div class="ml-dialog-heading">'.$helper->__('Error')."</div>". $e->getMessage(),
                    'stop'  => 1,
                    'session' => Mage::helper('importexportplugin/gdata')->getSession()
                ));
        }


    }

}    

