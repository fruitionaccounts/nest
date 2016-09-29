<?php


include_once("Mage/Core/Controller/Front/Action.php");

class Fruition_Countertops_OrderController extends Mage_Core_Controller_Front_Action
{

    public function indexAction()
    {

        $this->loadLayout();

        $head = Mage::app()->getLayout()->getBlock('head');
        $head->addItem('skin_css', 'css/countertops.css');
        $head->addItem('skin_js', 'js/angular.js');
        $head->addItem('skin_js', 'js/angular-sanitize.min.js');
        $head->addItem('skin_js', 'js/jquery.numeric.min.js');

        $this->renderLayout();
    }


    public function sinksAction()
    {
        $last = '';
        $style = '';
        $width = '';
        $sink1maxheight = '';
        $sink2maxheight = '';

        /*
         * Collect all Details from Angular HTTP Request.
         */
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        @$slabwidth = $request->slabwidth;
        @$slabdepth = $request->slabdepth;
        $output1 = '';
        $output2 = '';
        $last2 = '';


        $attrSetName = 'Sinks';
        $attributeSetId = Mage::getModel('eav/entity_attribute_set')
            ->load($attrSetName, 'attribute_set_name')
            ->getAttributeSetId();


        $products = Mage::getModel('catalog/product')
            ->getCollection()
            ->addAttributeToSelect('*')
            ->addFieldToFilter('attribute_set_id', $attributeSetId)
            ->addFieldToFilter(array(
                array('attribute' => 'sink_shape', 'neq' => '')
            ));


        $cot = 0;

        foreach ($products as $product) {

            $fullname = $product->getName();

            $_sku = $product->getSku();
            $productid = $product->getId();
            $sinkShape = $product->getResource()->getAttribute('sink_shape')->getFrontend()->getValue($product);
            $sinkWidth = $product->getResource()->getAttribute('sink_width')->getFrontend()->getValue($product);
            $sinkHeight = $product->getResource()->getAttribute('sink_height')->getFrontend()->getValue($product);
            $sinkType = $product->getResource()->getAttribute('sink_type')->getFrontend()->getValue($product);
            $sinkMaterial = $product->getResource()->getAttribute('sink_material')->getFrontend()->getValue($product);
            
            $fullproduct = Mage::getModel('catalog/product')->load($productid);
            $sink1min = $fullproduct->getResource()->getAttribute('min_width_1')->getFrontend()->getValue($fullproduct);
            $sink2min = $fullproduct->getResource()->getAttribute('min_width_2')->getFrontend()->getValue($fullproduct);

            $productimage = Mage::getModel('catalog/product')->load($product->getId());
            $imagebase = Mage::getBaseUrl() . "media/catalog/product";
            $icheck = 0;
            foreach ($productimage->getMediaGallery('images') as $img) {
                if ($img['disabled'] == 1 && $img['label'] == 'countertop') {
                    $imageurl = $img['file'];
                } elseif ($icheck != 1) {
                    $firstimage = $img['file'];
                    $icheck = 1;
                }
            }
            $cot++;

            // 14.75" x 2 = 29.5 (2 bowls) + 8 (eitherside) + 8 (between) = 45.5
            $sink1maxheight = $sinkHeight + 8;
            $sink1max = $sinkWidth + 8;

            if ($sinkShape == 'Vessel' && $_sku == 'LAB-CUT-CUS') {
                $last .= '<div class="sinkholder sinkmove' . $_sku . '" style="' . $style . '"><div class="help"><div class="help' . $cot . ' sinkbubble sinkhelp-width" style="display:none;' . $width . '"><img src="' . $imagebase . $firstimage . '">' . $fullname . '<br>Width: ' . $sinkWidth . '<br> Height: ' . $sinkHeight . '<br> Type: ' . $sinkType . '<br> Material: ' . $sinkMaterial . '<br> Min Width 1 Sink: ' . $sink1min . '<br> Min Width 2 Sinks: ' . $sink2min . '</div></div>';
                $last .= '<div onmouseover="sinkHoverShow(\'' . $cot . '\')" onmouseout="sinkHoverHide(\'' . $cot . '\')" class="sink-image' . $_sku . ' sink-div" style="' . $style . '" sinkwidth="' . $sinkWidth . '" sinksize="' . $sinkShape . '" sinksku="' . $_sku . '" sinkname="' . $fullname . '" onclick="chooseSink(\'' . $_sku . '\',\'' . $sinkShape . '\',\'center\')" ><img class="sinkimage" src="' . $imagebase . $imageurl . '"></div></div>';

            }

            if ($sinkShape == 'Vessel' && $_sku == 'LAB-CUT-CUS' && $slabwidth >= 31.5) {

                $last2 = $last;
            }

            // 1 sink
            if ($slabwidth >= $sink1min && $sink1maxheight <= $slabdepth) {

                $output1 .= '<div class="sinkholder sinkmove' . $_sku . '" style="' . $style . '"><div class="help"><div class="help' . $cot . ' sinkbubble sinkhelp-width" style="display:none;' . $width . '"><img src="' . $imagebase . $firstimage . '">' . $fullname . '<br>Width: ' . $sinkWidth . '<br> Height: ' . $sinkHeight . '<br> Type: ' . $sinkType . '<br> Material: ' . $sinkMaterial . '<br> Min Width 1 Sink: ' . $sink1min . '<br> Min Width 2 Sinks: ' . $sink2min . '</div></div>';
                $output1 .= '<div onmouseover="sinkHoverShow(\'' . $cot . '\')" onmouseout="sinkHoverHide(\'' . $cot . '\')" class="sink-image' . $_sku . ' sink-div" style="' . $style . '" sinkwidth="' . $sinkWidth . '" sinksize="' . $sinkShape . '" sinksku="' . $_sku . '" sinkname="' . $fullname . '" onclick="chooseSink(\'' . $_sku . '\',\'' . $sinkShape . '\',\'center\')" ><img class="sinkimage" src="' . $imagebase . $imageurl . '"></div></div>';

            }

            // 2 sinks
            $sink2max = ($sinkWidth * 2) + 16;
            if ($slabwidth >= $sink2min && $sink1maxheight <= $slabdepth) {

                $output2 .= '<div class="sinkholder sinkmove' . $_sku . '" style="' . $style . '"><div class="help"><div class="help' . $cot . ' sinkbubble sinkhelp-width" style="display:none;' . $width . '"><img src="' . $imagebase . $firstimage . '">' . $fullname . '<br>Width: ' . $sinkWidth . '<br> Height: ' . $sinkHeight . '<br> Type: ' . $sinkType . '<br> Material: ' . $sinkMaterial . '<br> Min Width 1 Sink: ' . $sink1min . '<br> Min Width 2 Sinks: ' . $sink2min . '</div></div>';
                $output2 .= '<div onmouseover="sinkHoverShow(\'' . $cot . '\')" onmouseout="sinkHoverHide(\'' . $cot . '\')" class="sink-image' . $_sku . ' sink-div" style="' . $style . '" sinkwidth="' . $sinkWidth . '" sinksize="' . $sinkShape . '" sinksku="' . $_sku . '" sinkname="' . $fullname . '" onclick="chooseSink(\'' . $_sku . '\',\'' . $sinkShape . '\',\'center\')" ><img class="sinkimage" src="' . $imagebase . $imageurl . '"></div></div>';

            }

        }

        $finaloutput = '<div class="1sinkok" style="display:none">' . $output1 . $last . '</div><div class="2sinkok" style="display:none">' . $output2 . $last2 . '</div>';
        $this->getResponse()->setBody($finaloutput);
    }

    public function stoneAction()
    {
        /*
         * Collect all Details from Angular HTTP Request.
         */
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        @$width = $request->width;
        @$height = $request->height;
        @$thickness = $request->thickness;
        @$slabwidth = $request->slabwidth;
        @$slabdepth = $request->slabdepth;

        $output = $this->getSlabs($thickness, $slabwidth, $slabdepth);
        $this->getResponse()->setBody($output);
    }

    public function stonesAction()
    {
        /*
         * Collect all Details from Angular HTTP Request.
         */
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        @$width = $request->width;
        @$height = $request->height;
        @$thickness = $request->thickness;
        @$slabwidth = $request->slabwidth;
        @$slabdepth = $request->slabdepth;

        $blank = '';
        $output = $this->getAllSlabs($thickness, $slabwidth, $slabdepth,$blank,$blank,$blank,$blank,$blank,$blank,$blank,$blank);

        $this->getResponse()->setBody($output);
    }

