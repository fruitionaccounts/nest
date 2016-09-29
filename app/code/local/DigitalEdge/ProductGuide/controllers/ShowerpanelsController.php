<?php
	
class DigitalEdge_ProductGuide_ShowerpanelsController extends Mage_Core_Controller_Front_Action {
	public function indexAction() 
	{

		$this->loadLayout();
$this->getLayout()->getBlock("root")->setTemplate("page/1column.phtml");
		$block=$this->getLayout()->createBlock('core/template');
		$block->setTemplate("catalog/product/panel_guide.phtml");
		$this->getLayout()->getBlock("content")->append($block);
     	$this->renderLayout();
		
		//die();
	}
}
?>