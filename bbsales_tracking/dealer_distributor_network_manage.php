<?php
$page_id=406;$page_slug='page_admin';
$ctable 	= "dealer_distributor_network";
$ctable1 	= "Dealer Distributor Network";
$main_page 	= "product_mgmt";
$page 		= "manage_".$ctable;
$page_title = "System User";
$page_hierarchy=array(array("link"=>"","title"=>"Utility"),array("link"=>$ctable."_manage.php","title"=>$page_title));
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
			<?php if(isset($_REQUEST['msg']) && $_REQUEST['msg']!=""){ ?>
				<div class="alert alert-success alert-dismissable"> <i class="fa fa-check"></i>
					<button class="close" aria-hidden="true" data-dismiss="alert" type="button">×</button>
					<strong>Success! </strong>
					<?php
						if($_REQUEST['msg']=="inserted"){
							echo $ctable1." detail Added successfully.";
						}else if($_REQUEST['msg']=="updated"){
							echo $ctable1." detail Updated successfully.";
						}else if($_REQUEST['msg']=="deleted"){
							echo $ctable1." Deleted successfully.";
						}
					?>
				</div>
			<?php } ?>
			<div class="row">
				<div class="col-xl-12">
					<div class="portlet box blue">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-filter"></i>Filters </div>
                             <div class="tools">
                                <a href="javascript:;" class="collapse" data-original-title="" title=""> </a>

                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="slimScrollDiv" style="position: relative; /*overflow: hidden;*/ width: auto; height: auto;">
								<div class="row">

									    <div class="col-md-5 col-xs-5 col-sm-5">
									 	<?php
										echo $db->getAddButton($ctable);
									      ?>	

									     </div>

                                 	<div class="col-md-7 col-xs-7 col-sm-7" style="margin-top:10px">
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
                                                        <!-- <li>
                                                            <a onClick="Importexcel(this)" data-toggle="modal" data-target="#uploadLeeds"><i class="fa fa-download"></i>Import</a>
                                                        </li> -->
                                                        <li>
                                                            <a name="print" onClick="genSalesExecutivePrint()" title="Print Report"><i class="fa fa-print"></i>Print</a>
                                                        </li>
                                                        <li>
                                                            <a class="excel" name="excel" onClick="genSalesExecutiveExcel()" id="excel" title="Download XL Report"><i class="fa fa-file-excel-o"></i>Excel</a>
                                                        </li>
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
				<div class="portlet light">
							<div class="table-toolbar">
								<div class="row">
									<!-- <div class="col-md-6">
										<?php
											echo $db->getAddButton($ctable);
										?>	
									</div> -->
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
<!-- password model -->
<div id="changePasswordModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="changePasswordModal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
				<h4 class="modal-title">Change Password</h4>
			</div>
			<form action="#" id="changePasswordForm">
				<div class="modal-body">						
					<div class="form-body row">
						<div class="form-group col-sm-6">
							<label>New Password</label>
							<input type="password" name="nPassword" id="nPassword" class="form-control" value="" placeholder="New Password">								
							<p class="help-block text-danger"></p>
						</div>
						<div class="form-group col-sm-6">
							<label>Re-type new Password</label>
							<input type="password" name="nRPassword" id="nRPassword" class="form-control" value="" placeholder="Re-type New Password">
							<p class="help-block text-danger"></p>
							<input type="hidden" name="userId" id="userId" class="form-control" value="">
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<input class="btn btn-success" type="submit" value="Update password">
				</div>
			</form>
		</div>
	</div>
