<?php
class Cedcoss_Dealers_Model_System_Config_Source_Groups
{
	public function toOptionArray()
	{
		
		$data=array();
		$customer_group = new Mage_Customer_Model_Group();
		$allGroups  = $customer_group->getCollection()->toOptionHash();
		
		foreach($allGroups as $key=>$allGroup){
		   $data[]=array('value'=>$key,'label'=>$allGroup);
		}
	   return $data;

	}

}
?>