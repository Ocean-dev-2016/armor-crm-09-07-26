<?php
$page_id=603;$page_slug='salesexecutive_performance_report_page';
/*
 * @author Ravi Patel
 */
include("connect.php");
include("../include/no_to_word.php");
$ctable       = "sales_executive";
$ctable1      = "Orders";
$ctable_where = "";
$area         = $_REQUEST['area'];

$today_date=date('Y-m-d',strtotime($_REQUEST['FromDate']));

  // echo $today_date;exit;




$Where='';$Where1='';$Where2='';$Where3='';$Where4='';$Where5='';$Where6='';
$Where.=" isDelete=0 ";


if(isset($_REQUEST['searchName']) && $_REQUEST['searchName'] != "")
{
    $Where.= " AND name LIKE '%".$_REQUEST['searchName']."%' ";
}

if(isset($_REQUEST['state']) && $_REQUEST['state'] != "")
{
    $Where.= " AND state='".$_REQUEST['state']."' ";
    $state=$_REQUEST['state'];
}

if(isset($_REQUEST['city']) && $_REQUEST['city'] != "")
{
    $Where.= " AND city='".$_REQUEST['city']."' ";
    // $city=$_REQUEST['city'];
}


// $Where .= (isset($_REQUEST['searchName']) && $_REQUEST['searchName'] != "")?" AND name LIKE %'".$_REQUEST['searchName']."'%":"";
$Where1 = (isset($_REQUEST['type']) && $_REQUEST['type'] != "")?" AND type_of_executive ='".$_REQUEST['type']."'":"";

if (isset($_REQUEST['ToDate']) && $_REQUEST['ToDate'] != "" && $_REQUEST['ToDate'] != NULL) {
    $Where2 = " AND order_date <= '" . date_format(date_create($_REQUEST['ToDate']), "Y-m-d") . "' ";
    $Where3 = " AND created_date <= '" . date_format(date_create($_REQUEST['ToDate']), "Y-m-d") . "' ";
    $Where4 = " AND complain_date <= '" . date_format(date_create($_REQUEST['ToDate']), "Y-m-d") . "' ";
    $Where5 = " AND inquiry_date <= '" . date_format(date_create($_REQUEST['ToDate']), "Y-m-d") . "' ";
    $Where6 = " AND invoice_date <= '" . date_format(date_create($_REQUEST['ToDate']), "Y-m-d") . "' ";
}

if (isset($_REQUEST['FromDate']) && $_REQUEST['FromDate'] != "" && $_REQUEST['FromDate'] != NULL) {
    $Where2 .= " AND order_date >= '" . date_format(date_create($_REQUEST['FromDate']), "Y-m-d") . "' ";
    $Where3 .= " AND created_date >= '" . date_format(date_create($_REQUEST['FromDate']), "Y-m-d") . "' ";
    $Where4 .= " AND complain_date >= '" . date_format(date_create($_REQUEST['FromDate']), "Y-m-d") . "' ";
    $Where5 .= " AND inquiry_date >= '" . date_format(date_create($_REQUEST['FromDate']), "Y-m-d") . "' ";
    $Where6 .= " AND invoice_date >= '" . date_format(date_create($_REQUEST['FromDate']), "Y-m-d") . "' ";
}

$date = ($_REQUEST['FromDate']!="")?" - ".$_REQUEST['FromDate']." TO ":"";
$date .=($_REQUEST['ToDate']!="")?$_REQUEST['ToDate']:"";

