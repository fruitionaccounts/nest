<?php
/**
* Oscheckout Extension
*
* @category  Magento Extensions
* @package   AHT_Oscheckout
* @author    Hoang Nguyen <hoangeht2004@gmail.com>
* @copyright 2007-2011 AHT
* @license   http://store.ahtsoft.com/license.txt
* @version   1.0.1
* @link      http://store.ahtsoft.com
*/

class Supernova_Orders_Block_Checkout_Shipping_Method_Available extends Mage_Checkout_Block_Onepage_Abstract
{
    protected $_rates;
    protected $_address;

    public function getShippingRates()
    {

        if (empty($this->_rates)) {
            $this->getAddress()->collectShippingRates()->save();

            $groups = $this->getAddress()->getGroupedAllShippingRates();
           
            return $this->_rates = $groups;
        }

        return $this->_rates;
    }

    public function getAddress()
    {
        if (empty($this->_address)) {
            $this->_address = $this->getQuote()->getShippingAddress();
        }
        return $this->_address;
    }

    public function getCarrierName($carrierCode)
    {
        if ($name = Mage::getStoreConfig('carriers/'.$carrierCode.'/title')) {
            return $name;
        }
        return $carrierCode;
    }

    public function getAddressShippingMethod()
    {
        return $this->getAddress()->getShippingMethod();
    }

    public function getShippingPrice($price, $flag)
    {
        return $this->getQuote()->getStore()->convertPrice(Mage::helper('tax')->getShippingPrice($price, $flag, $this->getAddress()), true);
    }
   
}
