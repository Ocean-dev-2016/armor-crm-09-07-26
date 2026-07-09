<?php
$page_id=400;$page_slug='dashboard';
include("connect.php");	
$val= $_REQUEST['val'];
?>
<?php
	if($val == "2")
	{
		?>
		<!-- <label>Select Report</label>
								<select name="report" id="report" class="form-control"> -->
									<option value="">Select Report</option>
									<!-- <option value="1">Pie-Sales Order Report</option> -->
									<option value="8">Bar-Sales Order Report</option>
									<!-- <option value="2">Pie-Quotation Report</option> -->
									<option value="9">Bar-Quotation Report</option>
									<!-- <option value="3">Pie-Invoice Report</option> -->
									<option value="10">Bar-Invoice Report</option>
									<!-- <option value="4">Pie-Visit Report</option> -->
									<option value="11">Bar-Visit Report</option>
									<!-- <option value="5">Pie-Complain Report</option> -->
									<option value="12">Bar-Complain Report</option>
									<!-- <option value="6">Pie-Inquiry Report</option> -->
									<option value="13">Bar-Inquiry Report</option>
									<!-- <option value="7">Pie-Lead Report</option> -->
									<option value="14">Bar-Lead Report</option>
								</select>

									<?php
	}
	else
	{
		?>
			<!-- <label>Select Report</label>
			<select name="report" id="report" class="form-control"> -->
									<option value="">Select Report</option>
									<option value="1">Pie-Sales Order Report</option>
									<!-- <option value="8">Bar-Sales Order Report</option> -->
									<option value="2">Pie-Quotation Report</option>
									<!-- <option value="9">bar-Quotation Report</option> -->
									<option value="3">Pie-Invoice Report</option>
									<!-- <option value="10">Bar-Invoice Report</option> -->
									<option value="4">Pie-Visit Report</option>
									<!-- <option value="11">Bar-Visit Report</option> -->
									<option value="5">Pie-Complain Report</option>
									<!-- <option value="12">Bar-Complain Report</option> -->
									<option value="6">Pie-Inquiry Report</option>
									<!-- <option value="13">Bar-Inquiry Report</option> -->
									<option value="7">Pie-Lead Report</option>
									<!-- <option value="14">bar-Lead Report</option> -->


		<?php
	}
	?>
	<?php


 
  	
?>
<?php require_once 'disconnect.php';  ?>