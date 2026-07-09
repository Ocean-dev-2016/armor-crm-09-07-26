<?php
$page_id=636;$page_slug='outstanding_report';
$ctable 	= "customer_account";
$ctable_v 	= "account";
$ctable1 	= "Employee Outstanding Report";
$main_page 	= "product_mgmt";
$page 		= "manage_".$ctable;
$page_title = "Manage ".$ctable1;
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
<link rel="stylesheet" type="text/css" href="assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css"/>
</head>
<body class="page-md">
<?php include("header.php"); ?>
<div class="page-container">
    <div class="page-head bg-grey">
        <div class="container">
            <div class="page-title">
                <h1><a href="<?php echo "dashboard.php";?>" class="btn primary"><i class="fa  fa-arrow-circle-o-left"></i>&nbsp;back</a> &nbsp;<?php echo $page_title; ?></h1>
            </div>
        </div>
    </div>
    <div class="page-content">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
	                <?php $db->printErrorMessage(); ?>
	                <?php $db->printSuccessMessage(); ?>
					<div class="alert alert-success alert-dismissable " id="alert-msg" style="display:none;margin-top:15px">
						<button class="close" aria-hidden="true" data-dismiss="alert" type="button">×</button>
						<p></p>
					</div>
                </div>
                <div class="col-md-12 ">
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
				         <div class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto; height: auto;">
				            <div class="row">
				               <div class="col-md-4  col-xs-4  col-sm-4" style="margin-top:10px;">
				                  <div class="form-inline" role="form">
				                     <div class="form-group">
				                        <select class="form-control" onChange="FindCustomer()" id="sales_id" style="width:400px;">
				                           <option value="">--Select Employee--</option>
				                            <!-- <?php 
											$acc_r=$db->rp_getData('executive',"*","isDelete=0","",0);
											while($acc_d=mysqli_fetch_assoc($acc_r))
											{
											?>
											<option <?php echo ($account_id==$acc_d['id'])?"selected":"" ; ?> value="<?php echo $acc_d['id']?>"><?php echo $acc_d['company_name']." - ".$acc_d['cname']." - ".$db->rp_getValue("sales_executive","name","id=".$acc_d['seid'].""); ?></option>
											<?php
											}
											?> -->

											<?php 
											$acc_r=$db->rp_getData('sales_executive',"*","isDelete=0","",0);
											while($acc_d=mysqli_fetch_assoc($acc_r))
											{
											?>
											<option <?php echo ($account_id==$acc_d['id'])?"selected":"" ; ?> value="<?php echo $acc_d['id']?>"><?php echo $acc_d['name']." - ".$acc_d['username']; ?></option>
											<?php
											}
											?> 
				                        </select>
				                     </div>
				                     <h5 style="font-weight: 700;color: red">*note : Employee name - Employee username</h5>
				                  </div>
				               </div>
				               <div hidden class="col-md-6  col-xs-6  col-sm-6" style="margin-top:10px">
				                  <div class="form-inline" role="form">
				                     <div class="form-group">
				                        <label>Filter By Created Date : &nbsp;</label>
				                        <input type="text"  name="FromDate" class="form-control input-small" id="FromDate" value="<?php echo $FromDate; ?>" placeholder="From Date">
				                     </div>
				                     <div class="form-group">
				                        <label>&nbsp;&nbsp;</label>
				                        <input type="text"  name="ToDate" class="form-control input-small" id="ToDate" value="<?php echo $ToDate; ?>" placeholder="To Date">
				                     </div>
				                     <div class="form-group">
				                        <input class="btn btn-danger btn-sm" type="submit" value="Filter" onClick="getByDate();">
				                     </div>
				                  </div>
				               </div>
				               <div class="col-md-6 pull-right" style="margin-top:10px">
				               </div>
				            </div>
				         </div>
				      </div>
				   </div>
				   <!-- END Portlet PORTLET-->
				</div>
                <div class="col-md-12">
                    <div class="portlet light">
                        <div class="table-toolbar">
                            <div class="row"></div>
                        </div>
                        <div class="portlet-body">
                            <div class="loading-div" style="display:none;"> <img src="assets/admin/layout/img/ajax-loader.gif" alt="" style="margin-bottom: 10%;;margin-top:10%;padding-left:48%;" > </div>
                            <div id="results"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="loading" class="modal fade" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body portlet box white">
				<div class="portlet-title" style="color:black;">
					<div class="caption">Loading.......
					<img src="../images/loading-spinner-blue.gif">
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
$("#sales_id").select2();
$("#account_customer_id").select2();
$("#negotiation_customer_id").select2();
var searchName="";
var ToDate="";
var FromDate="";
var cid="";
$('#ToDate').datepicker({  datepicker: true, autoclose: true });
$('#FromDate').datepicker({  datepicker: true, autoclose: true });

var data_url = "employee_outstanding_report_get_ajax.php";

