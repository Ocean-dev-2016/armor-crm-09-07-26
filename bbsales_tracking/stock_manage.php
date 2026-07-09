<?php
$page_id=638;$page_slug='product_stock_page';
$ctable 	= "Product"; 
$ctable2 	= "product";

$ctable1 	= "Stock";
$main_page 	= $ctable;
$page 		= "manage_".$ctable;
$page_title = "Product Stock";
$page_hierarchy=array(array("link"=>"","title"=>"Utility"),array("link"=>"stock_manage.php","title"=>$page_title ));
$se_id=isset($_REQUEST['id'])?$_REQUEST['id']:"";
if(!isset($_REQUEST['id']))
{
	$redirect="dashboard.php";
}
else
{
	$redirect="sales_executive_manage.php";
}
include("connect.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<title><?php echo $page_title; ?> | <?php echo SITETITLE; ?></title>
<?php include("include_css.php"); ?>

<link rel="stylesheet" type="text/css" href="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css"/>
<link rel="stylesheet" type="text/css" href="assets/global/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css"/>
<link rel="stylesheet" href="<?=ADMINSITEURL?>css/lightbox.css" />
</head>
<body class="page-md">
<?php include("header.php"); ?>
<div class="page-container">
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				<h1><a href="<?php echo $redirect;?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php $db->pageBar($page_hierarchy);?> </h1>
			</div>
		</div>
	</div>
	
	<div class="page-content">
		<div class="container">
			<div class="row">
				<div class="col-xl-12">
				<?php $db->printErrorMessage(); ?>
				<?php $db->printSuccessMessage(); ?>
				</div>
				<div class="col-xl-12">
                    <!-- BEGIN Portlet PORTLET-->
                    <div class="portlet box blue">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-filter"></i>Filters 
                            </div>
                            <div class="tools">
                            	<a href="javascript:;" class="collapse" data-original-title="" title=""> </a>
							</div>
                        </div>
                        <div class="portlet-body">
                            <div class="slimScrollDiv" style="position: relative; /*overflow: hidden;*/ width: auto; height: auto;">
								<div class="row filter_list">
									<div class="col-md-1 col-xs-1 col-sm-1" style="margin-top:10px">
									 	<?php echo $db->getAddButton($ctable2);?>	
									</div>
									<div class="col-md-2">
										<div class="form-group">
											<label>Search By Warehouse </label>
											<select class="form-control" name="warehouse_id" id="warehouse_id">
												<option value="">Select Warehouse</option>
												<?php
												$WarehouseR=$db->rp_getData('warehouse',"*","isDelete=0","",0);
												while($WarehouseD=mysqli_fetch_assoc($WarehouseR))
												{
												?>
												<option <?=($warehouse_id == $WarehouseD['id'])?"selected":"";?> value="<?php echo $WarehouseD['id']; ?>">
												<?php echo $WarehouseD['name']; ?>
												</option>
												<?php
												}
												?>
											</select> 
										</div>
									</div>
									<div class="col-md-2 col-xs-2 col-sm-2">
										<label>Search By Category</label><br/>
								  		<div class="form-group" role="form">
											<select class="form-control" name="category_id" id="category_id" onchange="getSubcat(this.value)">
						                		<option value="">Select Category</option>
						                		<option value="-1">All</option>
						                		<?php
						                			$TopcatR = $db->rp_getData("top_category_master","id,name","","",0);
						                			while ($TopcatD = mysqli_fetch_assoc($TopcatR))
						                			{
					                				?>
					                				<option value="<?=$TopcatD['id']?>"><?=$TopcatD['name']?></option>
					                				<?php
						                			}
						                		?>
						                	</select>
								        </div>
                                    </div>
                                    <div class="col-md-2 col-xs-2 col-sm-2">
										<label>Search By Sub Category</label><br/>
								  		<div class="form-group" role="form">
											<select class="form-control" name="sub_category_id" id="sub_category_id" onchange="getProduct(this.value)">
						                		<option value="">Select Sub Category</option>
						                		<!-- <option value="-2">All</option> -->
						                	</select>
								        </div>
                                    </div>
                                    <div class="col-md-2 col-xs-2 col-sm-2">
										<label>Search By Item Name</label><br/>
								  		<div class="form-group" role="form">
											<select class="form-control" name="item_name" id="item_name">
						                		<option value="">Item Name</option>
						                		
						                	</select>
								        </div>
                                    </div>
                                    <div class="col-md-4 col-xs-4 col-sm-4 pull-right " style="margin-top:10px">
                                    	<form class="form-inline pull-right" role="form" onSubmit="return searchByName();">
											<div class="form-group">
												<input type="text" style="width: 250px!important" placeholder="Search By Item Name/Item Code :  " class="form-control input-large" name="searchName" id="searchName" value="" />
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
                                                            <a name="print" onClick="genstockPrint()" title="Print Report"><i class="fa fa-print"></i>Print</a>
                                                        </li>
                                                        <?php
                                                      }
                                                        ?>
                                                     <?php
																								if($rights['export_excel_flag']==1 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
																								{
																									?>
                                                        <!-- <li>
                                                            <a class="excel" name="excel" onClick="genReport()" id="excel" title="Download XL Report"><i class="fa fa-file-excel-o"></i>Excel</a>
                                                        </li> -->
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
                <!-- END Portlet PORTLET-->
            </div>
		</div>
		<div class="portlet light">
			<div class="table-toolbar">
				<div class="row">
					<div class="col-md-6">
						<?php
							//echo $db->getAddButton($ctable);
						?>	
					</div>
				</div>
			</div>
			<div class="portlet-body">
				<div class="loading-div" style="display:none;"> <img src="assets/admin/layout/img/ajax-loader.gif" alt="" style="margin-bottom: 10%;;margin-top:10%;padding-left:48%;" > 
				</div>
				<div class="table-responsive">
					<div id="results"></div>
				</div>
			</div>
		</div>
	</div>
</div>
</div>
<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>
<script src="<?=ADMINSITEURL?>js/lightbox.js"></script>
<script type="text/javascript" src="assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-daterangepicker/moment.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
<script type="text/javascript">
var se_id="<?= $se_id; ?>";
var searchName="";
var warehouse_id=""; 
var item_name = "";
var category_id = "";
var sub_category_id = "";
var data_url = "stock_get_ajax.php";
  
function searchByName(){ 	 
	displayRecords();
	return false;
}
 
function clearSearchByName(){
	searchName = "";
	warehouse_id = ""; 
	item_name = "";
	category_id = "";
	sub_category_id = ""; 
	$("#searchName").val(""); 
	$("#item_name").select2("val","");
	$("#warehouse_id").select2("val","");
	$("#category_id").select2('destroy');
	$("#category_id").val(category_id);
	$("#category_id").select2();
	// $("#sub_category_id").select2("val","");
	$("#sub_category_id").select2('destroy');
	$("#sub_category_id").val(sub_category_id);
	$("#sub_category_id").select2();
	 
	displayRecords();
}
$("#searchName").keyup(function(event){
	if(event.keyCode == 13){
		$("#searchByName").click();
	}
});
function loadDataTable(){
	$('#datatable_1').dataTable({
		"bPaginate": false,
		"bFilter": false,
		"bInfo": false,
		"bAutoWidth": false, 
		"aoColumns": [
			  { "sWidth": "10%" }, 
			  { "sWidth": "10%" }, 
			  { "sWidth": "10%" },
			  { "sWidth": "10%" },
			  { "sWidth": "10%" },
			  { "sWidth": "10%" },
			  { "sWidth": "10%" },
			]
	});
}
function displayRecords() {
	 
	var searchName 	= $("#searchName").val();
	searchName 	= encodeURIComponent(searchName.trim());
	var item_name 	= $("#item_name").val();
	item_name 	= encodeURIComponent(item_name.trim());
	var category_id 	= $("#category_id").val();
	category_id 	= encodeURIComponent(category_id.trim());
	var sub_category_id 	= $("#sub_category_id").val();
	sub_category_id 	= encodeURIComponent(sub_category_id.trim());
	var warehouse_id 	= $("#warehouse_id").val();
	$("#results" ).html("");
	$("#results" ).load( data_url+"?searchName=" + searchName + "&warehouse_id=" + warehouse_id + "&se_id=" + se_id +"&item_name=" + item_name +"&category_id=" + category_id+"&sub_category_id=" + sub_category_id,function(){
		loadDataTable();
	}); //load initial records
}  

$(document).ready(function() {
	displayRecords();
});
</script>

<script type="text/javascript"> 
  function genReport()
	{
		var searchName     = $("#searchName").val();
      	searchName 	= encodeURIComponent(searchName.trim());
		var item_name 	= $("#item_name").val();
		item_name 	= encodeURIComponent(item_name.trim());
		var category_id 	= $("#category_id").val();
		category_id 	= encodeURIComponent(category_id.trim());
		var sub_category_id 	= $("#sub_category_id").val();
		sub_category_id 	= encodeURIComponent(sub_category_id.trim());
		var warehouse_id 	= $("#warehouse_id").val();
 
      	$.ajax({
			method: "POST",
			url: "product_stock_excel_ajax.php",
			data:{
        		searchName:searchName,
        		sub_category_id:sub_category_id,
        		category_id:category_id,
        		item_name:item_name,		
        		warehouse_id:warehouse_id,		
			},
			dataType : 'json',
			beforeSend: function() {
				// $("#loading-modal").modal('show');
				$('.preloader').fadeIn('slow');
			},
			success: function(result){
	        		// $("#loading-modal").modal('hide');
        		$('.preloader').fadeOut('slow');
        		window.location.href="<?=SITEURL?>"+result.file_path;
        	},
		});

    } 
	function genstockPrint(){
		var searchName     = $("#searchName").val();
      	searchName 	= encodeURIComponent(searchName.trim());
		var item_name 	= $("#item_name").val();
		item_name 	= encodeURIComponent(item_name.trim());
		var category_id 	= $("#category_id").val();
		category_id 	= encodeURIComponent(category_id.trim());
		var sub_category_id 	= $("#sub_category_id").val();
		sub_category_id 	= encodeURIComponent(sub_category_id.trim());
		var warehouse_id 	= $("#warehouse_id").val();

     	var myWindow = window.open("print_stock_ajax.php?searchName=" + searchName + "&warehouse_id=" + warehouse_id + "&se_id=" + se_id +"&item_name=" + item_name +"&category_id=" + category_id+"&sub_category_id=" + sub_category_id ,'','width=700,height=800');
     	 myWindow.print(); 
    }
   function getSubcat(val)
    {
	    $.ajax({
	       type:"POST",
	       url:"get_stock_subcat_ajax.php",
	       data:'id='+val,
	       success:function(data){
	          $("#sub_category_id").html(data);
	          // alert("state selected successfuly !!!");
	       }
	    })
 	}

 	function getProduct(val)
    {
	    $.ajax({
	       type:"POST",
	       url:"get_stock_product_ajax.php",
	       data:'sub_cat_id='+val,
	       success:function(data){
	          $("#item_name").html(data);
	          // alert("state selected successfuly !!!");
	       }
	    })
 	}
</script>
</body>
</html>