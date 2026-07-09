<?php
$page_id=556;$page_slug='page_sales_executive';
include("connect.php");
$Where="isDelete=0 AND sales_executive_id='".$_REQUEST['sales_executive_id']."'";
			
$Results=$db->rp_getData("sales_executive_map_area","*",$Where,"",0);
?> 
 
<table class="table table-striped table-hover table-bordered unit_table_class" id="country_table_id">
    <thead>
        <tr>
            <th>Sr.no</th>
			<th>Sales Officer</th> 
			<th>State</th> 
			<th>City</th> 
			<th>Route</th> 
			<th>Action</th>
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
			<td><?= $db->rp_getValue("sales_executive","name","id='".$_REQUEST['sales_executive_id']."' AND isDelete=0");?></td>
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
			<td>
				<?php

				$area_arr = array();
						$area_name_r = $db->rp_getData("area","name","isDelete=0 AND id IN(".$R['area_id'].")","",0); 
						if ($area_name_r) 
						{
							while ($area_name_d = mysqli_fetch_assoc($area_name_r)) 
							{
								$area_arr[] = $area_name_d['name'];
							}

							$area_arr_d = implode(", ",$area_arr);
							echo $area_arr_d;
						}


				 // $db->rp_getValue("area","name","id='".$R['area_id']."' AND isDelete=0");
				 ?>
					
				</td>
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
<?php require_once "disconnect.php"; ?>