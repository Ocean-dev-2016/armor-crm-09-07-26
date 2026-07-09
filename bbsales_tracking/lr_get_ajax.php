<?php
$page_id=615;$page_slug='lr_details';
include("connect.php");
$Where="isDelete=0";
$OrderBy="";
$Limit="";
$RequiredColumns="";
$RequestedData= $_REQUEST;
 
// Response Column Name Specify
$RequiredColumns = (isset($RequestedData['columns']))?$RequestedData['columns']:array(0=>"id",1=>"pricelist_name",);
// getting total number records without any search

if( isset($_REQUEST['searchName']) && !empty($_REQUEST['searchName']) ) {
	$Query=$_REQUEST['searchName'];
	$getR = $db->rp_getData("invoice_new","id","invoice_no LIKE '%".$Query."%' AND isDelete=0");
	if($getR)
	{
		$invoice_id = array();
		while($getD = mysqli_fetch_assoc($getR))
		{
			$invoice_id[] = $getD['id'];
		}
		$invoice_id = implode(",",$invoice_id);
		if($invoice_id!="")
		{
			$Where.=" AND invoice_id IN (".$invoice_id.") ";	
		}
		else
		{
			$Where.=" AND (id LIKE '%".$Query."%'  OR lr_number LIKE '%".$Query."%' )";		
		}	
	}
	else
	{
		$Where.=" AND (id LIKE '%".$Query."%'  OR lr_number LIKE '%".$Query."%' )";
	}
}
$TotalFiltered = $db->rp_getTotalRecord("price_list",$Where);
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
	$OrderBy="pricelist_name ASC";
}
$UpperLimit=($PageNumber-1)*$LowerLimit;
if($UpperLimit!="" &&  $LowerLimit!="")
{
	$Limit=$UpperLimit." ,".$LowerLimit."   ";
	
}
else if($UpperLimit!="")
$Limit=$UpperLimit;
$RequiredColumns=implode(",",$RequiredColumns);
$Results=$db->rp_getData("lr_detail","*",$Where,0,$Limit);
?> 
 
<table class="table table-striped table-hover table-bordered unit_table_class" id="country_table_id">
    <thead>
        <tr>
            <th style="width: 5%;"></th>
            <th style="width: 5%;">Sr.no</th>
			<th>Invoice No</th> 
			<th>LR Number</th>
			<th>Remark</th>
			<th>Attachment</th>
        </tr>
    </thead>
    <tbody>
	    <?php 
		if($Results)
		{	$cnt=0;
			while($R=mysqli_fetch_assoc($Results))
			{
				$cnt++;
				if($R['image_path']!="")
                {
                    $file_path = ADMINSITEURL.LRCOPY_DOCUMENTS.$R['image_path'];
                } 
                else
                {
                    $file_path = "";
                }
 				?>
			  	<tr class="">
			  		<td>
						<?php $R['id']; 				
						if($rights['update_flag']==1)
						{
							?>
							<div class="btn-group">				
								<button aria-expanded="false" data-toggle="dropdown" type="button" class="btn btn-sm blue dropdown-toggle">
									<i class="fa fa-gear"></i>
								</button>
								<ul role="menu" class="dropdown-menu">
									<li>
										<a  onClick="del_conf('<?php echo $R['id']; ?>');" title="Delete"><span class="text-danger"><i class="fa fa-times"></i> &nbsp;Delete</span></a>
									</li>		
								</ul>
							</div>
							<?php
						}
						?>
					</td>
					<td><?php echo $cnt; ?></td>
					<td><?php echo $invoice_no=$db->rp_getValue("invoice_new","invoice_no","isDelete=0 AND id='".$R['invoice_id']."'"); ?></td>
					<td><?php echo $R['lr_number']; ?></td>
					<td><?php echo $R['remark']; ?></td>
					<td>
		                <?php 
		                if($R['image_path']!="")
		                {
			                ?>
			                <a href="<?= $file_path ?>" download  class="text-warning" title="View"><i class="fa fa-download" style="font-size: 15px;"></i></a>&nbsp;&nbsp;&nbsp;
			                <a href="<?= $file_path ?>" target="_blank"  class="text-sucess" title="View"><i class="fa fa-eye" style="font-size: 15px;"></i></a>
			              	<?php
			            }
			            ?>
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
<div class="row">
		<div class="col-md-6">
			<div class="dataTables_info"> Rows Limit:
				<select id="numRecords" onChange="changeDisplayRowCount(this.value);">
					<option value="500" <?php if ($_REQUEST["show"] == 500 || $_REQUEST["show"] == "") { echo ' selected="selected"';}  ?>>500</option>
					<option value="1000" <?php if ($_REQUEST["show"] == 1000) { echo ' selected="selected"';}  ?>>1000</option>
					<option value="2000" <?php if ($_REQUEST["show"] == 2000) { echo ' selected="selected"';}  ?>>2000</option>
					<option value="5000" <?php if ($_REQUEST["show"] == 5000) { echo ' selected="selected"';}  ?>>5000</option>
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
			for($i=1; $i<=$last; $i++) {
				if ($i == $pagenum ) {
				?>
					<a class="paginate_button current" aria-controls="datatable1"><?php echo $i ?></a>
			<?php
				} else {  
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
	<br />						

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
<?php require_once 'disconnect.php';  ?>