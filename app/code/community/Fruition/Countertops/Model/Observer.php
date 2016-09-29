<?php
class Fruition_Countertops_Model_Observer
{

    public function saveDiagram($observer)
    {
        $order_id=$observer->getEvent()->getOrder()->getIncrementId(); //getId() is blank at this time
        //Mage::log($quote_id." mogrifies to ".$order_id." or is it ".$observer->getEvent()->getOrder()->getIncrementId());
        
        //Mage::getModel('countertops/counterdiagram')->getCollection();
		/*
		as Fruition_Countertops_Model_Counterdiagram extends Mage_Core_Model_Abstract, we need a compatible save() methon
		(FLJ, 12/18/15)
		*/
        Mage::getModel('countertops/counterdiagram')->save();

    }

    // manages min pricing for product
    public function updatePrice($observer){
        $quoteItem = $observer->getEvent()->getQuoteItem();
        $product = $observer->getEvent()->getProduct(); 
        $minprice = '';

        $itemsku = $quoteItem->getSku();
        $designlink = 'minprice'.$itemsku;
        $minprice = Mage::getSingleton('core/session')->getData($designlink); 
        // we have an issue with 2 of the same sku added to cart
        if ($minprice != ''){
            $quoteItem->setOriginalCustomPrice($minprice);
            Mage::getSingleton('core/session')->setData($designlink,'');
        }

    }

}