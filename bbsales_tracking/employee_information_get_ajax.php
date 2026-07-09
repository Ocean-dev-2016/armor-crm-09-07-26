<?php
$page_id=658;$page_slug='sales_executive_info_form';
include("connect.php");
$ctable 	= "sales_executive_information";
$ctable1 	= "Sales Executive Information";
$ctable_where = "isDelete = 0 AND isActive = 1";

if (isset($_REQUEST['searchName']) && $_REQUEST['searchName'] != "") {
	$ctable_where .= " AND first_name LIKE '%".$_REQUEST['searchName']."%' OR middle_name LIKE '%".$_REQUEST['searchName']."%' OR surname LIKE '%".$_REQUEST['searchName']."%'";
}

$ctable_r = $db->rp_getData($ctable,"*",$ctable_where,"id DESC",0);
// print_r($_REQUEST);die;
?>
<style>
.table-scrollable 
{
	width: auto;
	height: 300px;
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
<table id="example1" class="table table-bordered table-striped dataTable ">
	<thead class="fix-th">
		<tr>
			<th class="fix-th1"></th>
			<th class="fix-th1">Sr No.</th>
			<th class="fix-th1">First Name</th>
			<th class="fix-th1">Middle Name</th>
			<th class="fix-th1">Surname</th>
			<th class="fix-th1">Gender</th>
			<th class="fix-th1">Contact No</th>
			<th class="fix-th1">Post Applied</th>
			<th class="fix-th1">Present Address</th>
			<th class="fix-th1">Image</th>
		</tr>
	</thead>
	<tbody>
	<?php
	if($ctable_r)
	{
		$count = 0;
		while($ctable_d = mysqli_fetch_array($ctable_r))
		{
			$count++;
			?>
			<tr>
				<td><?php $ctable_d['id']; ?>				
					<?php				
					if($rights['update_flag']==1)
					{
						?>	
						<div class="btn-group">				
							<button aria-expanded="false" data-toggle="dropdown" type="button" class="btn btn-sm blue dropdown-toggle"> <i class="fa fa-gear"></i>
							</button>
							<ul role="menu" class="dropdown-menu">
								<li>
									<a  href="employee_information_crud.php?mode=edit&id=<?php echo $ctable_d['id']; ?>" title="Edit"><span class="text-primary"><i class="fa fa-pencil"></i> &nbsp;Edit</span></a>
								</li>
								<?php
								if($rights['delete_flag']==1 && $ctable_d['id']!=-1)
								{
									?>
									<li>
										<a  onClick="del_conf('<?php echo $ctable_d['id']; ?>');" title="Delete"><span class="text-danger"><i class="fa fa-times"></i> &nbsp;Delete</span></a>
									</li>
									<?php
									}
									$self_analysis_r=$db->rp_getTotalRecord("self_analysis","isDelete=0 AND sales_executive_form_id='".$ctable_d['id']."'",0);
									// echo $self_analysis_r;die;
									if($self_analysis_r > 0)
									{
									// echo $self_analysis_r;die;
										$id_r=$db->rp_getValue("self_analysis","id","isDelete=0 AND sales_executive_form_id='".$ctable_d['id']."'");
										$editUrl="mode=edit&id=".$id_r."&employee_id=".$ctable_d['id'];
									}
									else
									{
										// echo "no";die;
										$editUrl="mode=add&employee_id=".$ctable_d['id'];
									}
									?>
									<li>

										<a  href="self_analysis_crud.php?<?php echo $editUrl;?>" title="Self Analysis Questios"><span class="text-primary"><i class="fa fa-circle"></i> &nbsp;Self Anaiysis</span></a>
									</li>

									<?php 
									$goal_setting_to_achieving_r=$db->rp_getTotalRecord("goal_setting_to_achieving","isDelete=0 AND sales_executive_form_id='".$ctable_d['id']."'",0);

									if($goal_setting_to_achieving_r > 0)
									{
										$id_r=$db->rp_getValue("goal_setting_to_achieving","id","isDelete=0 AND sales_executive_form_id='".$ctable_d['id']."'");
										$editUrl="mode=edit&id=".$id_r."&employee_id=".$ctable_d['id'];
									}
									else
									{
										$editUrl="mode=add&employee_id=".$ctable_d['id'];
									}
									?>
									<li>

										<a  href="goal_setting_to_achieving_crud.php?<?php echo $editUrl;?>" title="goal_setting_to_achieving_crud"><span class="text-primary"><i class="fa fa-circle"></i> &nbsp;Goal Settings & Achieve</span></a>
									</li>




									
							</ul>
						</div>
						<?php
					}
					?>
				</td>
				<td><?php echo $count; ?></td>
				<td>
					<a href="view_employee_information.php?mode=get_sales_executive_all_information&sales_executive_id=<?=$ctable_d['id']?>">
						<?php echo stripslashes($ctable_d['first_name']); ?>
					</a>
				</td>
				<td><?php echo stripslashes($ctable_d['middle_name']); ?></td>
				<td><?php echo stripslashes($ctable_d['surname']); ?></td>
				<td><?php echo stripslashes($ctable_d['gender']); ?></td>
			    <td><?php echo stripslashes($ctable_d['contact_no']); ?></td>
				<td><?php echo stripslashes($ctable_d['post_applied']); ?></td>
				<td><?php echo stripslashes($ctable_d['present_address']); ?></td>
				<td>
					<?php 
						$img = explode(",", $ctable_d['image_path']);
						$imgpath = array();
						for ($i=0; $i < sizeof($img); $i++)
						{ 
							$imgpath[] = EMPLOYEE_IMAGE.$db->rp_getValue("media","url","reference_id='".$ctable_d["id"]."' AND id='".$img[$i]."'",0);
						}
						// print_r($imgpath);
						for ($i=0; $i < sizeof($imgpath); $i++)
						{
							if($i==0)
							{
								?>
									<a href="<?=$imgpath[$i]?>" data-lightbox="visit<?=$count?>" data-title="stop_visit <?=$ctable_d['id']?>"><img src="<?=$imgpath[$i]?>" style="height: 80px;"></a>
								<?php 
							}
							else
							{
								?>
								<div class="hidden">
									<a href="<?=$imgpath[$i]?>" data-lightbox="visit<?=$count?>" data-title="stop_visit <?=$ctable_d['id']?>"><img src="<?=$imgpath[$i]?>" style="height: 80px;"></a>
								</div>
								<?php
							}
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


</div>
<div class="modal fade" id="leaveReasonModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="leave_status"></h4>
				<button style="margin-top: -15px!important;" type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<label for="cancel-reject reason" id="status1"></label>
				<textarea class="form-control" name="cancel_reject_reason" id="cancel_reject_reason"></textarea>
				<p style="color:red;" id="require_msg"></p>
				<input type="hidden" class="leave_id" name="leave_id" id="leave_id">
				<input type="hidden" class="status2" name="mode" id="mode">
				<input type="hidden" class="msg" name="msg" id="msg">
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary" onclick="CancelRejectReason()">Save changes</button>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$('#leaveReasonModal').on('show.bs.modal', function (event) 
    {
        var button = $(event.relatedTarget) // Button that triggered the modal
        var id=button.data("id");
        $(".leave_id").val(id);
        var status=button.data("status");
        $("#leave_status").html(status);
        var status1=button.data("status1");
        $("#status1").html(status1);
        var status2=button.data("status2");
        $(".status2").val(status2);
        var msg=button.data("msg");
        $(".msg").val(msg);
    })
</script>

<!-- <?php
	echo $db->getAddButton($ctable,$ctable1);
?> -->
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
	function acceptLeave(id,mode,msg)
	{   
		var cancel_reject_reason = $("#cancel_reject_reason").val();
		if(cancel_reject_reason=="" && mode!="accept")
		{
			var require_msg = "Please Provide Reason";
			$("#require_msg").html(require_msg);
		}
		else
		{
			var r = confirm(msg);
	        if(r){
	        	$.ajax({
	                url:"leave_request_ajax_function.php",
	                type:"POST",
	                data:{
	                    m:mode,
	                    id:id, 
	                    cancel_reject_reason,
	                    cancel_reject_reason,               
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
	                    if(result.ack==1)
	                    {                       
	                        toastr.success(result.ack_msg,"Success!!"); 
	                        location.reload();
	                    }
	                    else
	                    {
	                        toastr.error(result.ack_msg, 'Error!!');
	                    }
	                },            
	            })
	        }
	    }
	}
	function CancelRejectReason()
	{
		var cancel_reject_reason = $("#cancel_reject_reason").val();
		var leave_id = $("#leave_id").val();
		var mode = $("#mode").val();
		var msg = $("#msg").val();
		acceptLeave(leave_id,mode,msg);
	}
</script> 
<script type="text/javascript">
	$(".filterBtn").on("click",function()
	{
		sales_executive = $("#sales_executive").val();
		status = $("#status").val();
		customer_id = $("#customer_id").val();
		df=$("#material_request_filter_input").val();
		df = encodeURI(df)
		displayRecords(100,1);
	})
	
	  $(".datetimerange-picker-btn").on("click",function(){
        $(".datetimerange-picker-input",$(this).closest(".date")).focus();
    });
    $(".datetimerange-picker-input").daterangepicker({"format":"dd-mm-yy ",autoUpdateInput: false,timePicker:false,ranges: {
           'Today': [moment(), moment()],
           'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
           'Last 7 Days': [moment().subtract(6, 'days'), moment()],
           'Last 30 Days': [moment().subtract(29, 'days'), moment()],
           'This Month': [moment().startOf('month'), moment().endOf('month')],
           'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
}});
    $('.datetimerange-picker-input').on('apply.daterangepicker', function(ev, picker) {
 $(".datetimerange-picker-input").val(picker.startDate.format('DD-MM-YYYY')+" to "+picker.endDate.format('DD-MM-YYYY'));
});
</script>
<script type="text/javascript">
	$("#leave_type").select2();
	$("#status").select2();
</script>