<?php
   $page_id=617;$page_slug='daily_activity_report';
   $ctable 	= "orders";
   $ctable1 	= "Daily Activity Report";
   $main_page 	= $ctable;
   $page 		= "manage_".$ctable;
   $page_title = "Manage ".$ctable1." Reports";
   $page_hierarchy=array(array("link"=>"","title"=>"Reports"),array("link"=>$ctable."_manage.php","title"=>"Manage ".$ctable1));
   include("connect.php");
   $FromDate="";
   $ToDate="";
   ?>
<!DOCTYPE html>
<!--[if IE 8]> 
<html lang="en" class="ie8 no-js">
   <![endif]-->
   <!--[if IE 9]> 
   <html lang="en" class="ie9 no-js">
      <![endif]-->
      <!--[if !IE]><!-->
      <html lang="en">
         <!--<![endif]-->
         <!-- BEGIN HEAD -->
         <head>
            <meta charset="utf-8"/>
            <title><?php echo $page_title; ?> | <?php echo SITETITLE; ?></title>
            <?php include("include_css.php"); ?>
            <link rel="stylesheet" type="text/css" href="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css"/>
            <link rel="stylesheet" type="text/css" href="assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css"/>
            <link rel="stylesheet" type="text/css" href="css/fSelect.css"/>
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
                        </div>
                        <div class="col-xl-12 ">
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
                                 	<div class="slimScrollDiv" style="position: relative;width: auto; height: auto;">
                                    	<div class="row">
                                    		<div class="col-md-3 col-xs-3 col-sm-3" style="margin-top:10px;">
                                       		<div class="form-group">
                                          		<label>Select Type</label>
                                         			<select class="form-control" name="select_type" id="select_type" >
                                         				<option value="">Select Type</option>
                                         				<option value="1">By Customer</option>
                                         				<option value="2">By Sales Person</option>
                                         				<option value="3">By Raw Data/Inquiry/Lead</option>
                                         			</select>
                                      			</div>
                                       	</div>
                                       	<div class="col-md-3 col-xs-3 col-sm-3" style="margin-top:10px;">
                                          	<div class="form-group">
                                             	<label>Select User</label>
                                         			<select class="form-control status" name="ids" id="ids" >
                                         			</select>
                                         		</div>
                                       	</div>
                                       	<div class="col-md-4  col-xs-4  col-sm-4" style="margin-top:10px">
	                                          	<div class="form-inline" role="form">
	                                            		<div class="form-group">
	                                                	<label>Filter By Order Date : &nbsp;</label></br>
	                                                	<input type="text"  name="FromDate" class="form-control input-small" id="FromDate" value="<?php echo $FromDate; ?>" placeholder="From Date">
	                                             	</div>
	                                             	<div class="form-group">
	                                                	<label>&nbsp;&nbsp;</label></br>
	                                                	<input type="text"  name="ToDate" class="form-control input-small" id="ToDate" value="<?php echo $ToDate; ?>" placeholder="To Date">
	                                             	</div>
	                                             	<div class="form-group">
	                                                	<label>&nbsp;&nbsp;</label></br>
	                                                	<input class="btn btn-danger btn-sm" type="submit" value="Filter" onClick="getByDate();">
	                                             	</div>
	                                          	</div>
                                       		</div>
                                       	</div>
                                    </div>
                              	</div>
                           	</div>
                           <!-- END Portlet PORTLET-->
                        </div>
                        <div class="col-xl-12">
                           <div class="portlet light">
                              <div class="portlet-body">
                                 <div class="loading-div" style="display:none;"> <img src="assets/admin/layout/img/ajax-loader.gif" alt="" style="margin-bottom: 10%;;margin-top:10%;padding-left:48%;" > </div>
                                 <div id="results">
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <?php include("footer.php"); ?>
            <?php include("include_js.php"); ?>
            <script type="text/javascript" src="assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
            <script type="text/javascript" src="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>
            <script type="text/javascript" src="assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>

            <script type="text/javascript">
               
               var searchName="";
               var ids = "";
               var user_type = "";
               var data_url = "daily_activity_report_get_ajax.php";

            	$('#ToDate').datepicker({  datepicker: true, autoclose: true });
            	$('#FromDate').datepicker({  datepicker: true, autoclose: true });
            	var ToDate="";
            	var FromDate="";
               	
            	function searchByName()
            	{
            		searchName = $("#searchName").val();
            		type = $("#sales_executive_type").val();
            		state = $("#state").val();
            		city = $("#city").val();
            		displayRecords(100,1);
            		return false;
            	}

            	function clearSearchByName()
            	{
            		searchName = "";
               	ToDate = "";
               	FromDate = "";
               	status = "";
               	type = "";
               	city = "";
               	state = "";

               	$("#sales_executive_type").select2("val","");
               	$("#searchName").val("");
               	$("#ToDate").val("");
               	$("#FromDate").val("");
               	$("#status").select2("val","");
               	$("#type").select2("val","");
               	$("#status").select2("val","");
               	displayRecords(100,1);
            	}

            	$("#searchName").keyup(function(event){
            		if(event.keyCode == 13)
            		{
            			$("#searchByName").click();
            		}
            	});

            	$("#select_type" ).change(function() {
            		var type = $("#select_type").val();
            		if(type==1)
            		{
            			var mode = "customer";
            		}
            		else if(type==2)
            		{
            			var mode = "sales_executive";
            		}
            		else
            		{
            			var mode = "inquiry";	
            		}

					  	$.ajax({
							type: "post",
							url: "ajax_get_type_data.php",
							data: "type=" + type+"&mode="+mode,
							beforeSend: function() {
								$(".transCover").fadeIn(800);
								$('.preloader').fadeIn('slow');
							},
							success: function(result) 
							{
								setTimeout(function() 
								{
									$("#ids").select2("destroy");
									$('#ids').html(result);
									$("#ids").select2();
									$('.preloader').fadeOut('slow');
								});
							}
						})
					});

            	function getByDate()
            	{
            		if(FromDate<=ToDate)
            		{
            			ids    = $("#ids").val();
            			ToDate = $("#ToDate").val();
            			FromDate = $("#FromDate").val();
            			user_type = $("#select_type").val();
            			displayRecords(100,1);
            		}
            		else
            		{
            			alert("From Date Should Be Less Than To Date");
            		}
            	}
               
            	function loadDataTable()
            	{
            		$('#datatable_1').dataTable({
            			"bPaginate": false,
            			"bFilter": false,
            			"bInfo": false,
            			"bAutoWidth": false, 
            			"aoColumns": [
            			]
            		});
            	}
               
               function displayRecords(numRecords) {
               	var searchName 	= $("#searchName").val();
               	ids = encodeURIComponent(ids.trim());
               	$("#results" ).html("");
               	$("#results" ).load( data_url+"?show=" + numRecords + "&searchName=" + searchName + "&ToDate=" + ToDate + "&FromDate=" + FromDate + "&ids=" + ids + "&user_type=" + user_type ,function(){
               		loadDataTable();
               		// alert(type);
               	}); //load initial records
               	
               	//executes code below when user click on pagination links
               	$("#results").on( "click", ".paging_simple_numbers a", function (e){
               		e.preventDefault();
               		var numRecords  = $("#numRecords").val();
               		$(".loading-div").show(); //show loading element
               		var page = $(this).attr("data-page"); //get page number from link
               		$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName + "&type=" + type + "&flag=" + flag + "&state=" + state + "&city=" + city,{"page":page}, function(){ //get content from PHP page
               			$(".loading-div").hide(); //once done, hide loading element
               			loadDataTable();
               		});
               	});

               	$("#results").on( "change", "#numRecords", function (e){
               		e.preventDefault();
               		var numRecords  = $("#numRecords").val();
               		$(".loading-div").show(); //show loading element
               		var page = $(this).attr("data-page"); //get page number from link
               		$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName + "&type=" + type + "&flag=" + flag + "&state=" + state + "&city=" + city,{"page":page}, function(){ //		get content from PHP page
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
               	displayRecords(100,1);
               });
           	</script>
        	</body>
      </html>