    public function checksplashAction()
    {
        /*
        * Collect all Details from Angular HTTP Request.
        */
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        @$slabsku = $request->slabsku;
        @$slabwidth = $request->slabwidth;
        @$slabheight = $request->slabheight;
        @$backsplashback = $request->backsplashback;
        @$backsplashleft = $request->backsplashleft;
        @$backsplashright = $request->backsplashright;
        @$side = $request->side;

        $totalqty = 0;

        if ($backsplashback != 0){
           $totalqty = $totalqty+$slabwidth;  
        }

        if ($backsplashleft != 0){
           $totalqty = $totalqty+$slabheight;  
        }

        if ($backsplashright != 0){
           $totalqty = $totalqty+$slabheight;  
        }        


        $backsplashsku = str_replace('slab', 'splash', $slabsku);

        $_product = Mage::getModel('catalog/product')->loadByAttribute('sku', $backsplashsku);
        $productId = $_product->getId();
        $stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($_product);
        $edgetype = Mage::getResourceModel('catalog/product')->getAttributeRawValue($productId, 'stone_max_width', 1);
        $backorder = $stock->getBackorders();
        $qty =  $stock->getQty();

        if ($totalqty > $qty && $backorder != 1){
            // we need to void this selection
            echo 'void,'.$side;
            die();
        }

        $productModel = Mage::getModel('catalog/product');
        $attr = $productModel->getResource()->getAttribute('stone_max_width');
        $backsplashlimit = $attr->getSource()->getOptionText($edgetype);

        if ($slabwidth > $backsplashlimit && $backsplashlimit > 0) {
            $this->getResponse()->setBody('1');
        } else {
            $this->getResponse()->setBody('');
        }
    }

    public function edgingAction()
    {

        $edgethickness = '';
        $first = '';
        $second = '';

        /*
        * Collect all Details from Angular HTTP Request.
        */
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        @$width = $request->width;
        @$height = $request->height;
        @$thickness = $request->thickness;
        @$slabwidth = $request->slabwidth;
        @$slabdepth = $request->slabdepth;

        $cot = 0;
        $num = 0;
        $_resource = Mage::getSingleton('catalog/product')->getResource();

        //Fetch attribute set id by attribute set name
        $attrSetName = 'Edging';
        $attributeSetId = Mage::getModel('eav/entity_attribute_set')
            ->load($attrSetName, 'attribute_set_name')
            ->getAttributeSetId();

        //Load product model collecttion filtered by attribute set id
        $products = Mage::getModel('catalog/product')
            ->getCollection()
            ->addAttributeToSelect(
                array(
                    'name',
                    'description',
                    'edge_thinkness',
                    'sku',
                    'price',
                    'image',
                    'small_image',
                    'thumbnail'
                )
            )
            ->addFieldToFilter('attribute_set_id', $attributeSetId);

        //process your product collection as per your bussiness logic
        $images = '<div class="edgeconstrain">';

        foreach ($products as $p) {
            $productId = $p->getId();

            $pthick = Mage::getResourceModel('catalog/product')->getAttributeRawValue($productId, 'edge_thickness', 1);
            $edgetype = Mage::getResourceModel('catalog/product')->getAttributeRawValue($productId, 'edge_type', 1);

            $productModel = Mage::getModel('catalog/product');
            $attr = $productModel->getResource()->getAttribute('edge_type');
            $edge_label = $attr->getSource()->getOptionText($edgetype);


            if ($p->getId() != 1279 && $p->getId() != 1280) {
                $cot++;
                $num++;
                if ($num == 1) {
                    $images .= '<div class="edge-holder">';
                    $addclass = "edgeone";
                } elseif ($num == 2) {
                    $addclass = "edgetwo";
                } else {
                    $addclass = "edgethree";
                }
                $name = $p->getDescription();
                $price = round($p->getPrice(), 2);
                if ($price < 1) {
                    $price = "free";
                } else {
                    $price = "$" . $price;
                }

                if ($thickness == 2 && $pthick == 170 || $thickness == 3) {

                    $imagesearch = "default-prod-image_13.gif";
                    $imageurl = Mage::helper('catalog/image')->init($p, 'image');
                    $checkimage = strpos($imageurl, $imagesearch);

                    $productMediaConfig = Mage::getModel('catalog/product_media_config');
                    $smallImageUrl = $productMediaConfig->getMediaUrl($p->getSmallImage());
                    $thumbImageUrl  = $productMediaConfig->getMediaUrl($p->getThumbnail());

                    if ($checkimage === false) {
                        $width = "top:-280px";
                    } else {
                        $width = "top:-215px";
                    }

                    $help = $edgethickness . '<div class="help"><div class="help' . $cot . ' ' . $p->getSku() . ' edgebubble edgehelp-width" style="display:none;' . $width . '" edgename="' . $name . '"><img src="' . $imageurl . '">' . $edge_label . '<br>' . $price . '/per inch</div></div>';

                    if ($price == "free") {
                        $first .= '<div class="edge-image ei' . $cot . ' ' . $addclass . '" onmouseover="edgeHoverShow(\'' . $cot . '\')" onmouseout="edgeHoverHide(\'' . $cot . '\')">' . $help . '<img class="edge-under' . $cot . '" onmouseover="edgeover(' . $cot . ')" src="' . $smallImageUrl . '" width="50"><img onclick="edgeselect(' . $cot . ',\'' . $name . '\',\'' . $p->getSku() . '\')" onmouseout="edgeout(' . $cot . ')" class="edge-over' . $cot . '" src="' . $thumbImageUrl . '" width="50" style="display:none"></div>';
                    } else {
                        $second .= '<div class="edge-image ei' . $cot . ' ' . $addclass . '" onmouseover="edgeHoverShow(\'' . $cot . '\')" onmouseout="edgeHoverHide(\'' . $cot . '\')">' . $help . '<img class="edge-under' . $cot . '" onmouseover="edgeover(' . $cot . ')" src="' . $smallImageUrl . '" width="50"><img onclick="edgeselect(' . $cot . ',\'' . $name . '\',\'' . $p->getSku() . '\')" onmouseout="edgeout(' . $cot . ')" class="edge-over' . $cot . '" src="' . $thumbImageUrl . '" width="50" style="display:none"></div>';
                    }

                    if ($num == 3) {
                        $second .= '</div><br>';
                        $num = 0;
                    }
                }
            }
        }

        $second .= '</div></div>';
        $images .= $first . $second;

        $this->getResponse()->setBody($images);
    }

    public function sampleAction()
    {
        /*
         * Collect all Details from Angular HTTP Request.
         */
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        @$sku = $request->sku;
        $samplesku = str_replace('slab', 'sample', $sku);
        $currentproduct = Mage::getModel('catalog/product')->loadByAttribute('sku', $samplesku);
        $stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($currentproduct)->getIsInStock();
        if ($stock == 1) {
            $product_id = $currentproduct->getId();
            $product = Mage::getModel('catalog/product')->load($product_id);
            $cart = Mage::getModel('checkout/cart');
            $cart->init();

            $params = array(
                'qty' => 1
            );


            try {
                $cart->addProduct($product, $params);
                Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
                $cart->save();

                $output = '<div class="shiptext">Your order has been add to your cart.</div><br clear="all">';
                $this->getResponse()->setBody($output);
            } catch (Exception $ex) {
                $output = $ex->getMessage();
                $this->getResponse()->setBody($output);
            }
        } else {
            $output = 'stop';
            $this->getResponse()->setBody($output);
        }

    }

