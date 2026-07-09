<?php
$page_id=636;$page_slug='employee_outstanding_report';
/*
 * @author Ravi Patel
 */
include("connect.php");
$ctable 	= "employee_account_transaction";
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

if(isset($_REQUEST['sales_id']) && $_REQUEST['sales_id']!="" && $_REQUEST['sales_id']!=NULL)
{
 // $ctable_where .= " AND account_id ='".$_REQUEST['sales_id']."'";
 $ctable_where .= " AND sales_id ='".$_REQUEST['sales_id']."'";
}

$ctable_r = $db->rp_getData($ctable,"*",$ctable_where." GROUP BY sales_id","payment_date ASC",0);
$outstanding_id = $db->rp_getData($ctable,"*",$ctable_where." GROUP BY sales_id","payment_date ASC",0);
//$Outstanding_Ids[];
if($outstanding_id)
{	
	while($K1=mysqli_fetch_assoc($outstanding_id))
	{
		$Outstanding_Ids[]=$K1['id'];
	}

	$outstanding_ids=implode(",",$Outstanding_Ids);
}
 // print_r($outstanding_ids);exit;
?>
<!-- <button type="button" class="btn  excel pull-left"  name="send_mail" id="send_mail" title="Send Mail"  style="background-color:#f34545;color: white;" onClick="OutstandingSendMail();">
	<i class="fa fa-envelope-o" ></i>&nbsp; Send Mail</button> -->

<!-- <button type="button" class="btn red-haze excel pull-right"  name="excel" id="excel" title="Download XL Report" onClick="genReportPdf();" target="_blank"><i class="fa fa-file-pdf-o"></i>&nbsp; PDF</button>

<button type="button" class="btn green-haze excel pull-right" style="margin-bottom:10px;" name="excel" id="excel" href="" title="Download XL Report" onClick="genReportExcel()"><i class="fa fa-file-excel-o"></i>Excel</button> -->

<form action="" name="frm" method="post" id="print_info">
	<table class="table table-striped table-hover table-bordered outstanding_report_table_class" id="outstanding_report_table_id">
        <thead>
         	<tr>
         		<td colspan="6" align="center"><h3><b>Employee Outstanding Report</b></h3></td>
         	</tr>
			<tr>
				<th>Sr No.</th>
				<th>Sales Officer Type</th>
				<th>Employee Name</th>
				<!-- <th>Responsible Person</th> -->
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
				$total_debit=0;
				while($ctable_d = mysqli_fetch_assoc($ctable_r))
	            {
					//payment due date 
	            	$pay_date = date('Y-m-d',strtotime($ctable_d['created_date']));
	  				$before_date=date('Y-m-d', strtotime('-30 days'));
					$current_date = date('Y-m-d');

					$credit = $db->rp_getValue($ctable,"SUM(credit)","isDelete=0 AND isActive=1 AND sales_id='".$ctable_d['sales_id']."'",0);
					
					$debit = $db->rp_getValue($ctable,"SUM(debit)","isDelete=0 AND isActive=1 AND sales_id='".$ctable_d['sales_id']."'",0);

					if($debit=="null")
					{
						$debit = 0;
					}
					if($credit=="null")
					{
						$credit = 0;
					}
					// $total_credit = $credit;

					// $total_credit = $debit;

					$total_debit = $debit;
					 
					// echo $credit;
					// echo "<br>";
					// echo $total_debit;

					$pending=$total_debit-(-0-$credit);

					// echo "<br>";
					// echo $pending;
					// if($pending<0)
					if($pending>0)
					{
						// echo "Hello"; 
						$total +=round($pending,2);
	        			?>
	            		<tr>
			                <td><?php echo ++$count; ?></td>
			                <td>
			                	<!-- <?php echo $db->rp_getValue("executive","company_name","id=".$ctable_d['cid']."");?> -->
			                	
			                	<?php				

								if($ctable_d['sales_type']=="sales_manager")
				                        {
				                            $ctable_d['sales_type']="Regional Sales Manager";
				                        }

				                        if($ctable_d['sales_type']=="area_sales_manager")
				                        {
				                            $ctable_d['sales_type']="Business Development Manager";
				                        }

				                        if($ctable_d['sales_type']=="dispatch_sales_manager")
				                        {
				                            $ctable_d['sales_type']="Dispatch Manager";
				                        }
				                        
				                        if($ctable_d['sales_type']=="sales_officer")
				                        {
				                            $ctable_d['sales_type']="Area Sales Manager";
				                        }
				                        
				                        if($ctable_d['sales_type']=="sales_executive")
				                        {
				                            $ctable_d['sales_type']="Sales Officer";
				                        }
				                        if($ctable_d['sales_type']=="service_executive")
				                        {
				                            $ctable_d['sales_type']="Service Executive";
				                        }
				                        echo stripslashes($ctable_d['sales_type']); 
								?>

			                </td>
							<td><?php echo $sales_name=$db->rp_getValue("sales_executive","name","id='".$ctable_d['sales_id']."'",0); ?></td>
							<!-- <td><?php echo $db->rp_getValue("account","account_name","id=".$ctable_d['account_id']."")." - ".$db->rp_getValue("executive","cname","id=".$ctable_d['cid']."");?></td> -->
							<!-- <td>
							<?php
								$sales_executive_id = $db->rp_getValue("executive","seid","id=".$ctable_d['cid']."",0);
								$responsibleperson = $db->rp_getValue("sales_executive","name","id=".$sales_executive_id."");
								echo ($responsibleperson!="")?$responsibleperson:"-";
							?>
							</td> -->
							<td><?php echo $db->rp_getValue("sales_executive","phone","id=".$ctable_d['sales_id']."");?></td>
							<td align="right"><?php echo $db->rp_num(round($pending,2));?></td>

							<!-- <td align="right"><?php echo $db->rp_num(round(-0-$pending,2));?></td> -->
							<td><?php echo $db->rp_getValue("sales_executive","email","id=".$ctable_d['sales_id']."");?></td>
						</tr>
						<?php
					}
				}
				?>
				<tr>
					<td colspan="5" align="right"><b>Total Outstanding</b></td>
					<!-- <td align="right"><b><?php  echo $db->rp_num(-0-$total);?></b></td> -->
					<td align="right"><b><?php  echo $db->rp_num($total);?></b></td>
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