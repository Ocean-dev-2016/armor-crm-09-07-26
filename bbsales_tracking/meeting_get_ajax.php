<?php
$page_id=585;$page_slug='meeting_master';
include("connect.php");
// $Where="isDelete=0";
$Where="";
$OrderBy="";
$Limit="";
$RequiredColumns="";
$RequestedData= $_REQUEST;
$customer_ids = array();
// Response Column Name Specify
$RequiredColumns = (isset($RequestedData['columns']))?$RequestedData['columns']:array(0=>"id",1=>"pricelist_name",);
// getting total number records without any search
if( isset($_REQUEST['searchName']) && !empty($_REQUEST['searchName']) ) 
{
	$Query=$_REQUEST['searchName'];
	$Where.=" (title LIKE '%".$Query."%' )" ;
	$customer_Data = $db->rp_getData("executive","id","company_name LIKE '%".$_REQUEST['searchName']."%' AND isDelete=0","",0);
	if($customer_Data)
	{
		while($customer_Data_d=mysqli_fetch_assoc($customer_Data))
		{
			$customer_ids[]=$customer_Data_d['id'];
		}
		$customer_ids=implode(",",$customer_ids);
		$Where .= " OR customer_id IN (".$customer_ids.") AND";
	}
	else
	{
		$Where .= " customer_id IN ('') AND";	
	}
}

if(isset($_REQUEST["type"]) && $_REQUEST["type"]!="" && $_REQUEST["type"]!=undefined)
{
	$Where .= " meeting_type LIKE '%".trim($_REQUEST["type"])."%' AND";
	$type = $_REQUEST['type'];
}
$TotalFiltered = $db->rp_getTotalRecord("meeting",$Where);

