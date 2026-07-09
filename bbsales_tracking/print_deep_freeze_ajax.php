<?php
$page_id=651;$page_slug='deep_freezer_scheme';
/*
 * @author Ravi Patel
 */
include("connect.php");
$ctable 	= "freezer_scheme";

$ctable_where = "";
// $status_id="";
// Get the total number of rows in the table

if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
	$ctable_where .= " (mobile_no like '%".$db->clean($_REQUEST['searchName'])."%') AND ";
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

if(isset($_REQUEST['status']) && $_REQUEST['status']!="")
{
	$ctable_where .= " AND status='".$_REQUEST['status']."' ";
	$status=$_REQUEST['status'];
}

if(isset($_REQUEST['df']) && $_REQUEST['df']!=""){
	//echo $_REQUEST['df'];exit;
	$date_filter_query = urldecode( $_REQUEST['df'] );

	$date_filter_query_ex=explode(" to ",$date_filter_query);

	$ctable_where .= " AND ( DATE(complain_date)>='".date("Y-m-d",strtotime($date_filter_query_ex['0']))."' AND DATE(complain_date)<='".date("Y-m-d",strtotime($date_filter_query_ex['1']))."'  ) ";
}

if (isset($_REQUEST['complain_month']) && $_REQUEST['complain_month'] != "" && $_REQUEST['complain_month'] != NULL) {
	$ctable_where .= " AND MONTH(complain_date) = '".$_REQUEST['complain_month']."'";
}

if (isset($_REQUEST['complain_year']) && $_REQUEST['complain_year'] != "" && $_REQUEST['complain_year'] != NULL) {
	$ctable_where .= " AND YEAR(complain_date) = '".$_REQUEST['complain_year']."'";
}
if (isset($_REQUEST['todate']) && $_REQUEST['todate'] != "" && $_REQUEST['todate'] != NULL && $_REQUEST['todate'] != undefined) {
	$ctable_where .= " AND complain_date >= '" . $_REQUEST['todate'] . "' ";
}

if (isset($_REQUEST['fromdate']) && $_REQUEST['fromdate'] != "" && $_REQUEST['fromdate'] != NULL && $_REQUEST['fromdate'] != undefined) {
	$ctable_where .= " AND complain_date <= '" .$_REQUEST['fromdate']. "' ";
}


if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
{
	if($rights['personal_flag']==1)
	{
		 $check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];
    	$ctable_where .= " AND (complain_assign_to = '".$check_id."' OR complain_created_by = '".$check_id."') ";
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
					
						$ctable_where .= "  AND (complain_assign_to IN (".$SALEID1.','.$_SESSION[SITE_SESS.'REFERANCE_ID'].") OR  complain_created_by IN (".$SALEID1.','.$_SESSION[SITE_SESS.'REFERANCE_ID'].") ) ";	
					
					
				}
				else
				{
						$ctable_where .= "  AND (complain_assign_to IN (".$_SESSION[SITE_SESS.'REFERANCE_ID'].") OR complain_created_by(".$_SESSION[SITE_SESS.'REFERANCE_ID']."))";		
				}

	 	}
	 	else
	 	{
	 		
	 	}
	}
}


$get_total_rows = $db->rp_getTotalRecord($ctable,$ctable_where,0); //hold total records in variable
//break records into pages
$total_pages = ceil($get_total_rows/$item_per_page);

//get starting position to fetch the records
$page_position = (($page_number-1) * $item_per_page);