    public function addtocartAction()
    {   

    	$edgeid2 = '';
    	$backsplashid = '';
    	$edgeid7 = '';
    	$edgeid8 = '';
    	$edge2 = '';
    	$edge7 = '';
    	$edge8 = '';
    	$totalbacksplash = '';
    	$holeid = '';
    	$faucetid = '';
    	$sinkid = '';
    	$faucetnum = '';
    	$holetotal = '';
    	$totalsinks = '';
        $allskus = '';
        $backsplashsku = '';
        $holecutsku = '';
        $updateqty = '';
        $fsku = '';
        $newsku = '';
        $remnant = '';
        $currentsku = '';
        $pskuleft = '';
        $pskuright = '';
        $pskuback = '';
        $oldsku = '';
        $id1 = '';
        $id2 = '';
        $id3 = '';
        $id4 = '';
        $id5 = '';
        $id6 = '';
        $id7 = '';
        $id8 = '';
        $id9 = '';
        $edge9 = '';
        $edgeid9 = '';

         /*
         * Collect all Details from Angular HTTP Request.
         */
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        @$slab = $request->slab;
        @$sink = $request->sink;
        @$faucet = $request->faucet;
        @$polish = $request->polish;
        @$backsplash = $request->backsplash;
        @$edging = $request->edging;
        @$edgename = $request->edgename;
        @$vessels = $request->vessels;
        @$sinknames = $request->sinknames;
        @$oldsku = $request->oldsku;

        $staticBlock = Mage::getModel('cms/block')->load('counter_shipping_text')->getContent();
        $slabs = explode(',', $slab);
        $sinks = explode(',', $sink);
        $polishs = explode(',', $polish);
        $faucets = explode(',', $faucet);
        $backsplashs = explode(',', $backsplash);
        $sku = $slabs[2];
        $slabwidth = str_replace('\'', '', $slabs[0]);
        $slabheight = str_replace('\'', '', $slabs[1]);
        $vesselsitems = explode(",", $vessels);

        // make sure we remove old item from cart if user edit the product
        
        if ($oldsku != ''){
            
            $session = Mage::getSingleton('checkout/session');
            $cart = Mage::getModel('checkout/cart');
            
            $quote = $session->getQuote();
            $cartItems = $cart->getItems();
                foreach ($cartItems as $item) {
                   
                        //Mage::log($item->getProduct()->getSku().' - '.$alloldproducts[$cot]);
                        if ($item->getProduct()->getSku() == $oldsku) {
                            //$quote->removeItem($item->getSku())->save();
                            $item->isDeleted(true);
                        }
                    
                }
            $quote->save();
            
             Mage::getSingleton('core/session')->setData('minprice'.$oldsku,'');
             Mage::getSingleton('core/session')->setData('designLink'.$oldsku,'');
             Mage::getSingleton('core/session')->setData('productLink'.$oldsku,'');            
        }


        $remnant = Mage::getSingleton('core/session')->getRemnant();
   
        if ($remnant == 'Yes'){
            $currentsku = 'vanity-countertops-remnant';
            $product = $this->getProduct($currentsku); 
        }else{
             $currentsku = 'vanity-countertops';
            $product = $this->getProduct($currentsku); 
        }
        $product_id = $product->getId();
        $product = Mage::getModel('catalog/product')->load($product_id);

        // build array and QTY
        $options = array();
        $params = array();
        

        foreach ($product->getOptions() as $_option) {

            if ($_option->getTitle() == 'Stone and Width') {
                $option1 = $_option->getValues();
                $id1 = $_option->getId();
            }

            if ($_option->getTitle() == 'Left Side Polish') {
                $option2 = $_option->getValues();
                $id2 = $_option->getId();
            }

            if ($_option->getTitle() == 'Faucet Holes') {
                $option3 = $_option->getValues();
                $id3 = $_option->getId();
            }

            if ($_option->getTitle() == 'Sink Holes') {
                $option4 = $_option->getValues();
                $id4 = $_option->getId();
            }

            if ($_option->getTitle() == 'Sinks') {
                $option5 = $_option->getValues();
                $id5 = $_option->getId();
            }

            if ($_option->getTitle() == 'Backsplash') {
                $option6 = $_option->getValues();
                $id6 = $_option->getId();
            }

            if ($_option->getTitle() == 'Right Side Polish') {
                $option7 = $_option->getValues();
                $id7 = $_option->getId();
            }

            if ($_option->getTitle() == 'Back Side Polish') {
                $option8 = $_option->getValues();
                $id8 = $_option->getId();
            }

            if ($_option->getTitle() == 'Front Side Polish') {
                $option9 = $_option->getValues();
                $id9 = $_option->getId();
            } 

        }


        // slab price
        foreach ($option1 as $v) {
            if ($v->getSku() == $sku) {
                $slabid = $v->getId();
                $slabsku = $v->getSku();
                $fullproduct = Mage::getModel('catalog/product')->load($slabid);

                break;
            }
        }

        // backsplash price
        if ($backsplashs[0] == 1 || $backsplashs[1] == 1 || $backsplashs[2] == 1) {
            $backsplashsku = str_replace('slab', 'splash', $sku);
            foreach ($option6 as $v6) {

                if ($v6->getSku() == $backsplashsku) {
                    $backsplashid = $v6->getId();
                }
            }

            $totalbacksplash = 0;
            if ($backsplashs[0] == 1) {
                $totalbacksplash += $slabheight;
            }
            if ($backsplashs[1] == 1) {
                $totalbacksplash += $slabwidth;
            }
            if ($backsplashs[2] == 1) {
                $totalbacksplash += $slabheight;
            }
        }
       

        // polish price
        if ($polish) {

            // leftside
            if ($polishs[0] == 1) {
                foreach ($option2 as $v2) {

                    if ($v2->getSku() == $edging) {
                        $edgeid2 = $v2->getId();
                        $edge2 = $slabheight;
                        $edgeid = $edgeid2;
                        $pskuleft = $edging;
                    }
                }
            }

            // rightside
            if ($polishs[2] == 1) {
                foreach ($option7 as $v7) {

                    if ($v7->getSku() == $edging) {
                        $edgeid7 = $v7->getId();
                        $edge7 = $slabheight;
                        $edgeid = $edgeid7;
                        $pskuright = $edging;
                    }
                }
            }

            // backside
           if ($polishs[1] == 1) {
                foreach ($option8 as $v8) {
                    if ($v8->getSku() == $edging) {
                        $edgeid8 = $v8->getId();
                        $edge8 = $slabwidth;
                        $edgeid = $edgeid8;
                        $pskuback = $edging;
                    }
                }
            }

        }

        
        
        foreach ($option9 as $v9) {
            if ($v9->getSku() == $edging) {
                $edgeid9 = $v9->getId();
                $edge9 = $slabwidth;
                $edgeid = $edgeid9;
                $pskufront = $edging;
            }
        }


        // sink price
        if ($sinks[1] != 'LAB-CUT-CUS' && $sinks[1] != '') {
            foreach ($option5 as $v5) {

                if ($v5->getSku() == $sinks[1]) {
                    $sinkid = $v5->getId();
                }
            }

            if ($sink[0] == 2) {
                $totalsinks = 2;
            } else {
                $totalsinks = 1;
            }

            // hole price
            $holecutsku = explode('-', $sinks[1]);
            $holecutsku[2] = "LAB-CUT-".$holecutsku[2];
            foreach ($option4 as $v4) {
                $thissku = explode('-', $v4->getSku());
                $thissku[2] = "LAB-CUT-".$thissku[2];
                
                if ($thissku[2] == $holecutsku[2]) {
                    $holeid = $v4->getId();
                }
                //Mage::log($thissku[2] .'=='. $holecutsku[2].' hole:'.$holeid);
            }
            if ($sink[0] == 2) {
                $holetotal = 2;
            } else {
                $holetotal = 1;
            }

        } else {
            
            // get vessels hole size
            if ($sinks[1] == 'LAB-CUT-CUS' && $vesselsitems[0] > 1.75) {
                foreach ($option4 as $v4) {

                    if ($v4->getSku() == $sinks[1]) {
                        $holeid = $v4->getId();
                        break;
                    }
                }
            } elseif ($sinks[1] == 'LAB-CUT-CUS' && $vesselsitems[0] <= 1.75) {
                $sinks[1] = 'LAB-CUT-VES';
                foreach ($option4 as $v4) {

                    if ($v4->getSku() == 'LAB-CUT-VES') {
                        $holeid = $v4->getId();
                       break; 
                    }
                }
            }

            if ($holeid != '') {
                if ($sink[0] == 2) {
                    $holetotal = 2;
                } else {
                    $holetotal = 1;
                }
            }

        }

       

        // faucet price
        if ($faucets[0] && $faucets[0] != 'holes-none') {

            // we could update the faucets section to be dynamic so we don't have to do this
            if ($faucets[0] == 'holes-one') {
                $fsku = 'LAB-CUT-S';
            } elseif ($faucets[0] == 'holes-two') {
                $fsku = 'LAB-CUT-4';
            } else {
                $fsku = 'LAB-CUT-8';
            }


            foreach ($option3 as $v3) {
                if ($v3->getSku() == $fsku) {
                    $faucetid = $v3->getId();
                }
            }
            if ($sink[0] == 2) {
                $faucetnum = 2;
            } else {
                $faucetnum = 1;
            }
        }

        // save all product skus so we can create our unique diagram for each product
        if (isset($sinks[1])){$sinksku = $sinks[1]; }else{$sinksku = '';}
        if (isset($holecutsku[2])){$holesku = $holecutsku[2]; }else{$holesku = '';}

  
        $allskus = $currentsku.','.$slabsku;
        if ($backsplashsku){
           $allskus .= ','.$backsplashsku;
        } 
        if ($sinksku){
           $allskus .= ','.$sinksku;
        }
        if ($holecutsku){
            $allskus .= ','.$holecutsku[2];
        }
        if ($fsku){
           $allskus .= ','.$fsku;
        }          
        if ($pskuleft){
           $allskus .= ','.$pskuleft;
        }  
        if ($pskuright){
           $allskus .= ','.$pskuright;
        }  
        if ($pskuback){
           $allskus .= ','.$pskuback;
        }                 
 
        if ($pskufront){
           $allskus .= ','.$pskufront;
        }  

        //Mage::log($allskus);           


        $this->productSkus($allskus);

        // load design codes for this order
        $design = Mage::getSingleton('core/session')->getDesignLink();
        $diagram = Mage::getSingleton('core/session')->getProductLink();
        $minprice = Mage::getSingleton('core/session')->getMinProduct();
        Mage::getSingleton('core/session')->unsMinProduct();
        $this->linkCreate('minprice',$minprice,$allskus);
        $this->linkCreate('designLink',$design,$allskus);
        $this->linkCreate('productLink',$diagram,$allskus);

        // load new product into cart
        $cart = Mage::getModel('checkout/cart');
        $cart->init();



        $params = array(
            'product'                  => $product_id,
            'qty'                      => 1,
            'options'                  => array(
                $id1 => $slabid,
                $id2 => $edgeid2,
                $id3 => $faucetid,
                $id4 => $holeid,
                $id5 => $sinkid,
                $id6 => $backsplashid,
                $id7 => $edgeid7,
                $id8 => $edgeid8,
                $id9 => $edgeid9
            ),
            'options_' . $id1 . '_qty' => $slabwidth,
            'options_' . $id2 . '_qty' => $edge2,
            'options_' . $id3 . '_qty' => $faucetnum,
            'options_' . $id4 . '_qty' => $holetotal,
            'options_' . $id5 . '_qty' => $totalsinks,
            'options_' . $id6 . '_qty' => $totalbacksplash,
            'options_' . $id7 . '_qty' => $edge7,
            'options_' . $id8 . '_qty' => $edge8,
            'options_' . $id9 . '_qty' => $edge9,
            'custom_options' => '<a href="' . $diagram . '">View Quote</a>'

        );

        //print_r($params);die();

        try {
            $cart->addProduct($product, $params);
            Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
            $cart->save();
            $output = '1';
            //$output = '<div class="shiptext">Your order has been add to your cart.</div><br clear="all">';
            $this->getResponse()->setBody($output);
        } catch (Exception $ex) { 
            $output = '2,'.$ex->getMessage();
            $this->getResponse()->setBody($output);
        }

        // can we call this function in AOC? public function insertNewOrderItem


    }

