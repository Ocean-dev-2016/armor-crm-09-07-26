<?php
$page_id=581;$page_slug='manage_complain';
require_once("connect_in.php");
$ctable 	= "complain";
$ctable1 	= "complain";

$ctable_where = "";

// Get the total number of rows in the table



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
}if(isset($_REQUEST["visit_id"]) && $_REQUEST["visit_id"]!="" && $_REQUEST["visit_id"]!=undefined){
	$ctable_where .= " AND id='".$_REQUEST["visit_id"]."'";
	$visit_id = $_REQUEST["visit_id"];
}





$get_total_rows = $db->rp_getTotalRecord($ctable,$ctable_where,0); //hold total records in variable
//break records into pages
$total_pages = ceil($get_total_rows/$item_per_page);

//get starting position to fetch the records
$page_position = (($page_number-1) * $item_per_page);

$ctable_r = $db->rp_getData("complain","*",$ctable_where,"id DESC limit $page_position, $item_per_page",0);
$ctable_d 	= mysqli_fetch_assoc($ctable_r);
?>
<html>
	<head>
		<style>
		.mainDiv, table{
			border: 1px solid #595959;
			border-collapse: collapse;
			font-size: 18px!important;
			width:250mm!important;
			background-color: #FFF;
			margin:auto;
			padding:auto;
		}
		.fontsizeinner{
				font-size: 12px!important;
		}
		table , td, th {
			border: 1px solid #595959;
		}
		td, th {
			padding: 5px;
			height: 25px;

		}
		b{
		font-size: 12px!important;	
		}
		.text-center{
			text-align: center!important;
		}
		.text-right{
			text-align: right!important;
		}
		.no-border-left{
			border-left: hidden;
		}
		.no-border-right{
			border-right: hidden;
		}
		.no-border-bottom{
			border-bottom: hidden !important;
		}
		.no-border-top{
			border-top: hidden !important;
		}
		.border td{
			border-bottom: hidden !important;
		}
		.color {
			background: #D3D3D3;
		}
		tbody
		{
			/*text-transform: uppercase;*/
		}
		.font-size td
		{
			font-size: 18px!important;
		}
		.image-width
		{
			width: 10% !important;
			min-width: 10% !important;
			max-width: 10% !important;
		}
		.border-r-width
		{
			border-right-width: 5px;
		}
		.border-gray
		{
			border-right-color:#E5E5E5;
		}
		.border-blue
		{
		border-right-color:<?= VIEW_COLOR ?>;
		}
		.vertical-top
		{
		vertical-align: top;
		}
		.height-5
		{
		height: 5px;
		}
		.bg-gray
		{
		background-color: #E5E5E5 !important;
		}
		.font-13
		{
		font-size:18px !important;
		} 
		@media {
         div.page {page-break-after: always;}
         }
         .margin{
         	margin-top: 100px;
         }
          

		</style>
	</head>
	<div class="page">
	<body>
         
		<table>
			<tbody class="<?= $cl; ?>"> 
				<tr>
					<td colspan="16">
						<img style="width: 100%;padding: 0px !important;"  src="<?= VIEW_LOGO_ONE ?>">
					</td>

				</tr>
			
				
				
			</tbody>
		</table>
		<table>
		<tbody>
			<!-- <tr>
				<td class="no-border-bottom"><h2 style="text-align:center;margin-top: 3px;"><u>Complain Report</u></h2>
		</td>
			</tr> -->
			<!-- <tr style="border: none;">
				<td style="border: none;" class="text-right"><b>Complain Date: </b><span class="fontsizeinner"><?php echo date("d-m-Y",strtotime($ctable_d['complain_date'])); ?></span></td>
			</tr> -->


		</tbody>
	</table>
	<table style="height:200px!important">
		<tbody>
			<tr>
				<td colspan="4"></td>
				<td colspan="12" class="no-border-left text-right"><b>COMPLAIN DATE: </b><span class="fontsizeinner" ><?= date('d-m-Y',strtotime($ctable_d['complain_date']))?></span></td>
				
			</tr>
					<td colspan="12"><b>SALES PERSON NAME: </b><span class="fontsizeinner" ><?php echo $db->rp_getValue("sales_executive","name","id='".$ctable_d['user_id']."'") ?></span></td>
					<td colspan="4"><b>CUSTOMER NAME: </b> <span class="fontsizeinner"><?php echo $db->rp_getValue("executive","cname","id='".$ctable_d['customer_id']."'")?></span></td>
				</tr>
				<tr>
					<td colspan="12"><b>MOBILE NO: </b><span class="fontsizeinner"> <?php echo $db->rp_getValue("executive","phone","id='".$ctable_d['customer_id']."'") ?></span></td>
					<td colspan="4"><b>SOURCE OF COMPLAIN: </b><span class="fontsizeinner"> <?php echo $db->rp_getValue("source_of_inquiry","name","id='".$ctable_d['complain_type']."'") ?></span></td>
					<!-- <td colspan="4"><b>CENTER.: </b> <?php echo $db->rp_getValue("executive","city","isDelete=0 AND id='". $ctable_d['customer_id']."'",0);?></td> -->
				</tr>
				<tr>
					<td colspan="12"><b>COMPANY NAME: </b><span class="fontsizeinner"> <?php echo $db->rp_getValue("executive","company_name","id='".$ctable_d['customer_id']."'") ?></span></td>
					<td colspan="4"><b>CUSTOMER ADDRESS:</b><span class="fontsizeinner"> <?php echo $db->rp_getValue("executive","address","id='".$ctable_d['complain_type']."'") ?></span></td>
					<!-- <td colspan="4"><b>CENTER.: </b> <?php echo $db->rp_getValue("executive","city","isDelete=0 AND id='". $ctable_d['customer_id']."'",0);?></td> -->
				</tr>
				<tr>
					<td colspan="12"><b>STATE: </b><span class="fontsizeinner"> <?php echo $db->rp_getValue("executive","state","id='".$ctable_d['customer_id']."'") ?></span></td>
					<td colspan="4"><b>CITY:</b><span class="fontsizeinner"> <?php echo $db->rp_getValue("executive","city","id='".$ctable_d['customer_id']."'") ?></span></td>
					<!-- <td colspan="4"><b>CENTER.: </b> <?php echo $db->rp_getValue("executive","city","isDelete=0 AND id='". $ctable_d['customer_id']."'",0);?></td> -->
				</tr>
				<tr>
					<td colspan="12"><b>ZONE:</b><span class="fontsizeinner"> <?php echo $db->rp_getValue("executive","zone","id='".$ctable_d['customer_id']."'") ?></span></td>
					<!-- <td colspan="4"><b>CITY</b><span class="fontsizeinner"> <?php echo $db->rp_getValue("executive","city","id='".$ctable_d['customer_id']."'") ?></span></td> -->
					<!-- <td colspan="4"><b>CENTER.: </b> <?php echo $db->rp_getValue("executive","city","isDelete=0 AND id='". $ctable_d['customer_id']."'",0);?></td> -->
				</tr>

				<tr>
					<td colspan="12"><b>COMPLAIN CATEGORY: </b><span class="fontsizeinner"><?php echo  $db->rp_getValue("complain_category","name","id='".$ctable_d['complain_cat_id']."'"); ?></span></td>
					<td colspan="4"><b>COMPLAIN SUB CATEGORY: </b><span class="fontsizeinner"><?php echo  $db->rp_getValue("complain_sub_category","name","id='".$ctable_d['complain_subcat_id']."'");?></span></td>
				<!--  <td colspan="4"><b> </td> -->
				</tr>
				<tr>
					<td colspan="16" ><b>DESCRIPTION: </b><span class ="fontsizeinner"><?php echo stripslashes($ctable_d['remark']); ?></td>
					<!--  <td colspan="12" ><b></td>  -->
				</tr>
				<tr>
					<td colspan="16"  ><b>ADDRESS:</b><span class="fontsizenner"><?php echo $ctable_d['app_address']; ?></span></td>
				<!-- 	<td colspan="12" ><b></td>  -->
				</tr>
				<tr>
					<td colspan="12"><b>COMPLAIN CREATE: </b><span class="fontsizeinner"> <?php echo $db->rp_getValue("sales_executive","name","id='".$ctable_d['complain_created_by']."'") ?></span></td>
					<td colspan="4"><b>COMPLAIN ASSIGN: </b><span class="fontsizeinner"> <?php echo $db->rp_getValue("sales_executive","name","id='".$ctable_d['complain_assign_to']."'") ?></span></td>
					
				</tr>
				<tr>
					<td colspan="12"><b>PRODUCT:</b><span class="fontsizeinner"> <?php echo $db->rp_getValue("product","name","id='".$ctable_d['product_id']."'") ?></span></td>
					<td colspan="4"><b>CUSTOMER REQUIREMENT:</b><span class="fontsizeinner"> <?php echo $ctable_d['customer_requirement'] ?></span></td>
					
				</tr>
				
		</tbody>
	</table>
	
		  
			
	</body>
</div>

	
</html>