<?php
$page_id=566;$page_slug='page_order_ajax';
/*
 * @author Ravi Patel
 */
include("connect.php");

// include("../include/no_to_word.php");
$dispatch_id=$_REQUEST['id'];
$ctable_where	= "dispatch_id='".$_REQUEST['id']."' AND isDelete=0";
$ctable_r = $db->rp_getData("dispatch_item","*",$ctable_where,"",0);
$d="";
$discount="";

?>
<div id="print_info">
<div class="row">
<div class="col-sm-12">
<h4 align="center"><b>LR Detail</b></h4>
