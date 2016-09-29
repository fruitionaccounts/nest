<?php

class MageLynx_ImportExportPlugin_Model_Worksheet extends Varien_Object{
    var $_rows = array();  
    var $_loaded_skus = array(); 
    var $_headings = array();
    var $_edit_link = false;
    var $_prepared_products_data = array();
	
    private function _validate(){
        if(!strlen($this->getId())){
            throw new Exception( Mage::helper('importexportplugin')->__('Worksheet ID not specified'));
        }
        if(!$this->getKey()){
            throw new Exception( Mage::helper('importexportplugin')->__('Spreadsheet key not specified'));
        }
    }

    public function _generateCacheFilename(){
        //$this->_validate();
        return Mage::getBaseDir().DS.join(DS, array(
                                'magelynx_importexportplugin',
                                'worksheet_cache',
                                Mage::helper('importexportplugin/gdata')->getSession(),
                                $this->getKey().'-'.$this->getGid().'.csv'
                            ));
    }
	
	
	public function _getCsvUrl(){
        //$this->_validate();
        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).join('/', array(
								
                                'magelynx_importexportplugin',
                                'worksheet_cache',
                                Mage::helper('importexportplugin/gdata')->getSession(),
                                $this->getKey().'-'.$this->getGid().'.csv'
                            ));
    }

    public function _getDownloadedCacheFilename(){
        $this->_validate();
        $params = array(
                '{key}' => $this->getKey(),
                '{id}' => $this->getGid()
           );
	    $config_url=Mage::getStoreConfig('importexportplugin/settings/url');
		$filename = $this->_generateCacheFilename();
		
	
        if(!is_file($filename)){ 
            if( !is_dir(dirname($filename)) && !mkdir(dirname($filename), 0777, true) || !touch($filename)){
                throw new Exception( Mage::helper('importexportplugin')->__("Can't create file %s, check your permissions to the folder", $filename) );
            }
			if(strpos($config_url, 'key') !== FALSE)
			{	
				$params = array(
					'{key}' => $this->getKey(),
					'{gid}' => $this->getGid()
				);
				$url = str_replace( array_keys($params), array_values($params), 'https://spreadsheets.google.com/ccc?key={key}&gid={gid}&output=csv&ndplr=1');
				$body = Mage::helper('importexportplugin/gdata')->getHttpClient()->setUri($url)->request('POST')->getBody();

			}
			else
			{
				$params = array(
					'{key}' => $this->getKey(),
					'{id}' => $this->getId()
				  );
				$url = str_replace( array_keys($params), array_values($params), 'https://spreadsheets.google.com/feeds/worksheets/{key}/private/full/{id}');
				$body = Mage::helper('importexportplugin/gdata')->getHttpClient()->setUri($url)->request('GET')->getBody();
							
				$p = xml_parser_create();
				xml_parse_into_struct($p, $body, $vals, $index);
				xml_parser_free($p);
				
				
				//print_r($vals);die;
				
				foreach($index['LINK'] as $val){
					
					
					if(strstr($vals[$val]['attributes']['HREF'],'csv')){
						$url=$vals[$val]['attributes']['HREF'];
					
					}
					
				}
				
				//print_r($_SESSION['parsed_already']);die;
				
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_SSLVERSION,3);
				$body = curl_exec ($ch);
				$error = curl_error($ch); 
				curl_close ($ch);
			}
			
			
			/*$service = new Zend_Gdata_Spreadsheets(Mage::helper('importexportplugin/gdata')->getHttpClient());
            $query = new Zend_Gdata_Spreadsheets_ListQuery();
            $query->setSpreadsheetKey($this->getKey())
                        ->setWorksheetId($this->getId());
            $array=$service->getSpreadsheetListFeedContents($query);
			
			
			$query = new Zend_Gdata_Spreadsheets_CellQuery();
			$query->setSpreadsheetKey($this->getKey())
					->setWorksheetId($this->getId());
			$query->setMinRow(1);
			$query->setMaxRow(1);
			$headerFeed = $service->getCellFeed($query);
			$headers=array();
			foreach($headerFeed as $cellEntry){
				$headers[]=$cellEntry->cell->getText();
			}
			
			$csv=array();
			array_unshift($array,$headers);
			foreach($array as $row){
				$replaced=array();
				foreach($row as $cell){
					$replaced[]=str_replace('"','""',$cell);
				}
				$csv[]='"'.implode('","',$replaced).'"';
			}
				
			$body=implode(PHP_EOL,$csv);*/
			
            if(!$body){
                throw new Exception( Mage::helper('importexportplugin')->__("Can't fetch data or empty sheet", $filename) );
            }

            if(!file_put_contents($filename, $body)){                
                throw new Exception( Mage::helper('importexportplugin')->__("Can't save to file %s, check your permissions to the file", $filename) );
            }

        }
		
		/*$data = file_get_contents($filename);
		
		$login = Mage::getStoreConfig('importexportplugin/settings/google_login');
        $password = Mage::helper('core')->decrypt( Mage::getStoreConfig('importexportplugin/settings/google_password'));    
		
		
		
		$response = Mage::helper('importexportplugin/gdata')->getHttpClient()
			->setUri('https://www.googleapis.com/upload/drive/v2/files?convert=true&uploadType=media')
			->setFileUpload('testFileUpload123','formname',$data,'text/csv')
			->setAuth($login,$password)
			->request('POST');
		
		print_r($response);
		
		$service = new Google_Service_Drive();
		$title='testtest';
		$title='description';
		$parentId='';
		$mimeType='text/csv';
		$filename='test.csv';
		
		$this->insertFile($service, $title, $description, $parentId, $mimeType, $filename);
		
		die;*/
		
        return $filename;
    }

    public function getCsv(){              
        return file_get_contents($this->_getDownloadedCacheFilename());
    } 
	
	public function insertFile($service, $title, $description, $parentId, $mimeType, $filename) {
	  $file = new Google_DriveFile();
	  $file->setTitle($title);
	  $file->setDescription($description);
	  $file->setMimeType($mimeType);

	  // Set the parent folder.
	  if ($parentId != null) {
		$parent = new ParentReference();
		$parent->setId($parentId);
		$file->setParents(array($parent));
	  }

	  try {
		$data = file_get_contents($filename);

		$createdFile = $service->files->insert($file, array(
		  'data' => $data,
		  'mimeType' => $mimeType,
		  'convert' => true,
		));

		// Uncomment the following line to print the File ID
		// print 'File ID: %s' % $createdFile->getId();

		return $createdFile;
	  } catch (Exception $e) {
		print "An error occurred: " . $e->getMessage();
	  }
	}    private function _sanitizeCsvHeader($arg){
        return strtolower(str_replace(' ','',$arg));
    }
    private function _sanitizeCsvValue(&$arg, $key){
        if(!strlen(trim($arg)) && strlen($arg) ){
            $arg = " ";//set it to space ONLY if this must be exactly space in the spreadsheet
        }else{
            $arg = trim($arg);
        }
        //$arg.=$key;
    }

    private function _is_gt_1($val){
        return $val > 1;
    }

    private function _validateHeaderRow($header_row){
        if(!in_array('sku', $header_row)){
            //var_dump($header_row); die();
            throw new Exception( Mage::helper('importexportplugin')->__("Error: Can't find header 'sku' in worksheet %s", $this->getTitle() ));
        }
        $duplicate_headers = array_filter( array_count_values($header_row), array($this, '_is_gt_1') );
        if(!empty($duplicate_headers)){
            throw new Exception( Mage::helper('importexportplugin')->__('Error: Following columns duplicated in worksheet "%s": %s', $this->getTitle(),
                                                                            join(", ", array_keys($duplicate_headers)) ));
        }

    }

    private function _validateRow($row, $row_id){
        $helper = Mage::helper('importexportplugin');

        if(trim(join("",$row)) != '' && empty($row['sku'])){
            throw new Exception( $helper->__("Row #%s missing 'sku' value in worksheet %s", $row_id, $this->getTitle()) );
        }
        if(strlen(trim($row['sku'])) && isset($this->_loaded_skus[$row['sku']])){
				
		
             throw new Exception( $helper->__("Rows #%s and #%s are duplicated: they define the same sku. Remove one of the rows.", $row_id, $this->_loaded_skus[$row['sku']], $this->getTitle()) );
        }

    }


    public function getRows(){
        if(empty($this->_rows)){
            $handle = fopen($this->_getDownloadedCacheFilename(), "r");
			
			$row_id = 0;
            while($row = fgetcsv($handle)){
                $row_id++;
                if(!isset($header_row)){
                    $header_row = array_map(array($this, '_sanitizeCsvHeader'),$row);
                    $this->_validateHeaderRow($header_row);
                    $this->setCsvHeaderRow($header_row);
                    continue;

                }else{
                    $row = array_combine( $header_row, $row );
                    array_walk($row, array($this, '_sanitizeCsvValue'));
                    $this->_validateRow($row, $row_id);
                    $this->_loaded_skus[$row['sku']] = $row_id;
                    $this->_rows[ $row_id] = $row;
                }
            }
            //post-cleanize header row
            $header_row = array();
            foreach($this->getCsvHeaderRow() as $field){
                if(!strlen(trim($field))){
                    foreach($this->_rows as $k=> $row){
                        unset($this->_rows[$k][$field]);
                    }
                }else{
                    $header_row[]=$field;
                }
            };
            $this->setCsvHeaderRow($header_row);


        }
		
        return $this->_rows;
    }
    public function getGid(){
        $this->_validate();
        return intval($this->getId(),36)^31578;
    }

    public function _getMapping(){
        $rows = $this->getRows();
        
        if(is_array($this->getCsvHeaderRow()) && is_array($this->_getHeadingsRow())
            && count($this->getCsvHeaderRow()) == count($this->_getHeadingsRow())  ){
            return array_combine($this->getCsvHeaderRow(), $this->_getHeadingsRow());
        }else{
            throw new Exception( Mage::helper('importexportplugin')->__("Internal Error #".__LINE__));
        }

    }

    private function _prepareProductToSend($product){        
        if(empty($this->_prepared_products_data[$product->getSku()] )){
            $this->_prepared_products_data[$product->getSku()] = array();
            foreach($this->_getMapping() as $csv_header => $ss_header){
                $this->_prepared_products_data[$product->getSku()][$ss_header] = Mage::helper('importexportplugin')->getProductField($product, $csv_header);
            }

        }
        return $this->_prepared_products_data[$product->getSku()];
       
    }
    
    private function _hasProduct($product){
        $rows = $this->getRows();      
        return isset($this->_loaded_skus[$product->getSku()]) ? true : false;
    }

    private function _spreadsheetNeedsUpdate($product){
        if($this->_hasProduct($product)){
            $helper = Mage::helper('importexportplugin');
            $rows = $this->getRows();      
            $csv_product_data = $rows[$this->_loaded_skus[$product->getSku()]];
            $db_product_data = $this->_prepareProductToSend($product);
			
			$sku = $product->getSku();
            $updated_fields = array();
            foreach($this->_getMapping() as $csv_header => $ss_header){                
                $prepared1 = trim(str_replace( array("\r\n", "\r", "\n"), ' ', $csv_product_data[$csv_header]));
                $prepared2 = trim(str_replace( array("\r\n", "\r", "\n"), ' ', $db_product_data[$ss_header]));  
                if($prepared1 != $prepared2){
                    $updated_fields[]= array('csv_header' => $csv_header, 'prepared1'=>$prepared1, 'prepared2'=>$prepared2);
					
					if(Mage::getStoreConfig('importexportplugin/advanced/export_type')=='type_fast'){
						$this->updateRowsCell($this->_loaded_skus[$product->getSku()],$csv_header,$db_product_data[$ss_header]);
					}
                }
            }
            if(count($updated_fields)>0){
                $helper->push_registry_key('updated', array('sku'=> $sku, 'fields' => $updated_fields));
            }else{
                $helper->push_registry_key('not_changed', $sku );
            }
            return count($updated_fields)>0;

        }
        
        return true;
    }
	
	private function updateRowsCell($row,$col_header,$value){
		
		$this->_rows[$row][$col_header]=$value;
		
		return true;
	}
	
	private function addRow($row_array){ // array('sku'=>...) 
		
		$this->_rows[]=$row_array;
		
		return true;
	}

    private function _getEditLink(){
        if(!$this->_edit_link){            
            $service = new Zend_Gdata_Spreadsheets(  Mage::helper('importexportplugin/gdata')->getHttpClient());
            $query = new Zend_Gdata_Spreadsheets_ListQuery();
            $query->setSpreadsheetKey($this->getKey())
                        ->setWorksheetId($this->getId());

            $feed = $service->getListFeed($query);
            $this->_edit_link = $feed->getLink('http://schemas.google.com/g/2005#post')->href;
            //var_dump($this->_edit_link);die();
        }
        return $this->_edit_link;
    }

    public function sendProduct($product){ 
        $rows = $this->getRows();       
        $data = $this->_prepareProductToSend($product);
 		
        if($this->_hasProduct($product) && $this->_spreadsheetNeedsUpdate($product)){
			
			if(Mage::getStoreConfig('importexportplugin/advanced/export_type')=='type_row'){
				$service = new Zend_Gdata_Spreadsheets(  Mage::helper('importexportplugin/gdata')->getHttpClient() );
				$query = new Zend_Gdata_Spreadsheets_ListQuery();
				$query->setSpreadsheetKey($this->getKey())
						->setWorksheetId($this->getId())
						->setSpreadsheetQuery('sku="'.$product->getSku().'"');
				$entry = $service->getListFeed($query);
				
				$service->updateRow($entry->entries->entry[0], $data);
			}
			//else			
			//the row is being updated in $this->_spreadsheetNeedsUpdate
			
        }elseif(!$this->_hasProduct($product)){

			if(Mage::getStoreConfig('importexportplugin/advanced/export_type')=='type_row'){
				$newEntry = new Zend_Gdata_Spreadsheets_ListEntry();
				$newCustomArr = array();
				foreach ($data as $k => $v) {
					$newCustom = new Zend_Gdata_Spreadsheets_Extension_Custom();
					$newCustom->setText($v)->setColumnName($k);
					$newEntry->addCustom($newCustom);
				}
				$service = new Zend_Gdata_Spreadsheets(  Mage::helper('importexportplugin/gdata')->getHttpClient() );
				$service->insertEntry($newEntry->saveXML(), $this->_getEditLink(), 'Zend_Gdata_Spreadsheets_ListEntry');
			}
			elseif(Mage::getStoreConfig('importexportplugin/advanced/export_type')=='type_fast'){
				$this->addRow($this->_prepared_products_data[$product->getSku()]);
			}
			
			Mage::helper('importexportplugin')->push_registry_key('inserted', $product->getSku());
        }
    }


    private function _getHeadingsRow(){
        $this->_validate();
        if(empty($this->_headings)){
            $service = new Zend_Gdata_Spreadsheets(  Mage::helper('importexportplugin/gdata')->getHttpClient() );
            $query = new Zend_Gdata_Spreadsheets_ListQuery();
            $query->setSpreadsheetKey($this->getKey());
            $query->setWorksheetId($this->getId());
            $query->setRowId(0);       
            try{ 
                $entry = $service->getListEntry($query);

                foreach($entry->getCustom() as $elm){
                    $this->_headings[]= $elm->getColumnName();
                }
            }catch(Exception $e){
                try{
                    $service->updateCell(2, 1," " ,$this->getKey(),$this->getId());
                    $entry = $service->getListEntry($query);
                    foreach($entry->getCustom() as $elm){
                        $this->_headings[]=$elm->getColumnName();
                    }
                    $service->updateCell(2, 1,"" ,$this->getKey(),$this->getId());
                }catch(Exception $e){
                    throw new Exception( Mage::helper('importexportplugin')->__("Internal Error #%s: %s",__LINE__,$e->getMessage() ));
                }
            }
        }
        return $this->_headings;
    }

}
