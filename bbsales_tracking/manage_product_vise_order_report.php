<?php
   $page_id=619;$page_slug='product_vise_order_report';
   $ctable  = "orders";
   $ctable1    = "Product Wise Order Report";
   $main_page  = $ctable;
   $page       = "manage_".$ctable;
   $page_title = "Manage ".$ctable1." Reports";
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
            <!-- <link rel="stylesheet" type="text/css" href="assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css"/> -->
            <link rel="stylesheet" type="text/css" href="assets/global/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css"/>
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
                                          <label>Filter By Date</label>
                                           <div class="input-group">
                                             <input class="form-control datetimerange-picker-input " id="material_request_filter_input" value="<?php echo $date_filter_query; ?>" name="df" placeholder="Select Date Range" type="text" style="border-radius: 0;height: 34px!important;">
                                                <span class="input-group-addon datetimerange-picker-btn">
                                                         <i class="fa fa-calendar"></i>
                                                </span>
                                                   
                                                <span class="input-group-btn">
                                                   <button class="btn btn-success filterBtn" type="submit" value="search">Filter</button>
                                                </span>
                                          </div>
                                       </div>
                                       <div class="col-md-3 col-xs-3 col-sm-3" style="margin-top:10px;">
                                          
                                       </div>
                                       <div class="col-md-5 col-xs-5 col-sm-5 " style="margin-top:10px;">
                                          <div class="form-inline" role="form">
                                             <label>Search By Product Name :</label>
                                             <form class="form-inline" role="form" onSubmit="return searchByName();">
                                                <div class="form-group">
                                                   <input type="text" style="width:250px!important" placeholder="Search By Product Name :  " class="form-control input-small" name="searchName" id="searchName" value="" />
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
                                                         <!-- <li>
                                                            <a onClick="Importexcel(this)" data-toggle="modal" data-target="#uploadLeeds"><i class="fa fa-download"></i>Import</a>
                                                            </li> -->
                                                            <?php
                                                            if($rights['pdf_download_flag']==1 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
                                                         { 
                                                            ?>
                                                         <li>
                                                            <a name="print" onClick="genReportPrint()" title="Print Report"><i class="fa fa-print"></i>PDF</a>
                                                         </li>
                                                         <?php
                                                         }
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
                                       <!-- <br />
                                          <br /> -->
                                    </div>
                                    <div class="row">


                                       <div class="col-md-2 col-xs-2 col-sm-2" style="margin-top:10px;">
                                          <div class="form-group">
                                             <label>Select Cateogry</label>
                                                <select name="top_cat" id="top_cat" class="form-control" onChange="return Cateogry(this.value);">
                                                   <option value="">--Select Cateogry--</option>
                                                   <?php
                                                   $top_cat_r = $db->rp_getData("top_category_master", "*");
                                                   if (mysqli_num_rows($top_cat_r) > 0) {
                                                      while ($top_cat_d = mysqli_fetch_array($top_cat_r)) {
                                                   ?>
                                                         <option value="<?php echo $top_cat_d['id']; ?>" <?php if ($top_cat_d['name'] == $country) { ?> selected <?php } ?>><?php echo $top_cat_d['name']; ?></option>
                                                   <?php
                                                      }
                                                   }
                                                   ?>
                                             </select>
                                          </div>
                                       </div> 

                                       <div class="col-md-2 col-xs-2 col-sm-2" style="margin-top:10px;">
                                          <div class="form-group">
                                             <label>Select Sub Cateogry</label>
                                                <select name="cat_id" id="cat_id" class="form-control">
                                                   <option value="">--Select Sub Cateogry--</option>
                                                   
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
            <script type="text/javascript" src="assets/global/plugins/bootstrap-daterangepicker/moment.min.js"></script>
            <script type="text/javascript" src="assets/global/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
            <script type="text/javascript" src="js/fSelect.js"></script>
            <script type="text/javascript">
               $("#sales_executive_type").fSelect();
               // $("#state").select2("val");
               // $("#Source_of_inquiry").fSelect();
               // type = $("#sales_executive_type").val();
            </script>
            <script type="text/javascript">
               var searchName="";
               var sales_executive_id="";
               var data_url = "product_vise_order_report_get_ajax.php";
               $('#ToDate').datepicker({  datepicker: true, autoclose: true });
               $('#FromDate').datepicker({  datepicker: true, autoclose: true });
               var ToDate="";
               var FromDate="";
               var df1="";
               var status="";
               var type="";
               var cat_id="";
               var top_cat="";
               var flag="1";
               var isFillter=false;
               
               $('#myModal').on('show.bs.modal', function (event) {
                 var button = $(event.relatedTarget) // Button that triggered the modal
                 var requesting_id=button.data("id");
                  $("#requesting_ajax").attr("data-url","orders_information_get_ajax.php?id="+requesting_id);
                  $("#requesting_ajax").click();
               })

               function searchByName(){
                  searchName = $("#searchName").val();
                  type = $("#sales_executive_type").val();
                  top_cat = $("#top_cat").val();
                  cat_id = $("#cat_id").val();
                  df1 = $("#material_request_filter_input").val();
                  isFillter=true;
                  displayRecords(50,1);
                  return false;
               }
               
               function clearSearchByName(){
                  searchName = "";
                  ToDate = "";
                  FromDate = "";
                  status = "";
                  type = "";
                  cat_id = "";
                  top_cat = "";
                  df1 = "";
                  isFillter=false;

                  $("#sales_executive_type").select2("val","");
                  $("#material_request_filter_input").val("");
                  $("#searchName").val("");
                  $("#ToDate").val("");
                  $("#FromDate").val("");
                  $("#status").select2("val","");
                  $("#type").select2("val","");
                  $("#status").select2("val","");
                  displayRecords(50,1);
               }

               $(".filterBtn").on("click",function()
               {
                  df1=$("#material_request_filter_input").val();
                  // alert(df1);
                  sales_executive = $("#sales_executive").val();
                  df1 = encodeURI(df1)
                  displayRecords(50,1);
               })

               $("#searchName").keyup(function(event){
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
                     displayRecords(50,1);
                  }
                  else
                  {
                     alert("From Date Should Be Less Than To Date");
                  }
               }

               function searchBySales(sid)
               {
                  sales_executive_id=sid;
                  displayRecords(50,1);
               }

               function getType(tid)
               {
                  type=tid;
                  // alert(type);
                  displayRecords(50,1);
               }
               
               function loadDataTable(){
                  $.fn.dataTable.ext.errMode = 'none';
                  $('#datatable_1').dataTable({
                     "bPaginate": false,
                     "bFilter": false,
                     "bInfo": false,
                     "bAutoWidth": false, 
                     "aoColumns": [
                        { "sWidth": "5%" }, 
                        { "sWidth": "55%" },
                        { "sWidth": "10%" },
                        { "sWidth": "10%" }, 
                        { "sWidth": "10%" },
                        { "sWidth": "10%" },
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
                  $("#results" ).load( data_url+"?show=" + numRecords + "&searchName=" + searchName + "&df=" + df1 + "&FromDate=" + FromDate + "&sales_executive_id=" + sales_executive_id + "&type=" + type + "&flag=" + flag + "&top_cat=" + top_cat + "&cat_id=" + cat_id + "&isFillter="+isFillter ,function(){
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
                     $("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName + "&df=" + df1 + "&flag=" + flag + "&top_cat=" + top_cat + "&cat_id=" + cat_id,{"page":page}, function(){ //get content from PHP page
                        $(".loading-div").hide(); //once done, hide loading element
                        loadDataTable();
                     });
                  });

                  $("#results").on( "change", "#numRecords", function (e){
                     e.preventDefault();
                     var numRecords  = $("#numRecords").val();
                     $(".loading-div").show(); //show loading element
                     var page = $(this).attr("data-page"); //get page number from link
                     $("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName + "&df=" + df1 + "&flag=" + flag + "&top_cat=" + top_cat + "&cat_id=" + cat_id,{"page":page}, function(){ //get content from PHP page
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
                  displayRecords(50,1);
               });

               function del_conf(id,quotation_id){
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
                  var df1        = $("#material_request_filter_input").val();
                  df1            = encodeURIComponent(df1.trim());
                  var top_cat    = $("#top_cat").val();
                  var cat_id     = $("#cat_id").val();

                  var myWindow = window.open('product_vise_order_report_print.php?searchName='+searchName+'&type='+type+'&df='+df1+'&FromDate='+FromDate+"&top_cat="+top_cat+"&cat_id="+cat_id+"&flag="+flag +"&sales_executive_id="+sales_executive_id,'','width=700,height=800');
                  myWindow.print();
                  setTimeout(function () 
                  {
                     myWindow.print();
                     var ival = setInterval(function() 
                     {
                        myWindow.close();
                        clearInterval(ival);
                     }, 500);
                  }, 5000);
               }
               
               function genReport_excel()
               {
                  var searchName    = $("#searchName").val();
                  searchName  = encodeURIComponent(searchName.trim());
                  var df1=$("#material_request_filter_input").val();
                  var type = $("#sales_executive_type").val();
                  var top_cat = $("#top_cat").val();
                  var cat_id = $("#cat_id").val();
                  $.ajax({
                     method: "POST",
                     url: "product_vise_order_genReport_ajax.php",
                     data:{
                        searchName:searchName,
                        type:type,
                        df1:df1,
                        top_cat:top_cat,
                        cat_id:cat_id
                     }, 
                     dataType : 'json',
                     beforeSend: function()
                     {
                           
                     },
                     success: function(result){
                        // alert(result);
                        window.location.href="<?=SITEURL?>"+result.file_path;
                     },
                     /*error:function(result){
                        window.location.href="<?=SITEURL?>"+result.file_path;
                     }*/
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
               function Cateogry(val) 
               {
                  $.ajax({
                     type: "POST",
                     url: "ajax_get_Cateogry.php",
                     data: 'top_cat_id=' + val,
                     success: function(result) {
                        $("#cat_id").html(result);
                     }
                  });
               }
            </script>
         </body>
      </html>