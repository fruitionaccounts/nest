<?php

require_once(dirname(__FILE__) . '/classes/infusion_data.class.php');
require_once(dirname(__FILE__) . '/classes/infusion_apiemail.class.php');
require_once(dirname(__FILE__) . '/classes/infusion_invoice.class.php');
require_once(dirname(__FILE__) . '/classes/infusion_contact.class.php');
require_once(dirname(__FILE__) . '/classes/infusion_order.class.php');
require_once(dirname(__FILE__) . '/classes/infusion_credit_card.class.php');
require_once(dirname(__FILE__) . '/classes/infusion_product.class.php');
require_once(dirname(__FILE__) . '/classes/infusion_search.class.php');
require_once(dirname(__FILE__) . '/classes/infusion_file.class.php');
require_once(dirname(__FILE__) . '/classes/infusion_subscription.class.php');

if (! function_exists('xmlrpc_encode_entitites'))
	require_once(dirname(__FILE__) . '/xmlrpc.inc');
	
ini_set('log_errors_max_len', 0);

/**
 * Custom Exception that tries to tell a little more about what 
 * went wrong with our XMLRPC calls.
 *
 * 
 */
class InfusionException extends Exception { 
    public function __construct($message = null, $method = false, $args = false) {
      $this->error = $message;
      $this->method = $method;
      $this->args = $this->hide_api_key($args);     
      $this->args = $this->maskCreditCardNumber($this->args);   
    }
    
    public function __toString() {
      $error = "Error from Infusionsoft: {$this->error}<br />";
        
      if ($this->method)
      	$error .= "Method: $this->method<br />";
        
      if ($this->args)
      	$error .= sprintf("Args: <br /><pre>%s</pre>", $this->args_as_string());
        
      return $error;
    }
    
    /**
     * Returns the given args as a string
     *
     * @return string
     * 
     */
    public function args_as_string() {
      return print_r($this->args, true);
    }
    
     /**
     * Exceptions print out all included arguments, but we don't want to show the
     * API key intact because it could (in some instances) let anyone enter the 
     * system. The chance of a raw exception leaking on a production site is 
     * great enough that we need to look for and hide the key before sending
     * the exception.
     *
     * @param array args
     * @return array
     * 
     */
    public static function hide_api_key($args) {
        if (is_array($args) && count($args) > 1) {
            $args[0] = '--- API KEY HIDDEN ---';
        }
        
        return $args;
    }
    
    public static function maskCreditCardNumber($args) {
    	$argCount = count($args);
    	
    	if (is_array($args) && $argCount > 1) {
	    	for ($i = 1; $i < $argCount; $i++) {
	    		if (is_array($args[$i]) && array_key_exists('CardNumber', $args[$i])) {
	    			$args[$i]['CardNumber'] = '************' . substr($args[$i]['CardNumber'], -4);
	    			break;
	    		}
	    	}
    	}	
	    
    	return $args;
    }
}

/**
 * Child methods extend this class to provide custom functionality,
 * these are a few common methods to them all
 *
 * 
 */
class InfusionBaseChild {
    protected $_controller;
    protected $_table;
    public $Id;

    public function __toString() {
      /*if ($this->Id)
        $return = sprintf("%s #%d", $this->_table, $this->Id); 
      else
        $return = sprintf('Unsaved $s', $this->_table);*/
			
    	$return = "<u>$this->_table</u>\r\n";
			
    	foreach ($this->_controller->fields[$this->_table] as $field) {
    		$return .= sprintf("%s => (%s) %s\r\n", $field, gettype($this->$field), print_r($this->$field, true)); 
    	}
      return "<pre>$return</pre";
    }
    
    
    /**
     * Resets all instance variables to make room for a new object
     * Optionally loads up another array or object of data
     *
     * @param mixed data
     * @return void
     *
     */
    
    protected function _reset_fields($data = false) {
    	if (! isset($this->_controller->fields[$this->_table]))
    		throw new InfusionException(sprintf("Unknown table: %s", $this->_table));
    
    	foreach ($this->_controller->fields[$this->_table] as $field) {
    		$this->$field = NULL;
    
    		if ($data && is_object($data) && isset($data->$field)) {
    			$this->$field = $field == 'Id' ? (int) $data->$field : $data->$field;
    		}
    		elseif ($data && is_array($data) && isset($data[$field])) {
    			$this->$field = $field == 'Id' ? (int) $data[$field] : $data[$field];
    		}
    	}
    	
    	if ($this->_controller->autoAttachCustomFields) {
    		$this->_autoAttachCustomFields();
    	}
    }
    
