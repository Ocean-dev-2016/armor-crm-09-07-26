<?php
$page_id=593;$page_slug='attendance_page';
/*
 * @author Ravi Patel
 */
include("connect.php");
// print_r($_REQUEST);exit;
$ctable     = "attendance";
$ctable1    = "Attendance";

$ctable_where = "";

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
        $ctable_where .="sales_id IN (".$USER_IDS.") AND ";
    }
    else
    {
        $ctable_where .="sales_id IN (0) AND ";
    }
}
if(isset($_REQUEST['se_id']) && $_REQUEST['se_id']!="")
{
    $ctable_where .=" sales_id='".$_REQUEST['se_id']."' AND ";
    $sid = $_REQUEST['se_id'];
}
if(isset($_REQUEST['io']) && $_REQUEST['io']!="")
{
    $ctable_where .=" inout_status='".$_REQUEST['io']."' AND ";
    $io = $_REQUEST['io'];
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

    $ctable_where .=" sales_id IN(".$sales_executive_str.") AND ";

    $state = $_REQUEST['state'];
}

if (isset($_REQUEST['attendance_month']) && $_REQUEST['attendance_month'] != "" && $_REQUEST['attendance_month'] != NULL) {
    $ctable_where .= "  MONTH(date_time) = '".$_REQUEST['attendance_month']."' AND ";
}

if (isset($_REQUEST['attendance_year']) && $_REQUEST['attendance_year'] != "" && $_REQUEST['attendance_year'] != NULL) {
    $ctable_where .= "  YEAR(date_time) = '".$_REQUEST['attendance_year']."' AND";
}

if (isset($_REQUEST['todate']) && $_REQUEST['todate'] != "" && $_REQUEST['todate'] != NULL && $_REQUEST['todate'] != undefined) {
    $ctable_where .= "  date_time >= '" . $_REQUEST['todate'] . "'  AND";
}

if (isset($_REQUEST['fromdate']) && $_REQUEST['fromdate'] != "" && $_REQUEST['fromdate'] != NULL && $_REQUEST['fromdate'] != undefined) {
    $ctable_where .= "  date_time <= '" .$_REQUEST['fromdate']. "' AND";
}
/*if(isset($_REQUEST['attendance_type']) && $_REQUEST['attendance_type']!="")
{
    $ctable_where .=" attandance_type='".$_REQUEST['attendance_type']."' AND ";
    $attendance_type = $_REQUEST['attendance_type'];
}*/
$ctable_where .= " isDelete=0";

$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"]) ) ? intval($_REQUEST["show"]) : 100;

if(isset($_REQUEST["page"])){
    $page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
    if(!is_numeric($page_number)){die('Invalid page number!');} //incase of invalid page number
}else{
    $page_number = 1; //if there's no page number, set it to 1
}

// print_r($_REQUEST["sales_executive"]);exit;
if(isset($_REQUEST["sales_executive"]) && $_REQUEST["sales_executive"]!="" && $_REQUEST["sales_executive"]!=undefined){
    $ctable_where .= " AND sales_id='".$_REQUEST["sales_executive"]."'";
    $sid = $_REQUEST["sales_executive"];
    // echo $sid;exit();
}

if(isset($_REQUEST['df']) && $_REQUEST['df']!=""){
  
    $date_filter_query = urldecode($_REQUEST['df']);

    $date_filter_query_ex=explode(" to ",$date_filter_query);

    $ctable_where .= " AND ( DATE(date_time)>='".date("Y-m-d",strtotime($date_filter_query_ex['0']))."' AND DATE(date_time)<='".date("Y-m-d",strtotime($date_filter_query_ex['1']))."'  ) ";
   
    
}


// if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0 && $_SESSION[SITE_SESS.'_ADMIN_TYPE']!=14)
// {
//     $check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];
//     $ctable_where .= " AND sales_id='".$check_id."' ";
// }


