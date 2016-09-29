<?php

class Fruition_Customcounter_Block_Adminhtml_Sales_Items_Column_Name extends Mage_Adminhtml_Block_Sales_Items_Column_Default {

    public function getFormattedOption($value) {
        $_remainder = '';
        $value = Mage::helper('core/string')->truncate($value, 55, '', $_remainder);
        $result = array(
            'value' => nl2br($value),
            'remainder' => nl2br($_remainder)
        );
        return $result;
    }
}