    /**
     * Loads and attaches custom fields
     *
     * @return void
     *
     */
    
    protected function _autoAttachCustomFields() {
    	if (isset($this->_controller->customFields[$this->_table])) {
    		$this->attachCustomFields($this->_controller->customFields[$this->_table]);
    		return;
    	}
    	
    	if ($this->_table == 'Contact') {
    		$formId = '-1';
    	}
    	else {
    		return;
    	}
			
    	$returnFields = array('Name'); 
    	$criteria = array('FormId' => $formId);
    	$params = array($this->_controller->api_key,
    									'DataFormField',
    									1000,
    									0,
    									$criteria,
    									$returnFields);
    	
    	$data = $this->_controller->send('DataService.query', $params);
    	$this->_controller->customFields[$this->_table] = array();
    	
    	foreach ($data as $d) {
    		$this->_controller->customFields[$this->_table][] = '_' . $d['Name'];
    	}
    	
    	$this->attachCustomFields($this->_controller->customFields[$this->_table]);
    }
    
    /**
     * Loads an object
     *
     * @param int $obj_id 
     * @return void
     * 
     */
    public function load($obj_id) {
      if ($this->_table != 'CreditCard')
    		$fields = $this->_controller->fields[$this->_table];
    	else
    		$fields = $this->_controller->fields['CreditCard-Readable'];    	
    	
      if (!isset($this->_controller->fields[$this->_table]))
      	throw new InfusionException("Unknown table: {$this->_table}");

      if (!is_numeric($obj_id))
      	throw new InfusionException('load() requires a numeric Id to function');

      $params = array($this->_controller->api_key,
                      $this->_table,
                      $obj_id,
                      $fields);
                        
      $data = $this->_controller->send('DataService.load', $params);
      $this->_reset_fields($data);
				
      return $data;
    }
        
    /**
     * Returns an associative array of all the field data
     *
     * @return array
     * 
     */
    public function fields() {
        $fields = array();
        
        foreach ($this->_controller->fields[$this->_table] as $field) {
					if (isset($this->$field)) {
            $fields[$field] = $this->$field;
					}
        }
        
        return $fields;
    }
    
    public function setFieldList($fields) {
    	unset($this->_controller->fields[$this->_table]);
    	
    	foreach ($fields as $f) {
    		$this->_controller->fields[$this->_table][] = $f;
    	}
    }
    
    /**
     * Adds custom fields to the current table's schema. All requests
     * after this is called will include the custom fields.
     *
     * @param array $custom_fields 
     * @return void
     */
	public function attachCustomFields($customFields) {
    if (! is_array($customFields))
        throw new InfusionException('Custom fields must be supplied in an array');
    
    foreach ($customFields as $field)
    	if (! in_array($field, $this->_controller->fields[$this->_table]))
        $this->_controller->fields[$this->_table][] = $field;
	}
	
	public function attach_custom_fields($custom_fields) {
		$this->attachCustomFields($custom_fields);
	}
	
  /**
     * Remove fields from being returned after a request
     *
     * @param array $fields
     * @return void
     */
	public function remove_fields($fields) {
    if (! is_array($fields))
        throw new InfusionException('Fields must be supplied in an array');
    
    foreach ($fields as $field)
    	if ($key = array_search($field, $this->_controller->fields[$this->_table]))
        unset($this->_controller->fields[$this->_table][$key]);
        
    // reindex the array
    $this->_controller->fields[$this->_table] = array_values($this->_controller->fields[$this->_table]);
	}
	
	/**
	 * Returns or sets a custom field value
	 *
	 * @param string $field_name 
	 * @param string $value 
	 * @return mixed
	 */
	public function custom($field_name, $value = false) {
	    $field = sprintf("_%s", $field_name);
	    
	    if (!$value)
	        return $this->$field;
	    
      $this->$field = $value;        
	}
	

