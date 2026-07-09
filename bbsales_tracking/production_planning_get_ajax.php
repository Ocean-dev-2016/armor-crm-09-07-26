<?php
$page_id=616;$page_slug='production_planning_page';
include("connect.php");
$Where="isDelete=0";

if (isset($_REQUEST['df']) && $_REQUEST['df'] != "" && $_REQUEST['df'] != NULL && $_REQUEST['df'] != undefined) {
	//echo $_REQUEST['df'];exit;
	$date_filter_query = urldecode($_REQUEST['df']);

	$date_filter_query_ex = explode(" to ", $date_filter_query);

	$Where .= " AND ( DATE(planning_date)>='" . date("Y-m-d", strtotime($date_filter_query_ex['0'])) . "' AND DATE(planning_date)<='" . date("Y-m-d", strtotime($date_filter_query_ex['1'])) . "'  ) ";
}

$Results=$db->rp_getData("production_planning","*",$Where,"",0);
?> 
 
<table class="table table-striped table-hover table-bordered unit_table_class" id="country_table_id">
    <thead>
    	
        <tr>
            <th>Sr.no</th>
			<th>Product Name</th> 
			<th>Qty</th> 
			<th>Planning Date</th> 
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
					<td><?php echo $R['pro_name']." - ".$db->rp_getValue("product_weight_price","catno","product_id='".$R['pro_id']."'"); ?></td>
					<td><?php echo $R['pro_qty']; ?></td>
					<td><?php echo date('d-m-Y',strtotime($R['planning_date'])); ?></td>
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