<?php
$page_id=577;$page_slug='visit_page';
require_once("connect_in.php");
$ctable 	= "visit";
$ctable1 	= "User";

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

$ctable_r = $db->rp_getData("visit","*",$ctable_where,"id DESC limit $page_position, $item_per_page",0);
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
			
				<tr style="border: none;">
					 <td colspan="8" style="border: none;"></td>  
					
					<td colspan="8"style="text-align: right; border-left: none; border-bottom: none;"><b>Date :</b> <span class="fontsizeinner"> <?= date("d-m-Y h:i a");?></span></td>

				</tr>
				<tr>
					<td colspan="8"><b>Name: </b><span class="<?php echo ($ctable_d['isActive']==0)?"text-danger fontsizeinner":"text-success fontsizeinner"; ?>"><?php echo $db->rp_getValue("sales_executive","name","id='".$ctable_d['user_id']."'") ?></span></td>
					<td colspan="8"><b>POST: : </b><span class="fontsizeinner">
                 <?php 
                 $type= $db->rp_getValue("sales_executive","type","isDelete=0 AND id='". $ctable_d['user_id']."'",0);
                 if($type=="sales_manager")
                 {
                 	echo "Regional Sales Manager";
                 }
                 else if($type=="area_sales_manager")
                 {
                 	echo "National Sales Manager";
                 }
                 else if($type="sales_officer")
                 {
                 	echo "Area Sales Manager";
                 }
                 else if($type=="sales_executive")
                 {
                 	echo "Sales Officer";
                 }
                 else
                 {
                 	echo "Service Engineer";
                 }
                 ?>
                 <?php
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
			    ?>
			</span>
                </td>
				</tr>
				<tr>
					<td colspan="8"><b>Center: <span class="fontsizeinner"> <?php echo $db->rp_getValue("sales_executive","city","isDelete=0 AND id='". $ctable_d['user_id']."'",0);?> </span></b></td>
					<td colspan="8"><b>Mobile No.:  <span class="fontsizeinner"> <?php echo $db->rp_getValue("sales_executive","phone","isDelete=0 AND id='". $ctable_d['user_id']."'",0);?></span></b></td>
				</tr>

				
			</tbody>
		</table>
		<table>
		<tbody>
			<tr>
				<td class="no-border-bottom"><h2 style="text-align:center;margin-top: 3px;"><u>Visit Report</u></h2></td>
			</tr>
			<tr style="border: none;">
				<td style="border: none;" class="text-right"><b>Visit Date: </b><span class="fontsizeinner"><?php echo date("d-m-Y H:i:s",strtotime($ctable_d['created_date'])); ?></span></td>
			</tr>


		</tbody>
	</table>
	<table style="height:200px!important">
		<tbody>
			<tr>
					<td colspan="12"><b>CONTACT PERSON: </b><span class="fontsizeinner" ><?php echo $db->rp_getValue("executive","cname","isDelete=0 AND id='". $ctable_d['customer_id']."'",0);?></span></td>
					<td colspan="4"><b>MOBILE NO.: </b> <?php echo $db->rp_getValue("executive","phone","isDelete=0 AND id='". $ctable_d['customer_id']."'",0);?></td>
				</tr>
				<tr>
					<td colspan="12"><b>FIRM NAME: </b><span class="fontsizeinner"> <?php echo $db->rp_getValue("executive","company_name","isDelete=0 AND id='". $ctable_d['customer_id']."'",0);?></span></td>
					<td colspan="4"><b>CENTER.: </b> <?php echo $db->rp_getValue("executive","city","isDelete=0 AND id='". $ctable_d['customer_id']."'",0);?></td>
				</tr>
				<tr>
					<td colspan="4" class="no-border-right"><b>CURRENT BUSSINESS: </b><span class="fontsizeinner"><?=  $ctable_d['current_bussiness'] ?></span></td>
				 <td colspan="12"><b> </td>
				</tr>
				<tr>
					<td colspan="4" class="no-border-right"><b>MEETING PURPOSE: </b><span class ="fontsizeinner"><?=  $ctable_d['remark'] ?></td>
					 <td colspan="12" ><b></td> 
				</tr>
				<tr>
					<td colspan="4" class="no-border-right" ><b>MEETING DISCUSION: </b><span class="fontsizenner"><?=  $ctable_d['meeting_discussion'] ?></span></td>
					<td colspan="12" ><b></td> 
				</tr>
				<tr>
					<td colspan="4" class="no-border-right" ><b>REPORT: </b><span class="fontsizeinner"><?=  $ctable_d['stop_remark'] ?></span></td>
					<td colspan="12" ><b></td> 
				</tr>
		</tbody>
	</table>
	<table style="height:50px!important">
				<tbody class="<?= $cl; ?>">
						<tr class="font-size">
						<td  colspan="5"><b>REPORTED BY</b> :<span class="<?php echo ($ctable_d['isActive']==0)?"text-danger fontsizeinner":"text-success fontsizeinner"; ?>"><?php echo $db->rp_getValue("sales_executive","name","id='".$ctable_d['user_id']."'") ?></span>
						</td>
						<td colspan="5"><b>H.O.D.: </b><span class="fontsizeinner"><?= $ctable_d[''] ?></span></td>
						<td colspan="5"><b>AUTHORITY: </b><span class="fontsizeinner"><?= $ctable_d[''] ?></span></td>
					</tr>
				</tbody>
			</table>
		  
			
	</body>
</div>
<div class="page">
	<table>
		<tbody>
			<tr>
				<td colspan="16" style="border-right:none; border-bottom: none;"><b>Start Image</b>:</td>
			</tr>
			<tr>
				<td colspan="16" style="border-right:none;">
					
			            <?php

						$img = explode(",", $ctable_d['image_path']);
						$imgpath = array();
						for ($i=0; $i < sizeof($img); $i++)
						{ 
							$imgpath[] = SITEURL."resource/image/".$db->rp_getValue("media","url","reference_id='".$ctable_d["id"]."' AND id='".$img[$i]."'",0);
						}
						 for ($i=0; $i < sizeof($imgpath); $i++)
							                        {
							                        	?>
                         <a href="" data-lightbox="visit<?=$count?>" data-title="visit<?=$ctable_d['id']?>"><img src="<?=$imgpath[$i]?>"width="435"  height="250"/></a>&nbsp;&nbsp;&nbsp;&nbsp;
                         <?php
                        }
                         ?>
                     </td>  
	    	
		</tr>

		<tr>
				<td colspan="16" style="border-left:none;"> <b>Stop Image</b>:	</td>

			</tr>
                 <tr>
					<td colspan="16" style="border-left:none;">
                        
                          <?php
			               
						$img = explode(",", $ctable_d['stop_image_path']);
						$imgpath = array();
						for ($i=0; $i < sizeof($img); $i++)
						{ 
							$imgpath[] = SITEURL."resource/image/".$db->rp_getValue("media","url","reference_id='".$ctable_d["id"]."' AND id='".$img[$i]."'",0);
						}
						 for ($i=0; $i < sizeof($imgpath); $i++)
							                        {
							                        	?>
                         <a href="" data-lightbox="visit<?=$count?>" data-title="stop_visit<?=$ctable_d['id']?>"><img src="<?=$imgpath[$i]?>"width="435"  height="250"/></a>&nbsp;&nbsp;&nbsp;&nbsp;
                         <?php
                        }
                         ?>
					
					
						   
	         </td>
	    	
		</tr>
		</tbody>
	</table>
	</div>
</html>