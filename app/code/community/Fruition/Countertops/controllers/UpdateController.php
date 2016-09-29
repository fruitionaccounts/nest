<?php
	
include_once("Mage/Core/Controller/Front/Action.php");

class Fruition_Countertops_UpdateController extends Mage_Core_Controller_Front_Action {

	public function indexAction(){

		$check = explode("/",$_GET['a']);
		if ($check[0] != 2478){
			echo "Please login to access this area.";
			die();
		}

		$resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $writeConnection = $resource->getConnection('core_write');

		$product1_id = Mage::getModel("catalog/product")->getIdBySku( 'vanity-countertops' ); 

        $query = "SELECT b.option_id FROM catalog_product_option a, catalog_product_option_title b WHERE a.product_id = $product1_id AND a.option_id = b.option_id AND b.title = 'Stone and Width' ";
        $results = $readConnection->fetchAll($query);
        $p1option = $results[0]['option_id'];


		$product2_id = Mage::getModel("catalog/product")->getIdBySku( 'vanity-countertops-remnant' ); 

        $query = "SELECT b.option_id FROM catalog_product_option a, catalog_product_option_title b WHERE a.product_id = $product2_id AND a.option_id = b.option_id AND b.title = 'Stone and Width' ";
        $results = $readConnection->fetchAll($query);
        $p2option = $results[0]['option_id'];


        $attrSetName = 'Vanity Slabs';
        $attributeSetId = Mage::getModel('eav/entity_attribute_set')
            ->load($attrSetName, 'attribute_set_name')
            ->getAttributeSetId();

          $products = Mage::getModel('catalog/product')
            ->getCollection()
            ->addAttributeToSelect('*')
            ->addFieldToFilter('attribute_set_id', $attributeSetId);


		foreach ($products as $product) {

            $fullname = $product->getName();
            $pid = $product->getId();
            $sku = $product->getSku();

            $flag = $product->getResource()->getAttribute('fruition_flag')->getFrontend()->getValue($product); 
            $sortorder = $product->getResource()->getAttribute('fruition_sort_order')->getFrontend()->getValue($product);          

            $query = "SELECT sku FROM catalog_product_option_type_value WHERE sku = '".$sku."' AND option_id = $p1option";
        	$results = $readConnection->fetchAll($query);
        	$found = $results[0]['sku'];

        	if ($found == '' && $flag == 'Yes'){
        		$query = "INSERT INTO catalog_product_option_type_value (option_id,sku,sort_order) VALUES ($p1option, '".$sku."', '".$sortorder."')";
        		$writeConnection->query($query);

        		$query = "INSERT INTO catalog_product_option_type_value (option_id,sku,sort_order) VALUES ($p2option, '".$sku."', '".$sortorder."')";
        		$writeConnection->query($query);

        		$query = "SELECT option_type_id FROM catalog_product_option_type_value WHERE sku = '".$sku."' AND option_id = $p1option";
	        	$results = $readConnection->fetchAll($query);
	        	$titleid = $results[0]['option_type_id'];

        		$query = "INSERT INTO catalog_product_option_type_title (option_type_id,title) VALUES ($titleid, '".$fullname."')";
        		$writeConnection->query($query);

        		$query = "SELECT option_type_id FROM catalog_product_option_type_value WHERE sku = '".$sku."' AND option_id = $p2option";
	        	$results = $readConnection->fetchAll($query);
	        	$titleid2 = $results[0]['option_type_id'];


        		$query = "INSERT INTO catalog_product_option_type_title (option_type_id,title) VALUES ($titleid2, '".$fullname."')";
        		$writeConnection->query($query);	        	
        		
        	}elseif ($sortorder != '' && $flag == 'Yes'){

        		$query = "UPDATE catalog_product_option_type_value SET sort_order='".$sortorder."' WHERE sku='".$sku."' AND option_id = $p1option";
        		$writeConnection->query($query);
        		$query = "UPDATE catalog_product_option_type_value SET sort_order='".$sortorder."' WHERE sku='".$sku."' AND option_id = $p2option";
        		$writeConnection->query($query);        		
        	}elseif ($flag != 'Yes'){
        		// remove the slab

        		$query = "SELECT option_type_id FROM catalog_product_option_type_value WHERE sku = '".$sku."' AND option_id = $p1option";
	        	$results = $readConnection->fetchAll($query);
	        	$titleid = $results[0]['option_type_id'];

        		$query = "SELECT option_type_id FROM catalog_product_option_type_value WHERE sku = '".$sku."' AND option_id = $p2option";
	        	$results = $readConnection->fetchAll($query);
	        	$titleid2 = $results[0]['option_type_id'];

	        	if ($titleid != ''){
	         		$query = "DELETE FROM catalog_product_option_type_value WHERE sku='".$sku."' AND option_id = $p1option";
	        		$writeConnection->query($query);
	        		$query = "DELETE FROM catalog_product_option_type_value WHERE sku='".$sku."' AND option_id = $p2option";
	        		$writeConnection->query($query);  
	        		$query = "DELETE FROM catalog_product_option_type_title WHERE option_type_id = $titleid)";
	        		$writeConnection->query($query);     
	        		$query = "DELETE FROM catalog_product_option_type_title WHERE option_type_id = $titleid2)";
	        		$writeConnection->query($query);          		   		        		
        		}
        	}
			
        }

        // backsplash

        $query = "SELECT b.option_id FROM catalog_product_option a, catalog_product_option_title b WHERE a.product_id = $product1_id AND a.option_id = b.option_id AND b.title = 'Backsplash' ";
        $results = $readConnection->fetchAll($query);
        $p1option = $results[0]['option_id'];

        $query = "SELECT b.option_id FROM catalog_product_option a, catalog_product_option_title b WHERE a.product_id = $product2_id AND a.option_id = b.option_id AND b.title = 'Backsplash' ";
        $results = $readConnection->fetchAll($query);
        $p2option = $results[0]['option_id'];


        $attrSetName = 'Backsplash';
        $attributeSetId = Mage::getModel('eav/entity_attribute_set')
            ->load($attrSetName, 'attribute_set_name')
            ->getAttributeSetId();

          $products = Mage::getModel('catalog/product')
            ->getCollection()
            ->addAttributeToSelect('*')
            ->addFieldToFilter('attribute_set_id', $attributeSetId);


		foreach ($products as $product) {

            $fullname = $product->getName();
            $sku = $product->getSku();
            $flag = $product->getResource()->getAttribute('fruition_flag')->getFrontend()->getValue($product); 

            $query = "SELECT sku FROM catalog_product_option_type_value WHERE sku = '".$sku."' AND option_id = $p1option";
        	$results = $readConnection->fetchAll($query);
        	$found = $results[0]['sku'];

        	if ($found == '' && $flag == 'Yes'){
        		$query = "INSERT INTO catalog_product_option_type_value (option_id,sku,sort_order) VALUES ($p1option, '".$sku."', 0)";
        		$writeConnection->query($query);

        		$query = "INSERT INTO catalog_product_option_type_value (option_id,sku,sort_order) VALUES ($p2option, '".$sku."', 0)";
        		$writeConnection->query($query);

        		$query = "SELECT option_type_id FROM catalog_product_option_type_value WHERE sku = '".$sku."' AND option_id = $p1option";
	        	$results = $readConnection->fetchAll($query);
	        	$titleid = $results[0]['option_type_id'];

        		$query = "INSERT INTO catalog_product_option_type_title (option_type_id,title) VALUES ($titleid, '".$fullname."')";
        		$writeConnection->query($query);

        		$query = "SELECT option_type_id FROM catalog_product_option_type_value WHERE sku = '".$sku."' AND option_id = $p2option";
	        	$results = $readConnection->fetchAll($query);
	        	$titleid2 = $results[0]['option_type_id'];


        		$query = "INSERT INTO catalog_product_option_type_title (option_type_id,title) VALUES ($titleid2, '".$fullname."')";
        		$writeConnection->query($query);	        	
        		
       		}elseif ($flag != 'Yes'){
        		// remove the slab

        		$query = "SELECT option_type_id FROM catalog_product_option_type_value WHERE sku = '".$sku."' AND option_id = $p1option";
	        	$results = $readConnection->fetchAll($query);
	        	$titleid = $results[0]['option_type_id'];

        		$query = "SELECT option_type_id FROM catalog_product_option_type_value WHERE sku = '".$sku."' AND option_id = $p2option";
	        	$results = $readConnection->fetchAll($query);
	        	$titleid2 = $results[0]['option_type_id'];

	        	if ($titleid != ''){
	         		$query = "DELETE FROM catalog_product_option_type_value WHERE sku='".$sku."' AND option_id = $p1option";
	        		$writeConnection->query($query);
	        		$query = "DELETE FROM catalog_product_option_type_value WHERE sku='".$sku."' AND option_id = $p2option";
	        		$writeConnection->query($query);  
	        		$query = "DELETE FROM catalog_product_option_type_title WHERE option_type_id = $titleid)";
	        		$writeConnection->query($query);     
	        		$query = "DELETE FROM catalog_product_option_type_title WHERE option_type_id = $titleid2)";
	        		$writeConnection->query($query);          		   		        		
        		}
        	}
            

        }

       // sinks

        $query = "SELECT b.option_id FROM catalog_product_option a, catalog_product_option_title b WHERE a.product_id = $product1_id AND a.option_id = b.option_id AND b.title = 'Sinks' ";
        $results = $readConnection->fetchAll($query);
        $p1option = $results[0]['option_id'];

        $query = "SELECT b.option_id FROM catalog_product_option a, catalog_product_option_title b WHERE a.product_id = $product2_id AND a.option_id = b.option_id AND b.title = 'Sinks' ";
        $results = $readConnection->fetchAll($query);
        $p2option = $results[0]['option_id'];


        $attrSetName = 'Sinks';
        $attributeSetId = Mage::getModel('eav/entity_attribute_set')
            ->load($attrSetName, 'attribute_set_name')
            ->getAttributeSetId();

          $products = Mage::getModel('catalog/product')
            ->getCollection()
            ->addAttributeToSelect('*')
            ->addFieldToFilter('attribute_set_id', $attributeSetId);


		foreach ($products as $product) {

            $fullname = $product->getName();
            $sku = $product->getSku();
            $flag = $product->getResource()->getAttribute('fruition_flag')->getFrontend()->getValue($product); 

            $query = "SELECT sku FROM catalog_product_option_type_value WHERE sku = '".$sku."' AND option_id = $p1option";
        	$results = $readConnection->fetchAll($query);
        	$found = $results[0]['sku'];

        	if ($found == '' && $flag == 'Yes'){
        		$query = "INSERT INTO catalog_product_option_type_value (option_id,sku,sort_order) VALUES ($p1option, '".$sku."', 0)";
        		$writeConnection->query($query);

        		$query = "INSERT INTO catalog_product_option_type_value (option_id,sku,sort_order) VALUES ($p2option, '".$sku."', 0)";
        		$writeConnection->query($query);

        		$query = "SELECT option_type_id FROM catalog_product_option_type_value WHERE sku = '".$sku."' AND option_id = $p1option";
	        	$results = $readConnection->fetchAll($query);
	        	$titleid = $results[0]['option_type_id'];

        		$query = "INSERT INTO catalog_product_option_type_title (option_type_id,title) VALUES ($titleid, '".$fullname."')";
        		$writeConnection->query($query);

        		$query = "SELECT option_type_id FROM catalog_product_option_type_value WHERE sku = '".$sku."' AND option_id = $p2option";
	        	$results = $readConnection->fetchAll($query);
	        	$titleid2 = $results[0]['option_type_id'];


        		$query = "INSERT INTO catalog_product_option_type_title (option_type_id,title) VALUES ($titleid2, '".$fullname."')";
        		$writeConnection->query($query);	        	
        		
       		}elseif ($flag != 'Yes'){
        		// remove the slab

        		$query = "SELECT option_type_id FROM catalog_product_option_type_value WHERE sku = '".$sku."' AND option_id = $p1option";
	        	$results = $readConnection->fetchAll($query);
	        	$titleid = $results[0]['option_type_id'];

        		$query = "SELECT option_type_id FROM catalog_product_option_type_value WHERE sku = '".$sku."' AND option_id = $p2option";
	        	$results = $readConnection->fetchAll($query);
	        	$titleid2 = $results[0]['option_type_id'];

	        	if ($titleid != ''){
	         		$query = "DELETE FROM catalog_product_option_type_value WHERE sku='".$sku."' AND option_id = $p1option";
	        		$writeConnection->query($query);
	        		$query = "DELETE FROM catalog_product_option_type_value WHERE sku='".$sku."' AND option_id = $p2option";
	        		$writeConnection->query($query);  
	        		$query = "DELETE FROM catalog_product_option_type_title WHERE option_type_id = $titleid)";
	        		$writeConnection->query($query);     
	        		$query = "DELETE FROM catalog_product_option_type_title WHERE option_type_id = $titleid2)";
	        		$writeConnection->query($query);          		   		        		
        		}
        	}
            

        }



       // Polish

        $query = "SELECT b.option_id FROM catalog_product_option a, catalog_product_option_title b WHERE a.product_id = $product1_id AND a.option_id = b.option_id AND b.title = 'Back Side Polish' ";
        $results = $readConnection->fetchAll($query);
        $p1option = $results[0]['option_id'];

        $query = "SELECT b.option_id FROM catalog_product_option a, catalog_product_option_title b WHERE a.product_id = $product2_id AND a.option_id = b.option_id AND b.title = 'Back Side Polish' ";
        $results = $readConnection->fetchAll($query);
        $p2option = $results[0]['option_id'];

        $query = "SELECT b.option_id FROM catalog_product_option a, catalog_product_option_title b WHERE a.product_id = $product1_id AND a.option_id = b.option_id AND b.title = 'Left Side Polish' ";
        $results = $readConnection->fetchAll($query);
        $p11option = $results[0]['option_id'];

        $query = "SELECT b.option_id FROM catalog_product_option a, catalog_product_option_title b WHERE a.product_id = $product2_id AND a.option_id = b.option_id AND b.title = 'Left Side Polish' ";
        $results = $readConnection->fetchAll($query);
        $p22option = $results[0]['option_id'];

        $query = "SELECT b.option_id FROM catalog_product_option a, catalog_product_option_title b WHERE a.product_id = $product1_id AND a.option_id = b.option_id AND b.title = 'Right Side Polish' ";
        $results = $readConnection->fetchAll($query);
        $p111option = $results[0]['option_id'];

        $query = "SELECT b.option_id FROM catalog_product_option a, catalog_product_option_title b WHERE a.product_id = $product2_id AND a.option_id = b.option_id AND b.title = 'Right Side Polish' ";
        $results = $readConnection->fetchAll($query);
        $p222option = $results[0]['option_id'];


        $attrSetName = 'Edging';
        $attributeSetId = Mage::getModel('eav/entity_attribute_set')
            ->load($attrSetName, 'attribute_set_name')
            ->getAttributeSetId();

          $products = Mage::getModel('catalog/product')
            ->getCollection()
            ->addAttributeToSelect('*')
            ->addFieldToFilter('attribute_set_id', $attributeSetId);


		foreach ($products as $product) {

            $fullname = $product->getName();
            $sku = $product->getSku();
            $flag = $product->getResource()->getAttribute('fruition_flag')->getFrontend()->getValue($product); 

            $query = "SELECT sku FROM catalog_product_option_type_value WHERE sku = '".$sku."' AND option_id = $p1option";
        	$results = $readConnection->fetchAll($query);
        	$found = $results[0]['sku'];

        	if ($found == '' && $flag == 'Yes'){
        		$query = "INSERT INTO catalog_product_option_type_value (option_id,sku,sort_order) VALUES ($p1option, '".$sku."', 0)";
        		$writeConnection->query($query);

        		$query = "INSERT INTO catalog_product_option_type_value (option_id,sku,sort_order) VALUES ($p2option, '".$sku."', 0)";
        		$writeConnection->query($query);

        		$query = "INSERT INTO catalog_product_option_type_value (option_id,sku,sort_order) VALUES ($p11option, '".$sku."', 0)";
        		$writeConnection->query($query);

        		$query = "INSERT INTO catalog_product_option_type_value (option_id,sku,sort_order) VALUES ($p22option, '".$sku."', 0)";
        		$writeConnection->query($query);

        		$query = "INSERT INTO catalog_product_option_type_value (option_id,sku,sort_order) VALUES ($p111option, '".$sku."', 0)";
        		$writeConnection->query($query);

        		$query = "INSERT INTO catalog_product_option_type_value (option_id,sku,sort_order) VALUES ($p222option, '".$sku."', 0)";
        		$writeConnection->query($query);

        		//back
        		$query = "SELECT option_type_id FROM catalog_product_option_type_value WHERE sku = '".$sku."' AND option_id = $p1option";
	        	$results = $readConnection->fetchAll($query);
	        	$titleid = $results[0]['option_type_id'];

        		$query = "INSERT INTO catalog_product_option_type_title (option_type_id,title) VALUES ($titleid, '".$fullname."')";
        		$writeConnection->query($query);

        		$query = "SELECT option_type_id FROM catalog_product_option_type_value WHERE sku = '".$sku."' AND option_id = $p2option";
	        	$results = $readConnection->fetchAll($query);
	        	$titleid2 = $results[0]['option_type_id'];

        		$query = "INSERT INTO catalog_product_option_type_title (option_type_id,title) VALUES ($titleid2, '".$fullname."')";
        		$writeConnection->query($query);	        	

        		//left
        		$query = "SELECT option_type_id FROM catalog_product_option_type_value WHERE sku = '".$sku."' AND option_id = $p11option";
	        	$results = $readConnection->fetchAll($query);
	        	$titleid = $results[0]['option_type_id'];

        		$query = "INSERT INTO catalog_product_option_type_title (option_type_id,title) VALUES ($titleid, '".$fullname."')";
        		$writeConnection->query($query);

        		$query = "SELECT option_type_id FROM catalog_product_option_type_value WHERE sku = '".$sku."' AND option_id = $p22option";
	        	$results = $readConnection->fetchAll($query);
	        	$titleid2 = $results[0]['option_type_id'];

        		$query = "INSERT INTO catalog_product_option_type_title (option_type_id,title) VALUES ($titleid2, '".$fullname."')";
        		$writeConnection->query($query);        		

        		//right
        		$query = "SELECT option_type_id FROM catalog_product_option_type_value WHERE sku = '".$sku."' AND option_id = $p111option";
	        	$results = $readConnection->fetchAll($query);
	        	$titleid = $results[0]['option_type_id'];

        		$query = "INSERT INTO catalog_product_option_type_title (option_type_id,title) VALUES ($titleid, '".$fullname."')";
        		$writeConnection->query($query);

        		$query = "SELECT option_type_id FROM catalog_product_option_type_value WHERE sku = '".$sku."' AND option_id = $p222option";
	        	$results = $readConnection->fetchAll($query);
	        	$titleid2 = $results[0]['option_type_id'];

        		$query = "INSERT INTO catalog_product_option_type_title (option_type_id,title) VALUES ($titleid2, '".$fullname."')";
        		$writeConnection->query($query); 

       		}elseif ($flag != 'Yes'){
        		// remove the slab

        		$query = "SELECT option_type_id FROM catalog_product_option_type_value WHERE sku = '".$sku."' AND option_id = $p1option";
	        	$results = $readConnection->fetchAll($query);
	        	$titleid = $results[0]['option_type_id'];

        		$query = "SELECT option_type_id FROM catalog_product_option_type_value WHERE sku = '".$sku."' AND option_id = $p2option";
	        	$results = $readConnection->fetchAll($query);
	        	$titleid2 = $results[0]['option_type_id'];

        		$query = "SELECT option_type_id FROM catalog_product_option_type_value WHERE sku = '".$sku."' AND option_id = $p11option";
	        	$results = $readConnection->fetchAll($query);
	        	$titleid11 = $results[0]['option_type_id'];

        		$query = "SELECT option_type_id FROM catalog_product_option_type_value WHERE sku = '".$sku."' AND option_id = $p22option";
	        	$results = $readConnection->fetchAll($query);
	        	$titleid22 = $results[0]['option_type_id'];


        		$query = "SELECT option_type_id FROM catalog_product_option_type_value WHERE sku = '".$sku."' AND option_id = $p111option";
	        	$results = $readConnection->fetchAll($query);
	        	$titleid111 = $results[0]['option_type_id'];

        		$query = "SELECT option_type_id FROM catalog_product_option_type_value WHERE sku = '".$sku."' AND option_id = $p222option";
	        	$results = $readConnection->fetchAll($query);
	        	$titleid222 = $results[0]['option_type_id'];

	        	if ($titleid != ''){
	         		$query = "DELETE FROM catalog_product_option_type_value WHERE sku='".$sku."' AND option_id = $p1option";
	        		$writeConnection->query($query);
	        		$query = "DELETE FROM catalog_product_option_type_value WHERE sku='".$sku."' AND option_id = $p2option";
	        		$writeConnection->query($query);  
	        		$query = "DELETE FROM catalog_product_option_type_title WHERE option_type_id = $titleid)";
	        		$writeConnection->query($query);     
	        		$query = "DELETE FROM catalog_product_option_type_title WHERE option_type_id = $titleid2)";
	        		$writeConnection->query($query);          		   		        		
        		}

	        	if ($titleid11 != ''){
	         		$query = "DELETE FROM catalog_product_option_type_value WHERE sku='".$sku."' AND option_id = $p11option";
	        		$writeConnection->query($query);
	        		$query = "DELETE FROM catalog_product_option_type_value WHERE sku='".$sku."' AND option_id = $p22option";
	        		$writeConnection->query($query);  
	        		$query = "DELETE FROM catalog_product_option_type_title WHERE option_type_id = $titleid11)";
	        		$writeConnection->query($query);     
	        		$query = "DELETE FROM catalog_product_option_type_title WHERE option_type_id = $titleid22)";
	        		$writeConnection->query($query);          		   		        		
        		}

	        	if ($titleid111 != ''){
	         		$query = "DELETE FROM catalog_product_option_type_value WHERE sku='".$sku."' AND option_id = $p111option";
	        		$writeConnection->query($query);
	        		$query = "DELETE FROM catalog_product_option_type_value WHERE sku='".$sku."' AND option_id = $p222option";
	        		$writeConnection->query($query);  
	        		$query = "DELETE FROM catalog_product_option_type_title WHERE option_type_id = $titleid111)";
	        		$writeConnection->query($query);     
	        		$query = "DELETE FROM catalog_product_option_type_title WHERE option_type_id = $titleid222)";
	        		$writeConnection->query($query);          		   		        		
        		}

        	}
            

        }


        echo "Vanity Countertop updated with latest products.";
        $baseurl = Mage::getBaseUrl();
        echo "<br><br><a href='".$baseurl."/nestappe_admin'>Return to Admin</a>";
	}



}