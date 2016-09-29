<?php
class DigitalEdge_LGDiagram_IndexController extends Mage_Core_Controller_Front_Action
{
	const XML_PATH_EMAIL_RECIPIENT  = 'kitchendiagram/email/recipient_email';
	const XML_PATH_EMAIL_SENDER     = 'kitchendiagram/email/sender_email_identity';
	const XML_PATH_EMAIL_TEMPLATE   = 'kitchendiagram/email/email_template';
	const XML_PATH_ENABLED          = 'kitchendiagram/kitchendiagram/enabled';

	public function indexAction()
	{
		//NB:  Mage::getUrl("*/*/post"); produces "lazy-granite-diagram/index/post", which is correct
		//die("Hello Mr. Johnson");
		$this->loadLayout();
		$this->getLayout()->getBlock('root')->setTemplate("page/1column.phtml");
		//Change the value of wanted_title to change the page title
		$wanted_title="";
		if(!empty($wanted_title))
		{
			$this->getLayout()->getBlock('head')->setTitle($wanted_title);
		}
		$formblock=$this->getLayout()->createBlock("core/template","diagram_form",array("template"=>"lgdiagram/form.phtml"),"diagram_form");
		$this->getLayout()->getBlock('content')->append($formblock);
		
		$this->_initLayoutMessages('customer/session');
		$this->renderLayout();
	}

	public function postAction()
	{
        $post = $this->getRequest()->getPost();
        if ( $post ) {
            $translate = Mage::getSingleton('core/translate');
            /* @var $translate Mage_Core_Model_Translate */
            $translate->setTranslateInline(false);
			Mage::getSingleton('customer/session')->setDiagramRetry($post);
            try {
		$complaint="NIL";
                $error = false;

                if (!Zend_Validate::is(trim($post['first_name']) , 'NotEmpty')) {
					throw new Exception("Please state your name.");
                }
                if (!Zend_Validate::is(trim($post['last_name']) , 'NotEmpty')) {
					throw new Exception("Please state your name.");
                }

                if (!Zend_Validate::is(trim($post['description']) , 'NotEmpty')) {
                    throw new Exception("Please provide details about your project.");
				}
				
                if (!Zend_Validate::is(trim($post['email']), 'EmailAddress')) {
                    throw new Exception("Please give a valid email address.");
			
                }
		//	if (!array_key_exists('diagram',$post) || Zend_Validate::is(trim($post['diagram']), 'NotEmpty')) {
		//	$complaint="diagram";
               //     $error = true;
                //}

		    $file_type = strtolower($_FILES['diagram']['type']);
			if(empty($file_type))
			{
				$complaint="No file given.";
				$error=true;
				
			}
			else
			{
			    if ( !in_array($file_type, array('image/jpeg','image/jpg','image/gif','image/png','image/bmp','application/pdf')))
			    {
				$complaint[]=$file_type;
				  $error = true;
			    }
			}
		    
                if (Zend_Validate::is(trim($post['hideit']), 'NotEmpty')) {
			$complaint="hideit";
                    $error = true;
                }

		   

                if ($error) {
                    throw new Exception($complaint);
                }
		  //  $uploads_dir = '/home/ecoflush/bathandgranite4less.com/html/media/diagrams/';
			$uploads_dir =$_SERVER['DOCUMENT_ROOT'].'/media/diagrams/';
		    $post_name = str_replace(' ','_',$post['first_name']." ".$post['last_name']);
			$diagram_name = $post_name . '_' . $_FILES['diagram']['name'];
		    

		    if ( !move_uploaded_file($_FILES['diagram']['tmp_name'], $uploads_dir . $diagram_name) )
		    {
			  throw new Exception("Could not move files to $uploads_dir");
		    }

		    $postObject = new Varien_Object();
		    $diagram_link = Mage::getUrl('media/diagrams/') . $diagram_name;
			/*
		    $postObject->setData($post);

                $mailTemplate = Mage::getModel('core/email_template');
                // @var $mailTemplate Mage_Core_Model_Email_Template 
                $mailTemplate->setDesignConfig(array('area' => 'frontend'))
                    ->setReplyTo($post['email'])
                    ->sendTransactional(
                        Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE),
                        Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
                        Mage::getStoreConfig('kitchendiagram_config/general/email'),
                        //Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT),
                        null,
                        array('data' => $postObject)
                    );
			
                if (!$mailTemplate->getSentSuccess()) {
                    throw new Exception("Did not succeed in sending mail");
                }
			*/

                //$translate->setTranslateInline(true);


				Mage::getSingleton('customer/session')->unsDiagramRetry();
				echo "diagram_link:".$diagram_link;
				//redirect to success is handled by NetSuite 
                return;
            } catch (Exception $e) {
                $translate->setTranslateInline(true);
                Mage::getSingleton('customer/session')->addError(Mage::helper('contacts')->__($e->getMessage()));
                 echo "redirect:".Mage::getUrl('*/*/');
                return;
            }

        } else {
            $this->_redirect('*/*/');
        }
    }
}
