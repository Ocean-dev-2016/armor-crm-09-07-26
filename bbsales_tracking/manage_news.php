<?php

$page_id=579;$page_slug='news';

$ctable 	= "news";

$ctable1 	= "News";

$main_page 	= "utility";

$page 		= "manage_news";

$page_title = $ctable1;

$page_hierarchy=array(array("link"=>"","title"=>"Utility"),array("link"=>"manage_news.php","title"=>$page_title));

include("connect.php");



if(isset($_REQUEST['submit'])){

	$disp_count = $_REQUEST['disp_count'];

	for($i=1;$i<=$disp_count;$i++){

		$b_id 			= $_REQUEST['b_id'.$i];

		$display_order	= $_REQUEST['disp'.$i];

		if($b_id>0){

			$check_disp=$db->rp_getTotalRecord("news","isDelete=0 AND display_order='".$display_order."' AND id!='".$b_id."'");
			if($check_disp >0)
			{
				$db->addErrorMessage("The Display Order Should Not be Same");
			}
			else
			{

			$rows 	= array("display_order"=>$display_order);

			$where	= "id='".$b_id."'";

			$db->rp_update($ctable,$rows,$where);
				

			}


		}

	}

	// $db->rp_location("manage_banner.php?msg=updated");

}

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

			<div class="row">

				<div class="col-sm-12">

					<?php $db->getMessageBlock(); ?>

				</div>

				<div class="col-md-12">

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

<script type="text/javascript">

var searchName="";

var data_url = "news_get_ajax.php";



function loadDataTable(){

	$('#datatable_1').dataTable({

		"bPaginate": false,

		"bFilter": false,

		"bInfo": false,

		"bAutoWidth": false, 

		"aoColumns": [

			  { "sWidth": "5%" }, 

			  { "sWidth": "10%","bSortable": false },

			  { "sWidth": "23%","bSortable": false }

			]

	});

}

function displayRecords(numRecords) {

	$("#results" ).html("");

	$("#results" ).load( data_url+"?show=" + numRecords ,function(){

		loadDataTable();

	}); //load initial records

	

	//executes code below when user click on pagination links

	$("#results").on( "click", ".paging_simple_numbers a", function (e){

		e.preventDefault();

		var numRecords  = $("#numRecords").val();

		$(".loading-div").show(); //show loading element

		var page = $(this).attr("data-page"); //get page number from link

		$("#results").load(data_url+"?show=" + numRecords ,{"page":page}, function(){ //get content from PHP page

			$(".loading-div").hide(); //once done, hide loading element

			loadDataTable();

		});

		

	});

	$("#results").on( "change", "#numRecords", function (e){

		e.preventDefault();

		var numRecords  = $("#numRecords").val();

		$(".loading-div").show(); //show loading element

		var page = $(this).attr("data-page"); //get page number from link

		$("#results").load(data_url+"?show=" + numRecords ,{"page":page}, function(){ //get content from PHP page

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

// $(document).ready(function() {       

   // $('#datatable_1').dataTable();

// });


/*dispay order function*/
function CheckDispalyOrder(id)
{
	var display_order = $("#disp"+id).val();
	var size_id = $("#disp"+id).data("size-id");

	$.ajax({
		type: "POST",
		url: "check_display_order_ajax.php",
		data: 'display_order='+display_order+"&id="+size_id+"&table=news",
		success: function(result){
			result=$.parseJSON(result);
			if(result.ack==1)
			{
				toastr.success("Update Successfully!!","Success");
			}
			else
			{
				toastr.error("Value Already Available","Error");
				var display_order = $("#disp"+id).val(0);
			}
		}
	});
}
/*dispay order function*/

</script>

</body>

</html>