    public function totalsAction()
    {
    	$sinkprice = '';
    	$holeprice = '';
    	$sinktype = '';
    	$vessel1id = '';
    	$vessel2id = '';
    	$faucethole = '';
    	$warnme = '';

        /*
         * Collect all Details from Angular HTTP Request.
         */
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        @$slab = $request->slab;
        @$slabname = $request->slabname;
        @$thickness = $request->thickness;
        @$sink = $request->sink;
        @$faucet = $request->faucet;
        @$faucetdes = $request->faucetdes;
        @$polish = $request->polish;
        @$backsplash = $request->backsplash;
        @$edging = $request->edging;
        @$edgename = $request->edgename;
        @$vessels = $request->vessels;
        @$sinknames = $request->sinknames;
        @$vfaucet = $request->vfaucet;


        $staticBlock = Mage::getModel('cms/block')->load('counter_shipping_text')->getContent();
        $slabs = explode(',', $slab);
        $sinks = explode(',', $sink);
        $polishs = explode(',', $polish);
        $vfaucets = explode(',', $vfaucet);
        $backsplashs = explode(',', $backsplash);
        $vesselsitems = explode(",", $vessels);
        $sku = $slabs[2];
        $slabwidth = str_replace('\'', '', $slabs[0]);
        $slabheight = str_replace('\'', '', $slabs[1]);


        $_product = $this->getProduct('vanity-countertops');

        foreach ($_product->getOptions() as $_option) {

            if ($_option->getTitle() == 'Stone and Width') {
                $option1 = $_option->getValues();
            }

            if ($_option->getTitle() == 'Left Side Polish') {
                $option2 = $_option->getValues();
            }

            if ($_option->getTitle() == 'Faucet Holes') {
                $option3 = $_option->getValues();
            }

            if ($_option->getTitle() == 'Sink Holes') {
                $option4 = $_option->getValues();
            }

            if ($_option->getTitle() == 'Sinks') {
                $option5 = $_option->getValues();
            }

            if ($_option->getTitle() == 'Backsplash') {
                $option6 = $_option->getValues();
            }

            if ($_option->getTitle() == 'Right Side Polish') {
                $option7 = $_option->getValues();
            }

            if ($_option->getTitle() == 'Back Side Polish') {
                $option8 = $_option->getValues();
            }

            if ($_option->getTitle() == 'Front Side Polish') {
                $option9 = $_option->getValues();
            }            

        }

        // slab price
        foreach ($option1 as $v) {
            if ($v->getSku() == $sku) {
                $slabprice = $v->getPrice();
                $currentproduct = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);
                $slabname = $currentproduct->getResource()->getAttribute('color')->getFrontend()->getValue($currentproduct);
                $stock = $currentproduct->getResource()->getAttribute('stock_type')->getFrontend()->getValue($currentproduct);
                $remnant = $currentproduct->getResource()->getAttribute('stone_remnant_flag')->getFrontend()->getValue($currentproduct);
                if ($stock == 'In Stock Stone'){
                    $remnant = 'Yes';
                }
                Mage::getSingleton('core/session')->setRemnant($remnant); 
                break;
            }
        }

