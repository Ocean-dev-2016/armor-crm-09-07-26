<?php 
$page_id=555;$page_slug='page_executive';
$ctable 	= "executive_map_area";
$ctable1 	= "Class Area";
$main_page 	= $ctable;
$page 		= "manage_".$ctable;
$page_title = "Manage Class Area";
$page_hierarchy=array(array("link"=>"","title"=>"HR"),array("link"=>"executive_manage.php","title"=>"Manage Class Area City"));
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
<link rel="stylesheet" type="text/css" href="css/fSelect.css"/>
</head>
<body class="page-md">
<?php include("header.php"); ?>
<div class="page-container">
	
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				<h1><a href="<?php echo "executive_manage.php";?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php $db->pageBar($page_hierarchy);?> </h1>
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
                            Add Class And Area For "<?= $db->rp_getValue("executive","company_name","id='".$_REQUEST['executive_id']."' AND isDelete=0"); ?>"</div>
                        </div>
                        <div class="portlet-body">
                            <div class="slimScrollDiv">
								<div class="row">                                   
									<div class="col-md-4">
										<div class="form-group">
											<label> State <code>*</code></label>
											<select class="form-control class_id" name="class_id" id="class_id" >
												<option value="">-----Select State-----</option>
												<?php
													if($_REQUEST['type']==2)
													{
														$ss_id = $db->rp_getValue("executive","super_stockist_id","id='".$_REQUEST['executive_id']."' AND isDelete=0");
														$class_id = $db->rp_getData("executive_map_area","DISTINCT class_id","executive_id='".$ss_id."' AND isDelete=0","",0);
														$classids = array();
														while($class_id_d = mysqli_fetch_assoc($class_id))
														{
															$classids[] = $class_id_d['class_id'];
														}
														$classids = implode(",",$classids);
														$where.= "id IN (".$classids.") AND ";
													}
													else if($_REQUEST['type']==3)
													{

														$dd_id = $db->rp_getValue("executive","dealer_distributor_id","id='".$_REQUEST['executive_id']."' AND isDelete=0");
														$class_id = $db->rp_getData("executive_map_area","DISTINCT class_id","executive_id='".$dd_id."' AND isDelete=0");
														while($class_id_d = mysqli_fetch_assoc($class_id))
														{
															$classids[] = $class_id_d['class_id'];
														}
														$classids = implode(",",$classids);
														$where.= "id IN (".$classids.") AND ";
													}
													$class_r = $db->rp_getData("class","*",$where."isDelete=0 AND isActive=1","",0);
													if(mysqli_num_rows($class_r)>0){
														while($class_d = mysqli_fetch_array($class_r)){
														?>
														<option <?php echo (in_array($class_d['id'],$class_id))?"selected":""; ?> value="<?php echo $class_d['id']; ?>"><?php echo $class_d['name']; ?></option>
													<?php
														}
													}
													?>
											</select>
										</div>
									</div>

									<div class="col-md-4">
										<div class="form-group">
											<label for="area_id">City<code>*</code></label>
												<div class="city_html_data">
													<select class="form-control" name="city_id" id="city_id" multiple="multiple" >

													<option value="">Select City</option>
													</select>	
												</div>
											<p class="help-block"></p>
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<label for="area_id">Route<!-- <code>*</code> --></label>
												<div class="abc">
													<select class="form-control area_id" name="area_id" id="area_id" multiple="multiple" data-validation="required" data-validation-error-msg="please select class">
													<option value="">Select Route</option>
													</select>	
												</div>
											<p class="help-block"></p>
										</div>
									</div>	
									<div class="col-md-3" style="margin-top: 25px;">
										<button type="button" id="add_class_area" name="add_class_area" class="btn sbold blue-ebonyclay"><i class="fa fa-plus"></i> ADD</button>
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
<script type="text/javascript" src="js/jquery-aj.js"></script>
<script type="text/javascript" src="js/jquery.numeric.min.js"></script>
<script type="text/javascript" src="js/fSelect.js"></script> 

<script type="text/javascript">
	//$(".area_id").fSelect();
	$(".area_id").fSelect();
	$("#city_id").fSelect();
	$("#class_id").select2();
  //$("#city_id").fSelect();
</script>

<script type="text/javascript">
	// $(".class_id").on('change',function()
  //       {
  //         	var class_id=$("#class_id").val();
  //         	$.ajax({
  //             type: "POST",
  //             url: "ajax_get_area_city.php",
  //             data: 'class_id='+class_id,
  //             success: function(data){
  //               // $("#city_id").html(data);
  //               // $("#city_id").fSelect();

  //                $("#city_id").select2("destroy");
	// 		       		$("#city_id").fSelect("destroy");
	// 		        	$("#city_id").html(data);
	// 		       		$("#city_id").fSelect('create');

  //             }
  //           });
  //       });

	$(".class_id").on('change',function()
        {
          	var class_id=$("#class_id").val();
          	$.ajax({
              type: "POST",
              url: "ajax_get_area_city_all.php",
              data: 'class_id='+class_id,
              success: function(data){
                $(".city_html_data").html(data);
                // $("#city_id").select2();

                 // $("#city_id").select2("destroy");
			       		// $("#city_id").fSelect("destroy");
			        	// $("#city_id").html(data);
			       		// $("#city_id").fSelect('create');
              }
            });
        });

	// $("#city_id").on('change', function() {
	// var city = $("#city_id").val();
	// filter_city(city,"");
	// });



	$("#city_id").on('change',function()
        {
          	var city_id=$("#city_id").val();
          	$.ajax({
              type: "POST",
              url: "ajax_find_area.php",
              data: 'city_id='+city_id,
              success: function(data){
              //  $(".abc").html(data);
               // $("#area_id").fSelect();
                //$("#area_id").select2();
                	 $("#area_id").select2("destroy");
			       		$("#area_id").fSelect("destroy");
			        	$("#area_id").html(data);
			       		$("#area_id").fSelect('create');
              }
            });
        });
</script>

<script type="text/javascript">
var executive_id="<?= $_REQUEST['executive_id']?>";
var type="<?= $_REQUEST['type']?>";

$("#add_class_area").on("click",function()
{
	var class_id=$("#class_id").val();
	var city_id=$("#city_id").val();
	var area_id=$("#area_id").val();

	if(class_id!="")
	{
		$.ajax({
		type: "POST",
		url: "ajax_add_executive_class_area.php",
			data: {
				class_id:class_id,
				city_id:city_id,
				area_id:area_id,
				executive_id:executive_id,
				type:type,
				mode:"add_area",
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
					$("#class_id").select2('val',"");
					// $("#city_id").fSelect();
					// $('#area_id').select2('val',"");

					$("#city_id").fSelect("destroy");
					$("#city_id").val("");
					$("#city_id").fSelect("create");

					$("#area_id").fSelect("destroy");
					$("#area_id").val("");
					$("#area_id").fSelect("create");



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
		toastr.error("Please Select Class");
	}
});

var data_url = "class_area_get_ajax.php";

function getMember()
{	
	$.ajax({
		type: "POST",
		url: "executive_class_area_get_ajax.php",
		data: {
			executive_id:executive_id,
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
			url: "ajax_add_executive_class_area.php",
			data: {
				executive_id:executive_id,
				id:id,
				mode:"delete_class_area",
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