<?php

class Infusion_APIEmail {
    public $email;
    
    public function __construct($controller)
    {
        $this->_controller = $controller;
        $this->email = null;        
    }
    
    /**
     * Loads a generic email "template" that will be used to send
     *
     * @param string $content_type
     * @return void
     * 
     */
    public function loadBlankTemplate($content_type)
    {
        $this->email = new EmailTemplate($content_type);
    }    
    
    /**
     * Gets an email template
     *
     * @param int $templateId
     * @return array
     * 
     */
    public function getEmailTemplate($templateId) {        
        $params = array($this->_controller->api_key,
                        (int) $templateId);
                    
        return $this->_controller->send('APIEmailService.getEmailTemplate', $params);
    }  
    
    /**
     * Opt-In an email address
     *
     * @param string $email 
     * @param string $permission_reason
     * @return void
     * 
     */
    public function optIn($email, $permission_reason = 'API Opt-in')
    {        
        $params = array($this->_controller->api_key,
                        $email,
                        $permission_reason);
                    
        $this->_controller->send('APIEmailService.optIn', $params);
    }
    
	/**
     * Opt-Out an email address
     *
     * @param string $email 
     * @param string $permission_reason
     * @return void
     * 
     */
    public function optOut($email, $permission_reason = 'API Opt-out')
    {
        $params = array($this->_controller->api_key,
                        $email,
                        $permission_reason);
                    
        $this->_controller->send('APIEmailService.optOut', $params);
    }
    
    /**
     * Sends an email through the application and links the sent email to the given contact.
     *
     * @param array $contactList 
     * @return bool
     * 
     */
    public function sendEmail($contactList, $templateId = 0)
    {
        if (!$this->email && $templateId == 0)
        {
            throw new InfusionException('You must load an email template or pass a template id before sending email!');
        }
        
        if ($templateId == 0) {
	        $params = array($this->_controller->api_key,
	                        $contactList,
	                        $this->email->fromAddress,
	                        $this->email->toAddress,
	                        $this->email->ccAddresses,
	                        $this->email->bccAddresses,
	                        $this->email->contentType,                        
	                        $this->email->subject,
	                        $this->email->htmlBody,
	                        $this->email->textBody);
        }
        else {
        	$params = array($this->_controller->api_key,
								        	$contactList,
								        	(int) $templateId);
        }
        
        return $this->_controller->send('APIEmailService.sendEmail', $params);
    }
}

class ContentType {
	const text = 'Text';
	const html = 'HTML';
	const both = 'Multipart';
}

class EmailTemplate {
	public $fromAddress = '';
	public $toAddress = '';
	public $ccAddresses = '';
	public $bccAddresses = '';
	public $contentType = '';
	public $subject = '';
	public $htmlBody = '';
	public $textBody = '';
	
	public function __construct($cType) {
		$this->contentType = $cType;
	}
}