        $subproduct = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);
        $minprice = $subproduct->getResource()->getAttribute('min_fee')->getFrontend()->getValue($subproduct);


        $price = $slabwidth * $slabprice;
        $warn = 0;
        if ($minprice > $price) {
            $price = $minprice;
            $warn = 1;
        }

        
        // backsplash price
        if ($backsplash) {
            $backsplashsku = str_replace('slab', 'splash', $sku);
            foreach ($option6 as $v6) {

                if ($v6->getSku() == $backsplashsku) {
                    $backsplashprice = $v6->getPrice();
                    break;
                }
            }
            if ($backsplashs[0] == 1) {
                $price = $price + ($backsplashprice * $slabheight);
                
            }
            if ($backsplashs[1] == 1) {
                $price = $price + ($backsplashprice * $slabwidth);
                
            }
            if ($backsplashs[2] == 1) {
                $price = $price + ($backsplashprice * $slabheight);
                
            }
        }

        

        // polish price
        if ($polish) {


            if ($polishs[0] == 1) {
                foreach ($option2 as $v2) {

                    if ($v2->getSku() == $edging) {
                        $price = $price + $v2->getPrice();
                        break;
                    }
                }
            }

            if ($polishs[1] == 1) {
                foreach ($option8 as $v8) {

                    if ($v8->getSku() == $edging) {
                        $price = $price + $v8->getPrice();
                        break;

                    }
                }
            }

            if ($polishs[2] == 1) {
                foreach ($option7 as $v7) {

                    if ($v7->getSku() == $edging) {
                        $price = $price + $v7->getPrice();
                        break;
                    }
                }
            }

            
            foreach ($option9 as $v9) {

                if ($v9->getSku() == $edging) {
                    $price = $price + $v9->getPrice();
                    break;
                }
            }
            

        }

        $edgingid = Mage::getModel("catalog/product")->getIdBySku($edging);
        $edgetype = Mage::getResourceModel('catalog/product')->getAttributeRawValue($edgingid, 'edge_type', 1);
        $productModel = Mage::getModel('catalog/product');
        $attr = $productModel->getResource()->getAttribute('edge_type');
        $edgenameone = $attr->getSource()->getOptionText($edgetype);


        // sink price
        if ($sinks && $sinks[1] != 'LAB-CUT-CUS') {
            foreach ($option5 as $v5) {

                if ($v5->getSku() == $sinks[1]) {
                    $sinkprice = $v5->getPrice();
                    break;
                }
            }

            if ($sink[0] == 2) {
                $price = $price + ($sinkprice * 2);
            } else {
                $price = $price + $sinkprice;
            }

            // hole price
            $holecutsku = explode('-', $sinks[1]);
            foreach ($option4 as $v4) {
                $thissku = explode('-', $v4->getSku());

                if (!isset($thissku[2])){ $thissku[2] = ''; }
                if (!isset($holecutsku[2])){ $holecutsku[2] = ''; }

                if ($thissku[2] == $holecutsku[2]) {
                    $holeprice = $v4->getPrice();
                    break;
                }
            }
            if ($sink[0] == 2) {
                $price = $price + ($holeprice * 2);
            } else {
                $price = $price + $holeprice;
            }


        } else {

            // get vessels hole size
            if ($sinks[1] == 'LAB-CUT-CUS' && $vesselsitems[0] > 1.75) {
                foreach ($option4 as $v4) {

                    if ($v4->getSku() == $sinks[1]) {
                        $holeprice = $v4->getPrice();
                        break;
                    }
                }
            } elseif ($sinks[1] == 'LAB-CUT-CUS' && $vesselsitems[0] <= 1.75) {
                foreach ($option4 as $v4) {

                    if ($v4->getSku() == 'LAB-CUT-VES') {
                        $holeprice = $v4->getPrice();
                        break;
                    }
                }
            }

            if ($sink[0] == 2) {
                $price = $price + ($holeprice * 2);
            } else {
                $price = $price + $holeprice;
            }
        }


        // faucet price

        if ($faucet) {

            // we could update the faucets section to be dynamic so we don't have to do this
            if ($faucet == 'holes-one') {
                $fsku = 'LAB-CUT-S';
            } elseif ($faucet == 'holes-two') {
                $fsku = 'LAB-CUT-4';
            } else {
                $fsku = 'LAB-CUT-8';
            }

            foreach ($option3 as $v3) {
                if ($v3->getSku() == $fsku) {
                    $faucetprice = $v3->getPrice();
                    break;
                }
            }
            if ($sink[0] == 2) {
                $price = $price + ($faucetprice * 2);
            } else {
                $price = $price + $faucetprice;
            }
        }


        $baseurl = Mage::getBaseUrl();
        $design = $baseurl . 'countertops/design/';
        $diagram = $baseurl . 'countertops/order/';
        $designurl = htmlentities('?w=' . $slabwidth . '&h=' . $slabheight . '&t=' . $thickness . '&s=' . $slabs[2] . '&sn=' . $slabname . '&p=' . $polish . '&e=' . $edgenameone . '&en=' . $edgename . '&eid=' . $edging . '&b=' . $backsplash . '&sinknames=' . $sinknames . '&sinksku=' . $sinks[1] . '&sink=' . $sinks[2] . '&sinknum=' . $sinks[0] . '&sink1inch=' . $sinks[3] . '&sink2inch=' . $sinks[4] . '&sinksize=' . $sinks[5] . '&sinktype=' . $sinktype . '&v1id=' . $vessel1id . '&v1holeleft=' . $vesselsitems[3] . '&v1holeback=' . $vesselsitems[2] . '&v1holeinch=' . $vesselsitems[0] . '&v2id=' . $vessel2id . '&v2holeleft=' . $vesselsitems[5] . '&v2holeback=' . $vesselsitems[4] . '&v2holeinch=' . $vesselsitems[1] . '&f=' . $faucet . '&fd=' . $faucetdes . '&fh=' . $faucethole . '&f1degree=' . $vfaucets[0] . '&f1dis=' . $vfaucets[1] . '&f2degree=' . $vfaucets[2] . '&f2dis=' . $vfaucets[3]);

        $design .= $designurl;
        $diagram .= $designurl;

        $this->tempLinks($design, $diagram);

        if ($warn == 1) {
            Mage::getSingleton('core/session')->setMinProduct($price);
            //$warnme = '<div class="warning">Your stone choice has an extra cost of $'.$addprice.'</div>';
        }
        $price = number_format((float)$price, 2, '.', '');
        $output = '<div class="shiptext"><div class="designurl" style="display:none">' . $designurl . '</div>' . $warnme . '<div class="totalprice">$' . $price . '</div>' . $staticBlock . '</div><br clear="all">';

        $this->getResponse()->setBody($output);


    }

    // this loads the filtered slabs
    public function filterAction()
    {
        /*
         * Collect all Details from Angular HTTP Request.
         */
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        @$width = $request->width;
        @$height = $request->height;
        @$thickness = $request->thickness;
        @$price = $request->price;
        @$color = $request->color;
        @$type = $request->type;
        @$veins = $request->veins;
        @$tones = $request->tones;
        @$remenants = $request->remenants;
        @$slabwidth = $request->slabwidth;
        @$slabdepth = $request->slabdepth;
        @$page = $request->page;
        @$page2 = $request->page2;
        @$previouspage = $request->previouspage;
        @$previouspage2 = $request->previouspage2;


        // if you want to turn on remnant display filtering, comment out the line below
        $remnants = 1;
        $blank = '';
        $output = $this->getAllSlabs($thickness, $slabwidth, $slabdepth, $price, $color, $type, $veins, $tones, $remnants,$blank,$blank);
        $this->getResponse()->setBody($output);
    }

    // this loads just the filters
    public function filtersAction()
    {
        /*
         * Collect all Details from Angular HTTP Request.
         */
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        @$width = $request->width;
        @$height = $request->height;
        @$thickness = $request->thickness;
        @$price = $request->price;
        @$color = $request->color;
        @$type = $request->type;
        @$veins = $request->veins;
        @$tones = $request->tones;
        @$remenants = $request->remenants;
        @$slabwidth = $request->slabwidth;
        @$slabdepth = $request->slabdepth;

        // if you want to turn on remnant display filtering, comment out the line below
        $remnants = 1;

        $output = $this->getFilters($thickness, $slabwidth, $slabdepth, $price, $color, $type, $veins, $tones, $remnants);
        $this->getResponse()->setBody($output);
    }

    public function pagetopAction()
    {

        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        @$width = $request->width;
        @$height = $request->height;
        @$thickness = $request->thickness;
        @$price = $request->price;
        @$color = $request->color;
        @$type = $request->type;
        @$veins = $request->veins;
        @$tones = $request->tones;
        @$slabwidth = $request->slabwidth;
        @$slabdepth = $request->slabdepth;
        @$page = $request->page;
        @$page2 = $request->page2;

        $output = $this->getTopSlabs($thickness, $slabwidth, $slabdepth, $price, $color, $type, $veins, $tones, $remnants, $page, $page2);
        $this->getResponse()->setBody($output);
    }

    public function pagebottomAction()
    {

        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        @$width = $request->width;
        @$height = $request->height;
        @$thickness = $request->thickness;
        @$price = $request->price;
        @$color = $request->color;
        @$type = $request->type;
        @$veins = $request->veins;
        @$tones = $request->tones;
        @$slabwidth = $request->slabwidth;
        @$slabdepth = $request->slabdepth;
        @$page = $request->page;
        @$page2 = $request->page2;
        $remnants = "";
        

        $output = $this->getBottomSlabs($thickness, $slabwidth, $slabdepth, $price, $color, $type, $veins, $tones, $remnants, $page, $page2);
        $this->getResponse()->setBody($output);
    }

    private function productSkus($link)
    {
        Mage::getSingleton('core/session')->setproductSkus($link);

    }

    private function linkCreate($sesname, $link, $prodsku)
    { 
        //'productLink',$diagram,$allskus

        $sesname .= $prodsku;
        Mage::getSingleton('core/session')->setData($sesname,$link);
    }


    private function tempLinks($design, $diagram)
    {

        Mage::getSingleton('core/session')->setDesignLink($design);
        Mage::getSingleton('core/session')->setProductLink($diagram);
    }


   // private function minProduct($minprice,$minsku,$slabwidth)
    //{
     //   $minchange = $minprice.','.$minsku.','.$slabwidth;
     //   $this->linkCreate('minProduct',$minchange);
    //}

    public function getProduct($_sku)
    {
        $_product = Mage::getModel('catalog/product')->loadByAttribute('sku', $_sku);
        $product = Mage::getModel('catalog/product')->load($_product->getId());

        return $product;
    }

    public function getFilters($size, $slabwidth, $slabdepth, $price, $color, $type, $veins, $tones, $remnants)
    {
        // convert size
        $oldsize = $size;

        if ($size == 2) {
            $size = 85;
        } else {
            $size = 86;
        }

        //Fetch attribute set id by attribute set name
        $attrSetName = 'Vanity Slabs';
        $attributeSetId = Mage::getModel('eav/entity_attribute_set')
            ->load($attrSetName, 'attribute_set_name')
            ->getAttributeSetId();


        return $this->filters($oldsize, $size, $price, $color, $type, $veins, $tones, $attributeSetId);
    }

    public function getTopSlabs($size, $slabwidth, $slabdepth, $price, $color, $type, $veins, $tones, $remnants, $page, $page2)
    {

        // convert size
        $oldsize = $size;
        if ($size == 2) {
            $size = 85;
        } else {
            $size = 86;
        }

        //Fetch attribute set id by attribute set name
        $attrSetName = 'Vanity Slabs';
        $attributeSetId = Mage::getModel('eav/entity_attribute_set')
            ->load($attrSetName, 'attribute_set_name')
            ->getAttributeSetId();

        // get top slabs and make sure there are at least 20 slabs if they exist
        $topcollect = array();
        $allslabs = array();
        if ($page == '') {
            $page = 0;
        }
        $secondrecord = '';
        do {
            $page++;
            $topcollect = $this->loadSlabs($groupid, $attributeSetId, $size, $slabwidth, $slabdepth, 87, $page, $price, $color, $type, $veins, $tones);

            $firstrecord = $topcollect[0];

            if ($secondrecord == $topcollect[0]) {
                $stop = 1;
                break;
            } else {
                $secondrecord = $firstrecord;
                unset($topcollect[0]);
                $allslabs = array_merge($allslabs, $topcollect);
            }

        } while (count($allslabs) < 50);


        foreach ($allslabs as $slab) {

            if ($slab != $firstrecord) {

                $topslabs .= $slab;
            }
        }
        if ($stop == 1) {
            $topslabs = $topslabs . '<div id="stop" style="display:none">1</div>';
        }

        $topslabs = $topslabs . '<div id="loadmore" class="button-stones clearme"><center> <a href="javascript: void(0)" style="color:#fff" onclick="loadMore(' . $oldsize . ',' . $page . ',' . $page2 . ',\'' . $price . '\',\'' . $color . '\',\'' . $type . '\',\'' . $veins . '\',\'' . $tones . '\')">Load More Stones</a></center></div>';

        return $topslabs;

    }


    public function getBottomSlabs($size, $slabwidth, $slabdepth, $price, $color, $type, $veins, $tones, $remnants, $page, $page2)
    {
        $groupid = '';
        $fullslabs = '';

        // convert size
        $oldsize = $size;
        if ($size == 2) {
            $size = 85;
        } else {
            $size = 86;
        }


        //Fetch attribute set id by attribute set name
        $attrSetName = 'Vanity Slabs';
        $attributeSetId = Mage::getModel('eav/entity_attribute_set')
            ->load($attrSetName, 'attribute_set_name')
            ->getAttributeSetId();

        // get full slabs and make sure there are at least 20 slabs if they exist
        $topcollect = array();
        $allslabs = array();
        if ($page2 == '') {
            $page2 = 0;
        }
        $secondrecord = '';
        do {
            $page2++;

            $topcollect = $this->loadSlabs($groupid, $attributeSetId, $size, $slabwidth, $slabdepth, 88, $page2, $price, $color, $type, $veins, $tones);

            $firstrecord = $topcollect[0];

            if ($secondrecord == $topcollect[0]) {
                $stop = 1;
                break;
            } else {
                $secondrecord = $firstrecord;
                unset($topcollect[0]);
                $allslabs = array_merge($allslabs, $topcollect);
            }

        } while (count($allslabs) < 50);

        if (count($allslabs) < 1) {
            $fullslabs = '';
        }
        foreach ($allslabs as $slab) {

            if ($slab != $firstrecord) {

                $fullslabs .= $slab;
            }
        }
        if ($stop == 1) {
            $fullslabs = $fullslabs . '<div id="stop2" style="display:none">1</div>';
        }
        $fullslabs = $fullslabs . '<div id="loadmore2" class="clearme button-stones"><center> <a href="javascript: void(0)" style="color:#fff" onclick="loadMore2(' . $oldsize . ',' . $page . ',' . $page2 . ',\'' . $price . '\',\'' . $color . '\',\'' . $type . '\',\'' . $veins . '\',\'' . $tones . '\')">Load More Stones</a></center></div>';

        return $fullslabs;
    }

    public function getAllSlabs($size, $slabwidth, $slabdepth, $price, $color, $type, $veins, $tones, $remnants, $page, $page2)
    {
    	$slabs = '';
    	$loadmore = '';
    	$loadmore2 = '';
    	$colorhead = '';
    	$groupid = '';

        // convert size
        $oldsize = $size;
        if ($size == 2) {
            $size = 85;
        } else {
            $size = 86;
        }

        // Build head of ajax response
        $staticBlock = Mage::getModel('cms/block')->load('counter_stone_info')->getContent();
        $staticBlock2 = Mage::getModel('cms/block')->load('counter_stone_info2')->getContent();
        $topslabs = '<div class="renments"><div class="cp-message2">' . $staticBlock . '</div><div class="slab-images">';
        $fullslabs = '<div class="renments"><div class="cp-message2">' . $staticBlock2 . '</div><div class="slab-images">';

        //Fetch attribute set id by attribute set name
        $attrSetName = 'Vanity Slabs';
        $attributeSetId = Mage::getModel('eav/entity_attribute_set')
            ->load($attrSetName, 'attribute_set_name')
            ->getAttributeSetId();


        // get top slabs and make sure there are at least 20 slabs if they exist
        $topcollect = array();
        $allslabs = array();
        if ($page == '') {
            $page = 0;
        }
        $secondrecord = '';
        do {
            $page++;
            $topcollect = $this->loadSlabs($groupid, $attributeSetId, $size, $slabwidth, $slabdepth, 87, $page, $price, $color, $type, $veins, $tones);

            if (isset($topcollect[0])){
            	$firstrecord = $topcollect[0];
            }else{
            	$firstrecord = '';
            	$topcollect[0] = '';
            }

            if ($secondrecord == $topcollect[0]) {
                break;
            } else {
                $secondrecord = $firstrecord;
                unset($topcollect[0]);
                $allslabs = array_merge($allslabs, $topcollect);
            }

        } while (count($allslabs) < 50);

        $topcount = count($allslabs);

        foreach ($allslabs as $slab) {

            if ($slab != $firstrecord) {

                $topslabs .= $slab;
            }
        }



        // get full slabs and make sure there are at least 20 slabs if they exist
        $topcollect = array();
        $allslabs = array();
        if ($page2 == '') {
            $page2 = 0;
        }
        $secondrecord = '';
        do {
            $page2++;
            $topcollect = $this->loadSlabs($groupid, $attributeSetId, $size, $slabwidth, $slabdepth, 88, $page2, $price, $color, $type, $veins, $tones);

            if (isset($topcollect[0])){
            	$firstrecord = $topcollect[0];
            }else{
            	$firstrecord = '';
            	$topcollect[0] = '';
            }

            if ($secondrecord == $topcollect[0]) {
                break;
            } else {
                $secondrecord = $firstrecord;
                unset($topcollect[0]);
                $allslabs = array_merge($allslabs, $topcollect);
            }

        } while (count($allslabs) < 50);
        $bottomcount = count($allslabs);
        if (count($allslabs) < 1) {
            $fullslabs = '';
        }
        foreach ($allslabs as $slab) {

            if ($slab != $firstrecord) {

                $fullslabs .= $slab;
            }
        }


        // Build rest of ajax response


        $filters = '<center><img src="' . Mage::getBaseUrl() . 'skin/frontend/rwd/default/images/countertops/cp-spinner.gif"></center>';

        if ($topcount >= 50) {
            $loadmore = '<div id="loadmore" class="button-stones1"><center> <a href="javascript: void(0)" style="color:#fff" onclick="loadMore(' . $oldsize . ',' . $page . ',' . $page2 . ',\'' . $price . '\',\'' . $color . '\',\'' . $type . '\',\'' . $veins . '\',\'' . $tones . '\')">Load More Stones</a></center></div>';
        }

        if ($bottomcount >= 50) {
            $loadmore2 = '<div id="loadmore2" class="button-stones"><center> <a href="javascript: void(0)" style="color:#fff" onclick="loadMore2(' . $oldsize . ',' . $page . ',' . $page2 . ',\'' . $price . '\',\'' . $color . '\',\'' . $type . '\',\'' . $veins . '\',\'' . $tones . '\')">Load More Stones</a></center></div>';
        }

        $slabs .= '<div class="fullslabs">' . $fullslabs . '</div><br clear="left"/><br>' . $loadmore2 . '<br></div>';
        $topslabs .= '</div><br clear="all"/></div>';
        $layout = '<div id="filters">' . $filters . '</div><div class="ajax-load"><div class="top-slabs">' . $topslabs . '<br>' . $loadmore . '</div>' . $colorhead . $slabs . '</div>';

        return $layout;
    }

    public function loadSlabs($groupid, $attributeSetId, $size, $slabwidth, $slabdepth, $partner, $page, $price, $color, $type, $veins, $tones)
    {

        $slab = array();
        $sortorder = '';

        //We need to find the option ID super quick on 600+ products
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $query = 'SELECT * FROM catalog_product_option_title WHERE title LIKE "%Stone and Width%"';
        $results = $readConnection->fetchAll($query);
        $groupid = $results[0]['option_id'];


        //Load product model collecttion filtered by attribute set id
        $products = Mage::getModel('catalog/product')
            ->getCollection()
            ->setPageSize(10)
            ->setCurPage($page)
            ->addAttributeToSelect('*')
            ->addFieldToFilter('attribute_set_id', $attributeSetId)
            ->addFieldToFilter(array(
                array('attribute' => 'thickness', 'eq' => $size)
            ))
            ->addFieldToFilter(array(
                array('attribute' => 'stock_type', 'eq' => $partner)
            ));

        if ($price != '' && $price != 'all') {
            $products->addFieldToFilter(array(
                array('attribute' => 'price_group', 'eq' => $price)
            ));
        }

        if ($color != '' && $color != 'all') {
            $products->addFieldToFilter(array(
                array('attribute' => 'color', 'eq' => $color)
            ));
        }

        if ($type != '' && $type != 'all') {
            $products->addFieldToFilter(array(
                array('attribute' => 'stone', 'eq' => $type)
            ));
        }

        if ($veins != '' && $veins != 'all') {
            $products->addFieldToFilter(array(
                array('attribute' => 'veins', 'eq' => $veins)
            ));
        }

        if ($tones != '' && $tones != 'all') {
            $products->addFieldToFilter(array(
                array('attribute' => 'tone', 'eq' => $tones)
            ));
        }

        $counter = 0;

        foreach ($products as $product) {

            $fullname = $product->getName();
//            $productid = $product->getId();
//            $fullproduct = Mage::getModel('catalog/product')->load($productid);

            $color = $product->getResource()->getAttribute('color')->getFrontend()->getValue($product);
            $stone = $product->getResource()->getAttribute('stone')->getFrontend()->getValue($product);
            $pricegroup = $product->getResource()->getAttribute('price_group')->getFrontend()->getValue($product);
            $stone_min_depth = $product->getResource()->getAttribute('stone_min_depth')->getFrontend()->getValue($product);
            $stone_max_depth = $product->getResource()->getAttribute('stone_max_depth')->getFrontend()->getValue($product);
            $stone_min_width = $product->getResource()->getAttribute('stone_min_width')->getFrontend()->getValue($product);
            $stone_max_width = $product->getResource()->getAttribute('stone_max_width')->getFrontend()->getValue($product);

            // make sure have stone slab that can fit the user dimensions
            if ($slabwidth >= $stone_min_width && $slabwidth <= $stone_max_width && $slabdepth >= $stone_min_depth && $slabdepth <= $stone_max_depth) {

                // store first product so we can make sure we don't get dupes
                $counter++;
                if ($counter == 1) {
                    $slab[] = $product->getId();
                }

                $realsku = $product->getSku();

                $_sku = str_replace(" ", "_", $realsku);
                $samplesku = str_replace("slab", "sample", $realsku);

 
                $pid = $product->getId();
                $query = 'SELECT * FROM cataloginventory_stock_item WHERE product_id = '.$pid;
                $stockResults = $readConnection->fetchAll($query);
                $stock =  $stockResults[0]['is_in_stock'];
                $backorder =  $stockResults[0]['backorders'];


                $samplestock = '';
                $pid = Mage::getModel("catalog/product")->getIdBySku( $samplesku );
                if ($pid != ''){
                    $query = 'SELECT * FROM cataloginventory_stock_item WHERE product_id = '.$pid;
                    $stockResults = $readConnection->fetchAll($query);
                    $samplestock =  $stockResults[0]['is_in_stock'];

                }
 

                $optionid = '';
                $query = 'SELECT * FROM catalog_product_option_type_value WHERE option_id = ' . $groupid . ' AND sku = "' . $realsku . '"';
                $results = $readConnection->fetchAll($query);
                if (isset($results[0])){
                	$optionid = $results[0]['option_type_id'];
            	}else{
            		$optionid = '';
            	}

                // get product image
                $entireProduct = Mage::getModel('catalog/product')->load($product->getId());
                $imageurl = Mage::helper('catalog/image')->init($entireProduct, 'image')->resize(200);

                // make sure this product exists in our main product
                if ($optionid != '' && $stock == 1 || $optionid != '' && $backorder == 1) {

                     foreach ($results as $result) {
                        if ($result['sku'] == $_sku){
                            $sortorder = $result['sort_order'];
                            break;
                        }
                     }

                    if ($samplestock == 1) {
                        $sample = '<center><a href="javascript:void(0);" onclick="orderSample(\'' . $_sku . '\')" >add sample to cart</a></center>';
                    } else {
                        $sample = '<center>no sample available</center>';
                    }
                    $topslabs = '<div id="' . $_sku . '" sortorder="' . $sortorder . '"  realsku="' . $realsku . '" optionid="' . $optionid . '" class="slab-info" onclick="slabClick(\'' . $_sku . '\')" onmouseover="slabHoverShow(\'' . $_sku . '\')" onmouseout="slabHoverHide(\'' . $_sku . '\')"><div class="help"><div class="' . $_sku . ' slabbubble slabhelp-width" ><img src="' . $imageurl . '"><b>' . $color . '</b><br>' . $stone . '<br>' . $pricegroup . '<br><hr>' . $sample . '</div></div>';
                    $topslabs .= '<div class="slabname">' . $fullname . '</div><img src="' . $imageurl . '" class="small-slab-image"></div>';
                    $slab[] = $topslabs;
                }
            }
        }


        return $slab;
    }


    public function checkFilter($size, $check, $price, $color, $type, $veins, $tones, $attributeSetId)
    {

        //echo $size.','.$check.','.$price.','.$color.','.$type.','.$veins.','.$tones."<br>";
        //Load product model collecttion filtered by attribute set id
        $products = Mage::getModel('catalog/product')
            ->getCollection()
            ->addAttributeToSelect('*')
            ->addFieldToFilter('attribute_set_id', $attributeSetId)
            ->addFieldToFilter(array(
                array('attribute' => 'thickness', 'eq' => $size)
            ));


        if ($price != '' && $price != 'all' && $check == 'price') {
            $products->addFieldToFilter(array(
                array('attribute' => 'price_group', 'eq' => $price)
            ));
        }

        if ($color != '' && $color != 'all' && $check == 'color') {
            $products->addFieldToFilter(array(
                array('attribute' => 'color', 'eq' => $color)
            ));
        }

        if ($type != '' && $type != 'all' && $check == 'stone') {
            $products->addFieldToFilter(array(
                array('attribute' => 'stone', 'eq' => $type)
            ));
        }

        if ($veins != '' && $veins != 'all' && $check == 'veins') {
            $products->addFieldToFilter(array(
                array('attribute' => 'veins', 'eq' => $veins)
            ));
        }

        if ($tones != '' && $tones != 'all' && $check == 'tones') {
            $products->addFieldToFilter(array(
                array('attribute' => 'tone', 'eq' => $tones)
            ));
        }

        return count($products);
    }

    /**
     *
     * Builds out filters
     *
     * @param
     *            $oldsize    = human readable size
     *            $size        = converted size associated with attribute thickness
     *
     * @return   HTML code of filters
     *
     */

    public function filters($oldsize, $size, $price, $color, $type, $veins, $tones, $attributeSetId)
    {

    	$colorremnants = '';

        //*****************************************************************************
        // Begin filters
        // we need to filter the filters
        //*****************************************************************************

        $products = Mage::getModel('catalog/product')
            ->getCollection()
            ->addAttributeToSelect('*')
            ->addFieldToFilter('attribute_set_id', $attributeSetId)
            ->addFieldToFilter(array(
                array('attribute' => 'thickness', 'eq' => $size)
            ));

        if ($price != '' && $price != 'all') {
            $products->addFieldToFilter(array(
                array('attribute' => 'price_group', 'eq' => $price)
            ));
        }

        if ($color != '' && $color != 'all') {
            $products->addFieldToFilter(array(
                array('attribute' => 'color', 'eq' => $color)
            ));
        }

        if ($type != '' && $type != 'all') {
            $products->addFieldToFilter(array(
                array('attribute' => 'stone', 'eq' => $type)
            ));
        }

        if ($veins != '' && $veins != 'all') {
            $products->addFieldToFilter(array(
                array('attribute' => 'veins', 'eq' => $veins)
            ));
        }

        if ($tones != '' && $tones != 'all') {
            $products->addFieldToFilter(array(
                array('attribute' => 'tone', 'eq' => $tones)
            ));
        }


        // thickness values
        $attribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'thickness');
        $label = $attribute->getFrontendLabel();
        $colorthickness = '<div class="color-price">' . $label . '<br><select class="color-thickness-options" onChange="filterColors()">';

        if ($oldsize == 2) {
            $colorthickness .= '<option value="2" selected>2cm</option>';
        } else {
            $colorthickness .= '<option value="2">2cm</option>';
        }

        if ($oldsize == 3) {
            $colorthickness .= '<option value="3" selected>3cm</option>';
        } else {
            $colorthickness .= '<option value="3">3cm</option>';
        }

        $colorthickness .= '</select></div>';


        // price values
        $attribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'price_group');
        $label = $attribute->getFrontendLabel();
        $colorprice = '<div class="color-price">' . $label . '<br><select class="color-price-options" onChange="filterColors()"><option value="all">All</option>';

        if ($attribute->usesSource()) {
            $options = $attribute->getSource()->getAllOptions(false);

            foreach ($options as $key => $item) {

                if ($price == 'all' || $price == '' || $price == $options[$key]['value']) {

                    if ($price == $options[$key]['value']) {
                        $colorprice .= '<option value="' . $options[$key]['value'] . '" selected>' . $options[$key]['label'] . '</option>';
                    } else {
                        $pcheck = $options[$key]['value'];
                        //$check = $this->checkFilter($size, 'price', $pcheck, $ccheck, $tcheck, $vcheck, $tocheck, $attributeSetId);
                        foreach ($products as $product) {

                            if ($product['price_group'] == $options[$key]['value']) {
                                $colorprice .= '<option value="' . $options[$key]['value'] . '">' . $options[$key]['label'] . '</option>';
                                break;
                            }

                        }


                    }

                }
            }
        }
        $colorprice .= '</select></div>';

        // stone color
        $attribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'color');
        $label = $attribute->getFrontendLabel();
        $colorvalues = '<div class="color-values">' . $label . '<br><select class="color-color-options" onChange="filterColors()"><option value="all">All</option>';


        if ($attribute->usesSource()) {
            $options = $attribute->getSource()->getAllOptions(false);

            foreach ($options as $key => $item) {

                if ($color == 'all' || $color == '' || $color == $options[$key]['value']) {
                    if ($color == $options[$key]['value']) {
                        $colorvalues .= '<option value="' . $options[$key]['value'] . '" selected>' . $options[$key]['label'] . '</option>';
                    } else {
                        $ccheck = $options[$key]['value'];

                        //$check = $this->checkFilter($size, 'color', '', $ccheck, $tcheck, $vcheck, $tocheck, $attributeSetId);
                        foreach ($products as $product) {

                            if ($product['color'] == $options[$key]['value']) {
                                $colorvalues .= '<option value="' . $options[$key]['value'] . '">' . $options[$key]['label'] . '</option>';
                                break;
                            }

                        }
                    }

                }
            }
        }
        $colorvalues .= '</select></div>';


        // Stone
        $attribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'stone');
        $label = $attribute->getFrontendLabel();
        $colortypes = '<div class="color-types">' . $label . '<br><select class="color-type-options" onChange="filterColors()"><option value="all">All</option>';

        if ($attribute->usesSource()) {
            $options = $attribute->getSource()->getAllOptions(false);

            foreach ($options as $key => $item) {

                if ($type == 'all' || $type == '' || $type == $options[$key]['value']) {

                    if ($type == $options[$key]['value']) {
                        $colortypes .= '<option value="' . $options[$key]['value'] . '" selected>' . $options[$key]['label'] . '</option>';
                    } else {
                        $tcheck = $options[$key]['value'];
                        //$check = $this->checkFilter($size, 'stone', '', '', $tcheck, $vcheck, $tocheck, $attributeSetId);
                        foreach ($products as $product) {

                            if ($product['stone'] == $options[$key]['value']) {
                                $colortypes .= '<option value="' . $options[$key]['value'] . '">' . $options[$key]['label'] . '</option>';
                                break;
                            }

                        }


                    }

                }
            }
        }
        $colortypes .= '</select></div>';


        // color veins
        $attribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'veins');
        $label = $attribute->getFrontendLabel();
        $colorveins = '<div class="color-veins">' . $label . '<br><select class="color-veins-options" onChange="filterColors()"><option value="all">All</option>';

        if ($attribute->usesSource()) {
            $options = $attribute->getSource()->getAllOptions(false);

            foreach ($options as $key => $item) {
                if ($veins == 'all' || $veins == '' || $veins == $options[$key]['value']) {

                    if ($veins == $options[$key]['value']) {
                        $colorveins .= '<option value="' . $options[$key]['value'] . '" selected>' . $options[$key]['label'] . '</option>';
                    } else {
                        $vcheck = $options[$key]['value'];
                        //$check = $this->checkFilter($size, 'veins','', '', '', $vcheck, $tocheck, $attributeSetId);
                        foreach ($products as $product) {

                            if ($product['veins'] == $options[$key]['value']) {
                                $colorveins .= '<option value="' . $options[$key]['value'] . '">' . $options[$key]['label'] . '</option>';
                                break;
                            }

                        }

                    }

                }
            }
        }
        $colorveins .= '</select></div>';


        $attribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'tone');
        $label = $attribute->getFrontendLabel();
        $colorveins .= '<div class="color-tones">' . $label . '<br><select class="color-tones-options" onChange="filterColors()"><option value="all">All</option>';

        if ($attribute->usesSource()) {
            $options = $attribute->getSource()->getAllOptions(false);

            foreach ($options as $key => $item) {

                if ($tones == 'all' || $tones == '' || $tones == $options[$key]['value']) {
                    if ($tones == $options[$key]['value']) {
                        $colorveins .= '<option value="' . $options[$key]['value'] . '" selected>' . $options[$key]['label'] . '</option>';
                    } else {
                        $tocheck = $options[$key]['value'];
                        //$check = $this->checkFilter($size, 'tones', '', '', '', '', $tocheck, $attributeSetId);
                        foreach ($products as $product) {

                            if ($product['tone'] == $options[$key]['value']) {
                                $colorveins .= '<option value="' . $options[$key]['value'] . '">' . $options[$key]['label'] . '</option>';
                                break;
                            }

                        }
                    }
                }
            }
        }
        $colorveins .= '</select></div>';

        //*****************************************************************************
        // End filters
        //*****************************************************************************

        $filters = '<div class="edge-search">' . $colorthickness . $colorprice . $colorvalues . $colortypes . $colorveins . $colorremnants . '</div>';

        return $filters;
    }


}