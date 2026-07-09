<?php
   $page_id=603;$page_slug='salesexecutive_performance_report_page';
   $ctable  = "orders";
   $ctable1    = "Sales Officer Wise";
   $main_page  = $ctable;
   $page       = "manage_".$ctable;
   $page_title = "Manage ".$ctable1;
   $page_hierarchy=array(array("link"=>"","title"=>"Reports"),array("link"=>$ctable."_manage.php","title"=>"Manage ".$ctable1));
   include("connect.php");
   $FromDate="";
   $ToDate="";
   ?>
<!DOCTYPE html>
<!--[if IE 8]> 
<html lang="en" class="ie8 no-js">
   <![endif]-->
   <!--[if IE 9]> 
   <html lang="en" class="ie9 no-js">
      <![endif]-->
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
            <link rel="stylesheet" type="text/css" href="css/fSelect.css"/>
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
                        <div class="col-xl-12">
                           <?php $db->printErrorMessage(); ?>
                           <?php $db->printSuccessMessage(); ?>
                        </div>
                        <div class="col-xl-12 ">
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
                                 <div class="slimScrollDiv" style="position: relative;width: auto; height: auto;">
                                    <div class="row">
                                       <div class="col-md-4  col-xs-4  col-sm-4" style="margin-top:10px">
                                          <div class="form-inline" role="form">
                                             <div class="form-group">
                                                <label>Filter By Order Date : &nbsp;</label></br>
                                                <input type="text"  name="FromDate" class="form-control input-small" id="FromDate" value="<?php echo $FromDate; ?>" placeholder="From Date">
                                             </div>
                                             <div class="form-group">
                                                <label>&nbsp;&nbsp;</label></br>
                                                <input type="text"  name="ToDate" class="form-control input-small" id="ToDate" value="<?php echo $ToDate; ?>" placeholder="To Date">
                                             </div>
                                             <div class="form-group">
                                                <label>&nbsp;&nbsp;</label></br>
                                                <input class="btn btn-danger btn-sm" type="submit" value="Filter" onClick="getByDate();">
                                             </div>
                                          </div>
                                       </div>
                                       <div class="col-md-3 col-xs-3 col-sm-3" style="margin-top:10px;">
                                          <div class="form-group">
                                             <label>Search by Sales Officer Type</label>
                                             <select class="form-control input-large status" multiple="multiple" name="sales_executive_type" id="sales_executive_type" >
                                                <!-- <option value=""> Select Sales Officer Type </option>
                                                <option value="sales_manager" <?= ($sales_executive_type=="sales_manager")?"selected":""; ?>>Regional Sales Manager</option>
                                                <option value="area_sales_manager" <?= ($sales_executive_type=="area_sales_manager")?"selected":""; ?>>Business Development Manager</option>
                                                <option value="sales_officer" <?= ($sales_executive_type=="sales_officer")?"selected":""; ?>>Area Sales Manager</option>
                                                <option value="sales_executive" <?= ($sales_executive_type=="sales_executive")?"selected":""; ?>>Sales Officer</option> -->

                                                <option value="sales_manager" <?= ($sales_executive_type=="sales_manager")?"selected":""; ?>>Regional Sales Manager</option>
                                               <option value="area_sales_manager" <?= ($sales_executive_type=="area_sales_manager")?"selected":""; ?>>Business Development Manager</option>

                                               <!-- <option value="dispatch_sales_manager" <?= ($sales_executive_type=="dispatch_sales_manager")?"selected":""; ?>>Dispatch Manager</option> -->
                                               
                                               <option value="sales_officer" <?= ($sales_executive_type=="sales_officer")?"selected":""; ?>>Area Sales Manager</option>
                                               <option value="sales_executive" <?= ($sales_executive_type=="sales_executive")?"selected":""; ?>>Sales Officer</option>
                                               <!-- <option value="service_executive" <?= ($sales_executive_type=="service_executive")?"selected":""; ?>>Service Executive</option> -->
                       
                                             </select>
                                          </div>
                                       </div>
                                       <div class="col-md-5 col-xs-5 col-sm-5 " style="margin-top:10px;">
                                          <div class="form-inline" role="form">
                                             <label>Search By Sales Person Name :</label>
                                             <form class="form-inline" role="form" onSubmit="return searchByName();">
                                                <div class="form-group">
                                                   <input type="text" style="width:250px!important" placeholder="Search By Sales Officer Name :  " class="form-control input-small" name="searchName" id="searchName" value="" />
                                                </div>
                                                <div class="form-group">
                                                   <input class="btn btn-danger btn-sm" type="submit" value="search">
                                                </div>
                                                <div class="form-group">
                                                   <input class="btn btn-success btn-sm" type="button" value="clear" onClick="clearSearchByName();">
                                                </div>
                                                <div class="form-group">
                                                   <div class="btn-group">
                                                      <button aria-expanded="false" data-toggle="dropdown" type="button" class="btn btn-sm blue dropdown-toggle">
                                                      <i class="fa fa-gear"></i>
                                                      </button>
                                                      <ul role="menu" class="dropdown-menu dropdown-menu-right pull-right" >
                                                         <?php
                                                         if($rights['pdf_download_flag']==1 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
                                                         { 
                                                            ?>
                                                            <li>
                                                               <a name="print" onClick="genReportPrint()" title="Print Report"><i class="fa fa-print"></i>Print</a>
                                                            </li>
                                                            <?php
                                                         }
                                                         ?>

                                                         <?php
                                                         if($rights['export_excel_flag']==1 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
                                                         {
                                                            ?>
                                                            <li>
                                                               <a class="excel" name="excel" onClick="genReport_excel()" id="excel" title="Download XL Report"><i class="fa fa-file-excel-o"></i>Excel</a>
                                                            </li>
                                                            <?php
                                                         }
                                                         ?>
                                                      </ul>
                                                   </div>
                                                </div>
                                             </form>
                                          </div>
                                       </div>
                                    </div>
                                    <div class="row">
                                       <div class="col-md-2 col-xs-2 col-sm-2" style="margin-top:10px;">
                                          <div class="form-group">
                                             <label>Select Sales Person</label>
                                             <select name="sales_executive_id" id="sales_executive_id" class="form-control" multiple="multiple">
                                                <option value="">--Select Sales Person--</option>
                                                <?php
                                                if ($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0) 
                                                {
                                                      $whereCustom = "";
                                                      $whereCustom = "isDelete=0 AND isActive=1";

                                                      if($rights['personal_flag']==1)
                                                   {
                                                      $whereCustom .= " AND id='" . $_SESSION[SITE_SESS.'REFERANCE_ID'] . "'";
                                                   }
                                                   else 
                                                   {
                                                      if($rights['chain_vise_flag'] == 1)
                                                      {  
                                                         $exeType = $db->rp_getValue("sales_executive","type","id = '".$_SESSION[SITE_SESS.'REFERANCE_ID']."'");
                                                         // echo $exeType;
                                                         if($exeType=='sales_manager')
                                                         { 
                                                            $whereCustom .= " AND (sm_id = '".$_SESSION[SITE_SESS.'REFERANCE_ID']."' OR id = '".$_SESSION[SITE_SESS.'REFERANCE_ID']."')";
                                                         }
                                                         else if($exeType=='area_sales_manager' || $exeType=='dispatch_sales_manager')
                                                         { 
                                                            $whereCustom .= " AND (asm_id = '".$_SESSION[SITE_SESS.'REFERANCE_ID']."' OR id = '".$_SESSION[SITE_SESS.'REFERANCE_ID']."')";
                                                         }
                                                         else if($exeType=='sales_officer')
                                                         { 
                                                            $whereCustom .= " AND (so_id = '".$_SESSION[SITE_SESS.'REFERANCE_ID']."' OR id = '".$_SESSION[SITE_SESS.'REFERANCE_ID']."')";
                                                         }
                                                         else
                                                         {
                                                            $whereCustom .= " AND id = '".$_SESSION[SITE_SESS.'REFERANCE_ID']."'";
                                                         }
                                                      }
                                                   }
                                                }
                                                $SalesR = $db->rp_getData("sales_executive", "*",$whereCustom,"",0);
                                                if (mysqli_num_rows($SalesR) > 0) 
                                                {
                                                   while ($SalesD = mysqli_fetch_array($SalesR)) 
                                                   {
                                                      ?>
                                                      <option value="<?php echo $SalesD['id']; ?>" <?=($SalesD['id']==$_SESSION[SITE_SESS.'REFERANCE_ID'])?"selected":"";?> <?php echo ($sales_executive_id==$SalesD['id'])?"selected":"" ; ?> ><?php echo $SalesD['name']; ?></option>
                                                      <?php
                                                   }
                                                }
                                                ?>
                                             </select>
                                          </div>
                                       </div>
                                       <div class="col-md-2 col-xs-2 col-sm-2" style="margin-top:10px;">
                                          <div class="form-group">
                                             <label>Select State</label>
                                             <select name="state" id="state" class="form-control" onChange="return City(this.value);">
                                                <option value="">--Select State--</option>
                                                <?php
                                                $country_r = $db->rp_getData("state", "*");
                                                if (mysqli_num_rows($country_r) > 0) 
                                                {
                                                   while ($country_d = mysqli_fetch_array($country_r)) 
                                                   {
                                                     ?>
                                                      <option value="<?php echo $country_d['name']; ?>" <?php if ($country_d['name'] == $country) { ?> selected <?php } ?>><?php echo $country_d['name']; ?></option>
                                                     <?php
                                                   }
                                                }
                                                ?>
                                             </select>
                                          </div>
                                       </div> 

                                       <div class="col-md-2 col-xs-2 col-sm-2" style="margin-top:10px;">
                                          <div class="form-group">
                                             <label>Select City</label>
                                             <select name="city" id="city" class="form-control">
                                                <option value="">--Select City--</option>
                                                <?php
                                                if ($mode == 'edit') 
                                                {
                                                   $city_name = $db->rp_getValue("city", "name", "name='" . $city . "'", 0);
                                                   ?>
                                                   <option value="<?php echo $city; ?>" <?php echo $city_name ?> selected> <?php echo $city_name; ?> </option>
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
                        <div class="col-xl-12">
                           <div class="portlet light">
                              <div class="portlet-body">
                                 <div class="loading-div" style="display:none;"> <img src="assets/admin/layout/img/ajax-loader.gif" alt="" style="margin-bottom: 10%;;margin-top:10%;padding-left:48%;" > </div>
                                 <div id="results">
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div id="myModal" class="modal fade" data-backdrop="static" data-keyboard="false">
               <div class="modal-dialog">
                  <div class="modal-content">
                     <div class="modal-body portlet box blue">
                        <div class="portlet-title">
                           <div class="caption">
                              <i class="fa fa-gift"></i>View Order Information 
                           </div>
                           <div class="tools">
                              <a href="javascript:;" id="requesting_ajax" data-load="true" data-url="" class="reload" data-original-title="" title=""><i class="fa fa-reload"></i> </a>
                              <a href="javascript:;" data-original-title="" title="" data-dismiss="modal" style="color:white;"> <i class="fa fa-close"></i></a>
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
            <script type="text/javascript" src="js/fSelect.js"></script>
            <script type="text/javascript">
               $("#sales_executive_type").fSelect();
               $("#sales_executive_id").fSelect();
            </script>
            <script type="text/javascript">
               var searchName="";
               var sales_executive_id="";
               var data_url = "sales_executive_performance_report_get_ajax.php";
               $('#ToDate').datepicker({  datepicker: true, autoclose: true });
               $('#FromDate').datepicker({  datepicker: true, autoclose: true });
               var ToDate="";
               var FromDate="";
               var status="";
               var type="";
               var sales_executive_id="";
               var city="";
               var state="";
               var flag="1";
               var isFillter=false;

               $('#myModal').on('show.bs.modal', function (event) {
                 var button = $(event.relatedTarget) // Button that triggered the modal
                 var requesting_id=button.data("id");
                  $("#requesting_ajax").attr("data-url","orders_information_get_ajax.php?id="+requesting_id);
                  $("#requesting_ajax").click();
               })

               var admintype = "<?=$_SESSION[SITE_SESS.'_ADMIN_TYPE']?>";
               if(admintype!=0)
               {
                  searchByName();
               }  

               function searchByName()
               {
                  searchName = $("#searchName").val();
                  sales_executive_id = $("#sales_executive_id").val();
                  type = $("#sales_executive_type").val();
                  state = $("#state").val();
                  city = $("#city").val();
                  isFillter = true;
                  displayRecords(100,1);
                  return false;
               }

               function clearSearchByName()
               {
                  searchName = "";
                  ToDate = "";
                  FromDate = "";
                  status = "";
                  type = "";
                  sales_executive_id = "";
                  city = "";
                  state = "";
                  isFillter=false;


                  $("#sales_executive_id").select2("val","");
                  $("#sales_executive_id").fSelect("destroy");
                  $("#sales_executive_id").val("");
                  $("#sales_executive_id").fSelect("create");

                  $("#sales_executive_type").fSelect("destroy");
                  $("#sales_executive_type").val("");
                  $("#sales_executive_type").fSelect("create");

                  $("#type").fSelect("destroy");
                  $("#type").val("");
                  $("#type").fSelect("create");

                  // $("#sales_executive_type").select2("val","");
                  $("#searchName").val("");
                  $("#ToDate").val("");
                  $("#FromDate").val("");
                  $("#status").select2("val","");
                  $("#type").select2("val","");
                  $("#status").select2("val","");
                  displayRecords(100,1);
               }

               $("#searchName").keyup(function(event)
               {
                  if(event.keyCode == 13){
                     $("#searchByName").click();
                  }
               });

               function getByDate() 
               {
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
               
               function getType(tid)
               {
                  type=tid;
                  // alert(type);
                  displayRecords(100,1);
               }
               
               function loadDataTable()
               {
                  $('#datatable_1').dataTable({
                     "bPaginate": false,
                     "bFilter": false,
                     "bInfo": false,
                     "bAutoWidth": false, 
                     "aoColumns": [
                        // { "sWidth": "5%" }, 
                        // { "sWidth": "5%" },
                        // { "sWidth": "5%" },
                        // { "sWidth": "5%" }, 
                        // { "sWidth": "5%" },
                        // { "sWidth": "5%" },
                        // { "sWidth": "5%" },
                        // { "sWidth": "5%" },
                        // { "sWidth": "5%" },
                        // { "sWidth": "5%" },
                        // { "sWidth": "5%" },
                        // { "sWidth": "5%" },
                     ]
                  });
               }

               function displayRecords(numRecords) {
                  var searchName    = $("#searchName").val();

                  searchName  = encodeURIComponent(searchName.trim());
                  $('.preloader').fadeIn('slow');
                  $("#results" ).html("");
                  $("#results" ).load( data_url+"?show=" + numRecords + "&searchName=" + searchName + "&ToDate=" + ToDate + "&FromDate=" + FromDate + "&sales_executive_id=" + sales_executive_id + "&type=" + type + "&flag=" + flag + "&state=" + state + "&city=" + city + "&isFillter=" + isFillter ,function(){
                     $('.preloader').fadeOut('slow');
                     loadDataTable();
                     // alert(type);
                  }); //load initial records
                  
                  //executes code below when user click on pagination links
                  $("#results").on( "click", ".paging_simple_numbers a", function (e){
                     e.preventDefault();
                     var numRecords  = $("#numRecords").val();
                     $(".loading-div").show(); //show loading element
                     var page = $(this).attr("data-page"); //get page number from link
                     $("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName + "&type=" + type + "&flag=" + flag + "&state=" + state + "&city=" + city,{"page":page}, function(){ //get content from PHP page
                        $(".loading-div").hide(); //once done, hide loading element
                        loadDataTable();
                     });
                  });

                  $("#results").on( "change", "#numRecords", function (e){
                     e.preventDefault();
                     var numRecords  = $("#numRecords").val();
                     $(".loading-div").show(); //show loading element
                     var page = $(this).attr("data-page"); //get page number from link
                     $("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName + "&type=" + type + "&flag=" + flag + "&state=" + state + "&city=" + city,{"page":page}, function(){ //get content from PHP page
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

               function del_conf(id,quotation_id)
               {
                  var r = confirm("Are you sure you want to delete?");
                  if(r){
                     window.location.href='<?php echo $ctable; ?>_crud.php?mode=delete&id='+id +'&flag=1';
                  }
               }
               
               function confirmChange(id) 
               {
                  var r=confirm("Are you sure to forward order to production department?");
                  if(r)
                  {
                     window.location.href="orders_crud.php?mode=isActive&id="+id+"&status=1";
                  }
               }
               
               function genReportPrint()
               {
                  var searchName = $("#searchName").val();
                  searchName     = encodeURIComponent(searchName.trim());
                  var type       = $("#sales_executive_type").val();
                  var state      = $("#state").val();
                  var city       = $("#city").val();

                  var myWindow = window.open('print_sales_execute_report.php?searchName='+searchName+'&type='+type+'&ToDate='+ToDate+'&FromDate='+FromDate+"&state="+state+"&city="+city+"&flag="+flag +"&sales_executive_id="+sales_executive_id,'','width=700,height=800');
                  myWindow.print();
                  setTimeout(function () 
                  {
                     myWindow.print();
                     var ival = setInterval(function() 
                     {
                        myWindow.close();
                        clearInterval(ival);
                     }, 200);
                  }, 100000);
               }
               
               function genReport_excel()
               {
                  var searchName    = $("#searchName").val();
                  searchName  = encodeURIComponent(searchName.trim());
                  var type = String($("#sales_executive_type").val());
                  var sales_executive_id = String($("#sales_executive_id").val());
                  var state = $("#state").val();
                  var city = $("#city").val();
                  $.ajax({
                       method: "POST",
                       url: "sales_executive_performance_genReport_ajax.php",
                       data:{
                        searchName:searchName,
                        type:type,
                        state:state,
                        sales_executive_id:sales_executive_id,
                        city:city,
                        ToDate:ToDate,
                        FromDate:FromDate,
                     }, 
                     dataType : 'json',
                     beforeSend: function()
                     {
                        
                     },
                     success: function(result){
                        window.location.href="<?=SITEURL?>"+result.file_path;
                     },
                  });
               }
               
               function printPDF() 
               {
                  var myWindow = window.open('','','width=700,height=800')
                  myWindow.document.write("<style>th,tr,td{border:1px solid #000; padding:10px;}</style>"+$("#print_info").html());
                  myWindow.print();
               }

            </script>

            <script type="text/javascript">
               function State(val) {
                  $.ajax({
                     type: "POST",
                     url: "ajax_get_state.php",
                     data: 'cid=' + val,     
                     success: function(result) {
                        $("#state").html(result);
                     }
                  });
               }
               
               function City(val) {
                  $.ajax({
                     type: "POST",
                     url: "ajax_get_main_city.php",
                     data: 'sid=' + val,
                     success: function(result) {
                        $("#city").html(result);
                     }
                  });
               }

function genReportexcel(cid)
{
   var rc = encodeURIComponent($("#print_info").html());
   // alert(rc);

   $.ajax({
      type: "POST",
      url: "item_wise_sales_report_excel.php",
      data: '&rc='+rc,
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