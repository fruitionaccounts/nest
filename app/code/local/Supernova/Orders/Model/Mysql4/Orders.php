<?php

class Supernova_Orders_Model_Mysql4_Orders extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the orders_id refers to the key field in your database table.
        $this->_init('orders/orders', 'orders_id');
    }
}