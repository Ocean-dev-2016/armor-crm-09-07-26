<?php 
$page_id=580;$page_slug='price_list_master';
$ctable 	= "price_list";
$ctable1 	= "Price List";
$main_page 	= $ctable;
$page 		= "manage_".$ctable;
$page_title = $ctable1;
$page_hierarchy=array(array("link"=>"","title"=>"Sales & Marketing"),array("link"=>"pricelist_master_manage.php","title"=>$page_title));
include("connect.php");

?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
<meta charset="utf-8"/>
<title><?php echo $page_title; ?> | <?php echo SITETITLE; ?></title>
<?php include("include_css.php"); ?>
<link rel="stylesheet" type="text/css" href="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css"/>
<link rel="stylesheet" type="text/css" href="css/jquery.datetimepicker.css" />
 <link rel="stylesheet" type="text/css" href="assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" />
</head>
<body class="page-md">
<?php include("header.php"); ?>
<div class="page-container">
	
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				<h1><a href="<?php echo "dashboard.php";?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php $db->pageBar($page_hierarchy);?> </h1>
			</div>
		</div>
	</div>
	
	<div class="page-content">
		<div class="container">
			<div class="row">
				<div class="col-xl-12"> 
				<?php $db->printErrorMessage(); ?>
				<?php $db->printSuccessMessage(); ?>
				<div class="col-xl-12 ">
                    <!-- BEGIN Portlet PORTLET-->
                    <div class="portlet box blue">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-filter"></i>Filters </div>
                             <div class="tools">
                                <a href="javascript:;" class="collapse" data-original-title="" title=""> </a>

                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="slimScrollDiv" style="position: relative;  width: auto; height: auto;">
								<div class="row">
                                 	 <div class="col-md-2 col-xs-2 col-sm-2">
									<?php
										echo $db->getAddButton("pricelist_master");
									?>	
									</div>
									<button style="margin: 6px 0 0 0" class=" btn btn-danger btn-sm" data-toggle="modal" data-target="#exampleModal">Change Pricelist</button>
									<!-- <div class="col-md-3">
										<a href="import_pricelist_multiple_data_crud.php" name="inport_btn" value="Import Pricelist" class="btn btn-warning "><i class="fa fa-upload"></i> Import Pricelist</a> -->

										<!-- <button class=" btn btn-danger btn-sm" data-toggle="modal" data-target="#exampleModal">Change Pricelist</button> -->
									<!-- </div> -->


                           <div class="col-md-7 col-xs-7 col-sm-7 pull-right">
                             <div class="form-inline" role="form">
                                    <form class="form-inline pull-right" role="form" onSubmit="return searchByName();">
                                       	<div class="form-group">

                                          <input type="text" style="width: 450px!important" placeholder="Search By Name :  " class="form-control input-large" name="searchName" id="searchName" value="" />

                                       	</div>

                                       	<div class="form-group">
                                          <input class="btn btn-danger btn-sm" type="submit" value="search">
                                       	</div>

                                       	<div class="form-group">
                                          <input class="btn btn-success btn-sm" type="button" value="clear" onClick="clearSearchByName();">
                                       	</div>

                                      	<div class="form-group">
			                                    <div class="btn-group">

													<button aria-expanded="false" data-toggle="dropdown" type="button" class="btn btn-sm blue dropdown-toggle">
														<i class="fa fa-gear"></i>
													</button>
													
													<ul role="menu" class="dropdown-menu dropdown-menu-right pull-right">

														<?php
												if($rights['print_flag']==1 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
												{ 
													?>
														
														<li>
															<a name="print" onClick="PricelistPrint()" title="Print Report"><i class="fa fa-print"></i>Print</a>
														</li>

														<?php
																						}
												if($rights['export_excel_flag']==1 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
																{ 
																	?>
														<li>
															<a class="excel" name="excel" onClick="genReport()" id="excel" title="Download XL Report"><i class="fa fa-file-excel-o"></i>Excel</a>
														</li>

														<?php
															}
															?>
													</ul>
												</div>
	                                       	</div>
                                    </form>
                                    </div>
                                </div>	
								</div>
							</div>
                        </div>
                     </div>
                    	</div>
                    <!-- END Portlet PORTLET-->
                </div>
					
		    </div>
		</div>
			<div class="portlet light">
						<div class="table-toolbar">
							<div class="row">
								<!-- <div class="col-md-6">
									<?php
										echo $db->getAddButton("hsncode");
									?>	
								</div>							 -->	
							</div>
						</div>
						<div class="portlet-body">
							<div class="loading-div" style="display:none;"> <img src="assets/admin/layout/img/ajax-loader.gif" alt="" style="margin-bottom: 10%;;margin-top:10%;padding-left:48%;" > </div>
							<div id="results"></div>
						</div>
					</div>
		</div>
	</div>
	
</div>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Change Pricelist</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group">
			<label>Applied Date </label>
			<input type="text" class="form-control datetimepicker" name="applied_date" id="applied_date" value="<?php echo date("d-m-Y"); ?>" >    	
        </div>

        <div class="form-group">
			<label>Old Price List </label>
			<select class="form-control" id="old_price_list" name="old_price_list">
				<option value="">Select Old PriceList</option>
				<?php 
				$pricelist_old=$db->rp_getData("price_list","*","isDelete=0 ","",0);
				while ($pricelist_old_d=mysqli_fetch_assoc($pricelist_old)) 
				{
				?>
				<option value="<?=$pricelist_old_d['id']?>" data-customer-count="<?=$db->rp_getValue("executive","COUNT(*)","isDelete=0 AND price_list_id='".$pricelist_old_d['id']."'")?>"><?=$pricelist_old_d['pricelist_name']?></option>
				<?php
				}
				?>
			</select> 
			<span id="customer_count"></span>       	
        </div>

        <div class="form-group">
			<label> New Price List </label>
			<select class="form-control" id="new_price_list" name="new_price_list">
				<option value="">Select New PriceList</option>
				<?php 
				$pricelist_new=$db->rp_getData("price_list","*","isDelete=0 ","",0);

				while ($pricelist_new_d=mysqli_fetch_assoc($pricelist_new)) 
				{
				?>
				<option value="<?=$pricelist_new_d['id']?>"><?=$pricelist_new_d['pricelist_name']?></option>
				<?php
				}
				?>
			</select>        	
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick="changepricelist()">Save changes</button>
      </div>
    </div>
  </div>
</div>


<!-- Modal -->

<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>
<script type="text/javascript" src="assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>
<script src="js/jquery.datetimepicker.js"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript">
var status="";
var searchName="";
var data_url = "pricelist_master_get_ajax.php";

function searchByName(){
	searchName = $("#searchName").val();
	displayRecords(500,1);
	return false;
}
function clearSearchByName(){
	searchName = "";
	$("#searchName").val("");
	displayRecords(500,1);
}
$("#searchName").keyup(function(event){
	if(event.keyCode == 13){
		$("#searchByName").click();
	}
});
function loadDataTable(){
	
	$('#datatable_1').dataTable({
		"bPaginate": false,
		"order":['desc'],
		"bFilter": false,
		"bInfo": false,
		"bAutoWidth": false,
   //      "aoColumns": [
			  
			//   { "sWidth": "5%" },
			//   { "sWidth": "5%" },
			//   { "sWidth": "20%" },
			//   { "sWidth": "20%","bSortable": false }
			// ],
			// "oLanguage": { "sEmptyTable":     "<i class='fa fa- fa-cubes '></i> &nbsp; No Product Found"},
			"order": [[1,'asc']], /* default order is index 1 */
                    'columnDefs': [ {
                        'targets': [0], /* column index */
                        'orderable': false, /* true or false */
                    }],

                    "oLanguage": {
                        "sEmptyTable": "Sorry No Data Available!!"
                    }
	});
}
function displayRecords(numRecords) {
	var searchName 	= $("#searchName").val();
	searchName 	= encodeURIComponent(searchName.trim());
	$("#results" ).html("");
	$("#results" ).load( data_url+"?show=" + numRecords + "&searchName=" + searchName + "&status=" + status,function(){
		loadDataTable();
	}); //load initial records
	
	//executes code below when user click on pagination links
	$("#results").on( "click", ".paging_simple_numbers a", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName,{"page":page}, function(){ //get content from PHP page
			$(".loading-div").hide(); //once done, hide loading element
			loadDataTable();
		});
		
	});
	$("#results").on( "change", "#numRecords", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName,{"page":page}, function(){ //get content from PHP page
			$(".loading-div").hide(); //once done, hide loading element
			loadDataTable();
		});
		
	});
}

