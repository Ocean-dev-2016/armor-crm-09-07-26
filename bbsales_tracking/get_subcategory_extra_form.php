<?php
$page_id=559;$page_slug='page_product';
include("connect.php");
$type=$_REQUEST['type'];
$mode=$_REQUEST['mode'];
$id=$_REQUEST['id'];
require_once("../include/expence_subcategory.class.php");
$objCategory= new ExpenceSubCategory();
if($id!="" && $mode=="edit")
{
	$detail=array();
	$detail['id']=$id;
	$reply=$objCategory->GetEditDataExpenceSubCategory($detail);
	if($reply['ack']==1)
	{
		$result1=$reply['result'];		
		extract($result1);	
	}
}
else
{
	/*$in_class="";
	$a_class="collapse";
	$aria_expanded="false";*/
}
?>
<link rel="stylesheet" type="text/css" href="css/jquery.datetimepicker.css" />
<style>
table{
    height: auto;	
    width:100%;
	font-family: Calibri,sans-serif;
    font-style: normal;
    font-weight: 400;
    padding: 0;
    text-decoration: none;
	font-size: 16px;
	margin:auto;
	padding:auto;
}
.box1 th
{
	border-color: #000;
	color: #FFF;
}
table{
    height: auto;	
    width:100%;
}
table , td, th {
 border: 1px solid #95A5A6;
 border-collapse: collapse;
}
td, th {
 padding: 2px;
 width: 30px;
 height: 15px;
 /*color: #FFF;*/
 /*line-height: 0.8;*/
 text-align: center;
}
h4{
	padding-left:40px;
}
th {
}
.center{
	text-align:center;
}
.left{
	text-align:left;
	padding-left:15px;
}
.right{
	text-align:right;
	padding-right:15px;
}
</style>
<?php
if($type==1)
{
?>
<div class="portlet grey-cascade box">
	<div class="portlet-title">
		<div class="caption">
		   <i class="fa fa-user"></i> &nbsp; Expense
		</div>
	</div>
	<div class="portlet-body">
	   <div class="row">																							<div class="col-md-12">
				<table class="table table-striped table-bordered table-hover">
					<thead>
						<tr>
							<th>Name</th>
							<th align="center">Flag</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>Image</td>
							<td align="center"><input type="checkbox" name="image_flag" id="image_flag" value="1" <?php echo ($image_flag==1)?"checked":""; ?> style="width:60px;text-align:center"></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<?php
}
else if($type==2)
{?>
	<div class="portlet grey-cascade box">
	<div class="portlet-title">
		<div class="caption">
		   <i class="fa fa-user"></i> &nbsp; Expense
		</div>
	</div>
	<div class="portlet-body">
	   <div class="row">																							<div class="col-md-12">
				<table class="table table-striped table-bordered table-hover">
					<thead>
						<tr>
							<th>Name</th>
							<th align="center">Flag</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>Image</td>
							<td align="center"><input type="checkbox" name="image_flag" id="image_flag" value="1" <?php echo ($image_flag==1)?"checked":""; ?> style="width:60px;text-align:center"></td>
						</tr>
						<tr>
							<td>Fix Amount</td>
							<td align="center"><input class="form-control" type="text" name="fix_amount" id="fix_amount" value="<?php echo $fix_amount; ?>"></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<?php
}
else if($type==3)
{?>
		<div class="portlet grey-cascade box">
	<div class="portlet-title">
		<div class="caption">
		   <i class="fa fa-user"></i> &nbsp; Expense
		</div>
	</div>
	<div class="portlet-body">
	   <div class="row">																							<div class="col-md-12">
				<table class="table table-striped table-bordered table-hover">
					<thead>
						<tr>
							<th>Name</th>
							<th align="center">Flag</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>Image</td>
							<td align="center"><input type="checkbox" name="image_flag" id="image_flag" value="1" <?php echo ($image_flag==1)?"checked":""; ?> style="width:60px;text-align:center"></td>
						</tr>
						<tr>
							<td>Fix Amount</td>
							<td align="center"><input class="form-control" type="text" name="fix_amount" id="fix_amount" value="<?php echo $fix_amount; ?>"></td>
						</tr>
						<tr>
							<td>Min Time</td>
							<td align="center"><input class="form-control timepicker" type="text" name="min_time" id="min_time" value="<?php echo $min_time; ?>"></td>
						</tr>
						<tr>
							<td>Max Time</td>
							<td align="center"><input class="form-control timepicker" type="text" name="max_time" id="max_time" value="<?php echo $max_time; ?>"></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<?php
}
?>
<script src="js/jquery.datetimepicker.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$('.timepicker').datetimepicker({
	  		datepicker:false,
	  		format:'H:i'
		}); 
 	});
</script>