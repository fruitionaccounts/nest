<?php

class Infusion_Order extends InfusionBaseChild {
    
    public function __construct($controller, $order) {
        $this->_controller = $controller;
        $this->_table = 'Job';
        
        $this->_reset_fields();
        
        if (is_numeric($order))
            $this->load($order);
        
        if (is_array($order)) {
          foreach ($order as $col => $val)
	          $this->$col = $val;
        }
    }
    
    /**
     * Loads an order object
     *
     * @param int $orderId 
     * @return void
     * 
     */
    public function load($orderId) {
        $params = array($this->_controller->api_key,
                        $this->_table,
                        $orderId,
                        $this->_controller->fields[$this->_table]);
                        
        foreach ($this->_controller->send('DataService.load', $params) as $col => $val)
            $this->$col = $val;
    }
    
	/**
		* Loads an order object based on an invoice id
    *
    * @param int $invoiceId 
    * @return void
    * 
    */
	public function loadFromInvoiceId($invoiceId) {
		$params = array($this->_controller->api_key,
										$this->_table,
										1,
										0,
										'InvoiceId',
										(int) $invoiceId,
										$this->_controller->fields[$this->_table]);
                        
		$order = $this->_controller->send('DataService.findByField', $params);                    

		foreach ($order[0] as $col => $val)
			$this->$col = $val;
	}
}