if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
{
    if($_SESSION[SITE_SESS.'REFERANCE_TYPE']==2) //sales executive and its chain wise order
    { 
        if($rights['personal_flag']==1)
        {
            $check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];
            $ctable_where .= " AND sales_id='".$check_id."' ";
        }
        else
        {
            if($rights['chain_vise_flag'] == 1)
            {
                $check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];

                $get_sales_type=$db->rp_getValue("sales_executive","type","isDelete=0 AND id='". $check_id."'",0);
                if ($get_sales_type== "sales_manager") 
                {
                    $sales_executive_type = "Regional Sales Manager";
                    $key="sm_id";
                    $WhereCondition.=' ' .$key.'='.$check_id;
                }

                else if ($get_sales_type == "area_sales_manager") 
                {
                    $sales_executive_type = "National Sales Manager";//Business Development Manager
                    $key="asm_id";
                    $WhereCondition.=' ' .$key.'='.$check_id;
                }

                else if ($get_sales_type == "sales_officer") 
                {
                    $sales_executive_type = "Area Sales Manager";//Area Sales Manager
                    $key="so_id";
                    $WhereCondition.=' ' .$key.'='.$check_id;
                }
                else if ($get_sales_type == "sales_executive") 
                {
                    $sales_executive_type = "Sales Officer";
                    $key="se_id";
                    $WhereCondition.=' ' .$key.'='.$check_id;
                }
                else
                {
                    $WhereCondition.=' type = "service_engineer"';
                }

                $data = $db->rp_getData("sales_executive","id",$WhereCondition,"",0);

                $SALEID1=array();
                if($data)
                {
                    while($data_d=mysqli_fetch_assoc($data))
                    {
                        $SALEID1[]=$data_d['id'];
                    }
                }
                if(!empty($SALEID1))
                {
                    $SALEID1=implode(",", $SALEID1);
                    
                    $ctable_where .= "  AND sales_id IN (".$SALEID1.','.$_SESSION[SITE_SESS.'REFERANCE_ID'].")";  
                }
                else
                {
                   $ctable_where .= "  AND sales_id IN (".$_SESSION[SITE_SESS.'REFERANCE_ID'].")";       
                } 
            } 
        }
    }
}

$get_total_rows = $db->rp_getTotalRecord($ctable,$ctable_where,0); //hold total records in variable
//break records into pages
$total_pages = ceil($get_total_rows/$item_per_page);

//get starting position to fetch the records
$page_position = (($page_number-1) * $item_per_page);

$ctable_r = $db->rp_getData($ctable,"*",$ctable_where,"id DESC limit $page_position, $item_per_page",0);
?>
<style type="text/css">
/*.io_select .select2-container .select2-choice {width: 80px};*/
.table-scrollable {
        width: auto;
        height: 450px;
        overflow-x: scroll;
        overflow-y: scroll;
        border: 1px solid #e7ecf1;
        margin: 10px 0 !important;
    }

    .fix-th
    {
        background-color: #f5f5f5 !important;position: sticky;top: 0; z-index: 1;
    }
    .fix-th1
    {
        background-color: #e5e5e5 !important;position: sticky;top: 0; z-index: 1;
    }

