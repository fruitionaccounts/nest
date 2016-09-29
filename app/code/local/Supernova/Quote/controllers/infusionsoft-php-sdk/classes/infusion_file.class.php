<?php
class Infusion_File {
	public $contactId;
	public $fileName;
	public $fileData;
	public $fileId;
	public $public;
	
	public function __construct($controller)
	{
		$this->_controller = $controller;
		$this->contactId = null;
		$this->fileName = null;
		$this->fileData = null;
		$this->fileId = null;
		$this->public = 0;
	}
		
	
	//  returns the encoded file data	
  public function getFile()
  {
		$params = array($this->_controller->api_key, $this->$fileId);
		$result = $this->_controller->send("FileService.getFile", $params);
		return $result;
	}    
    
	public function uploadFile() {
    $result = 0;
    if(!$this->contactId) {
      $params = array($this->_controller->api_key,
											$this->fileName, 
											$this->fileData);
      $result = $this->_controller->send("FileService.uploadFile",$params);
    }
    else {
      $params = array($this->_controller->api_key,
      								$this->contactId, 
											$this->fileName, 
											$this->fileData);
      $result = $this->_controller->send("FileService.uploadFile",$params);
    }
    
    $this->fileId = $result;
    return $result;
	}
	    
	public function replaceFile() {
  	$params = array($this->_controller->api_key,
                    $this->fileId,
                    $this->fileData);
    $result = $this->_controller->send("FileService.replaceFile",$params);
    return $result;
	}

//boolean renameFile(String key, int fileId, String fileName) - returns true if successful
	public function renameFile($fileName) {
    $params = array($this->_controller->api_key,
                    $this->fileId,
                    $fileName);
    $result = $this->_controller->send("FileService.renameFile",$params);
    return $result;
	}

	//String getDownloadUrl(String key, int fileId)
	public function getDownloadUrl($fileId = null) {
		if (! isset($fileId))
			$fileId = $this->fileId;
			
		$params = array($this->_controller->api_key,
										$fileId);
		$result = $this->_controller->send("FileService.getDownloadUrl",$params);
		return $result;
	} 
}