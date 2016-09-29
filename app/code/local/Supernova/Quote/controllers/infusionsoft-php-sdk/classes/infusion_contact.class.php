<?php

class Infusion_Contact extends InfusionBaseChild {
    
	public $returnObjects;
	public $return_objects; // backward compatibility
	
    public function __construct($controller, $contact = false, $returnObjects = true)
    {
        $this->_controller = $controller;
        $this->_table = 'Contact';
        $this->_reset_fields();
        $this->returnObjects = $returnObjects;  // set to false to return arrays instead of objects                
        $this->return_objects = $returnObjects;
        if ($contact)
        {
            if (is_numeric($contact))
            {
                $this->load($contact);
            }
            else if (is_array($contact))
            {
                $this->_load_array_as_contact($contact);
            }
        }
        
    }
    
    public function __toString() {
        
        if ($this->FirstName && $this->LastName)
        {
            $return = sprintf("%s %s", $this->FirstName, $this->LastName);
        }
        elseif ($this->Email)
        {
            $return = $this->Email;
        } 
        else
        {
            $return = 'Infusionsoft User';
        }
        
        if ($this->Id)
        {
            $return = sprintf("%s (#%d)", $return, $this->Id); 
        }
        else
        {
            $return = sprintf('%s (Unsaved)', $return);
        }
        
        return $return;
    }
    
    /**
     * Clears existing values and loads a contact from a contact data array
     *
     * @param array $contact_data 
     * @return void
     * 
     */
    private function _load_array_as_contact($contactData) {
			if (!isset($contactData['Id']))
				throw new InfusionException('An Id must be provided to load a contact');
        
			//$this->_reset_fields();
			$this->autoAttachCustomFields(array_keys($contactData));
      $contactData['Id'] = (int) $contactData['Id'];

      foreach ($contactData as $col => $val)
      	$this->$col = $val;
      
      //var_dump($this->_controller->fields[$this->_table]);
      /*
       * Not sure we want to do this
       *
			foreach ($contactData as $col => $val) {
				if (is_string($val)) {
					if (strlen($val))
						$this->$col = $val;
				}
				else
					$this->$col = $val;
			}
			*/
    }

    public function autoAttachCustomFields($contactData) {
    	$customFields = array();
    
    	foreach($contactData as $field) {
    		if(strpos($field, '_') !== false)
    			$customFields[] = $field;
    	}
    
    	$this->attachCustomFields($customFields);
    }
    
    /**
     * Loads a contact from Infusionsoft
     *
     * @param int $contactId 
     * @return array
     * 
     */
    public function load($contactId) {
        if (!is_numeric($contactId))
					throw new InfusionException('load() requires a numeric Id to function');
        
        //$this->_reset_fields();
        
        $params = array($this->_controller->api_key, 
                        intval($contactId), 
                        $this->_controller->fields[$this->_table]);
        
        $contact = $this->_controller->send('ContactService.load', $params);
        
        if ($contact && $this->returnObjects && $this->return_objects)
        	$this->_load_array_as_contact($contact);
        
        return $contact;
    }
    
    /**
     * Adds a contact to the database
     *
     * @param array $data 
     * @return object
     * 
     */
    public function add($data) {
        $params = array($this->_controller->api_key, $data);
                
        $contactId = $this->_controller->send('ContactService.add', $params);
        
        return $this->_controller->Contact($contactId);
    }
    
    /**
    * Adds or updates a contact record based on matching data
    *
    * @param array $data
    * @param string $dupCheckType
    * @return object
    *
    */
    public function addWithDupCheck($data, $dupCheckType = 'Email') {
    	$params = array($this->_controller->api_key, $data, $dupCheckType);
    
    	$contactId = $this->_controller->send('ContactService.addWithDupCheck', $params);
    
    	return $this->_controller->Contact($contactId);
    }
    
