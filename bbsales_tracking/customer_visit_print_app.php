<?php
// print_r($_REQUEST);exit;
$page_id=577;$page_slug='visit_page';
include("connect_in.php");
$ctable 	= "visit";
$ctable1 	= "User";

$ctable_where = "";
// Get the total number of rows in the table
$ctable_where .= " isDelete=0 ";
if(isset($_REQUEST["visit_id"]) && $_REQUEST["visit_id"]!="" && $_REQUEST["visit_id"]!=undefined)
{
	$ctable_where .= " AND id='".$_REQUEST["visit_id"]."'";
	$visit_id = $_REQUEST["visit_id"];
	$ctable_r = $db->rp_getData("visit","*",$ctable_where,"id DESC",0);
	$ctable_d 	= mysqli_fetch_assoc($ctable_r);
	// print_r($ctable_d);exit();
}
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
				font-size: 24px!important;
		}
		table , td, th {
			border: 1px solid #595959;
		}
		td, th {
			padding: 5px;
			height: 25px;

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
	<body>
		<div class="page">
			<table>
				<tbody class="<?= $cl; ?>"> 
					<tr>
						<td colspan="16">
							<img style="width: 100%;padding: 0px !important;"  src="../images/craftbox_header.jpg">
						</td>
					</tr>
				
					<tr style="border: none;">
						 <td colspan="8" style="border: none;"></td>  
						
						<td colspan="8"style="text-align: right; border-left: none; border-bottom: none;"><b>Date :</b> <span class="fontsizeinner"> <?= date("d-m-Y h:i a");?></span></td>
					</tr>
					<tr>
						<td colspan="8"><b>Name: </b><span class="<?php echo ($ctable_d['isActive']==0)?"text-danger fontsizeinner":"text-success fontsizeinner"; ?>"><?php echo $db->rp_getValue("sales_executive","name","id='".$ctable_d['user_id']."'") ?></span></td>
						<td colspan="8"><b>POST: </b><span class="fontsizeinner">
		            
		            
		             <?php echo $db->rp_getValue("sales_executive","type","isDelete=0 AND id='". $ctable_d['user_id']."'",0);?>
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
			<table style="height:500px!important">
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
					            if ($_REQUEST['flag'] == 1 && $ctable_d['image_path'] != "") 
					            {
					            	$img = explode(",", $ctable_d['image_path']);
									$imgpath = array();
									for ($i=0; $i < sizeof($img); $i++)
									{ 
										$imgpath[] = "../resource/image/".$db->rp_getValue("media","url","reference_id='".$ctable_d["id"]."' AND id='".$img[$i]."'",0);
									}
									 for ($i=0; $i < sizeof($imgpath); $i++)
										                        {
										                        	?>
			                         <img src="<?=$imgpath[$i]?>" width="435"  height="250" />
			                         <?php
			                        }
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
					            if ($_REQUEST['flag'] == 1 && $ctable_d['stop_image_path'] != "" ) 
					            {
									$img = explode(",", $ctable_d['stop_image_path']);
									$imgpath = array();
									for ($i=0; $i < sizeof($img); $i++)
									{ 
										$imgpath[] = "../resource/image/".$db->rp_getValue("media","url","reference_id='".$ctable_d["id"]."' AND id='".$img[$i]."'",0);
									}
									 for ($i=0; $i < sizeof($imgpath); $i++)
										                        {
										                        	?>
			                         <img src="<?=$imgpath[$i]?>" width="435"  height="250" />
			                         <?php
			                        }
					            } 
		                         ?>
							
							
								   
			         </td>
			    	
				</tr>
				</tbody>
			</table>
		</div>
	</body>	
</html>