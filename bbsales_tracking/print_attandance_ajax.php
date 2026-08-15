<?php 
$page_id=593;$page_slug='attendance_page';
/*
 * @author Ravi Patel
 */
include("connect.php");
// print_r($_REQUEST);exit;
$ctable   = "attendance";
$ctable1  = "Attendance";

$ctable_where = "isDelete=0";
// Get the total number of rows in the table

if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
  $sales_id = $db->rp_getData("sales_executive","*","name LIKE '%".$_REQUEST['searchName']."%' OR phone LIKE '%".$_REQUEST['searchName']."%'  AND isDelete=0","",0);
    if($sales_id)
    {       
        while($K=mysqli_fetch_assoc($sales_id))
        {
            $USER_IDS[]=$K['id'];
        }
        $USER_IDS=implode(",",$USER_IDS);
        $ctable_where .=" AND sales_id IN (".$USER_IDS.") ";
    }
    else
    {
        $ctable_where .=" AND sales_id IN (0) ";
    }
}

// if(isset($_REQUEST["sales_executive"]) && $_REQUEST["sales_executive"]!="" && $_REQUEST["sales_executive"]!='' && $_REQUEST["sales_executive"]!=undefined){
//   // $_REQUEST['sales_executive'] = implode(",",$_REQUEST['sales_executive']);
//   $ctable_where .= " user_id='".$_REQUEST["sales_executive"]."' AND ";
// }
if(isset($_REQUEST["sales_executive"]) && $_REQUEST["sales_executive"]!="" && $_REQUEST["sales_executive"]!=undefined && $_REQUEST["sales_executive"]!="null"){
    // $ctable_where .= " AND sales_id='".$_REQUEST["sales_executive"]."' ";
    $ctable_where .= " AND sales_id IN (".$_REQUEST["sales_executive"].") ";
    $sid = $_REQUEST["sales_executive"];
}

/*if(isset($_REQUEST['io']) && $_REQUEST['io']!="" && $_REQUEST['io']!="null")
{
    $ctable_where .=" AND inout_status='".$_REQUEST['io']."' ";
} */
$in = true;
$out = true;
if(isset($_REQUEST['io']) && $_REQUEST['io']!="" && $_REQUEST['io']!="null")
{
  $in = false;
  $out = false;
  $_REQUEST['io'] = explode(",", $_REQUEST['io']);
  if(in_array("In", $_REQUEST['io']))
  {
    $in = true;
  }
  if(in_array("Out", $_REQUEST['io']))
  {
    $out = true;
  }
}

if(isset($_REQUEST['io']) && $_REQUEST['io']!="" && $_REQUEST['io']!="null")
{
    $ctable_where .=" AND inout_status='".implode(" ", $_REQUEST['io'])."' ";
}

if(isset($_REQUEST['state']) && $_REQUEST['state']!="")
{

    $sales_executive_r = $db->rp_getData("sales_executive","id","state='". $_REQUEST['state']."' AND isDelete=0 ","",0);
    while ($sales_executive_d = mysqli_fetch_assoc($sales_executive_r)) 
    {
        $sales_executive_arr[] = $sales_executive_d['id'];
    }

    $sales_executive_str = implode(",",$sales_executive_arr);
    // echo $sales_executive_str;exit;
" AND sales_id IN(".$sales_executive_str.") ";
    $ctable_where .=" AND sales_id IN(".$sales_executive_str.") ";

    $state = $_REQUEST['state'];
}

// if(isset($_REQUEST['attendance_type']) && $_REQUEST['attendance_type']!="")
// {
//     $ctable_where .=" AND attandance_type='".$_REQUEST['attendance_type']."' ";

// }

if(isset($_REQUEST['df']) && $_REQUEST['df']!="" && $_REQUEST['df']!="undefined"){
    //echo $_REQUEST['df'];exit;
    $date_filter_query = urldecode( $_REQUEST['df'] );

    $date_filter_query_ex=explode(" to ",$date_filter_query);

    $ctable_where .= " AND ( DATE(date_time)>='".date("Y-m-d",strtotime($date_filter_query_ex['0']))."' AND DATE(date_time)<='".date("Y-m-d",strtotime($date_filter_query_ex['1']))."'  ) ";
      $date1= $date_filter_query_ex['0']." to ".$date_filter_query_ex[1];
}

if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0 && $_SESSION[SITE_SESS.'_ADMIN_TYPE']!=14)
{
    $check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];
    $ctable_where .= " AND sales_id='".$check_id."' ";
}

// if(isset($_REQUEST['filter_month']) && $_REQUEST['filter_month']!="")
// {
//   $ctable_where .= " AND MONTH(date_time)='".$_REQUEST['filter_month']."' ";
// }
// if(isset($_REQUEST['filter_year']) && $_REQUEST['filter_year']!="")
// {
//   $ctable_where .= " AND YEAR(date_time)='".$_REQUEST['filter_year']."' ";
// }

