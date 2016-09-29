<?php
class Infusion_Search {
	public $userId;
	
	public function __construct($controller, $userId = 0)
	{
		$this->_controller = $controller;
		$this->userId = $userId;
	}    
    
	public function getSavedSearchResultsAllFields($savedSearchId, $pageNumber = 0) {
    if(! $this->userId)
    	throw new InfusionException('User Id is required to run a saved search!');

    //echo $savedSearchId."::".$this->userId."::".$pageNumber;
			
		$params = array($this->_controller->api_key,
										(int) $savedSearchId,		
										(int) $this->userId, 
										(int) $pageNumber);

		$result = $this->_controller->send("SearchService.getSavedSearchResultsAllFields", $params);
    
    return $result;
	}
	public function getDefaultQuickSearch() {
    if(! $this->userId)
    	throw new InfusionException('User Id is required to run a saved search!');
			
		$params = array($this->_controller->api_key,		
										(int) $this->userId);

		$result = $this->_controller->send("SearchService.getDefaultQuickSearch", $params);
    
    return $result;
	}
	//Function For Quick search(Search.quickservice)
	public function quickSearch($type, $searchData, $page=0, $limit=1000) {
    if(! $this->userId)
    	throw new InfusionException('User Id is required to run a saved search!');
			
		$params = array($this->_controller->api_key,
										$type,		
										(int) $this->userId,
										$searchData,
										(int) $page,
										(int) $limit);
		//print_r( $params );

		$result = $this->_controller->send("SearchService.quickSearch", $params);
    
    return $result;
	}
}