<?php
$page_id=404;$page_slug='manage_admin_type';
$ctable 	= "page_admin_right";
$ctable1 	= "See Admin Right For Page";
$main_page 	= "product_mgmt";
$page 		= "manage_".$ctable;
$page_title = "Manage ".$ctable1;
include("connect.php");
$pid=$_REQUEST['pid'];
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
	.tableFixHead 
	{
      overflow-y: auto;
      height: 500px;
   }
   .tableFixHead thead th 
   {
      position: sticky;
      top: 0;
   }
   table 
   {
      border-collapse: collapse;
      width: 100%;
   }
   th,td 
   {
      padding: 8px 16px;
      border: 1px solid #ccc;
   }
   th 
   {
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
				<h1><a href="<?php echo "admin_type_manage.php";?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php echo $page_title; ?></h1>
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
				<div class="col-md-12">
					<div class="portlet light">
						<div class="box-body">
							<form action="" name="frm" id="frm" method="post">
								<div class=""><?php
									echo $db->getAddButton($ctable,"",$ctable."_crud.php?pid=".$_REQUEST['pid']."&mode=add");
								?>
								<button class="btn btn-primary sidebar" type="button" onClick="clc();">Update All</button>								
								</div>
							<div class="tableFixHead" > 
								<table id="example1" class="table table-bordered table-striped dataTable">
									<thead>
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
									</thead>
									<tbody>
											<?php
												
													$ctable_r = $db->rp_getData("page_admin_right","*","admin_id='".$pid."' AND isDelete=0","",0);
												
												if(mysqli_num_rows($ctable_r)>0){
													$count = 0;
													
													while($ctable_d = mysqli_fetch_array($ctable_r)){
														$count++;
												?>
										   <tr id="row<?php echo $ctable_d['id']; ?>">
																		<td><?php echo $count; ?></td>
																		<td><?php 
												echo stripslashes($db->rp_getValue("page_table","page_title","id='".$ctable_d['page_id']."'")); 
												?></td>
																		<td align="center">
																			<input type="checkbox" name="view<?php echo $ctable_d['id']; ?>[]" id="view<?php echo $ctable_d['id']; ?>" value="1" <?php echo ($ctable_d['view_flag']==1)?"checked":""; ?> style="width:60px;text-align:center">
																		</td>	
								<td align="center">
																			<input type="checkbox" name="insert<?php echo $ctable_d['id']; ?>[]" id="insert<?php echo $ctable_d['id']; ?>" value="1" <?php echo ($ctable_d['insert_flag']==1)?"checked":""; ?> style="width:60px;text-align:center">
																		</td>	
								<td align="center">
																			<input type="checkbox" name="update<?php echo $ctable_d['id']; ?>[]" id="update<?php echo $ctable_d['id']; ?>" value="1"  <?php echo ($ctable_d['update_flag']==1)?"checked":""; ?> style="width:60px;text-align:center">
																		</td>	
								<td align="center">
																			<input type="checkbox" name="delete<?php echo $ctable_d['id']; ?>[]" id="delete<?php echo $ctable_d['id']; ?>" value="1" <?php echo ($ctable_d['delete_flag']==1)?"checked":""; ?> style="width:60px;text-align:center">
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
																			<a class="btn btn-info btn-sm clc" href="javascript:void(0);" title="Add" onClick="addPT('<?php echo $ctable_d['id']; ?>');"><i class="fa fa-pencil"></i> Update</a>
																			<a class="btn btn-danger btn-sm" onClick="del_conf('<?php echo $ctable_d['id']; ?>');" title="Delete"><i class="fa fa-times"></i></a>
																		</td>
																	</tr>
																	<?php
																		}
																	}
																	
																	?>
										</tbody>
									
								</table>
							</div>
								<input type="hidden" name="price_count" value="<?php echo $count; ?>">
								<input type="hidden" name="submit" value="submit">
								<div class="">
								<?php
									echo $db->getAddButton($ctable,"",$ctable."_crud.php?pid=".$_REQUEST['pid']."&mode=add");
								?>
								<button class="btn btn-primary sidebar" type="button" onClick="clc();">Update All</button>
								<a  class="btn btn-success sidebar"  href="admin_type_manage.php">Back</a>
								</div>
							</form>
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

</script>
<script type="text/javascript">function clc(){
	$(".clc").trigger('click');
}
function addPT(pid){
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
	
	
	if(insert_flag==0 && view_flag==0 && update_flag==0 && delete_flag==0 && export_excel_flag==0 && print_flag==0 && pdf_download_flag==0 && email_flag==0 && all_data_flag==0 && approve_flag==0  && personal_flag==0 && chain_vise_flag==0){
		toastr.error("Please enter valid rights or delete completly");
	}else{
		$.ajax({
			type: "POST",
			url: "ajax_addAR.php",
			data: 'mode=edit&id='+pid+'&insert_flag='+insert_flag+'&view_flag='+view_flag+'&update_flag='+update_flag+'&delete_flag='+delete_flag+'&export_excel_flag='+export_excel_flag+'&print_flag='+print_flag+'&pdf_download_flag='+pdf_download_flag+'&email_flag='+email_flag+'&all_data_flag='+all_data_flag+'&approve_flag='+approve_flag+'&personal_flag='+personal_flag+'&chain_vise_flag='+chain_vise_flag,
			success: function(result){
					if(result==1){
						toastr.success("Rights updated successfully.");
					}else{
						toastr.error("Something went wrong. Please try again.");
					}
				}
		});
		
	}
}
function del_conf(id){
	var r = confirm("Are you sure you want to delete?");
	if(r){
		window.location.href='<?php echo $ctable; ?>_crud.php?mode=delete&pid=<?php echo $_REQUEST['pid']; ?>&id='+id;
	}
}
</script>
<script>
$(document).ready(function() {       
   $('#example1').dataTable({
	    "paging":   false,
        "ordering": false,
        "info":     false
   });
});
</script>
</body>
</html>