</style>
<div class="col-md-12 col-xs-12 col-sm-12 pull-right" style="text-align: right;">
                  <?php $attendance_date=date("Y-m-d",strtotime($_REQUEST['df']));?>
                    <span>
                        <strong style="font-size: 18px;">
                            Total In :
                            <?php 
                                if($_REQUEST['df']!="")
                                {
                                   echo  $get_in_total = $db->rp_getTotalRecord("attendance","inout_status='In' AND DATE(date_time)>='".date("Y-m-d",strtotime($date_filter_query_ex['0']))."' AND DATE(date_time)<='".date("Y-m-d",strtotime($date_filter_query_ex['1']))."'  AND isDelete=0",0);
                                }
                                else
                                {
                                  echo  $get_in_total = $db->rp_getTotalRecord("attendance","inout_status='In' AND isDelete=0",0);  
                                } 
                            ?>                
                        </strong>
                    </span>
                    <br>
                    <span>
                        <strong style="font-size: 18px;">
                            Total Out :
                            <?php 
                                if($_REQUEST['df']!="")
                                {
                                   echo  $get_out_total = $db->rp_getTotalRecord("attendance","inout_status='Out' AND DATE(date_time)>='".date("Y-m-d",strtotime($date_filter_query_ex['0']))."' AND DATE(date_time)<='".date("Y-m-d",strtotime($date_filter_query_ex['1']))."'  AND isDelete=0",0);
                                }
                                else
                                {
                                  echo  $get_out_total = $db->rp_getTotalRecord("attendance","inout_status='Out' AND isDelete=0",0);  
                                } 
                            ?>                
                        </strong>
                    </span>
                    <br>
                    <span>
                        <strong style="font-size: 18px;">
                            Not Present :
                            <?php 
                                if($_REQUEST['df']!="")
                                {
                                    $present_sales_executive_r = $db->rp_getData("attendance","sales_id","DATE(date_time)>='".date("Y-m-d",strtotime($date_filter_query_ex['0']))."' AND DATE(date_time)<='".date("Y-m-d",strtotime($date_filter_query_ex['1']))."'  AND isDelete=0 GROUP BY sales_id","",0);
                                    if(mysqli_num_rows($present_sales_executive_r) > 0){
                                        $present_sales_executive_array = array();
                                        while($present_sales_executive_d = mysqli_fetch_assoc($present_sales_executive_r)){
                                            $present_sales_executive_array[] = $present_sales_executive_d['sales_id'];

                                        }
                                        $present_sales_executive = implode(",",$present_sales_executive_array);
                                        echo $db->rp_getValue("sales_executive","count(id)","isDelete = 0 AND isActive = 1 AND id NOT IN(".$present_sales_executive.")");
                                    }else{
                                        echo 0;
                                    }
                                }
                                else
                                {
                                    $present_sales_executive_r =$present_sales_executive = $db->rp_getData("attendance","sales_id"," isDelete=0 GROUP BY sales_id","",0);
                                    if(mysqli_num_rows($present_sales_executive_r) > 0){
                                        $present_sales_executive_array = array();
                                        while($present_sales_executive_d = mysqli_fetch_assoc($present_sales_executive_r)){
                                            $present_sales_executive_array[] = $present_sales_executive_d['sales_id'];

                                        // print_r($present_sales_executive_d);
                                        }
                                        $present_sales_executive = implode(",",$present_sales_executive_array);
                                        echo $db->rp_getValue("sales_executive","count(id)","isDelete = 0 AND isActive = 1 AND id NOT IN(".$present_sales_executive.")");  
                                    }else{
                                        echo 0;
                                    }
                                } 
                            ?>                
                        </strong>
                    </span>
              </div>
