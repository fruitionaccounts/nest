<?php
class Infusion_Subscription extends Infusion_Data {
 
    public function __construct($controller, $subscription) {
        $this->_controller = $controller;
        $this->_table = 'CProgram';
        
        $this->_reset_fields();
        
        if (is_numeric($subscription))
            $this->load($subscription);
        
        if (is_array($subscription)) {
            foreach ($subscription as $col => $val)
                $this->$col = $val;
        }
    }
    
    /**
     * Loads a subscription object
     *
     * @param int $subscription_id 
     * @return void
     * 
     */
    public function load($subscription_id) {
        $params = array($this->_controller->api_key,
                        $this->_table,
                        $subscription_id,
                        $this->_controller->fields[$this->_table]);
                        
        if ($subscription = $this->_controller->send('DataService.load', $params)) {
					foreach ($subscription as $col => $val)
        		$this->$col = $val;        	
        }
    }
    
    /**
     * Returns array of all product objects
     *
     * @return array
     * 
     */
    public function getAll() {
        $params = array($this->_controller->api_key,
                        $this->_table,
                        1000,
                        0,
                        array('Id'=>'%'),
                        $this->_controller->fields[$this->_table]);
            
        $subscriptions = $this->_controller->send('DataService.query', $params);
        
        if ($this->returnObjects && $this->return_objects) {
	        $return_subscriptions = array();
	        
	        foreach ($subscriptions as $subscription)
	            $return_subscriptions[] = $this->_controller->Subscription($subscription);
	        
	        return $return_subscriptions;
        }
	      else {
	      	return $subscriptions;
	      }
    }
}