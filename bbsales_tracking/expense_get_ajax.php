<?php
$page_id=592;$page_slug='expense_page';
include("connect.php");
$ctable = "expense";
$amounts=array();
$ctable_where = "isDelete=0 AND isActive=1";
$name="";
$expense_date="";
$items="";
$total_t="";
$month_id="";
// Get the total number of rows in the table

$date = date("m");

$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"]) ) ? intval($_REQUEST["show"]) : 100;

if(isset($_REQUEST["page"])){
	$page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
	if(!is_numeric($page_number)){die('Invalid page number!');} //incase of invalid page number
}else{
	$page_number = 1; //if there's no page number, set it to 1
}
////filter by month

if($_REQUEST['expense_month'] != "" || $_REQUEST['expense_year'] != "")
{
	if($_REQUEST['expense_month'] != "")
	{
		$ctable_where.=" AND MONTH(expense_date)='".$_REQUEST['expense_month']."' ";
	}
	if($_REQUEST['expense_year'] != "")
	{		
		$ctable_where.=" AND YEAR(expense_date)='".$_REQUEST['expense_year']."' ";
	}
}
else
{
	if(isset($_REQUEST['df']) && $_REQUEST['df']!=""){
		//echo $_REQUEST['df'];exit;
		$date_filter_query = urldecode( $_REQUEST['df'] );

		$date_filter_query_ex=explode(" to ",$date_filter_query);

		$ctable_where .= " AND ( DATE(expense_date)>='".date("Y-m-d",strtotime($date_filter_query_ex['0']))."' AND DATE(expense_date)<='".date("Y-m-d",strtotime($date_filter_query_ex['1']))."'  ) ";
	}
	else
	{
		$ctable_where .= "  AND MONTH(expense_date)='".$date."'";
	}	
}

if(isset($_REQUEST['expense_status']) && $_REQUEST['expense_status']!="" && $_REQUEST['expense_status']!=NULL)
{
 $ctable_where .= " AND expense_status = '".$_REQUEST['expense_status']."' ";
}
if (isset($_REQUEST['todate']) && $_REQUEST['todate'] != "" && $_REQUEST['todate'] != NULL && $_REQUEST['todate'] != undefined && $_REQUEST['todate']!="01-01-1970") {
	$ctable_where .= " AND DATE(expense_date) >= '" . $_REQUEST['todate'] . "' ";
}

if (isset($_REQUEST['fromdate']) && $_REQUEST['fromdate'] != "" && $_REQUEST['fromdate'] != NULL && $_REQUEST['fromdate'] != undefined  && $_REQUEST['fromdate']!="01-01-1970") {
	$ctable_where .= " AND DATE(expense_date) <= '" .$_REQUEST['fromdate']. "' ";
}

if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
{
	if($rights['personal_flag']==1)
	{
		$check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];
    	$ctable_where .= " AND sales_executive_id='".$check_id."' ";
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
				$ctable_where .= "  AND sales_executive_id IN (".$SALEID1.','.$_SESSION[SITE_SESS.'REFERANCE_ID'].")";	 
			}
			else
			{
				$ctable_where .= "  AND sales_executive_id IN (".$_SESSION[SITE_SESS.'REFERANCE_ID'].")";		
			}
	 	} 
	}
}

if(isset($_REQUEST['sales_executive_id']) && $_REQUEST['sales_executive_id']!="" && $_REQUEST['sales_executive_id']!=NULL && $_REQUEST['sales_executive_id']!=0)
{
 $ctable_where .= " AND sales_executive_id = '".$_REQUEST['sales_executive_id']."' ";
}
$get_total_rows = $db->rp_getTotalRecord($ctable,$ctable_where,0); //hold total records in variable
//break records into pages
$total_pages = ceil($get_total_rows/$item_per_page);

//get starting position to fetch the records
$page_position = (($page_number-1) * $item_per_page);

