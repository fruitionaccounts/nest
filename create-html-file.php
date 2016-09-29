<?php

$html_text = $_REQUEST['html'];


$order_details = $_REQUEST['order-details'];


$html_text_link = '<link rel="stylesheet" href="/skin/frontend/rwd/nestappeal/css/shower-order-style.css" />
';
$html_text_link .= ' <script src="http://code.jquery.com/jquery-latest.js"></script>
<script type="text/javascript" src="/skin/frontend/rwd/nestappeal/script/order-details.js"></script>
';


$html_text = base64_decode($html_text);

echo $html_text = $html_text_link.''.$html_text;


//$today = date("Y-m-d-H-i-s");
$fp = fopen($_SERVER['DOCUMENT_ROOT']."/order/ss-order-".$order_details.".html","wb");
fwrite($fp,$html_text);
fclose($fp);

/* $fp = fopen($_SERVER['DOCUMENT_ROOT'] . "/myText.txt","wb");
fwrite($fp,$html_text );
fclose($fp); */
?>