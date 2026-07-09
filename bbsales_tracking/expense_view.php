<?php
$page_id=566;$page_slug='page_order_ajax';
/*
 * @author Ravi Patel
 */
include("connect.php");

include("../include/no_to_word.php");
$id=$_REQUEST['id'];
$expense_date=$db->rp_getValue("expense","DATE(expense_date)","id='".$_REQUEST['id']."' AND isDelete=0 AND isActive=1");
$se_id=$db->rp_getValue("expense","sales_executive_id","id='".$_REQUEST['id']."' AND isDelete=0 AND isActive=1");
// $ctable_where	= "id='".$_REQUEST['id']."' AND isDelete=0 AND isActive=1";
$ctable_where	= "DATE(expense_date)='".$expense_date."' AND sales_executive_id='".$se_id."' AND isDelete=0 AND isActive=1";
$ctable_r = $db->rp_getData("expense","*",$ctable_where,"",0);
$d="";
$discount="";
$imgExts = array("gif", "jpg", "jpeg", "png", "tiff", "tif");
?><!-- 
<style>
#datatable_1
{
	align:center;
}
#lightbox,#lightboxOverlay
{
	z-index: 999999;
}
</style> -->
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
<div id="print_info">
<div class="row">
<div class="col-sm-12">
<h4 align="center" style="font-size:20px"><b>EXPENSE DETAIL - </b><?php echo date('d-m-Y',strtotime($expense_date));?></h4>
<table id="datatable_1"  style="border-collapse:collapse;" align="center" class="table table-striped table-bordered table-hover">
		<thead>
			<tr>
				<td colspan="4" style="border-right: none;"><b>EXECUTIVE</b> : <?= $sales=$db->rp_getValue("sales_executive","username","id='".$se_id."'",0); ?></td>				
				<td colspan="5" style="text-align: right;border-left: none;"><b>EXPENSE DATE</b> : <?php echo date('d-m-Y',strtotime($expense_date));?></td>
				
			</tr>			
			<tr>
				<th>Category Name</th>
				<th>Sub Category Name</th>
				<th>Total Kilometer</th>
				<th>Remark</th>
				<th>Expense Date Time</th>
				<th>Request Amount</th>
				<th>Passed Amount</th>
				<th>Entry Type</th>
				<th>Attachment</th>
			</tr>
		</thead>
		<tbody>
	<?php
        if(mysqli_num_rows($ctable_r)>0){
            $count = 0;
            $total=0;
            $entry_type_status = array("1"=>"Admin Panel","2"=>"customer","3"=>"Web Sales",4=>"Web Customer",5=>"Sales App",6=> "Customer App");
            while($ctable_d = mysqli_fetch_array($ctable_r)){
                $count++;
        ?>			
			<tr>
				<td >
					<?php 
					echo $db->rp_getValue("expence_category","name","id='".$ctable_d['category_id']."'",0); 
					?>
				</td>

				<td><?php echo $db->rp_getValue("expence_sub_category","name","id='".$ctable_d['subcategory_id']."'",0); ?></td>

				<td>
								<?php 
								if($ctable_d['category_id']=='2')
								{

							 		echo "Start KM : " .$ctable_d['start_kilometer']."<br/> END KM : ".$ctable_d['end_kilometer']."<br/> TOTAL KM : ".$ctable_d['total_kilometer']  ; 
								}
								else
								{
									echo "";
								}
								?>
				</td>
			
				<td ><?php echo $ctable_d['remark'];?></td>

				<td ><?php echo date("d-m-Y h:i:s A",strtotime($ctable_d['created_date']));?></td>
			
				<td align="right"><?php echo $db->rp_num($ctable_d['total']);?></td>

				<td align="right"><?php echo $db->rp_num($ctable_d['pass_expense_amount']);?></td>

				 <td><?php echo $entry_type_status[$ctable_d['entry_flag']]; ?></td>
				
				<td align="center">
					<?php
								if($ctable_d['image_path']!="" && file_exists(EXPENCE_A.$ctable_d['image_path'])){
								?>
									<img src="<?php echo EXPENCE_A.$ctable_d['image_path']; ?>" width="100px;" />
								<?php
								}else{
									echo "No Image Available.";
								}
								?>
					<?php 
						// $img = explode(",", $ctable_d['image_path']);
						// $imgpath = array();
						// for ($i=0; $i < sizeof($img); $i++)
						// { 
						// 	$imgpath[] = SITEURL."resource/image/".$db->rp_getValue("media","url","reference_id='".$ctable_d["id"]."' AND id='".$img[$i]."'",0);
						// }
						// print_r($imgpath);exit;
						// 	for ($i=0; $i < sizeof($imgpath); $i++)
						// 	{
						// 		$urlExt = pathinfo($imgpath[$i], PATHINFO_EXTENSION);
						// 		if (in_array($urlExt, $imgExts)) {
								    
						// if($i==0){
					?>
					<!-- <a href="<?=$imgpath[$i]?>" data-lightbox="EXPENSE<?=$count?>" data-title="EXPENSE <?=$ctable_d['id']?>"><img src="<?=$imgpath[$i]?>" style="height: 80px;margin-bottom: 10px;border:1px solid #909090;float: left;margin-left: 10px"></a> -->
					<?php 
					//}else{
						?>
							<!-- <div class="hidden">
								<a href="<?=$imgpath[$i]?>" data-lightbox="EXPENSE<?=$count?>" data-title="EXPENSE <?=$ctable_d['id']?>"><img src="<?=$imgpath[$i]?>" style="height: 80px;margin-bottom: 10px;border:1px solid #909090;float: left;margin-left: 10px"></a>
							</div> -->
						<?php
						// 	}
						// }
						// }
					?>
				</td>
			</tr>
			<?php
			$total+=$ctable_d['total'];
			$total_pass_amount+=$ctable_d['pass_expense_amount'];
				}
			}else{
				?>
				<tr>
					<td colspan="5" style="text-align:center;">No Data Available</td>
				</tr>
				<?php
			}
			?>
			
			</tbody>
			<tfoot>
				<tr>
					<th colspan="5" style="text-align: right;">Total</th>
					<th style="text-align: right;"><?= $db->rp_num($total); ?></th>
					<th style="text-align: right;"><?= $db->rp_num($total_pass_amount); ?></th>
					<td></td>
					<td></td>

				</tr>
			</tfoot>
			</table>
			</div>

			</div>
			
</div>