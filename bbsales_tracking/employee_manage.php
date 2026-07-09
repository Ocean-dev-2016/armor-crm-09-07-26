<?php
$page_id=554;$page_slug='page_employee';
$etable 	= "employee";
$ctable 	= "emp_personal_info";
$ctable1 	= "Employee";
$main_page 	= $ctable;
$page 		= "manage_".$ctable;
$page_title = "Manage ".$ctable1;
$page_hierarchy=array(array("link"=>"","title"=>"HR"),array("link"=>$ctable."_manage.php","title"=>"Manage ".$ctable1));
include("connect.php");
$designation 	= "";
$department 	= "";
if($rights['view_flag']!=1)
{
        $db->rp_location('access_denied.php?msg=insert_access_denied');
}
$ToDate="";
$FromDate="";
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
                <h1><a href="<?php echo "dashboard.php";?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php $db->pageBar($page_hierarchy);?> </h1>
            </div>
        </div>
    </div>

    <div class="page-content">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                <?php $db->printErrorMessage(); ?>
                <?php $db->printSuccessMessage(); ?>
                </div>
                <div class="col-md-12 ">
                    <!-- BEGIN Portlet PORTLET-->
                    <div class="portlet box blue">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-filter"></i>Filters </div>
                             <div class="tools">
                                <a href="javascript:;" class="collapse" data-original-title="" title=""> </a>

                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto; height: auto;">

                                <div class="row">
                                         <div class="col-md-6  col-xs-6  col-sm-6" style="margin-top:10px">
                                          <div class="form-inline" role="form">
                                           <div class="form-group">
                                                <label>Filter By Created Date : &nbsp;</label>
                                                <input type="text" class="form-control input-small"  name="FromDate" id="FromDate" value="<?php echo $FromDate; ?>" placeholder="From Date">
                                            </div>
                                             <div class="form-group">
                                                 <label>&nbsp;&nbsp;</label>
                                                <input type="text" class="form-control input-small"  name="ToDate" id="ToDate" value="<?php echo $ToDate; ?>" placeholder="To Date">
                                            </div>
                                             <div class="form-group">
                                                		<input class="btn btn-danger btn-sm" type="submit" value="Filter" onClick="getByDate();">
                                            </div>
                                          </div>
                                          </div>
                                          <div class="col-md-6  col-xs-6 col-sm-6 " style="margin-top:10px">

                                          <form class="form-inline pull-right" role="form" onSubmit="return searchByName();">
                                           <div class="form-group">
                                                <label>Search By Employee Code: &nbsp;</label>
                                                <input type="text" class="form-control" name="searchName" id="searchName" value="" placeholder="Emp Code OR Name" />
                                            </div>
                                             <div class="form-group">
                                                <input class="btn btn-danger btn-sm" type="submit" value="search">
                                            </div>
                                             <div class="form-group">
                                                   	<input class="btn btn-success btn-sm" type="button" value="clear" onClick="clearSearchByName();">
                                            </div>
                                          </div>
                                        </form>
                                </div>
                                 <div class="row">
                                    <div class="col-md-4 col-xs-4 col-sm-4" style="margin-top:10px">
                                        <div class="form-group">
                                            <label>Search by Deparment</label>
                                             <select class="form-control input-large department" name="department" id="department" autofocus onChange="getDepartment(this.value);">
                                                <option value="">Select Department</option>
                                                <?php
                                                    $department_list_d=$db->rp_getData('department',"*","1=1","name asc",0);
                                                    while($department_list_r=mysqli_fetch_assoc($department_list_d))
                                                    {
                                                        ?>
                                                        <option <?php echo ($department==$department_list_r['id'])?"selected":"" ; ?> value="<?php echo $department_list_r['id']?>">
                                                        <?php echo $department_list_r['name'];?>
                                                        </option>
                                                        <?php
                                                    }
                                                ?>
                                        </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-xs-12 col-sm-12" style="margin-top:10px">
                                        <div class="form-group">
                                            <label>Search by Designation</label>

                                            <select class="form-control input-large designation" name="designation" id="designation" autofocus onChange="getDesignation(this.value);">
                                                    <option value="">Select Designation</option>
                                                    <?php
                                                        $designation_list_d=$db->rp_getData('designation',"*","1=1","name asc",0);
                                                        while($designation_list_r=mysqli_fetch_assoc($designation_list_d))
                                                        {
                                                            ?>
                                                            <option <?php echo ($designation==$designation_list_r['id'])?"selected":"" ; ?> value="<?php echo $designation_list_r['id']?>">
                                                            <?php echo $designation_list_r['name'];?>
                                                            </option>
                                                            <?php
                                                        }
                                                    ?>
                                            </select>

                                        </div>
                                    </div>
                                </div>

                                </div>
                            </div>
                    </div>
                    <!-- END Portlet PORTLET-->
                </div>
                <div class="col-sm-12">
                    <div class="portlet light">
                        <div class="table-toolbar">
                            <div class="row">
                                <div class="col-md-1">
                                    <?php
                                        echo $db->getAddButton($etable);
                                    ?>
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
<div id="empInfoView" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body portlet box blue">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-gift"></i>View Employee Information</div>
                    <div class="tools">
                        <a href="javascript:;" id="requesting_ajax" data-load="true" data-url="" class="reload" data-original-title="" title=""><i class="fa fa-reload"></i></a>
                        <a href="javascript:;"  data-original-title="" title="" data-dismiss="modal" style="color:white;"><i class="fa fa-close"></i></a>
                    </div>
                </div>
                <div class="portlet-body portlet-empty" style="">
                </div>
            </div>
        </div>
    </div>