</div>
<!-- password model -->
<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>
<script type="text/javascript" src="assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>
<script>
	$(function(){
		$('#changePasswordModal').on('shown.bs.modal', function (event) {
		   var button = $(event.relatedTarget) // Button that triggered the modal
		  var userId = button.data('id') 	
		  var modal = $(this)
		  modal.find('input[type=hidden][name=userId]').val(userId);
		});

		$('#changePasswordModal').on('hidden.bs.modal', function (event) {			   
		var modal = $(this)
		modal.find('input[type=hidden][name=userId]').val("");
		  modal.find('input[name=nRPassword]').val("");
		  modal.find('input[name=nPassword]').val("");
		  modal.find('p.help-block').html("");
		});
			
		$('#changePasswordForm').on('submit',function(e)
		{
			var error=0;
			e.preventDefault();
			if($('#nPassword').val()=="" )
			{
				// error=1;
				// $('#nPassword').parent('div.form-group').find('p.help-block').html("*Please Enter Password");
			}else{
				error=0;
				$('#nPassword').parent('div.form-group').find('p.help-block').html("");
			}
			if($('#nRPassword').val()=="" || $('#nPassword').val()!=$('#nRPassword').val())
			{
				error=1;
				$('#nRPassword').parent('div.form-group').find('p.help-block').html("*It Must be match with password field !!");
			}
			else{
				error=0;
				$('#nRPassword').parent('div.form-group').find('p.help-block').html("");
			}
			if($('#userId').val()=="")
			{
				error=1;
				alert('Internal Error Please Try Again !!');
				$('#changePasswordModal').modal('hide');
			}
			if(error==0)
			{
				var nPassword=$('#nPassword').val();
				var userId=$('#userId').val();					
				$.ajax({
				  type: "POST",
				  url: "change_password_system_user.php",
				  data: {nPassword:nPassword,userId:userId},						
				  success: function(data){
					  var json_obj=$.parseJSON(data);
					  if(json_obj['data']['ack']==1)
					  {
						
						$('#alert-msg').find('p').html(json_obj['data']['ack_msg']);
						$('#alert-msg').show();								
						$('#changePasswordModal').modal('hide');
						// displayRecords();
						// displayRecords(10,1);
						callAjax();
					  }
					  else
					  {
						$('#alert-msg').find('p').html(json_obj['data']['ack_msg']);
						$('#alert-msg').show();								
														 
						 $('#changePasswordModal').modal('hide');
						
					  }
				  }						 
				});
			}
		});
	});
</script>

<script type="text/javascript">
var searchName="";
var data_url = "dealer_distributor_network_get_ajax.php";
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
		"bFilter": false,
		"bInfo": false,
		"bAutoWidth": false, 
		"aoColumns": [
			  { "sWidth": "2%" }, 
			  { "sWidth": "5%" },
			  { "sWidth": "20%" },
			  { "sWidth": "20%" },
			  { "sWidth": "20%" },
			  
			  { "sWidth": "23%","bSortable": false }
			]
	});
}
function displayRecords(numRecords) {
	var searchName 	= $("#searchName").val();
	searchName 	= encodeURIComponent(searchName.trim());
	$("#results" ).html("");
	$("#results" ).load( data_url+"?show=" + numRecords + "&searchName=" + searchName,function(){
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

 function genSalesExecutiveExcel()
     {
        var searchName     = $("#searchName").val();
        searchName         = searchName.trim();
        // searchName          = encodeURIComponent(searchName.trim());
        var state          = $("#state").val();
        var city          = $("#city").val();
        var type          = $("#sales_executive_type").val();
        $.ajax({
            method: "POST",
            url: "dealer_distributer_excel.php",
            data:{
                searchName:searchName,
                state:state,
                city:city,
                type:type
            },  
            dataType : 'json',
            beforeSend: function()
            {
                // $("#loading-modal").modal('show');
                $('.preloader').fadeIn('slow');
            },
            success: function(result){
                // $("#loading-modal").modal('hide');
                $('.preloader').fadeOut('slow');
                window.location.href="<?=SITEURL?>"+result.file_path;
            },
            /*error:function(result){
                window.location.href="<?=SITEURL?>"+result.file_path;
            }*/
        });
    }

    function genSalesExecutivePrint()
    {

        var searchName     = $("#searchName").val();
        searchName         = encodeURIComponent(searchName.trim());
        var state          = $("#state").val();
        var city          = $("#city").val();
        var type          = $("#sales_executive_type").val();

        var myWindow = window.open('print_dealer_distributer_ajax.php?searchName='+searchName+ "&type=" + type + "&state=" + state + "&city=" + city ,'','width=700,height=800');
        myWindow.print();
        // setTimeout(function () 
        // {
        //     myWindow.print();
        //     var ival = setInterval(function() 
        //     {
        //         myWindow.close();
        //         clearInterval(ival);
        //     }, 200);
        // }, 500);
     }


// used when user change row limit
function changeDisplayRowCount(numRecords) {
	displayRecords(numRecords, 1);
}

$(document).ready(function() {
	displayRecords(500,1);
});
</script>
<script type="text/javascript">
function del_conf(id){
	var r = confirm("Are you sure you want to delete?");
	if(r){
		window.location.href='<?php echo $ctable; ?>_crud.php?mode=delete&id='+id;
	}
}
</script>
<script>
$(document).ready(function() {       
   $('#datatable_1').dataTable();
});
</script>
</body>
</html>