<?php
$page_id=592;$page_slug='expense_page';
include("connect.php");
$ctable = "expense";
$amounts=array();
$ctable_where = "";
$name="";
$expense_date="";
$items="";
$total_t="";
$month_id="";
// print_r($_REQUEST);exit();
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

///For ToDate & FromDate
// if(isset($_REQUEST['ToDate']) && $_REQUEST['ToDate']!="" && $_REQUEST['ToDate']!=NULL)
// {
//  $ctable_where .= " expense_date  <= '".date_format(date_create($_REQUEST['ToDate']),"Y-m-d")."' ";
// }

// if(isset($_REQUEST['FromDate']) && $_REQUEST['FromDate']!="" && $_REQUEST['FromDate']!=NULL)
// {
//  $ctable_where .= " AND expense_date >= '".date_format(date_create($_REQUEST['FromDate']),"Y-m-d")."' AND isActive=1 AND isDelete=0 ";
// }
// echo $_REQUEST['df'];exit();
if(isset($_REQUEST['df']) && $_REQUEST['df']!=""){
	//echo $_REQUEST['df'];exit;
	$date_filter_query = urldecode( $_REQUEST['df'] );

	$date_filter_query_ex=explode(" to ",$date_filter_query);

	$ctable_where .= " ( DATE(expense_date)>='".date("Y-m-d",strtotime($date_filter_query_ex['0']))."' AND DATE(expense_date)<='".date("Y-m-d",strtotime($date_filter_query_ex['1']))."'  ) ";
}
else
{
	$ctable_where .= " isDelete=0 AND isActive=1 AND MONTH(expense_date)='".$date."'";
}

if(isset($_REQUEST['sales_executive_id']) && $_REQUEST['sales_executive_id']!="" && $_REQUEST['sales_executive_id']!=NULL && $_REQUEST['sales_executive_id']!=0)
{
 	$ctable_where .= " AND sales_executive_id = '".$_REQUEST['sales_executive_id']."' ";
}

if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0 && $_SESSION[SITE_SESS.'_ADMIN_TYPE']!=14)
{
    $check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];
    $ctable_where .= " AND sales_executive_id='".$check_id."' ";
}

$get_total_rows = $db->rp_getTotalRecord($ctable,$ctable_where,0); //hold total records in variable
//break records into pages
$total_pages = ceil($get_total_rows/$item_per_page);

//get starting position to fetch the records
$page_position = (($page_number-1) * $item_per_page);

$ctable_r = $db->rp_getData($ctable,"DISTINCT sales_executive_id",$ctable_where,"id DESC limit $page_position, $item_per_page",0);
// http://24.24.25.250/cmk/bbsales_tracking/print_expense_ajax.php?searchName=&ToDate=&FromDate=&sales_executive_id=



?>
<style>
.mainDiv, table{
  height: auto;   
  width:100%;
  font-family: Calibri,sans-serif;
  font-style: normal;
  font-weight: 400;
  padding: 0;
  text-decoration: none;
  font-size: 10pt;
  margin:auto;
  padding:auto;
}
tr{height: 30px;}
table , td, th { border-collapse: collapse;border: 1px solid #000;}
td, th {  padding: 5px;}
th { border: 1px solid #595959;background: #f0e6cc;}
.text-right{text-align: right;}
.center{text-align:center;}
.space{ padding: 10px;}
.no-border{border-bottom: 1px solid #fff;}
h2
{
	text-transform: uppercase;
	margin-bottom: 0px;
}
</style>
<!-- <button type="button" class="btn red-haze pull-right" name="excel" id="excel" title="Download XL Report" onClick="genReportPdf(1);"><i class="fa fa-file-pdf-o"></i>&nbsp; PDF</button>
<button type="button" class="btn green-haze excel pull-right" style="margin-bottom:10px;" name="excel" id="excel" href="" title="Download XL Report"><i class="fa fa-file-excel-o"></i>Excel</button> -->

	<table class="table table-striped table-bordered table-hover" >
        <thead>
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

			?></b></h3></td>
		</tr>
		<tr>
			<td colspan="31"></td>
			<td style="border-top:1px solid #ddd;"><b>Total</b></td>
			<td style="text-align: right;border-top:1px solid #ddd;" id="upTotal" >--</td>
		</tr>
            <tr>
                <th>Area Sales Manager Name</th>
				<?php for($i=1;$i<=31;$i++)
				{
					?>
				<th>
					<?php echo $i; ?></th>	
					<?php
				}	
				?>			
                <th>Amount</th> 
			</tr>
        </thead>
        <tbody>
        <?php
        if ($_REQUEST['sales_executive_id']!="") 
        {
        
	        if($ctable_r)
	        {
	            $count = 0;
	            while($ctable_d = mysqli_fetch_array($ctable_r))
	            {
	                $count++;
					
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
									
									// $total_amount+=($items[$i]['total']);
									echo "<td><a href='#myExpenseModal' data-id=".$items[$i]['id']." data-toggle='modal'>R ".$same_day_total." <br/><b>P</b> ".$same_passe_total."</a></td>"; 
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
									$total_amount+=($items[$i]['total']);
									$total_pass_amount+=($items[$i]['pass_expense_amount']);
									echo "<td style='text-align: right;'>-</td>"; 
								}
								else{
									$total_amount=0;
									$total_pass_amount=0;
									echo "<td>-</td>";
								}
							}
						}
							?>
							<td >
								<?php 
								echo "R ".$db->rp_num($total_amount)." <br/><b>P</b> ".$db->rp_num($total_pass_amount); ?> <br/><b>C</b> 
						<?php 
						if($total_pass_amount!=0)
						{ 
							echo $db->rp_num($total_reject); 
						}
						else
						{
							echo "";
						}
								//echo "R ".$total_amount." <br/><b>P</b> ".$total_pass_amount ;
								 ?>
								
							</td>
							<?php 
								 $total_t+=$db->rp_num($total_pass_amount);
							//$total_t+=$total_amount;
							 ?>

							</tr>
							<?php
					}

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
<?php require_once("disconnect.php"); ?>
    
