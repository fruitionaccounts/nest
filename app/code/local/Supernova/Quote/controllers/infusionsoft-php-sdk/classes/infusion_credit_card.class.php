<?php

class Infusion_Credit_Card extends InfusionBaseChild {
    
    /**
     * Infusion_Credit_Card objects are used by Infusion_Invoice objects to charge
     *
     * If you supply a numeric card number the card is verified to exist and if
     * so loaded as the object. Numbers can be supplied either as just the last
     * four or the entire CC number.
     *
     * Supplying an array as $card makes the class first attempt to verify the card
     * and if it does not exist add it and load it as the object.
     *
     *
     * @param object $controller 
     * @param object $contact 
     * @param mixed $card 
     * 
     */
    public function __construct($controller, $contact, $card = FALSE)
    {
        $this->_controller = $controller;
        $this->_table = 'CreditCard';
        $this->_contact = $contact;

        $this->_reset_fields();
        
        if (is_numeric($card))
          $this->load($card);
        else if (is_string($card) && strlen($card) == 4)
        	$this->locateExistingCard($card);
        else if (is_array($card)) {
          if (!empty($card['CardNumber']))
            $this->add($card);
          else
            throw new InfusionException('You must supply a credit card number!');
        }
    }
    
    public function __tostring()
    {
        return 'Credit Card';
    }

    /**
     * Clears existing values and loads a credit card from a credit card data array
     *
     * @param array $credit_card_data 
     * @return void
     * 
     
    private function _load_array_as_credit_card($credit_card_data)
    {
        if (!isset($credit_card_data['Id']))
        {
            throw new InfusionException('An Id must be provided to load a credit card');
        }
        
        $this->_reset_fields();
        
        foreach ($credit_card_data as $col => $val)
        {
            $this->$col = $val;
        }
    }
    
    /**
     * Loads a credit card from Infusionsoft
     *
     * @param int $credit_card_id
     * @return void
     * 
     
    public function load($credit_card_id)
    {
        if (!is_numeric($credit_card_id))
        {
            throw new InfusionException('load() requires a numeric Id to function');
        }
        
        $params = array($this->_controller->api_key,
        				$this->_table, 
                        intval($credit_card_id), 
                        $this->_controller->fields[$this->_table]);
        
        $credit_card = $this->_controller->send('DataService.load', $params);
                
        $this->_load_array_as_credit_card($credit_card);
    }
    */
    
    /**
     * Adds a credit card to a user
     *
     * @param array $card 
     * @return void
     * 
     */
    public function add($card) {
        if (! isset($card['ContactId'])) {
            if (is_object($this->_contact) && $this->_contact->Id)
                $card['ContactId'] = $this->_contact->Id;
            else
                throw new InfusionException("Card can't be added without a contact id");
        }
        
        if ($this->LocateExistingCard($card))
        	$this->update($card);
        else {
	        if ($this->validate($card)) {
	            $params = array($this->_controller->api_key,
	                            $this->_table,
	                            $card);
	                        
	            $this->Id = $this->_controller->send('DataService.add', $params);
	        }
	        else {
	            throw new InfusionException('Supplied card was invalid');
	        }
        }
    }
    
    /**
     * Updates a credit card for a user
     *
     * @param array $card 
     * @return void
     * 
     */
    public function update($card) {
    		if (! isset($card['ContactId'])) {
            if (is_object($this->_contact) && $this->_contact->Id)
                $card['ContactId'] = $this->_contact->Id;
            else
                throw new InfusionException("Card can't be updated without a contact id");
        }
        
        $params = array($this->_controller->api_key,
                        $this->_table,
                        $this->Id,
                        $card);
                        
        $this->Id = $this->_controller->send('DataService.update', $params);
    }
        
    /**
     * Verifies a card
     *
     * @param mixed $card 
     * @return bool
     * 
     */
    public function validate($card = false) {
        if (!$card && !$this->Id) {
            throw new InfusionException('You must first supply or load a card before validating it');
        }
        
        $params = array($this->_controller->api_key,
                        $card);
                        
        $status = $this->_controller->send('InvoiceService.validateCreditCard', $params);
        
        if ($status['Valid'] != 'true') {
            throw new InfusionException(sprintf('Credit card invalid: %s', $status['Message']));
        }
        else {
            return true;
        }

    }
    
    /**
     * Locates and loads a card if it exists. $card_number can be supplied as the whole
     * card number or just last four digits.
     *
     * @param array $card
     * @return bool
     * 
     */
    public function locateExistingCard($card) {
    	$existingCard = false;
    	$cardNumber = $card['CardNumber'];
    	
    	if (! isset($card['ContactId'])) {
				throw new InfusionException("Card can't be located without a contact id");
    	}
    	
      if (strlen($cardNumber) > 4)
				$last4 = substr($cardNumber, -4);
      else
				$last4 = $cardNumber;
        
      $params = array($this->_controller->api_key,
                      $card['ContactId'],
                      $last4);
                        
      $cardId = $this->_controller->send('InvoiceService.locateExistingCard', $params);
        
      if ($cardId > 0) {
        $this->Id = $cardId;
        $existingCard = true;
      }
      
      return $existingCard;
    }
}