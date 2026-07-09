<?php
$page_id=659;$page_slug='self_analysis';
include("connect.php");
$ctable 	= "self_analysis";
$ctable1 	= "self_analysis";

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
<table id="example1" class="table table-striped table-bordered table-hover">

        <thead>

            <tr>
                <th style="width:50px;" class="fix-th1">No.</th>
				<th style="width:150px;" class="fix-th1">Name</th>
				<th style="width:500px;" class="fix-th1">Questions Answers</th>
				<!-- <th style="width:300px;" class="fix-th1">Answer</th> -->
				<th style="width:50px;" class="fix-th1">Created Date</th>
			</tr>

        </thead>

        <tbody>

        <?php
        if($_REQUEST['searchName']!="" || $_REQUEST['sales_executive']!="undefined")
        {

		if(mysqli_num_rows($ctable_r)>0){

			$count = 0;

			

			while($ctable_d = mysqli_fetch_array($ctable_r)){

				$count++;

		?>

			<tr>
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
												<td><?php echo $sr_no;?></td>
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