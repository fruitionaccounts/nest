<?php
	
class Fruition_Countertops_Model_Counterdiagram extends Mage_Core_Model_Abstract{
	protected $_tableName;
	protected $_diagramUrl;
	
	
	protected function _construct(){
		$this->_init('countertops/counterdiagram');
		$this->_tableName = Mage::getSingleton('core/resource')->getTableName('order_counter_diagram');
	}
	
	protected function sanityCheck() {
		$write = Mage::getSingleton('core/resource')->getConnection('core_write');
		$write->query("CREATE TABLE IF NOT EXISTS ".$this->_tableName." (
		`id` int(11) NOT NULL auto_increment,
			`order_id` int(11) DEFAULT NULL,
			`item_sku` varchar(2048) NOT NULL,
			`diagram_url` varchar(2048) NOT NULL,
			PRIMARY KEY  (`id`),
			KEY `order_id` (`order_id`)
			) DEFAULT CHARSET utf8 ENGINE = InnoDB; 
		");
		return $write;
	}
	public function getCollection() {
		$write=$this->sanityCheck();
		//we've covered our six in case the table doesn't already exist.
		//Now, grab some data from the table.
		$write->fetchAll("SELECT * FROM ".$this->_tableName);
		$rv=new Varien_Data_Collection_Db($write);
		$rv->getSelect()->from($this->_tableName);
		
		return $rv;
	}
	
	
	public function save() {
		//to maintain compatibility with Mage_Core_Model_Abstract::save(), we get the order_id by other means
		//(FLJ,12/18/15)
		$order_id=Mage::getSingleton('sales/order')->getIncrementId();
		$diagram_url = '';
		$order = Mage::getModel('sales/order')->loadByIncrementId($order_id);
		$orderItems = $order->getItemsCollection();

		$write = Mage::getSingleton('core/resource')->getConnection('core_write');	
		$write->beginTransaction();
		try
		{
		$check=$write->query("SELECT * FROM {$this->_tableName} WHERE order_id='{$order_id}'");
		if($check->rowCount() == 0){

			foreach ($orderItems as $item){
				$itemsku = $item->getSku();
	            $designlink = 'designLink'.$itemsku;
	            $design_url = Mage::getSingleton('core/session')->getData($designlink);		
	
	            if ($design_url != ''){	
			
					$write->query("INSERT INTO {$this->_tableName} (order_id, item_sku, diagram_url) VALUES ('{$order_id}', '{$itemsku}', '{$design_url}')");		
					//Mage::log('saved');
					//$write->commit();
				}

			}
			
		}
		}
		catch(Exception $ecch)
		{
			Mage::log("HEADS UP:".$ecch->getMessage(),null,"FLJ.log");
		}
		$write->commit();
	}
	
	
}
	
	
?>