<?php

/**
 * Created by PhpStorm.
 * User: scotttaylor
 * Date: 2/2/16
 * Time: 1:55 PM
 */
class Fruition_Customcounter_IndexController extends Mage_Core_Controller_Front_Action
{

    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function productsAction()
    {

        $productsCollection = Mage::getModel('catalog/product')->getCollection()
                                  ->addAttributeToSelect('*')
                                  ->addAttributeToFilter('status', array('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED))
                                  ->addAttributeToFilter('attribute_set_id',
                                      array(
                                          'in' =>
                                              array(
                                                  Mage::helper('fruition_customcounter')->getAttributeSetId('Vanity Slabs'),
                                                  Mage::helper('fruition_customcounter')->getAttributeSetId('Edging'),
                                                  Mage::helper('fruition_customcounter')->getAttributeSetId('Backsplash'),
                                                  Mage::helper('fruition_customcounter')->getAttributeSetId('Sinks'),
                                                  Mage::helper('fruition_customcounter')->getAttributeSetId('Faucets'),
                                              ),
                                      )
                                  );
        
        $faucetsCollection = Mage::getModel('catalog/product')->getCollection()
                                 ->addAttributeToSelect('*')
                                 ->addAttributeToFilter('status', array('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED))
                                 ->addAttributeToFilter('attribute_set_id', Mage::helper('fruition_customcounter')->getAttributeSetId('Faucets'))
                                 ->addAttributeToFilter('name', array('LIKE' => 'Labor - %'));

        $_mergeCollectionIds = array_merge($productsCollection->getAllIds(), $faucetsCollection->getAllIds());

        $productsCollection = Mage::getResourceModel('catalog/product_collection')
                                  ->addAttributeToSelect('*')
                                  ->joinField('qty',
                                      'cataloginventory/stock_item',
                                      'qty',
                                      'product_id=entity_id',
                                      '{{table}}.stock_id=1',
                                      'left')
                                  ->addAttributeToSelect('qty')
                                  ->addFieldToFilter('entity_id', array('in' => $_mergeCollectionIds))
                                  ->load();

        $attributeSets = array(
            'attribute_sets' => array(
                Mage::helper('fruition_customcounter')->getAttributeSetId('Vanity Slabs') => 'Vanity Slabs',
                Mage::helper('fruition_customcounter')->getAttributeSetId('Edging')       => 'Edging',
                Mage::helper('fruition_customcounter')->getAttributeSetId('Backsplash')   => 'Backsplash',
                Mage::helper('fruition_customcounter')->getAttributeSetId('Sinks')        => 'Sinks',
                Mage::helper('fruition_customcounter')->getAttributeSetId('Faucets')      => 'Faucets',
            ),
        );

        $prepareAttributes = array(
            'thickness',
            'remnant_min_width',
            'remnant_max_width',
            'remnant_min_depth',
            'remnant_max_depth',
            'stone_min_width',
            'stone_max_width',
            'stone_min_depth',
            'stone_max_depth',
            'price_group',
            'stone',
            'color',
            'stone',
            'veins',
            'tone',
            'min_width_1',
            'min_width_2',
            'min_depth_sink',
            'sink_width',
            'sink_depth',
            'sink_type',
            'sink_material',
        );
        $preparedAttributes = array();
        foreach ($prepareAttributes as $attributeCode) {
            $preparedAttributes[$attributeCode] = Mage::helper('fruition_customcounter')->prepareAttribute($attributeCode);
        }
        $preparedAttributes = array('prepared_attributes' => $preparedAttributes);

        $productsCollection = array_merge($productsCollection->toArray(), $attributeSets, $preparedAttributes);

        echo Mage::helper('core')->jsonEncode($productsCollection);

    }

    public function productupsellsAction()
    {

        $product_id = file_get_contents('php://input');
        $product_id = json_decode($product_id);
        echo Mage::helper('core')->jsonEncode(Mage::getModel('catalog/product')->load($product_id->product_id)->getUpSellProductIds());

    }

    public function orderSampleAction(){
        $data = Mage::helper('core')->jsonDecode(file_get_contents('php://input'), Zend_Json::TYPE_OBJECT);
        $dataSku = str_replace('-slab', '-sample', $data->sku);
        $sampleProductId = Mage::getModel('catalog/product')->getIdBySku($dataSku);
        $sampleProduct = Mage::getModel('catalog/product')->load($sampleProductId);
        $cart = Mage::getSingleton('checkout/cart')->init();
        $quote = $cart->getQuote();
        $quote->addProduct($sampleProduct, new Varien_Object(array('qty' => 1)));
        $cart->save();
    }

    public function addProductsAction()
    {

        $data = Mage::helper('core')->jsonDecode(file_get_contents('php://input'), Zend_Json::TYPE_OBJECT);
        $model = Mage::getModel('fruition_customcounter/counterproduct');
        $slab = Mage::getModel('catalog/product')->load($data->slab);
        $model->setDiagramId($data->diagramId);
        $model->setSlab($slab);
        $model->setWidth($data->width);
        $model->setDepth($data->depth);

        $model->setPolishCount($data->polish->total);
        $model->setBacksplashCount($data->backsplash->total);
        $model->setSinkCount($data->sinks->sinkCount);
        $model->setRemnant($data->isRemnant);

        if($data->sinks->sinkCount==1){
            if($data->faucets->faucet1!='vn1' && $data->faucets->faucet1!='0'){
                $model->setFaucetCount(1);
            }
        }
        if($data->sinks->sinkCount==2 && $data->faucets->faucet1 != '0'){
            if($data->faucets->faucet1!='vn1') {
                if ($data->faucets->faucet2 != 'vn2' && $data->faucets->faucet2 != '0') {
                    $model->setFaucetCount(2);
                }
            }else if($data->faucets->faucet2!='vn2'){
                $model->setFaucetCount(1);
            }
        }

        $model->processPolish($data);
        $model->processBacksplash($data);
        $model->processSinks($data);
        $model->processFaucets($data);
        $model->setRawData($data);

        $model->setSerial(serialize($model));
        if(!property_exists($data, 'isDiagram'))
        {
            $quoteId = $model->addToCart();
            $model->setQuoteId($quoteId);
        }

        $model->setCreatedAt(Mage::getModel('core/date')->date('Y-m-d H:i:s'));
        $model->setUpdatedAt(Mage::getModel('core/date')->date('Y-m-d H:i:s'));
        $model->save();
        
    }

    public function diagramAction(){

        $this->loadLayout();
        $this->renderLayout();

    }

    public function printAction(){

        $this->loadLayout();
        $this->renderLayout();

    }

    public function getDiagramAction(){

        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('fruition_customcounter/counterproduct')->load($id, 'diagram_id');
        $modelData = $model->getSerial();
        $data = unserialize($modelData);

        echo Mage::helper('core')->jsonEncode($data);

    }

}