$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"]) ) ? intval($_REQUEST["show"]) : 100;

$get_total_rows = $db->rp_getTotalRecord($ctable,$ctable_where,0); //hold total records in variable
//break records into pages
$total_pages = ceil($get_total_rows/$item_per_page);

//get starting position to fetch the records
$page_position = (($page_number-1) * $item_per_page);

$ctable_r = $db->rp_getData("attendance","*",$ctable_where,"id DESC",0);
?>
<!DOCTYPE html>
<html>
<head>
  <title></title>

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
  </style>
</head>
<body>

  <table class="table table-striped table-bordered table-hover" id="datatable_1" style="min-height: 300px!important">
        <thead>
          <tr><th colspan="9"><h3 style="margin: 0"><b>Attendance <?= $date1; ?> Printed By <?= ucfirst($db->rp_getValue("dealer_distributor_network","name","id='".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID']."'")); ?></b></h3></th></tr>
          <tr>
            <th style="width: 5%!important;">No.</th>
            <th style="width: 10%!important;">Sales Person Name</th>
            <th style="width: 7%!important;">CUG No.</th>
            <th style="width: 10%!important;">Date and Time</th>
            <th style="width: 10%!important;">In/Out</th>
            <!-- <th>Attendance Type</th> -->
            <th>State</th>
            <th style="width: 5%!important;">City</th>
            <!-- <th>Imei</th> -->
            <!-- <th>Location Map</th> -->
            <th >Address</th>
            <th style="width: 10px!important;" >Image</th>
          </tr>
   <!--    <tr>
              <th>Sales Name</th>
        <?php for($i=1;$i<=$lastdate;$i++)
        {
          ?>
        <th style="text-align: center;">
          <?php echo $i; ?></th>  
          <?php
        } 
        ?>      
          </tr> -->
        </thead>
        <tbody>
          <?php
           if(mysqli_num_rows($ctable_r)>0){
            $count = 0;
            $AttandanceType= array('1' =>"In" ,'2' =>"Out" ,'3' =>"Auto Out" ,'4' =>"Logout With Out" ,'5' =>"Out On Nextday"  ,"6"=>"Out from server");
            while($ctable_d = mysqli_fetch_array($ctable_r)){

            $img = armor_attendance_image(isset($ctable_d['image_path']) ? $ctable_d['image_path'] : '');

            $sales_person_table_r = $db->rp_getData("sales_executive","*","id='".$ctable_d['sales_id']."' AND isDelete=0 ","",0);

            $sales_person_table_d = mysqli_fetch_assoc($sales_person_table_r);

          //print_r($ctable_d);
            ?>
            <tr>
                <td><?php echo ++$count; ?></td>
                 <td><?php echo $sales_person_table_d['name']?></td>
                <td><?php echo  $sales_person_table_d['phone']?></td>
                <td><?php echo date("d-m-Y h:i A",strtotime($ctable_d['date_time'])); ?></td>
                <td><?php echo $ctable_d['inout_status']; ?></td>
                  <!-- <td><?=  $AttandanceType[$ctable_d['attandance_type']]; ?></td> -->
                  <td><?php echo  $sales_person_table_d['state']?></td>
                 <td><b>
                    <?php  
                    $numbers = $ctable_d['app_address'];
                    $arr = explode(',', $numbers);
                    echo $arr[count($arr) - 3];
                    ?></b>
                </td>
                <!-- <td><?php echo $ctable_d['imei']; ?></td> -->
                <!-- <td> -->
                    <!-- Trigger the modal with a button -->
              <!--       <a class="mapbtn" data-lat="<?php echo stripslashes($ctable_d['latitude']); ?>" data-long="<?php echo stripslashes($ctable_d['longitude']); ?>" data-date="<?=date("d M H:i",strtotime($ctable_d['created_date']));?>" data-salesexename="<?=$db->rp_getValue("sales_executive","name","id='".$ctable_d["sales_id"]."'",0);?>" data-toggle="modal" data-target="#OpenMap">
                        <img src="<?=SITEURL?>resource/map.png" style="height: 80px;">
                    </a>
                </td> -->
                <td ><?php echo $ctable_d['app_address']; ?></td>
                <td >
                    <a href="<?=$img?>" data-lightbox="attendance<?=$count?>" data-title="attendance <?=$ctable_d['id']?>"><img src="<?=$img?>" style="height: 120px;width:100px;"></a>
                  <!-- <img src="<?php echo $img ?>" height="100px" width="auto"> -->
                </td>
            </tr>
        <?php
            }
        }
        ?>
        </tbody>

</body>
</html>
 <?php require_once("disconnect.php"); ?>
