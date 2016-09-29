<?php
	
	// Design URL string variables

	//$designurl = '?w='.$slabwidth.'&h='.$slabheight.'&t='.$thickness.'&s='.$slabid.
	//'&p='.$polish.'&e='.$edgeid.'&b='.$backsplashid.'&sink='.$sinkid.'&sinknum='.$sink[0].
	//'&sink1inch='.$sink1inch.'&sink2inch='.$sink2inch.'&sinktype='.$sinktype.'&v1id='.$vessel1id.
	//'&v1holeleft='.$vessel1holeleft.'&v1holeback='.$v1holeback.'&v1holeinch='.$vessel1holesize.
	//'&v2id='.$vessel2id.'&v2holeleft='.$vessel2holeleft.'&v2holeback='.$v2holeback.'&v2holeinch='.$vessel2holesize.'&f='.$faucets.'&fh='.$faucethole;

include_once("Mage/Core/Controller/Front/Action.php");

class Fruition_Countertops_DesignController extends Mage_Core_Controller_Front_Action {



	public function indexAction(){
		$orderid = '';
		$width = '';
		$height = '';
		$slab = '';
		$slabname = '';
		$edgename = '';
		$edgename2 = '';
		$edgeid = '';
		$polish = '';
		$polish2 = '';
		$backsplash = '';
		$backsplash2 = '';
		$sink = '';
		$sinknum = '';
		$sinksku = '';
		$sinknames = '';
		$sinksize = '';
		$sink1inch = '';
		$sink2inch = '';
		$sinktype = '';
		$faucet = '';
		$faucetdes = '';
		$faucethole = '';
		$v1id = '';
		$v2id = '';
		$v1holeleft = '';
		$v2holeleft = '';
		$v1holeback = '';
		$v2holeback = '';
		$v1holeinch = '';
		$v2holeinch = '';
		$f1degree = '';
		$f2degree = '';
		$f1dis = '';
		$f2dis = '';

		if (isset($_GET['orderid'])){ $orderid = $_GET['orderid']; }
		if (isset($_GET['w'])){ $width = $_GET['w']; }
		if (isset($_GET['h'])){ $height = $_GET['h']; }
		if (isset($_GET['t'])){ $thickness = $_GET['t']; }
		if (isset($_GET['s'])){ $slab = $_GET['s']; }
		if (isset($_GET['sn'])){ $slabname = $_GET['sn']; }
		if (isset($_GET['e'])){ $edgename = $_GET['e']; }
		if (isset($_GET['en'])){ $edgename2 = $_GET['en']; }
		if (isset($_GET['eid'])){ $edgeid = $_GET['eid']; }
		if (isset($_GET['p'])){
			$polish = explode(",",$_GET['p']);
			$polish2 = $_GET['p'];
		}
		if (isset($_GET['b'])){
			$backsplash = explode(",",$_GET['b']);
			$backsplash2 = $_GET['b'];
		}

		if (isset($_GET['sink'])){ $sink = $_GET['sink']; }
		if (isset($_GET['sinknum'])){ $sinknum = $_GET['sinknum']; }
		if (isset($_GET['sinksku'])){ $sinksku = $_GET['sinksku']; }
		if (isset($_GET['sinknames'])){ $sinknames = $_GET['sinknames']; }
		if (isset($_GET['sinksize'])){ $sinksize = $_GET['sinksize']; }
		if (isset($_GET['sink1inch'])){ $sink1inch = $_GET['sink1inch']; }
		if (isset($_GET['sink2inch'])){ $sink2inch = $_GET['sink2inch']; }
		if (isset($_GET['sinktype'])){ $sinktype = $_GET['sinktype']; }
		if (isset($_GET['f'])){ $faucet = $_GET['f']; }
		if (isset($_GET['fd'])){ $faucetdes = $_GET['fd']; }
		if (isset($_GET['fh'])){ $faucethole = $_GET['fh']; }
		if (isset($_GET['v1id'])){ $v1id = $_GET['v1id']; }
		if (isset($_GET['v2id'])){ $v2id = $_GET['v2id']; }
		if (isset($_GET['v1holeleft'])){ $v1holeleft = $_GET['v1holeleft']; }
		if (isset($_GET['v2holeleft'])){ $v2holeleft = $_GET['v2holeleft']; }
		if (isset($_GET['v1holeback'])){ $v1holeback = $_GET['v1holeback']; }
		if (isset($_GET['v2holeback'])){ $v2holeback = $_GET['v2holeback']; }
		if (isset($_GET['v1holeinch'])){ $v1holeinch = $_GET['v1holeinch']; }
		if (isset($_GET['v2holeinch'])){ $v2holeinch = $_GET['v2holeinch']; }
		if (isset($_GET['f1degree'])){ $f1degree = $_GET['f1degree']; }
		if (isset($_GET['f2degree'])){ $f2degree = $_GET['f2degree']; }	
		if (isset($_GET['f1dis'])){ $f1dis = $_GET['f1dis']; }
		if (isset($_GET['f2dis'])){ $f2dis = $_GET['f2dis']; }	

		$baseurl = Mage::getBaseUrl();
		$diagram = $baseurl.'countertops/order/';
		$designurl = $diagram.htmlentities('?w='.$width.'&h='.$height.'&t='.$thickness.'&s='.$slab.'&sn='.$slabname.'&p='.$polish2.'&e='.$edgename.'&en='.$edgename2.'&eid='.$edgeid.'&b='.$backsplash2.'&sinknames='.$sinknames.'&sinksku='.$sinksku.'&sink='.$sink.'&sinknum='.$sinknum.'&sink1inch='.$sink1inch.'&sink2inch='.$sink2inch.'&sinksize='.$sinksize.'&sinktype='.$sinktype.'&v1id='.$v1id.'&v1holeleft='.$v1holeleft.'&v1holeback='.$v1holeback.'&v1holeinch='.$v1holeinch.'&v2id='.$v2id.'&v2holeleft='.$v2holeleft.'&v2holeback='.$v2holeback.'&v2holeinch='.$v2holeinch.'&f='.$faucet.'&fd='.$faucetdes.'&fh='.$faucethole.'&f1degree='.$f1degree.'&f1dis='.$f1dis.'&f2degree='.$f2degree.'&f2dis='.$f2dis);

		$sinkwidth = $sinksize*8;

		$sinksize = round($sinksize * 2) / 2;	
		$halfsink = $sinksize/2;
		$halfsinkpx = $halfsink*8;

		if ($sinknum == 2){
			$sink1left = ($sink1inch-$halfsink)*8;
			$sink2left = ($sink2inch-$halfsink)*8;
		}else{
			$sink1left = ($sink1inch-$halfsink)*8;
			$sink2left = $sink2inch*8;
		}
		$middle1faucet = ($halfsink*8)-10;
		

		$slabwidth = $width*8;
		$slabheight = $height*8;

		$orderID = "";

		if( isset($_POST['orderID']) )
		{$orderID = $_POST['orderID'];}

		$activesinks = 0;

		$v1px = $v1holeinch*8;
		$v2px = $v2holeinch*8;

		if ($sink != "Vessel"){
			$sink1class = strtolower(str_replace(" ", "-", $sink));
			$sink2class = $sink1class;
			
			$activesinks = 1;
		}else{
			$sink1class = "vessel";
			$sink1left = $v1holeleft*8;
			$sink1top = $v1holeback*8;

			$f1distance = $f1dis*8;
			$f2distance = $f2dis*8;

			if ($sinknum == 2){
			$sink2class = "vessel2";
			$sink2left = $v2holeleft*8;
			$sink2top = $v2holeback*8;	
			}

		}
		

		?>

		<html lang="en"> 
		<head> 
		  <meta charset="utf-8"> 
		 
		  <title>Nestappeal Custom Vanity Countertop</title> 
		  <meta name="description" content="Custom Vanity Counter"> 
		  <meta name="author" content="NestAppeal"> 
		 
		   
		  <!--[if lt IE 9]> 
		  <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script> 
		  <![endif]--> 
		 
		  <style type="text/css"> 
		 
		  * { 
		  	font-family: Arial, "Verdana", Times, sans-serif; 
		  	font-size: 14px; 
		  } 
		 
		  .container { 
		  	width: 1100px; 
		  	height: 792px; 
			margin-left: auto;
			margin-right: auto; 
		 	 
		  } 
		  .countertopRender { 
		  	max-width: 1200px; 
		  	border-bottom: 4px solid #00868d; 
		    min-height: 309px; 
		  } 
		  img.logo { 
		  	width: 617px; 
		  } 
		  .headerLogo { 
		  	float: left; 
		  } 
		  .headerDetails { 
		  	float: right; 
		  	margin-top: 81px; 
		  	font-weight: bold;
			font-family: "Raleway";
			color: #000;
			letter-spacing: 2px;
			margin-right:30px;		  	 
		  } 
		  .headerDetails .phone, .headerDetails .email { 
		  	float: left; 
		  } 
		.headerDetails .phone { 
			margin-right: 20px; 
			font-family: "Raleway";
			font-size: 20px;			
		} 
		.countertop {clear: both;} 
		.countertopHeader h2 { 
			float: left; 
			font-size: 30px;
			font-family: "Raleway";
			margin-left: 70px;
			margin-top: 10px;
			margin-bottom: 50px;
		} 
		.countertopHeader .orderNumber { 
			float: right; 
			margin-top: 20px; 
			margin-right:100px;
		} 
		.countertop-slab{
			margin-top:50px;
			margin-left:auto;
			margin-right:auto;
			width: <?php echo $slabwidth+100; ?>px;
		}
		.cp-counter {
			background:#fff;
			width: <?php echo $slabwidth; ?>px;
			height: <?php echo $slabheight; ?>px;
			float:left;
			position: relative;
			border: solid thin black;
		}

		@font-face {
		  font-family: 'Raleway';
		  font-style: normal;
		  font-weight: 300;
		  src: local('Raleway Light'), local('Raleway-Light'), url(http://fonts.gstatic.com/s/raleway/v9/-_Ctzj9b56b8RgXW8FAriQzyDMXhdD8sAj6OAJTFsBI.woff2) format('woff2');
		  unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2212, U+2215, U+E0FF, U+EFFD, U+F000;
		}

		.countertopRender {clear: both;position: relative;padding-top:10px;} 
		.summary { 
			margin: 0 50px 20px 30px; 
		} 
		h3.order-number{
			float: right;
			font-family: "Raleway";
			font-size: 17px;
			margin-top: 25px;			
		}
		.summary h3 { 
			font-size: 18px; 
			font-family: "Raleway";
			text-transform: uppercase;
			margin-bottom: 15px; 
		} 
		.specs { 
			font-size: 14px;
			float: left;
			line-height: 18px;
			width: 47%;
		} 
		.specs.right { 
			float: right; 
		} 
		.spec { 
			font-weight: bold;
			font-size: 13px;
			margin-right: 4px;
			text-transform: capitalize;
		} 
		.footer { 
			clear: both; 
			font-size: 12px; 
			border-top: 4px solid #00868d; 
			padding-top: 10px; 
		} 


		.office-use h3, .footer h3{
		   font-size: 18px;
		   margin-bottom: 4px;
		   font-family: "Raleway";
		   text-transform: uppercase;
		   margin-bottom: 15px;
		}

		.specs ul {
		   list-style-type: none;
		   padding-left: 22px;
		}

		.specs ul li {
		   font-size: 20px;
		   margin-bottom: 15px;
		   color: #000;
		   font-weight:bold;
		   border-bottom: solid medium #000;
		   padding-bottom: 5px;
		   text-transform: uppercase;
		   font-family: "Raleway";
		   width:500px;
		}

		.footer ul li {
		   font-size: 15px;
		   padding-bottom: 10px;
		}

		.protect-width {
			width:0px;
			float:left;
		}


		.shrink-main-width {
		    border-left: 2px solid #000000;
		    border-right: 2px solid #000000;
		    float: left;
		    height: 13px;
		    line-height: 6px;
		    margin-top:-43px;
		    margin-left:0px;
		    margin-bottom: 0;
		    text-align: center;
		    width:<?php echo $slabwidth; ?>px;
		}

		.horz-line {
			width:100%;
			height:2px;
			background-color:#000;
		}

		.vert-line {
			height:100%;
			width:2px;
			background-color:#000;
			margin-left:auto;
			margin-right:auto;
		}

		.shrink-main-width  span{
			background: #000;
			padding:5px 10px 5px 10px;
			color:#fff;
			font-weight:bold;
			position: relative;
			z-index:11;
			-moz-border-radius: 5px;
			border-radius: 5px;
		}

		.shrink-main-height {
		    border: medium none;
		    position:relative;
		    float: left;
		    margin: 0 0 0 14px;
		    padding: 0;
		    vertical-align: middle;	
		   	border-bottom: 2px solid #000000;
		    border-top: 2px solid #000000;
		    height: <?php echo $slabheight; ?>px;
		    padding: 0;
		    text-align: center;
		    width: 20px;
		}

		.shrink-main-height  span{
			position:absolute !important;
			top:41%;
			left:-10px;
			background: #000;
			padding:5px 10px 5px 10px;
			color:#fff;
			font-weight:bold;
			position: relative;
			z-index:11;
			-moz-border-radius: 5px;
			border-radius: 5px;
		}

		.cp-counter-left {
			overflow:hidden;
			float:left;
			margin-left:5px;
			width:20px;
			height:<?php echo $slabheight; ?>px;
			text-align:center;
			padding-top:10px;
		}

		.cp-counter-left .backsplash, .cp-counter-left .polish, .cp-counter-left .edging {
			writing-mode:tb-rl;
			-webkit-transform:rotate(-90deg);
			-moz-transform:rotate(-90deg);
			-o-transform: rotate(-90deg);
			white-space:nowrap;
			display:block;
			background:none !important;
			color:#000;
			font-weight:normal;
			font-size:12px;
			padding:0px;
			margin-bottom:38px
			
		}



<?php if ($polish[0] != 1){ ?>
.cp-counter-left .polish, .cp-counter-left .edging{
	display:none;
}

	<?php if ($backsplash[0] == 1){	?>
	.cp-counter-left  {
	background-color: #f1ffbc;
	    border: 0 none;
	}
	<?php }else{ ?>
	.cp-counter-left .backsplash {
		display:none;
	}
	<?php } ?> 
.cp-counter-left .backsplash {
	margin-top: <?php echo $slabheight-36; ?>px;
}
<?php }else{ ?>
.cp-counter-left {	
    border-bottom: 1px solid #000;
    border-left: 1px solid #000;
    border-top: 1px solid #000;
}
.cp-counter-left .edging{
	margin-top: <?php echo $slabheight-86; ?>px;
}
.cp-counter-left .backsplash {
	display:none;
}
<?php } ?>    

.cp-counter-front {
    border-bottom: 1px solid #000;
    border-left: 1px solid #000;
    border-right: 1px solid #000;
    float: left;
    height: 20px;
    margin-top:5px;
    margin-left: 25px;
    overflow: hidden;
    text-align: center;
    width:<?php echo $slabwidth; ?>px;
}

.cp-text-front {
	margin-left: 25px;
	float: left;
	text-align: center;
	width:<?php echo $slabwidth; ?>px;
}

.cp-counter-front .edging {
	margin-left:5px;
}
.cp-counter-back {
	text-align:center;
	float;left;
	margin-top:13px;
	height:20px;
	padding-top:5px;
	width:<?php echo $slabwidth-4; ?>px;
	}
.cp-counter-back .backsplash, .cp-counter-back .polish, .cp-counter-back .edging {background:none !important;color:#000;font-weight:normal;font-size:12px;padding:0px;}
.cp-counter-back .backsplash, .cp-counter-back .polish {padding-right:5px;}		


<?php if ($polish[1] != 1){ ?>
.cp-counter-back .polish, .cp-counter-back .edging{
	display:none;
}
	<?php if ($backsplash[1] == 1){	?>
	.cp-counter-back  {
	background-color: #f1ffbc;
	    border: 0 none;
	}

	<?php }else{ ?>
		.cp-counter-back .backsplash {
			display:none;
		}
	<?php } ?> 

<?php }else{ ?>	
.cp-counter-back{	
    border-right: 1px solid #000;
    border-left: 1px solid #000;
    border-top: 1px solid #000;
}
.cp-counter-back .backsplash {
	display:none;
}
<?php } ?> 

.cp-counter-right {
	overflow:hidden;

	float:left;
	width:20px;
	height:<?php echo $slabheight; ?>px;
	text-align:center;
	padding-top:10px;
}
.cp-counter-right .backsplash, .cp-counter-right .polish, .cp-counter-right .edging {
	writing-mode:tb-rl;
	-webkit-transform:rotate(90deg);
	-moz-transform:rotate(90deg);
	-o-transform: rotate(90deg);
	white-space:nowrap;
	background:none !important;
	color:#000;
	font-weight:normal;
	font-size:12px;
	padding:0px;
	margin-bottom:5px;
	display:block;
}

.cp-counter-right .backsplash {margin-bottom:63px;}
.cp-counter-right .edging {margin-top:36px;}

<?php if ($polish[2] != 1){ ?>
.cp-counter-right .polish, .cp-counter-right .edging{
	display:none;
}
	<?php if ($backsplash[2] == 1){	?>
	.cp-counter-right  {
	background-color: #f1ffbc;
	    border: 0 none;
	}
	<?php }else{ ?>
		.cp-counter-right .backsplash {
			display:none;
		}
		<?php } ?> 

<?php }else{ ?>	
	
.cp-counter-right{	
    border-right: 1px solid #000;
    border-bottom: 1px solid #000;
    border-top: 1px solid #000;

}
.cp-counter-right .backsplash {
	display:none;
}
<?php } ?> 


.small-oval{
  position: absolute;
  top:50px;
  left:50px;
  height:75px;
  width:<?php echo $sinkwidth; ?>px;
  border: 2px solid #000;
  border-radius:107px / 67px;
  display: none;
}

.large-oval{
  position: absolute;
  height:85px;
  top:40px;
  width:<?php echo $sinkwidth; ?>px;
  border: 2px solid #000;
  border-radius:107px / 67px;
  display: none;
}

.small-square{
  position: absolute;
  height:75px;
  top:50px;
  width:<?php echo $sinkwidth; ?>px;
  border: 2px solid #000;
  border-radius:5px;
  display: none;
}

.large-square{
  position: absolute;
  height:85px;
  top:40px;
  width:<?php echo $sinkwidth; ?>px;
  border: 2px solid #000;
  border-radius:5px;
  display: none;
}

.vessel{
  position: absolute;
  height:<?php echo $v1px-2;?>px;
  width:<?php echo $v1px-2;?>px;
  border: 2px solid #000;
  border-radius:<?php echo $v1px/2;?>px;
  display: none;
}

.vessel2{
  position: absolute;
  height:<?php echo $v2px-2;?>px;
  width:<?php echo $v2px-2;?>px;
  border: 2px solid #000;
  border-radius:<?php echo $v2px/2;?>px;
  display: none;
}


<?php
if ($sink == "Vessel"){
	$sink1top = $v1holeback*8;
	$sink2top = $v2holeback*8;
}elseif ($sink == "Small Square" || $sink == "Small Oval"){
	$sink1top = 50;
	$sink2top = 50;
}elseif ($sink == "Large Square" || $sink == "Large Oval"){
	$sink1top = 40;
	$sink2top = 40;
}
?>
.magic {top: <?php echo $sink;?> }
<?php if ($sinknum == 1){ ?>		
	.sink2-display  {display:none;}
<?php } ?>

.sink1-display{
	left: <?php echo $sink1left;?>px;
	top: <?php echo $sink1top;?>px;
}

.sink2-display{
	left: <?php echo $sink2left;?>px;
	<?php if ($sink2top == ''){ ?>
		top: <?php echo $sink1top;?>px;
	<?php }else{ ?>
	top: <?php echo $sink2top;?>px;
	<?php } ?>

}

.sink1-display-dash-line{
	position: absolute;
	border-bottom:2px dashed #000;
	border-right:2px dashed #000;
	height:10px;
	width:<?php echo $sink1left+$halfsinkpx;?>px;
	top:<?php echo $sink1top+86;?>px;
	display: none;
}

.sink2-display-dash-line{
	position: absolute;
	border-bottom:2px dashed #000;
	border-right:2px dashed #000;
	height:28px;
	width:<?php echo $sink2left+$halfsinkpx;?>px;
	top:<?php echo $sink1top+84;?>px;
	display: none;
}

.sink1-distance{
	position: absolute;
	top:<?php echo $sink1top+84;?>px;
	left:<?php echo $sink1left+$halfsinkpx+4;?>px;
	display: none;
}

.sink2-distance{
	position: absolute;
	top:<?php echo $sink1top+84;?>px;
	left:<?php echo $sink2left+$halfsinkpx+4;?>px;
	display: none;
}

.left45, .right45, .left452, .right452 {display: none;}


element.style {
    display: block;
}
*, *::before, *::after {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}
*, *::before, *::after {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}
.centerhole1, .centerhole2 {
    border: 0px;
    border-radius: 14px;
    bottom: 0;
    height: 14px;
    left: 0;
    margin: auto;
    position: absolute;
    right: 0;
    top: 0;
    width: 14px;
    z-index: 50;
}

<?php if ($activesinks == 1 && $sinknum == 1){ ?>
	.sink1-display, .sink1-display-dash-line, .sink1-distance {display: block;}
<?php }elseif ($activesinks == 1 && $sinknum == 2){ ?>
	.sink1-display, .sink1-display-dash-line, .sink1-distance {display: block;}
	.sink2-display, .sink2-display-dash-line, .sink2-distance {display: block;}
<?php }elseif ($sink1class != "vessel"){ ?>
.sink1-display, .sink2-display, .sink1-display-dash-line, .sink2-display-dash-line, .sink1-distance, .sink2-distance {display:none;}	
<?php }elseif ($sink1class == "vessel" && $sinknum == 1){ ?>
	.sink1-display {display:block;}
	<?php if ($v1holeinch > 1.75){ ?>
		.centerhole1 {display:block;border: 2px solid #000;}
	<?php } ?>
<?php }elseif ($sink2class == "vessel2" && $sinknum == 2){ ?>
	.sink1-display {display:block;}
	.sink2-display {display:block;}
	<?php if ($v1holeinch > 1.75){ ?>
		.centerhole1 {display:block;border: 2px solid #000;}
	<?php } ?>
	<?php if ($v2holeinch > 1.75){ ?>
		.centerhole2 {display:block;border: 2px solid #000;}	
	<?php } ?>
<?php } ?>



.sink1-lefthole, .sink1-middlehole, .sink1-righthole {
	-webkit-border-radius: 14px;
	-moz-border-radius: 14px;
	border-radius: 14px;
	border:2px solid #000;
	width:12px;
	height:12px;
	position: absolute;
	z-index: 50;
}





.sink2-lefthole, .sink2-middlehole, .sink2-righthole {
	-webkit-border-radius: 14px;
	-moz-border-radius: 14px;
	border-radius: 14px;
	border:2px solid #000;
	width:12px;
	height:12px;
	position: absolute;
	z-index: 50;
}


<?php


if ($sink == "Small Square" || $sink == "Large Square" ){
	$circle = -22;
}else{
	$circle = -18;
}



if ($faucet == "holes-two"){
 $leftfaucet = $middle1faucet-20;
 $rightfaucet = $middle1faucet+20;
}else{
 $leftfaucet = $middle1faucet-30;
 $rightfaucet = $middle1faucet+30;
}
?>

<?php if ($faucet == "holes-three" && $sink1class != "vessel"){ ?>
	.sink1-middlehole {
	    display: block;
	    left: <?php echo $middle1faucet;?>px;
	    top: -22px;
	}

	.sink1-righthole{
	    display: block;
	    left: <?php echo $rightfaucet;?>px;
	    top: <?php echo $circle;?>px;
	}
	.sink1-lefthole {
	    display: block;
	    left: <?php echo $leftfaucet;?>px;
	    top: <?php echo $circle;?>px;	
	}
<?php }elseif ($faucet == "holes-two"  && $sink1class != "vessel"){ ?>
	.sink1-middlehole {
	    display: block;
	    left: <?php echo $middle1faucet;?>px;
	    top:  -22px;
	}

	.sink1-righthole{
	    display: block;
	    left: <?php echo $rightfaucet;?>px;
	    top: <?php echo $circle;?>px;
	}
	.sink1-lefthole {
	    display: block;
	    left: <?php echo $leftfaucet;?>px;
	    top: <?php echo $circle;?>px;	
	}
<?php }elseif ($faucet == "holes-one"  && $sink1class != "vessel"){ ?>

	<?php
	if ($sink1class == 'large-square' || $sink1class == 'large-oval'){
		$sinkshape = '66.5';
	}

	if ($sink1class == 'small-square' || $sink1class == 'small-oval'){
		$sinkshape = '48';
	}

	?>

	.sink1-middlehole {
	    display: block;
	    left: <?php echo $sinkshape;?>px;
	    top:  -22px;
	}
	.sink1-lefthole, .sink1-righthole {
	display:none;
	}	
<?php }else{ ?>
	.sink1-lefthole, .sink1-middlehole, .sink1-righthole {
	display:none;
	}
	.sink2-lefthole, .sink2-middlehole, .sink2-righthole {
	display:none;
	}	
<?php } ?>

.sinkholeleft1, .sinkholeleft2 {
	background: #808184;
	position: absolute;
	top:62px;
	left:-4px;
	border:2px solid #808184;
	height:14px;
	width:14px;
	-webkit-border-radius: 10px;
	-moz-border-radius: 10px;
	border-radius: 10px;	
}

.sinkholeright1, .sinkholeright2 {
	background: #808184;
	position: absolute;
	border:2px solid #808184;
	height:14px;
	width:14px;
	-webkit-border-radius: 10px;
	-moz-border-radius: 10px;
	border-radius: 10px;	
}

.sinkholecenter1, .sinkholecenter2{
	background: #808184;
	position: absolute;
	top:-13px;
	left:-8px;
	border:2px solid #808184;
	height:14px;
	width:14px;
	-webkit-border-radius: 10px;
	-moz-border-radius: 10px;
	border-radius: 10px;		
}

<?php // lets do vessels ?>


<?php if ($f1degree == 1){ ?>
	.left45 {
		position: absolute;
		display: block;
		left:-<?php echo $f1distance;?>px;
		top: -<?php echo $f1distance+60;?>px;
	}
	.left45 .line1{
		height:<?php echo $f1distance+60;?>px;
		width:<?php echo $f1distance;?>px;
	}
<?php }else{ ?>
	.left45{
		display: none;
	}
<?php } ?>

<?php if ($f1degree == 2){ 
$v1half = (($v1holeinch/2)*8)-4;

?>

	.center1 {
		display: block;
	    border-left: 2px dashed #000;
	    height: <?php echo $f1distance;?>px;
	    left: 4px;
	    position: absolute;
	    top: -<?php echo $f1distance;?>px;
	}
<?php }else{ ?>
	.center1, .sinkholecenter1{
		display: none;
	}
<?php } ?>

<?php if ($f1degree == 3){ 
	$adjust = $f1distance-8;
	$hadjust = 46-(((($f1dis-4.5)*8)+24)-34);
	
	?>
	.right45 {
		position: absolute;
		display: block;
		left:<?php echo $f1distance-$adjust;?>px;
		top: -97px;
	}
	.right45 .line1{
		height:97px;
		width:<?php echo $f1distance-8;?>px;
	}
	.sinkholeright1{
		top:<?php echo $hadjust;?>px;
		left:<?php echo $f1distance-8;?>px;
	}
<?php }else{ ?>
	.right45{
		display: none;
	}
<?php } ?>



<?php if ($f2degree == 1){ ?>
	.left452 {
		position: absolute;
		display: block;
		left:-<?php echo $f2distance;?>px;
		top: -<?php echo $f2distance+60;?>px;
	}
	.left452 .line1{
		height:<?php echo $f2distance+60;?>px;
		width:<?php echo $f2distance;?>px;
	}
<?php }else{ ?>
	.left452{
		display: none;
	}
<?php } ?>

<?php if ($f2degree  == 2){ 	
$v2half = (($v2holeinch/2)*8)-4;

?>
	.center2 {
	    border-left: 2px dashed #000;
	    height: <?php echo $f2distance;?>px;;
	    left: <?php echo $v2half;?>px;
	    position: absolute;
	    top: -<?php echo $f2distance;?>px;
	}
<?php }else{ ?>
	.center2, .sinkholecenter2{
		display: none;
	}
<?php } ?>

<?php if ($f2degree == 3){ 
	$adjust = $f1distance-8;
	$hadjust = 46-(((($f1dis-4.5)*8)+24)-34);
	
	?>
	.right452 {
		position: absolute;
		display: block;
		left:<?php echo $f1distance-$adjust;?>px;
		top: -97px;
	}
	.right452 .line1{
		height:97px;
		width:<?php echo $f1distance-8;?>px;
	}
	.sinkholeright2{
		top:<?php echo $hadjust;?>px;
		left:<?php echo $f1distance-4;?>px;
	}
<?php }else{ ?>
	.right452{
		display: none;
	}
<?php } ?>


<?php if ($faucet == "holes-three" && $sinknum == 2  && $sink2class != "vessel2"){ ?>
	.sink2-middlehole {
	    display: block;
	    left: <?php echo $middle1faucet;?>px;
	    top: -22px;
	}

	.sink2-righthole{
	    display: block;
	    left: <?php echo $rightfaucet;?>px;
	    top: <?php echo $circle;?>px;
	}
	.sink2-lefthole {
	    display: block;
	    left: <?php echo $leftfaucet;?>px;
	    top: <?php echo $circle;?>px;	
	}
<?php }elseif ($faucet == "holes-two" && $sinknum == 2 && $sink2class != "vessel2"){ ?>
	.sink2-middlehole {
	    display: block;
	    left: <?php echo $middle1faucet;?>px;
	    top: -22px;
	}

	.sink2-righthole{
	    display: block;
	    left: <?php echo $rightfaucet;?>px;
	    top: <?php echo $circle;?>px;
	}
	.sink2-lefthole {
	    display: block;
	    left: <?php echo $leftfaucet;?>px;
	    top: <?php echo $circle;?>px;	
	}
<?php }elseif ($faucet == "holes-one" && $sinknum == 2 && $sink2class != "vessel2"){ ?>
	.sink2-middlehole {
	    display: block;
	    left: <?php echo $middle1faucet;?>px;
	    top: -22px;
	}
	.sink2-lefthole, .sink2-righthole {
	display:none;
	}	
<?php }else{ ?>
	.sink2-lefthole, .sink2-middlehole, .sink2-righthole {
	display:none;
	}
<?php } ?>


		</style> 
		</head> 
		 
		<body> 


		  <div class="container"> 
		  	<div class="header"> 
		  		<div class="headerLogo"><img class="logo" src="http://nestappeal.com/skin/frontend/rwd/default/images/logo.gif"></div> 
		  		<div class="headerDetails"> 
		  			<div class="phone">888.789.0560</div> 
		  			<div class="email">mail@nestappeal.com</div> 
		  		</div> 
		  	</div> 
		  	<div class="countertop"> 
		  	<center><a style="color:#000" href="<?php echo $designurl;?>">Edit this Countertop</a></center>
		  		<div class="countertopHeader"> 
		  			<h2>Your Vanity Counter Design</h2> 
		  			<div class="orderNumber">Order #<?php echo $orderid; ?></div>
		  		</div> 
		  		<div class="countertopRender">
		
		<?php // begin of slab ?>

		<div class="countertop-slab">

			<div class="cp-counter-left"><span class="edging"><?php echo $edgename; ?></span><span class="polish">Polished</span><span class="backsplash">4" Backsplash</span></div>		
			<div class="protect-width">
				<div class="shrink-main-width">
					<span class="shrink-width-span"><?php echo $width;?>"</span>
					<div class="horz-line"></div>
					<div class="cp-counter-back"><span class="backsplash">4" Backsplash</span><span class="polish">Polished</span><span class="edging"><?php echo $edgename; ?></span></div>
				</div>
			</div>
						<div row-height="myheight" boxsize="" class="cp-counter">
				<div class="sink1-display <?php echo $sink1class;?>">
					<div class="centerhole1" >
						<div class="left45"><div class="sinkholeleft1"></div>
						<svg class="line1" width="44" height="98">
	    				<line stroke-dasharray="5,5" y2="172" x2="112" y1="70" x1="4" stroke-miterlimit="10" stroke="#000" fill="none"/>
						</svg></div>
						<div class="right45"><div class="sinkholeright1"></div>
						<svg class="line1" width="30" height="98">
	    				<line stroke-dasharray="5,5" y2="151" x2="-44" y1="-37" x1="134" stroke-miterlimit="10" stroke="#000" fill="none"/>
						</svg></div>
						<div class="center1"><div class="sinkholecenter1"></div></div>	
					</div>				
					<div class="sink1-lefthole"></div><div class="sink1-middlehole"></div><div class="sink1-righthole"></div>
				</div>
				<div class="sink2-display <?php echo $sink2class;?>">
					<div class="centerhole2" >
						<div class="left452"><div class="sinkholeleft2"></div>
						<svg class="line1" width="44" height="98">
	    				<line stroke-dasharray="5,5" y2="172" x2="112" y1="70" x1="4" stroke-miterlimit="10" stroke="#000" fill="none"/>
						</svg></div>
						<div class="right452"><div class="sinkholeright2"></div>
						<svg class="line1" width="30" height="98">
	    				<line stroke-dasharray="5,5" y2="151" x2="-44" y1="-37" x1="134" stroke-miterlimit="10" stroke="#000" fill="none"/>
						</svg></div>
						<div class="center2"><div class="sinkholecenter2"></div></div>	
					</div>	
				<div class="sink2-lefthole"></div><div class="sink2-middlehole"></div><div class="sink2-righthole"></div></div>
				<div class="sink1-display-dash-line"></div>
				<div class="sink1-distance"><?php echo $sink1inch; ?>"</div>
				<div class="sink2-display-dash-line"></div>
				<div class="sink2-distance"><?php echo $sink2inch; ?>"</div>
				<div class="cp-text-front">
					
				</div>
			</div>
			<div class="cp-counter-right"><span class="backsplash">4" Backsplash</span><span class="polish">Polished</span><span class="edging"><?php echo $edgename; ?></span></div>
			<div class="shrink-main-height">
				<div class="vert-line"></div>
				<span class="shrink-height-span"><?php echo $height;?>"</span>	
			</div>
				<br clear="left">
				<div class="cp-counter-front"><span class="polish">Polished</span><span class="edging"><?php echo $edgename; ?></span></div>
				<br clear="left">
				<div class="cp-text-front" >FRONT</div>
		</div>

		<?php // end of slab ?>
		
		</div> 
		  	</div> 
		  	<div class="summary"> 
		  		<h3>Design Summary</h3> 
		  		<div class="specs"> 
		  			<span class="spec">Thickness:</span> <?php echo $thickness;?>cm<br> 
		  			<span class="spec">Stone:</span> <?php echo $slabname; ?><br> 
		  			
		  				<span class="spec">Sinks:</span> <?php echo $sinknames ?>
		  			
		  		</div> 
		  		<div class="specs right"> 

		  			<?php if ($faucetdes != '' && $sinknum > 0){ ?>
		  			<span class="spec">faucet 1:</span> <?php echo $faucetdes;?><br> 
		  			<?php if ($v1holeleft){ ?>
		  			sink hole diameter: <?php echo $v1holeinch;?> inches<br> 
					sink hole x: <?php echo $v1holeleft;?> inches from left edge<br> 
					sink hole y: <?php echo $v1holeback;?> inches from back edge<br>
					<?php if ($f1dis != ''){ ?> faucet hole: <?php echo $f1dis;?> inches from sink hole<br> <?php } ?>
					<?php } ?>
					<br> 
					<?php } ?>
					<?php if ($faucetdes != '' && $sinknum > 1){ ?>
					<span class="spec">faucet 2:</span> <?php echo $faucetdes;?><br> 
					<?php if ($v2holeleft){ ?>
					sink hole diameter: <?php echo $v2holeinch;?> inches<br>
					sink hole x: <?php echo $v2holeleft;?> inches from left edge<br> 
					sink hole y: <?php echo $v2holeback;?> inches from back edge<br>
					<?php if ($f2dis != ''){ ?> faucet hole: <?php echo $f2dis;?> inches from sink hole <br> <?php } ?>
					<?php } ?>
					<?php } ?>
		  		</div> 
		  		<div style="clear: both;"></div> 
		 
		  	</div> 
		  	<div class="footer"> 
			<div class="office-use">
			    <h3>FOR OFFICE USE ONLY:</h3>
			    <div class="specs">
			  <ul>
			    <li>Name</li>
			    <li>Phone</li>
			    <li>Email</li>
			  </ul>
			    </div>
			<div class="specs right">
			  <ul>
			    <li>Date of Pickup</li>
			    <li>Glue Sink:&nbsp;&nbsp;&nbsp;Yes&nbsp;&nbsp;/&nbsp;&nbsp;No</li>
			    <li>Sales Rep</li>
			  </ul>
			    </div>
			<div style="clear:both"></div>



		  	<strong>All Sales Are Final!</strong> 
			  	<ul> 
					<li>We custom cut stone to your specifications - we cannot accept returns. For that reason you must be certain that everything you have selected is correct and final. Once a slab is ordered it cannot be changed.</li> 
					<li>All of our products are natural stone. Colors between each slab can vary. Some granite and marble have a shiner polish than other granites or marbles. All Travertine slabs have cracks, pores, and fissures, sometimes filled with colored epoxy. That is part of the natural beauty of Travertine.</li> 
					<li>We write this warning not to scare but to make sure you have real expectations. If you need to change specifications before your slab is cut you will be charged $20 change fee. Changes impede our work flow and require extra time.</li> 
				</ul> 
				<div class="specs">
				  <ul>
			    <li>SIGNATURE</li>
			    </ul>
			    </div>
			    <div class="specs right">
			  <ul>
			    <li>DATE</li>
			    </ul>
			    </div><br><br><br><br><br><br>
		  	</div> 
		  </div> 
		</body> 
		</html>

		<?php
	}
}