// $Query = "SELECT `sales_executive`.`id`, `sales_executive`.`name` AS name, ( SELECT name FROM `customer_type` WHERE id = `sales_executive`.`type_of_executive` ) AS type, `sales_executive`.`state` AS state, `sales_executive`.`city` AS city, ( SELECT COUNT(*) FROM `orders` WHERE isDelete = 0 AND sales_id=`sales_executive`.`id` ".$Where2." ) AS total_order, ( SELECT SUM(grand_total) FROM `orders` WHERE isDelete = 0 AND sales_id=`sales_executive`.`id` ".$Where2." ) AS total_order_value, ( SELECT COUNT(*) FROM `visit` WHERE isDelete = 0 AND user_id=`sales_executive`.`id` ".$Where3." ) AS total_visit, ( SELECT COUNT(*) FROM `complain` WHERE isDelete = 0 AND user_id=`sales_executive`.`id` ".$Where4." ) AS total_complain, ( SELECT COUNT(*) FROM `no_order_inquiry` WHERE isDelete = 0 AND sales_executive_id=`sales_executive`.`id` ".$Where5." ) AS total_inquiry, ( SELECT COUNT(*) FROM `invoice` WHERE isDelete = 0 AND  FIND_IN_SET(`sales_executive`.`id`,sales_executive_id) ".$Where6." ) AS total_invoice, ( SELECT SUM(amount) FROM `invoice` WHERE isDelete = 0 AND  FIND_IN_SET(`sales_executive`.`id`,sales_executive_id) ".$Where6." ) AS total_invoice_value FROM `sales_executive` WHERE isDelete = 0 ".$Where." ".$Where1.";";

$ctable_r = $db->rp_getData($ctable,"*",$Where,"id DESC",0);