$ctable_r = $db->rp_getData($ctable,"DISTINCT sales_executive_id",$ctable_where,"id DESC limit $page_position, $item_per_page",0);
if($_REQUEST['sales_executive_id']!="")
{ 
?>
<style>
.table-scrollable {
		width: auto;
		height: 450px;
		overflow-x: scroll;
		overflow-y: scroll;
		border: 1px solid #e7ecf1;
		margin: 10px 0 !important;
	}

.fix-th
    {
        background-color: #f5f5f5 !important;position: sticky;top: 0; z-index: 1;
    }
    .fix-th1
    {
        background-color: #e5e5e5 !important;position: sticky;top: 0; z-index: 1;
    }
</style>
<!-- <button type="button" class="btn red-haze pull-right" name="excel" id="excel" title="Download XL Report" onClick="genReportPdf(1);"><i class="fa fa-file-pdf-o"></i>&nbsp; PDF</button>
<button type="button" class="btn green-haze excel pull-right" style="margin-bottom:10px;" name="excel" id="excel" href="" title="Download XL Report"><i class="fa fa-file-excel-o"></i>Excel</button> -->
<form action="" id="print_info1" name="frm" id="frm" method="post">
	<div class="table-scrollable" >
	<table class="table table-striped table-bordered table-hover" >
        <thead class="fix-th">
		<tr>
			<td colspan="33" style="text-align: center;"><h3><b>EXPENSE REPORT 
			<?php 
				if(isset($_REQUEST['df']) && $_REQUEST['df']!="")
				{
					$date_filter_query = urldecode( $_REQUEST['df'] );
					$date_filter_query_ex=explode(" to ",$date_filter_query);
					$month_name=date("Y-m-d",strtotime($date_filter_query_ex[0]));
					$monthName = date('F', strtotime($month_name));
					echo "-".$monthName;
				}
				else
				{
					$date=date("m");
					$monthNum = sprintf("%2s", $date);
					$monthName = date("F", mktime(0, 0, 0, $monthNum, 10));
					echo "-".$monthName;
				}
			// if(isset($_REQUEST["month_id"]))
			// {
			// 	$monthNum = sprintf("%2s", $_REQUEST["month_id"]);
			// 	$monthName = date("F", mktime(0, 0, 0, $monthNum, 10));
			// 	echo "-".$monthName;
			// }
			// else
			// {
			// 	$date=date("m");
			// 	$monthNum = sprintf("%2s", $date);
			// 	$monthName = date("F", mktime(0, 0, 0, $monthNum, 10));
			// 	echo "-".$monthName;
			// }
			?></b></h3></td>
		</tr>
		<tr>
			<td colspan="31"></td>
			<td style="border-top:1px solid #ddd;"><b>Total</b></td>
			<td style="text-align: right;border-top:1px solid #ddd;" id="upTotal" >--</td>
		</tr>
            <tr>
                <th class="fix-th1">Sales Person</th>
				<?php for($i=1;$i<=31;$i++)
				{
				?>
				<th class="fix-th1">
				<?php echo $i; ?></th>	
				<?php
				}	
				?>			
                <th class="fix-th1">Amount</th> 
			</tr>
        </thead>
        <tbody>
        <?php
        if($ctable_r){
            $count = 0;

            $credit=0;
			$debit=0;
			$total=0;
			$total_credit=0;
			$total_debit=0;

            while($ctable_d = mysqli_fetch_array($ctable_r)){
                $count++;

                $credit = $db->rp_getValue("employee_account_transaction","SUM(credit)","isDelete=0 AND isActive=1 AND sales_id='".$ctable_d['sales_executive_id']."'",0);
					
					$debit = $db->rp_getValue("employee_account_transaction","SUM(debit)","isDelete=0 AND isActive=1 AND sales_id='".$ctable_d['sales_executive_id']."'",0);


					if($debit=="null")
					{
						$debit = 0;
					}
					if($credit=="null")
					{
						$credit = 0;
					}
					$total_debit = $debit;

					$pending=$total_debit-(-0-$credit);


				
				/*$ctable_r1=$db->rp_getData("expense","total",$ctable_where,"id DESC limit $page_position, $item_per_page",0);*/
				$ctable_r1=$db->rp_getData("expense","SUM(total) as total,sales_executive_id,expense_date,id",$ctable_where." AND sales_executive_id='".$ctable_d['sales_executive_id']."' GROUP BY DATE(expense_date)","id DESC limit $page_position,$item_per_page",0);
				$expense_date = date('d', strtotime($expense_date));
				if($ctable_r1)
				{
					$items=array();
					while($ctable_d1 = mysqli_fetch_array($ctable_r1))
					{
						$items[date("j",strtotime($ctable_d1['expense_date']))]=$ctable_d1;
					}
					?>
					  <tr>
						<td><?php 
						$sales_name=$db->rp_getValue("sales_executive","name","id='".$ctable_d['sales_executive_id']."'",0);
						echo $sales_name; ?>
						</td>
					<?php 
						$total_amount=0;
						$total_pass_amount=0;
						//$total=0;
						for($i=1;$i<=31;$i++)
						{
							if(array_key_exists($i,$items))
							{
								$items[$i]['expense_date']=date('Y-m-d',strtotime($items[$i]['expense_date']));
								$same_day_total=$db->rp_getValue("expense","SUM(total)" ,"sales_executive_id='".$ctable_d['sales_executive_id']."' AND DATE(expense_date)='".$items[$i]['expense_date']."' AND isDelete=0 AND isActive=1",0);

								$same_passe_total=$db->rp_getValue("expense","SUM(pass_expense_amount)" ,"sales_executive_id='".$ctable_d['sales_executive_id']."' AND DATE(expense_date)='".$items[$i]['expense_date']."' AND isDelete=0 AND isActive=1",0);

								$total_reject_amount= $same_day_total - $same_passe_total;




								$total_amount+=$same_day_total;
								$total_pass_amount+=$same_passe_total;
								$total_reject = $total_amount - $total_pass_amount;


								
								$total_km = $db->rp_getValue("salesexecutive_tracking_km","total_km","sales_executive_id='".$ctable_d['sales_executive_id']."' AND DATE(route_date)='".$items[$i]['expense_date']."'",0);
								// if($total_km)
						  //       { 
						  //         $total_km = "<b>GK - </b>".$total_km." KM";
						  //       }
						  //       else
						  //       {
						  //         $total_km = "<b>GK - </b>NA"; 
						  //       }
						        $xx = '"'.$ctable_d['sales_executive_id'].'","'.$items[$i]['expense_date'].'"';
						        // echo $xx;
         
								// $total_amount+=($items[$i]['total']);
								echo "<td><a href='#myExpenseModal' data-id=".$items[$i]['id']." data-toggle='modal'>R ".$db->rp_num($same_day_total)." <br/><b>P</b> ".$db->rp_num($same_passe_total)."</a>";
								// <br/><a onclick='update_km(".$xx.")'>".$total_km."</a></td> 
							}
							else{
								echo "<td>-</td>";
							}
						}
					}
					else{
                        $items=array();
						?>
						<tr>
						<td>
						<?php 
						$sales_name=$db->rp_getValue("sales_executive","username","id='".$ctable_d['sales_executive_id']."'",0);
						echo $sales_name; ?>
						</td>
						<?php 
						$total_amount=0;
						$total_pass_amount=0;
						$total_reject=0;
						for($i=1;$i<=31;$i++)
						{
							if(array_key_exists($i,$items))
							{
								$total_amount+= $db->rp_num($items[$i]['total']);
								$total_pass_amount+=$db->rp_num($items[$i]['pass_expense_amount']);
								$total_reject=$total_amount - $total_pass_amount;
								//echo $total_reject;exit();
								// $total_pass_amount+=($items[$i]['pass_expense_amount']);
								echo "<td style='text-align: right;'>-</td>"; 
							}
							else{
								$total_amount=0;
								$total_pass_amount=0;
								$total_reject=0;
								echo "<td>-</td>";
							}
						}
					}
					?>
					<td ><?php echo "R ".$db->rp_num($total_amount)." <br/><b>P</b> ".$db->rp_num($total_pass_amount); ?> <br/><b>C</b> 
						<?php 
						if($total_pass_amount!=0)
						{ 
							echo $db->rp_num($total_reject); 
						}
						else
						{
							echo "";
						}
						?>
						<!-- <?= ($pending<0)?-0-$pending:""; ?>
						<?= ($pending>0)?$pending:""; ?> -->
					</td>
					<?php $total_t+=$db->rp_num($total_pass_amount);
					?>
					</tr>
					<?php
				}
		}
				?>
		<tr>
			<td colspan="31"></td>
			<td><b>Total</b></td>
			<td style="text-align: right;"><?php echo $total_t; ?></td>
			<script>$("#upTotal").html("<?php echo $total_t; ?>")</script>
		</tr>
        </tbody>
    </table>
    </div>
</form>
<!-- <?php
//echo $db->getAddButton($ctable);
?> -->
<!-- <button type="button" class="btn red-haze  pull-right"  name="excel" id="excel" title="Download XL Report" onClick="genReportPdf(1);"><i class="fa fa-file-pdf-o"></i>&nbsp; PDF</button>
<button type="button" class="btn green-haze excel pull-right"  name="excel" id="excel" title="Download XL Report"><i class="fa fa-file-excel-o"></i>&nbsp; Excel</button> -->
<div id="myModal" class="modal fade">
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
<script>
function update_km(sales_id,route_date)
{
	// alert(route_date);                             ``
  $.ajax({
    type: "POST",
    url: "../service/service_sales_km_update.php?key=1226&s=197",
    data: 'sales_id=' + sales_id+'&route_date=' + route_date+'&save_flag=1',
 	beforeSend: function() {
		$("#loading-modal").modal('show');
	},
    success: function(data) { 
        // location.reload();
    	$("#loading-modal").modal('hide');
    	displayRecords(); 
    }
  }); 
}
</script>
<?php 
}
else
{
	echo "<h3 style='margin:0;text-align:center'>Please select sales person to see result</h3>";
}
require_once("disconnect.php");
?>