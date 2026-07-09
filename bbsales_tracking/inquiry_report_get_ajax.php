<?php
// print_r($_REQUEST);exit;
$page_id=600;$page_slug='inquiry_report_page';
/*
 * @author Ravi Patel
 */
include("connect.php");
$ctable 	= "no_order_inquiry";
$ctable1 	= "Inquiry";
$ctable_where = "";
$isFillter = filter_var($_REQUEST['isFillter'], FILTER_VALIDATE_BOOLEAN);
// Get the total number of rows in the table
if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
	
	$ctable_where .="company_name like '%".$_REQUEST['searchName']."%' OR mobile_number like '%".$_REQUEST['searchName']."%' OR id like '%".$_REQUEST['searchName']."%' OR person_name like '%".$_REQUEST['searchName']."%'  AND ";
    $isFillter=true;
}

$ctable_where .= " isDelete=0 AND inquiry_lead_flag=0";

if(isset($_REQUEST['status_id']) && $_REQUEST['status_id']!="" && $_REQUEST['status_id']!="null")
{
	//$ctable_where .= " AND status='".$_REQUEST['status_id']."' ";
	$ctable_where .= " AND status IN (".$_REQUEST['status_id'].") ";
    $isFillter=true;
}



// if(isset($_REQUEST['state']) && $_REQUEST['state']!="" && $_REQUEST['state']!=NULL && $_REQUEST['state']!="null")
// {
// 	$stringA = $_REQUEST['state'];
//     $stringAArray = explode(",",$stringA);
//     $stringiO = "(";
//     foreach ($stringAArray as $key => $value) {
//         $stringiO .= "'".$value."',";
//     }
//     $stringiO = rtrim($stringiO,",");
//     $stringiO .= ")";
//  	$ctable_where .= " AND state IN ".$stringiO." ";
// }

// if(isset($_REQUEST['city']) && $_REQUEST['city']!="" && $_REQUEST['city']!=NULL && $_REQUEST['city']!="null")
// {
// 	$stringD = $_REQUEST['city'];
//     $stringDArray = explode(",",$stringD);
//     $stringio = "(";
//     foreach ($stringDArray as $key => $value) {
//         $stringio .= "'".$value."',";
//     }
//     $stringio = rtrim($stringio,",");
//     $stringio .= ")";
//     $ctable_where .=" AND main_city IN ".$stringio." ";
// }

// if(isset($_REQUEST['route']) && $_REQUEST['route']!="" && $_REQUEST['route']!=NULL && $_REQUEST['route']!="null")
// {
//     $stringD = $_REQUEST['route'];
//     $stringDArray = explode(",",$stringD);
//     $stringio = "(";
//     foreach ($stringDArray as $key => $value) {
//         $stringio .= "'".$value."',";
//     }
//     $stringio = rtrim($stringio,",");
//     $stringio .= ")";
//     $ctable_where .=" AND city IN ".$stringio." ";
// }


if(isset($_REQUEST['state']) && $_REQUEST['state']!="" && $_REQUEST['state']!=NULL && $_REQUEST['state']!="null")
{
    $state_r = $db->rp_getData("state","name","id in (".$_REQUEST['state'].")","",0);
    while($state_d = mysqli_fetch_array($state_r)) 
    {
        $state_str[] = "'".$state_d['name']."'";
    }
    $class_str = implode(",",$state_str);
    $ctable_where .= " AND  state IN (".$class_str.") ";
    $isFillter=true;
}
//for area----//
if(isset($_REQUEST['city']) && $_REQUEST['city']!="" && $_REQUEST['city']!=NULL && $_REQUEST['city']!="null")
{
    $city_r = $db->rp_getData("city","name","id in (".$_REQUEST['city'].")","",0);
    while($city_d = mysqli_fetch_array($city_r)) 
    {
        $city_str[] = "'".$city_d['name']."'";
    }
    // echo implode(",",$city_str);exit;
    $ctable_where .= " AND main_city IN (".implode(",",$city_str).") ";
    $isFillter=true;
            
}

if(isset($_REQUEST['route']) && $_REQUEST['route']!="" && $_REQUEST['route']!=NULL && $_REQUEST['route']!="null")
{
    $area_r = $db->rp_getData("area","name","id in (".$_REQUEST['route'].")","",0);
    while($area_d = mysqli_fetch_array($area_r)) 
    {
        $area_str[] = "'".$area_d['name']."'";
    }
    // echo implode(",",$area_str);exit;
    $ctable_where .= " AND city IN (".implode(",",$area_str).") ";
    $isFillter=true;
            
}

