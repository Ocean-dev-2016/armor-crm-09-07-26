<?php 
$page_id=556;$page_slug='page_sales_executive';
$ctable 	= "target";
$ctable1 	= "Target";
$main_page 	= $ctable;
$page 		= "manage_".$ctable;
$page_title = "Manage ".$ctable1;
$page_hierarchy=array(array("link"=>"","title"=>"Master"),array("link"=>"meeting_manage.php","title"=>"Manage ".$ctable1));
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
<style type="text/css">
	.member-list{float:left;list-style:none;margin-top:-3px;padding:0;width:190px;z-index: 999}
	.member-list li{padding: 10px; background: #f0f0f0; border-bottom: #bbb9b9 1px solid;}
	.member-list li:hover{background:#ece3d2;cursor: pointer;}
</style>
<link rel="stylesheet" type="text/css" href="assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css" />
</head>
<body class="page-md">
<?php include("header.php"); ?>
<div class="page-container">
	
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				<h1><a href="<?php echo "sales_executive_manage.php";?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php $db->pageBar($page_hierarchy);?> </h1>
			</div>
		</div>
	</div>
	
	<div class="page-content">
		<div class="container">
			<div class="row">
				<div class="col-md-12"> 
				<?php $db->printErrorMessage(); ?>
				<?php $db->printSuccessMessage(); ?>
				<div class="col-md-12 "><br/>
                    <!-- BEGIN Portlet PORTLET-->
                    <div class="portlet box blue">
                       <div class="portlet-title">
                            <div class="caption">
                                <!-- <i class="fa fa-plus"></i> -->Add Target For "<?= $db->rp_getValue("sales_executive","name","id='".$_REQUEST['sales_id']."' AND isDelete=0"); ?>"</div>
                             <!-- <div class="tools">
                                <a href="javascript:;" class="collapse" data-original-title="" title=""> </a>

                            </div> -->
                        </div>
                        <div class="portlet-body">
                            <div class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto; height: auto;">
								

								<div class="row">                                   
									<div class="col-md-3">
										<label>Target Year<code>*</code></label>
										<div class="form-group">
											<input type="text" name="target_year" id="target_year" value="" class="form-control">
											<!-- <select class="form-control" name="target_year" id="target_year"></select> -->
											<div id="suggesstion-box"></div>
										</div>
									</div>	
									<div class="col-md-2">
										<label>Target Month<code>*</code></label>
										<div class="form-group">
											<!-- <input type="text" name="target_month" id="target_month" value="" class="form-control"> -->
											<select class="form-control" name="target_month" id="target_month">
												<option>- Select Month -</option>
												<option value="January">January</option>
												<option value="February">February</option>
												<option value="March">March</option>
												<option value="April">April</option>
												<option value="May">May</option>
												<option value="June">June</option>
												<option value="July">July</option>
												<option value="August">August</option>
												<option value="September">September</option>
												<option value="October">October</option>
												<option value="November">November</option>
												<option value="December">December</option>
											</select>
										</div>
									</div>
									<div class="col-md-2">
										<label>Target Amount<code>*</code></label>
										<div class="form-group">
											<input type="text" name="target_amount" id="target_amount" value="" class="form-control  nagative">
										</div>
									</div>
									<div class="col-md-2">
										<label>Target Quantity<code>*</code></label>
										<div class="form-group">
											<input type="text" name="target_quantity" id="target_quantity" value="" class="form-control  nagative">
										</div>
									</div>	
									<div class="col-md-3" style="margin-top: 25px;">
										<button type="button" id="add_target" name="add_target" class="btn sbold blue-ebonyclay"><i class="fa fa-plus"></i> ADD</button>
									</div>                               
                                </div>
                                

                            </div>
                            </div>
                    </div>
                    <!-- END Portlet PORTLET-->
                </div>
					<div class="portlet light">
						
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


<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>
<script type="text/javascript" src="assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>
<script type="text/javascript" src="js/jquery.numeric.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript">
/*$('#target_year').datetimepicker({ format: 'Y', maxDate:0, timepicker: false, });*/
$("#target_year").datepicker({
    format: "yyyy",
    viewMode: "years", 
    minViewMode: "years"
});
</script>

<script type="text/javascript">
	$(".nagative").keypress(function(event) {
		 if ( event.keyCode == 46 || event.keyCode == 8 ) {
		 // let it happen, don't do anything
		 } else if (/\D/g.test(this.value)) {
		  toastr.error("Sorry!! Only Digits Allowed");
		 this.value = this.value.replace(/\D/g, '');
		 }
		 });
</script>


<script type="text/javascript">
var sales_executive_id="<?= $_REQUEST['sales_id']?>";
$("#target_amount").numeric();


$("#add_target").on("click",function()
{
	var target_year=$("#target_year").val();
	var target_month=$("#target_month").val();
	var target_amount=$("#target_amount").val();
	var target_quantity=$("#target_quantity").val();
	if(target_year != "" && target_month != "" && target_quantity != "" || target_amount != "")
	{
		
			$.ajax({
			type: "POST",
			url: "ajax_add_target.php",
			data: {
				sales_executive_id:sales_executive_id,
				target_year:target_year,
				target_month:target_month,
				target_amount:target_amount,
				target_quantity:target_quantity,
				mode:"add_target",
			},
			cache: false,
			beforeSend: function() {
				
			},
			success: function(json)
			{
				json=$.parseJSON(json);
				msg=json.ack_msg;
				if(json.ack==1)
				{						
					toastr.success(msg,"Success!!");
					$("#target_amount").val("");
					$("#target_quantity").val("");
					getMember();	
				}
				else
				{
					toastr.error(msg, 'Error!!')
				}
			}
			});
		
	}	
	else
	{
		toastr.error("Please Enter Target Year And Month And Target Amount Or Target Quantity");
	}
});

var data_url = "target_get_ajax.php";

function getMember()
{	
	$.ajax({
		type: "POST",
		url: "target_get_ajax.php",
		data: {
			sales_executive_id:sales_executive_id,
		},
		cache: false,
		beforeSend: function() {
			
		},
		success: function(json)
		{
			$("#results").html(json);
		}
		});
}
$(document).ready(function() {
	getMember();
});
function del_conf(id)
{
	var r = confirm("Are you sure you want to delete?");
	if(r)
	{
		$.ajax({
			type: "POST",
			url: "ajax_add_target.php",
			data: {
				sales_executive_id:sales_executive_id,
				id:id,
				mode:"delete_member",
			},
			cache: false,
			beforeSend: function() {
				
			},
			success: function(json)
			{
				json=$.parseJSON(json);
				msg=json.ack_msg;
				if(json.ack==1)
				{						
					toastr.success(msg,"Success!!");
					getMember();	
				}
				else
				{
					toastr.error(msg, 'Error!!')
				}
			}
			});
	}
}


</script>
</body>
</html>