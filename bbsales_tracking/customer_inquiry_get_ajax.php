<?php
$page_id=605;$page_slug='customer_inquiry';
/*
 * @author Ravi Patel
 */
include("connect.php");
$ctable 	= "customer_inquiry";
$ctable1 	= "Inquiry";
$ctable_where = "";
// Get the total number of rows in the table
if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
	
	$ctable_where .="(company_name like '%".$_REQUEST['searchName']."%' OR mobile_number like '%".$_REQUEST['searchName']."%' OR id like '%".$_REQUEST['searchName']."%' OR person_name like '%".$_REQUEST['searchName']."%' OR country like '%".$_REQUEST['searchName']."%' OR state like '%".$_REQUEST['searchName']."%' OR city like '%".$_REQUEST['searchName']."%' OR email_address like '%".$_REQUEST['searchName']."%') AND ";
}

if(isset($_REQUEST['status_id']) && $_REQUEST['status_id']!="")
{
	$ctable_where .= " status='".$_REQUEST['status_id']."' AND ";
	$status_id=$_REQUEST['status_id'];
}

$ctable_where .= " isDelete=0";

$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"]) ) ? intval($_REQUEST["show"]) : 100;

if(isset($_REQUEST["page"])){
	$page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
	if(!is_numeric($page_number)){die('Invalid page number!');} //incase of invalid page number
}else{
	$page_number = 1; //if there's no page number, set it to 1
}

$get_total_rows = $db->rp_getTotalRecord($ctable,$ctable_where); //hold total records in variable

if(isset($_REQUEST['se_id']) && $_REQUEST['se_id']!="" && $_REQUEST['se_id']!=NULL)
{
    $ctable_where .= " AND sales_executive_id = '".$_REQUEST['se_id']."' ";
}

if(isset($_REQUEST['c_type']) && $_REQUEST['c_type']!="" && $_REQUEST['c_type']!=NULL)
{
    $ctable_where .= " AND executive_type = '".$_REQUEST['c_type']."' ";
    $c_type=$_REQUEST['c_type'];
}

if(isset($_REQUEST['country']) && $_REQUEST['country']!="" && $_REQUEST['country']!=NULL)
{
    $ctable_where .= " AND country = '".$_REQUEST['country']."' ";
    $country=$_REQUEST['country'];
}

if(isset($_REQUEST['state']) && $_REQUEST['state']!="" && $_REQUEST['state']!=NULL)
{
    $ctable_where .= " AND state = '".$_REQUEST['state']."' ";
    $state=$_REQUEST['state'];
}

if(isset($_REQUEST['city']) && $_REQUEST['city']!="" && $_REQUEST['city']!=NULL)
{
    $ctable_where .= " AND city = '".$_REQUEST['city']."'";
    $city=$_REQUEST['city'];
}

if(isset($_REQUEST['type']) && $_REQUEST['type']!="" && $_REQUEST['type']!=NULL)
{
    $ctable_where .= " AND sales_executive_id = '".$_REQUEST['type']."' ";
    $type=$_REQUEST['type'];
}

if(isset($_REQUEST['df']) && $_REQUEST['df']!=""){
    //echo $_REQUEST['df'];exit;
    $date_filter_query = urldecode( $_REQUEST['df'] );

    $date_filter_query_ex=explode(" to ",$date_filter_query);

    $ctable_where .= " AND ( DATE(date_of_call)>='".date("Y-m-d",strtotime($date_filter_query_ex['0']))."' AND DATE(date_of_call)<='".date("Y-m-d",strtotime($date_filter_query_ex['1']))."'  ) ";
}

if(isset($_REQUEST['assigned_to']) && $_REQUEST['assigned_to']!="" && $_REQUEST['assigned_to']!=NULL)
{
    $ctable_where .= " AND inquiry_assign_to = '".$_REQUEST['assigned_to']."' ";
    $assigned_to=$_REQUEST['assigned_to'];
}
	


if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
{
    $check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];
    $ctable_where .= " AND (inquiry_assign_to = '".$check_id."' OR inquiry_created_by = '".$check_id."') ";
}


$get_total_rows = $db->rp_getTotalRecord($ctable,$ctable_where,0); //hold total records in variable

//break records into pages
$total_pages = ceil($get_total_rows/$item_per_page);

//get starting position to fetch the records
$page_position = (($page_number-1) * $item_per_page);

