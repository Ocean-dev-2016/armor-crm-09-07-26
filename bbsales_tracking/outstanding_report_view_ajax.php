<?php
$page_id=618;$page_slug='outstanding_report';
/*
 * @author Ravi Patel
 */
include("connecting.php");
$ctable 	= "account_transaction";
$ctable1 	= "Account Transaction";
$credit_total='';
$debit_total='';
$ctable_where = "";
// Get the total number of rows in the table

if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
	$customer_id=$db->rp_getValue("customer","id","isDelete=0 AND lname='".$_REQUEST['searchName']."'",0);
	$ctable_where .= " ( remark like '%".$_REQUEST['searchName']."%' ) AND ";
}

$final = date("Y-m-d", strtotime("-1 month"));
$ctable_where .= " isDelete=0 AND isActive=1 ";

if(isset($_REQUEST['cid']) && $_REQUEST['cid']!="" && $_REQUEST['cid']!=NULL)
{
 $ctable_where .= " AND cid ='".$_REQUEST['cid']."'";
}

$ctable_r = $db->rp_getData($ctable,"*",$ctable_where." GROUP BY account_id","payment_date ASC",0);
?>
<!-- <button type="button" class="btn red-haze excel pull-right"  name="excel" id="excel" title="Download XL Report" onClick="genReportPdf(1);" target="_blank"><i class="fa fa-file-pdf-o"></i>&nbsp; PDF</button>

<button type="button" class="btn green-haze excel pull-right" style="margin-bottom:10px;" name="excel" id="excel" href="" title="Download XL Report" onClick="genReportExcel()"><i class="fa fa-file-excel-o"></i>Excel</button> -->


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
<form action="" name="frm" method="post" id="print_info">
	<table class="table table-striped table-hover table-bordered outstanding_report_table_class" id="outstanding_report_table_id">
        <thead>
         	<tr>
         		<td colspan="7" align="center"><h3><b>Outstanding Report</b></h3></td>
         	</tr>
			<tr>
				<th>Sr No.</th>
				<th>Company Name</th>
				<th>Customer Name</th>
				<th>Responsible Person</th>
				<th>Phone No</th>
				<th>Outstanding</th>
				<th>Email Id.</th>
			</tr>
		</thead>
      <tbody>
      	<?php
				if($ctable_r)
        	{
          	$count = 0;
          	$credit=0;
						$debit=0;
						$total=0;
						$total_credit=0;
						while($ctable_d = mysqli_fetch_assoc($ctable_r))
			            {
							//payment due date 
			            	$pay_date = date('Y-m-d',strtotime($ctable_d['created_date']));
			  				$before_date=date('Y-m-d', strtotime('-30 days'));
							$current_date = date('Y-m-d');

							$credit = $db->rp_getValue($ctable,"SUM(credit)","isDelete=0 AND isActive=1 AND account_id='".$ctable_d['account_id']."'",0);
							
							$debit = $db->rp_getValue($ctable,"SUM(debit)","isDelete=0 AND isActive=1 AND account_id='".$ctable_d['account_id']."'",0);

							if($debit=="null")
							{
								$debit = 0;
							}
							if($credit=="null")
							{
								$credit = 0;
							}
							$total_credit = $credit;
							// echo $total_credit;
							// echo $debit;
							$pending=$total_credit-(-0-$debit);

							if($pending<0)
							{
								$total +=round($pending,2);
			        			?>
            		<tr>
		              <td><?php echo ++$count; ?></td>
		              <td><?php echo $db->rp_getValue("executive","company_name","id=".$ctable_d['cid']."");?></td>
									<td><?php echo $db->rp_getValue("executive","cname","id=".$ctable_d['cid']."");?></td>
									<!-- <td><?php echo $db->rp_getValue("account","account_name","id=".$ctable_d['account_id']."")." - ".$db->rp_getValue("executive","cname","id=".$ctable_d['cid']."");?></td> -->
									<td>
									<?php
										$sales_executive_id = $db->rp_getValue("executive","seid","id=".$ctable_d['cid']."");
										$responsibleperson = $db->rp_getValue("sales_executive","name","id=".$sales_executive_id."");
										echo ($responsibleperson!="")?$responsibleperson:"-";
									?>
									</td>
									<td><?php echo $db->rp_getValue("executive","phone","id=".$ctable_d['cid']."");?></td>
									<td align="right"><?php echo $db->rp_num(round(-0-$pending,2));?></td>
									<td><?php echo $db->rp_getValue("executive","email","id=".$ctable_d['cid']."");?></td>
								</tr>
								<?php
							}
						}
						?>
						<tr>
							<td colspan="5" align="right"><b>Total Outstanding</b></td>
							<td align="right"><b><?php  echo $db->rp_num(-0-$total);?></b></td>
							<td></td>
						</tr>
						<?php
		    		}
					else
					{
						?>
						<tr>
							<td colspan="6"><p style="text-align:center;">No data available in table</p></td>
						</tr>
						<?php
					}
		    ?>
			</tbody>
    </table>
</form>
<?php require_once 'disconnect.php';  ?>