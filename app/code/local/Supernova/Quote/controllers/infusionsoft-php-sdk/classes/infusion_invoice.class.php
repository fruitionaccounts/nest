<?php

class Infusion_Invoice extends InfusionBaseChild {
	public $doRecalculateTax;
	public $order;
    
    public function __construct($controller, $contact = false, $invoice = false)
    {
    	/*
        if (!$contact->Id)
        {
            throw new InfusionException('A contact is required before an invoice can be initialized');
        }
      */  
        $this->_controller  = $controller;
        $this->_table       = 'Invoice';
        $this->_contact     = $contact;
        $this->doRecalculateTax = false;
        
        if (is_numeric($invoice))
            $this->Id = $invoice;
    }
    
    /**
     * Creates a new blank invoice and loads it as the current Infusion_Invoice object
     *
     * @return int
     * 
     */
    public function create($desc = 'API Order', $date = 0, $lead_affiliate_id = 0, $sale_affiliate_id = 0)
    {
        $this->_reset_fields();
        
        if ($date == 0)
        	$date = date('Ymd\TH:i:s', time());
        
        $params = array($this->_controller->api_key,
                        $this->_contact->Id,
                        $desc,
                        php_xmlrpc_encode($date, array('auto_dates')),
                        (int) $lead_affiliate_id,
                        (int) $sale_affiliate_id);

        $invoice = $this->_controller->send('InvoiceService.createBlankOrder', $params);
        
        $this->Id = $invoice;
				//$this->updateOrderShippingFields();
        
        return $invoice;
    }
    
	/**
     * Returns all the payments for the current invoice
     *
     * @return array
     * 
     */
    public function addManualPayment($amt, $payment_date, $payment_type,
    									$payment_description = 'API Payment', $bypass_commissions = false) {
    	if (empty($payment_date))
    		$payment_date = $this->_controller->date();

    	if ($this->doRecalculateTax) {
    		$this->recalculateTax();
    	}
    	
      $params = array($this->_controller->api_key,
                      $this->Id,
                      $amt,
                      $payment_date,
                      $payment_type,
                      $payment_description,
                      $bypass_commissions
                      );
                        
      return $this->_controller->send('InvoiceService.addManualPayment', $params);
    }
    
    public function AddPaymentPlan($card, $numberOfPaymentLegs, $daysUntilFirstLegCharge,  
    															$daysBetweenPaymentLegs, $immediateChargeAmount = 0,
    															$autoCharge = true, $daysBetweenRetry = 2, $maxRetry = 3) {
      if (empty($this->_controller->merchant_acct))
        throw new InfusionException('You must set a merchant account before adding a payment plan to an invoice');

			$params = array($this->_controller->api_key,
											$this->Id,
											$autoCharge,
											$card->Id,
											$this->_controller->merchant_acct,
											$daysBetweenRetry,
											$maxRetry,
											(double) $immediateChargeAmount,
											$this->_controller->date(),
											$this->_controller->date($daysUntilFirstLegCharge),
											$numberOfPaymentLegs,
											$daysBetweenPaymentLegs
			);
									
			return $this->_controller->send('InvoiceService.addPaymentPlan', $params);
    }
    
    /**
     * Returns all the payments for the current invoice
     *
     * @return array
     * 
     */
    public function getPayments()
    {
        $params = array($this->_controller->api_key,
                        $this->Id);
                        
        return $this->_controller->send('InvoiceService.getPayments', $params);
    }
    
    /**
     * Adds an Infusion_Product to the existing invoice
     *
     * @param object $product 
     * @param int $quantity 
     * @param int $type
     * @param int $price
     * @return bool
     * 
     */
    public function add($product, $quantity = 1, $type = 0, $price = -1)
    {
    		if ($price == -1) {
    			$price = $product->ProductPrice;
    		}
    		
    		if (! isset($product->Description)) {
    			$product->Description = '';
    		}
    		
    		$params = array($this->_controller->api_key,
                        (int) $this->Id,
                        (int) $product->Id,
                        (int) $type,
                        (double) $price,
                        (int) $quantity,
                        (string) $product->ProductName,
                        (string) $product->Description);
                        
        return $this->_controller->send('InvoiceService.addOrderItem', $params);

    }
    
    /**
     * Applies taxes to the current invoice. We automatically run this in the 
     * charge() method.
     *
     * @return void
     * 
     */
    public function recalculateTax()
    {
        $params = array($this->_controller->api_key,
                        $this->Id);
                        
        return $this->_controller->send('InvoiceService.recalculateTax', $params);
    }

