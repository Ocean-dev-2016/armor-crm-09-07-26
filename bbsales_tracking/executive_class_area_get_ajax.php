<?php
$page_id=556;$page_slug='page_sales_executive';
include("connect.php");
$Where="isDelete=0 AND executive_id='".$_REQUEST['executive_id']."'";
			
$Results=$db->rp_getData("executive_map_area","*",$Where,"",0);
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
 
<table class="table table-striped table-hover table-bordered unit_table_class" id="country_table_id">
    <thead class="fix-th">
        <tr>
            <th class="fix-th1">Sr.no</th>
			<th class="fix-th1">Executive</th> 
			<th class="fix-th1">State</th> 
			<th class="fix-th1">City</th> 
			<th class="fix-th1">Route</th> 
			<th class="fix-th1">Action</th>
        </tr>
    </thead>
    <tbody>
	    <?php 
		if($Results)
		{	
			$cnt=0;
			while($R=mysqli_fetch_assoc($Results))
			{
				$cnt++;
 		?>
	  	<tr class="">

			<td><?php echo $cnt; ?></td>
			<td><?= $db->rp_getValue("executive","company_name","id='".$_REQUEST['executive_id']."' AND isDelete=0");?></td>
			<td><?= $db->rp_getValue("class","name","id='".$R['class_id']."' AND isDelete=0");?></td>
			<td>
				<?php 
						$city_arr = array();
						$city_name_r = $db->rp_getData("city","name","isDelete=0 AND id IN(".$R['city_id'].")","",0); 
						if ($city_name_r) 
						{
							while ($city_name_d = mysqli_fetch_assoc($city_name_r)) 
							{
								$city_arr[] = $city_name_d['name'];
							}

							$city_arr_d = implode(", ",$city_arr);
							echo $city_arr_d;
						}
					
				
				 // $db->rp_getValue("city","name","id='".$R['city_id']."' AND isDelete=0");
						?>
			
					
				</td>
			<td><?= $db->rp_getValue("area","name","id='".$R['area_id']."' AND isDelete=0");?></td>
			<td>
				<a class="btn btn-danger btn-sm" onClick="del_conf('<?php echo $R['id']; ?>');" title="Delete"><i class="fa fa-times"></i></a>				
			</td>
	 	</tr> 
		<?php													 
			}
		}
		else
		{
			?>
			<tr>
				<td colspan="6" class="text-center">No Data Found!!</td>
			</tr>
			<?php
		}
		?>   
    </tbody>
</table> 
</div>
<?php require_once 'disconnect.php';  ?>