<?php
$page_id=581;$page_slug='manage_complain';
/*
 * @author Ravi Patel
 */
include("connect.php");
$ctable 	= "request";

$ctable_where = "";
$status_id="";
// Get the total number of rows in the table

if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){

	$ctable_where .= " (request_no like '%".$db->clean($_REQUEST['searchName'])."%') AND ";

	// $sales_id = $db->rp_getData("sales_executive","*","name LIKE '%".$_REQUEST['searchName']."%' AND isDelete=0","",0);
	// if($sales_id)
	// {		
	// 	while($K=mysqli_fetch_assoc($sales_id))
	// 	{
	// 		$USER_IDS[]=$K['id'];
	// 	}
	// 	$USER_IDS=implode(",",$USER_IDS);
	// 	$ctable_where .="user_id IN ('".$USER_IDS."') OR";
	// }
	// else
	// {
	// 	$ctable_where .="user_id IN ('') OR";
	// }

	// $customer_id = $db->rp_getData("executive","*","cname LIKE '%".$_REQUEST['searchName']."%' OR phone LIKE '%".$_REQUEST['searchName']."%' AND isDelete=0","",0);
	// if($customer_id)
	// {		
	// 	while($K1=mysqli_fetch_assoc($customer_id))
	// 	{
	// 		$CUSTOMER_IDS[]=$K1['id'];
	// 	}
	// 	$CUSTOMER_IDS=implode(",",$CUSTOMER_IDS);
	// 	$ctable_where .=" customer_id IN ('".$CUSTOMER_IDS."') AND";
	// }
	// else
	// {
	// 	$ctable_where .=" customer_id IN ('') AND ";
	// }
}

$ctable_where .= " isDelete=0 ";

$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"]) ) ? intval($_REQUEST["show"]) : 100;

if(isset($_REQUEST["page"])){
	$page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
	if(!is_numeric($page_number)){die('Invalid page number!');} //incase of invalid page number
}else{
	$page_number = 1; //if there's no page number, set it to 1
}
// print_r($_REQUEST["sales_executive"]);exit;
if(isset($_REQUEST["sales_executive"]) && $_REQUEST["sales_executive"]!="" && $_REQUEST["sales_executive"]!=undefined){
	$ctable_where .= " AND user_id='".$_REQUEST["sales_executive"]."'";
	$sid = $_REQUEST["sales_executive"];
}
if(isset($_REQUEST["customer_id"]) && $_REQUEST["customer_id"]!="" && $_REQUEST["customer_id"]!=undefined){
	$ctable_where .= " AND customer_id='".$_REQUEST["customer_id"]."'";
	$cid = $_REQUEST["customer_id"];
}

if(isset($_REQUEST['status_id']) && $_REQUEST['status_id']!="")
{
	$ctable_where .= " AND status='".$_REQUEST['status_id']."' ";
	$status_id=$_REQUEST['status_id'];
}

if(isset($_REQUEST['df']) && $_REQUEST['df']!=""){
	//echo $_REQUEST['df'];exit;
	$date_filter_query = urldecode( $_REQUEST['df'] );

	$date_filter_query_ex=explode(" to ",$date_filter_query);

	$ctable_where .= " AND ( DATE(created_date)>='".date("Y-m-d",strtotime($date_filter_query_ex['0']))."' AND DATE(created_date)<='".date("Y-m-d",strtotime($date_filter_query_ex['1']))."'  ) ";
}
$get_total_rows = $db->rp_getTotalRecord($ctable,$ctable_where,0); //hold total records in variable
//break records into pages
$total_pages = ceil($get_total_rows/$item_per_page);

//get starting position to fetch the records
$page_position = (($page_number-1) * $item_per_page);

