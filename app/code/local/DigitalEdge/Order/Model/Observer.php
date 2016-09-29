<?php
class DigitalEdge_Order_Model_Observer {
	public function sales_convert_quote_to_order($observer)
	{
		//Mage::log('ulp', null, "flj.log");
		$quote_id=$observer->getEvent()->getQuote()->getId();
		$order_id=$observer->getEvent()->getOrder()->getIncrementId(); //getId() is blank at this time
		//Mage::log($quote_id." mogrifies to ".$order_id." or is it ".$observer->getEvent()->getOrder()->getIncrementId(),null, "flj.log");
		$bunch=Mage::getModel('order/dealerdiagram')->getCollection()
			->addFieldToFilter('quote_id',$quote_id);
		foreach($bunch as $diagram)
		{
			$diagram->setOrderId($order_id);
			Mage::getModel('order/dealerdiagram')->update($diagram);
		}
		//Mage::log($quote_id." mogrified to ".$order_id,null, "flj.log");
	}
}

?>