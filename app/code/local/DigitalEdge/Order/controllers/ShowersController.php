<?php
	
class DigitalEdge_Order_ShowersController extends Mage_Core_Controller_Front_Action {
	public function indexAction() 
	{

		$this->loadLayout();
$this->getLayout()->getBlock("root")->setTemplate("page/1column.phtml");
		$block=$this->getLayout()->createBlock('core/template');
		$block->setTemplate("catalog/product/shower_order.phtml");
		$this->getLayout()->getBlock("content")->append($block);
		
     	$this->renderLayout();
		
		//die();
	}
}
?>