</div>
<div id="empSalaryView" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body portlet box blue">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-gift"></i>View Employee Salary Information</div>
                    <div class="tools">
                        <a href="javascript:;" id="requesting_ajax_salary" data-load="true" data-url="" class="reload" data-original-title="" title=""><i class="fa fa-reload"></i></a>
                        <a href="javascript:;"  data-original-title="" title="" data-dismiss="modal" style="color:white;"><i class="fa fa-close"></i></a>
                    </div>
                </div>
                <div class="portlet-body portlet-empty" style="">
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
var department="";
var designation="";
$('#ToDate').datepicker({  datepicker: true, autoclose: true });
$('#FromDate').datepicker({  datepicker: true, autoclose: true });


$('#empInfoView').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget) // Button that triggered the modal
  var requesting_id=button.data("id");
    $("#requesting_ajax").attr("data-url","emp_information_get_ajax.php?id="+requesting_id);
    $("#requesting_ajax").click();
})
$('#empSalaryView').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget) // Button that triggered the modal
  var requesting_id=button.data("id");
    $("#requesting_ajax_salary").attr("data-url","emp_salary_information_get_ajax.php?id="+requesting_id);
    $("#requesting_ajax_salary").click();
})
var data_url = "<?php echo $etable ?>_get_ajax.php";
function searchByName(){
    searchName = $("#searchName").val();
    displayRecords(100,1);
    return false;
}
function clearSearchByName(){
    searchName = "";
    $("#searchName").val("");
    displayRecords(100,1);
}
function getByDate() {
	//alert("date");
	ToDate = $("#ToDate").val();
	FromDate = $("#FromDate").val();
	if(FromDate<=ToDate)
	{
		
		
		displayRecords(100,1);
	}
	else
	{
		alert("From Date Should Be Less Than To Date");
	}

	
}
$("#searchName").keyup(function(event){
    if(event.keyCode == 13){
        $("#searchByName").click();
    }
});
function getDesignation(cid){
        designation=cid;
        displayRecords(100,1);
}
function getDepartment(cid){
        department=cid;
        displayRecords(100,1);
}
function displayRecords(numRecords) {
    var searchName 	= $("#searchName").val();
    ToDate = $("#ToDate").val();
	FromDate = $("#FromDate").val();
    searchName 	= encodeURIComponent(searchName.trim());
    $("#results" ).html("");
    $("#results" ).load( data_url+"?show=" + numRecords + "&searchName=" + searchName + "&ToDate=" + ToDate  + "&FromDate=" + FromDate + "&department=" + department + "&designation=" + designation,function(){
        loadDataTable();
    }); //load initial records

    //executes code below when user click on pagination links
    $("#results").on( "click", ".paging_simple_numbers a", function (e){
        e.preventDefault();
        var numRecords  = $("#numRecords").val();
        $(".loading-div").show(); //show loading element
        var page = $(this).attr("data-page"); //get page number from link
        $("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName + "&ToDate=" + ToDate  + "&FromDate=" + FromDate,{"page":page}, function(){ //get content from PHP page
            $(".loading-div").hide(); //once done, hide loading element
            loadDataTable();
        });

    });
    $("#results").on( "change", "#numRecords", function (e){
        e.preventDefault();
        var numRecords  = $("#numRecords").val();
        $(".loading-div").show(); //show loading element
        var page = $(this).attr("data-page"); //get page number from link
        $("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName + "&ToDate=" + ToDate  + "&FromDate=" + FromDate,{"page":page}, function(){ //get content from PHP page
            $(".loading-div").hide(); //once done, hide loading element
            loadDataTable();
        });

    });
}
function loadDataTable(){
        $('#datatable_1').dataTable({
                "bPaginate": false,
                "bFilter": false,
                "bInfo": false,
                "bAutoWidth": false,
                "aoColumns": [
                            { "sWidth": "10%" },
                            { "sWidth": "10%" },
                            { "sWidth": "10%" },
                            { "sWidth": "10%","bSortable": false },
                            { "sWidth": "10%","bSortable": false },
                            { "sWidth": "10%","bSortable": false },
                            { "sWidth": "10%","bSortable": false },
                            { "sWidth": "10%","bSortable": false },
                            { "sWidth": "20%","bSortable": false }
                        ]
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
        window.location.href='<?php echo $etable; ?>_crud.php?mode=delete&id='+id;
    }
}
function genReport(eid){
    var rc = encodeURIComponent($("#print_info").html());
    $.ajax({
        type: "POST",
        url: "employee_ajax_genReport.php",
        data: 'eid='+eid+'&rc='+rc,
        beforeSend: function() {
            $(".transCover").fadeIn(800);
        },
        success: function(result){
                setTimeout(function(){
                    $(".transCover").fadeOut(100);
                    window.location.href=result;
                },1500);
            }
    });
}
function printPDF()
{
     var myWindow = window.open('','','width=800,height=800')
    myWindow.document.write("<style>th,tr,td{border:1px solid #000; padding:10px;}</style>"+$("#print_info").html());
    myWindow.print();

}
function genReportSalary(salary_id){
    var rc = encodeURIComponent($("#print_info_salary").html());
    $.ajax({
        type: "POST",
        url: "employee_salary_ajax_genReport.php",
        data: 'salary_id='+salary_id+'&rc='+rc,
        beforeSend: function() {
            $(".transCover").fadeIn(800);
        },
        success: function(result){ 
                setTimeout(function(){
                    $(".transCover").fadeOut(100);
                    window.location.href=result;
                },1500);
            }
    });
}
function printsalaryPDF()
{
    var myWindow = window.open('','','width=800,height=800')
    myWindow.document.write("<style>th,tr,td{border:1px solid #000; padding:10px;}</style>"+$("#print_info_salary").html());
    myWindow.print();

}
</script>
</body>
</html>