$ctable_r = $db->rp_getData($ctable,"*",$ctable_where,"id DESC limit $page_position, $item_per_page",0);
?>
<style type="text/css">
.table-scrollable 
{
    width: auto;
    height: 810px;
    overflow-x: scroll;
    overflow-y: scroll;
}
</style>
<form action="" name="frm" id="frm" method="post">
	<div class="table-scrollable">
	<table id="datatable_1" class="table table-striped table-bordered table-hover">
        <thead>
        	<tr>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
        		<th>
                    <select class="form-control" id="c_type" name="c_type" style="width:120px;text-align:center;margin: auto;">
                        <option value="">Select Customer Type</option>
                        <?php
                        $customer_type = $db->rp_getData("customer_type","*","isDelete=0");
                        if($customer_type)
                        {
                            while($customer_type_d = mysqli_fetch_assoc($customer_type))
                            {?>
                                <option value="<?=$customer_type_d['id']?>" <?=($c_type == $customer_type_d['id'])?"selected":"";?>><?=$customer_type_d['name']?></option>
                            <?php
                            }
                        } 
                        ?>
                    </select>     
                </th>
                <th></th>
        		<th></th>
        		<th></th>
                <th></th>
        		<th>
                    <select class="form-control" name="country" id="country" onChange="filter_country(this.value);">
                        <option value="">Select Country</option>
                        <?php
                        $country_r = $db->rp_getData("country","*",0);
                        if(mysqli_num_rows($country_r)>0)
                        {
                            while($country_d = mysqli_fetch_array($country_r))
                            {
                                ?>
                                <option value="<?php echo $country_d['name']; ?>" <?=($country == $country_d['name'])?"selected":"";?>><?php echo $country_d['name']; ?></option>
                                <?php
                            }
                        }
                        ?>
                    </select>      
                </th>
        		<th>
                    <select class="form-control" name="state" id="state" autofocus onChange="filter_state(this.value);">
                        <option value="">Select State</option>
                    </select> 
                    <?php ?>     
                </th>
        		<th>
                    <select class="form-control" name="city" id="city">
                        <option value="">Select City</option>
                    </select>      
                </th>
                <th>
                <!-- <label>Filter By Date</label> -->
                    <div class="input-group">
                        <input class="form-control datetimerange-picker-input " id="material_request_filter_input" value="<?php echo $date_filter_query; ?>" name="df" placeholder="Select Date Range" type="text" style="border-radius: 0;height: 34px!important;">
                        <span class="input-group-addon datetimerange-picker-btn">
                        <i class="fa fa-calendar"></i>
                        </span>
                                    
                        <span class="input-group-btn">
                            <!-- <button class="btn btn-success filterBtn" type="submit" value="search">Filter</button> -->
                        </span>
                    </div>
                    <button class="btn btn-success filterBtn" type="submit" value="search">Filter</button>          
                </th>
        		<th>
                     <select class="form-control" name="type" id="type" onChange="getSalesExecutive(this.value);">
                        <option value="">Select Inquiry Taken By</option>
                        <?php 
                            $se_r=$db->rp_getData("sales_executive","*","isDelete=0 AND isActive=1");
                            
                            if($se_r){
                                while($se_d=mysqli_fetch_assoc($se_r)){
                                    ?>
                                    <option value="<?php echo $se_d['id'];?>" <?=($type == $se_d['id'])?"selected":"";?>><?php echo $se_d['name']; ?></option>
                                    <?php
                                }
                            }
                        ?>
                    </select>     
                </th>
        		<th>
                    <select class="form-control" name="assigned_to" id="assigned_to" onChange="getSalesExecutive(this.value);">
                        <option value="">Select Inquiry Assigned By</option>
                        <?php 
                            $se_r=$db->rp_getData("sales_executive","*","isDelete=0 AND isActive=1");
                            
                            if($se_r){
                                while($se_d=mysqli_fetch_assoc($se_r)){
                                    ?>
                                    <option value="<?php echo $se_d['id'];?>" <?=($type == $se_d['id'])?"selected":"";?>><?php echo $se_d['name']; ?></option>
                                    <?php
                                }
                            }
                        ?>
                    </select>           
                </th>
                <th></th>
            </tr>
            <tr>
                <th style="width: 5%;"></th>
            	<th>Sr No.</th>  
                <th>Followup</th>     
                <th>Status</th>     
                <th>Source Medium</th>
            	<th>Customer Type</th>
                <th>Company Name</th>
                <th>Person Name</th>
                <th>Mobile Number</th>
                <th>Email Address</th>
                <th>Country</th>
                <th>State</th>
				<th>City</th>				
				<th>Date Of Call</th>
				<th>Inquiry Taken By</th>
                <th>Inquiry Assigned to</th>
                <th>Image Path</th>                
            </tr>
        </thead>
        <tbody>
        <?php
		
        if(mysqli_num_rows($ctable_r)>0){
            $count = 0;
            
            while($ctable_d = mysqli_fetch_array($ctable_r)){
                $inquiry_type_array = array("1"=>"Email","2"=>"Call","3"=>"Whatsapp","4"=>"India Mart","5"=>"Just Dial","6"=>"Trade India","7"=>"Ali baba","8"=>"Facebook","9"=>"Instagram");
        ?>
            <tr>
                <td><?php $ctable_d['id']; ?>               
                <?php               
                if($rights['update_flag']==1)
                {
                    ?>
                    <div class="btn-group">             
                        <button aria-expanded="false" data-toggle="dropdown" type="button" class="btn btn-sm blue dropdown-toggle">
                            <i class="fa fa-gear"></i>
                        </button>
                        <ul role="menu" class="dropdown-menu">
                            <li>
                                <a href="<?php echo $ctable; ?>_crud.php?mode=edit&id=<?php echo $ctable_d['id']; ?>" title="Edit">
                                    <span class="text-primary">
                                        <i class="fa fa-pencil"></i>
                                        &nbsp;Edit
                                    </span>
                                </a>
                            </li>
                            <?php
                            if($rights['delete_flag']==1 && $ctable_d['id']!=-1)
                            {
                                ?>
                                <li>
                                    <a onClick="del_conf('<?php echo $ctable_d['id']; ?>');" title="Delete">
                                        <span class="text-danger">
                                            <i class="fa fa-times"></i>
                                            &nbsp;Delete
                                        </span>
                                    </a>
                                </li>
                                <?php
                            }
                            ?>  
                        </ul>
                    </div>
                    <?php
                }
                ?>
            </td>
            	<td><?php echo ++$count; ?></td>
                <td> <a href="followup.php?mode=leads_followup&inquiry_id=<?php echo $ctable_d['id']?>&sales_id=<?php echo $ctable_d['sales_executive_id']?>" class="btn btn-primary btn-sm" title="track">View</a></td>
                <td>
                    <select class="form-control" disabled="disabled" id="inquiry_status<?= $ctable_d['id']?>" style="width:120px;text-align:center;margin: auto;">
                        <option value="">Select Status</option>
                        <option <?= ($ctable_d['status']==0)?"selected":""; ?> value="0">Generate</option>              
                        <option <?= ($ctable_d['status']==1)?"selected":""; ?> value="1">In Followup</option>
                        <option <?= ($ctable_d['status']==2)?"selected":""; ?> value="2">Interested</option>
                        <option <?= ($ctable_d['status']==-1)?"selected":""; ?> value="-1">Not Interested</option>
                        <option <?= ($ctable_d['status']==3)?"selected":""; ?> value="3">Working</option>
                    </select>
                        <a href="javascript:void(0);" id="editStatus_<?php echo $ctable_d['id']; ?>" onClick="editStatus('<?php echo $ctable_d['id']; ?>')">Edit</a>                    
                        <span id="editStatus2_<?php echo $ctable_d['id']; ?>" style="display:none;">
                            <a href="javascript:void(0);" id="saveEditStatus<?php echo $ctable_d['id']; ?>" onClick="saveEditStatus('<?php echo $ctable_d['id']; ?>')">Save</a> |
                            <a href="javascript:void(0);" id="cancelEditStatus<?php echo $ctable_d['id']; ?>" onClick="cancelEditStatus('<?php echo $ctable_d['id']; ?>')">Cancel</a>
                        </span>
                </td>
                <td><?php  echo $inquiry_type_array[$ctable_d['source_of_inquiry']]; ?></td>
				<td><?php echo $db->rp_getValue("customer_type","name","id='".$ctable_d['executive_type']."'"); ?></td>
                 <td><span class="<?php echo ($ctable_d['isActive']==0)?"text-danger":"text-success"; ?>"><?php echo $ctable_d['company_name']; ?></span></td>
				<td><span class="<?php echo ($ctable_d['isActive']==0)?"text-danger":"text-success"; ?>"><?php echo $ctable_d['person_name']; ?></span></td>
				<td><i class="fa fa-phone"></i>&nbsp;<a target="_blank" href="https://api.whatsapp.com/send?phone=91<?php echo stripslashes($ctable_d['mobile_number']); ?>&text=<?= $sms; ?>"><?php echo $ctable_d['mobile_number']; ?></a></td>
				<td><?php if($ctable_d['email_address']!="'"){?><a target="_blank" href="mailto:<?php echo $ctable_d['email_address']; ?>"><?php }?><?php echo $ctable_d['email_address']; ?><?php if($ctable_d['email_address']!="'"){?></a><?php }?></td>
                <td><?php echo $ctable_d['country']; ?></td>
				<td><?php echo $ctable_d['state']; ?></td>
				<td><?php echo $ctable_d['city']; ?></td>
                <td><?php if($ctable_d['date_of_call']=="0000-00-00 00:00:00" || $ctable_d['date_of_call']=="1970-01-01" ){ echo "";} else { echo date('d-m-Y',strtotime($ctable_d['date_of_call'])); } ?></td>
                <td>
                <?php               
                $sales_executive=$db->rp_getValue("sales_executive","name","id='".$ctable_d['sales_executive_id']."'");?>
                <?php echo stripslashes($sales_executive); ?></td>
				<td>
                <?php               
                $inquiry_assign_to=$db->rp_getValue("sales_executive","name","id='".$ctable_d['inquiry_assign_to']."'");?>
                <?php echo stripslashes($inquiry_assign_to); ?></td>
                <td>
                <?php 
                    if($ctable_d['image_path']!="")
                    {
                        $img = explode(",", $ctable_d['image_path']);
                        $imgpath = array();
                        for ($i=0; $i < sizeof($img); $i++)
                        { 
                            $imgpath[] = SITEURL."resource/image/".$db->rp_getValue("media","url","reference_id='".$ctable_d["id"]."' AND id='".$img[$i]."'",0);
                        }

                        for ($i=0; $i < sizeof($imgpath); $i++)
                        {
                            if($i==0){
                            ?>
                                <a href="<?=$imgpath[$i]?>" data-lightbox="complain<?=$count?>" data-title="complain <?=$ctable_d['id']?>"><img src="<?=$imgpath[$i]?>" style="height: 80px;"></a>
                            <?php 
                            }else{
                            ?>
                                <div class="hidden">
                                    <a href="<?=$imgpath[$i]?>" data-lightbox="complain<?=$count?>" data-title="complain <?=$ctable_d['id']?>"><img src="<?=$imgpath[$i]?>" style="height: 80px;"></a>
                                </div>
                            <?php
                            }
                        }
                    }
                    else
                    {
                        $img = $ctable_d['image_path'] = DEFAULTIMG;
                    ?>
                        <a href="<?=$img?>" data-lightbox="attendance<?=$count?>" data-title="attendance <?=$ctable_d['id']?>"><img src="<?=$img?>" style="height: 80px;"></a>
                    <?php
                    }
                    ?>
                </td>

            </tr>
        <?php
            }
		}
		else
		{
			?>
			<tr>
				<th colspan="14" style="text-align: center;">No Data Found</th>
			</tr>
			<?php
		}
		
		?>
	
        </tbody>
    </table>	
	</div>
    <div class="row">
		<div class="col-md-6">
			<div class="dataTables_info"> Rows Limit:
				<select id="numRecords" onChange="changeDisplayRowCount(this.value);">
					<option value="100" <?php if ($_REQUEST["show"] == 100 || $_REQUEST["show"] == "" ) { echo 'selected="selected"'; }  ?> >100</option>
					<option value="500" <?php if ($_REQUEST["show"] == 500) { echo ' selected="selected"'; }  ?> >500</option>
					<option value="1000" <?php if ($_REQUEST["show"] == 1000) { echo ' selected="selected"'; }  ?> >1000</option>
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
<script type="text/javascript">
    $("#country").select2();
    $("#state").select2();
    $("#city").select2();
    $("#c_type").select2();
    $("#type").select2();
    $("#assigned_to").select2();
</script>
<script type="text/javascript">
    $(".filterBtn").on("click",function()
    {
        sales_executive = $("#sales_executive").val();
        customer_id = $("#customer_id").val();
        df1=$("#material_request_filter_input").val();
        df1 = encodeURI(df1)
        callAjax();
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
<?php require_once "disconnect.php"; ?>