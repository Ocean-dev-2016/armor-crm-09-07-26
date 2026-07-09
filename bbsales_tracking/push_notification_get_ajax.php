<?php
$page_id=580;$page_slug='price_list_master';
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
	$Where.=" AND (id LIKE '%".$Query."%'  OR title LIKE '%".$Query."%' )";
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
	$OrderBy="title ASC";
}
$UpperLimit=($PageNumber-1)*$LowerLimit;
if($UpperLimit!="" &&  $LowerLimit!="")
{
	$Limit=$UpperLimit." ,".$LowerLimit."   ";
	
}
else if($UpperLimit!="")
$Limit=$UpperLimit;
$RequiredColumns=implode(",",$RequiredColumns);
// $Results=$Pricelist->get_all($Where,$OrderBy,$Limit,$RequiredColumns);				
$Results=$db->rp_getData("push_notification","*",$Where,$OrderBy,0,$Limit);
?> 
<style type="text/css">
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

<div class="row">	
			<div class="col-md-3">
				<label>Select Type</label>
				<div class="form-group  has-info">
					<input type="hidden" name="isVaidToSend" id="isVaidToSend" value="0">
					<select class="form-control edited  type1"  value="<?php echo $type1; ?>"  id="type1" name="type1" onChange="getTypeData(this.value);">
						<option value="">Select Type </option> 
						<option value="1">All Sales Person</option>
						<option value="2">Selected Sales Person</option>
						<option value="3">All Customer</option>
						<option value="4">Selected Customer</option>
					</select>
					<span class="help-block"></span>
				</div>
			</div> 
			<div class="col-md-3 hidden" id="typeData"> 
				<label for="typeVal" id="typeTitle"></label><br/>
				<div class="form-group  has-info">
					<select class="form-control typeVal" name="typeVal[]" id="typeVal" multiple="" onchange="checkValue(this.value);">
						 
					</select> 
				</div> 
			</div>  
		</div>
		<div class="table-scrollable">
<table class="table table-striped table-hover table-bordered unit_table_class" id="country_table_id">
    <thead class="fix-th">
        <tr>
			<th class="fix-th1" style="width: 5%;"></th>
            <th class="fix-th1">Sr.no</th>
			<th class="fix-th1">Title</th> 
			<th class="fix-th1">Description</th> 
			<th class="fix-th1">Image</th> 
			<th class="fix-th1">Action</th> 
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
				$file=NOTIFICATION.$R['image_path'];
				if($R['image_path']!="" )
                {
                    $img=SITEURL.NOTIFICATION.$R['image_path'];
                    $br="";
                }
                else
                {                               
                    $img=SITEURL."images/no_image_found.jpg";
                    $br="border:1px solid #000";
                }
 		?>
	  	<tr class="">

	  		<td>
				<?php $ctable_d['id']; 				
				if($rights['update_flag']==1)
				{
					?>
					<div class="btn-group">				
						<button aria-expanded="false" data-toggle="dropdown" type="button" class="btn btn-sm blue dropdown-toggle">
							<i class="fa fa-gear"></i>
						</button>
						<ul role="menu" class="dropdown-menu">
							<li>
								<a href="push_notification_crud.php?mode=edit&id=<?php echo $R['id']; ?>" title="Edit">
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
			<td><?php echo $R['title']; ?></td>
			<td><?php echo $R['descr']; ?></td>
			<td style="text-align: center;"><img src="<?= $img; ?>" style="<?= $br; ?>" width="80" height="80"></td>
			<td>		
				<!-- <a  href="push_notification_crud.php?mode=edit&id=<?php echo $R['id']; ?>" title="Edit"><span class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i></span></a>
				<a class="btn btn-danger btn-sm" onClick="del_conf('<?php echo $R['id']; ?>');" title="Delete"><i class="fa fa-times"></i></a> -->
				<input type="hidden" name="send_msg_text" id="send_msg_text" value="">
				<input type="hidden" name="refresh_token_table" id="refresh_token_table" value="">
				<button type="submit" name="submit" value="print"  onClick="sendPN('<?php echo $R['id']; ?>')" class="btn yellow btn-sm sendpn">Send</button>
			</td>
	 	</tr> 
		<?php													 
			}
		}
		else
		{
			?>
			<tr>
				<td colspan="5" class="text-center">No Data Found!!</td>
			</tr>
			<?php
		}
		?>   
    </tbody>
</table> 
</div>
<div class="row">
		<div class="col-md-6">
			<div class="dataTables_info"> Rows Limit:
				<select id="numRecords" onChange="changeDisplayRowCount(this.value);">
					<option value="500" <?php if ($_REQUEST["show"] == 500 || $_REQUEST["show"] == "") {
											echo ' selected="selected"';
										}  ?>>500</option>
					<option value="1000" <?php if ($_REQUEST["show"] == 1000) {
											echo ' selected="selected"';
										}  ?>>1000</option>
					<option value="2000" <?php if ($_REQUEST["show"] == 2000) {
												echo ' selected="selected"';
											}  ?>>2000</option>
					<option value="5000" <?php if ($_REQUEST["show"] == 5000) {
												echo ' selected="selected"';
											}  ?>>5000</option>
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

<script type="text/javascript">
	function sendPN(id)
	{
	    var msg=$("#send_msg_text").val();
	    var get_refresh_token_table = $("#refresh_token_table").val();
		var r = confirm(msg);
            if(r){

            	var type1 = $("#type1").val(); 
            	if(type1==2 || type1==4)
            	{
            		var typeVal = $("#typeVal").val(); 
            	}

                $.ajax({
                    url:"push_notification_ajax_function.php",
                    type:"POST",
                    data:{
                        m:'send_to_all',
                        id:id,                
                        type1:type1,                
                        typeVal:typeVal, 
                        get_refresh_token_table:get_refresh_token_table,
                    },
                    beforeSend: function() {
						// $("#loading-modal").modal('show');
						$('.preloader').fadeIn('slow');
					},
                    success:function(result) 
                    {
                        var result=JSON.parse(result);
                        // $("#loading-modal").modal('hide');
                        $('.preloader').fadeOut('slow');
                        //alert(result['ack']);
                        // msg=result['ack_msg'];
                        if(result.ack==1)
                        {                       
                            toastr.success(result.ack_msg,"Success!!"); 
                        }
                        else
                        {
                            toastr.error(result.ack_msg, 'Error!!');
                        }
                    },            
                })
            }
	}
</script> 
<script type="text/javascript">
	$("#type1").select2();
   
</script>
<?php require_once 'disconnect.php';  ?>