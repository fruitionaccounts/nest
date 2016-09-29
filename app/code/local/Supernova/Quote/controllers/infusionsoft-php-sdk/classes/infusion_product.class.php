<?php

class Infusion_Product extends Infusion_Data {
    
    public function __construct($controller, $product) {
        $this->_controller = $controller;
        $this->_table = 'Product';
        
        $this->_reset_fields();
        
        if (is_numeric($product)) {
					$this->load($product);
        }
        
        if (is_array($product)) {
            foreach ($product as $col => $val) {
							$this->$col = $val;
            }
        }
    }
    
    /**
     * Loads a product object
     *
     * @param int $product_id 
     * @return void
     * 
     */
    public function load($product_id) {
        $params = array($this->_controller->api_key,
                        $this->_table,
                        $product_id,
                        $this->_controller->fields[$this->_table]);

        if ($product = $this->_controller->send('DataService.load', $params)) {
					foreach ($product as $col => $val) {
        		$this->$col = $val;
					}
        }
    }
    
    /**
     * Returns array of all product objects
     *
     * @return array
     * 
     */
    public function getAll() {
    	$returnProducts = array();
    	$page = 0;
    	
    	while(1) {
    		$temp = array();
    		
        $params = array($this->_controller->api_key,
                        $this->_table,
                        1000,
                        $page,
                        array('Id'=>'%'),
                        $this->_controller->fields[$this->_table]);
            
        $products = $this->_controller->send('DataService.query', $params);
        
        foreach ($products as $product) {
					$temp[] = $this->_controller->Product($product);
        }
        
        $returnProducts = array_merge($returnProducts, $temp);
        if(sizeof($temp) < 1000) break;
        $page++;
    	}
    	
    	return $returnProducts;
    }
}