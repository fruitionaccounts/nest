<?php

class FarApp_Connector_Model_Export extends Mage_ImportExport_Model_Export
{
    public function processProductExport($filter = null)
    {
        $this->setEntity(Mage_Catalog_Model_Product::ENTITY);
        $this->_getEntityAdapter()->setParameters($this->getFilters());
        return $this->export();

    }

    public function processCustomerExport($filter = null)
    {
        $this->setEntity('customer');
        $this->_getEntityAdapter()->setParameters($this->getFilters());
        return $this->export();
    }
    public function processOrderExport($filter = null)
    {
        $this->setEntity('order');
        $entityAdapter = Mage::getModel('farapp_connector/export_entity_order');
        $entityAdapter->setIncludePayment($this->getIncludePayment());
        $entityAdapter->setIncludeItems($this->getIncludeItems());
        $entityAdapter->setIncludeShipment($this->getIncludeShipment());
        $entityAdapter->setIncludeAddresses($this->getIncludeAddresses());
        $this->setEntityAdapter($entityAdapter);
        return $this->export();
    }
    public function export()
    {
        $this->addLogComment(Mage::helper('importexport')->__('Begin export of %s', $this->getEntity()));
        //var_dump(get_class($this->_getEntityAdapter()));
        $result = $this->_getEntityAdapter()
            ->setWriter($this->_getWriter())
            ->export();

        return $result;
    }

    public function getIncludeAddresses()
    {
        return $this->getData('include_addresses');
    }
    public function getIncludeShipment()
    {
        return $this->getData('include_shipment');
    }
    public function getIncludePayment()
    {
        return $this->getData('include_payment');
    }
    public function getFilters()
    {
        return (array)$this->getData('export_filter');
    }
    public function getIncludeItems()
    {
        return $this->getData('include_items');
    }

    protected function _getWriter()
    {

        if (!$this->_writer) {
            $this->_writer = Mage::getModel('farapp_connector/export_adapter_array');
                if (! $this->_writer instanceof FarApp_Connector_Model_Export_Adapter_Abstract) {
                    Mage::throwException(
                        Mage::helper('importexport')->__('Adapter object must be an instance of %s', 'FarApp_Connector_Model_Export_Adapter_Abstract')
                    );
                }
            }
        return $this->_writer;
    }

    /**
     * @param Mage_ImportExport_Model_Import_Entity_Abstract $entityAdapter
     *
     * @return void
     */
    public function setEntityAdapter($entityAdapter)
    {
        $this->_entityAdapter = $entityAdapter;
    }

    /**
     * @return Mage_ImportExport_Model_Import_Entity_Abstract
     */
    public function getEntityAdapter()
    {
        return $this->_entityAdapter;
    }
}
