<?php $page_id=597;$page_slug='daily_sales_report_page'; include
("connect.php"); 
$ctable   = "attendance"; 
$ctable1  = "visit"; 
$ctable2  ="no_order_inquiry"; 
$ctable3  = "followup"; 
$ctable4  = "quotation_detail";
$ctable5  = "orders"; 
$ctable6  = "followup"; 
$ctable7  = "dispatch_detail";
$ctable8  = "packing_slip"; 
$ctable9  = "invoice_new"; 
$ctable10 ="complain";
$ctable11 = "expense"; 
$ctable12 = "leave_request"; 
$ctable13 ="executive";

 $sales_id=$_REQUEST['sales_id'];
//$date = date('Y-m-d');
$date = $_REQUEST['date'];
$date1 = $_REQUEST['date1'];
$salesexename = $db->rp_getValue("sales_executive","name","id='".$_REQUEST["sales_id"]."'",0);
$salesexephone = $db->rp_getValue("sales_executive","phone","id='".$_REQUEST["sales_id"]."'",0);
$sm_id = $db->rp_getValue("sales_executive","sm_id","id='".$_REQUEST["sales_id"]."'",0);
$asm_id = $db->rp_getValue("sales_executive","asm_id","id='".$_REQUEST["sales_id"]."'",0);
$so_id = $db->rp_getValue("sales_executive","so_id","id='".$_REQUEST["sales_id"]."'",0);
$se_id = $db->rp_getValue("sales_executive","se_id","id='".$_REQUEST["sales_id"]."'",0);

 if($sm_id==0)
{
$type="Regional Sales Manager";
}
else if($sm_id!=0 && $asm_id==0)
{
$type="Business Development Manager";
}
else if($sm_id!=0 && $asm_id!=0 && $so_id==0)
{
  $type="Area Sales Manager";
}
else
{
  $type="Sales Officer";
}

$ctable_where = "";
$ctable_where .="sales_id=".$sales_id." AND isDelete=0";
$ctable_where .= " AND DATE(date_time) >= '".date("Y-m-d",strtotime($date))."'";
$ctable_where .= " AND DATE(date_time) <= '".date("Y-m-d",strtotime($date))."'";
$ctable_r = $db->rp_getData($ctable,"*",$ctable_where,"date_time DESC",0);
$ctable_r1 = $db->rp_getData($ctable,"*",$ctable_where,"date_time DESC",0);

/*visit*/
$ctable_where_visit = "";
$ctable_where_visit .="user_id=".$sales_id." AND isDelete=0";
// $ctable_where_visit .= " AND DATE(created_date) = '".date("Y-m-d",strtotime($date))."'";
$ctable_where_visit .= " AND DATE(created_date) >= '".date("Y-m-d",strtotime($date))."' ";
$ctable_where_visit .= " AND DATE(created_date) <= '".date("Y-m-d",strtotime($date))."'";
$ctable_r2 = $db->rp_getData($ctable1,"*",$ctable_where_visit,"id DESC",0);
/*visit*/

/*Prospects*/
$ctable_where_prospect = "";
//$ctable_where_prospect .="sales_executive_id=".$sales_id." AND isDelete=0 ";
$ctable_where_prospect .="inquiry_assign_to=".$sales_id." AND isDelete=0 ";
$ctable_where_prospect .= " AND DATE(inquiry_date) >= '".date("Y-m-d",strtotime($date))."' ";
 $ctable_where_prospect .= " AND DATE(inquiry_date) <= '".date("Y-m-d",strtotime($date))."' AND inquiry_lead_flag = '-1'";
$ctable_r3 = $db->rp_getData($ctable2,"*",$ctable_where_prospect,"id DESC",0);
/*Prospects*/


/*inquiry*/
$ctable_where_inquiry = "";
//$ctable_where_inquiry .="sales_executive_id=".$sales_id." AND isDelete=0 ";
$ctable_where_inquiry .="inquiry_assign_to=".$sales_id." AND isDelete=0 ";
$ctable_where_inquiry .= " AND DATE(inquiry_date) >= '".date("Y-m-d",strtotime($date))."' AND inquiry_lead_flag = '0' ";
$ctable_where_inquiry .= " AND DATE(inquiry_date) <= '".date("Y-m-d",strtotime($date))."'";
$ctable_r4 = $db->rp_getData($ctable2,"*",$ctable_where_inquiry,"id DESC",0);
/*inquiry*/

/*leads*/
$ctable_where_lead = "";
//$ctable_where_lead .="sales_executive_id=".$sales_id." AND isDelete=0 ";
$ctable_where_lead .="inquiry_assign_to=".$sales_id." AND isDelete=0 ";
$ctable_where_lead .= " AND DATE(inquiry_date) >= '".date("Y-m-d",strtotime($date))."' ";
$ctable_where_lead .= " AND DATE(inquiry_date) <= '".date("Y-m-d",strtotime($date))."' AND inquiry_lead_flag = '1'";
$ctable_r5 = $db->rp_getData($ctable2,"*",$ctable_where_lead,"id DESC",0);
/*leads*/

/*followup*/
$ctable_where_followup = "";
$ctable_where_followup .="user_id=".$sales_id." AND isDelete=0";
$ctable_where_followup .= " AND DATE(followup_date) >= '".date("Y-m-d",strtotime($date))."'";
$ctable_where_followup .= " AND DATE(followup_date) <= '".date("Y-m-d",strtotime($date))."'";
$ctable_r6 = $db->rp_getData($ctable3,"*",$ctable_where_followup,"id DESC",0);
/*followup*/


/*quotation*/
$ctable_where_quotation = "";
$ctable_where_quotation .="sales_id=".$sales_id." AND isDelete=0";
$ctable_where_quotation .= " AND DATE(quotation_date) >= '".date("Y-m-d",strtotime($date))."'";
$ctable_where_quotation .= " AND DATE(quotation_date) <= '".date("Y-m-d",strtotime($date))."'";
$ctable_r7 = $db->rp_getData($ctable4,"*",$ctable_where_quotation,"id DESC",0);
/*quotation*/

/*orders*/
$ctable_where_orders = "";
$ctable_where_orders .="sales_id=".$sales_id." AND isDelete=0";
$ctable_where_orders .= " AND DATE(order_date) >= '".date("Y-m-d",strtotime($date))."'";
$ctable_where_orders .= " AND DATE(order_date) <= '".date("Y-m-d",strtotime($date))."'";
$ctable_r8 = $db->rp_getData($ctable5,"*",$ctable_where_orders,"id DESC",0);
/*orders*/

/*dispatch*/
$ctable_where_dispatch = "";
$ctable_where_dispatch .="sales_id=".$sales_id." AND isDelete=0";
$ctable_where_dispatch .= " AND DATE(dispatch_date) >= '".date("Y-m-d",strtotime($date))."'";
$ctable_where_dispatch .= " AND DATE(dispatch_date) <= '".date("Y-m-d",strtotime($date))."'";
$ctable_r13 = $db->rp_getData($ctable7,"*",$ctable_where_dispatch,"id DESC",0);
/*dispatch*/

/*packing slip*/
$ctable_where_packingslip = "";
$sales_dispatch_ids=array();
$dispatch_r=$db->rp_getData("dispatch_detail","*","isDelete=0 AND sales_id='".$sales_id."' ");
while($dispatch_d=mysqli_fetch_assoc($dispatch_r))
{
  $sales_dispatch_ids[]=$dispatch_d['id'];
}
// $ctable_where_packingslip .="sales_id=".$sales_id." AND isDelete=0";
$ctable_where_packingslip .=" dispatch_id IN (".implode(",",$sales_dispatch_ids).") AND isDelete=0";
$ctable_where_packingslip .= " AND DATE(packing_slip_date) >= '".date("Y-m-d",strtotime($date))."'";
$ctable_where_packingslip .= " AND DATE(packing_slip_date) <= '".date("Y-m-d",strtotime($date))."'";
$ctable_r14 = $db->rp_getData($ctable8,"*",$ctable_where_packingslip,"id DESC",0);
/*packing slip*/

/*invoice*/
$ctable_where_invoice = "";
$ctable_where_invoice .="sales_id=".$sales_id." AND isDelete=0";
$ctable_where_invoice .= " AND DATE(invoice_date) >= '".date("Y-m-d",strtotime($date))."'";
$ctable_where_invoice .= " AND DATE(invoice_date) <= '".date("Y-m-d",strtotime($date))."'";
$ctable_r15 = $db->rp_getData($ctable9,"*",$ctable_where_invoice,"id DESC",0);
/*invoice*/


/*customer receipt*/
$ctable_where_customer_receipt = "";
$ctable_where_customer_receipt .="sales_executive_id=".$sales_id." AND isDelete=0";
$ctable_where_customer_receipt .= " AND DATE(payment_date) >= '".date("Y-m-d",strtotime($date))."'";
$ctable_where_customer_receipt .= " AND DATE(payment_date) <= '".date("Y-m-d",strtotime($date))."'";
$ctable_customer_receipt = $db->rp_getData($ctable14,"*",$ctable_where_customer_receipt,"id DESC",0);
/*customer receipt*/


