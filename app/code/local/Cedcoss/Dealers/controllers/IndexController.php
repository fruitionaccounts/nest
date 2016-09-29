<?php 
class Cedcoss_Dealers_IndexController extends Mage_Core_Controller_Front_Action
{
	 public function preDispatch()
	{
		parent::preDispatch();

		if (!Mage::getSingleton('customer/session')->authenticate($this)) {
			$this->setFlag('', 'no-dispatch', true);
		}elseif($this->checkGroup())
		{	
			
			$this->_redirect('customer/account');
		}
	}
	public function checkGroup()
	{
		//here is the implementation that matters on nest (FLJ, 7/25/15)

		$group_id=$this->_getSession()->getCustomerGroupId();
		$group_name = Mage::getModel('customer/group')->load($group_id)->getCode();
		if(stripos("reseller",$group_name)===0)
		{
			return false; //that means this user passed the check, and this user may see the dealer portal
		}
		/*
		if(Mage::app()->getStore()->getName()!='Dealers')
		{
			return true;
		}
		if(Mage::app()->getStore()->getWebsite()->getName()!='shopshowers')
		{
			return true;
		}
		*/
		$groups=explode(',',Mage::getStoreConfig('customer/dealersgroup/groupname',Mage::app()->getStore()->getId()));
		
		/* echo $this->_getSession()->getCustomerGroupId();
		print_r($groups);
		var_dump(in_array($this->_getSession()->getCustomerGroupId(),$groups));die; */
		if(!in_array($this->_getSession()->getCustomerGroupId(),$groups))
		{	
			return true;
		}
		return false;
		
	}
	 /**
		* Retrieve customer session model object
		*
		* @return Mage_Customer_Model_Session
		*/
		protected function _getSession()
		{
		return Mage::getSingleton('customer/session');
		}

	/* public function preDispatch()
	{
		parent::preDispatch();
		if (!$this->getRequest()->isDispatched()) {
		return;
		}

		$action = $this->getRequest()->getActionName();
		$openActions = array(
		'create',
		'login',
		'logoutsuccess',
		'forgotpassword',
		'forgotpasswordpost',
		'resetpassword',
		'resetpasswordpost',
		'confirm',
		'confirmation'
		);
		$pattern = '/^(' . implode('|', $openActions) . ')/i';

		if (!preg_match($pattern, $action)) {
			if (!$this->_getSession()->authenticate($this)) {
				$this->setFlag('', 'no-dispatch', true);
			}elseif($this->_getSession()->authenticate($this) && $this->_getSession()->getCustomerGroupId()==3){
				$this->_redirect('dealer-portal/index/index');
			}
		} else {
			$this->_getSession()->setNoReferer(true);
		}
	}
 */
	public function indexAction()
	{
		$this->loadLayout(); //print_r($this->loadLayout());
        $this->renderLayout();
		//echo "ecch".print_r($this->getResponse()->getHttpResponseCode(),true);
		//die();
	}
	public function loadBlockAction()
    {	
        $request = $this->getRequest();
        $asJson= $request->getParam('json');
        $block = $request->getParam('block');

        $update = $this->getLayout()->getUpdate();
        if ($asJson) {
            $update->addHandle('adminhtml_sales_order_create_load_block_json');
        } else {
            $update->addHandle('adminhtml_sales_order_create_load_block_plain');
        }

        if ($block) {
            $blocks = explode(',', $block);
            if ($asJson && !in_array('message', $blocks)) {
                $blocks[] = 'message';
            }

            foreach ($blocks as $block) {
                $update->addHandle('adminhtml_sales_order_create_load_block_' . $block);
            }
        }
        $this->loadLayoutUpdates()->generateLayoutXml()->generateLayoutBlocks();
        $result = $this->getLayout()->createBlock('Cedcoss_Dealers_Block_Sales_Create_Search_Grid')->toHtml();
        if ($request->getParam('as_js_varname')) {
            Mage::getSingleton('adminhtml/session')->setUpdateResult($result);
            $this->_redirect('*/*/showUpdateResult');
        } else {
            $this->getResponse()->setBody($result);
        }
    }
}