// $ctable_r = $db->rp_getQuery($Query);
?>
<!-- <button type="button" class="btn red-haze export" name="export" onClick="genReport(this)" id="export" href="" title="Download PDF Report"><i class="fa fa-file-pdf-o"></i>&nbsp; Pdf</button>
<button type="button" class="btn green-haze export" name="export" onClick="genReportexcel(this)" id="export" href="" title="Download Excel Report"><i class="fa fa-file-excel-o"></i> &nbsp; Excel</button> -->
<div class="table-responsive">
<form action="" name="frm" id="print_info" method="post">
	<table id="datatable_1" class="table table-bordered table-striped dataTable" style="overflow: scroll; !important">
        <thead>
			<tr>
				<td class="header" align="center" colspan="14" ><h3><b>Daily Achievement Analysis Report - <?= date('d-m-Y',strtotime($today_date)) ?></b></h3></td>
			</tr>
            <tr>
                <td colspan="14"></td>
            </tr>
            <tr>
                <td colspan="2">Month :- <?php echo date('F',strtotime($today_date)); ?></td>
                <td colspan="3">Date  :- <?= date('d-m-Y',strtotime($today_date)) ?></td>
                <td class="state_s hide-this-on-excel class-state"> 
                    <select class="form-control status" name="state" id="state" onChange="filter_state(this.value);" autofocus >
                        <option value="">Select State</option>
                        <?php
                        $state_r = $db->rp_getData("class", "*"," isDelete=0 ", 0);
                        if (mysqli_num_rows($state_r) > 0) {
                            while ($state_d = mysqli_fetch_array($state_r)) {
                        ?>
                            <option value="<?php echo $state_d['name']; ?>" <?= ($state == $state_d['name']) ? "selected" : ""; ?>><?php echo $state_d['name']; ?></option>
                        <?php
                            }
                        }
                        ?>
                    </select>
                
                
                   
                </td>
                <td class="city_s hide-this-on-excel class-city">
                    
                     <select class="form-control status" name="city" id="city" autofocus >
                        <option value="">Select City</option>
                    </select>
                </td>
                <td colspan="10"></td>
            </tr>
            <tr class="tr">
                <th class="th size-sr">No.</th>
                <th class="th">Sales Person Name</th>
                <th class="th">in</th>
                <th class="th">Out</th>
                <th class="th">Leave</th>
                <th class="th">State</th>
                <th class="th">City</th>
                <th class="th">Visit</th>
				<th class="th">Order</th>
				<th class="th">Amount</th>
				<th class="th">Target</th>
				<th class="th">Achivement</th>
				<th class="th">Diffrence (RS)</th>
                <th class="th">Diffrence (%)</th>
			</tr>
        </thead>
        <tbody>
        <?php
        if(mysqli_num_rows($ctable_r)>0){
            $count = 1;
            $totalvisitfull = 0;
            $totalorderfull = 0;
            $totalamountfull = 0;
            while($ctable_d = mysqli_fetch_array($ctable_r)){
        ?>
            <tr class="tr">
                <td class="td" style="width:50px;">
                	<?php echo $count++; ?>
                </td>
                <td class="td">
                	<?php echo stripslashes($ctable_d['name']); ?>
                </td>
                <td class="td">
                	<?php  
                    $in = $db->rp_getValue("attendance","date_time","inout_status='in' AND  sales_id='".$ctable_d['id']."' AND DATE(date_time)='".$today_date."'",0);
                    if($in!="")
                    {
                        echo date('H:i A',strtotime($in));
                    }
                    else
                    {
                        echo "-";
                    }
                    ?>
                </td>
                <td class="td">
                	<?php 
                    $out = $db->rp_getValue("attendance","date_time","inout_status='out'    
                        AND  sales_id='".$ctable_d['id']."' AND DATE(date_time)='".$today_date."'",0); 
                    if($out)
                    {
                        echo date('H:i A',strtotime($out));
                    }
                    else
                    {
                        echo "-";
                    }
                    ?>
                </td>
                <td class="td text-right">
                	<?php echo $total_leave=$db->rp_getValue("leave_request","count(*)","isDelete=0 AND status=1 AND sales_executive_id='".$ctable_d['id']."' AND DATE(created_date)='".$today_date."'",0); ?>
                </td>
                <td class="td text-right">
                	<?php 
                        echo $ctable_d['state'];
                    ?>
                </td>
                 <td class="td text-right">
                    <?php 
                        echo $ctable_d['city'];
                    ?>
                </td>
                <td class="td text-right">
                	<?php 
                        echo $total_visit=$db->rp_getValue("visit","count(*)","isDelete=0 AND user_id='".$ctable_d['id']."' AND DATE(created_date)='".$today_date."' ",0);
                        $totalvisitfull += $total_visit;
                    ?>
                </td>
                <td class="td text-right">
                	<?php 
                        echo $total_order_a=$db->rp_getValue("orders","count(*)","isDelete=0 AND sales_id='".$ctable_d['id']."' AND order_date='".$today_date."' AND (status='1' OR status='2')",0)." A  <br/>";
                        $totalorderfull_approve += $total_order_a;
                       
                        echo $total_order_p=$db->rp_getValue("orders","count(*)","isDelete=0 AND sales_id='".$ctable_d['id']."' AND order_date='".$today_date."' AND status='0'",0)." P";
                        $totalorderfull_disapprove += $total_order_p;
                     ?>
                </td>
                <td class="td text-right">
                	<?php 
                        $total_amount_Approve = $db->rp_getValue("orders","sum(grand_total)","isDelete=0 AND sales_id='".$ctable_d['id']."' AND order_date='".$today_date."' AND (status='1' OR status='2')",0);

                       echo  ceil($total_amount_Approve)." A <br/>";
                       
                       $total_amount_Disaprove = $db->rp_getValue("orders","sum(grand_total)","isDelete=0 AND sales_id='".$ctable_d['id']."' AND order_date='".$today_date."' AND status='0'",0);
                       
                       echo  ceil($total_amount_Disaprove)." P";
                       
                       $totalamountfull_Approve += $total_amount_Approve;
                       $totalamountfull_disapprove += $total_amount_Disaprove;
                     ?>
                </td>
                <td class="td text-right">
                	<?php
                         echo $total_target=$db->rp_getValue("target","target_amount","isDelete=0 AND sales_executive_id='".$ctable_d['id']."' AND target_month='".date('F',strtotime($today_date))."'",0);  
                    ?>
                </td>
                <td class="td text-right">
                	<?php 


                        $total_amount_achivement=$db->rp_getValue("orders","sum(grand_total)","isDelete=0 AND (status='1' OR status='2') AND sales_id='".$ctable_d['id']."' AND MONTH(order_date)='".date('m',strtotime($today_date))."'",0);
                       echo  $db->rp_number_format(( $total_amount_achivement),2);
                     ?>

                </td>
                <td><?php
                    if($total_target == "")
                    {
                        echo "";
                    }
                    else
                    {
                        $total_diffrence=$total_target-$total_amount_achivement; echo $total_diffrence;
                    }
                  

                  ?>
                     
                 </td>
                 <td>
                    <?php
                    if($total_target == "")
                    {
                        echo "";
                    }
                    else
                    {
                        $total_diffrence_inper = (($total_amount_achivement*100)/$total_target);
                        echo $total_diffrence_inper." %";
                    }
                    ?>
                </td>

                 <!-- --- milan --- 22-06-2021 --- -->
                <?php
                $t_target += $total_target;
                $t_amount_achivement += $total_amount_achivement;
                // if($total_target == "")
                // {

                // }
                // else
                // {
                //     $t_difference = $total_target - $total_amount_achivement;
                //     $t_t_difference +=$t_difference;
                // }
                ?>
                <!-- --- milan --- 22-06-2021 --- -->
                 
            </tr>
            
        <?php
            }
        }
		
        ?>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td><b>Total</b></td>
            <td class="text-right">
                <?php 
                    echo $totalvisitfull;
                ?>
            </td>
            <td class="text-right">
                <?php 
                    
                    echo ceil($totalorderfull_approve). " A <br/>";
                    echo ceil($totalorderfull_disapprove). " P <br/>";
                ?>
            </td>
            <td class="text-right">
                <?php 
                    // echo ceil($totalamountfull_Approve). " A <br/>";
                    // echo ceil($total_amount_Disaprove). " P <br/>";
                    echo ceil($totalamountfull_Approve). " A <br/>";
                    echo ceil($totalamountfull_disapprove). " P <br/>";
                ?>
            </td>
            <!-- --- milan --- 22-06-2021 --- -->
            <td class="text-right"><?= $t_target ?></td>
            <td class="text-right"><?= $db->rp_number_format(($t_amount_achivement),2) ?></td>
            <td></td>
            <td></td>
            <!-- <td><?= $t_t_difference ?></td> -->
            <!-- --- milan --- 22-06-2021 --- -->
        </tr>
        </tbody>
    </table>
    
</form>
</div>
<div id="loading" class="modal fade" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body portlet box white">
				<div class="portlet-title" style="color:black;">
					<div class="caption">Loading.......
					<img src="../images/loading-spinner-blue.gif">
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- <button type="button" class="btn red-haze export" name="export" onClick="genReport(this)" id="export" href="" title="Download PDF Report"><i class="fa fa-file-pdf-o"></i> &nbsp PDF</button>
<button type="button" class="btn green-haze export" name="export" onClick="genReportexcel(this)" id="export" href="" title="Download Excel Report"><i class="fa fa-file-excel-o"></i>&nbsp; Excel</button -->
<script>

    $("#state").select2();
    $("#city").select2();
function genReport(cid){
    
	if($("#datatable_1").find("tbody").find("tr").length>=1)
	{

    $("#state").select2("destroy");
    $("#city").select2("destroy");
    var state_id = $("#state").val();
    var city_id = $("#city").val();
    $("#state").remove();
    $("#city").remove();
	var rc = encodeURIComponent($("#print_info").html());
	
	$.ajax({
		type: "POST",
		url: "ordersReport_gen_ajax.php",
		data: '&rc='+rc,
		beforeSend: function() {
			$(".transCover").fadeIn(800);
			$("#loading").modal('show');
		},
		success: function(result){ 
		//alert(result);
				setTimeout(function(){
					$(".transCover").fadeOut(100);
					alert("Report file generated!!");
					$("#loading").modal('hide');
					window.open(
					  result,
					  '_blank' // <- This is what makes it open in a new window.
					);
                    getCityStateBack(state_id,city_id);

                    // $("#state").show();
                    // $("#city").show();

                    // $("#state").select2();
                    // $("#city").select2();
					// window.location.href=result;
				},1500);
			}
	});
}
else
{
	toastr.error("Report Can't generated");
}

}





function filter_state(state_id, city = "") {
        $.ajax({
            type: "POST",
            url: "find_city.php",
            data: 'state_id=' + state_id + "&city=" + city,
            beforeSend: function() {
                $("#loading-modal").modal('show');
            },
            success: function(data) {
                $("#city").select2("destroy");
                $("#city").html(data);
                $("#city").select2();
                $("#loading-modal").modal('hide');
            }
        });
    }
</script>
<?php require_once 'disconnect.php';  ?>