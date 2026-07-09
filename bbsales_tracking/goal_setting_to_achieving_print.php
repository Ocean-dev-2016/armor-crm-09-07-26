<?php
$page_id=661;$page_slug='goal_setting_to_achieving';
include("connect.php");
$ctable 	= "goal_setting_to_achieving";
$ctable1 	= "goal_setting_to_achieving";

$ctable_where = "";
// Get the total number of rows in the table

if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){

$ctable_where1 .= " (first_name like '%".$_REQUEST['searchName']."%' OR middle_name like '%".$_REQUEST['searchName']."%' OR contact_no like '%".$_REQUEST['searchName']."%' OR surname like '%".$_REQUEST['searchName']."%') AND ";
$id_r=$db->rp_getValue("sales_executive_information","id",$ctable_where1."isDelete=0",0);
if($id_r!="")
{

	$ctable_where .= " (sales_executive_form_id like '%".$id_r."%') AND ";
}
else
{
	$ctable_where .= " (sales_executive_form_id like '%"."0"."%') AND ";
}
}

if(isset($_REQUEST['sales_executive']) && $_REQUEST['sales_executive']!="" && $_REQUEST['sales_executive']!=null && $_REQUEST['sales_executive']!=undefined)
{
	$ctable_where .= "sales_executive_form_id='".$_REQUEST['sales_executive']."' AND ";
	$sales_executive=$_REQUEST['sales_executive'];
}


$ctable_where .= "isDelete=0";

$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"]) ) ? intval($_REQUEST["show"]) : 100;



if(isset($_REQUEST["page"])){

	$page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number

	if(!is_numeric($page_number)){die('Invalid page number!');} //incase of invalid page number

}else{

	$page_number = 1; //if there's no page number, set it to 1

}



$get_total_rows = $db->rp_getTotalRecord($ctable,$ctable_where,0); //hold total records in variable

//break records into pages

$total_pages = ceil($get_total_rows/$item_per_page);



//get starting position to fetch the records

$page_position = (($page_number-1) * $item_per_page);



$ctable_r = $db->rp_getData($ctable,"*",$ctable_where,"",0);
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
<table id="example1" class="table table-bordered table-striped dataTable">
	<thead>
		<tr>
			<th colspan="12" class="center">
				<h2>Self Analysis Report <?= date("d-m-Y h:i a");?> Printed By : <?=$_SESSION[SITE_SESS.'SESS_NAME'];?></h2>
			</th>
	    </tr>

       <tr>
        <th  class="fix-th1">No.</th>
        <th  class="fix-th1">Name</th>
				<th  class="fix-th1">Questions Answers</th>
				<th  class="fix-th1">Created Date</th>
			</tr>
	
	</thead>
	<tbody>
	<?php
	if($ctable_r)
	{
		$count = 0;

			while($ctable_d = mysqli_fetch_array($ctable_r))
			{
				$count++;
			
	?>
		<tr>
			  <td><?php echo $count; ?></td>
			  
			  <td>
					<?php 
				   	$sales_r=$db->rp_getData("sales_executive_information","*","isDelete=0 AND id='".$ctable_d['sales_executive_form_id']."'");
					  $sales_d=mysqli_fetch_array($sales_r);
					  echo $sales_d['first_name']." ".$sales_d['middle_name']." ".$sales_d['surname']."-".$sales_d['contact_no'];
				
					?>		
				</td>
				<td>
						<table class="table table-striped table-bordered table-hover">
							<thead>
								<tbody>
									<tr>
										<th>Questions</th>
										<th>Answers</th>
									</tr>
								  
								   <tr>
										<td>
										If we were meeting here three years from now, what do you think should have happened in your both life, both personally and professionally for you to feel happy about your progress?"
										</td>
										<td><b>Professional:</b><?= $ctable_d['professional'];?><br><br><b>Personal:</b><?= $ctable_d['personal'];?></td>
									   </tr> 
									   
								  <tr>
										<td>
										What are your Roadblocks and challenges are from the market place that can stop you from achieving your goal?
										</td>
										<td><?= $ctable_d['answer1'];?><br><br><?= $ctable_d['answer2'];?><br><br><?= $ctable_d['answer3'];?></td>
									</tr>
									<tr>
										<td>
										What support/help you require from Management to achieve your goal?
									    </td>
									    <td>
									    	<?= $ctable_d['answer4'];?><br><br><?= $ctable_d['answer5'];?><br><br><?= $ctable_d['answer6'];?>
									    </td>
									</tr>
									<tr>
									    <td>
									    What are your Personal Roadblocks and challenges that can stop you from achieving your goal?
									    </td>
										  <td>
											<?= $ctable_d['answer7'];?><br><br><?= $ctable_d['answer8'];?><br><br><?= $ctable_d['answer9'];?>
										  </td>

											<?php $que_count++;
											?>		
				      
								  </tbody>
							   </thead>
						</table>
				</td>
				<td><?php echo date('d-m-Y',strtotime($ctable_d['created_date']));?></td>
				 <input type="hidden" name="b_id<?php echo $count ?>" value="<?php echo $ctable_d['id']; ?>">
				</td>
		</tr>
	<?php
		}
	}
	else
	{
		?>
		<tr>
			<td colspan="8" class="text-center">No Data Found!!</td>
		</tr>
		<?php
	}
	?>
	</tbody>
	</table>
	