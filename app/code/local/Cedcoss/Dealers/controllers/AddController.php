<?php
 /**
 * CEDCOSS Technologies
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category    Cedcoss
 * @package     Cedcoss_Dealers
 * @copyright   Copyright (c) 2013 CEDCOSS Technologies Pvt. Ltd (http://www.cedcoss.com)
 * @author		ASHEESH SINGH <asheeshsingh@cedcoss.com>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Cedcoss_Dealers_AddController extends Mage_Core_Controller_Front_Action 
{
	/**
     * Add multiple product to shopping cart action
     */
	public function indexAction()
		{
			/* die(var_dump($_POST['check'])."<br><br>".var_dump($_POST['qty'])); */
			$session= Mage::getSingleton('checkout/session');
			/* var_dump($_POST);die;  */
			foreach($session->getQuote()->getAllItems() as $item)
			{
			   $current_cart['product_id'][$item->getProductId()]= $item->getQty();
			}
			$cart = Mage::getModel("checkout/cart");
			$succ=array();
			
			if(!empty($_POST['qty'])) {
				/* for($i=0;$i<count($_POST['check']);$i++) */
					foreach($_POST['qty'] as $key=>$val)
						foreach($val as $id=>$qty){
							if($qty > 0) {
								$_product = Mage::getModel('catalog/product')->load($id);
								if($_product->getTypeId()=='bundle')
								{
									$quantity = $_product->getStockItem()->getQty();
									    $params = array(
														'product' =>$id,
														'related_product' => '',
														'qty' =>"{$qty}",
														'bundle_option' => $_POST['bundle_option_'.$key],
														 'bundle_option_qty' =>$_POST['bundle_option_qty_'.$key],
													);
												
													$cart->addProduct($_product, $params);
													
								}
								elseif($_product->getTypeId()=='grouped')
								{
									 $super_group=$_POST['super_group_'.$key];
									foreach($super_group as $key=>$value)
									{
										$super_group[$key]=$value*$qty;
									}
									 $params = array('super_group' => $super_group);
									 $cart->addProduct($_product, $params);
									
								}
								
								else
								{	
									if(isset($_POST['config'][$key]))
									{
										
										$parentIds = Mage::getResourceSingleton('catalog/product_type_configurable')
											->getParentIdsByChild($id);

										$parent = Mage::getModel('catalog/product')->load(current($parentIds));

										if ($parent->getTypeId() === Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
											$paramsSuperAttribute = array('super_attribute' => array());

											foreach ($parent->getTypeInstance(true)->getConfigurableAttributes($parent) as $attribute) {
												$paramsSuperAttribute['super_attribute'][$attribute->getProductAttribute()->getAttributeId()] =
													(int) $_product->getData($attribute->getProductAttribute()->getAttributeCode());
											}
										}

										$params = array('qty' => $qty);
										$cart->addProduct(isset($parent) ? $parent : $_product, array_merge($params, $paramsSuperAttribute));
									}
									else
									{
										$quantity = $_product->getStockItem()->getQty();
										$manageStock = $_product->getStockItem()->getManageStock();
										$match_quantity = $qty + $current_cart['product_id'][$_product->getId()];
										if($quantity >= $match_quantity || $manageStock == 0) {
											$cart->addProduct($id, $qty);
											
											$succ[] = $_product->getName();
										} else {
											$err[] = $_product->getName();
										}
									}
								}
							}
				}
			}
			
			$error_message = implode(', ',$err);
			$success_message = implode(', ',$succ);
			if(count($succ) > 1) {
				$succ_msg = "products";
			} else {
				$succ_msg = "product";
			}
			if(count($err) > 1) {
				$err_msg = "products";
			} else {
				$err_msg = "product";
			}
			
			$cart->save(); 
			$cid=Mage::getSingleton('customer/session')->getCustomerId();
			foreach($_FILES as $key=>$file)
			{
				if(stripos($key,"diagram_".$cid)===FALSE)
				{
					continue;
				}
				if(!empty($_FILES[$key]['name']))
				{
					$uploads_dir =Mage::getBaseDir('media').'/dealer_diagrams/';
					if(!is_dir($uploads_dir))
					{
						mkdir($uploads_dir,0777,true); //recursive mkdir
					}
					$new_file_name="upload_".$cid."_".$_FILES[$key]['name'];
				    if ( !move_uploaded_file($_FILES[$key]['tmp_name'], $uploads_dir . $new_file_name) )
				    {
						die("Could not put file in $uploads_dir");
					}
					$file_url=Mage::getUrl('media'.'/dealer_diagrams/').$new_file_name;
					//now tuck this url away
					
					$diag=Mage::getModel('order/dealerdiagram')
						->setQuoteId($cart->getQuote()->getId())
						->setDiagramUrl($file_url)
							;
					$diag->save();
				}
			}
			
			if(!empty($err)) {
				Mage::getSingleton('core/session')->addError('Inventory is not sufficiant for '.$error_message.' '.$err_msg.' !');
			} elseif(!empty($succ)) {
				Mage::getSingleton('core/session')->addSuccess($success_message.' '.$succ_msg.' are added to your cart !');
			}
			$this->_redirect("checkout/cart");
		}
		public function configureProductToAddAction()
		{
			// Prepare data
			$productId  = (int) $this->getRequest()->getParam('id');
			$inputid  = (int) $this->getRequest()->getParam('inputid');

			/* $configureResult = new Varien_Object();
			$configureResult->setOk(true);
			$configureResult->setProductId($productId);
			
			$configureResult->setCurrentStoreId(Mage::app()->getStore()->getStoreId());
			$configureResult->setCurrentCustomerId(1);

			
			$helper = Mage::helper('adminhtml/catalog_product_composite');
		//	$helper->renderConfigureResult($this, $configureResult); */
			
            $product = Mage::getModel('catalog/product')
                ->setStoreId($currentStoreId)
                ->load($productId);
			$_attributes=$product->getTypeInstance(true)->getConfigurableAttributes($product);
			/* $temp = new Mage_Catalog_Block_Product_View_Type_Configurable();
			$temp->setData('product', $product);  
			 print_r(json_decode($temp->getJsonConfig()) ); */
				/* $productAttributeOptions = $product->getTypeInstance(true)->getConfigurableAttributesAsArray($product);
				$attributeOptions = array();
			foreach ($productAttributeOptions as $productAttribute) {
				foreach ($productAttribute['values'] as $attribute) {
					$attributeOptions[$productAttribute['label']][$attribute['value_index']] = $attribute['store_label'];
				}
			}
			print_r($attributeOptions); */
			$temp = new Mage_Catalog_Block_Product_View_Type_Configurable();
			$temp->setData('product', $product);  
			$priceOption=new Mage_Catalog_Block_Product_View();
			 Mage::register('product', $product);
			$html='';
			if ($product->isSaleable() && count($_attributes)):
				$html.='
				
				<div id="product-options-wrapper"><dl>';
				foreach($_attributes as $_attribute):
				$class=$_attribute->decoratedIsLast?"last":'';
					$html.='
					
					<dt><label class="required"><em>*</em>'.$_attribute->getLabel().'</label></dt><br/>';
					$html.='<dd class="'.$class.'">
						<div class="input-box">';
							$html.='<select  onchange = "setproduct('.$inputid.','.$productId.',spConfig.getIdOfSelectedProduct())"  name="super_attribute['.$_attribute->getAttributeId().']" id="attribute'.$_attribute->getAttributeId().'" class="required-entry super-attribute-select">
								<option>Choose an Option...</option>
							  </select>
						  </div>
					</dd>';
				 endforeach; 
				$html.='</dl></div>
				<script type="text/javascript">
				 
					 if (window.productConfigure) {
						config.containerId = window.productConfigure.blockFormFields.id;
						if (window.productConfigure.restorePhase) {
							config.inputsInitialized = true;
						}
					}
					var spConfig = new Product.Config('.$temp->getJsonConfig().');
					decorateGeneric($$("#product-options-wrapper dl"), ["last"]);
					 var optionsPrice = new Product.OptionsPrice('.$priceOption->getJsonConfig().');
					 
					 	spConfig.getIdOfSelectedProduct = function () {
					var existingProducts = new Object();
					for (var i = this.settings.length - 1; i >= 0; i--) {
						var selected = this.settings[i].options[this.settings[i].selectedIndex];
						if (selected.config) {
							for (var iproducts = 0; iproducts < selected.config.products.length; iproducts++) {
								var usedAsKey = selected.config.products[iproducts] + "";
								if (existingProducts[usedAsKey] == undefined) {
									existingProducts[usedAsKey] = 1;
								} else {
									existingProducts[usedAsKey] = existingProducts[usedAsKey] + 1;
								}
							}
						}
					}
					for (var keyValue in existingProducts) {
						for (var keyValueInner in existingProducts) {
							if (Number(existingProducts[keyValueInner]) < Number(existingProducts[keyValue])) {
								delete existingProducts[keyValueInner];
							}
						}
					}
					var sizeOfExistingProducts = 0;
					var currentSimpleProductId = "";
					for (var keyValue in existingProducts) {
						currentSimpleProductId = keyValue;
						sizeOfExistingProducts = sizeOfExistingProducts + 1
					}
					if (sizeOfExistingProducts == 1) {
						/* alert("Selected product is: " + currentSimpleProductId) */
						return currentSimpleProductId;
					}
				}
				function setproduct(current_i,id,selectedid)
				{
					if(selectedid!="undefined")
					{	jQuery("#loading-mask").css("display","");
						/* alert(current_i+" "+id+" "+selectedid); */
						jQuery("#id_"+current_i).attr("name","qty["+current_i+"]["+selectedid+"]");
						jQuery.post("'.Mage::getUrl("dealers/add/configureProductUpdatePrice").'",{id:selectedid},function(data){
							jQuery("#price_"+current_i).children().children().children().html(data);
							jQuery("#check_"+current_i).attr("checked","true");
							jQuery("#id_"+current_i).removeAttr("disabled");
							jQuery("#dealer-checkout").append("<input type=\"hidden\" name=\"config['.$inputid.']\" value=\"1\">");
							jQuery("#id_"+current_i).val("1");
							jQuery("#loading-mask").css("display","none");
						});
					}
				}
				</script>';
			endif;
			echo $html;
			
		}
		public function bundlegetjsAction()
		{
			$productId  = (int) $this->getRequest()->getParam('id');
			$product = Mage::getModel('catalog/product')
                ->setStoreId($currentStoreId)
                ->load($productId);
			$temp = new Mage_Bundle_Block_Catalog_Product_View_Type_Bundle();
			$temp->setData('product', $product);  
			$priceOption=new Mage_Catalog_Block_Product_View();
			 Mage::register('product', $product);
			$htm='<script type="text/javascript">
			//<![CDATA[
			var optionsPrice = new Product.OptionsPrice('.$priceOption->getJsonConfig().');
				var bundle = new Product.Bundle('.$temp->getJsonConfig().');
				
			//]]>
			</script>';
			echo $htm;
		}
		public function bundleProductToAddAction()
		{
			// Prepare data
			$productId  = (int) $this->getRequest()->getParam('id');
			$inputid  = (int) $this->getRequest()->getParam('inputid');
			$product = Mage::getModel('catalog/product')
                ->setStoreId($currentStoreId)
                ->load($productId);
			$currentblock=$this->getLayout()->createBlock('bundle/catalog_product_view_type_bundle')->setProduct($product);
			$currentblock->addRenderer('select','bundle/catalog_product_view_type_bundle_option_select');
			$currentblock->addRenderer('multi','bundle/catalog_product_view_type_bundle_option_multi');
			$currentblock->addRenderer('radio','bundle/catalog_product_view_type_bundle_option_radio');
			$currentblock->addRenderer('checkbox','bundle/catalog_product_view_type_bundle_option_checkbox');
			
			$_options = Mage::helper('core')->decorateArray($currentblock->getOptions());
			$html='';
			 if ($product->isSaleable()):
				if (count($_options)):
				 	$html.='<dl>';
					foreach ($_options as $_option):
						if (!$_option->getSelections()):
							continue;
						endif;
						$html.=$currentblock->getOptionHtml($_option);
					 endforeach;
					$html.='</dl>';
				 else:
					$html.='<p> '.$this->__('No options of this product are available.').' </p>';
				 endif;
			endif;
			echo $html;
			
		}
		public function bundleformAction()
		{
			$data=$this->getRequest()->getParams();
			$htm='<div id="bundle_form_detail_'.$data['currenti'].'" style="display:none;">';
			$i=0;
			foreach($data['bundle_option'] as $key=>$value)
			{
			
				if($data['bundle_option_qty'][$key]>0)
				{	$i++;
					$htm.='<input type="hidden" name="bundle_option_'.$data['currenti'].'['.$key.']" value="'.$value.'"/>';
					$htm.='<input type="hidden" name="bundle_option_qty_'.$data['currenti'].'['.$key.']" value="'.$data['bundle_option_qty'][$key].'"/>';
				}
			}
			$htm.='</div>';
			if($i!=0)
			{
				echo $htm;
			}
		}
		public function groupedformAction()
		{
			$data=$this->getRequest()->getParams();
			$htm='<div id="grouped_form_detail_'.$data['currenti'].'" style="display:none;">';
			$i=0;
			$price=0;
			foreach($data['super_group'] as $key=>$value)
			{
				
				if($value>0)
				{	$i++;
					$product = Mage::getModel('catalog/product')->load($key);
					$price+=($product->getFinalPrice())*$value;
					$htm.='<input type="hidden" name="super_group_'.$data['currenti'].'['.$key.']" value="'.$value.'"/>';
				}
				
			}
			
			$htm.='
			<div class="price">'.Mage::helper('core')->currency($price, true, false).'</div>
			</div>';
			if($i!=0)
			{
				echo $htm;
			}
		}
		public function configureProductUpdatePriceAction()
		{
			$productId  = (int) $this->getRequest()->getParam('id');
			$product = Mage::getModel('catalog/product')->load($productId);
			 echo Mage::helper('core')->currency($product->getFinalPrice(), true, false);
		}
}
