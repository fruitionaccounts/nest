<?php
class Cedcoss_Dealers_Block_Addproducts_Search extends Mage_Adminhtml_Block_Sales_Order_Create_Search
{
	public function getButtonsHtml()
	{
		$addButtonData = array(
				'label' => Mage::helper('sales')->__('Add Selected Product(s)'),
				'onclick' => 'addproducts()',
				'class' => 'add',
		);
		return $this->getLayout()->createBlock('adminhtml/widget_button')->setData($addButtonData)->toHtml();
	}
}