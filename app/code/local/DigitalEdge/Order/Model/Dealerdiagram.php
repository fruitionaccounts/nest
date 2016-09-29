<?php
	
class DigitalEdge_Order_Model_Dealerdiagram extends Mage_Core_Model_Abstract{
	protected $_tableName;
	protected $_diagramUrl;
	
	protected function _construct(){
		$this->_init('order/dealerdiagram');
		$this->_tableName = Mage::getSingleton('core/resource')->getTableName('order_dealer_diagram');
	}
	
	protected function sanityCheck() {
		$write = Mage::getSingleton('core/resource')->getConnection('core_write');
		$write->query("CREATE TABLE IF NOT EXISTS ".$this->_tableName." (
		`id` int(11) NOT NULL auto_increment,
			`order_id` int(11) DEFAULT NULL,
			`quote_id` int(11) DEFAULT NULL,
			`product_id` int(11) DEFAULT NULL,
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
		//Now, grab some data from the thing.
		$write->fetchAll("SELECT * FROM ".$this->_tableName);
		$rv=new Varien_Data_Collection_Db($write);
		$rv->getSelect()->from($this->_tableName);
		
		return $rv;
	}
	
	
	public function save() {
		$quote_id = $this->getQuoteId();
		$order_id = $this->getOrderId();
		$product_id = $this->getProductId();
		$diagram_url = $this->getDiagramUrl();
		
		$write = $this->sanityCheck();
		
		$write->beginTransaction();
		$stmt=$write->query("SELECT * FROM {$this->_tableName} WHERE quote_id='{$quote_id}' AND diagram_url='{$diagram_url}'");
		if($stmt->rowCount()==0)
		{
			$write->query("INSERT INTO {$this->_tableName} (quote_id,order_id,product_id,diagram_url) VALUES ('{$quote_id}','{$order_id}', '{$product_id}','{$diagram_url}')");
		}
		else
		{
			$write->query("UPDATE {$this->_tableName} SET order_id='{$order_id}',product_id='{$product_id}' WHERE quote_id='{$quote_id}' AND diagram_url='{$diagram_url}'");
		}
		$write->commit();
	}
	
	public function update($thing)
	{
		$quote_id = $thing->getQuoteId();
		$order_id = $thing->getOrderId();
		$product_id = $thing->getProductId();
		$diagram_url = $thing->getDiagramUrl();
		
		$write = $this->sanityCheck();
		
		$write->beginTransaction();
		$stmt=$write->query("SELECT * FROM {$this->_tableName} WHERE quote_id='{$quote_id}' AND diagram_url='{$diagram_url}'");
		if($stmt->rowCount()==0)
		{
			$write->query("INSERT INTO {$this->_tableName} (quote_id,order_id,product_id,diagram_url) VALUES ('{$quote_id}','{$order_id}', '{$product_id}','{$diagram_url}')");
		}
		else
		{
			$write->query("UPDATE {$this->_tableName} SET order_id='{$order_id}',product_id='{$product_id}' WHERE quote_id='{$quote_id}' AND diagram_url='{$diagram_url}'");
		}
		$write->commit();
	}
	
}
	
	
?>