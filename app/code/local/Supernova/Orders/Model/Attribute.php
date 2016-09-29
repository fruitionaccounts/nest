<?php

/*...
 *@team: supernova
  @email: supernova.cps@gmail.com
  @phone: +84989302850
  @package: filer product
 ...*/


class Supernova_Orders_Model_Attribute   extends Mage_Eav_Model_Mysql4_Entity_Attribute_Collection
{
        const COLOR = 'color';
         protected $_color = null;
     
        public function getColorToFilter(){
           if (is_null($this->_color)) {
                $option = array(
                     'value'=>Mage::helper('core')->__(''),
                     'label'=>Mage::helper('core')->__('Name of a Granite Color')
                  );
            $options  = Mage::getModel('eav/config')->getAttribute('catalog_product', 'color')
                              ->getSource()->getAllOptions(false);
            $this->_color = $options;
            $opt = $this->_color;
            array_unshift($opt,$option);
        }
        return $opt;
        }


    
}

