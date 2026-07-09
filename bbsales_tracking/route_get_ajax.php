<?php
$page_id=631;$page_slug='route_page';
/*
 * @author Ravi Patel
 */
include("connect.php");
$ctable 	= "my_route";
$ctable1 	= "Sales Officer";

$ctable_where = "";
// Get the total number of rows in the table

if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!="")
{
     //echo $_REQUEST['searchName'];exit();
    $phone_id = array();
    $exe_ids_r = $db->rp_getData("executive","*","cname LIKE '%".trim($_REQUEST['searchName'])."%' OR phone LIKE '%".trim($_REQUEST['searchName'])."%' OR company_name LIKE '%".trim($_REQUEST['searchName'])."%' ","",0);

   

    // $customer_ids_r = $db->rp_getData("executive","*","phone LIKE '%".trim($_REQUEST['searchName'])."%' OR cname LIKE '%".trim($_REQUEST['searchName'])."%'  OR company_name LIKE '%".trim($_REQUEST['searchName'])."%'  ","",0);

    if ($exe_ids_r)
    {
        while($exe_id_d = mysqli_fetch_assoc($exe_ids_r))
        {
          $phone_id[] = $exe_id_d['id'];
        }

        $phone_no_id = implode(",", $phone_id);

        $ctable_where.="customer_id IN (".$phone_no_id.") AND "; 
    }
    // if($order_ids_r)
    // {
    //     while($order_id_d = mysqli_fetch_assoc($order_ids_r))
    //     {
    //       $phone_id[] = $order_id_d['id'];
    //     }

    //     $phone_no_id = implode(",", $phone_id);

    //     $ctable_where.="reference_id IN (".$phone_no_id.") OR ";
    // }
    // if($customer_ids_r)
    // {
    //     while($customer_id_d = mysqli_fetch_assoc($customer_ids_r))
    //     {
    //         $cname= $db->rp_getValue("complain","customer_id","id='".$customer_id_d['id']."'",0);

    //       $phone_id[] = $cname;
    //     }

    //     $phone_no_id = implode(",", $phone_id);
    //     $phone_no_id_f = rtrim($phone_no_id,(","));
    //     // print_r($phone_no_id_f);exit;

    //     $ctable_where.="reference_id IN (".$phone_no_id_f.") OR ";
    // }
    // if($customer_ids_r)
    // {
    //     while($customer_id_d = mysqli_fetch_assoc($customer_ids_r))
    //     {
    //         $cname= $db->rp_getValue("request","customer_id","id='".$customer_id_d['id']."'",0);

    //       $phone_id[] = $cname;
    //     }

    //     $phone_no_id = implode(",", $phone_id);
    //     $phone_no_id_f = rtrim($phone_no_id,(","));
    //     $ctable_where.="reference_id IN (".$phone_no_id_f.") OR ";
    // }
    //$ctable_where.=" description LIKE '%".trim($_REQUEST['searchName'])."%' OR ";
    // $ctable_where.=" 0=1 AND ";
    else 
    {
        $ctable_where.="phone IN ('') AND ";
    }
}

//$ctable_where .= " isDelete=0 AND type='sales_manager'";

$ctable_where .= " isDelete=0 ";

$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"]) ) ? intval($_REQUEST["show"]) : 100;

if(isset($_REQUEST["page"])){
	$page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
	if(!is_numeric($page_number)){die('Invalid page number!');} //incase of invalid page number
}else{
	$page_number = 1; //if there's no page number, set it to 1
}

$get_total_rows = $db->rp_getTotalRecord($ctable,$ctable_where); //hold total records in variable


// if(isset($_REQUEST['sales_id']) && $_REQUEST['sales_id']!="" && $_REQUEST['sales_id']!=NULL)
// {
//  	$ctable_where .= " AND sales_id = '".$_REQUEST['sales_id']."' ";
//  	$sales_id=$_REQUEST['sales_id'];
// }

if(isset($_REQUEST['df1']) && $_REQUEST['df1']!="")
{
			//echo $_REQUEST['df'];exit;
			$date_filter_query = urldecode( $_REQUEST['df1'] );

			$date_filter_query_ex=explode(" to ",$date_filter_query);

			$ctable_where .= " AND ( DATE(date)>='".date("Y-m-d",strtotime($date_filter_query_ex['0']))."' AND DATE(date)<='".date("Y-m-d",strtotime($date_filter_query_ex['1']))."'  ) ";
}