$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"]) ) ? intval($_REQUEST["show"]) : 100;

if(isset($_REQUEST["page"])){
	$page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
	if(!is_numeric($page_number)){die('Invalid page number!');} //incase of invalid page number
}else{
	$page_number = 1; //if there's no page number, set it to 1
}

// if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
// {
//     $check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];
//     $ctable_where .= " AND (inquiry_assign_to = '".$check_id."' OR inquiry_created_by = '".$check_id."') ";
// }

$get_total_rows = $db->rp_getTotalRecord($ctable,$ctable_where); //hold total records in variable

if(isset($_REQUEST['type']) && $_REQUEST['type']!="" && $_REQUEST['type']!=NULL && $_REQUEST['type']!="null" && $_REQUEST['type']!="undefined")
{
 	//$ctable_where .= " AND sales_executive_id = '".$_REQUEST['type']."' ";
 	//$ctable_where .= " AND sales_executive_id IN (".$_REQUEST['type'].") ";
 	$ctable_where .= " AND inquiry_assign_to IN (".$_REQUEST['type'].") ";
    $isFillter=true;
}

if(isset($_REQUEST['df']) && $_REQUEST['df']!=""){
	//echo $_REQUEST['df'];exit;
	$date_filter_query = urldecode( $_REQUEST['df'] );

	$date_filter_query_ex=explode(" to ",$date_filter_query);

	$ctable_where .= " AND ( DATE(datetime)>='".date("Y-m-d",strtotime($date_filter_query_ex['0']))."' AND DATE(datetime)<='".date("Y-m-d",strtotime($date_filter_query_ex['1']))."'  )";
    $isFillter=true;
}

if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
{
    if($rights['personal_flag']==1)
    {

         $check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];
         $ctable_where .= " AND (inquiry_assign_to = '".$check_id."' OR inquiry_created_by = '".$check_id."') ";


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
                    
                        $ctable_where .= " AND (inquiry_assign_to IN (".$SALEID1.','.$_SESSION[SITE_SESS.'REFERANCE_ID'].") OR inquiry_created_by IN (".$SALEID1.','.$_SESSION[SITE_SESS.'REFERANCE_ID']."))"; 
                    
                    
                }
                else
                {
                        $ctable_where .= " AND ( inquiry_assign_to IN (".$_SESSION[SITE_SESS.'REFERANCE_ID'].") OR inquiry_created_by IN(".$_SESSION[SITE_SESS.'REFERANCE_ID']."))";      
                }

        }
        else
        {

        }



    }

}

//service_executive user not show condition start --//
	$SEID=array();
	$sales_type_r=$db->rp_getData("sales_executive","*","type='service_executive'","",0);
	while($sales_type_d = mysqli_fetch_array($sales_type_r))
	{
		$SEID[] = $sales_type_d['id'];
	}
	$SEID=implode(",",$SEID);
	$ctable_where .="  AND sales_executive_id NOT IN ('".$SEID."')  ";
//service_executive user not show condition end	--//	
if(isset($_REQUEST['type_of_company']) && $_REQUEST['type_of_company']!="" && $_REQUEST['type_of_company']!="null" &&  $_REQUEST['type_of_company']!="0" )
{
    
    $ctable_where.=" AND type_of_company='".$_REQUEST['type_of_company']."'";
    $isFillter=true;
}
$get_total_rows = $db->rp_getTotalRecord($ctable,$ctable_where,0); //hold total records in variable

//break records into pages
$total_pages = ceil($get_total_rows/$item_per_page);

//get starting position to fetch the records
$page_position = (($page_number-1) * $item_per_page);
if ($isFillter) 
{
    $ctable_r = $db->rp_getData($ctable,"*",$ctable_where,"id DESC limit $page_position, $item_per_page",0);
    
}
?>

<style>
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