    /**
     * Adds multiple field values at once using a supplied array
     *
     * @param array $values 
     * @return bool
     * 
     */
    public function addValues($values) {
      if (!is_array($values))
      	return false;
                
      foreach ($values as $field=>$val)
	      $this->$field = $val;
        
      return true;
    }
    
    public function add_values($values) {
    	$this->addValues($values);
    }
    
    /**
     * Returns all matching contacts for a given email address
     * The return is an array of Contact objects
     *
     * @param string $email 
     * @param bool $return_as_objects 
     * @return bool FALSE or array
     * 
     */
    public function find_by_email($email, $return_as_objects = TRUE) {
        $params = array($this->_controller->api_key,
                        $email,
                        $this->_controller->fields[$this->_table]);
                    
        $contacts = $this->_controller->send('ContactService.findByEmail', $params);
        
        if (!$contacts)
        {
            return FALSE;
        }
        
        if ($return_as_objects)
        {
            $return_contacts = array();
        
            foreach ($contacts as $contact)
            {
                $return_contacts[] = $this->_controller->Contact($contact);
            }
        
            return $return_contacts;
        }
        
        return $contacts;
    }
    
    /**
     * Wrapper around find_by_email to limit results to a single contact
     * If a single contact is found that contact's Infusion_Contact object
     * is loaded. If no contacts are found boolean FALSE is returned. If
     * multiple contacts are found an exception is thrown.
     *
     * @param string $email 
     * @return bool
     * 
     */
    public function find_one_by_email($email) {
        $contacts = $this->find_by_email($email, FALSE);
        
        if (!$contacts)
        {
            return FALSE;
        }
        elseif (count($contacts) == 1)
        {
            $this->_load_array_as_contact($contacts[0]);
            return TRUE;
        }
        else
        {
            throw new InfusionException('More than one contact was found with ' . $email);
        }
    }

    /**
     * Searches the table for a specified column and value. Returns an array 
     * of Contact objects
     *
     * @param string $field 
     * @param string $value 
     * @param int $limit 
     * @param int $page 
     * @return array of objects
     * 
     */
    public function findByField($field, $value, $limit = 1000, $page = 0) {		
        $params = array($this->_controller->api_key,
                        $this->_table,
                        $limit,
                        $page,
                        $field,
                        $value,
                        $this->_controller->fields[$this->_table]);
                        
        $contacts = $this->_controller->send('DataService.findByField', $params);
        
        if (!$contacts)
        {
            return false;
        }
        
				$return_contacts = array();
        
        foreach ($contacts as $contact)
        {
        	$return_contacts[] = $this->_controller->Contact($contact);
        }
        
				return $return_contacts;
    }
    
    public function find_by_field($field, $value, $limit = 1000, $page = 0) {
    	$this->findByField($field, $value, $limit = 1000, $page = 0);
    }
    
    /**
     * Adds a contact to a group (AKA tag)
     *
     * @param int $group_id 
     * @return void
     * 
     */
    public function addToGroup($groupId) {
        if (! $this->Id)
        	throw new InfusionException('You must load a contact before adding to a group');
        else if (! $groupId)
        	return false;
        
        $params = array($this->_controller->api_key,
                        $this->Id,
                        (int) $groupId);
                        
        $this->_controller->send('ContactService.addToGroup', $params);
    }
    
    public function add_to_group($groupId) {
    	$this->addToGroup($groupId);
    }

    /**
     * Adds a contact to a campaign (AKA Follow Up Sequence)
     *
     * @param int $campaign_id 
     * @return void
     * 
     */
    public function add_to_campaign($campaign_id) {
        if (! $this->Id)
          throw new InfusionException('You must load a contact before adding to a campaign');
        else if (! $campaign_id)
        	return false;
                
        $params = array($this->_controller->api_key,
                        $this->Id,
                        (int) $campaign_id);
                        
        $this->_controller->send('ContactService.addToCampaign', $params);
    }