// if(isset($_REQUEST['state']) && $_REQUEST['state']!="" && $_REQUEST['state']!=NULL)
// {
//  	$ctable_where .= " AND state = '".$_REQUEST['state']."' ";
//  	$state=$_REQUEST['state'];
// }
// if(isset($_REQUEST['city']) && $_REQUEST['city']!="" && $_REQUEST['city']!=NULL)
// {
//  	$ctable_where .= " AND city = '".$_REQUEST['city']."'";
//  	$city=$_REQUEST['city'];
// }



if($_SESSION[SITE_SESS.'REFERANCE_TYPE']!=0)
{
	if($_SESSION[SITE_SESS.'REFERANCE_TYPE']==2) //sales executive and its chain wise order
	{ 
		if($rights['personal_flag']==1)
		{
			$ctable_where .= " AND sales_id='" . $_SESSION[SITE_SESS.'REFERANCE_ID'] . "'";
		}
		else
		{ 
			if($rights['chain_vise_flag'] == 1)
		 	{
				$check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];
				$get_sales_type=$db->rp_getValue("sales_executive","type","isDelete=0 AND id='". $check_id."'",0);
			    if ($get_sales_type== "sales_manager") 
			    {
			        $sales_executive_type = "Regional Sales Manager";
			        $key="sm_id";
			        $WhereCondition.=' ' .$key.'='.$check_id;
			    }
			    else if ($get_sales_type == "area_sales_manager") 
			    {
			        $sales_executive_type = "National Sales Manager";//Business Development Manager
			        $key="asm_id";
			        $WhereCondition.=' ' .$key.'='.$check_id;
			    }
			    else if ($get_sales_type == "sales_officer") 
			    {
			        $sales_executive_type = "Area Sales Manager";//Area Sales Manager
			        $key="so_id";
			        $WhereCondition.=' ' .$key.'='.$check_id;
			    }
			    else if ($get_sales_type == "sales_executive") 
			    {
			        $sales_executive_type = "Sales Officer";
			        $key="se_id";
			        $WhereCondition.=' ' .$key.'='.$check_id;
			    }
			    else
			    {
			    	$WhereCondition.=' type = "service_engineer"';
			    }

			    $data = $db->rp_getData("sales_executive","id",$WhereCondition,"",0);

			    $SALEID1=array();
				if($data)
				{
					while($data_d=mysqli_fetch_assoc($data))
					{
						$SALEID1[]=$data_d['id'];
					}
				}
				if(!empty($SALEID1))
				{
					$SALEID1=implode(",", $SALEID1);
					$ctable_where .= " AND sales_id IN (".$SALEID1.','.$_SESSION[SITE_SESS.'REFERANCE_ID'].")";	
				}
				else
				{
					$ctable_where .= " AND sales_id IN (".$_SESSION[SITE_SESS.'REFERANCE_ID'].")";		
				}
			}
			else
			{
				// $ctable_where .= " isDelete=0 ";
			}
		}
	}
}
else
{
	// $ctable_where .= " isDelete=0 AND status!=-1";
}

	if(isset($_REQUEST['sales_id']) && $_REQUEST['sales_id']!="" && $_REQUEST['sales_id']!=NULL)
	{
	 	$ctable_where .= " AND sales_id = '".$_REQUEST['sales_id']."' ";
	 	$sales_id=$_REQUEST['sales_id'];
	}

	if(isset($_REQUEST['route_id']) && $_REQUEST['route_id']!="" && $_REQUEST['route_id']!=NULL)
	{
	 	$ctable_where .= " AND route_id = '".$_REQUEST['route_id']."' ";
	}

$get_total_rows = $db->rp_getTotalRecord($ctable,$ctable_where,0); //hold total records in variable

//break records into pages
$total_pages = ceil($get_total_rows/$item_per_page);

//get starting position to fetch the records
$page_position = (($page_number-1) * $item_per_page);

$ctable_r = $db->rp_getData($ctable,"*",$ctable_where,"id DESC limit $page_position, $item_per_page",0);
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
    .custom-checkbox {
	  width: 20px;
	  height: 20px;
	  appearance: none; /* Remove default appearance */
	  border: 2px solid #555; /* Border color of the checkbox */
	  border-radius: 4px; /* Rounded corners */
	  outline: none; /* Remove focus outline */
	  cursor: pointer; /* Show a pointer cursor on hover */
	}

	/* Custom style for the checkbox when checked */
	.custom-checkbox:checked {
	  background-color: #007bff; /* Change the background color when checked */
	  border-color: #007bff; /* Change the border color when checked */
	}

	/* Custom style for the checkbox when hovered */
	.custom-checkbox:hover {
	  border-color: #999; /* Change the border color when hovered */
	}

