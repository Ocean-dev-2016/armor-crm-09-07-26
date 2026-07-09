<?php 
$page_id=585;$page_slug='meeting_master';
$ctable 	= "meeting";
$ctable1 	= "Members";
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
</head>
<body class="page-md">
<?php include("header.php"); ?>
<div class="page-container">
	
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				<h1><a href="<?php echo "meeting_manage.php";?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php $db->pageBar($page_hierarchy);?> </h1>
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
	                               Add Member For "<?= $db->rp_getValue("meeting","title","id='".$_REQUEST['mid']."' AND isDelete=0"); ?>" Meeting
	                           	</div>
	                        </div>
	                        <div class="portlet-body">
	                            <div class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto; height: auto;">
									<div class="row">                                   
										<div class="col-md-4">
											<label>Member Mobile No. <code>*</code></label>
											<div class="form-group">
												<input type="text" name="member_phone" id="member_phone" value="" class="form-control member-phone-search-box" maxlength="10">
												<div id="suggesstion-box"></div>
											</div>
										</div>	
										<div class="col-md-4">
											<label>Member Name <code>*</code></label>
											<div class="form-group">
												<input type="text" name="member_name" id="member_name" value="" class="form-control">
											</div>
										</div>	
										<div class="col-md-4" style="margin-top: 25px;">
											<button type="button" id="add_member" name="add_member" class="btn sbold blue-ebonyclay"><i class="fa fa-plus"></i> ADD</button>
										</div>                               
	                                </div>
	                            </div>
	                        </div>
	                    </div>
	                    <!-- END Portlet PORTLET-->
	                </div>
					<div class="portlet light">
						<div class="portlet-body">
							<div class="loading-div" style="display:none;"> <img src="assets/admin/layout/img/ajax-loader.gif" alt="" style="margin-bottom: 10%;;margin-top:10%;padding-left:48%;" > 
							</div>
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

<script type="text/javascript">
var meeting_id="<?= $_REQUEST['mid']?>";
$("#member_phone").numeric();

$("#member_phone").keyup(function()
{
	$.ajax({
		type: "POST",
		url: "autosuggetion_get_ajax.php",
		data: "keyword="+$(this).val()+"&mode=member_phone",
		beforeSend: function(){
			$(".member-phone-search-box").css("background","#FFF url(LoaderIcon.gif) no-repeat 165px");
		},
		success: function(data){
			$("#suggesstion-box").show();
			$("#suggesstion-box").html(data);
			$(".member-phone-search-box").css("background","#FFF");
		}
	});
});

function selectmember(val) {
	$(".member-phone-search-box").val(val);
	var nm=$(".member-list").find("li").data("name");
	$("#member_name").val(nm);
	$("#suggesstion-box").hide();
}

$("#add_member").on("click",function()
{
	var member_phone=$("#member_phone").val();
	var member_name=$("#member_name").val();
	if(member_phone!="" && member_phone.length==10)
	{
		if(member_name!="")
		{
			$.ajax({
			type: "POST",
			url: "ajax_add_member.php",
			data: {
				meeting_id:meeting_id,
				member_phone:member_phone,
				member_name:member_name,
				mode:"add_member",
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
					$("#member_phone").val("");
					$("#member_name").val("");
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
			toastr.error("Please Enter Name");
		}
	}	
	else
	{
		toastr.error("Please Enter valid 10 digit Mobile No.");
	}
});

var data_url = "meeting_member_get_ajax.php";

function getMember()
{	
	$.ajax({
		type: "POST",
		url: "meeting_member_get_ajax.php",
		data: {
			meeting_id:meeting_id,
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
			url: "ajax_add_member.php",
			data: {
				meeting_id:meeting_id,
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