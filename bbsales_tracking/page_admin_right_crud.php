<?php
$page_id=404;$page_slug='manage_admin_type';
$ctable 	= "page_admin_right";
$ctable1 	= "Right to Admin";
$main_page 	= "product_mgmt";
$page 		= "manage_page_admin_right";
$page_title = "Add ".$ctable1;

include("connect.php");
include("../include/menu_permission_label.php");
$tids = "5895,".implode(",",$comman_pages).",";
if(!isset($_REQUEST['pid']) || $_REQUEST['pid']==''){
	$db->rp_location("admin_type_manage.php");
}
$pid=$_REQUEST['pid'];
if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="delete"){
	$rows 	= array(
				"isDelete"	=> "1"
			);
	$where	= "id='".$_REQUEST['id']."'";
	$db->rp_delete($ctable,$where);
	$db->rp_location($ctable."_manage.php?msg=deleted&pid=".$_REQUEST['pid']);
}
$pctable_r = $db->rp_getData($ctable,"*","admin_id='".$_REQUEST['pid']."' AND isDelete=0","",0);
if(mysqli_num_rows($pctable_r)>0){
	while($pctable_d = mysqli_fetch_array($pctable_r)){
		$tids .= $pctable_d['page_id'].",";
	}	
}$tids = trim($tids,",");


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
<link href="js/toastr.css" rel="stylesheet" type="text/css" />
<style type="text/css">
	.tableFixHead {
        overflow: auto;
        max-height: 70vh;
    }
    .tableFixHead thead th {
        position: sticky;
        top: 0;
        background: #e5e5e5;
        z-index: 2;
    }
    table {
        border-collapse: collapse;
        width: 100%;
    }
    th,td {
        padding: 8px 10px;
        border: 1px solid #ccc;
    }
    .perm-section td {
        background: #c62828;
        color: #fff;
        font-weight: bold;
    }
    .perm-sub, .perm-alias {
        font-size: 12px;
        color: #666;
        margin-top: 3px;
    }
    .perm-search {
        margin: 10px 0;
        max-width: 320px;
    }
</style>
</head>
<body class="page-md">
<?php include("header.php"); ?>
<div class="page-container">
	
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				<h1><a href="<?php echo $ctable."_manage.php?pid=".$pid;?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php echo $page_title; ?></h1>
			</div>
		</div>
	</div>
	
	<div class="page-content">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<div class="portlet light">
						<div class="table-toolbar">							
						</div >
						<div class="portlet-body">
							
							<div class="loading-div" style="display:none;"> <img src="assets/admin/layout/img/ajax-loader.gif" alt="" style="margin-bottom: 10%;;margin-top:10%;padding-left:48%;" > </div>
							<div id="results">
							
<form action="" name="frm" id="frm" method="post">
  <input type="text" id="permSearch" class="form-control perm-search" placeholder="Search menu or submenu">
  <div class="tableFixHead" >
	<table id="datatable_1" class="table table-striped table-bordered table-hover">
        <thead>
				<tr>
					<th>Menu / Submenu</th>
					<th>All <input type="checkbox" id="permHeadAll" title="All rights for every menu"></th>
					<th>View </th>
					<th>Insert </th>
					<th>Update </th>
					<th>Delete </th>
					<th>Export Excel</th>
					<th>Print</th>
					<th>Pdf Download</th>
					<th>Email</th>
					<th>All Data</th>
					<th>Approve Data</th>
					<th>Personal</th>
					<th>Chain Vise</th>
					<th>Action</th>
				</tr>
        </thead>
        <tbody>
			<?php
				$exclude_pages = $comman_pages;
				$exclude_pages[] = 5895;
				armor_echo_permission_board($db, $pid, 'add', $exclude_pages);
			?>
        </tbody>
    </table>
    </div>
</form>
						
							
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
<script src="js/toastr.js"></script>
<script type="text/javascript">
$('#permSearch').on('keyup', function() {
	var q = $.trim($(this).val()).toLowerCase();
	$('.perm-row').each(function() {
		var show = q === '' || $(this).text().toLowerCase().indexOf(q) !== -1;
		$(this).toggle(show);
	});
	$('.perm-section').each(function() {
		var next = $(this).nextUntil('.perm-section');
		$(this).toggle(next.filter(':visible').length > 0);
	});
});
var searchName="";
var data_url = "<?php echo $ctable ?>_get_ajax.php";
function searchByName(){
	searchName = $("#searchName").val();
	displayRecords(10,1);
	return false;
}
function clearSearchByName(){
	searchName = "";
	$("#searchName").val("");
	displayRecords(10,1);
}
$("#searchName").keyup(function(event){
	if(event.keyCode == 13){
		$("#searchByName").click();
	}
});
function loadDataTable(){
	
}