    /**
     * Removes a contact from a campaign (AKA Follow Up Sequence)
     *
     * @param int $campaign_id 
     * @return void
     * 
     */
    public function remove_from_campaign($campaign_id) {
        if (! $this->Id)
          throw new InfusionException('You must load a contact before adding to a campaign');
        else if (! $campaign_id)
        	return false;
                
        $params = array($this->_controller->api_key,
                        $this->Id,
                        (int) $campaign_id);
                        
        $this->_controller->send('ContactService.removeFromCampaign', $params);
    }
            
    /**
     * Runs an action sequence for the current contact
     *
     * @param int $action_seq_id 
     * @return void
     * 
     */
    public function runActionSet($action_seq_id) {
        if (! $this->Id)
					throw new InfusionException('You must load a contact before running an action sequence');
        else if (! $action_seq_id)
        	return false;
        
        $params = array($this->_controller->api_key,
                        $this->Id,
                        (int) $action_seq_id);
                        
        $this->_controller->send('ContactService.runActionSequence', $params);
    }

    public function run_action_sequence($action_seq_id) {
    	$this->runActionSet($action_seq_id);
    }
    
    /**
     * Removes a contact from a group (AKA tag)
     *
     * @param int $group_id 
     * @return bool
     * 
     */
    public function remove_from_group($group_id) {
        if (! $this->Id)
            throw new InfusionException('You must load a contact before removing it from a group');
        else if (! $group_id)
        	return false;
                
        $params = array($this->_controller->api_key,
                        $this->Id,
                        (int) $group_id);
                        
        $this->_controller->send('ContactService.removeFromGroup', $params);
    }
        
    /**
     * Saves the current contact by either updating it to Infusionsoft or creating it
     *
     * @return int
     * 
     */
    public function save() {
        if ($this->Id) {
            $params = array($this->_controller->api_key,
                            $this->Id,
                            $this->fields());
            return $this->_controller->send('ContactService.update', $params);
        }
        
        if ($this->_controller->fields_are_blank($this->fields()))
					throw new InfusionException('Cannot save blank contacts');
        
        $params = array($this->_controller->api_key,
                        $this->fields());
            
        $this->Id = $this->_controller->send('ContactService.add', $params);
        
        return $this->Id;
    }
    
	public function delete() {
		if ($this->Id) {
    	$tableContact = new Infusion_Data($this->_controller, 'Contact');
    	$tableContact->delete($this->Id);
		}
		else 
			throw new InfusionException('Cannot delete contact without Id');
	}
	
	public function getNextCampaignStep($sequenceId) {
		if (!$this->Id)
			throw new InfusionException('You must load a contact before you can getNextCampaignStep');
		else if (! $sequenceId)
			return false;
	
		$params = array($this->_controller->api_key,
										$this->Id,
										(int) $sequenceId);
	
		$return = $this->_controller->send('ContactService.getNextCampaignStep', $params);
		return $return;
	}
	
	public function pauseCampaign($sequenceId) {
		if (!$this->Id)
			throw new InfusionException('You must load a contact before pausing a campaign/fus');
		else if (! $sequenceId)
			return false;
        
		$params = array($this->_controller->api_key,
										$this->Id,
										(int) $sequenceId);
                        
		$return = $this->_controller->send('ContactService.pauseCampaign', $params);
		return $return;
	}
	
	public function rescheduleCampaignStep($contactIds, $sequenceStepId) {
		if (!is_array($contactIds))
			throw new InfusionException('You must pass $contactIds in an array');
	
		$params = array($this->_controller->api_key,
										$contactIds,
										(int) $sequenceStepId);
	
		$return = $this->_controller->send('ContactService.rescheduleCampaignStep', $params);
		return $return;
	}
	
	public function Credit_Card($last_4 = FALSE) {
		return new Infusion_Credit_Card($this->_controller, $this, $last_4);
	}
    
	public function Invoice($invoice = FALSE) {
		return new Infusion_Invoice($this->_controller, $this, $invoice);
	}
}