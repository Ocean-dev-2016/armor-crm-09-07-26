<?php 
$page_id=581;$page_slug='manage_complain';
/*
 * @author Ravi Patel
 */
include("connect.php");
$ctable 	= "general_credit_note";

$ctable_where = "";
// Get the total number of rows in the table



$ctable_r = $db->rp_getData($ctable,"*","isDelete=0 AND id='".$_REQUEST['id']."'",0);  
$ctable_d=mysqli_fetch_assoc($ctable_r);
extract($ctable_d);
$get_customer_id=$db->rp_getValue("general_credit_note","customer_id","isDelete=0 AND id='".$_REQUEST['id']."'");

$get_customer_info_r=$db->rp_getData("executive","*","isDelete=0 AND id='".$get_customer_id."'",0);
$get_customer_info_d=mysqli_fetch_assoc($get_customer_info_r);
extract($get_customer_info_d);

  ?> 
<style>
.mainDiv, table{
  height: auto;   
  width:100%;
  font-family: Calibri,sans-serif;
  font-style: normal;
  font-weight: 400;
  padding: 0;
  text-decoration: none;
  font-size: 10pt;
  margin:auto;
  padding:auto;
}
tr{height: 30px;}
table , td, th { border-collapse: collapse;border: 1px solid #000;}
td, th {  padding: 5px;}
th { border: 1px solid #595959;background: #f0e6cc;}
.text-right{text-align: right;}
.center{text-align:center;}
.space{ padding: 10px;}
.no-border{border-bottom: 1px solid #fff;}
.no-border-right{border-right: none;}
.no-border-left{border-left: none;}
h2
{
	text-transform: uppercase;
	margin-bottom: 0px;
}
</style>
<form action="" name="frm"  method="post">
    <table id="datatable_1" class="table table-striped table-bordered table-hover" width="100%">

      <tr>
      <td colspan="16" class="header-img">
        <img style="width: 100%;padding: 0px !important;"  src="<?= VIEW_LOGO_All ?>">
      </td>
    </tr>
    <tr>
     
      <td colspan="16" class="no-border-top no-border-right height-5 center"><b>GST - <?= COMPANY_GST ?></b></td>
     
    </tr>

    <!-- <tr>
    
      <td colspan="16" class="no-border-bottom  no-border-left height-5 center"><b>PAN - <?= COMPANY_PAN ?></b></td>
     
    </tr> -->

      <tr>
        <td colspan="16" align="center"><h2><strong>CREDIT NOTE</strong></h2></td>
      </tr>
      <tr>
        <td colspan="10" class="no-border-right" width="60%">
          <strong><?=$company_name?></strong><br>
          Address : </br>
          <?php echo wordwrap($address,40,"<br>\n")."  <br/>".$city." - ".$zip." , ".$state." , ".$country ?>
          <br>
          <br>
          <strong>Pan No : </strong><?=$pan?><br>
          <strong>Gst No : </strong><?=$gst?>
        </td>
        <td colspan="6" class="no-border-left" width="40%" style="vertical-align: top;">
          <strong>Credit Note Date  : </strong><?=date('d-m-Y',strtotime($payment_date))?><br>
          <strong>Credit Note Number : </strong><?=$receipt_no?><br>
         <!--  <strong>Currency : </strong><br> -->
          <strong>Ref. No. : </strong><?=$ref_no?><br>
          <strong>Amount   : </strong><?=CURR ." ".$paid_amount?><br>
          <br>
          <br>
          

        </td>
      </tr>
      <tr>
        <td colspan="14" width="80%" align="center"><strong>Particulars</strong></td>
        <td colspan="2" width="20%" align="center"><strong>Amount</strong></td>
      </tr>
      <tr>
        <?php 
          $get_discount_type_r=$db->rp_getData("discount_type","name","isDelete=0 AND id IN(".$discount_type_id.")");
        $get_names=array();
        while ($get_discount_type_d=mysqli_fetch_assoc($get_discount_type_r)) 
        {
            $get_names[]=$get_discount_type_d["name"];
        }
        $get_names=implode(",", $get_names);
        

          ?>
        <td colspan="14" width="80%"><strong><?=$company_name?>
          <br>Narration : <?=$get_names?>
          <br>Remarks   : <?=$remark?>
        </td>
        <td colspan="2" width="20%" align="center"><?=CURR." ".$db->rp_num($paid_amount)?></td>
      </tr>

      <tr>
        <td colspan="14" align="right" width="80%">
          <strong>Total</strong>
        </td>
        <td colspan="2" width="20%" align="center"><strong><?=CURR ." ".$db->rp_num($paid_amount)?></strong></td>
      </tr>
      <tr>
          <td colspan="5"  class="no-border-right text-left" width="33%">
            <br><br><br><br>
            <strong>Prepared By</strong>  
          </td>

          <td colspan="5"  class="no-border-right no-border-left" width="33%">
            <br><br><br><br>
            <center><strong>Checked By</strong></center>  
          </td>

          <td colspan="6"  align="right" class=" no-border-left" width="33%">

            <strong style="margin-right: 20px;">For, <?= CLIENT_BRAND_NAME ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br></strong>

            <br><br><br>
            <strong style="margin-right: 20px;">
            (Authorised SIgnatory)
            </strong> 
          </td>
        </tr>
       
  </form>
  <?php

?>