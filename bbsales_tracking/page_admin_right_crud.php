<?php
$page_id=404;$page_slug='manage_admin_type';
$ctable 	= "page_admin_right";
$ctable1 	= "Right to Admin";
$main_page 	= "product_mgmt";
$page 		= "manage_page_admin_right";
$page_title = "Add ".$ctable1;

include("connect.php");
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
        overflow-y: auto;
        height: 500px;
    }
    .tableFixHead thead th {
        position: sticky;
        top: 0;
    }
    table {
        border-collapse: collapse;
        width: 100%;
    }
    th,td {
        padding: 8px 16px;
        border: 1px solid #ccc;
    }
    th {
        background: #e5e5e5;
        position: fixed;
        z-index: 1;
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
  <div class="tableFixHead" >
	<table id="datatable_1" class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
				<tr>
					<th>No.</th>
					<th>Page Name</th>									
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
            </tr>
        </thead>
        <tbody>
			<?php
				if($tids=="")
					$ctable_r = $db->rp_getData("page_table","*","isDelete=0","",0);
				else
				$ctable_r = $db->rp_getData("page_table","*","id not in ($tids) AND isDelete=0","",0);
				if(mysqli_num_rows($ctable_r)>0){
					$count = 0;
					
					while($ctable_d = mysqli_fetch_array($ctable_r)){
						$count++;
				?>
           <tr id="row<?php echo $ctable_d['id']; ?>">
										<td><?php echo $count; ?></td>
										<td><?php echo stripslashes($ctable_d['page_title']); ?></td>
										<td align="center">
											<input type="checkbox" name="view<?php echo $ctable_d['id']; ?>[]" id="view<?php echo $ctable_d['id']; ?>" value="1" style="width:60px;text-align:center">
										</td>	
<td align="center">
											<input type="checkbox" name="insert<?php echo $ctable_d['id']; ?>[]" id="insert<?php echo $ctable_d['id']; ?>" value="1" style="width:60px;text-align:center">
										</td>	
<td align="center">
											<input type="checkbox" name="update<?php echo $ctable_d['id']; ?>[]" id="update<?php echo $ctable_d['id']; ?>" value="1" style="width:60px;text-align:center">
										</td>	
<td align="center">
											<input type="checkbox" name="delete<?php echo $ctable_d['id']; ?>[]" id="delete<?php echo $ctable_d['id']; ?>" value="1" style="width:60px;text-align:center">
										</td>			

										<td align="center">
															<input type="checkbox" name="export_excel<?php echo $ctable_d['id']; ?>[]" id="export_excel<?php echo $ctable_d['id']; ?>" value="1" <?php echo ($ctable_d['export_excel_flag']==1)?"checked":""; ?> style="width:60px;text-align:center">
														</td>
														<td align="center">
															<input type="checkbox" name="print<?php echo $ctable_d['id']; ?>[]" id="print<?php echo $ctable_d['id']; ?>" value="1" <?php echo ($ctable_d['print_flag']==1)?"checked":""; ?> style="width:60px;text-align:center">
														</td>	
														<td align="center">
															<input type="checkbox" name="pdf_download<?php echo $ctable_d['id']; ?>[]" id="pdf_download<?php echo $ctable_d['id']; ?>" value="1" <?php echo ($ctable_d['pdf_download_flag']==1)?"checked":""; ?> style="width:60px;text-align:center">
														</td>	
														<td align="center">
															<input type="checkbox" name="email<?php echo $ctable_d['id']; ?>[]" id="email<?php echo $ctable_d['id']; ?>" value="1" <?php echo ($ctable_d['email_flag']==1)?"checked":""; ?> style="width:60px;text-align:center">
														</td>	
														<td align="center">
															<input type="checkbox" name="all_data<?php echo $ctable_d['id']; ?>[]" id="all_data<?php echo $ctable_d['id']; ?>" value="1" <?php echo ($ctable_d['all_data_flag']==1)?"checked":""; ?> style="width:60px;text-align:center">
														</td>	
														<td align="center">
															<input type="checkbox" name="approve<?php echo $ctable_d['id']; ?>[]" id="approve<?php echo $ctable_d['id']; ?>" value="1" <?php echo ($ctable_d['approve_flag']==1)?"checked":""; ?> style="width:60px;text-align:center">
														</td>	
														<td align="center">
															<input type="checkbox" name="personal<?php echo $ctable_d['id']; ?>[]" id="personal<?php echo $ctable_d['id']; ?>" value="1" <?php echo ($ctable_d['personal_flag']==1)?"checked":""; ?> style="width:60px;text-align:center">
														</td>															

														<td align="center">
															<input type="checkbox" name="chain_vise<?php echo $ctable_d['id']; ?>[]" id="chain_vise<?php echo $ctable_d['id']; ?>" value="1" <?php echo ($ctable_d['chain_vise_flag']==1)?"checked":""; ?> style="width:60px;text-align:center">
														</td>															
										
										<td>
											<a class="btn btn-info btn-sm" href="javascript:void(0);" title="Add" onClick="addAR('<?php echo $ctable_d['id']; ?>');"><i class="fa fa-plus"></i> Add</a>
											
										</td>
									</tr>
									<?php
										}
									}
									
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
$('#datatable_1').dataTable({
		"bPaginate": false,
		"bFilter": false,
		"bInfo": false,
		"bAutoWidth": false, 
		"aoColumns": [
			  { "sWidth": "10%" }, 
			  { "sWidth": "30%" },
			  { "sWidth": "10%" },
			  { "sWidth": "10%" },
			  { "sWidth": "10%" },
			  { "sWidth": "10%" },			  
			  { "sWidth": "20%","bSortable": false }
			]
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
function addAR(pid){
	
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
		delete_flag=document.getElementById("update"+pid).value;
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
		alert("Please select atleast one right."+insert_flag+view_flag);
		
	}else{
		$.ajax({
			type: "POST",
			url: "ajax_addAR.php",
data: 'mode=add&pid=<?php echo $_REQUEST['pid']; ?>&tid='+pid+'&insert_flag='+insert_flag+'&view_flag='+view_flag+'&update_flag='+update_flag+'&delete_flag='+delete_flag +'&export_excel_flag='+export_excel_flag+'&print_flag='+print_flag+'&pdf_download_flag='+pdf_download_flag+'&email_flag='+email_flag+'&all_data_flag='+all_data_flag+'&approve_flag='+approve_flag+'&personal_flag='+personal_flag+'&chain_vise_flag='+chain_vise_flag,
			success: function(result){
					if(result==1){
						toastr.success("Right added successfully.");
					}else if(result==2){
						toastr.error("This Right is already added.");
					}else{
						toastr.error("Something went wrong. Please try again.");
					}
					$("#row"+pid).fadeOut(1200);
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
</body>
</html>