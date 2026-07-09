<?php

$page_id=659;$page_slug='self_analysis';

include("connect.php");

$ctable = "self_analysis";

$ctable1 = "self_analysis";

$ctable_where = "";
$ctable_where1 = "";

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
.table-scrollable 
{
	width: auto;
	height: 600px;
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



<form action="self_analysis_manage.php" name="print_info1" id="print_info1" method="post">
	<table id="example1" class="table table-striped table-bordered table-hover">

        <thead>
        	<tr>
        		<th style="width: 5%;"></th>
        		<th style="width:50px;"></th>
        		<th style="width:150px;">
        			  <select class="form-control" name="sales_executive" id="sales_executive">
					<option value="">--- Select Sales Person ---</option>
                     
					<?php 
					$sales_executive_r=$db->rp_getData('sales_executive_information',"*","isDelete=0","",0);
						while($sales_executive_d=mysqli_fetch_assoc($sales_executive_r))
						{

						?>
						<option <?=($sales_executive_d['id']==$sales_executive)?"selected":"";?> <?php echo ($sales_executive==$sales_executive_d['id'])?"selected":"" ; ?>  value="<?php echo $sales_executive_d['id']?>"><?php echo $sales_executive_d['first_name']." ".$sales_executive_d['middle_name']." ".$sales_executive_d['surname']."  " .$sales_executive_d['contact_no'];?></option>
						<?php
						}
					?>
				</select>
        		</th>
        		<th style="width:300px;"></th>
        		<th style="width:300px;"></th>
        		<!-- <th style="width:100px;"></th> -->

        	</tr>

            <tr>
				<th class="fix-th1" style="width: 5%;"></th>
                <th style="width:50px;" class="fix-th1">No.</th>
				<th style="width:150px;" class="fix-th1">Name</th>
				<th style="width:1000px;" class="fix-th1">Questions Answers</th>
				<!-- <th style="width:300px;" class="fix-th1">Answer</th> -->
				<th style="width:50px;" class="fix-th1">Created Date</th>
			</tr>

        </thead>

        <tbody>

        <?php
        if($_REQUEST['searchName']!="" || $_REQUEST['mode']=="edit" || $_REQUEST['sales_executive']!="undefined"  )
        {

		if(mysqli_num_rows($ctable_r)>0){

			$count = 0;

			

			while($ctable_d = mysqli_fetch_array($ctable_r)){

				$count++;

		?>

			<tr>

				<td>
					<?php $ctable_d['id']; 				
					if($rights['update_flag']==1)
					{
						?>
						<div class="btn-group">				
							<button aria-expanded="false" data-toggle="dropdown" type="button" class="btn btn-sm blue dropdown-toggle">
								<i class="fa fa-gear"></i>
							</button>
							<ul role="menu" class="dropdown-menu">
								<li>
									<a href="<?php echo $ctable; ?>_crud.php?mode=edit&id=<?php echo $ctable_d['id']; ?>" title="Edit">
										<span class="text-primary">
											<i class="fa fa-pencil"></i>
											&nbsp;Edit
										</span>
									</a>
								</li>
								<?php
								if($rights['delete_flag']==1 && $ctable_d['id']!=-1)
								{
									?>
									<li>
										<a onClick="del_conf('<?php echo $ctable_d['id']; ?>');" title="Delete">
											<span class="text-danger">
												<i class="fa fa-times"></i>
												&nbsp;Delete
											</span>
										</a>
									</li>
									<?php
								}
								?>	
							</ul>
						</div>
						<?php
					}
					?>
				</td>

				<td><?php echo $count; ?></td>

				<td>
					<?php 
					$sales_r=$db->rp_getData("sales_executive_information","*","isDelete=0 AND id='".$ctable_d['sales_executive_form_id']."'");
					$sales_d=mysqli_fetch_array($sales_r);
					echo $sales_d['first_name']." ".$sales_d['middle_name']." ".$sales_d['surname']."-".$sales_d['contact_no'];
					// echo $db->rp_getValue("sales_executive_information","first_name","isDelete=0 AND id='".."'"); 
					?>
						
					</td>

				<td>

					<?php
					$questions= explode(",",$ctable_d['questions_id']);
					$answers_d= explode(",",$ctable_d['answers']);
					//print_r($answers_d);
					$que_count=0;
					$sr_no=0;
				
						?>
						<table class="table table-striped table-bordered table-hover">
							<thead>
								<tbody>
									<tr>
										<th style="width:5px;">Sr No</th>
										<th style="width:300px;">Questions</th>
										<th style="width:300px;">Answers</th>
									</tr>
								
										<?php
										foreach ($questions as $que) 
										{
											$sr_no++;
										?>
											<tr>
												<td><?=$sr_no;?></td>
										<td>
											<?php 
											echo $db->rp_getValue("self_analysis_master","questions","isDelete=0 AND id='".$questions[$que_count]."'");
											?>
											</td>
											<td>
												<?php 
											echo $answers_d[$que_count];
											?>
										</td>

											<?php $que_count++;
											?>		
										</td>
											</tr>
										<?php 
										}
										?>
								
								</tbody>
							</thead>
						</table>
						<?php

					//}
					
					


					//echo $db->rp_getValue("self_analysis_master","questions","isDelete=0 AND id='".$ctable_d['questions_id']."'") 
					?><!-- </td> -->

				<!-- <td><?php echo $ctable_d['answers']; ?></td> -->
				<td><?php echo date('d-m-Y',strtotime($ctable_d['created_date']));?></td>
				<input type="hidden" name="b_id<?php echo $count ?>" value="<?php echo $ctable_d['id']; ?>">

				</td>
			</tr>

		<?php

			}
		}
	}
		else
		{
			?>
			<tr>
				<td colspan="6" style="margin:0;text-align:center"><?= "Please Select Sales Person" ?></td>
			</tr>
			<?php 
		}



		?>

        </tbody>

    </table>

    <div class="row">

		<div class="col-md-10 pull-left">

			<div class="btn-group">

				<!-- <a id="add_news" href="news_crud.php?mode=a" class="btn sbold blue-ebonyclay"> Add New

					<i class="fa fa-plus"></i>

				</a> -->

			</div>

		</div>

		<div class="col-md-2">
			<div class="btn-group">

				<input type="hidden" name="disp_count" value="<?php echo $count; ?>">

				<!-- <button type="submit" name="submit" onClick="document.frm.submit();" class="btn btn-primary btn-flat" >Update</button> -->

			</div>
		</div>

		<div class="col-md-1 pull-left">

			<div class="dataTables_info">

				<label >Rows Limit:</label>

				<select id="numRecords" class="form-control" onChange="changeDisplayRowCount(this.value);">

					<option value="500" <?php if ($_REQUEST["show"] == 500 || $_REQUEST["show"] == "") {
											echo ' selected="selected"';
										}  ?>>500</option>
					<option value="1000" <?php if ($_REQUEST["show"] == 1000) {
											echo ' selected="selected"';
										}  ?>>1000</option>
					<option value="2000" <?php if ($_REQUEST["show"] == 2000) {
												echo ' selected="selected"';
											}  ?>>2000</option>
					<option value="5000" <?php if ($_REQUEST["show"] == 5000) {
												echo ' selected="selected"';
											}  ?>>5000</option>

				</select>

			</div>

		</div>

		<div class="col-md-6 ">

			<div class="dataTables_paginate paging_simple_numbers ">

				<ul class="pagination">

				<?php 

				echo $db->rp_paginate_function($item_per_page, $page_number, $get_total_rows, $total_pages); 

				?>

				</ul>

			</div>

		</div>

	</div>

	<br/>

</form>
<script type="text/javascript">
	$("#sales_executive").select2();
</script>
<?php require_once("disconnect.php"); ?>