/*complain*/
$ctable_where_complain = "";
$ctable_where_complain .="user_id=".$sales_id." AND isDelete=0";
$ctable_where_complain .= " AND DATE(complain_date) >= '".date("Y-m-d",strtotime($date))."'";
$ctable_where_complain .= " AND DATE(complain_date) <= '".date("Y-m-d",strtotime($date))."'";
$ctable_r16 = $db->rp_getData($ctable10,"*",$ctable_where_complain,"id DESC",0);
/*complain*/

/*expense*/
$ctable_where_expense = "";
$ctable_where_expense .="sales_executive_id=".$sales_id." AND isDelete=0";
$ctable_where_expense .= " AND DATE(expense_date) >= '".date("Y-m-d",strtotime($date))."'";
$ctable_where_expense .= " AND DATE(expense_date) <= '".date("Y-m-d",strtotime($date))."'";
$ctable_r17 = $db->rp_getData($ctable11,"*",$ctable_where_expense,"id DESC",0);
/*expense*/

/*leave request*/
$ctable_where_leaverequest = "";
$ctable_where_leaverequest .="sales_executive_id=".$sales_id." AND isDelete=0";
$ctable_where_leaverequest .= " AND DATE(start_date) >= '".date("Y-m-d",strtotime($date))."'";
$ctable_where_leaverequest .= " AND DATE(start_date) <= '".date("Y-m-d",strtotime($date))."'";
$ctable_r18 = $db->rp_getData($ctable12,"*",$ctable_where_leaverequest,"id DESC",0);
/*leave request*/

/*new customer*/
$ctable_where_newcustomer = "";
$ctable_where_newcustomer .="seid=".$sales_id." AND isDelete=0";
$ctable_where_newcustomer .= " AND DATE(created_date) >= '".date("Y-m-d",strtotime($date))."'";
$ctable_where_newcustomer .= " AND DATE(created_date) <= '".date("Y-m-d",strtotime($date))."'";
$ctable_r19 = $db->rp_getData($ctable13,"*",$ctable_where_newcustomer,"id DESC",0);
/*new customer*/

/*followup*/
$ctable_where_Inquiry_followup = "";
$ctable_where_Inquiry_followup .="user_id=".$sales_id." AND isDelete=0 AND reference_table='no_order_inquiry' ";
$ctable_where_Inquiry_followup .= " AND DATE(followup_date) >= '".date("Y-m-d",strtotime($$date))."'";
$ctable_where_Inquiry_followup .= " AND DATE(followup_date) <= '".date("Y-m-d",strtotime($date))."'";
// $ctable_where_Inquiry_followup .= " OR DATE(created_date) = '".date("Y-m-d",strtotime($_REQUEST['date']))."'";
$ctable_r9 = $db->rp_getData($ctable6,"*",$ctable_where_Inquiry_followup,"id DESC",0);
/*followup*/

/*followup*/
$ctable_where_lead_followup = "";
$ctable_where_lead_followup .="user_id=".$sales_id." AND isDelete=0 AND reference_table='no_order_inquiry' ";
$ctable_where_lead_followup .= " AND DATE(followup_date) >= '".date("Y-m-d",strtotime($date))."'";
$ctable_where_lead_followup .= " AND DATE(followup_date) <= '".date("Y-m-d",strtotime($date))."'";
// $ctable_where_lead_followup .= " OR DATE(created_date) = '".date("Y-m-d",strtotime($_REQUEST['date']))."')";
$ctable_r10 = $db->rp_getData($ctable6,"*",$ctable_where_lead_followup,"id DESC",0);
/*followup*/

/*followup*/
$ctable_where_Prospects_followup = "";
$ctable_where_Prospects_followup .="user_id=".$sales_id." AND isDelete=0 AND reference_table='no_order_inquiry' ";
$ctable_where_Prospects_followup .= " AND DATE(followup_date) >= '".date("Y-m-d",strtotime($date))."'";
$ctable_where_Prospects_followup .= " AND DATE(followup_date) <= '".date("Y-m-d",strtotime($date))."'";
// $ctable_where_Prospects_followup .= " OR DATE(created_date) = '".date("Y-m-d",strtotime($_REQUEST['date']))."')";
$ctable_r11 = $db->rp_getData($ctable6,"*",$ctable_where_Prospects_followup,"id DESC",0);
/*followup*/

/*followup*/
$ctable_where_Q_followup = "";
$ctable_where_Q_followup .="user_id=".$sales_id." AND reference_table='quotation_detail' AND isDelete=0";
$ctable_where_Q_followup .= " AND DATE(followup_date) >= '".date("Y-m-d",strtotime($date))."'";
$ctable_where_Q_followup .= " AND DATE(followup_date) <= '".date("Y-m-d",strtotime($date))."'";
// $ctable_where_Inquiry_followup .= " OR DATE(created_date) = '".date("Y-m-d",strtotime($_REQUEST['date']))."')";
$ctable_r12 = $db->rp_getData($ctable6,"*",$ctable_where_Q_followup,"id DESC",0);
/*followup*/


// visit
 $in_time = $db->rp_getData("attendance","date_time","sales_id='".$_REQUEST['sales_id']."' AND DATE(date_time)='".date('Y-m-d',strtotime($_REQUEST['date']))."' AND inout_status='In'","",0);
  $in = array();
  while($in_time_d = mysqli_fetch_assoc($in_time))
  {
    $in[] = $in_time_d['date_time'];
  }
  // print_r($in); 

  $out_time = $db->rp_getData("attendance","date_time","sales_id='".$_REQUEST['sales_id']."' AND DATE(date_time)='".date('Y-m-d',strtotime($_REQUEST['date']))."' AND inout_status='Out'","",0);
  $out = array();
  while($out_time_d = mysqli_fetch_assoc($out_time))
  {
    $out[] = $out_time_d['date_time'];
  }
  // print_r($out);
  $diffrence ="00:00:00";
  $first_in=$in[0];
  $first_out=$out[0];
  $total_in=sizeof($in);
  $total_out=sizeof($out);
  $last_out=$out[$total_out-1];
  $total_in_out=$total_in+$total_out;

  for($i=0;$i<sizeof($in);$i++)
  {
    for($j=0;$j<sizeof($out);$j++)
    {
      if($i==$j)
      { 
        $in_date = date("Y-m-d",strtotime($in[$i]));        
        $out_date = date("Y-m-d",strtotime($out[$j]));       
        if(strtotime($in_date)==strtotime($out_date))
        {

          $in_time=$in[$i];
          $out_time=$out[$j];                   
          // Formulate the Difference between two dates                           
          $diff = abs(strtotime($out_time) - strtotime($in_time));  
            
          // To get the year divide the resultant date into 
          // total seconds in a year (365*60*60*24) 
          $years = floor($diff / (365*60*60*24));  

          // To get the month, subtract it with years and 
          // divide the resultant date into 
          // total seconds in a month (30*60*60*24) 
          $months = floor(($diff - $years * 365*60*60*24)  / (30*60*60*24)); 
            
          // To get the day, subtract it with years and  
          // months and divide the resultant date into 
          // total seconds in a days (60*60*24) 
          $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24)); 
            
          // To get the hour, subtract it with years,  
          // months & seconds and divide the resultant 
          // date into total seconds in a hours (60*60) 
          $hours = floor(($diff-$years*365*60*60*24-$months*30*60*60*24-$days*60*60*24)/(60*60));  
            
          // To get the minutes, subtract it with years, 
          // months, seconds and hours and divide the  
          // resultant date into total seconds i.e. 60 
          $minutes = floor(($diff - $years * 365*60*60*24-$months*30*60*60*24-$days*60*60*24-$hours*60*60)/ 60);  
            
          // To get the minutes, subtract it with years, 
          // months, seconds, hours and minutes  
          $seconds = floor(($diff-$years*365*60*60*24-$months*30*60*60*24-$days*60*60*24-$hours*60*60-$minutes*60));  
          
          $diff=$hours.":".$minutes.":".$seconds;
          $secs = strtotime($diffrence)-strtotime("00:00:00");
          $diffrence = date("H:i:s",strtotime($diff)+$secs)." ";
        }
      }           
    }

    if(sizeof($in)>sizeof($out) && $i+1==sizeof($in))  
    {
      $current_time=date("H:i:s");
      $running_in_time=date("H:i:s",strtotime($in[$i]));

      $diff = abs(strtotime($current_time) - strtotime($running_in_time));  
            
          // To get the year divide the resultant date into 
          // total seconds in a year (365*60*60*24) 
          $years = floor($diff / (365*60*60*24));  

          // To get the month, subtract it with years and 
          // divide the resultant date into 
          // total seconds in a month (30*60*60*24) 
          $months = floor(($diff - $years * 365*60*60*24)  / (30*60*60*24)); 
            
          // To get the day, subtract it with years and  
          // months and divide the resultant date into 
          // total seconds in a days (60*60*24) 
          $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24)); 
            
          // To get the hour, subtract it with years,  
          // months & seconds and divide the resultant 
          // date into total seconds in a hours (60*60) 
          $hours = floor(($diff-$years*365*60*60*24-$months*30*60*60*24-$days*60*60*24)/(60*60));  
            
          // To get the minutes, subtract it with years, 
          // months, seconds and hours and divide the  
          // resultant date into total seconds i.e. 60 
          $minutes = floor(($diff - $years * 365*60*60*24-$months*30*60*60*24-$days*60*60*24-$hours*60*60)/ 60);  
            
          // To get the minutes, subtract it with years, 
          // months, seconds, hours and minutes  
          $seconds = floor(($diff-$years*365*60*60*24-$months*30*60*60*24-$days*60*60*24-$hours*60*60-$minutes*60));  
      
          $diff=$hours.":".$minutes.":".$seconds;
          

          $secs = strtotime($diffrence)-strtotime("00:00:00");
          $diffrence = date("H:i:s",strtotime($diff)+$secs)." Running";
    }
  }