if(isset($RequestedData['page']) && is_numeric($RequestedData['page']))
$PageNumber= filter_var($RequestedData["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH);
else 
$PageNumber=1;

if(isset($RequestedData['show']) && is_numeric($RequestedData['show']))
$LowerLimit= filter_var($RequestedData["show"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH);
else 
$LowerLimit=100;

if(isset($RequestedData['order']))
$OrderBy=$RequiredColumns[$RequestedData['order'][0]['column']]."   ".$RequiredColumns['order'][0]['dir'];
else
{
	$OrderBy="id DESC";
}
$UpperLimit=($PageNumber-1)*$LowerLimit;
if($UpperLimit!="" &&  $LowerLimit!="")
{
	$Limit=$UpperLimit." ,".$LowerLimit." ";
}
else if($UpperLimit!="")
$Limit=$UpperLimit;
$RequiredColumns=implode(",",$RequiredColumns);
$Where .= " isDelete=0";
$Results=$db->rp_getData("meeting","*",$Where,$OrderBy,0,$Limit);
?> 
<table class="table table-striped table-hover table-bordered unit_table_class" id="country_table_id">
    <thead>
    	<tr>
    		<th></th>
    		<th></th>
    		<th>
    			<select class="form-control input-medium status" name="type" id="type"  autofocus onChange="getMeetingType(this.value);">
					<option value="">Select Meeting Type</option>
					<?php 
						$se_r=$db->rp_getData("meeting_type","*","isDelete=0 AND isActive=1");
						if($se_r)
						{
							while($se_d=mysqli_fetch_assoc($se_r))
							{
								?>
								<option value="<?= $se_d['slug']; ?>" <?=($type == $se_d['slug'])?"selected":"";?> ><?php echo $se_d['name']; ?></option>
								<?php
							}
						}
					?>
                </select>
    		</th>
    		<th></th>
    		<th></th>
    		<th></th>
    		<th></th>
    		<th></th>
    		<th></th>
    	</tr>
        <tr>
            <th style="width: 5%;"></th>
            <th>Sr.no</th>
			<th>Meeting Type</th> 
			<th>Customer</th> 
			<th>Meeting Date & Time</th> 
			<th>Meeting Address</th> 
			<th>Gift Details</th> 
			<th>Expence</th> 
			<th>Action</th>
        </tr>
    </thead>
    <tbody>
	    <?php 
		if($Results)
		{												
		  	// In Items there are all objects you need here are keys you will find in this array
			// id|name|slug														
			$cnt=0;
			while($R=mysqli_fetch_assoc($Results))
			{
				// print_r($R);
				$cnt++;
				$totalPro=$db->rp_getValue("meeting_member","COUNT(id)","meeting_id='".$R['id']."' AND isDelete=0");
 				?>
		  		<tr class="">
		  			<td>
						<?php $ctable_d['id']; 				
						if($rights['update_flag']==1)
						{
							?>
							<div class="btn-group">				
								<button aria-expanded="false" data-toggle="dropdown" type="button" class="btn btn-sm blue dropdown-toggle"><i class="fa fa-gear"></i></button>
								<ul role="menu" class="dropdown-menu">
									<li>
										<a href="meeting_crud.php?mode=edit&id=<?php echo $R['id']; ?>" title="Edit">
											<span class="text-primary">
												<i class="fa fa-pencil"></i>
												&nbsp;Edit
											</span>
										</a>
									</li>
									<?php
									if($rights['delete_flag']==1 && $ctable_d['id']!=-1)
									{
										?>
										<li>
											<a onClick="del_conf('<?php echo $R['id']; ?>');" title="Delete">
												<span class="text-danger">
													<i class="fa fa-times"></i>
													&nbsp;Delete
												</span>
											</a>
										</li>
										<?php
									}
									?>	
								</ul>
							</div>
							<?php
						}
						?>
					</td>
					<td><?php echo $cnt; ?></td>
					<td><?php echo $db->rp_getValue("meeting_type","name","slug='".$R['meeting_type']."'",0); ?></td>
					<td><?php echo $db->rp_getValue("executive","company_name","id='".$R['customer_id']."'",0); ?></td>
					<td><?php echo date("d-m-Y h:i A",strtotime($R['meeting_date'])); ?></td>
					<td><?php echo $R['meeting_venue']; ?></td>
					<td><?php echo $R['gift_details']; ?></td>
					<td><?php echo $R['expence']; ?></td>
					<td><a href="meeting_member_manage.php?mid=<?php echo $R['id']; ?>" title="Manage Member" class="btn btn-info btn-sm">Manage Member (<?php echo $totalPro; ?>)</a></td>
		 		</tr> 
				<?php													 
			}
		}
		else
		{
			?>
			<tr>
				<td colspan="8" class="text-center">No Data Found!!</td>
			</tr>
			<?php
		}
		?>   
    </tbody>
</table> 

<div class="row">
	<div class="col-md-6">
		<div class="dataTables_info"> Rows Limit:
			<select id="numRecords" onChange="changeDisplayRowCount(this.value);">
				<option value="500" <?php if ($_REQUEST["show"] == 500 || $_REQUEST["show"] == "") {echo ' selected="selected"';}  ?>>500</option>
				<option value="1000" <?php if ($_REQUEST["show"] == 1000) {echo ' selected="selected"';}  ?>>1000</option>
				<option value="2000" <?php if ($_REQUEST["show"] == 2000) {echo ' selected="selected"';}  ?>>2000</option>
				<option value="5000" <?php if ($_REQUEST["show"] == 5000) {echo ' selected="selected"';}  ?>>5000</option>
			</select>
		</div>
	</div>
	<div class="col-md-6">
		<div class="dataTables_paginate paging_simple_numbers">
			<ul class="pagination">
			<?php 
				echo paginate_function($item_per_page, $page_number, $get_total_rows, $total_pages); 
			?>
			</ul>
			<span>
			<?php
			for($i=1; $i<=$last; $i++) 
			{
				if ($i == $pagenum ) 
				{
					?>
					<a class="paginate_button current" aria-controls="datatable1"><?php echo $i ?></a>
					<?php
				} 
				else 
				{  
					?>
					<a class="paginate_button" aria-controls="datatable1" onclick="displayRecords('<?php echo $page_limit;  ?>', '<?php echo $i; ?>');"><?php echo $i ?></a>
					<?php 
				}
			} 
			?>
			</span>
		</div>
	</div>
</div>	
<br/>						

<?php
################ pagination function #########################################
function paginate_function($item_per_page, $current_page, $total_records, $total_pages)
{
    $pagination = '';
    if($total_pages > 0 && $total_pages != 1 && $current_page <= $total_pages){ //verify total pages and current page number
        $right_links    = $current_page + 3; 
        $previous       = $current_page - 3; //previous link 
        $next           = $current_page + 1; //next link
        $first_link     = true; //boolean var to decide our first link
        
        if($current_page > 1){
			$previous_link = ($previous<=0)?1:$previous;
            $pagination .= '<li class="paginate_button "><a href="#" aria-controls="datatable1" data-page="1" title="First">&laquo;</a></li>'; //first link
            $pagination .= '<li class="paginate_button "><a href="#" aria-controls="datatable1" data-page="'.$previous_link.'" title="Previous">&lt;</a></li>'; //previous link
                for($i = ($current_page-2); $i < $current_page; $i++){ //Create left-hand side links
                    if($i > 0){
                        $pagination .= '<li class="paginate_button "><a href="#"  data-page="'.$i.'" aria-controls="datatable1" title="Page'.$i.'">'.$i.'</a></li>';
                    }
                }   
            $first_link = false; //set first link to false
        }
        
        if($first_link){ //if current active page is first link
            $pagination .= '<li class="paginate_button active"><a aria-controls="datatable1">'.$current_page.'</a></li>';
        }elseif($current_page == $total_pages){ //if it's the last active link
            $pagination .= '<li class="paginate_button active"><a aria-controls="datatable1">'.$current_page.'</a></li>';
        }else{ //regular current link
            $pagination .= '<li class="paginate_button active"><a aria-controls="datatable1">'.$current_page.'</a></li>';
        }
                
        for($i = $current_page+1; $i < $right_links ; $i++){ //create right-hand side links
            if($i<=$total_pages){
                $pagination .= '<li class="paginate_button "><a href="#" aria-controls="datatable1" data-page="'.$i.'" title="Page '.$i.'">'.$i.'</a></li>';
            }
        }
        if($current_page < $total_pages){ 
			$next_link = ($i > $total_pages)? $total_pages : $i;
			$pagination .= '<li class="paginate_button "><a href="#" aria-controls="datatable1" data-page="'.$next_link.'" title="Next">&gt;</a></li>'; //next link
			$pagination .= '<li class="paginate_button "><a href="#" aria-controls="datatable1" data-page="'.$total_pages.'" title="Last">&raquo;</a></li>'; //last link
        }
    }
    return $pagination; //return pagination links
}
?>
<script type="text/javascript">
	$("#type").select2();
</script>
<?php require_once 'disconnect.php';  ?>