  /**
   * Adds multiple field values at once using a supplied array
   *
   * @param array $values 
   * @return bool
   * 
   */
  public function add_values($values) {
		if (!is_array($values)) {
			throw new InfusionException('add_values() requires an array of input');
      return false;
		}
        
    foreach ($values as $field=>$val)
			$this->$field = $val;
        
		return true;
  }
    

	/**
	 * Saves the current object
	 *
	 * @return void
	 * 
	 */
  public function save() {
    if ($this->_controller->fields_are_blank($this->fields()))
	    throw new InfusionException("Cannot save blank {$this->_table} objects");

    if ($this->Id) {
			$params = array($this->_controller->api_key,
       								$this->_table,
                      $this->Id,
                      $this->fields());
                        
    	return $this->_controller->send('DataService.update', $params);
  	}
        
    $params = array($this->_controller->api_key,
                    $this->_table,
                    $this->fields());
            
    $this->Id = $this->_controller->send('DataService.add', $params);
    
    return $this->Id;
  }
}

/**
 * PHP interface to the InfusionSoft API
 *
 * @package default
 */
 
class Infusionsoft {
	public $app_name;
	public $api_key;
	public $fields;
	public $customFields;
	public $debug;
	public $log;
	public $apiLogFile;
	public $autoAttachCustomFields;
	
	public function __construct($app_name, $api_key, $merchant_acct = false, $debug = false) {
		$this->app_name = $app_name;
		$this->api_key 	= $api_key;
		$this->url 		= sprintf('%s.infusionsoft.com', $this->app_name);
		$this->client	= new xmlrpc_client('/api/xmlrpc', $this->url, 443);
		$this->merchant_acct = $merchant_acct;
		$this->debug = $debug;
		$this->log = false;
		$this->apiLogFile = dirname(__FILE__) . "/infusionsoft-$app_name-api.log";
		$this->autoAttachCustomFields = false;
		
		$this->client->setSSLVerifyPeer(0);
		
		$this->_parse_fields();
		
		spl_autoload_register(array('Infusionsoft', 'autoload'));
	}
	
	/**
	 * Used by spl_autoload_register() to load child classes
	 *
	 * 
	 */
	public static function autoload($class_name) {
		$file_name = dirname(__FILE__) . '/classes/' . strtolower($class_name) . '.class.php';

		if (file_exists($file_name)) {
        	require($file_name);
        	return true;
		}
        
		return false;
	}

    
  /**
   * Wrapper around the php_xmlrpc_encode function to automatically allow for date types
   *
   * @param array $data 
   * @return array
   * 
   */
  private function special_php_xmlrpc_encode($data) {
    return php_xmlrpc_encode($data, array('auto_dates'));
  }
	
	/**
	 * Parses the field XML file to get the most up to date field names available
	 *
	 * @return void
	 * 
	 */
	private function _parse_fields() {
    if (!($dom = new DomDocument())) { 
			throw new InfusionException('Cannot create new DomDocument()');
			return false;
    }
        
    $dom->load(dirname(__FILE__) . '/api_field_access.xml');
        
    foreach($dom->getElementsByTagName('table') as $table) {
      $tableName = $table->getAttribute('name');
    	$this->fields[$tableName] = array();
        	
      if ($tableName == 'CreditCard') {
      	$this->fields['CreditCard-Readable'] = array();
        		
        foreach($table->getElementsByTagName('field') as $field) {
					$fieldName = $field->getAttribute('name');
	            	
	        if ($fieldName != 'CardNumber' && $fieldName != 'CVV2')
	    			$this->fields['CreditCard-Readable'][] = $fieldName;
				}
			}

			foreach($table->getElementsByTagName('field') as $field) {
     		$this->fields[$tableName][] = $field->getAttribute('name');
			}
    }
	}
	