// visit
 ?>
<!-- zoom css -->
<!-- <link rel="stylesheet" type="text/css" href="zoom-image/css/style.css" />
<link rel="stylesheet" type="text/css" href="zoom-image/cloud-zoom/cloud-zoom.css" />
<link rel="stylesheet" type="text/css" href="zoom-image/fancybox/jquery.fancybox-1.3.4.css" />
<script src="zoom-image/js/cufon-yui.js" type="text/javascript"></script> --> 
<?php include("include_css.php"); ?>
<style type="text/css">
  /*.no-border td
  {
    border:none!important;   
    font-size: 20px;
    font-weight: 700;
  }
  .no-border td.value
  {
     font-size: 20px;
    font-weight: 700;
  }*/
  .pad-0
  {
    padding: 0px!important;
  }
</style>
<!-- <link rel="stylesheet" href="<?=ADMINSITEURL?>css/lightbox.css" /> -->
<!-- zoom css -->

 <div class="col-md-12 col-xs-12 col-sm-12 pad-0" style="margin: 0">
    <h3><b>Daily Report</b></h3>
    </div>

    <div class="col-md-12 col-xs-12 col-sm-12 pad-0">
      <div class="col-md-6 col-xs-6 col-sm-6 pad-0">
          <table class="table table-striped table-bordered mt-15 ">
            <tr class="no-border">
              <td>Name</td>
              <td>:-</td>
              <td class="value"><?= ucfirst($salesexename);?></td>
            </tr>
            <tr class="no-border">
                <td>Type</td>
                <td>:-</td>
                <td class="value"><?= $type;?></td>
            </tr>
            <tr class="no-border">
                <td>Mobile No.</td>
                <td>:-</td>
                <td class="value"><?= $salesexephone;?></td>
              </tr>
            <tr class="no-border">
                <td>Total Visit</td>
                <td>:-</td>
                <td class="value"><?= $db->rp_getTotalRecord($ctable1,$ctable_where_visit);?></td>
              </tr>
        </table>
      </div>

      <div class="col-md-6 col-xs-6 col-sm-6">
          <table class="table table-striped table-bordered mt-15 ">
            <tr class="no-border">
                <td>First In</td>
                <td>:-</td>
                <td class="value"><?= (!empty($in))?date("d-m-Y h:i A",strtotime($first_in)):"-";?></td>
            </tr>
            <tr class="no-border">
                <td>Last Out</td>
                <td>:-</td>
                <td class="value"><?= (!empty($out))?date("d-m-Y h:i A",strtotime($last_out)):"-";?></td>
              </tr>
            <tr class="no-border">
                <td>Total In & Out</td>
                <td>:-</td>
                <td class="value"><?= $total_in_out;?></td>
              </tr>
            <tr class="no-border">
                <td>Total Working Time</td>
                <td>:-</td>
                <td class="value"><?= $diffrence;?></td>
              </tr>
          </table>
      </div>
    </div>


    <!-- attendance --> 
    <div class="col-md-12 col-xs-12 col-sm-12 pad-0" style="margin-bottom: 20px;">
      <h3><b>Attendance</b></h3>
    </div>

    <table class="table table-striped table-bordered mt-15 ">
      <thead>
          <tr>
              <th>No.</th>
              <th>In/Out</th>
              <th>Date and Time</th>
              <th>Location Map</th>
              <th>Address</th>
              <th>Image</th>
          </tr>
      </thead>
      <tbody>
        <?php
          if(mysqli_num_rows($ctable_r)>0)
          {
            while($ctable_d = mysqli_fetch_array($ctable_r))
            {
              ?>
              <?php 
                if ($ctable_d['image_path']!="" && file_exists(ATTENDANCE.$ctable_d['image_path'])) {
                  $img = ATTENDANCE.$ctable_d['image_path'];
                }
                else
                {
                  $img = $ctable_d['image_path'] = DEFAULTIMG;
                }
                ?>
                <tr>
                  <td><?php echo ++$count; ?></td>
                  <td><?php echo $ctable_d['inout_status']; ?></td>
                  <td><?php echo date("d-m-Y H:i:s a",strtotime($ctable_d['created_date'])); ?></td>
                  <td>
                      <a class="mapbtn" data-app_address="<?php echo stripslashes($ctable_d['app_address']); ?>" data-lat="<?php echo stripslashes($ctable_d['latitude']); ?>" data-long="<?php echo stripslashes($ctable_d['longitude']); ?>" data-date="<?=date("d M H:i",strtotime($ctable_d['created_date']));?>" data-salesexename="<?=$db->rp_getValue("sales_executive","name","id='".$ctable_d["sales_id"]."'",0);?>" data-toggle="modal" data-target="#OpenMap">
                          <img src="<?=SITEURL?>resource/map.png" style="height: 80px;">
                      </a>
                  </td>
                  <td><?php echo $ctable_d['app_address']; ?></td>
                  <td>
                    <a href="<?=$img?>" data-lightbox="attendance<?=$count?>" data-title="attendance <?=$ctable_d['id']?>"><img src="<?=$img?>" style="height: 80px;border-radius: 5px;border:1px solid #909090;"></a>
                  </td>
              </tr>
              <?php
            }
          }
          else
          {
            ?>
            <tr>
              <td align="center" colspan="6"><?php echo "No Attendance Found";?></td>
            </tr>
          <?php
        }
        ?>
      </tbody>
    </table>
    <!-- attendance -->

    <!-- total Prospect -->
    <hr/>
    <div class="col-md-12 col-xs-12 col-sm-12 pad-0" style="margin-bottom: 20px;">
      <h3><b>Raw Data (<?= $db->rp_getTotalRecord($ctable2,$ctable_where_prospect); ?>)</b></h3>
    </div>
    <table id="datatable_1" class="table table-striped table-bordered table-hover">
          <thead>
             <tr>
              <th>No.</th>
              <th>Inquiry No.</th>
              <th>Company Name</th>
              <th>Persoan Name</th>
              <th>Phone</th>
              <th>Country</th>
              <th>State</th>
              <th>City</th>
              <th>Inquiry Date</th>
              <th>Status</th>
            </tr>
          </thead>
          <?php
          if(mysqli_num_rows($ctable_r3)>0)
          {
              $count = 0;            
              while($ctable_d3 = mysqli_fetch_array($ctable_r3))
              {
                $inquiry_status_array = array("0"=>"Generated","1"=>"In Followup","3"=>"Buy Later","4"=>"Hot","5"=>"Cold","6"=>"Warm","-1"=>"Not Interested","-2"=>"Non Relavent","11"=>"Lost");
                ?>
                <tr>
                  <td><?php echo ++$count; ?></td>                
                  <td>#INQ/<?php  echo $ctable_d3['id']; ?></td>
                 <td><?php echo $ctable_d3['company_name'] ?></td>
                 <td><?php echo $ctable_d3['person_name'] ?></td>              
                 <td><?php echo $ctable_d3['mobile_number'] ?></td>              
                 <td><?php echo $ctable_d3['country'] ?></td>              
                 <td><?php echo $ctable_d3['state'] ?></td>              
                 <td><?php echo $ctable_d3['city'] ?></td>              
                 <td><?php if($ctable_d3['datetime']!="0000-00-00 00:00:00"){ echo date('d-m-Y',strtotime($ctable_d3['datetime'])); } else{ echo "";} ?></td>   
                 <td><?php echo $inquiry_status_array[$ctable_d3['status']] ?></td>        
                </tr>
                <?php
              }
          }           
          else
          {
            ?>
            <tr>
              <td colspan="10" class="text-center">No Raw Data Found</td>
            </tr>
            <?php
          }
          ?>
    </table>

    <div class="col-md-12 col-xs-12 col-sm-12 pad-0" style="margin-bottom: 20px;">
      <h3><b>Raw Data Followup </b></h3>
    </div>
     <table id="datatable_1" class="table table-striped table-bordered table-hover">
        <thead>
           <tr>
            <th>No.</th>
            <th>Inquiry No.</th>
            <th>Customer Name</th>
            <th>Mobile No </th>
            <th>Sales Person Name</th>
            <th>Description</th>
            <th>Through</th>
            <th>Date and Time</th>
            <th>Response</th>
          </tr>
        </thead>
        <?php
        if(mysqli_num_rows($ctable_r11)>0)
        {
            $count = 0;            
            while($ctable_dP = mysqli_fetch_array($ctable_r11))
            {
              $msg = array("1"=>"Call","2"=>"Sms",3=>"Email");
              $Prospects_ID = $db->rp_getValue("no_order_inquiry","id","id='".$ctable_dP['reference_id']."' AND inquiry_lead_flag = '-1'",0);
              if($Prospects_ID){
              ?>
              <tr>
                <td><?php echo ++$count; ?></td>    
                <td>#INQ/ <?php echo $db->rp_getValue("no_order_inquiry","id","id='".$ctable_dP['reference_id']."' AND inquiry_lead_flag = '-1'",0); ?></td>                            
                <td>
                    <?php
                    if($ctable_dP['reference_table']=="sales_executive")
                    {
                      echo $db->rp_getValue("executive","cname","id='".$ctable_dP['visitor_id']."'");
                    }
                    else if($ctable_dP['reference_table']=="no_order_inquiry")
                    {
                      echo $db->rp_getValue("no_order_inquiry","company_name","id='".$ctable_dP['reference_id']."' AND inquiry_lead_flag = '-1'",0); 

                    }
                    ?>
                </td>

                <td>
                    <?php 
                    if($ctable_dP['reference_table']=="sales_executive")
                    {
                      echo $db->rp_getValue("executive","phone","id='".$ctable_dP['visitor_id']."'");
                    }
                    else if($ctable_dP['reference_table']=="no_order_inquiry")
                    {
                      echo $db->rp_getValue("no_order_inquiry","mobile_number","id='".$ctable_dP['reference_id']."' AND inquiry_lead_flag = '-1'",0); 

                    }
                    ?>
                </td>
                <td><?php echo $db->rp_getValue("sales_executive","name","id='".$ctable_dP['user_id']."'");?></td>              
                 <td>
                    <?php echo  $ctable_dP['description']; ?>
                </td>              
                <td>
                  <?php echo $msg[$ctable_dP['through']]; ?>    
                </td>            
                <?php if($ctable_dP['followup_date']=="0000-00-00 00:00:00"){?>
                  <td></td>
                <?php 
                } else { ?>
                <td><?php echo date('d-m-Y H:i:s a',strtotime($ctable_dP['followup_date'])); ?></td>
                <?php } ?>           
                <td><?php 
                  if($ctable_dP['reference_table']=="no_order_inquiry"){
                    echo $ctable_dP['response'];
                  }
                 ?></td>              
              </tr>
              <?php
            }
            }
        }           
        else
        {
          ?>
          <tr>
            <td colspan="9" class="text-center">No Followup Found</td>
          </tr>
          <?php
        }
        ?>
    </table>
    <!-- total Prospect -->

    <!-- inqiury -->
    <hr/>
    <div class="col-md-12 col-xs-12 col-sm-12 pad-0" style="margin-bottom: 20px;">
      <h3><b>Inquiry (<?= $db->rp_getTotalRecord($ctable2,$ctable_where_inquiry); ?>)</b></h3>
    </div>
    <table id="datatable_1" class="table table-striped table-bordered table-hover">
          <thead>
             <tr>
              <th>No.</th>
              <th>Inquiry No.</th>
              <th>Company Name</th>
              <th>Persoan Name</th>
              <th>Phone</th>
              <th>Country</th>
              <th>State</th>
              <th>City</th>
              <th>Inquiry Date</th>
              <th>Status</th>
            </tr>
          </thead>
          <?php
          if(mysqli_num_rows($ctable_r4)>0)
          {
              $count = 0;            
              while($ctable_d4 = mysqli_fetch_array($ctable_r4))
              {
                $inquiry_status_array = array("0"=>"Generated","1"=>"In Followup","3"=>"Buy Later","4"=>"Hot","5"=>"Cold","6"=>"Warm","-1"=>"Not Interested","-2"=>"Non Relavent","11"=>"Lost");
                ?>
                <tr>
                  <td><?php echo ++$count; ?></td>                
                  <td>#INQ/<?php  echo $ctable_d4['id']; ?></td>
                 <td><?php echo $ctable_d4['company_name'] ?></td>
                 <td><?php echo $ctable_d4['person_name'] ?></td>              
                 <td><?php echo $ctable_d4['mobile_number'] ?></td>              
                 <td><?php echo $ctable_d4['country'] ?></td>              
                 <td><?php echo $ctable_d4['state'] ?></td>              
                 <td><?php echo $ctable_d4['city'] ?></td>              
                 <td><?php if($ctable_d4['datetime']!="0000-00-00 00:00:00"){ echo date('d-m-Y',strtotime($ctable_d4['datetime'])); } else{ echo "";} ?></td>   
                 <td><?php echo $inquiry_status_array[$ctable_d4['status']] ?></td>        
                </tr>
                <?php
              }
          }           
          else
          {
            ?>
            <tr>
              <td colspan="10" class="text-center">No Inquiry Found</td>
            </tr>
            <?php
          }
          ?>
    </table>

    <div class="col-md-12 col-xs-12 col-sm-12 pad-0" style="margin-bottom: 20px;">
      <h3><b>Inquiry Followup</b></h3>
    </div>
     <table id="datatable_1" class="table table-striped table-bordered table-hover">
        <thead>
           <tr>
            <th>No.</th>
            <th>Inquiry No.</th>
            <th>Customer Name</th>
            <th>Mobile No </th>
            <th>Sales Person Name</th>
            <th>Description</th>
            <th>Through</th>
            <th>Date and Time</th>
            <th>Response</th>
          </tr>
        </thead>
        <?php
        if(mysqli_num_rows($ctable_r9)>0)
        {
            $count = 0;            
            while($ctable_dF = mysqli_fetch_array($ctable_r9))
            {
              $msg = array("1"=>"Call","2"=>"Sms",3=>"Email");
               $Inquiry_ID = $db->rp_getValue("no_order_inquiry","id","id='".$ctable_dF['reference_id']."' AND inquiry_lead_flag = '0'",0);
              if($Inquiry_ID){
              ?>
              <tr>
                <td><?php echo ++$count; ?></td>    
                <td>#INQ/ <?php echo 
                $db->rp_getValue("no_order_inquiry","id","id='".$ctable_dF['reference_id']."' AND inquiry_lead_flag = '0'",0); ?></td>                            
                <td>
                    <?php
                    if($ctable_dF['reference_table']=="sales_executive")
                    {
                      echo $db->rp_getValue("executive","cname","id='".$ctable_dF['visitor_id']."'");
                    }
                    else if($ctable_dF['reference_table']=="no_order_inquiry")
                    {
                      echo $db->rp_getValue("no_order_inquiry","company_name","id='".$ctable_dF['reference_id']."' AND inquiry_lead_flag = '0'",0);
                    }
                    ?>
                </td>

                <td>
                    <?php 
                    if($ctable_dF['reference_table']=="sales_executive")
                    {
                      echo $db->rp_getValue("executive","phone","id='".$ctable_dF['visitor_id']."'");
                    }
                    else if($ctable_dF['reference_table']=="no_order_inquiry")
                    {
                      echo $db->rp_getValue("no_order_inquiry","mobile_number","id='".$ctable_dF['reference_id']."' AND inquiry_lead_flag = '0'",0);
                    }
                    ?>
                </td>
                <td><?php echo $db->rp_getValue("sales_executive","name","id='".$ctable_dF['user_id']."'");?></td>              
                <td>
                    <?php 
                     
                      echo  $ctable_dF['description'];
                      
                    ?>
                </td>              
                <td>
                  <?php 
                        echo $msg[$ctable_dF['through']];
                      
                    ?>
                    
                  </td>              
                <?php if($ctable_dF['followup_date']=="0000-00-00 00:00:00"){?>
                  <td></td>
                <?php 
                } else { ?>
                <td><?php echo date('d-m-Y H:i:s a',strtotime($ctable_dF['followup_date'])); ?></td>
                <?php } ?>           
                <td>
                    <?php 
                      if($ctable_dF['reference_table']=="no_order_inquiry")
                      {
                        echo $ctable_dF['response'];
                      }
                    ?>
                </td>              
              </tr>
              <?php
            }
            }
        }           
        else
        {
          ?>
          <tr>
            <td colspan="9" class="text-center">No Followup Found</td>
          </tr>
          <?php
        }
        ?>
    </table>
    <!-- inqiury -->

    <!-- leads -->
    <hr/>
    <div class="col-md-12 col-xs-12 col-sm-12 pad-0" style="margin-bottom: 20px;">
        <h3><b>Leads (<?= $db->rp_getTotalRecord($ctable2,$ctable_where_lead); ?>)</b></h3>
    </div>
    <table id="datatable_1" class="table table-striped table-bordered table-hover">
          <thead>
             <tr>
              <th>No.</th>
              <th>Inquiry No.</th>
              <th>Company Name</th>
              <th>Persoan Name</th>
              <th>Phone</th>
              <th>Country</th>
              <th>State</th>
              <th>City</th>
              <th>Inquiry Date</th>
              <th>Status</th>
            </tr>
          </thead>
          <?php
          if(mysqli_num_rows($ctable_r5)>0)
          {
              $count = 0;            
              while($ctable_d5 = mysqli_fetch_array($ctable_r5))
              {
                $inquiry_status_array = array("0"=>"Generated","1"=>"In Followup","3"=>"Buy Later","4"=>"Hot","5"=>"Cold","6"=>"Warm","-1"=>"Not Interested","-2"=>"Non Relavent","11"=>"Lost");
                ?>
                <tr>
                  <td><?php echo ++$count; ?></td>                
                  <td>#INQ/<?php  echo $ctable_d5['id']; ?></td>
                 <td><?php echo $ctable_d5['company_name'] ?></td>
                 <td><?php echo $ctable_d5['person_name'] ?></td>              
                 <td><?php echo $ctable_d5['mobile_number'] ?></td>              
                 <td><?php echo $ctable_d5['country'] ?></td>              
                 <td><?php echo $ctable_d5['state'] ?></td>              
                 <td><?php echo $ctable_d5['city'] ?></td>              
                 <td><?php if($ctable_d5['datetime']!="0000-00-00 00:00:00"){ echo date('d-m-Y',strtotime($ctable_d5['datetime'])); } else{ echo "";} ?></td>   
                 <td><?php echo $inquiry_status_array[$ctable_d5['status']] ?></td>        
                </tr>
                <?php
              }
          }           
          else
          {
            ?>
            <tr>
              <td colspan="10" class="text-center">No Leads Found</td>
            </tr>
            <?php
          }
          ?>
    </table>

    <div class="col-md-12 col-xs-12 col-sm-12 pad-0" style="margin-bottom: 20px;">
      <h3><b>Leads Followup </b></h3>
    </div>
     <table id="datatable_1" class="table table-striped table-bordered table-hover">
        <thead>
           <tr>
            <th>No.</th>
            <th>Inquiry No.</th>
            <th>Customer Name</th>
            <th>Mobile No </th>
            <th>Sales Person Name</th>
            <th>Description</th>
            <th>Through</th>
            <th>Date and Time</th>
            <th>Response</th>
          </tr>
        </thead>
        <?php
        if(mysqli_num_rows($ctable_r10)>0)
        {
            $count = 0;            
            while($ctable_dL = mysqli_fetch_array($ctable_r10))
            {
              $msg = array("1"=>"Call","2"=>"Sms",3=>"Email");
              $Leads_ID = $db->rp_getValue("no_order_inquiry","id","id='".$ctable_dL['reference_id']."' AND inquiry_lead_flag = '1'",0);
              if($Leads_ID){
              ?>
              <tr>
                <td><?php echo ++$count; ?></td>    
                <td>#INQ/ <?php echo  $db->rp_getValue("no_order_inquiry","id","id='".$ctable_dL['reference_id']."' AND inquiry_lead_flag = '1'",0); ?></td>                            
                <td>
                    <?php
                    if($ctable_dL['reference_table']=="sales_executive")
                    {
                      echo $db->rp_getValue("executive","cname","id='".$ctable_dL['visitor_id']."'");
                    }
                    else if($ctable_dL['reference_table']=="no_order_inquiry")
                    {
                      // echo $db->rp_getValue("no_order_inquiry","company_name","id='".$ctable_dL['reference_id']."'",0);
                      echo $db->rp_getValue("no_order_inquiry","company_name","id='".$ctable_dL['reference_id']."' AND inquiry_lead_flag = '1'",0);
                    }
                    ?>
                </td>

                <td>
                    <?php 
                    if($ctable_dL['reference_table']=="sales_executive")
                    {
                      echo $db->rp_getValue("executive","phone","id='".$ctable_dL['visitor_id']."'");
                    }
                    else if($ctable_dL['reference_table']=="no_order_inquiry")
                    {
                      // echo $db->rp_getValue("no_order_inquiry","mobile_number","id='".$ctable_dL['reference_id']."'",0);
                      echo $db->rp_getValue("no_order_inquiry","mobile_number","id='".$ctable_dL['reference_id']."' AND inquiry_lead_flag = '1'",0);
                    }
                    ?>
                </td>
                <td><?php echo $db->rp_getValue("sales_executive","name","id='".$ctable_dL['user_id']."'");?></td>              
                <td>
                    <?php echo  $ctable_dL['description']; ?>
                </td>              
                <td>
                  <?php echo $msg[$ctable_dL['through']]; ?>    
                </td>
                  
                              
                <?php if($ctable_dL['followup_date']=="0000-00-00 00:00:00"){?>
                  <td></td>
                <?php 
                } else { ?>
                <td><?php echo date('d-m-Y H:i:s a',strtotime($ctable_dL['followup_date'])); ?></td>
                <?php } ?>           
                <td><?php 
                if($ctable_d6['reference_table']=="no_order_inquiry"){
                echo $ctable_dL['response']; 
                }
                ?>
                  
                </td>              
              </tr>
              <?php
            }
            }
        }           
        else
        {
          ?>
          <tr>
            <td colspan="9" class="text-center">No Followup Found</td>
          </tr>
          <?php
        }
        ?>
    </table>
    <!-- leads -->

    <!-- total Followup -->
    <hr/>
    <div class="col-md-12 col-xs-12 col-sm-12 pad-0" style="margin-bottom: 20px;">
        <h3><b>Followup (<?= $db->rp_getTotalRecord($ctable3,$ctable_where_followup); ?>)</b></h3>
    </div>
    <table id="datatable_1" class="table table-striped table-bordered table-hover">
        <thead>
           <tr>
            <th>No.</th>
            <th>Customer Name</th>
            <th>Mobile No </th>
            <th>Sales Officer Name</th>
            <th>Description</th>
            <th>Through</th>
            <th>Date and Time</th>
          </tr>
        </thead>
        <?php
        if(mysqli_num_rows($ctable_r6)>0)
        {
            $count = 0;            
            while($ctable_d6 = mysqli_fetch_array($ctable_r6))
            {
              $msg = array("1"=>"Call","2"=>"Sms",3=>"Email");
              ?>
              <tr>
                <td><?php echo ++$count; ?></td>                
                <td>
                    <?php
                    if($ctable_d6['reference_table']=="sales_executive")
                    {
                      echo $db->rp_getValue("executive","cname","id='".$ctable_d6['visitor_id']."'");
                    }
                    else if($ctable_d6['reference_table']=="no_order_inquiry")
                    {
                      echo $db->rp_getValue("no_order_inquiry","company_name","id='".$ctable_d6['reference_id']."'",0);
                    }
                    else if($ctable_d6['reference_table']=="quotation_detail")
                    {
                        $followup_flag="quotation_followup";
                        $customer_id=$db->rp_getValue("quotation_detail","customer_id","id='".$ctable_d6['reference_id']."'");
                        echo $db->rp_getValue("executive","cname","id='".$customer_id."'");
                    }
                    ?>
                </td>
                <td>
                    <?php 
                    if($ctable_d6['reference_table']=="sales_executive")
                    {
                      echo $db->rp_getValue("executive","phone","id='".$ctable_d6['visitor_id']."'");
                    }
                    else if($ctable_d6['reference_table']=="no_order_inquiry")
                    {
                      echo $db->rp_getValue("no_order_inquiry","mobile_number","id='".$ctable_d6['reference_id']."'",0);
                    }
                    else if($ctable_d6['reference_table']=="quotation_detail")
                    {
                        $followup_flag="quotation_followup";
                        $customer_id=$db->rp_getValue("quotation_detail","customer_id","id='".$ctable_d6['reference_id']."'");
                        echo $db->rp_getValue("executive","phone","id='".$customer_id."'");
                    }
                    ?>
                </td>
                <td><?php echo $db->rp_getValue("sales_executive","name","id='".$ctable_d6['user_id']."'");?></td>              
                <td><?php echo $ctable_d6['description'] ?></td>              
                <td><?php echo $msg[$ctable_d6['through']]?></td>              
                <?php if($ctable_d6['followup_date']=="0000-00-00 00:00:00"){?>
                  <td></td>
                <?php 
                } else { ?>
                <td><?php echo date('d-m-Y H:i:s a',strtotime($ctable_d6['followup_date'])); ?></td>
                <?php } ?>           
              </tr>
              <?php
            }
        }           
        else
        {
          ?>
          <tr>
            <td colspan="9" class="text-center">No Followup Found</td>
          </tr>
          <?php
        }
        ?>
    </table>
    <!-- total Survey Followup -->


    <!-- quotation -->
    <hr/>
    <div class="col-md-12 col-xs-12 col-sm-12 pad-0" style="margin-bottom: 20px;">
        <h3><b>Quotation (<?= $db->rp_getTotalRecord($ctable4,$ctable_where_quotation); ?>) --- Total Quotation Amount (<?= CURR .$db->rp_number_format($db->rp_getValue($ctable4,"SUM(grand_total)",$ctable_where_quotation),2); ?>)</b></h3>
    </div>
    <table id="datatable_1" class="table table-striped table-bordered table-hover">
      <thead>
        <tr>
            <th>No.</th>
            <th>Quotation No.</th>
            <th>Revised From Quotation No.</th>
            <th>Inquiry No.</th>
            <th>Quotation Date</th>
            <th>Company Name</th>
            <th>Company Mobile No</th>
            <th>Person Name</th>
            <th>Sales Person Name</th>
            <th>Quotation Amount</th>
            <th>Status</th>
        </tr>
      </thead>
      <?php
      if(mysqli_num_rows($ctable_r7)>0)
      {
        $count = 0;   
        while($ctable_d7 = mysqli_fetch_array($ctable_r7))
        {
          $Quotationarray = array("-2"=>"Disapproved","0"=>"Pending","1"=>"Approved","3"=>"Cancelled","-1"=>"Add to Cart","4"=>"Order Generated","5"=>"Lost");
          ?>
          <tr>
            <td><?php echo ++$count; ?></td> 
            <td><a href="quotation_viewer.php?quotation_id=<?= $ctable_d7['id'] ?>"><span class="text-success"><?php echo stripslashes($ctable_d7['quotation_no']); ?></span></a></td>
            <td><a href="quotation_viewer.php?quotation_id=<?= $ctable_d7['refrence_id'] ?>"><span class="text-success"><?php echo $db->rp_getValue("quotation_detail","quotation_no","id='".$ctable_d7['refrence_id']."'"); ?></span></a></td>
            <td><?= "#INQ/" . $ctable_d7['inquiry_id']; ?></td>
            <td><?php echo date('d-m-Y', strtotime($ctable_d7['quotation_date'])); ?></td>
            <td><?php echo $ctable_d7['company_name']; ?></td>
            <td><a target="_blank" href="https://api.whatsapp.com/send?phone=91<?php echo stripslashes($ctable_d7['contact_number']); ?>&text=<?= $sms; ?>"><?php echo stripslashes($ctable_d7['mobile_no']); ?><?php echo $ctable_d7['contact_number']; ?></a></td>
              <td><?php echo stripslashes($ctable_d7['customer_name']); ?></td>
              <td><?php echo $db->rp_getValue("sales_executive", "name", "id='" . $ctable_d7['sales_id'] . "'"); ?></td>
              <td align="right"><?php echo stripslashes(CURR . $db->rp_num(round($ctable_d7['grand_total']))); ?></td>
              <td><?php echo $Quotationarray[$ctable_d7['status']]; ?></td>
          </tr>
          <?php 
        }
      }
      else
      {
        ?>
        <tr>
          <td colspan="11" class="text-center">No Quotation Found</td>
        </tr>
        <?php
      }
      ?>
    </table>
    <!-- quotation -->

    <!-- orders -->
    <hr/>
    <div class="col-md-12 col-xs-12 col-sm-12 pad-0" style="margin-bottom: 20px;">
      <h3><b>Orders (<?= $db->rp_getTotalRecord($ctable5,$ctable_where_orders); ?>) --- Total Order Amount (<?= CURR .$db->rp_number_format($db->rp_getValue($ctable5,"SUM(grand_total)",$ctable_where_orders),2); ?> )</b></h3>
    </div>
    <table id="datatable_1" class="table table-striped table-bordered table-hover">
      <thead>
        <tr>
            <th>No.</th>
            <th>Order No.</th>
            <th>Quotation No.</th>
            <th>Order Date</th>
            <th>Company Name</th>
            <th>Person Name</th>
            <th>Sales Person Name</th>
            <th>Order Amount</th>
            <th>Status</th>
        </tr>
      </thead>
      <?php
      if(mysqli_num_rows($ctable_r8)>0)
      {
        $count = 0;   
        while($ctable_d8 = mysqli_fetch_array($ctable_r8))
        {
          $orders_status = array("0"=>"Pending","1"=>"Approved","2"=>"Dispatch","3"=>"Cancelled","-1"=>"Add to Cart","-2"=>"Disapproved","4"=>"Partially Dispatched");
          ?>
          <tr>
            <td><?php echo ++$count; ?></td> 
            <td><span class="text-success"><a href="order_viewer.php?order_id=<?= $ctable_d8['id'] ?>"><?php echo stripslashes($ctable_d8['order_no']); ?></a></span></td>
            <td><?php if($ctable_d8['quotation_id']!=0){echo "<a class='text-success' target='_blank' href='quotation_viewer.php?quotation_id=".$ctable_d8['quotation_id']."'>". $db->rp_getValue("quotation_detail","quotation_no","id='".$ctable_d8['quotation_id']."'")."</a>";}?></td>
            <td><?php echo date('d-m-Y',strtotime($ctable_d8['order_date'])); ?></td>
            <td><?php echo $ctable_d8['company_name'];?></td>
            <td><?php echo stripslashes($ctable_d8['customer_name']); ?></td>
            <td><?php echo $db->rp_getValue("sales_executive","name","id='".$ctable_d8['sales_id']."'");; ?></td>
            <td align="right"><?php echo stripslashes(CURR.$db->rp_num(round($ctable_d8['grand_total']))); ?></td>
          <td><?php echo $orders_status[$ctable_d8['status']]; ?></td>
          </tr>
          <?php 
        }
      }
      else
      {
        ?>
        <tr>
          <td colspan="9" class="text-center">No Orders Found</td>
        </tr>
        <?php
      }
      ?>
    </table>
    <!-- orders -->

    <!-- dispatch -->
   <!--  <hr/>
    <div class="col-md-12 col-xs-12 col-sm-12 pad-0" style="margin-bottom: 20px;">
      <h3><b>Dispatch (<?= $db->rp_getTotalRecord($ctable7,$ctable_where_dispatch); ?>)</b></h3>
    </div>
    <table id="datatable_1" class="table table-striped table-bordered table-hover">
      <thead>
        <tr>
            <th>No.</th>
            <th>Diapatch No.</th>
            <th>Order No.</th>
            <th>Dispatch Date</th>
            <th>Company Name</th>
            <th>Person Name</th>
            <th>Sales Person Name</th>
            <th>Order Amount</th>
            <th>Status</th>
        </tr>
      </thead>
      <?php
      if(mysqli_num_rows($ctable_r13)>0)
      {
        $count = 0;   
        while($ctable_d13 = mysqli_fetch_array($ctable_r13))
        {
          $dispatch_status_array = array("0"=>"Pending","1"=>"Complete");
          ?>
          <tr>
            <td><?php echo ++$count; ?></td> 
            <td><span class="text-success"><a target="_blank" href="view_dispatch.php?id=<?= $ctable_d13['id'] ?>"><?php echo stripslashes($ctable_d13['dispatch_no']); ?></a></span></td>
            <td><span class="text-success"><a target="_blank" href="order_viewer.php?order_id=<?= $ctable_d13['order_id'] ?>"><?php echo $db->rp_getValue("orders","order_no","isDelete=0 AND id='".$ctable_d13['order_id']."' "); ?></a></span></td>
            <td><?php echo date('d-m-Y',strtotime($ctable_d13['dispatch_date'])); ?></td>
            <td><?php echo $ctable_d13['company_name'];?></td>
            <td><?php echo stripslashes($ctable_d13['customer_name']); ?></td>
            <td><?php echo $db->rp_getValue("sales_executive","name","id='".$ctable_d13['sales_id']."'");; ?></td>
            <td align="right"><?php echo stripslashes(CURR.$db->rp_num(round($ctable_d13['grand_total']))); ?></td>
            <td><?php echo $dispatch_status_array[$ctable_d13['status']]; ?></td>
          </tr>
          <?php 
        }
      }
      else
      {
        ?>
        <tr>
          <td colspan="9" class="text-center">No Dispatch Found</td>
        </tr>
        <?php
      }
      ?>
    </table> -->
    <!-- dispatch -->

    <!-- packing slip -->
    <!-- <hr/>
    <div class="col-md-12 col-xs-12 col-sm-12 pad-0" style="margin-bottom: 20px;">
      <h3><b>Packing Slip (<?= $db->rp_getTotalRecord($ctable8,$ctable_where_packingslip); ?>)</b></h3>
    </div>
    <table id="datatable_1" class="table table-striped table-bordered table-hover">
      <thead>
        <tr>
            <th>No.</th>
            <th>Packing Slip No.</th>
            <th>Diapatch No.</th>
            <th>Packing Slip Date</th>
            <th>Company Name</th>
            <th>Person Name</th>
            <th>Sales Person Name</th>
            <th>Order Amount</th>
            <th>Status</th>
        </tr>
      </thead>
      <?php
      if(mysqli_num_rows($ctable_r14)>0)
      {
        $count = 0;   
        while($ctable_d14 = mysqli_fetch_array($ctable_r14))
        {
          $packing_slip_status_array = array("0"=>"Pending","1"=>"Invoice Generate");
          ?>
          <tr>
            <td><?php echo ++$count; ?></td> 
            <td><span class="text-success"><a target="_blank" href="view_packing_slip.php?id=<?= $ctable_d14['id'] ?>"><?php echo stripslashes($ctable_d14['packing_slip_no']); ?></a></span></td>
            <td><span class="text-success"><a target="_blank" href="view_dispatch.php?id=<?= $ctable_d14['dispatch_id'] ?>"><?php echo $db->rp_getValue("dispatch_detail","dispatch_no","isDelete=0 AND id='".$ctable_d14['dispatch_id']."' "); ?></a></span></td>
            <td><?php echo date('d-m-Y',strtotime($ctable_d14['packing_slip_date'])); ?></td>
            <td><?php echo $db->rp_getValue("executive","company_name"," isDelete=0 AND id='".$ctable_d14['customer_id']."' "); ?></td>
            <td><?php echo $db->rp_getValue("executive","cname"," isDelete=0 AND id='".$ctable_d14['customer_id']."' "); ?></td>
            <?php 
            $dispatch_sales_id=$db->rp_getValue("dispatch_detail","sales_id","isDelete=0 AND id='".$ctable_d14['dispatch_id']."' ");
            $dispatch_sales_name=$db->rp_getValue("sales_executive","name","id='".$dispatch_sales_id."'"); 
            ?>
            <td><?php echo $dispatch_sales_name; ?></td>
            <td align="right"><?php echo stripslashes(CURR.$db->rp_num(round($ctable_d14['grand_total']))); ?></td>
            <td><?php echo $packing_slip_status_array[$ctable_d14['status']]; ?></td>
          </tr>
          <?php 
        }
      }
      else
      {
        ?>
        <tr>
          <td colspan="9" class="text-center">No Packing Slip Found</td>
        </tr>
        <?php
      }
      ?>
    </table> -->
    <!-- packing slip -->

    <!-- invoice -->
    <!-- <hr/>
    <div class="col-md-12 col-xs-12 col-sm-12 pad-0" style="margin-bottom: 20px;">
      <h3><b>Invoice (<?= $db->rp_getTotalRecord($ctable9,$ctable_where_invoice); ?>) --- Total Invoice Amount (<?= CURR .$db->rp_number_format($db->rp_getValue($ctable9,"SUM(grand_total)",$ctable_where_invoice),2); ?> )</b></h3>
    </div>
    <table id="datatable_1" class="table table-striped table-bordered table-hover">
      <thead>
        <tr>
            <th>No.</th>
            <th>Invoice No.</th>
            <th>Dispatch No.</th>
            <th>Invoice Date</th>
            <th>Company Name</th>
            <th>Person Name</th>
            <th>Sales Person Name</th>
            <th>Invoice Amount</th>
            <th>Status</th>
        </tr>
      </thead>
      <?php
      if(mysqli_num_rows($ctable_r15)>0)
      {
        $count = 0;   
        while($ctable_d15 = mysqli_fetch_array($ctable_r15))
        {
          $invoice_status = array("0"=>"Pending","1"=>"Approved","2"=>"Dispatch","3"=>"Cancelled","-1"=>"Add to Cart","-2"=>"Disapproved","4"=>"Partially Dispatched");
          ?>
          <tr>
            <td><?php echo ++$count; ?></td> 
            <td><span class="text-success"><a target="_blank" href="invoice_viewer.php?invoice_id=<?= $ctable_d15['id'] ?>"><?php echo stripslashes($ctable_d15['invoice_no']); ?></a></span></td>
            <td><span class="text-success"><a target="_blank" href="view_dispatch.php?id=<?= $ctable_d15['dispatch_ids'] ?>"><?php echo $db->rp_getValue("dispatch_detail","dispatch_no","isDelete=0 AND id='".$ctable_d15['dispatch_ids']."' "); ?></a></span></td>
            <td><?php echo date('d-m-Y',strtotime($ctable_d15['invoice_date'])); ?></td>
            <td><?php echo $ctable_d15['company_name']; ?></td>
            <td><?php echo $ctable_d15['customer_name']; ?></td>
            <td><?php echo $db->rp_getValue("sales_executive","name","id='".$ctable_d15['sales_id']."'"); ?></td>
            <td align="right"><?php echo stripslashes(CURR.$db->rp_num(round($ctable_d15['grand_total']))); ?></td>
            <td><?php echo $invoice_status[$ctable_d15['status']]; ?></td>
          </tr>
          <?php 
        }
      }
      else
      {
        ?>
        <tr>
          <td colspan="9" class="text-center">No Invoice Found</td>
        </tr>
        <?php
      }
      ?>
    </table> -->
    <!-- invoice -->

    <!-- complain -->
    <hr/>
    <div class="col-md-12 col-xs-12 col-sm-12 pad-0" style="margin-bottom: 20px;">
      <h3><b>Complain (<?= $db->rp_getTotalRecord($ctable10,$ctable_where_complain); ?>)</b></h3>
    </div>
    <table id="datatable_1" class="table table-striped table-bordered table-hover">
      <thead>
        <tr>
            <th>No.</th>
            <th>Complain No.</th>
            <th>Complain Date</th>
            <th>Customer Name</th>
            <th>Sales Person Name</th>
            <th>Source of Complain</th>
            <th>Status</th>
            <th>Complain Assign To</th>
        </tr>
      </thead>
      <?php
      if(mysqli_num_rows($ctable_r16)>0)
      {
        $count = 0;   
        while($ctable_d16 = mysqli_fetch_array($ctable_r16))
        {
          $complain_status = array("0"=>"Generate","1"=>"In Progress","2"=>"Complete","-1"=>"Reject","-2"=>"Not Done");
          $complain_type_array = array("1"=>"Email","2"=>"Call","3"=>"Whatsapp");
          ?>
          <tr>
            <td><?php echo ++$count; ?></td> 
            <td><?php echo $ctable_d16['complain_no']; ?></td>
            <td><?php echo date('d-m-Y',strtotime($ctable_d16['complain_date'])); ?></td>
            <td><?php echo $db->rp_getValue("executive","cname","id='".$ctable_d16['customer_id']."'"); ?></td>
            <td><?php echo $db->rp_getValue("sales_executive","name","id='".$ctable_d16['user_id']."'"); ?></td>
            <td><?php echo stripslashes($complain_type_array[$ctable_d16['complain_type']]); ?></td>
            <td><?php echo $complain_status[$ctable_d16['status']]; ?></td>
            <td><?php echo $db->rp_getValue("sales_executive","name","isDelete=0 AND id='".$ctable_d16['complain_assign_to']."' ") ?></td>
          </tr>
          <?php 
        }
      }
      else
      {
        ?>
        <tr>
          <td colspan="9" class="text-center">No Complain Found</td>
        </tr>
        <?php
      }
      ?>
    </table>
    <!-- complain -->

    <!-- visit -->
    <hr/>
    <div class="col-md-12 col-xs-12 col-sm-12 pad-0" style="margin-bottom: 20px;">
      <h3><b>Visit (<?= $db->rp_getTotalRecord($ctable1,$ctable_where_visit); ?>)</b></h3>
    </div>
    <table id="datatable_1" class="table table-striped table-bordered table-hover">
      <thead>
        <tr>
            <th>No.</th>
            <th>Visit Date</th>
            <th>Customer Name</th>
            <th>Sales Person Name</th>
            <th>Address</th>
        </tr>
      </thead>
      <?php
      if(mysqli_num_rows($ctable_r2)>0)
      {
        $count = 0;   
        while($ctable_d2 = mysqli_fetch_array($ctable_r2))
        {
          ?>
          <tr>
            <td><?php echo ++$count; ?></td> 
            <td><?php echo date('d-m-Y',strtotime($ctable_d2['created_date'])); ?></td>
            <td><?php echo $db->rp_getValue("executive","cname","id='".$ctable_d2['customer_id']."'"); ?></td>
            <td><?php echo $db->rp_getValue("sales_executive","name","id='".$ctable_d2['user_id']."'"); ?></td>
            <td><?php echo $ctable_d2['app_address']; ?></td>
          </tr>
          <?php 
        }
      }
      else
      {
        ?>
        <tr>
          <td colspan="9" class="text-center">No Visit Found</td>
        </tr>
        <?php
      }
      ?>
    </table>
    <!-- visit -->

    <!-- expense -->
    <hr/>
    <div class="col-md-12 col-xs-12 col-sm-12 pad-0" style="margin-bottom: 20px;">
      <h3><b>Expense (<?= $db->rp_getTotalRecord($ctable11,$ctable_where_expense); ?>)</b></h3>
    </div>
    <table id="datatable_1" class="table table-striped table-bordered table-hover">
      <thead>
        <tr>
            <th>No.</th>
            <th>Expense Date</th>
            <th>Sales Person Name</th>
            <th>Category Name</th>
            <th>Sub Category Name</th>
            <th>Request Amount</th>
            <th>Passed Amount</th>
            <th>Status</th>
        </tr>
      </thead>
      <?php
      if(mysqli_num_rows($ctable_r17)>0)
      {
        $count = 0;   
        while($ctable_d17 = mysqli_fetch_array($ctable_r17))
        {
          $expense_status_array = array("0"=>"Pending","1"=>"Pass","2"=>"Reject");
          ?>
          <tr>
            <td><?php echo ++$count; ?></td> 
            <td><?php echo date('d-m-Y',strtotime($ctable_d17['expense_date'])); ?></td>
            <td><?php echo $db->rp_getValue("sales_executive","name","id='".$ctable_d17['sales_executive_id']."'"); ?></td>
            <td><?php echo $db->rp_getValue("expence_category","name"," isDelete=0 AND id = '".$ctable_d17['category_id']."' "); ?></td>
            <td><?php echo $db->rp_getValue("expence_sub_category","name"," isDelete=0 AND id = '".$ctable_d17['subcategory_id']."' "); ?></td>
            <td><?php echo $ctable_d17['total'] ?></td>
            <td><?php echo $ctable_d17['pass_expense_amount'] ?></td>
            <td><?php echo $expense_status_array[$ctable_d17['expense_status']]; ?></td>
          </tr>
          <?php 
        }
      }
      else
      {
        ?>
        <tr>
          <td colspan="9" class="text-center">No Expense Found</td>
        </tr>
        <?php
      }
      ?>
    </table>
    <!-- expense -->

    <!-- leave request -->
    <hr/>
    <div class="col-md-12 col-xs-12 col-sm-12 pad-0" style="margin-bottom: 20px;">
      <h3><b>Leave Request (<?= $db->rp_getTotalRecord($ctable12,$ctable_where_leaverequest); ?>)</b></h3>
    </div>
    <table id="datatable_1" class="table table-striped table-bordered table-hover">
      <thead>
        <tr>
            <th>No.</th>
            <th>Leave Request Date</th>
            <th>Sales Person Name</th>
            <th>Leave Type</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Status</th>
            <th>Cancel/Reject Reason</th>
        </tr>
      </thead>
      <?php
      if(mysqli_num_rows($ctable_r18)>0)
      {
        $count = 0;   
        while($ctable_d18 = mysqli_fetch_array($ctable_r18))
        {
          $leaverequest_status_array = array("0"=>"Pending","1"=>"Accepted","2"=>"Rejected","3"=>"Cancelled");
          ?>
          <tr>
            <td><?php echo ++$count; ?></td> 
            <td><?php echo date('d-m-Y',strtotime($ctable_d18['created_date'])); ?></td>
            <td><?php echo $db->rp_getValue("sales_executive","name","id='".$ctable_d18['sales_executive_id']."'"); ?></td>
            <td><?php echo $db->rp_getValue("leave_type","name","id='".$ctable_d18['leave_type']."'"); ?></td>
            <td><?php echo date("d-m-Y",strtotime($ctable_d18['start_date'])). date("h:i a",strtotime($ctable_d18['start_time'])); ?></td>
            <td><?php echo date("d-m-Y",strtotime($ctable_d18['end_date'])). date("h:i a",strtotime($ctable_d18['end_time'])); ?></td>
            <td><?php echo $leaverequest_status_array[$ctable_d18['status']]; ?></td>
            <td><?php echo $ctable_d18['cancel_reject_reason'] ?></td>
          </tr>
          <?php 
        }
      }
      else
      {
        ?>
        <tr>
          <td colspan="9" class="text-center">No Leave Request Found</td>
        </tr>
        <?php
      }
      ?>
    </table>
    <!-- leave request -->

    <!-- new customer -->
    <hr/>
    <div class="col-md-12 col-xs-12 col-sm-12 pad-0" style="margin-bottom: 20px;">
      <h3><b>New Customer (<?= $db->rp_getTotalRecord($ctable13,$ctable_where_newcustomer); ?>)</b></h3>
    </div>
    <table id="datatable_1" class="table table-striped table-bordered table-hover">
      <thead>
        <tr>
            <th>No.</th>
            <th>Customer Create Date</th>
            <th>Customer Type</th>
            <th>Company Name</th>
            <th>Person Name</th>
            <th>Sales Person Name</th>
            <th>Phone</th>
            <th>State</th>
            <th>City</th>
        </tr>
      </thead>
      <?php
      if(mysqli_num_rows($ctable_r19)>0)
      {
        $count = 0;   
        while($ctable_d19 = mysqli_fetch_array($ctable_r19))
        {
          $newcustomer_status_array = array("1"=>"Super Stockist","2"=>"Distributor","3"=>"Dealer","4"=>"B2B Customer","5"=>"OEM","6"=>"B2C Customer","7"=>"Merchant Exports");
          ?>
          <tr>
            <td><?php echo ++$count; ?></td> 
            <td><?php echo date('d-m-Y',strtotime($ctable_d19['created_date'])); ?></td>
            <td><?php echo $newcustomer_status_array[$ctable_d19['type_of_executive']]; ?></td>
            <td><?php echo $ctable_d19['company_name']; ?></td>
            <td><?php echo $ctable_d19['cname']; ?></td>
            <td><?php echo $db->rp_getValue("sales_executive","name","id='".$ctable_d19['seid']."'"); ?></td>
            <td><?php echo $ctable_d19['phone']; ?></td>
            <td><?php echo $ctable_d19['state']; ?></td>
            <td><?php echo $ctable_d19['city']; ?></td>
          </tr>
          <?php 
        }
      }
      else
      {
        ?>
        <tr>
          <td colspan="9" class="text-center">No New Customer Found</td>
        </tr>
        <?php
      }
      ?>
    </table>
    <!-- new customer -->
    

    <!-- Current Date Path -->
   <!--  <hr/>
  <div class="col-md-12 col-xs-12 col-sm-12 pad-0" style="margin-bottom: 20px;">
      <h3><b><?= $_REQUEST['date']?> Google Map Route</b></h3>
      <h3><b><?= date('d-m-Y') ?> Google Map Route</b></h3>
  </div>
    <div>    
      <div class="map_route" style="height: 600px;">
      </div>
    </div> -->
   <!-- Current Date Path -->
    <!-- </table> -->