    /**
     * Returns any remaining total on the curent invoice
     *
     * @return float
     * 
     */
    public function calculateAmountOwed()
    {
        $params = array($this->_controller->api_key,
                        $this->Id);
                        
        return $this->_controller->send('InvoiceService.calculateAmountOwed', $params);
    }
    
    /**
     * Charges the invoice with the given Infusion_Credit_Card object
     *
     * @param object $card 
     * @return bool
     * 
     */
    public function charge($card, $notes = 'API Payment', $bypass_commissions = FALSE) {
        if (!$this->_controller->merchant_acct)
          throw new InfusionException('You must set a merchant account before charging an invoice');
        
        if ($this->doRecalculateTax) {
        	$tax = $this->recalculateTax();
        	var_dump($tax);
        }
        
        $params = array($this->_controller->api_key,
                        $this->Id,
                        $notes,
                        $card->Id,
                        $this->_controller->merchant_acct,
                        $bypass_commissions);
                        
        return $this->_controller->send('InvoiceService.chargeInvoice', $params);
    }

	/**
		* Deletes invoice
		*
		* @return bool
		* 
		*/
	public function delete() {
		$params = array($this->_controller->api_key,
										$this->Id);
                        
		return $this->_controller->send('InvoiceService.deleteInvoice', $params);
	}

	/**
		* Deletes subscription
		*
		* @return bool
		*
		*/
	public function deleteSubscription($recurringOrderId) {
		$params = array($this->_controller->api_key,
										$recurringOrderId);
	
		return $this->_controller->send('InvoiceService.deleteSubscription', $params);
	}
	
  /**
   * Adds a subscription for a contact.
   *  
   * @return int
   */
	public function addRecurringOrder($cProgramId, $creditCard, $affiliateId = 0, $daysUntilCharge = 0,
																		$allowDuplicate = false) {
		$params = array($this->_controller->api_key,
    								(int) $this->_contact->Id,
    								(boolean) $allowDuplicate,
								    (int) $cProgramId,
	        					(int) $this->_controller->merchant_acct,
								    (int) $creditCard->Id,
								    (int) $affiliateId,
								    (int) $daysUntilCharge);
	  
		return $this->_controller->send('InvoiceService.addRecurringOrder', $params);
	}

  /**
   * Adds a subscription for a contact. Allows Quantity, Price and Tax.
   *  
   * @return int
   */
	public function addRecurringOrderAdvanced($cProgramId, $qty, $price, $creditCard, $affiliateId = 0, 
																						$daysUntilCharge = 0, $allowTax = true, $allowDuplicate = false) {
		$params = array($this->_controller->api_key,
    								(int) $this->_contact->Id,
    								(boolean) $allowDuplicate,
								    (int) $cProgramId,
								    (int) $qty,
								    (double) $price,
								    (boolean) $allowTax,
								    (int) $this->_controller->merchant_acct,
								    (int) $creditCard->Id,
								    (int) $affiliateId,
								    (int) $daysUntilCharge);
								
		return $this->_controller->send('InvoiceService.addRecurringOrder', $params);
	}

  /**
   * Adds a subscription for a contact.
   *  
   * @return int
   */
	public function createForRecurring($recurringOrderId) {
		$params = array($this->_controller->api_key,
    								(int) $recurringOrderId);
	  
		$invoice = $this->_controller->send('InvoiceService.createInvoiceForRecurring', $params);
		
		$this->Id = $invoice;
		return $invoice;
	}
	
	private function updateOrderShippingFields() {
		$this->order = $this->_controller->Order();
		$this->order->loadFromInvoiceId($this->Id);
		$this->order->ShipFirstName = $this->_contact->FirstName;
		$this->order->ShipMiddleName = $this->_contact->MiddleName;
		$this->order->ShipLastName = $this->_contact->LastName ;
		$this->order->ShipCompany = $this->_contact->Company ;
		$this->order->ShipPhone = $this->_contact->Phone1 ;
		$this->order->ShipStreet1 = $this->_contact->Address2Street1;
		$this->order->ShipStreet2 = $this->_contact->Address2Street2;
		$this->order->ShipCity = $this->_contact->City2;
		$this->order->ShipState = $this->_contact->State2;
		$this->order->ShipZip = $this->_contact->PostalCode2;
		$this->order->ShipCountry = $this->_contact->Country2;
		$this->order->save();
	}
	
	/*
	 * Keep old functions/names for backward compatibility
	 */
	public function add_manual_payment($amt, $payment_date, $payment_type, $payment_description = '', $bypass_commissions = false) {
		$this->addManualPayment($amt, $payment_date, $payment_type, $payment_description = '', $bypass_commissions = false);
	}
}