// used when user change row limit
function changeDisplayRowCount(numRecords) {
	displayRecords(numRecords, 1);
}

$(document).ready(function() {
	displayRecords(500,1);
});
function del_conf(id){
	var r = confirm("Are you sure you want to delete?");
	if(r){
		window.location.href='<?php echo $ctable; ?>_crud.php?mode=delete&id='+id;
	}
}
function genReport()
{
	var searchName = $("#searchName").val();
	searchName     = encodeURIComponent(searchName.trim());

	$.ajax({
		method: "POST",
		url: "pricelist_report_excel.php",
		data: 'searchName='+searchName,
		dataType : 'json',
		beforeSend: function() {
			// $("#loading-modal").modal('show');
			$('.preloader').fadeIn('slow');
		},
		success: function(result){
        		// $("#loading-modal").modal('hide');
        		$('.preloader').fadeOut('slow');
        		window.location.href="<?=SITEURL?>"+result.file_path;
        	}
	});
} 
function PricelistPrint()
{
	var search =$("#searchName").val();
	var myWindow = window.open('pricelistprint.php?search='+search+"&p=1",'','width=1000,height=800');
	myWindow.print(); 
}
</script>
<script type="text/javascript">
$('.datetimepicker').datetimepicker({
  datepicker:true,
  timepicker:false,
  format: 'd-m-Y',
  minDate:'-0', 
  ampm: true 

});  
$("#old_price_list").change(function()
{
	var customer_count=$(this).find(':selected').data('customer-count') 
  	$('#customer_count').html('<strong>Total Customer : </strong> '+customer_count);
});

function changepricelist() 
{
	var old_price_list=$("#old_price_list").val();
	var new_price_list=$("#new_price_list").val();
	var applied_date=$("#applied_date").val();

	$.ajax({
		method: "POST",
		url: "change_pricelist_ajax.php",
		data: {
			old_price_list:old_price_list,
			new_price_list:new_price_list,
			applied_date:applied_date
		},
		cache: false, 
		success: function(json){
			json=$.parseJSON(json);
			msg=json.ack_msg;

			if(json.ack==1)
			{						
				toastr.success(msg,"Success!!");
				$("#old_price_list").select2('destroy');
				$("#old_price_list").val('');
				$("#old_price_list").select2();

				$("#new_price_list").select2('destroy');
				$("#new_price_list").val('');
				$("#new_price_list").select2();
				
    			$("#exampleModal").modal('hide');
    			$("#customer_count").html('');				
			}
			else
			{
				toastr.error(msg, 'Error!!')
			}
    		$("#old_price_list").val("");
			$("#new_price_list").val("");
			$("#applied_date").val("");
    	}
	});
}
</script>
</body>
</html>