// SELECT * FROM complain WHERE isDelete=0 AND user_id='4' ORDER BY id DESC limit 0, 500

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
<!-- <form action="" name="frm" id="frm" method="post"> -->
	<table id="datatable_1" class="table table-striped table-bordered table-hover">
        <thead>
        	<tr>
        		<th colspan="18" class="center"><h2>Deep Freeze Report <?= date("d-m-Y h:i a");?> Printed By : <?=$_SESSION[SITE_SESS.'SESS_NAME'];?></h2></th>
        	</tr>
            <tr>
            	<!-- <th></th> -->
                <th>No.</th>
                <th>Serial NO.</th>
                <th>Date</th>
                <th>Type Of Customer</th>
                <th>Customer </th>
                <th>Center </th>
                <th>Contact person</th>
                <th>Mobile Number</th>
                <th>Distributer</th>
                <th>Order Amt</th>
                <th>Utr</th>
                <!-- <th>Images</th>
                <th>Agency Permises Image</th>
                <th>Dealer Images</th>
                <th>Distributor Image</th>
                <th>Company Office Image</th> -->  
                <th>Status</th>  
            </tr>
        </thead>
        <tbody>
        <?php
        if(mysqli_num_rows($ctable_r)>0)
        {
            $count = 0;
            while($ctable_d = mysqli_fetch_array($ctable_r))
            {
            	  $Status = array('0' => "Pending",'1' => "Approve");
	           
	            ?>
	            <tr>
	            	
	                <td><?php echo ++$count; ?></td>
	                <td><?php echo stripslashes($ctable_d['serial_no']); ?></td>
	                <td><?php if($ctable_d['created_date']=="1970-01-01" || $ctable_d['created_date']=="0000-00-00"){ echo "";}else{
	                	echo date("d-m-Y",strtotime($ctable_d['created_date']));}?>
	                </td>
	                <td><?php echo $db->rp_getValue("customer_type","name","id='".$ctable_d['executive_type']."'")?></td>
	                <td><?php echo $db->rp_getValue("executive","cname","id='".$ctable_d['customer_id']."'")?></td>
	                <td><?php echo stripslashes($ctable_d['center']); ?></td>
	                <td><?php echo stripslashes($ctable_d['contact_person']); ?></td>  
	                <td><?php echo stripslashes($ctable_d['mobile_no']); ?></td>
	                <td><?php echo $db->rp_getValue("executive","company_name","id='".$ctable_d['distributor_agency']."'")?></td>
	               	<td>
	                	<?php 
	                	$order_amt = $db->rp_getValue("orders","SUM(grand_total)","customer_id='".$ctable_d['customer_id']."' AND isDelete=0");
	                	$order_amt = ($order_amt)?$order_amt:0;
	                	echo CURR.$db->rp_num($order_amt);
	                	?>
	                </td>
	                 <td><?php echo stripslashes($ctable_d['utr']); ?></td>
	                <!-- <td>
								<?php 
	                        if($ctable_d['image_path']!="")
	                        {
	                        $img = explode(",", $ctable_d['image_path']);
	                        $imgpath = array();
	                        for ($i=0; $i < sizeof($img); $i++)
	                        { 
	                            $imgpath[] = SITEURL."images/document_list/".$db->rp_getValue("media","url","reference_id='".$ctable_d["id"]."' AND id='".$img[$i]."'",0);
	                        }
	                        for ($i=0; $i < sizeof($imgpath); $i++)
	                        {
	                            if($i==0){
							 
	                            ?>
	                                <a href="<?=$imgpath[$i]?>" data-lightbox="relational<?=$count?>" data-title="relational<?=$ctable_d['id']?>"><img src="<?=$imgpath[$i]?>" style="height: 80px;"></a>
	                                <a href="<?=$imgpath[$i]?>" data-lightbox="scheme<?=$count?>" data-title="scheme<?=$ctable_d['id']?>"><img src="<?=$imgpath[$i]?>" style="height: 80px;"></a>
	                            <?php 
	                            }
	                            else{
	                            ?>
	                               <div class="hidden">
	                                    <a href="<?=$imgpath[$i]?>" data-lightbox="scheme<?=$count?>" data-title="scheme<?=$ctable_d['id']?>"><img src="<?=$imgpath[$i]?>" style="height: 80px;"></a>
	                                </div>
	                            <?php
	                            }
	                        }
	                    }
	                     else
	                    {
	                        $img = $ctable_d['image_path'] = DEFAULTIMG;
	                        ?>
	                        <a href="<?=$img?>" data-lightbox="scheme<?=$count?>" data-title="scheme<?=$ctable_d['id']?>"><img src="<?=$img?>" style="height: 80px;"></a>
	                        <?php
	                    }
	                    ?>
	          
				    </td>
	                <td>
								<?php
								 if($ctable_d['agency_permises_photo']!=""){
									?>
									<img src="<?php echo  AGENCY_PERMISES_PHOTO_A.$ctable_d['agency_permises_photo']; ?>" width="100" />
									<?php
									 // echo AGENCY_PERMISES_PHOTO_A.$ctable_d['agency_permises_photo'];exit();
								 }else{
									echo "No Image Available.";
								 }
								 if($ctable_d['url']!=""){
								 ?>
								 <br>
								 <a href="<?php echo $ctable_d['url']; ?>" target="_blank"><?php echo $ctable_d['url']; ?></a>
								 <?php
								 }
								 ?>
					</td>
					<td>
								<?php
								 if($ctable_d['dealer_image']!="" && file_exists(DEALER_PHOTO_A.$ctable_d['dealer_image'])){
									?>
									<img src="<?php echo DEALER_PHOTO_A.$ctable_d['dealer_image']; ?>" width="100" />
									<?php
								 }else{
									echo "No Image Available.";
								 }
								 if($ctable_d['url']!=""){
								 ?>
								 <br>
								 <a href="<?php echo $ctable_d['url']; ?>" target="_blank"><?php echo $ctable_d['url']; ?></a>
								 <?php
								 }
								 ?>
					 </td>
					 <td>
								<?php
								 if($ctable_d['distributor_image']!="" && file_exists(DISTRIBUTOR_PHOTO_A.$ctable_d['distributor_image'])){
									?>
									<img src="<?php echo DISTRIBUTOR_PHOTO_A.$ctable_d['distributor_image']; ?>" width="100" />
									<?php
								 }else{
									echo "No Image Available.";
								 }
								 if($ctable_d['url']!=""){
								 ?>
								 <br>
								 <a href="<?php echo $ctable_d['url']; ?>" target="_blank"><?php echo $ctable_d['url']; ?></a>
								 <?php
								 }
								 ?>
					</td>
					 <td>
								<?php
								 if($ctable_d['company_office_image']!="" && file_exists(COMPANY_OFFICE_PHOTO_A.$ctable_d['company_office_image'])){
									?>
									<img src="<?php echo COMPANY_OFFICE_PHOTO_A.$ctable_d['company_office_image']; ?>" width="100" />
									<?php
								 }else{
									echo "No Image Available.";
								 }
								 if($ctable_d['url']!=""){
								 ?>
								 <br>
								 <a href="<?php echo $ctable_d['url']; ?>" target="_blank"><?php echo $ctable_d['url']; ?></a>
								 <?php
								 }
								 ?>
					</td> -->
					<td><?= $Status[$ctable_d['status']];?></td>
	               
				</tr>
	        	<?php
            }
        }
        ?>
        </tbody>
    </table>
    
   
<!-- </form> -->

<!-- <div id="myModal" class="modal">
  <span class="close1" onclick='$("#myModal").css("display","none");'>&times;</span>
  <img class="modal-content" style="height: 80%;width: auto;" id="img01">
</div> -->



<!-- zoom image js -->
<!-- <script src="js/zoom-jquery-1.4.4.min.js" type="text/javascript"></script> -->
<!-- <script type="text/javascript" src="http://code.jquery.com/jquery-1.4.4.min.js"></script>
<script type="text/javascript" src="zoom-image/fancybox/jquery.easing-1.3.pack.js"></script>
<script type="text/javascript" src="zoom-image/fancybox/jquery.fancybox-1.3.4.js"></script>
<script type="text/javascript" src="zoom-image/cloud-zoom/cloud-zoom.1.0.2.js"></script> -->
<!-- zoom image js -->
<script type="text/javascript">
	$(".filterBtn").on("click",function()
	{
		sales_executive = $("#sales_executive").val();
		customer_id = $("#customer_id").val();
		df1=$("#material_request_filter_input").val();
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
<?php include("disconnect.php"); ?>