<?php

/**
 * Created by PhpStorm.
 * User: dseibert
 * Date: 4/5/16
 * Time: 9:13 AM
 */
class Fruition_Customcounter_Model_Observer
{

    public function addOptionsToOrder(Varien_Event_Observer $observer)
    {
        $quoteItem = $observer->getItem();
        if ($additionalOptions = $quoteItem->getOptionByCode('additional_options')) {
            $orderItem = $observer->getOrderItem();
            $options = $orderItem->getProductOptions();
            $options['additional_options'] = unserialize($additionalOptions->getValue());
            $orderItem->setProductOptions($options);
        }
    }
}