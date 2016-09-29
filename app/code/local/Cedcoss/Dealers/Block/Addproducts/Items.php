<?php
class Cedcoss_Dealers_Block_Addproducts_Items extends Mage_Adminhtml_Block_Sales_Order_Create_Items
{
	public function getHeaderText(){
		return $this->__('Dealer Products');
	}
}