<div class="table-scrollable">
<form action="" name="frm" id="frm" method="post">
	<table id="datatable_1" class="table table-striped table-bordered table-hover">
        <thead class="fix-th">
            <tr>
            	<th class="fix-th1">Sr No.</th>
            	<th class="fix-th1">Source Of Inquiry</th>
                <th class="fix-th1">Inquiry No.</th>
                <th class="fix-th1">Firm Name</th>
                <th class="fix-th1">Person Name</th>
                <th class="fix-th1">Phone</th>
                <th class="fix-th1">State</th>
				<th class="fix-th1">City</th>				
                <th class="fix-th1">Route</th>    
				<th class="fix-th1">Description</th>
				<th class="fix-th1">Inquiry Date</th>
				<th class="fix-th1">Inquiry Taken By</th>
				<th class="fix-th1">Status</th>  
				<th class="fix-th1">Image Path</th>              
			</tr>
        </thead>
        <tbody>
        <?php
        if ($isFillter) 
        {
    		if(mysqli_num_rows($ctable_r)>0)
    		{
                $count = 0;
                while($ctable_d = mysqli_fetch_array($ctable_r))
                {
                	$inquiry_status_array = array("0"=>"Generate","1"=>"In Followup","3"=>"Buy Later","4"=>"Hot","5"=>"Cold","6"=>"Warm","-2"=>"Non Relavent","-1"=>"Not Interested","11"=>"Lost");
            		?>
                	<tr>
                	<td><?php echo ++$count; ?></td>
                	<td><?php  echo $db->rp_getValue("source_of_inquiry","name","id='".$ctable_d['source_of_inquiry']."'"); ?></td>
                	<td>#INQ/<?php  echo $ctable_d['id']; ?></td>
                    <td><span class="<?php echo ($ctable_d['isActive']==0)?"text-danger":"text-success"; ?>"><?php
    					echo $ctable_d['company_name']; ?></span></td>
    				<td><span class="<?php echo ($ctable_d['isActive']==0)?"text-danger":"text-success"; ?>"><?php
    					echo $ctable_d['person_name']; ?></span></td>
    				<td><?php echo $ctable_d['mobile_number']; ?></td>
    				<td><?php echo $ctable_d['state']; ?></td>
                    <td><?php echo $ctable_d['main_city']; ?></td>
    				<td><?php echo $ctable_d['city']; ?></td>
    				<td><?php echo $ctable_d['description']; ?></td>
    				<td><?php if($ctable_d['datetime']!="0000-00-00 00:00:00"){ echo date('d-m-Y',strtotime($ctable_d['datetime'])); } else{ echo "";} ?></td>
    				<?php $action=$db->rp_getValue("no_order_inquiry_action","name","id='".$ctable_d['action']."'");?>
    				<td><?php $sales_executive=$db->rp_getValue("sales_executive","name","id='".$ctable_d['sales_executive_id']."'");?>
    				<?php echo stripslashes($sales_executive); ?></td>
    				<td><?php echo $inquiry_status_array[$ctable_d['status']] ?></td>
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
                                if($i==0)
                                {
                                ?>
                                    <a href="<?=$imgpath[$i]?>" data-lightbox="complain<?=$count?>" data-title="complain <?=$ctable_d['id']?>"><img src="<?=$imgpath[$i]?>" style="height: 80px; width: 100px;"></a>
                                <?php 
                                }else{
                                ?>
                                    <div class="hidden">
                                        <a href="<?=$imgpath[$i]?>" data-lightbox="complain<?=$count?>" data-title="complain <?=$ctable_d['id']?>"><img src="<?=$imgpath[$i]?>" style="height: 80px; width: 100px;"></a>
                                    </div>
                                <?php
                                }
                            }
                        }
                        else
                        {
                            $img = $ctable_d['image_path'] = DEFAULTIMG;
                        ?>
                            <a href="<?=$img?>" data-lightbox="attendance<?=$count?>" data-title="attendance <?=$ctable_d['id']?>"><img src="<?=$img?>" style="height: 80px; width: 100px;"></a>
                        <?php
                        }
                        ?>
                    </td> 
                </tr>
            <?php
                }
                $new_count = $count;
    		}
    		else
    		{
    			?>
    			<tr>
    				<th colspan="14" style="text-align: center;">No Data Found</th>
    			</tr>
    			<?php
    		}
        }
        else
        {
           ?>
           <tr>
                <th colspan="14" style="text-align: center;"><h3><strong><?= FILTER_INFO ?></strong></h3></th>
            </tr>
           <?php 
        }
		
		?>
	
        </tbody>
    </table>
</form>
</div>
    <div class="row">
        <?php
            if ($isFillter) 
                {
        ?>
		<div class="col-md-6">
			<div class="dataTables_info"> Rows Limit:
				<select id="numRecords" onChange="changeDisplayRowCount(this.value);">
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
        <?php
            }
        ?>
		<div class="col-md-6">
			<div class="dataTables_paginate paging_simple_numbers">
				<ul class="pagination">
				<?php
                if ($isFillter) 
                {
    				echo $db->rp_paginate_function($item_per_page, $page_number, $get_total_rows, $total_pages); 
                } 
				?>
				</ul>
			</div>
		</div>
	</div>
<script type="text/javascript">
	$("#status_id").select2();
	$("#totalcount").html('<?php echo $new_count; ?>');
</script>
 <?php require_once("disconnect.php"); ?>