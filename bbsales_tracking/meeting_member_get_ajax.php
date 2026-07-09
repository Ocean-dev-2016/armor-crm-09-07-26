<?php
$page_id=585;$page_slug='meeting_master';
include("connect.php");
$Where="isDelete=0 AND meeting_id='".$_REQUEST['meeting_id']."'";
			
$Results=$db->rp_getData("meeting_member","*",$Where);
?> 
 
<table class="table table-striped table-hover table-bordered unit_table_class" id="country_table_id">
    <thead>
        <tr>
            <th>Sr.no</th>
			<th>Member Name</th> 
			<th>Member Phone</th> 
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
				$cnt++;
 		?>
	  	<tr class="">

			<td><?php echo $cnt; ?></td>
			<td><?= $db->rp_getValue("member","name","id='".$R['member_id']."' AND isDelete=0");?></td>
			<td><?= $db->rp_getValue("member","mobile_no","id='".$R['member_id']."' AND isDelete=0");?></td>
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
				<td colspan="4" class="text-center">No Data Found!!</td>
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
					<option value="10" <?php if ($_REQUEST["show"] == 10 || $_REQUEST["show"] == "" ) { echo ' selected="selected"'; }  ?> >10</option>
					<option value="20" <?php if ($_REQUEST["show"] == 20) { echo ' selected="selected"'; }  ?> >20</option>
					<option value="30" <?php if ($_REQUEST["show"] == 30) { echo ' selected="selected"'; }  ?> >30</option>
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