$ctable_r = $db->rp_getData($ctable,"*",$ctable_where,"id DESC limit $page_position, $item_per_page",0);
?>
<!-- <link rel="stylesheet" type="text/css" href="zoom-image/css/style.css" />
<link rel="stylesheet" type="text/css" href="zoom-image/cloud-zoom/cloud-zoom.css" />
<link rel="stylesheet" type="text/css" href="zoom-image/fancybox/jquery.fancybox-1.3.4.css" />
<script src="zoom-image/js/cufon-yui.js" type="text/javascript"></script>  -->
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
h2
{
	text-transform: uppercase;
	margin-bottom: 0px;
}
</style>
	<table id="datatable_1" class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th>No.</th>
                <th>Request No.</th>
                <th>Date and Time</th>
                <th>Sales Officer Name</th>
                <th>Customer Name</th>
                <th>Source of Request</th>
                <th>Request Category</th>
                <th>Request Sub Category</th>
               	<th>Description</th>
				<!-- <th>Location Map</th> -->
				<th>Address</th>
				<th>Image</th>
				<th>Status</th>	
            </tr>
        </thead>
        <tbody>
        <?php
        if(mysqli_num_rows($ctable_r)>0){
            $count = 0;
            
            	$request_type_array = array("1"=>"Email","2"=>"Call","3"=>"Whatsapp");
            	$status_array = array("0"=>"Generate","1"=>"In Progress","2"=>"Complete","-1"=>"Reject","-2"=>"Not Done");
            while($ctable_d = mysqli_fetch_array($ctable_r)){
            //print_r($ctable_d);
        ?>
            <tr>
                <td><?php echo ++$count; ?></td>
                <td><?php echo "#".stripslashes($ctable_d['request_no']); ?></td>
                <td><?php echo date("d-m-Y h:i A",strtotime($ctable_d['created_date'])); ?></td>
                <td><span class="<?php echo ($ctable_d['isActive']==0)?"text-danger":"text-success"; ?>"><?php echo $db->rp_getValue("sales_executive","name","id='".$ctable_d['user_id']."'") ?></span></td>
                <td><span class="<?php echo ($ctable_d['isActive']==0)?"text-danger":"text-success"; ?>"><?php echo $db->rp_getValue("executive","cname","id='".$ctable_d['customer_id']."'")."</span><span><br/>".$db->rp_getValue("executive","phone","id='".$ctable_d['customer_id']."'") ?></span></td>
                <td><?php echo stripslashes($request_type_array[$ctable_d['request_type']]); ?></td>
                <td><?php echo  $db->rp_getValue("complain_category","name","id='".$ctable_d['request_cat_id']."'"); ?></td>
                <td><?php echo  $db->rp_getValue("complain_sub_category","name","id='".$ctable_d['request_subcat_id']."'");?></td>
				<td><?php echo stripslashes($ctable_d['remark']); ?></td>
				<!-- <td>
					<a class="mapbtn" data-lat="<?php echo stripslashes($ctable_d['latitude']); ?>" data-long="<?php echo stripslashes($ctable_d['longitude']); ?>" data-date="<?=date("d M H:i",strtotime($ctable_d['created_date']));?>" data-salesexename="<?=$db->rp_getValue("sales_executive","name","id='".$ctable_d["user_id"]."'",0);?>" data-toggle="modal" data-target="#OpenMap">
						<img src="<?=SITEURL?>resource/map.png" style="height: 80px;">
					</a>
				</td> -->
				<td><?php echo $ctable_d['app_address']; ?></td>
				<td>
					<?php 
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
						<a href="<?=$imgpath[$i]?>" data-lightbox="complain<?=$count?>" data-title="complain <?=$ctable_d['id']?>"><img src="<?=$imgpath[$i]?>" style="height: 80px;"></a>
						<?php
					}
					else
					{
						?>
						<div class="hidden">
							<a href="<?=$imgpath[$i]?>" data-lightbox="complain<?=$count?>" data-title="complain <?=$ctable_d['id']?>"><img src="<?=$imgpath[$i]?>" style="height: 80px;"></a>
						</div>
						<?php
					}
				}
				?>
				</td>
                <td><?php echo $status_array[$ctable_d['status']]; ?></td>
				<!-- <td>
					<?php 
					
					if($rights['update_flag']==1)
					{
						?>
						
						<div class="btn-group">
						
							<button aria-expanded="false" data-toggle="dropdown" type="button" class="btn btn-sm green dropdown-toggle"> 
								More
							</button>
							<ul role="menu" class="dropdown-menu">
								<li>
									<?php
									if($ctable_d['isActive']==0){
									?>
										<a  href="complain_crud.php?mode=isActive&id=<?php echo $ctable_d['id']; ?>&status=1" title="Activate"><span class="text-success"><i class="fa fa-circle"></i> &nbsp;Activate</span></a>
									<?php
									}else{
									?>
										<a  href="complain_crud.php?mode=isActive&id=<?php echo $ctable_d['id']; ?>&status=0" title="Deactivate"><span class="text-danger" ><i class="fa fa-circle-o"></i> &nbsp; Deactivate </span></a>
									<?php
									}
									?>
								</li>
							</ul>
						</div>
						<?php
					}
					?>
				</td> -->
            </tr>
        <?php
            }
        }
        ?>
        </tbody>
    </table>
    <!-- Modal -->
	<?php require_once 'disconnect.php';  ?>