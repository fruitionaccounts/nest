<?php

/**
 * Created by PhpStorm.
 * User: scotttaylor
 * Date: 2/2/16
 * Time: 2:08 PM
 */
class Fruition_Customcounter_Model_Counterproduct extends Mage_Core_Model_Abstract
{

    public $products = array();

    protected function _construct()
    {
        $this->_init('fruition_customcounter/counterproduct');
    }


    public function addToCart()
    {

        /** @var $cart Mage_Checkout_Model_Cart */
        $cart = Mage::getSingleton('checkout/cart');
        $cart->init();

        $quote = $cart->getQuote();

        $slabItem = $quote->addProduct($this->getSlab(), new Varien_Object(array('qty' => $this->getWidth())));
        $cartParams = new Varien_Object();
        $cartParams->setData('custom_counter', array('label' => 'custom_counter', 'value' => $this->getDiagramId()));

        $this->getSlab()->setData('cart_options', $cartParams);
        $slabItem->addOption(
            array(
                'code' => 'additional_options',
                'value' => serialize($this->getSlab()->getCartOptions()->toArray())
            )
        );

        for ($x = 0; $x < $this->getSinkCount(); $x++) {
            $sink = $this->getData('sink' . ($x + 1));

            /** @var Mage_Sales_Model_Quote_Item $item */
            $item = $quote->addProduct($sink, new Varien_Object(array(
                'qty' => 1,
            )));

            $item->addOption(
                array(
                    'code'  => 'additional_options',
                    'value' => serialize($sink->getCartOptions()->toArray()),
                )
            );

        }

        $polish = $this->getData('polish');
        $polishItem = $quote->addProduct($polish, new Varien_Object(array('qty' => $this->getPolishCount())));
        $polishItem->addOption(
            array(
                'code' => 'additional_options',
                'value' => serialize($polish->getCartOptions()->toArray())
            )
        );

        $backsplash = $this->getData('backsplash');
        if($backsplash){
            $backsplashItem = $quote->addProduct($backsplash, new Varien_Object(array('qty' => $this->getBacksplashCount())));
            $backsplashItem->addOption(
                array(
                    'code' => 'additional_options',
                    'value' => serialize($backsplash->getCartOptions()->toArray())
                )
            );
        }

        for ($x = 0; $x < $this->getFaucetCount(); $x++) {
            $faucet = $this->getData('faucet' . ($x + 1));

            /** @var Mage_Sales_Model_Quote_Item $item */
            $item = $quote->addProduct($faucet, new Varien_Object(array(
                'qty' => 1,
            )));

            $item->addOption(
                array(
                    'code'  => 'additional_options',
                    'value' => serialize($faucet->getCartOptions()->toArray()),
                )
            );

        }

        if($this->getData('remnant') == 1){
            $remnantId = Mage::getModel('catalog/product')->getIdBySku('vanity-countertops-remnant');
            $remnantProduct = Mage::getModel('catalog/product')->load($remnantId);
            $remnantResult =$quote->addProduct($remnantProduct, new Varien_Object(array('qty' => 1)));
            $remnantResult;
        }
        
        $cart->save();

        return $quote->getEntityId();
    }

    public function addPolish(Mage_Catalog_Model_Product $polishModel, array $params)
    {
        $cartParams = new Varien_Object();
        foreach ($params as $name => $value) {
            $cartParams->setData($name, array(
                'label' => $name,
                'value' => $value,
            ));
        }
//        $cartParams->setData($params);

        $polishModel->setData('cart_options', $cartParams);
        $this->setData('polish', $polishModel);

        return $this;
    }


    /**
     * @param $data
     * @var $model
     * @var $matches
     * @var $sinkParams
     * @return $this
     */
    public function processPolish($data)
    {
        if ($data->polish->total > 0) {
            $polishModel = Mage::getModel('catalog/product')->load($data->polish->id);
            $polishParams = array();
            foreach ($data->polish as $paramName => $polish) {
                preg_match('/side(.*)/', $paramName, $matches);
                if (array_key_exists(1, $matches)) {
                    $polishParams[$matches[1]] = $polish;
                }
            }
            $polishParams['custom_counter'] = $this->getDiagramId();
            $this->addPolish($polishModel, $polishParams, 1);
        }

        return $this;
    }

    public function addBacksplash(Mage_Catalog_Model_Product $backsplashModel, array $params, $position)
    {
        $cartParams = new Varien_Object();
        foreach ($params as $name => $value) {
            $cartParams->setData($name, array(
                'label' => $name,
                'value' => $value,
            ));
        }
//        $cartParams->setData($params);

        $backsplashModel->setData('cart_options', $cartParams);
        $this->setData('backsplash', $backsplashModel);

        return $this;
    }


