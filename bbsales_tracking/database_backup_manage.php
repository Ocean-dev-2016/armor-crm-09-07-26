<?php
$page_id=406;$page_slug='page_admin';
include("connect.php");

$main_page 	= "utility";
$page 		= "database_backup";
$page_title = "Database Backup";
$Path = SITEURL."/".ADMINFOLDER."/fonts/collection/";

/* backup the db OR just a table */
function backup_tables($host,$user,$pass,$name,$tables = '*')
{
	
	$link = mysqli_connect($host,$user,$pass);
	mysqli_select_db($name,$link);
	
	//get all of the tables
	if($tables == '*')
	{
		
		$tables = array();
		$result = mysqli_query('SHOW TABLES');
		while($row = mysqli_fetch_row($result))
		{
			$tables[] = $row[0];
		}
	}
	else
	{
		$tables = is_array($tables) ? $tables : explode(',',$tables);
	}
	
	//cycle through
	foreach($tables as $table)
	{
		$result = mysqli_query('SELECT * FROM '.$table);
		$num_fields = mysqli_num_fields($result);
		
		//$return.= 'DROP TABLE '.$table.';';
		$row2 = mysqli_fetch_row(mysqli_query('SHOW CREATE TABLE '.$table));
		$return.= "\n\n".$row2[1].";\n\n";
		
		for ($i = 0; $i < $num_fields; $i++) 
		{
			while($row = mysqli_fetch_row($result))
			{
				$return.= 'INSERT INTO '.$table.' VALUES(';
				for($j=0; $j<$num_fields; $j++) 
				{
					$row[$j] = addslashes($row[$j]);
					$row[$j] = ereg_replace("\n","\\n",$row[$j]);
					if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
					if ($j<($num_fields-1)) { $return.= ','; }
				}
				$return.= ");\n";
			}
		}
		$return.="\n\n\n";
	}
	
	//save file
	$time = time();
	$fileName 	= $time.'.sql';
	$zipfileName= $time.'.zip';
	$mysqliExportPath = "fonts/collection/".$fileName;
	$handle = fopen($mysqliExportPath,'w+');
	fwrite($handle,$return);
	fclose($handle);
	
	/**************************Zip File Creation****************************/
	$zip = new ZipArchive();
	$filename = "fonts/collection/".$time.".zip";
	if($zip->open($filename, ZIPARCHIVE::CREATE) !== TRUE) {
		exit("cannot open <$filename>n");
	}
	$zip->addFile($mysqliExportPath , $time.'.sql');
	$zip->close();
	@unlink($mysqliExportPath);
	/**************************Zip File Creation***************************/
	
	return $zipfileName;
}
if(isset($_POST['saveDB'])){
	
	$fileName = backup_tables('localhost','root','','primate');
	$dateDownload = date('Y-m-d H:i:s');
	
	$values = array($dateDownload,$fileName);
	$rows = array("createDate","fileUrl");
	$ps = $db->rp_insert("dbbackup",$values,$rows,0);
	$db->rp_location("database_backup_manage.php?msg=4");	
}
if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="delete"){
	
	$where = " id =".$_REQUEST['id'];
	$del_r = $db->rp_getData("dbbackup","*",$where);
	$del_d = mysqli_fetch_array($del_r);
	$filename = $del_d['fileUrl'];
	if($filename!="" && file_exists("fonts/collection/".$filename)){
		unlink("fonts/collection/".$filename);
	}
	$where = " id='".$_REQUEST['id']."'";
	$db->rp_delete("dbbackup",$where);
	
	$db->rp_location("database_backup_manage.php");
}
$scheck_res = $db->rp_getData("dbbackup","*");



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
				<h1><a href="<?php echo "dashboard.php";?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php echo $page_title; ?></h1>
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
						<div class="table-toolbar">
							<div class="row">
								<div class="col-md-7">
								<table class="table" style="margin-bottom:0;">
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
													<input class="btn btn-danger btn-sm" type="button" value="Filter" onClick="getByDate();">
										</div>
										</div>
										</table>
									
								</div>
								<div class="col-md-5">
									
								</div>
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
	</div>
	
</div>
<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>
<script type="text/javascript" src="assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript">
var date = new Date();
$('#ToDate').datepicker({  datepicker: true, autoclose: true ,format:'yyyy-mm-dd'});
$('#FromDate').datepicker({  datepicker: true, autoclose: true,format:'yyyy-mm-dd' });

//var searchName="";
var data_url = "database_backup_get_ajax.php";

var ToDate="";
var FromDate="";
function getByDate()
 {
	 // for don't select less than frome date
	var ToDate=Date.parse($("#ToDate").val());
	var FromDate=Date.parse($("#FromDate").val());
	
	if(FromDate<=ToDate)
	{
		
		ToDate = $("#ToDate").val();
		FromDate = $("#FromDate").val();
		displayRecords(100,1);
	}
	else
	{
		alert("From Date Should Be Less Than To Date");
	}
	
}

// function searchByName(){
	// searchName = $("#searchName").val();
	// displayRecords(100,1);
	// return false;
// }
// function clearSearchByName(){
	// searchName = "";
	// $("#searchName").val("");
	// $("#FromDate").val("");
	// $("#ToDate").val("");
	// displayRecords(100,1);
// }
// $("#searchName").keyup(function(event){
	// if(event.keyCode == 13){
		// $("#searchByName").click();
	// }
// });
function loadDataTable(){
	$('#datatable_1').dataTable({
		"bPaginate": false,
		"bFilter": false,
		"bInfo": false,
		"bAutoWidth": false, 
		"aoColumns": [
			  { "sWidth": "5%" }, 
			  { "sWidth": "20%" },
			  { "sWidth": "10%" },
			  { "sWidth": "10%" },
			  { "sWidth": "10%" },
			  
			  { "sWidth": "23%","bSortable": false }
			]
	});
}


function displayRecords(numRecords) {
	//var searchName 	= $("#searchName").val();
	var account 	= $("#account").val();
	//searchName 	= encodeURIComponent(searchName.trim());
	ToDate = $("#ToDate").val();
	FromDate = $("#FromDate").val();
	$("#results" ).html("");
	$("#results" ).load( data_url+"?show=" + numRecords + "&ToDate=" + ToDate + "&FromDate=" + FromDate + "&account=" + account ,function(){
		loadDataTable();
	}); //load initial records
	
	//executes code below when user click on pagination links
	$("#results").on( "click", ".paging_simple_numbers a", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results").load(data_url+"?show=" + numRecords + "&ToDate=" + ToDate + "&FromDate=" + FromDate + "&account=" + account ,{"page":page}, function(){ //get content from PHP page
			$(".loading-div").hide(); //once done, hide loading element
			loadDataTable();
		});
		
	});
	$("#results").on( "change", "#numRecords", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results").load(data_url+"?show=" + numRecords + "&ToDate=" + ToDate + "&FromDate=" + FromDate + "&account=" + account,{"page":page}, function(){ //get content from PHP page
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
					
					window.location.href='database_backup_manage.php?mode=delete&id='+id;
				}
			}
        </script>

</body>
</html>