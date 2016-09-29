<?php

class MageLynx_ImportExportPlugin_Helper_Gdata extends Mage_Core_Helper_Abstract{    
    var $_cache = array();
    	
    public function getSession(){        
        $session =  Mage::app()->getRequest()->getParam('session', false);
        
        if(!$session){
            $session = date('Y-m-d---h-i-s') ;
        }
        return $session;
    }

    function formatBytes($bytes, $precision = 2) { 
        $units = array('B', 'KB', 'MB', 'GB', 'TB'); 
        $bytes = max($bytes, 0); 
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
        $pow = min($pow, count($units) - 1); 
        $bytes /= (1 << (10 * $pow)); 
        return round($bytes, $precision) . ' ' . $units[$pow]; 
    }
 
    public function getKey(){
        if(isset($this->_cache['key'])){
            return $this->_cache['key'];
        }
        $url =  Mage::getStoreConfig('importexportplugin/settings/url');
        preg_match_all('/key=(([^&#])+)/', $url, $matches);
        if(!isset($matches[1]) || empty($matches[1][0]) ){

			$aux=explode('d/',$url);
			$aux=$aux[1];
			
			if(strstr($aux,'/')!==false)
			{
				$aux=explode('/',$aux);
				$aux=$aux[0];
			}

			if(!empty($aux))
			{
				//$this->_type='new';
				$this->_cache['key'] = $aux;                       
			}
			else
				throw new Exception($this->__('URL to Google Spreadsheet is incorrect'));          

        }else{
			//$this->_type='old';
            $this->_cache['key'] = $matches[1][0];                       
        }
		
		return $this->_cache['key'];
    }

	public function setSessionWorksheetCollection(){
		
		$worksheets=$this->getWorksheetCollection();
		
		$serial=serialize($worksheets);
		Mage::getSingleton('adminhtml/session')->setData('serialized_sheets',$serial);
		
		return $worksheets;
    }
	
	public function getSessionWorksheetCollection(){
		
		if(isset($this->_cache['worksheets'])){
		    ///var_dump($this->_cache['worksheets']);
			
			return $this->_cache['worksheets'];
			
        }
		
		$fromSession = Mage::getSingleton('adminhtml/session')->getData('serialized_sheets');
		$worksheets1=unserialize($fromSession);
		$this->_cache['worksheets']=$worksheets1;
		
		return $this->_cache['worksheets'];
    }
	
	
    public function getWorksheetCollection(){
		
		if(isset($this->_cache['worksheets'])){
		                    //echo 'returning from cache';

            return $this->_cache['worksheets'];
        }
		//echo 'returning not from cache';
		$key=$this->getKey();
		
		$service = new Zend_Gdata_Spreadsheets($this->getHttpClient()); 
        $query = new Zend_Gdata_Spreadsheets_DocumentQuery();
        $query->setSpreadsheetKey($key);

		$feed = $service->getWorksheetFeed($query);
		
        $this->_cache['worksheets'] = new Varien_Data_Collection();
        foreach($feed as $worksheet_entry){
			
            $worksheet = Mage::getModel('importexportplugin/worksheet', array(
                    'key' => $key,
                    'id' => basename($worksheet_entry->id), 
                    'title' => $worksheet_entry->title->getText()
                ));
            $this->_cache['worksheets']->addItem( $worksheet);
        }
		
        return $this->_cache['worksheets'];
    }

    public function getHttpClient(){
        if(isset($this->_cache['httpClient'])){
            return $this->_cache['httpClient'];
        }

        $login = Mage::getStoreConfig('importexportplugin/settings/google_login');
        $password =Mage::helper('core')->decrypt( Mage::getStoreConfig('importexportplugin/settings/google_password'));        
        $url =  Mage::getStoreConfig('importexportplugin/settings/url');


        if(empty($login)){
            throw new Exception($this->__('Google login not set'));
        }elseif(empty($password)){
            throw new Exception($this->__('Google password not set'));
        }elseif(empty($url)){
            throw new Exception( $this->__('Spreadsheet URL not set. Fill up corresponding setting.'));
        }else{
            try{
                $httpClient = Zend_Gdata_ClientLogin::getHttpClient($login, $password, 'wise');
                $this->_cache['httpClient'] = $httpClient;
                return $this->_cache['httpClient'];
            }catch(Exception $e){
                if($e->getMessage() == 'Authentication with Google failed. Reason: BadAuthentication' || $e->getMessage() =='CAPTCHA challenge issued by server'){
                    throw new Exception( Mage::helper('importexportplugin')->__('Authenitication failed. If you`re sure you`ve entered coorect login and password, you need to permit your Magento to connect to your Google account. Follow the instructions: <br/> 1. Click <a href="%s" target="_blank">here</a> to log in to you Google account <br/>2. Click <a href="%s" target="_blank">here</a> to allow your Magento access your Google account <br/> 3. Retry previous syncing action',"https://accounts.google.com/ServiceLogin","https://accounts.google.com/DisplayUnlockCaptcha" ));
                }else{
                    throw $e;
                }
            }
        }
    }
}
