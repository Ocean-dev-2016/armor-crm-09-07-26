<?php
   $page_id=655;$page_slug='sales_executive_wise_report';
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
                                       <div class="col-md-2 col-xs-2 col-sm-2 " style="margin-top:10px">
                                          <?php
                                             $months = array('1'=>'January','2'=>'February','3'=>'March','4'=>'April','5'=>'May','6'=>'June','7'=>'July ','8'=>'August','9'=>'September','10'=>'October','11'=>'November','12'=>'December',);
                                          ?>
                                          <label>Month</label><br/>
                                          <select class="form-control" name="filter_month" id="filter_month" onchange="displayRecords()">
                                             <option value="">Select Month</option>
                                             <?php
                                             $_REQUEST['filter_month'] = (isset($_REQUEST['filter_month']) && $_REQUEST['filter_month']!="")?$_REQUEST['filter_month']:date('m');
                                              foreach ($months as $months_key => $months_value)
                                              {
                                             ?>
                                             <option <?= ($months_key==$_REQUEST['filter_month'])?"selected":""; ?> value="<?=$months_key?>"><?=$months_value?></option>
                                             <?php
                                              }
                                             ?>
                                          </select>
                                       </div>
                                       <div class="col-md-2 col-xs-2 col-sm-2" style="margin-top:10px;">
                                          <div class="form-group">
                                             <label>Select Sales Person</label>
                                             <select name="sales_executive_id" id="sales_executive_id" class="form-control" onchange="displayRecords()">
                                                <option value="">--Select Sales Person--</option>
                                                <?php
                                                $whereCustom = "";
                                                if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
                                                { 
                                                  if($rights['personal_flag']==1)
                                                  {
                                                    $check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];
                                                    $whereCustom .= " AND id='".$check_id."' ";
                                                  }
                                                  else
                                                  {
                                                    if($rights['chain_vise_flag'] == 1)
                                                    { 
                                                        $check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];

                                                        $get_sales_type=$db->rp_getValue("sales_executive","type","isDelete=0 AND id='". $check_id."'",0);
                                                        if ($get_sales_type== "sales_manager") 
                                                        {
                                                            $sales_executive_type = "Regional Sales Manager";
                                                            $key="sm_id";
                                                            $WhereCondition.=' ' .$key.'='.$check_id;
                                                        }

                                                        else if ($get_sales_type == "area_sales_manager") 
                                                        {
                                                            $sales_executive_type = "National Sales Manager";//Business Development Manager
                                                            $key="asm_id";
                                                            $WhereCondition.=' ' .$key.'='.$check_id;
                                                        }

                                                        else if ($get_sales_type == "sales_officer") 
                                                        {
                                                            $sales_executive_type = "Area Sales Manager";//Area Sales Manager
                                                            $key="so_id";
                                                            $WhereCondition.=' ' .$key.'='.$check_id;
                                                        }
                                                        else if ($get_sales_type == "sales_executive") 
                                                        {
                                                            $sales_executive_type = "Sales Officer";
                                                            $key="se_id";
                                                            $WhereCondition.=' ' .$key.'='.$check_id;
                                                        }
                                                        else
                                                        {
                                                            $WhereCondition.=' type = "service_engineer"';
                                                        }

                                                        $data = $db->rp_getData("sales_executive","id",$WhereCondition,"",0);

                                                        $SALEID1=array();
                                                        if($data)
                                                        {
                                                            while($data_d=mysqli_fetch_assoc($data))
                                                            {
                                                                $SALEID1[]=$data_d['id'];
                                                            }
                                                        }
                                                        if(!empty($SALEID1))
                                                        {
                                                            $SALEID1=implode(",", $SALEID1);
                    
                                                          $whereCustom .= "  AND id IN (".$SALEID1.','.$_SESSION[SITE_SESS.'REFERANCE_ID'].")";    
                                                        }
                                                        else
                                                        {
                                                                $whereCustom .= "  AND id IN (".$_SESSION[SITE_SESS.'REFERANCE_ID'].")";       
                                                        } 
                                                    }
                                                    else
                                                    {
                                                        $whereCustom="";

                                                    }
                                                  }
                                                }
                                                $SalesR = $db->rp_getData("sales_executive", "*",$whereCustom."isDelete=0 AND isActive=1");
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
           
            <?php include("footer.php"); ?>
            <?php include("include_js.php"); ?>
            <script type="text/javascript" src="assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
            <script type="text/javascript" src="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>
            <!-- <script type="text/javascript" src="assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script> -->
            <!-- <script type="text/javascript" src="js/fSelect.js"></script> -->
            <script type="text/javascript">
               // $("#sales_executive_type").fSelect();
               $("#sales_executive_id").select2();
               $("#filter_month").select2();
            </script>
            <script type="text/javascript">
              var sales_executive_id="";
               var data_url = "sales_executive_wise_report_get_ajax.php";
                
               function loadDataTable()
               {
                  $('#datatable_1').dataTable({
                     "bPaginate": false,
                     "bFilter": false,
                     "bInfo": false,
                     "bAutoWidth": false, 
                     "aoColumns": [ 
                     ]
                  });
               }

               function displayRecords(numRecords) {
                  var sales_executive_id    = $("#sales_executive_id").val(); 
                  var filter_month    = $("#filter_month").val(); 
                  $("#results" ).html("");
                     $('.preloader').fadeIn('slow');
                  $("#results" ).load( data_url+"?sales_executive_id=" + sales_executive_id + "&filter_month=" + filter_month,
                     function(){
                     $('.preloader').fadeOut('slow');

                     loadDataTable();
                     // alert(type);
                  }); 
               }
                
               
               $(document).ready(function() {
                  displayRecords(100,1);

               }); 

            </script>
         </body>
      </html>