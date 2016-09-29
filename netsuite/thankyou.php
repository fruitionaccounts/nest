<?php 

	require_once('../app/Mage.php'); //Path to Magento 
	umask(0);
	Mage::app();

	$staticBlockTitle = Mage::getModel('cms/block')->load('counter_quote_thankyou')->getContent();

	echo '<center>'.$staticBlockTitle.'</center>';