</style>

<form action="" name="frm" id="frm" method="post">
	<div class="table-scrollable">
	<table id="datatable_sm" class="table table-striped table-bordered table-hover">
        <thead class="fix-th">
        	<tr>
        		<th>
        			<div style="display: flex;justify-content: center;">
        				<input type="checkbox" class="custom-checkbox" id="masterCheckbox">
        			</div>
        		</th>
        		<th></th>
        		<th></th>
        		<th>
        			<select style="width: 250px;" class="form-control" id="sales_id" name="sales_id">
						<option value="">Select Sales Person Name</option>
						<?php

						if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
							{
								  
								  	if($rights['personal_flag']==1)
									{
										$ctable_where_sales .=" AND id='".$_SESSION[SITE_SESS.'REFERANCE_ID']."' ";
									}
									else
									{ 
										if($rights['chain_vise_flag'] == 1)
									 	{
											$check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];
											$get_sales_typer=$db->rp_getValue("sales_executive","type","isDelete=0 AND id='". $check_id."'",0);
										    if ($get_sales_typer== "sales_manager") 
										    {
										        $sales_executive_type = "Regional Sales Manager";
										        $key="sm_id";
										        $WhereConditionr.=' ' .$key.'='.$check_id;
										    }
										    else if ($get_sales_typer == "area_sales_manager") 
										    {
										        $sales_executive_type = "National Sales Manager";//Business Development Manager
										        $key="asm_id";
										        $WhereConditionr.=' ' .$key.'='.$check_id;
										    }
										    else if ($get_sales_typer == "sales_officer") 
										    {
										        $sales_executive_type = "Area Sales Manager";//Area Sales Manager
										        $key="so_id";
										        $WhereConditionr.=' ' .$key.'='.$check_id;
										    }
										    else if ($get_sales_typer == "sales_executive") 
										    {
										        $sales_executive_type = "Sales Officer";
										        $key="se_id";
										        $WhereConditionr.=' ' .$key.'='.$check_id;
										    }
										    else
										    {
										    	$WhereConditionr.=' type = "service_engineer"';
										    }

										    $data_r = $db->rp_getData("sales_executive","id",$WhereConditionr,"",0);

										    $SALEID2=array();
											if($data_r)
											{
												while($data_dd=mysqli_fetch_assoc($data_r))
												{
													$SALEID2[]=$data_dd['id'];
												}
											}
											if(!empty($SALEID2))
											{
												$SALEID2=implode(",", $SALEID2);
												$ctable_where_sales .= " AND id IN (".$SALEID2.','.$_SESSION[SITE_SESS.'REFERANCE_ID'].")";	
											}
											else
											{
												$ctable_where_sales .= " AND id IN (".$_SESSION[SITE_SESS.'REFERANCE_ID'].")";		
											}
										}
										else
										{
											// $ctable_where_sales .= " isDelete=0 ";
										}
									}
						   }
						$boxtype_r=$db->rp_getData("sales_executive","*","isDelete=0 AND isActive=1".$ctable_where_sales,"",0);
						if($boxtype_r)
						{
							while($BT=mysqli_fetch_assoc($boxtype_r))
							{
								?>
								<option <?= ($sales_id==$BT['id'])?"selected":""; ?> value="<?= $BT['id']; ?>"><?= $BT['name']; ?></option>
								<?php
							}
						}
						?>
				   </select>
        		</th>
        		<th></th>
        		<th></th>
        		<th>
        			<label>Filter By Date</label>
						<div class="input-group">
							<input  class="form-control datetimerange-picker-input" id="quick_stock_adjustment_filter_input" value="<?php echo $date_filter_query; ?>" name="quick_stock_adjustment_filter_input" placeholder="Select Date Range" type="text" style="border-radius: 0;height: 34px!important; margin-right: -72px;">
							<span class="input-group-addon datetimerange-picker-btn">
								<i class="fa fa-calendar"></i>
							</span>

							<span class="input-group-btn">
								<!-- <button class="btn btn-success filterBtn" type="submit" value="search">Filter</button> -->
							</span>
						</div>
        		</th>
        		<th></th>
        		<th>
        			<!-- <select class="form-control status" name="state" id="state" onChange="filter_state(this.value);" autofocus >
	                    <option value="">Select State</option>
						<?php
						$state_r = $db->rp_getData("state","*",0);
						if(mysqli_num_rows($state_r)>0)
						{
							while($state_d = mysqli_fetch_array($state_r))
							{
								?>
								<option value="<?php echo $state_d['name']; ?>" <?=($state == $state_d['name'])?"selected":"";?>><?php echo $state_d['name']; ?></option>
								<?php
							}
						}
						?>
                	</select> -->
        		</th>
        		<th></th>
        		
        		
        	</tr>
            <tr>
            	<th class="fix-th1"></th>
                <th class="fix-th1">No.</th>
               
				<th class="fix-th1">Person Name</th>
                <th class="fix-th1">Sales Person Name</th>
                 <th class="fix-th1">Company Name</th>
                <th class="fix-th1">Mobile No</th>
				<th class="fix-th1">Date</th>
				
				<th class="fix-th1">State</th>	
				<th class="fix-th1">City</th>	
				<th class="fix-th1">Remark</th>	
				<!-- <th>Action</th> -->
				<!-- <th>User Create</th> -->
            </tr>
        </thead>
        <tbody>
        <?php
        if(mysqli_num_rows($ctable_r)>0){
            $count = 0;
            
            while($ctable_d = mysqli_fetch_array($ctable_r)){
            $sales_executive_type = "";

            // print_r($ctable_d);


                            if($ctable_d['visit_flag'] == 1 && $ctable_d['order_flag'] == 0)
							{
								// echo "gg";exit();
								$style = "style='background-color: #afcfdc;'";
							}
							else if($ctable_d['order_flag'] == 1 && $ctable_d['visit_flag'] == 0)
							{
								// echo "gglll";exit();
								$style = "style='background-color: #EDD487;'";
							}
							 else if ($ctable_d['visit_flag'] == 1 && $ctable_d['order_flag'] == 1) 
							{
								$style = "style='background-color: #B6DDB6;'";
							}
							else
							{
								$style = "";
							}
							// else if ($ctable_d['status'] == 1) 
							// {
							// 	$style = "style='background-color: #FFFF99;'";
							// }
							// else if ($ctable_d['status'] == 0) 
							// {
							// 	$style = "style='background-color: #add8e6;'";
							// }
            
        ?>
            <tr <?= $style ?> >
            	<td style="width: 110px;">
            		<div class="btn-group">
            			<button aria-expanded="false" data-toggle="dropdown" type="button" class="btn btn-sm blue dropdown-toggle">
							<i class="fa fa-gear"></i>
						</button>
            			<ul role="menu" class="dropdown-menu">
            				<!-- <li>
								<a href="route_crud.php?mode=edit&id=<?php echo $ctable_d['id']; ?>" title="Edit">
									<span class="text-primary">
										<i class="fa fa-pencil"></i>
										&nbsp;Edit
									</span>
								</a>
							</li> -->
							<li>
									<a onClick="del_conf('<?php echo $ctable_d['id']; ?>');" title="Delete">
										<span class="text-danger">
											<i class="fa fa-times"></i>
											&nbsp;Delete
										</span>
									</a>
							</li>
						
						</ul>
						<input type="checkbox" class="custom-checkbox subCheckbox" value="<?= $ctable_d['id'] ?>" id="checked_<?= $ctable_d['id'] ?>">
            		</div>
            	</td>
            	<td><?php echo ++$count; ?></td>
				<td><?php echo $db->rp_getValue("executive","cname","isDelete=0 AND id='".$ctable_d['customer_id']."'") ?></td>
              <td><?php echo $db->rp_getValue("sales_executive","name","isDelete=0 AND id='".$ctable_d['sales_id']."'") ?></td>
              <td><?php echo $db->rp_getValue("executive","company_name","isDelete=0 AND id='".$ctable_d['customer_id']."'") ?></td>
              <td><?php echo $db->rp_getValue("executive","phone","isDelete=0 AND id='".$ctable_d['customer_id']."'") ?></td>
				<td><?php echo date('d-m-Y',strtotime($ctable_d['date'])); ?></td>

				<td><?php echo $db->rp_getValue("master_route","state","isDelete=0 AND id='".$ctable_d['route_id']."'") ?></td>
				<td><?php echo $db->rp_getValue("master_route","city","isDelete=0 AND id='".$ctable_d['route_id']."'") ?></td>
				<td><?= $ctable_d['remark'] ?></td>

				 <!-- <td><?php echo $db->rp_getValue("class","name","isDelete=0 AND id='".$ctable_d['class_id']."'") ?></td>
				 <td><?php echo $db->rp_getValue("area","name","isDelete=0 AND id='".$ctable_d['area_id']."'") ?></td> -->
				
				
				
				<?php
            }
        }
		else{
			?>
			<tr>
			<td colspan="9"><p style="text-align:center;">No data available in table</p></td>
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
				echo $db->rp_paginate_function($item_per_page, $page_number, $get_total_rows, $total_pages); 
				?>
				</ul>
			</div>
		</div>
	</div>
</form>
<script type="text/javascript">
	$("#state").select2();
    $("#city").select2();
    $("#sales_executive_type").select2();
    $("#sales_id").select2();

    function filter_state(state_id,city=""){
	    $.ajax({
	        type: "POST",
	        url: "find_city.php",
	        data:'state_id='+state_id+"&city="+city,
	        beforeSend:function(){
	            // $("#loading-modal").modal('show');  
	            $('.preloader').fadeIn('slow');
	        },
	        success: function(data){
	            $("#city").select2("destroy");
	            $("#city").html(data);
	            $("#city").select2();
	            // $("#loading-modal").modal('hide');
	            $('.preloader').fadeOut('slow');
	        }
	    });
	}
</script>

<!-- system user create script -->
<script type="text/javascript">
	function CreateSystemUser(id)
	{
		var r = confirm("Are you sure you want to Create User ?");
		if(r)
		{
			$.ajax({
	            url:"create_user_ajax_function.php",
	            type:"POST",
	            data:{
	                id:id, 
	            },
	            beforeSend: function() {
					$('.preloader').fadeIn('slow');
				},
	            success:function(result) 
	            {
                    var result=JSON.parse(result);
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

	$(".filterBtn").on("click", function() {
		sales_executive = $("#sales_executive").val();
		customer_id = $("#customer_id").val();
		df1 = $("#quick_stock_adjustment_filter_input").val();
		df1 = encodeURI(df1)
		displayRecords(100, 1);
	})

	$(".datetimerange-picker-btn").on("click", function() {
		$(".datetimerange-picker-input", $(this).closest(".date")).focus();
	});
	$(".datetimerange-picker-input").daterangepicker({
		"format": "dd-mm-yy ",
		autoUpdateInput: false,
		timePicker: false,
		ranges: {
			'Today': [moment(), moment()],
			'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
			'Last 7 Days': [moment().subtract(6, 'days'), moment()],
			'Last 30 Days': [moment().subtract(29, 'days'), moment()],
			'This Month': [moment().startOf('month'), moment().endOf('month')],
			'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
		}
	});
	$('.datetimerange-picker-input').on('apply.daterangepicker', function(ev, picker) {
		$(".datetimerange-picker-input").val(picker.startDate.format('DD-MM-YYYY') + " to " + picker.endDate.format('DD-MM-YYYY'));
	});
</script>
<!-- system user create script -->
<script type="text/javascript">

	 // When the master checkbox is clicked
    $("#masterCheckbox").click(function() {
      // Get its checked status
      var isChecked = $(this).prop("checked");
      
      // Update all sub checkboxes with the same checked status
      $(".subCheckbox").prop("checked", isChecked);
      var checkedCount = $('.subCheckbox:checked').length;
      $("#check-count").html("("+checkedCount+")");
    });
    
    // When a sub checkbox is clicked
    $(".subCheckbox").click(function() {
      // Check if all sub checkboxes are checked
      var allChecked = $(".subCheckbox").length === $(".subCheckbox:checked").length;
      
      // Update the master checkbox accordingly
      $("#masterCheckbox").prop("checked", allChecked);
      var checkedCount = $('.subCheckbox:checked').length;
      $("#check-count").html("("+checkedCount+")");
    });

    function checkedDataDelete() {
    	var isValid = confirm("Are You Sure You Want To Delete Checked Sales Route?");
		if(isValid){
	    	// Get the values of the checked checkboxes
		    var checkedValues = $(".subCheckbox:checked").map(function() {
		      return $(this).val();
		    }).get();
		    
		    // Check if the array is empty
			if (checkedValues.length === 0) {
			 	toastr.error("Not Any Checkbox Is Selected ");
			} else {
			    $.ajax({
			      type: "POST",
			      url: "delete_sales_route_ajax.php",
			      data: { checkboxes: checkedValues },
			      success: function(response) {
			        // Display the response from the PHP file
			        response = JSON.parse(response);
			        if (response.ack==1) {
				        toastr.success(response.ack_msg);
			        } else {
			        	toastr.error(response.ack_msg);
			        }
				   callAjax();
			      }
			    });
			}
		}


    }
</script>
<?php require_once "disconnect.php"; ?>