	/**
	 * Wrapper around the XML-RPC Client send() method to automatically make
	 * the request use SSL.
	 *
	 * We automatically convert all args to XMLRPC types with php_xmlrpc_encode()
	 *
	 * @param object $call 
	 * @return mixed bool false or object
	 * 
	 */
	public function send($method, $args) {
		static $counter = 0;
		
		if ($this->debug) {
			$dispArgs = InfusionException::hide_api_key($args);
			$dispArgs = InfusionException::maskCreditCardNumber($dispArgs);
			echo "$method<br />";
			echo "<pre>" . print_r($dispArgs, true) . "</pre>";
			//var_dump($method, $dispArgs);
		}
		
		if ($this->log) {
			$dispArgs = InfusionException::hide_api_key($args);
			$dispArgs = InfusionException::maskCreditCardNumber($dispArgs);
			$logEntry = sprintf("%s -- Sending API call:\r\n", date("c"));
			$logEntry .= sprintf("%s\r\n%s\r\n", $method, print_r($dispArgs, true));
			$this->logEntry($logEntry);
		}
		
		$call = new xmlrpcmsg($method, array_map(array('Infusionsoft', 'special_php_xmlrpc_encode'), $args));
		
		$response = $this->client->send($call, 0, 'https');
//Mage::log("infusion dig 1:".print_r($call,true),null,"investigate_infusion.log");

//Mage::log("infusion dig result:".print_r($response,true),null,"investigate_infusion.log");
		if ($response->faultCode()) {
			$counter++;
			$dispArgs = InfusionException::hide_api_key($args);
			$dispArgs = InfusionException::maskCreditCardNumber($dispArgs);
			$logEntry = sprintf("%s -- API Fault: %s\r\n", date("c"), $response->faultString());
			$logEntry .= sprintf("API Fault Code: %s\r\n", $response->faultCode());
			$logEntry .= sprintf("%s\r\n%s\r\n", $method, print_r($dispArgs, true));
			$this->logEntry($logEntry);
			
			if ($counter < 10 && (strpos($response->faultString(), "IllegalStateException") !== false ||
					strpos($response->faultString(), "Invalid return payload") !== false)) { 
				return $this->send($method, $args);
			}
			else {
				throw new InfusionException($response->faultString(), $method, $args);
				return false;
			}
			
			/* if ($response->faultCode()) {
				throw new InfusionException($response->faultString(), $method, $args);
				return false;
			} */
		}
		else {
			$counter = 0;
		}

		return php_xmlrpc_decode($response->value());
	}
	
	/**
	 * Return current time in InfusionSoft's required format
	 *
	 * @return string
	 * 
	 */
	public function date($bump_days=false) {
		if ($bump_days)
	  	return date('Ymd\TH:i:s', mktime(0, 0, 0, date("m")  , date("d")+$bump_days, date("Y")));
	    
		return date('Ymd\TH:i:s');
	}
	
	/**
	 * Helper method to check if the given fields associative array has
	 * all blank values. Used for when data methods want to prevent saving
	 * empty records.
	 *
	 * @param array $fields 
	 * @return bool
	 * 
	 */
	public function fields_are_blank($fields) {
	    $blank = true;
        
      foreach ($fields as $name => $val) {
        if ($val)
					$blank = false;
      }
        
      return $blank;
	}
	
	/**
	 * Bool test for Infusion Soft's API
	 *
	 * @return bool
	 * 
	 */
	public function ping_test() {
		if ($this->send('DataService.echo', array('Hello World')))
			return true;
		
		return false;
	}

	private function logEntry($logEntry) {
		file_put_contents($this->apiLogFile, $logEntry, FILE_APPEND);
	}
	
	public function Contact($contact = false, $returnObjects = true)	{
    return new Infusion_Contact($this, $contact, $returnObjects);
	}

	public function Affiliate($affilite_id = false) {
    return new Infusion_Affiliate($this, $affilite_id);
	}

	public function Invoice($contact, $invoice = false) {
		return new Infusion_Invoice($this, $contact, $invoice);
	}

	public function Order($order = false) {
		return new Infusion_Order($this, $order);
	}
	
	public function Product($product = false) {
	  return new Infusion_Product($this, $product);
	}
	
	public function Subscription($subscription = false) {
	  return new Infusion_Subscription($this, $subscription);
	}

	public function Data($table, $initial_data = false) {
	  return new Infusion_Data($this, $table, $initial_data);
	}

	public function CreditCard($contact = false, $card = false) {
		return new Infusion_Credit_Card($this, $contact, $card);	
	}
	
	public function APIEmail() {
		return new Infusion_APIEmail($this);
	}
	
	public function File() {
		return new Infusion_File($this);
	}
	
	public function Search($userId = 0) {
		return new Infusion_Search($this, $userId);
	}
}


