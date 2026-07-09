<?php
$page_id=554;$page_slug='page_employee';
include("connect.php");
$emp_id = $_REQUEST['id'];
$ctable_where	= "emp_id='".$_REQUEST['id']."' AND isDelete=0";
$ctable_r = $db->rp_getData("emp_salary_info","*",$ctable_where,"",0);
?>
<div id="print_info_salary">						
	<div class="row">
	<div class="col-md-12">	
	<table id="datatable_2" class="table table-striped table-bordered table-hover">
		<thead>
			<tr><th colspan="6" class="bg-grey">Earning</th></tr>
		</thead>
		<tbody>
		<?php
		if(!empty($ctable_r))
		{
			if(mysqli_num_rows($ctable_r)>0){
            
            while($ctable_d = mysqli_fetch_array($ctable_r)){
                
        ?>
			<tr>
				<th>YEAR </th>
				<td><label><?php echo $ctable_d['year']; ?><label></td>
				<th>BASIC </th>
				<td><label><?php echo $ctable_d['basic']; ?><label></td>
				
			</tr>
			
			<tr>
				<th>MEDICAL</th>
				<td><label><?php echo  $ctable_d['medical']; ?></label></td>
				<th>CONV.ALL</th>
				<td><label><?php echo  $ctable_d['conv']; ?></label></td>
			</tr>
			<tr>									
				<th>WASH.ALL</th>
				<td><label><?php echo $ctable_d['wash']; ?></label></td>
				<th>EDU.ALLt</th>
				<td><label><?php echo $ctable_d['edu']; ?></label></td>
			</tr>
			
			<tr>
				<th>L.T.ALL</th>
				<td><label><?php echo $ctable_d['lt']; ?></label></td>
				<th>SPEC.ALL</th>
				<td><label><?php echo $ctable_d['spe']; ?></label></td>										
			</tr>
			
			<tr>
			<th>HRA</th>
				<td><label><?php echo  $ctable_d['hra']; ?></label></td>
				<th>GROSS</th>
				<td><label><?php echo $ctable_d['gross']; ?></label></td>
				
			</tr>
			<tr>
			<table class="table table-striped table-bordered table-hover">
				<thead>
					<tr><th colspan="4" class="bg-grey">Deduction</th></tr>
					<tr>
						<th>IT</th>
						<th>PT</th>
						<th>PF</th>	
					</tr>
				</thead>
				<tbody>
				<tr>
					<td><label><?php echo  $ctable_d['it']; ?></label></td>
					<td><label><?php echo $ctable_d['pt']; ?></label></td>
					<td><label><?php echo $ctable_d['pf']; ?></label></td>
				</tr>
			</tbody>
			</table>
			</tr>
			<tr>
			<table class="table table-striped table-bordered table-hover">
				<thead>
					<tr><th colspan="4" class="bg-grey">Net Payable</th></tr>
					<tr>
						<th>Net Payable</th>
						<th>Remark</th>
					</tr>
				</thead>
				<tbody>
				<tr>
					<td><label><?php echo  $ctable_d['net_payable']; ?></label></td>
					<td><label><?php echo $ctable_d['remark']; ?></label></td>
				</tr>
			</tbody>
			</table>
			</tr>
		<?php
            }
        }
		}
		else{
			?>
			<tr class="bg_grey">
				<td>No data available</td>
			<tr>
			<?php
		}
        ?>
		</tbody>
	</table>
	</div>
	</div>
</div>
	<div class="row">
		<div class="col-md-2">
			<a onClick="printsalaryPDF()" class="btn btn-info" title="print"><i class="fa fa-print"></i>Print</a>
		</div>
		<div class="col-md-2">
			<a class="btn btn-info" onClick="genReportSalary('<?php echo $emp_id; ?>')" title="Edit"><i class="fa fa-file-pdf-o"></i>Export</a>
		</div>
	</div>
	<?php require_once 'disconnect.php';  ?>