    /**
     * @param $data
     * @var $model
     * @var $matches
     * @var $sinkParams
     * @return $this
     */
    public function processBacksplash($data)
    {
        if ($data->backsplash->total > 0) {
            $backsplashModel = Mage::getModel('catalog/product')->load($data->backsplash->id);
            $backsplashParams = array();
            foreach ($data->backsplash as $paramName => $backsplash) {
                preg_match('/side(.*)/', $paramName, $matches);
                if (array_key_exists(1, $matches)) {
                    $backsplashParams[$matches[1]] = $backsplash;
                }
            }
            $backsplashParams['custom_counter'] = $this->getDiagramId();
            $this->addBacksplash($backsplashModel, $backsplashParams, 1);
        }

        return $this;
    }

    /**
     * @param Mage_Catalog_Model_Product $sinkModel
     * @param array $params
     * @param int $position
     * @return $this
     */
    public function addSink(Mage_Catalog_Model_Product $sinkModel, array $params, $position)
    {
        $cartParams = new Varien_Object();
        foreach ($params as $name => $value) {
            $cartParams->setData($name, array(
                'label' => $name,
                'value' => $value,
            ));
        }
//        $cartParams->setData($params);

        $sinkModel->setData('cart_options', $cartParams);
        $this->setData('sink' . $position, $sinkModel);

        return $this;
    }


    /**
     * @param $data
     * @var $model
     * @var $matches
     * @var $sinkParams
     * @return $this
     */
    public function processSinks($data)
    {
        if ($data->sinks->sinkCount > 0) {
            $sinkModel = Mage::getModel('catalog/product')->load($data->sinks->sinkProductId);
            foreach ($data->sinks as $paramName => $sink) {
                preg_match('/sink(\d)(.*)/', $paramName, $matches);
                if (array_key_exists(1, $matches) && array_key_exists(2, $matches)) {
                    $sinkParams[$matches[1]][$matches[2]] = $sink;
                }

            }

            for ($x = 0; $x < $data->sinks->sinkCount; $x++) {
                $sinkParams[$x + 1]['custom_counter'] = $this->getDiagramId();
                $this->addSink($sinkModel, $sinkParams[$x + 1], $x + 1);
            }
        }

        return $this;
    }

    public function addFaucet(Mage_Catalog_Model_Product $faucetModel, array $params, $position)
    {
        $cartParams = new Varien_Object();
        foreach ($params as $name => $value) {
            $cartParams->setData($name, array(
                'label' => $name,
                'value' => $value,
            ));
        }
//        $cartParams->setData($params);
        if(sizeof($cartParams)>0) {
            $faucetModel->setData('cart_options', $cartParams);
        }
        $this->setData('faucet' . $position, $faucetModel);

        return $this;
    }


    /**
     * @param $data
     * @var $model
     * @var $matches
     * @var $sinkParams
     * @return $this
     */
    public function processFaucets($data)
    {
        if ($this->getFaucetCount() > 0) {
            $faucetModel[1] = Mage::getModel('catalog/product')->load($data->faucets->faucetId1);
            $faucetParams = array();
            if($data->faucets->faucet1 != 0 && $data->faucets->faucet1 != 1 && $data->faucets->faucet1 != 2 && $data->faucets->faucet1 != 3){
                if($data->faucets->faucet1 != 'vn1') {
                    if ($data->faucets->faucet1 == 'vl1') {
                        $faucetParams[1]['Faucet1'] = '45&deg; Left';
                    }
                    if ($data->faucets->faucet1 == 'vc1') {
                        $faucetParams[1]['Faucet1'] = 'Center';
                    }
                    if ($data->faucets->faucet1 == 'vr1') {
                        $faucetParams[1]['Faucet1'] = '45&deg; Right';
                    }
                    $faucetParams[1]['Faucet_1_Distance_From_Vessel_Sink'] = $data->faucets->faucet1distance . '"';
                }
            }
            $faucetParams[1]['custom_counter'] = $this->getDiagramId();
            if($this->getSinkCount()==2){
                $faucetModel[2] = Mage::getModel('catalog/product')->load($data->faucets->faucetId1);
                if($data->faucets->faucet1 != 0 && $data->faucets->faucet1 != 1 && $data->faucets->faucet1 != 2 && $data->faucets->faucet1 != 3){
                    if($data->faucets->faucet2 != 'vn2') {
                        if($data->faucets->faucet2 == 'vl2') {
                            $faucetParams[2]['Faucet2'] = '45&deg; Left';
                        }
                        if($data->faucets->faucet2 == 'vc2') {
                            $faucetParams[2]['Faucet2'] = 'Center';
                        }
                        if($data->faucets->faucet2 == 'vr2') {
                            $faucetParams[2]['Faucet2'] = '45&deg; Right';
                        }
                        $faucetParams[2]['Faucet_2_Distance_From_Vessel_Sink'] = $data->faucets->faucet2distance . '"';
                    }
                }
                $faucetParams[2]['custom_counter'] = $this->getDiagramId();
            }
            for ($x = 0; $x < $this->getFaucetCount(); $x++) {
                $x1 = $x + 1;
                $this->addFaucet($faucetModel[$x+1], $faucetParams[$x+1], $x + 1);
            }
        }

        return $this;
    }

}