<form action="" name="frm" id="frm" method="post">
     <span style="color: red;font-size: 14px;font-style: italic;"><?= CURRENT_DATA_INFO ?></span>
    <div class="table-scrollable">
        <table id="datatable_1" class="table table-striped table-bordered table-hover">
            <thead class="fix-th">
                
                <tr>
                     <?php
                    if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
                    {
                    ?>
                    <th></th>
                    <?php
                     }
                     ?>
                    <th></th>
                    <th>
                        <?php
                        if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==13)
                        {
                        ?>
                        <select class="form-control input-small" name="sales_executive" id="sales_executive" style="width:100px">
                            <option value="">Sales Person</option>
                            <?php
                                $D_r = $db->rp_getData("sales_executive","id,name","isDelete=0 AND isActive=1 AND type!='service_engineer'","",0);
                                while ($D = mysqli_fetch_assoc($D_r))
                                {
                                    ?>
                                    <option value="<?=$D['id']?>" <?=($sid == $D['id'])?"selected":"";?>><?=$D['name']?></option>
                                    <?php
                                }
                            ?>
                        </select>
                        <?php
                        }

                        ?>
                    </th>
                    <th></th>
                    <th>
                        <!-- <label>Filter By Date</label> -->
                        <?php 
                            // $date_filter_query = urldecode($_REQUEST['df'] );
                            // $date_filter_query_ex=explode(" to ",$date_filter_query);
                            // $date_filter_query = date('d-m-Y',strtotime($date_filter_query));
                            // $date_filter_query=date("d-m-Y",strtotime($date_filter_query_ex['0']))." to ".date("d-m-Y",strtotime($date_filter_query_ex['1']));
                            ?>

                        <div class="input-group" style="width:200px">
                            <input class="form-control datetimerange-picker-input " id="material_request_filter_input" value="<?php echo $date_filter_query; ?>" name="df" placeholder="Select Date Range" type="text" style="border-radius: 0;height: 34px!important;">
                            <span class="input-group-addon datetimerange-picker-btn">
                            <i class="fa fa-calendar"></i>
                            </span>
                                        
                            <span class="input-group-btn">
                                <button class="btn btn-success filterBtn" id="fnt" type="button" value="search">Filter</button>
                            </span>
                        </div>
                        <!-- <button class="btn btn-success filterBtn" type="submit" value="search">Filter</button>           -->
                    </th>
                    <th class="io_select">
                        <select class="form-control input-small" name="io" id="io">style=
                            <?php
                            $io_r = $db->rp_getData("attendance","*","","",0);
                            ?>
                            <option value="">In/Out</option>
                            <option value="In" <?= strtolower($io)==strtolower("In")?"selected":""; ?>>In</option>
                            <option value="Out" <?= strtolower($io)==strtolower("Out")?"selected":""; ?>>Out</option>
                        </select>
                    </th>
                    <th class="input-group" style="width:200px">
                        <select class="form-control status" name="state" id="state" autofocus >
                            <option value="">Select State</option>
                            <?php
                            $state_r = $db->rp_getData("state","*","isDelete=0","",0);
                            if(mysqli_num_rows($state_r)>0)
                            {
                                while($state_d = mysqli_fetch_array($state_r))
                                {
                                    ?>
                                    <option value="<?php echo $state_d['name']; ?>" <?=($state == $state_d['name'])?"selected":"";?>><?php echo $state_d['name']; ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </th>
                   <!--  <th>
                        <select class="form-control input-small" name="attendance_type" id="attendance_type">
                            <?php
                                //$io_r = $db->rp_getData("attendance","*","","",0);
                                ?>
                                <option value="">select attendance type</option>
                                <option value="1" <?= ($attendance_type=="1")?"selected":""; ?>>In</option>
                                <option value="2" <?= ($attendance_type=="2")?"selected":""; ?>>Out</option>
                                <option value="3" <?= ($attendance_type=="3")?"selected":""; ?>>Auto Out</option>
                                <option value="4" <?= ($attendance_type=="4")?"selected":""; ?>>Logout With Out</option>
                                <option value="5" <?= ($attendance_type=="5")?"selected":""; ?>>Out On Nextday</option>
                                <option value="5" <?= ($attendance_type=="6")?"selected":""; ?>>Out From Server</option>
                        </select>
                    </th> -->
                    <th></th>
                    <th></th>
                    <th></th>
                    <!-- <th></th>
                    <th></th> -->
                </tr>
                <tr>
                    <?php
                    if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
                    {
                    ?>
                    <th>Action</th>
                    <?php
                    }
                    ?>
                    <th class="fix-th1">No.</th>
                    <th class="fix-th1" style="width:100px">Sales Person Name</th>
                    <th class="fix-th1" style="width:70px">CUG No.</th>
                    <th class="fix-th1" style="width:180px">Date and Time</th>
                    <th class="fix-th1" style="width:80px">In/Out</th>
                    <!-- <th>Attendance Type</th> -->
                    <th class="fix-th1" >State</th>
                    <th class="fix-th1" style="width: 100px;">City</th>
                    <!-- <th>Imei</th> -->
                    <!-- <th>Location Map</th> -->
                    <th class="fix-th1" style="width:400px">Address</th>
                    <th class="fix-th1" style="width: 15px!important;">Image</th>
                </tr>
            </thead>
            <tbody>
              <?php
               if(mysqli_num_rows($ctable_r)>0){
                $count = 0;
                $AttandanceType= array('1' =>"In" ,'2' =>"Out" ,'3' =>"Auto Out" ,'4' =>"Logout With Out" ,'5' =>"Out On Nextday"  ,"6"=>"Out from server");
                while($ctable_d = mysqli_fetch_array($ctable_r)){

                if ($ctable_d['image_path']!="" && file_exists(ATTENDANCE.$ctable_d['image_path'])) 
                {
                    $img = ATTENDANCE.$ctable_d['image_path'];
                }
                else
                {
                    $img = $ctable_d['image_path'] = DEFAULTIMG;
                }

                $sales_person_table_r = $db->rp_getData("sales_executive","*","id='".$ctable_d['sales_id']."' AND isDelete=0 ","",0); 
                $sales_person_table_d = mysqli_fetch_assoc($sales_person_table_r);
                //print_r($ctable_d);
                ?>
                <tr>
                    <?php
                    if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
                    {
                    ?>
                    <td>
                        <button style="margin-right: -5px;" type="button" name="button" value="print"  onClick="DeleteAttendance('<?php echo $ctable_d["id"]; ?>')" class="btn red btn-sm">
                                    <i class="fa fa-trash" aria-hidden="true"></i>
                        </button>
                    </td>
                    <?php } ?>
                    <td><?php echo ++$count; ?></td>
                    <td><?php echo $sales_person_table_d['name']?></td>
                    <td><?php echo $sales_person_table_d['phone']?></td>
                    <td><?php echo date("d-m-Y h:i:s A",strtotime($ctable_d['date_time'])); ?></td>
                    <td><?php echo $ctable_d['inout_status']; ?></td>
                    <!-- <td><?=  $AttandanceType[$ctable_d['attandance_type']]; ?></td> -->
                    <td><?php echo  $sales_person_table_d['state']?></td>
                    <td><b>
                        <?php  
                        $numbers = $ctable_d['app_address'];
                        $arr = explode(',', $numbers);
                        echo $arr[count($arr) - 3];
                        ?>
                        </b>
                    </td>
                    <!-- <td><?php echo $ctable_d['imei']; ?></td> -->
                    <!-- <td> -->
                        <!-- Trigger the modal with a button -->
                      <!--   <a class="mapbtn" data-app_address="<?php echo stripslashes($ctable_d['app_address']); ?>" data-lat="<?php echo stripslashes($ctable_d['latitude']); ?>" data-long="<?php echo stripslashes($ctable_d['longitude']); ?>" data-date="<?=date("d M H:i",strtotime($ctable_d['created_date']));?>" data-salesexename="<?=$db->rp_getValue("sales_executive","name","id='".$ctable_d["sales_id"]."'",0);?>" data-toggle="modal" data-target="#OpenMap">
                            <img src="<?=SITEURL?>resource/map.png" style="height: 80px;">
                        </a> -->
                    <!-- </td> -->
                   <td style="width: 15px!important;"><a href='#InTimeData' data-id="<?= $ctable_d['id'];?>" data-toggle='modal'><?= $ctable_d['app_address'];?></a></td>
                                          
                    <!-- <td><?php echo $ctable_d['app_address']; ?></td> -->
                    <td style="width: 15px!important;">
                      <a href="<?=$img?>" data-lightbox="attendance<?=$count?>" data-title="attendance <?=$ctable_d['id']?>"><img src="<?=$img?>" style="height: 150px;width:100px;"></a>
                        <!-- <img src="<?php echo $img ?>" height="100px" width="auto"> -->
                    </td>
                </tr>
            <?php
                }
            }
            ?>
            </tbody>
        </table>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="dataTables_info"> Rows Limit:
                <select id="numRecords" onChange="changeDisplayRowCount(this.value);">
                    <option value="50" <?php if ($_REQUEST["show"] == 50 || $_REQUEST["show"] == "") {
                                            echo ' selected="selected"';
                                        }  ?>>50</option>

                    <option value="100" <?php if ($_REQUEST["show"] == 100 || $_REQUEST["show"] == "") {
                                            echo ' selected="selected"';
                                        }  ?>>100</option>
                    <option value="500" <?php if ($_REQUEST["show"] == 500 || $_REQUEST["show"] == "") {
                                            echo ' selected="selected"';
                                        }  ?>>500</option>
                    <option value="1000" <?php if ($_REQUEST["show"] == 1000) {
                                            echo ' selected="selected"';
                                        }  ?>>1000</option>
                    <option value="2000" <?php if ($_REQUEST["show"] == 2000) {
                                                echo ' selected="selected"';
                                            }  ?>>2000</option>
                    <option value="5000" <?php if ($_REQUEST["show"] == 5000) {
                                                echo ' selected="selected"';
                                            }  ?>>5000</option>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="dataTables_paginate paging_simple_numbers">
                <ul class="pagination">
                <?php 
                echo $db->rp_paginate_function($item_per_page, $page_number, $get_total_rows, $total_pages); 
                ?>
                </ul>
            </div>
        </div>
    </div>
</form>

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

<script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js">
</script>
<script async defer
src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDklPuT2SCmcmlflaoZ4B0WywYK_em79x4&callback=initMap">
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
    function DeleteAttendance(id)
        {
            //alert(id);
            var r = confirm('Are You Sure Want To Delete Attendance?');
            if(r)
            {
                $.ajax({
                    url:"attendance_delete_ajax_function.php",
                    type:"POST",
                    data:{
                        m:'delete_attendance',
                        id:id,                
                        
                    },
                    beforeSend: function() {
                        $('.preloader').fadeIn('slow');
                    },
                    success:function(result) 
                    {
                        console.log(result);
                        var result=JSON.parse(result);
                        $('.preloader').fadeOut('slow');
                        if(result.ack==1)
                        {                       
                            toastr.success(result.ack_msg,"Success!!"); 
                             displayRecords(100,1);
                        }
                    },            
                })
            }
        }
    $("#sales_executive").select2();
    $("#io").select2();
    $("#attendance_type").select2();
    $("#state").select2();
</script>
<script type="text/javascript">
    $(".filterBtn").on("click",function()
    {
        df1=$("#material_request_filter_input").val();
        sales_executive=$("#sales_executive").val();
        df1 = encodeURI(df1)
        displayRecords(100,1);
    })
    $(".datetimerange-picker-btn").on("click",function(){
        $(".datetimerange-picker-input",$(this).closest(".date")).focus();
    });
    $(".datetimerange-picker-input").daterangepicker({"format":"dd-mm-yy ",autoUpdateInput: false,timePicker:false,ranges: {
           'Today': [moment(), moment()],
           'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
           'Last 7 Days': [moment().subtract(6, 'days'), moment()],
           'Last 30 Days': [moment().subtract(29, 'days'), moment()],
           'This Month': [moment().startOf('month'), moment().endOf('month')],
           'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
}});
    $('.datetimerange-picker-input').on('apply.daterangepicker', function(ev, picker) {
 $(".datetimerange-picker-input").val(picker.startDate.format('DD-MM-YYYY')+" to "+picker.endDate.format('DD-MM-YYYY'));
});
</script>
<?php require_once("disconnect.php"); ?>