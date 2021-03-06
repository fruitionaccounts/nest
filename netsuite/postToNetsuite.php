<html>
<head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
</head>
<body>
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once('../app/Mage.php'); //Path to Magento
umask(0);
Mage::app();
$firstname="";
$lastname="";
$email="";
$quoteamount="";
$diagram="";
$stonecolor="";

if( isset($_POST['firstname']) )
{
     $firstname = $_POST['firstname'];
}
if( isset($_POST['lastname']) )
{
     $lastname = $_POST['lastname'];
}
if( isset($_POST['email']) )
{
     $email = $_POST['email'];
}

if( isset($_POST['coupon']) )
{
        // Get the rule by ID
        $rule = Mage::getModel('salesrule/rule')->load(2);
         
        $generator = Mage::getModel('salesrule/coupon_massgenerator');
         
        $generator->setDash( !empty($parameters['dash_every_x_characters'])? (int) $parameters['dash_every_x_characters'] : 0);
        $generator->setLength( !empty($parameters['length'])? (int) $parameters['length'] : 6);
        $generator->setPrefix( !empty($parameters['prefix'])? $parameters['prefix'] : '');
        $generator->setSuffix( !empty($parameters['suffix'])? $parameters['suffix'] : '');
         
        $rule->setCouponCodeGenerator($generator);
        $rule->setCouponType( Mage_SalesRule_Model_Rule::COUPON_TYPE_AUTO );
         
        $count = !empty($parameters['count'])? (int) $parameters['count'] : 1;
        $codes = array();
        for( $i = 0; $i < $count; $i++ ){
          $coupon = $rule->acquireCoupon();
          $coupon
            ->setType(Mage_SalesRule_Helper_Coupon::COUPON_TYPE_SPECIFIC_AUTOGENERATED)
            ->save();
         
          $code = $coupon->getCode();
        }
        $couponcode = $code;
 

}
if( isset($_POST['quoteamount']) )
{
     $quoteamount = $_POST['quoteamount'];
}
if( isset($_POST['diagram']) )
{
     $diagram = $_POST['diagram'];
}
if( isset($_POST['stonecolor']) )
{
     $stonecolor = $_POST['stonecolor'];
}

// get cookie
$weblead = $_COOKIE['leadsource'];

$quoteamount = str_replace("$", "", $quoteamount);


?>
<form style="display: none;" id="main_form" action="https://forms.na1.netsuite.com/app/site/crm/externalleadpage.nl?compid=1192565&formid=4&h=f8a181eeb49c2fc3c9f2" method="post">
<input name="firstname" type="hidden" value='<?php echo $firstname; ?>' size="25"><br>
<input name="lastname" type="hidden" value='<?php echo $lastname; ?>' size="25"><br>
<input name="email" type="hidden" value='<?php echo $email; ?>' size="25"><br>
<input id="couponCode" name="custentity12" value='<?php echo $couponcode; ?>' type="hidden" size="25"><br>
<input name="weblead" type="hidden" value="T" size="25"><br>
<input name="leadsource" type="hidden" value="<?php echo $weblead; ?>" size="25">
<input name="custentity8" type="hidden" value='<?php echo $quoteamount; ?>' size="25">
<input name="custentity9" type="hidden" value='<a href="<?php echo $diagram; ?>">View Diagram</a>' size="25">
<input name="custentity11" type="hidden" value='<?php echo $stonecolor; ?>' size="25">
<input style="display: none;" type="submit" value="submit">
</form>
Please Wait...<br><img src="20-0.gif">
<script>
    $( "#main_form" ).submit();
</script>

</body>
</html>