// used when user change row limit
function changeDisplayRowCount(numRecords) {
	displayRecords(numRecords, 1);
}
function addAR(pid, quiet, done){
	
	var view_flag=0;
	var insert_flag=0;
	var update_flag=0;
	var delete_flag=0;
	var export_excel_flag=0;
	var print_flag=0;
	var pdf_download_flag=0;
	var email_flag=0;
	var all_data_flag=0;
	var approve_flag=0;
	var personal_flag=0;
	var chain_vise_flag=0;
	if(document.getElementById("insert"+pid).checked)
	{
		insert_flag=document.getElementById("insert"+pid).value;
	}
	if(document.getElementById("view"+pid).checked)
	{
		view_flag=document.getElementById("view"+pid).value;
	}
	if(document.getElementById("update"+pid).checked)
	{
		update_flag=document.getElementById("update"+pid).value;
	}
	if(document.getElementById("delete"+pid).checked)
	{
		delete_flag=document.getElementById("delete"+pid).value;
	}
	if(document.getElementById("export_excel"+pid).checked)
	{
		export_excel_flag=document.getElementById("export_excel"+pid).value;
	}
	if(document.getElementById("print"+pid).checked)
	{
		print_flag=document.getElementById("print"+pid).value;
	}
	if(document.getElementById("pdf_download"+pid).checked)
	{
		pdf_download_flag=document.getElementById("pdf_download"+pid).value;
	}
	if(document.getElementById("email"+pid).checked)
	{
		email_flag=document.getElementById("email"+pid).value;
	}
	if(document.getElementById("all_data"+pid).checked)
	{
		all_data_flag=document.getElementById("all_data"+pid).value;
	}
	if(document.getElementById("approve"+pid).checked)
	{
		approve_flag=document.getElementById("approve"+pid).value;
	}
	if(document.getElementById("personal"+pid).checked)
	{
		personal_flag=document.getElementById("personal"+pid).value;
	}

	if(document.getElementById("chain_vise"+pid).checked)
	{
		chain_vise_flag=document.getElementById("chain_vise"+pid).value;
	}

	
	
	if(insert_flag==0 && view_flag==0 && update_flag==0 && delete_flag==0 && export_excel_flag==0 && print_flag==0 && pdf_download_flag==0 && email_flag==0 && all_data_flag==0 && approve_flag==0 && personal_flag==0 && chain_vise_flag==0){
		if(!quiet){
			alert("Please select atleast one right.");
		}
		if(done){ done(); }
	}else{
		$.ajax({
			type: "POST",
			url: "ajax_addAR.php",
data: 'mode=add&pid=<?php echo $_REQUEST['pid']; ?>&tid='+pid+'&insert_flag='+insert_flag+'&view_flag='+view_flag+'&update_flag='+update_flag+'&delete_flag='+delete_flag +'&export_excel_flag='+export_excel_flag+'&print_flag='+print_flag+'&pdf_download_flag='+pdf_download_flag+'&email_flag='+email_flag+'&all_data_flag='+all_data_flag+'&approve_flag='+approve_flag+'&personal_flag='+personal_flag+'&chain_vise_flag='+chain_vise_flag,
			success: function(result){
					if(!quiet){
						if(result==1){
							toastr.success("Right added successfully.");
						}else if(result==2){
							toastr.error("This Right is already added.");
						}else{
							toastr.error("Something went wrong. Please try again.");
						}
					}
					$("#row"+pid).fadeOut(1200);
					if(done){ done(); }
				},
			error: function(){
				if(!quiet){ toastr.error("Something went wrong. Please try again."); }
				if(done){ done(); }
			}
		});
		
	}
}
</script>
<script type="text/javascript">
function del_conf(id){
	var r = confirm("Are you sure you want to delete?");
	if(r){
		window.location.href='<?php echo $ctable; ?>_crud.php?mode=delete&id='+id;
	}
}
</script>
<script type="text/javascript">
var permFields = ['view','insert','update','delete','export_excel','print','pdf_download','email','all_data','approve','personal','chain_vise'];
function permPaint(el, on) {
	if (!el) { return; }
	el.checked = on;
	if (window.jQuery && jQuery.uniform && jQuery.uniform.update) {
		jQuery.uniform.update(el);
	}
}
function permSetKey(key, on) {
	var i, el, row;
	for (i = 0; i < permFields.length; i++) {
		permPaint(document.getElementById(permFields[i] + key), on);
	}
	permPaint(document.getElementById('rowall' + key), on);
	row = document.getElementById('row' + key);
	if (row) {
		var boxes = row.getElementsByTagName('input');
		for (i = 0; i < boxes.length; i++) {
			if (boxes[i].type === 'checkbox') {
				permPaint(boxes[i], on);
			}
		}
	}
}
function permSaveKeys(keys) {
	var left = keys.length;
	if (!left) { return; }
	function finishOne() {
		left--;
		if (left === 0 && window.toastr) {
			toastr.success('All rights saved.');
		}
	}
	for (var i = 0; i < keys.length; i++) {
		permSetKey(keys[i], true);
		if (typeof addAR === 'function') {
			addAR(keys[i], true, finishOne);
		} else if (typeof addPT === 'function') {
			addPT(keys[i], true, finishOne);
		} else {
			finishOne();
		}
	}
}
$(document).on('change', '.perm-row-all', function() {
	var key = $(this).attr('data-key');
	if (this.checked) {
		permSaveKeys([key]);
	} else {
		permSetKey(key, false);
	}
});
$(document).on('change', '.perm-section-all', function() {
	var on = this.checked;
	var keys = [];
	$(this).closest('tr').nextUntil('.perm-section').filter('.perm-row:visible').each(function() {
		keys.push($(this).attr('data-key'));
	});
	if (on) {
		permSaveKeys(keys);
	} else {
		for (var i = 0; i < keys.length; i++) {
			permSetKey(keys[i], false);
		}
	}
});
$(document).on('change', '#permHeadAll', function() {
	var on = this.checked;
	$('.perm-section-all').prop('checked', on);
	var keys = [];
	$('.perm-row:visible').each(function() {
		keys.push($(this).attr('data-key'));
	});
	if (on) {
		permSaveKeys(keys);
	} else {
		for (var i = 0; i < keys.length; i++) {
			permSetKey(keys[i], false);
		}
	}
});
$(document).on('change', '.perm-flag', function() {
	var row = $(this).closest('tr');
	var key = row.attr('data-key');
	var on = true;
	var i, el;
	for (i = 0; i < permFields.length; i++) {
		el = document.getElementById(permFields[i] + key);
		if (!el || !el.checked) { on = false; break; }
	}
	el = document.getElementById('rowall' + key);
	if (el) { el.checked = on; }
});
</script>
</body>
</html>