function FindCustomer(){
	sales_id = $("#sales_id").val();
	displayRecords(100,1);
	return false;
}
function clearSearchByName(){
	searchName = "";
	ToDate="";
	FromDate="";
	sales_id = "";
	
	$("#sales_id").val("");
	$("#FromDate").val("");
	$("#ToDate").val("");
	displayRecords(100,1);
}
function getByDate() {
	if($("#FromDate").val() != '' && $("#ToDate").val() != '' ){
		ToDate = $("#ToDate").val();
		FromDate = $("#FromDate").val();
		displayRecords(100,1);
	}
	else
	{
		alert("Please Select Date");
	}
	
}
function loadDataTable(){
	$('#datatable_1').dataTable({
		"bPaginate": false,
		"order":['desc'],
		"bFilter": false,
		"bInfo": false,
		"bAutoWidth": false, 
		"aoColumns": [
			  { "sWidth": "5%" }, 
			  { "sWidth": "30%" },
			  { "sWidth": "30%" },
			  { "sWidth": "30%" },
			  { "sWidth": "30%" },
			]
	});
}
function displayRecords(numRecords) {
	$("#results" ).html("");
	$("#results" ).load( data_url+"?show=" + numRecords + "&searchName=" + searchName + "&ToDate=" + ToDate + "&FromDate=" + FromDate+ "&sales_id=" + sales_id,function(){
		loadDataTable();
	}); //load initial records
	
	//executes code below when user click on pagination links
	$("#results").on( "click", ".paging_simple_numbers a", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName + "&ToDate=" + ToDate + "&FromDate=" + FromDate + "&sales_id=" + sales_id,{"page":page}, function(){ //get content from PHP page
			$(".loading-div").hide(); //once done, hide loading element
			loadDataTable();
		});
		
	});
	$("#results").on( "change", "#numRecords", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName + "&ToDate=" + ToDate + "&FromDate=" + FromDate + "&sales_id=" + sales_id,{"page":page}, function(){ //get content from PHP page
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
<script type="text/javascript">
function del_conf(id){
	var r = confirm("Are you sure you want to delete?");
	if(r){
		window.location.href='challan_crud.php?mode=delete&id='+id;
	}
}
</script>
<script>
$(document).ready(function() {       
   $('#datatable_1').dataTable();
});

//var outstanding_ids = [];
// $(document).on('click', '#send_mail', function(e){
// 	var id = $("#mail_ids").val();
// 	//alert(id);
// 	OutstandingSendMail(id);
// });
function OutstandingSendMail() 
{
    var cid  = $("#sales_id").val();
  	// searchName     	= encodeURIComponent(searchName.trim());
    $.ajax({
        type: "POST",
        url: "outstanding_generate_email.php",
        data: {
            cid: cid
        },
        //dataType : 'json',	
       	beforeSend: function() {
			$("#loading-modal").modal('show');
		},
		success: function(result){ 
			//alert(data);
			//var json_obj=$.parseJSON(data);
			// if(json_obj['data']['ack']==1)
			// {
			// 	//alert(data);
				 //$('#alert-msg').find('p').html(json_obj['data']['ack_msg']);
				// $('#alert-msg').show();	

				toastr.success("Mail Sent Successfully");
				// toastr.success(json_obj['ack_msg']);
				$("#loading-modal").modal('hide');
				callAjax();
				//window.location.href=result.file_path;
				//window.location.href="<?=SITEURL?>"+result.file_path;
			
		},
    });
}
// function OutstandingSendMail(outstanding_ids)
// {
// 	// var id = $("#id").val();
// 	// var to_email = $("#to_email").val();
// 	// var cc_email = $("#cc_email").val();
// 	// var mail_type = $("#mail_type").val();
// 	// var description = CKEDITOR.instances['mail_description'].getData();
// 	//var outstanding_ids = outstanding_ids[];
// 	var account_id = outstanding_ids;
// 	//outstanding_ids.push('apple');
// 	 //alert(account_id);
// 	$.ajax({
// 		type: "POST",
// 		url: "outstanding_generate_email.php",
// 		data: {
// 			account_id: account_id,
// 			// to_email: to_email,
// 			// cc_email: cc_email,
// 			// mail_type: mail_type,
// 			// description: description,
// 		},

// 		beforeSend: function() {
// 			$(".transCover").fadeIn(800);
// 		},
// 		success: function(result) 
// 		{
// 			var result = $.parseJSON(result);
// 			//var acc_id =  $(this).val();
// 			//outstanding_ids.push({"id" : acc_id});
			
// 			if (result.ack == 1)
// 			{ 
// 				$(".transCover").fadeOut(100);
// 				toastr.success(result.ack_msg);
// 			} 
// 			else 
// 			{
// 				toastr.error(result.ack_msg);
// 			}
// 		}
// 	})
// }
// function genReportPdf(){
// 	var rc = encodeURIComponent($("#print_info").html());
// 	$.ajax({
// 		type: "POST",
// 		url: "outstanding_report_geReport.php",
// 		data: 'rc='+rc,
// 		beforeSend: function() {
// 			$(".transCover").fadeIn(800);
// 			$("#loading").modal('show');
// 		},
// 		success: function(result){ 
// 				setTimeout(function(){
// 					$(".transCover").fadeOut(100);
// 					$("#loading").modal('hide');
// 					//window.location.href=result;
// 					window.open(result, '_blank');
// 				},1500);
// 			}
// 	});
// }
// function genReportExcel()
// {
// 	//var rc = encodeURIComponent($("#print_info").html());
// 	$.ajax({
// 		type: "POST",
// 		url: "customer_accout_gen_ajax.php",
// 		//data: '&rc='+rc,
// 		beforeSend: function() {
// 			// $(".transCover").fadeIn(800);
// 			$("#loading").modal('show');
// 		},
// 		success: function(result){ //alert(result);
// 				setTimeout(function(){
// 					$(".transCover").fadeOut(100);
// 					//alert("Report file generated!!");
// 					$("#loading").modal('hide');
// 					window.location.href="<?=SITEURL?>"+result.file_path;
					
// 				},1500);
// 			}
// 	});
// }

	function SendSms(id)
	{
		var sales_id = id;
		$.ajax({
		url:"outstanding_send_sms_ajax.php",
		type:"POST",
		data: "sales_id="+sales_id,
		beforeSend:function(){
				$("#send_sms_btn").html("Sending..");
				$("#send_sms_btn").attr("disabled","disabled");
				$("#loading-modal").modal('show');
			},
		success:function(result){
			
				var result=$.parseJSON(result);
				
				if(result.ack==1)
				{
					
					$("#loading-modal").modal('hide');
					$("#send_sms_btn").html("Send Sms");
					$("#send_sms_btn").removeAttr("disabled");
					toastr.success("SMS sent successfully");
					
				}
				else
				{
					toastr.error("SMS Not sent Please try Again Later");
					//$("#loading-modal").modal('hide');
				}
			}
		})
	}

</script>



<script type="text/javascript">
	

	// function genReportExcel(){
		// var searchName     = $("#searchName").val();
		// var sales_executive = $("#sales_executive").val();
		// var customer_id = $("#customer_id").val();
		// var df1 = $("#material_request_filter_input").val();
      	// searchName     = encodeURIComponent(searchName.trim());
      	// window.location.href='visit_genReport_ajax.php?searchName='+searchName+'&sales_executive='+sales_executive+'&customer_id='+customer_id+'&df='+df1;
      	// $.ajax({
	      //   method: "POST",
	      //   url: "customer_accout_gen_ajax.php",
	  //       data:{
   //      		searchName:searchName,
			// 	sales_executive:String(sales_executive),
			// 	customer_id:String(customer_id),
			// 	df1:df1,
			// },	
			// dataType : 'json',
			// beforeSend: function()
			// {
			// 	$("#loading-modal").modal('show');
			// },
   //      	success: function(result){
   //      		$("#loading-modal").modal('hide');
   //      		window.location.href="<?=SITEURL?>"+result.file_path;
   //      	},
			/*error:function(result){
				window.location.href="<?=SITEURL?>"+result.file_path;
			}*/
    // 	});
    // }
</script>

<script type="text/javascript">
	
	function genReportPdf() {
        var sales_id  = $("#sales_id").val();
      	// searchName     	= encodeURIComponent(searchName.trim());

        $.ajax({
            type: "POST",
            url: "outstanding_report_geReport.php",
            data: {
                sales_id: sales_id
            },
            dataType : 'json',	
           	beforeSend: function() {
				$("#loading-modal").modal('show');
			},
			success: function(result){ 
				$("#loading-modal").modal('hide');
				window.location.href=result.file_path;
			},
        });
    }	
</script>


<script type="text/javascript">
	function genReportExcel(){
	// var show = $("#numRecords").val();
	// state = $("#state").val();
	// city_id = $("#city").val();
	// status = $("#status_id").val();
	// df1 = $("#material_request_filter_input").val();
	// alert(status);
	var sales_id  = $("#sales_id").val();
	$.ajax({
		type: "POST",
		url: "outstanding_report_excel_gen_ajax.php",
		data: {
                sales_id: sales_id
            },
		// data: '&status_id='+status_id+'&searchName='+searchName+'&type='+type +"&state=" + state +"&city_id=" + city_id +"&show=" + show +"&status=" + status +"&df1=" + df1,
		beforeSend: function() {
			$(".transCover").fadeIn(800);
			$("#loading").modal('show');
		},
		success: function(result){ //alert(result);
				setTimeout(function(){
					$(".transCover").fadeOut(100);
					$("#loading").modal('hide');
					window.location.href=result;
					
				},1500);
			}
	});
}


</script>
</body>
</html>