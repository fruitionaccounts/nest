<?php
/**
 * Created by PhpStorm.
 * User: scotttaylor
 * Date: 2/2/16
 * Time: 2:08 PM
 */ 
class Fruition_Customcounter_Model_Resource_Counterproduct extends Mage_Core_Model_Resource_Db_Abstract
{

    protected function _construct()
    {
        $this->_init('fruition_customcounter/counter_product', 'id');
    }

}