<!-- Modal -->
<div id="OpenMap" class="modal fade" role="dialog">
  <div class="modal-dialog" style="width: 970px;">

    <!-- Modal content-->
    <div class="modal-content" >
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Attendance</h4>
      </div>
      <div class="modal-body">
        <div id="map_canvas"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
<!-- Modal -->

 <!-- Modal -->
  <div id="OpenVisitMap" class="modal fade" role="dialog">
    <div class="modal-dialog" style="width: 970px;">

      <!-- Modal content-->
      <div class="modal-content" >
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Visit</h4>
        </div>
        <div class="modal-body">
          <div id="visit_map_canvas"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>

    </div>
  </div>
  <!-- Modal -->

<div id="myModal" class="modal">
  <span class="close1" onclick='$("#myModal").css("display","none");'>&times;</span>
  <img class="modal-content" style="height: 80%;width: auto;" id="img01">
</div>
<?php include("include_js.php"); ?>
<script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js">
      </script>
<script async defer
  src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDklPuT2SCmcmlflaoZ4B0WywYK_em79x4&callback=initMap">
  </script>
<script src="<?=ADMINSITEURL?>js/lightbox.js"></script>
</script>
<script type="text/javascript">
    $(".mapbtn").click(function(){
        var date = $(this).data("date");
        var salesexename = $(this).data("salesexename");
        var lat = $(this).data("lat");
        var lng = $(this).data("long");
        var app_address = $(this).data("app_address");
        if(lat!="" && lng!="")
        {
            $.ajax({
                url: "get_attendance_map.php",
                data: {
                    lat: lat,
                    lng: lng,
                    date: date,
                    app_address: app_address,
                    salesexename: salesexename,
                },
                beforeSend: function() {
                    $("#map_canvas").html("<div class='row text-center'><div class='col-sm-12'><h2><i class='fa fa-refresh fa-spin'></i>&nbsp;Loading..</h2></div></div>");
                },
                success: function(result) {
                    $("#map_canvas").html(result);
                }
            });
        }
        else
        {
            $("#map_canvas").html("<h3>Sorry No Location Available</h3>");
        }
    });

    $(".mapbtnvisit").click(function(){    
    var date = "<?=$_REQUEST['date']?>";
    var salesexename = "<?=$salesexename?>";
    lat = $(this).data("lat");
    lng = $(this).data("long");
    app_address = $(this).data("app_address");
    $.ajax({
            url: "get_visit_map.php",
            data: {
                lat: lat,
                lng: lng,
                date: date,
                app_address: app_address,
                salesexename: salesexename,
            },
            beforeSend: function() {
                $("#visit_map_canvas").html("<div class='row text-center'><div class='col-sm-12'><h2><i class='fa fa-refresh fa-spin'></i>&nbsp;Loading..</h2></div></div>");
            },
            success: function(result) {
                $("#visit_map_canvas").html(result);
            }
        });
  });

</script>
<?php include("disconnect.php"); ?>
<!-- <script type="text/javascript">
  $(document).ready(function()
  {
    var id="<?= $_REQUEST['id']; ?>";
    var date="<?= $_REQUEST['date']; ?>";
    $.ajax(
    {
      type: "POST",
      url: "all_pin_d.php",
      data: {
        id:id, 
        date:date, 
        reqheight:"900",
        flag:"report",
      },
      cache: false,
      beforeSend: function() {
        
      },
      success: function(result) {
        $(".map_route").html(result);
      }
    });
  });
</script> -->
<?php require_once("disconnect.php"); ?>