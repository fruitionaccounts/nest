<?php
/**
 * Created by PhpStorm.
 * User: scotttaylor
 * Date: 2/2/16
 * Time: 2:08 PM
 */ 
class Fruition_Customcounter_Model_Resource_Counterproduct_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{

    protected function _construct()
    {
        $this->_init('fruition_customcounter/counterproduct');
    }

}