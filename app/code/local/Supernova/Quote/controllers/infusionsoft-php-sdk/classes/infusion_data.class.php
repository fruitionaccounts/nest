<?php

/**
 * Wrapper around DataService class of Infusionsoft API
 *
 * 
 */
class Infusion_Data extends InfusionBaseChild {
	// no longer using $fields locally; use $this->_controller->fields[$this->_table]
	//private $fields;
	public $returnObjects;
	public $return_objects; // backward compatibility
    
	public function __construct($controller, $table, $initial_data = FALSE) {
		$this->_controller = $controller;
		$this->_table = $table;
		$this->returnObjects = true;  // set to false to return arrays instead of objects
		$this->return_objects = true;
        
		if ($initial_data) {
			$this->_reset_fields($initial_data);
		}
			
		/* if ($this->_table != 'CreditCard') {
			$this->fields = $this->_controller->fields[$this->_table];
		}
		else {
			$this->fields = $this->_controller->fields['CreditCard-Readable'];
		} */
	}
    
    /*
     * Defined in base class
     *
    public function load($id)
    {	
        if (!isset($this->fields))
        {
            throw new InfusionException('Unknown table');
        }
        
        if (!is_numeric($id))
        {
            throw new InfusionException('Id value must be numeric');
        }
        
        
        $params = array($this->_controller->api_key,
                        $this->_table,
                        intval($id),
                        $this->fields);
                    
        $data = $this->_controller->send('DataService.load', $params);
        
        $this->_reset_fields($data);
				
        return $data;
    }
    */
    
    /**
     * Adds or updates a record for the current table
     *
     * @return int
     * 
     */
	/* defined in base class */
    /* public function save() {
        if ($this->Id) {
            $params = array($this->_controller->api_key,
                            $this->_table,
                            $this->Id,
                            $this->fields());
            return $this->_controller->send('DataService.update', $params);
        }
        
        if ($this->_controller->fields_are_blank($this->fields())) {
            throw new InfusionException(sprintf('Cannot save a blank %s record', $this->_table));
        }
        
        $params = array($this->_controller->api_key,
                        $this->_table,
                        $this->fields());
            
        $this->Id = $this->_controller->send('DataService.add', $params);
        
        return $this->Id;
    } */
    
    /**
     * Searches the table for a specified column and value. Returns an array 
     * of Data objects
     *
     * @param string $field 
     * @param string $value 
     * @param int $limit 
     * @param int $page 
     * @return array of data objects or array of arrays
     * 
     */
    public function find_by_field($field, $value, $limit = 1000, $page = 0) {		
        $params = array($this->_controller->api_key,
                        $this->_table,
                        $limit,
                        $page,
                        $field,
                        $value,
                        $this->_controller->fields[$this->_table]);
                        
        $records = $this->_controller->send('DataService.findByField', $params);
        
        if (!$records)
            return array();
        
        if ($this->returnObjects && $this->return_objects) {
	        $return_records = array();
	        
	        foreach ($records as $record)
	        {
	            $return_records[] = $this->_controller->Data($this->_table, $record);
	        }
	        
	        return $return_records;
	        }
        else
        	return $records;

    }
    
    public function authenticate_user($value='') {
        # todo
    }
    
    /**
     * Queries the table for the supplied key/value pairs in $query.
     * % is wildcard.
     * 
     * Give query options like so: $query = array('key'=>'value');
     *
     * Returns an array of data objects or array of arrays
     * 
     * @param array $query 
     * @param int $limit 
     * @param int $page 
     * @return array of objects or array of arrays
     * 
     */
    public function query($query, $page = 0, $limit = 1000) {
        $params = array($this->_controller->api_key,
                        $this->_table,
                        $limit,
                        $page,
                        $query,
                        $this->_controller->fields[$this->_table]);
        
        $records = $this->_controller->send('DataService.query', $params);
        
        if (!$records) { 
          return array();
    		}
    		
        if ($this->returnObjects && $this->return_objects) {
	        $return_records = array();
	        
	        foreach ($records as $record) {
	            $return_records[] = $this->_controller->Data($this->_table, $record);
	        }
	        
	        return $return_records;
				}
        else {
        	return $records;
        }
    }
    
    /**
    * Queries the table for the supplied key/value pairs in $query.
    * This call includes the ability to order by a column (ascending/descending)
    *
    * Give query options like so: $query = array('key'=>'value');
    *
    * Returns an array of data objects or array of arrays
    *
    * @param array $query
    * @param string $orderBy
    * @param bool $ascending
    * @param int $limit
    * @param int $page
    * @return array of objects or array of arrays
    *
    */
    public function queryWithOrderBy($query, $orderBy, $ascending = false, $page = 0, $limit = 1000) {
    	$params = array($this->_controller->api_key,
    									$this->_table,
    									$limit,
								    	$page,
								    	$query,
								    	$this->_controller->fields[$this->_table],
								    	$orderBy,
								    	$ascending);
    
    	$records = $this->_controller->send('DataService.query', $params);
    
    	if (!$records) {
    		return array();
    	}
    
    	if ($this->returnObjects && $this->return_objects) {
    		$return_records = array();
    		 
    		foreach ($records as $record){
    			$return_records[] = $this->_controller->Data($this->_table, $record);
    		}
    		 
    		return $return_records;
    	}
    	else {
    		return $records;
    	}
    }
    
    /**
     * Returns array of all records in table
     *
     * @return array
     * 
     */
    public function getAll()
    {
        $params = array($this->_controller->api_key,
                        $this->_table,
                        1000,
                        0,
                        array('Id'=>'%'),
                        $this->_controller->fields[$this->_table]);
            
        $data = $this->_controller->send('DataService.query', $params);
        return $data;
        /*
        $return_products = array();
        
        foreach ($products as $product)
        {
            $return_products[] = $this->_controller->Product($product);
        }
        
        return $return_products;
        */
    }
    
    /**
     * Deletes the loaded object
     *
     * @return void
     * 
     */
    public function delete($id) {
    	/*
        if (empty($this->Id) && $id == 0)
        {
            throw new InfusionException("Record Id is required for deletion!");
        }
        */
        $params = array($this->_controller->api_key,
                        $this->_table,
                        intval($id));
        
        $status = $this->_controller->send('DataService.delete', $params);
        $this->_reset_fields();
        return $status;
    }
    
    /*
     * 
     */
    public function getAppSetting($module, $setting) {
			$params = array($this->_controller->api_key,
											$module,
											$setting);
        
			return $this->_controller->send('DataService.getAppSetting', $params);
    }
}