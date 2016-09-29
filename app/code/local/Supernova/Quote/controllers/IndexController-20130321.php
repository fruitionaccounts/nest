<?php

require_once('my-infusionsoft.php');

class Supernova_Quote_IndexController extends Mage_Core_Controller_Front_Action
{
    public $settings;
    public $log = array();
    public $_rates = 0;
    
    public function indexAction()
    {
	//the crude way to cut in an Action (FLJ, 3/20/2013)
	if(stripos($this->getRequest()->getRequestString(),"quote_thanks.html")!==FALSE)
	{
		$this->thanksAction();
		return;
	}
            $this->loadLayout();
            $this->renderLayout();
    }

public function thanksAction()
    {
	
$this->loadLayout();
$this->getLayout()->getBlock('root')->setTemplate("page/1column.phtml");
$block = $this->getLayout()->createBlock('Mage_Core_Block_Template',
'product_list',
array('template' => 'catalog/product/quote-thanks.phtml')
);
$this->getLayout()->getBlock('content')->append($block);
$this->renderLayout();
	
    }

     public function getProductsAction()
    {
            $body =  $this->getLayout()->createBlock('quote/products')->toHtml();
    	    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($body));
    }
    public function getproductAction(){
           $body =  $this->getLayout()->createBlock('quote/product')->toHtml();
    	   $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($body));
    }
    
    public function quoteAction() {
        if (!empty($_POST['productId'])) {
            $productId = $this->getRequest()->getParam('productId');
            $product = Mage::getModel('catalog/product')->load($productId);
            if (is_null($product->getTypeInstance(true)->getStoreFilter($product))) {
                $product->getTypeInstance(true)->setStoreFilter(Mage::app()->getStore(), $product);
            }
        }

        // print_r($_POST);
        $quote = $_POST['SquareFeetQuantity'] * $_POST['sqFeetPrice'];
        $SinkKit = 0;
        if ($_POST['SinkKit'] == 1) {
            $quote += $_POST['sinkKitPrice'];
            $SinkKit = 1;
        }   


        if (!empty($_POST['FirstName']) and !empty($_POST['LastName']) and !empty($_POST['email'])) {
            define('InfusionAppName', 'ql117');
            define('InfusionApiKey', '466f0caa3afa52042cee1f1da6405189c214e824eb8538946978ca62261dbc36');
            $infusion = new MyInfusionsoft(InfusionAppName, InfusionApiKey);                    
            $contact = $infusion->Contact();
            $contact->attachCustomFields(array('_NumberOfSquareFeet', '_SinkKit0', '_GraniteColor', '_QuoteAmount'));            
            $contact->FirstName = $_POST['FirstName'];
            $contact->LastName = $_POST['LastName'];
            $contact->Email = $_POST['email'];            
            $contact->_NumberOfSquareFeet = $_POST['SquareFeetQuantity'];
            $contact->_SinkKit0 = $SinkKit;
            $contact->_GraniteColor = $_POST['productName'];
            $contact->_QuoteAmount = round($quote * 100) / 100;
            $contactId = $infusion->addContact($contact->fields());
            print_r($contactId);
            $groupId = 107;
            $contact->Id = $contactId;
            //$contact->remove_from_group($groupId);
//assign a campaign based on the current store
		//(FLJ, 3/3/2013)
		$store_url=Mage::app()->getStore()->getUrl('quote/index/quote'); //Mage::app()->getStore()->getHomeUrl();
		
            
	

		try
		{
//		$contact->remove_from_campaign(1);
//		$contact->remove_from_campaign(3);
		if(stripos($store_url,"bath")!==FALSE)
		{
			//$contact->remove_from_campaign(19);
			$groupId=123; //this is a tag which triggers Campaign 19
		}
		else if(stripos($store_url,"lazy")!==FALSE)
		{
			//$contact->remove_from_campaign(3);
			$groupId=107; //this is the tag which triggers Campaign 3
		}
          $contact->addToGroup($groupId);  
		}
		catch(InfusionException $ecch)
		{
echo "BZZZT:".$ecch;
		}
		
        }
        exit;
    }
   
  
    public function getIPaddress(){
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                 $ip=$_SERVER['HTTP_CLIENT_IP'];
        }
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                  $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else{
                  $ip=$_SERVER['REMOTE_ADDR'];
        }
        $ip_server = substr($ip,0,9);

        if($ip_server=='192.168.1'){
            $ip='125.214.27.143';
        }
        return $ip; //'67.222.35.241';
    }
       
}
