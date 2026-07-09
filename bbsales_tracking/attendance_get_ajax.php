<?php
$page_id=569;$page_slug='dispatch_pages';
/*
 * @author Ravi Patel
 */
include("connect.php");
$ctable 	= "attendance";
$ctable1 	= "Attendance";
$sales_id=$_REQUEST['sales_id'];
$ctable_where = "";
// Get the total number of rows in the table

//for admin login


$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"]) ) ? intval($_REQUEST["show"]) : 100;

if(isset($_REQUEST["page"])){
	$page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
	if(!is_numeric($page_number)){die('Invalid page number!');} //incase of invalid page number
}else{
	$page_number = 1; //if there's no page number, set it to 1
}
//status
$ctable_where .="sales_id=".$sales_id." AND isDelete=0";
if(isset($_REQUEST['month_id']) && $_REQUEST['month_id']!="" && $_REQUEST['month_id']!=NULL)
{
 $ctable_where .= " AND MONTH(date_time) = '".$_REQUEST['month_id']."'";
 $ctable_where_month .= " AND MONTH(date_time) = '".$_REQUEST['month_id']."'";
}
else{
	 $date = date("m");
	 $ctable_where .= " AND MONTH(date_time)='".$date."' AND 1=1";
}
////filter by Year
if(isset($_REQUEST['year_id']) && $_REQUEST['year_id']!="" && $_REQUEST['year_id']!=NULL)
{

 $ctable_where .= " AND YEAR(date_time) = '".$_REQUEST['year_id']."'";
}
else{
	 $ctable_where .= " AND 1=1";
}

$get_total_rows = $db->rp_getTotalRecord($ctable,$ctable_where); //hold total records in variable
//break records into pages
$total_pages = ceil($get_total_rows/$item_per_page);
//get starting position to fetch the records
$page_position = (($page_number-1) * $item_per_page);

$ctable_r = $db->rp_getData($ctable,"*",$ctable_where,"date_time DESC limit $page_position, $item_per_page",0);
?>
<style type="text/css">
.thumb img { 
	border:1px solid #000;
	margin:3px;
	float:left;
}
.thumb span { 
	position:absolute;
	visibility:hidden;
	/*visibility:visible;*/
	right: 30%;
	margin-top: -60px;
	background: #fff;
}
.thumb:hover, .thumb:hover span { 
	visibility:visible; 
	z-index:9999;
}
#myImg {
  border-radius: 5px;
  cursor: pointer;
  transition: 0.3s;
}

#myImg:hover {opacity: 0.7;}

/* The Modal (background) */
.modal {
  display: none; /* Hidden by default */
  position: fixed; /* Stay in place */
  z-index: 1; /* Sit on top */
  padding-top: 100px; /* Location of the box */
  left: 0;
  top: 0;
  width: 100%; /* Full width */
  height: 100%; /* Full height */
  overflow: auto; /* Enable scroll if needed */
  background-color: rgb(0,0,0); /* Fallback color */
  background-color: rgba(0,0,0,0.9); /* Black w/ opacity */
}

/* Modal Content (image) */
.modal-content {
  margin: auto;
  display: block;
  width: 80%;
  max-width: 700px;
}

/* Caption of Modal Image */
#caption {
  margin: auto;
  display: block;
  width: 80%;
  max-width: 700px;
  text-align: center;
  color: #ccc;
  padding: 10px 0;
  height: 150px;
}

/* Add Animation */
.modal-content, #caption {  
  -webkit-animation-name: zoom;
  -webkit-animation-duration: 0.6s;
  animation-name: zoom;
  animation-duration: 0.6s;
}

@-webkit-keyframes zoom {
  from {-webkit-transform:scale(0)} 
  to {-webkit-transform:scale(1)}
}

@keyframes zoom {
  from {transform:scale(0)} 
  to {transform:scale(1)}
}

/* The Close1 Button */
.close1 {
  position: absolute;
  top: 15px;
  right: 35px;
  color: #f1f1f1;
  font-size: 40px;
  font-weight: bold;
  transition: 0.3s;
}

.close1:hover,
.close1:focus {
  color: #bbb;
  text-decoration: none;
  cursor: pointer;
}

/* 100% Image Width on Smaller Screens */
@media only screen and (max-width: 700px){
  .modal-content {
    width: 100%;
  }
}
</style>
<form action="" name="frm" id="frm" method="post">
	<table id="datatable_attandence" class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th>No</th>
                <th>Date</th>
                <th>Total Time</th>
								<th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if(mysqli_num_rows($ctable_r)>0){
            $count = 0;
			$previous_date="";
            while($ctable_d = mysqli_fetch_array($ctable_r)){
				$current_date=date("Y-m-d ",strtotime($ctable_d['date_time']));
				if($previous_date==$current_date)
				{
					continue;
				}
				else{
					$previous_date=$current_date;
				}

				/*$in_time=$db->rp_getValue("attendance","date_time","sales_id='".$ctable_d['sales_id']."' AND DAY(date_time)='".date('d',strtotime($ctable_d['date_time']))."' AND inout_status='In'","",0);

				$out_time=$db->rp_getValue("attendance","date_time","sales_id='".$ctable_d['sales_id']."' AND DAY(date_time)='".date('d',strtotime($ctable_d['date_time']))."' AND inout_status='Out'","",0);

				$final_in_time =  date("h:i:s",strtotime($in_time));
				$final_out_time = date("h:i:s",strtotime($out_time));

				$total_time = date("h:i:s",strtotime($final_out_time - $final_in_time));*/

				$in_time = $db->rp_getData("attendance","date_time","sales_id='".$ctable_d['sales_id']."' AND DATE(date_time)='".date('Y-m-d',strtotime($ctable_d['date_time']))."' AND inout_status='In'","",0);
				$in = array();
				while($in_time_d = mysqli_fetch_assoc($in_time))
				{
					$in[] = $in_time_d['date_time'];
				}
				// print_r($in); 

				$out_time = $db->rp_getData("attendance","date_time","sales_id='".$ctable_d['sales_id']."' AND DATE(date_time)='".date('Y-m-d',strtotime($ctable_d['date_time']))."' AND inout_status='Out'","",0);
				$out = array();
				while($out_time_d = mysqli_fetch_assoc($out_time))
				{
					$out[] = $out_time_d['date_time'];
				}
				// print_r($out);
				$diffrence ="00:00:00";
				for($i=0;$i<sizeof($in);$i++)
				{
					for($j=0;$j<sizeof($out);$j++)
					{
						if($i==$j)
						{	
							$in_date = date("Y-m-d",strtotime($in[$i]));
							// $in_time = date("H:i:s",strtotime($in[$i]));
							// echo " ";
							$out_date = date("Y-m-d",strtotime($out[$j]));
							// $out_time = date("H:i:s",strtotime($out[$j]));
							// echo " ";
							if(strtotime($in_date)==strtotime($out_date))
							{

								$in_time=$in[$i];
								$out_time=$out[$j];
								/*$date1 = strtotime("2018-09-21 11:36:37");  
								$date2 = strtotime("2018-09-21 11:36:47");  */
								// Formulate the Difference between two dates 
								// $diff = abs($date2 - $date1);  								
								$diff = abs(strtotime($out_time) - strtotime($in_time));  
								  
								  
								// To get the year divide the resultant date into 
								// total seconds in a year (365*60*60*24) 
								$years = floor($diff / (365*60*60*24));  
								  
								  
								// To get the month, subtract it with years and 
								// divide the resultant date into 
								// total seconds in a month (30*60*60*24) 
								$months = floor(($diff - $years * 365*60*60*24) 
								                               / (30*60*60*24));  
								  
								  
								// To get the day, subtract it with years and  
								// months and divide the resultant date into 
								// total seconds in a days (60*60*24) 
								$days = floor(($diff - $years * 365*60*60*24 -  
								             $months*30*60*60*24)/ (60*60*24)); 
								  
								  
								// To get the hour, subtract it with years,  
								// months & seconds and divide the resultant 
								// date into total seconds in a hours (60*60) 
								$hours = floor(($diff - $years * 365*60*604  
								       - $months*30*60*60*24 - $days*60*60*24) 
								                                   / (60*60));  
								  
								  
								// To get the minutes, subtract it with years, 
								// months, seconds and hours and divide the  
								// resultant date into total seconds i.e. 60 
								$minutes = floor(($diff - $years * 365*60*60*24  
								         - $months*30*60*60*24 - $days*60*60*24  
								                          - $hours*60*60)/ 60);  
								  
								  
								// To get the minutes, subtract it with years, 
								// months, seconds, hours and minutes  
								$seconds = floor(($diff - $years * 365*60*60*24  
								         - $months*30*60*60*24 - $days*60*60*24 
								                - $hours*60*60 - $minutes*60));  
								
								$diff=$hours.":".$minutes.":".$seconds;

								$secs = strtotime($diffrence)-strtotime("00:00:00");
								$diffrence = date("H:i:s",strtotime($diff)+$secs)." ";
							}
						}						
					}
					if(sizeof($in)>sizeof($out) && $i+1==sizeof($in))	
					{
						$current_time=date("H:i:s");
						$running_in_time=date("H:i:s",strtotime($in[$i]));

						$diff = abs(strtotime($current_time) - strtotime($running_in_time)); 							  
								  
						// To get the year divide the resultant date into 
						// total seconds in a year (365*60*60*24) 
						$years = floor($diff / (365*60*60*24));  
						  
						  
						// To get the month, subtract it with years and 
						// divide the resultant date into 
						// total seconds in a month (30*60*60*24) 
						$months = floor(($diff - $years * 365*60*60*24) 
						                               / (30*60*60*24));  
						  
						  
						// To get the day, subtract it with years and  
						// months and divide the resultant date into 
						// total seconds in a days (60*60*24) 
						$days = floor(($diff - $years * 365*60*60*24 -  
						             $months*30*60*60*24)/ (60*60*24)); 
						  
						  
						// To get the hour, subtract it with years,  
						// months & seconds and divide the resultant 
						// date into total seconds in a hours (60*60) 
						$hours = floor(($diff - $years * 365*60*604  
						       - $months*30*60*60*24 - $days*60*60*24) 
						                                   / (60*60));  
						  
						  
						// To get the minutes, subtract it with years, 
						// months, seconds and hours and divide the  
						// resultant date into total seconds i.e. 60 
						$minutes = floor(($diff - $years * 365*60*60*24  
						         - $months*30*60*60*24 - $days*60*60*24  
						                          - $hours*60*60)/ 60);  
						  
						  
						// To get the minutes, subtract it with years, 
						// months, seconds, hours and minutes  
						$seconds = floor(($diff - $years * 365*60*60*24  
						         - $months*30*60*60*24 - $days*60*60*24 
						                - $hours*60*60 - $minutes*60));  
						
						$diff=$hours.":".$minutes.":".$seconds;
						

						$secs = strtotime($diffrence)-strtotime("00:00:00");
						$diffrence = date("H:i:s",strtotime($diff)+$secs)." Running";
					}
				}
				
				
        ?>
		
            <tr>
                <td><?php echo ++$count; ?></td>
                <td><?php echo date('d-m-Y',strtotime($ctable_d['date_time'])); ?></td>
                <td><?php echo $diffrence; ?></td>
                
             <td>
			<a data-toggle="collapse" data-target="#<?php echo "P".date('Y-m-d',strtotime($ctable_d['date_time'])); ?>" class="accordion-toggle btn btn-success btn-sm">Show Attendance</a></td>
            </tr>
			<?php 
				$attendance=$db->rp_getData("attendance","*","sales_id='".$ctable_d['sales_id']."' AND DAY(date_time)='".date('d',strtotime($ctable_d['date_time']))."'","",0);
				if($attendance)
				{
					?>
					
			<tr>
					<td colspan="8" class="hiddenRow" style="padding:0px">
						<div class="accordian-body collapse" id="<?php echo "P".date('Y-m-d',strtotime($ctable_d['date_time'])); ?>"> 
						<div class="portlet box blue-hoki">
                                    <div class="portlet-title">
                                        <div class="caption" >Attendance
                                            <i class="fa fa-gift"></i>
										</div>
                                    </div>
                                    <div class="portlet-body">
                                        <div class="slimScrollDiv" style="position: relative; overflow: sroll; width: auto; height: auto;">
											<div class="scroller" style="height: auto; overflow: hidden; width: auto;" data-rail-visible="1" data-rail-color="yellow" data-handle-color="#a1b2bd" data-initialized="1">
											<table class="table table-striped table-bordered">
												<thead>								
												<tr>
													<th>Time</th>
													<th>In / Out </th>
													<th>IMEI </th>
													<th>Image</th>
												</tr>
											  </thead>
											  <tbody>
											  <?php 
												while($attendance_d=mysqli_fetch_assoc($attendance))
												{ 
													if ($attendance_d['image_path']!="" && file_exists(ATTENDANCE.$attendance_d['image_path'])) {
														$img = ATTENDANCE.$attendance_d['image_path'];
													}
													else
													{
														$img = $attendance_d['image_path'] = DEFAULTIMG;
													}
												?>
													<tr>
														<td><?php echo date('h:i A',strtotime($attendance_d['date_time']));?></td>
														<td><?php echo $attendance_d['inout_status'];?></td>
														<td><?php echo $attendance_d['imei'];?></td>
														<td>
															<div id="thumbwrap">
																<a class="thumb">
																	<span>
																		<img src="<?php echo $img ?>" height="200px" width="auto">
																	</span>
																	<img onclick="PopUp('<?php echo $img ?>')" id="myImg" class="myImg" src="<?php echo $img ?>" height="80px" width="80px">
																</a>
															</div>
														</td>
													</tr>
													<?php
												}
													?>
											  </tbody>
											</table>
											
											</div>
                                    </div>
                                </div>
					  </div> 
				  </td>
			</tr>
        <?php
            }
            }
        }
		else{
			?>
			<tr>
			<td align="center" colspan="3"><?php echo "No Attendance Found";?></td>
			</tr>
			<?php
		}
		
        ?>
        </tbody>
    </table>
    <div class="row">
		<div class="col-md-6">
			<div class="dataTables_info"> Rows Limit:
				<select id="numRecords" onChange="changeDisplayRowCount(this.value);">
					<option value="100" <?php if ($_REQUEST["show"] == 100 || $_REQUEST["show"] == "" ) { echo ' selected="selected"'; }  ?> >100</option>
					<option value="500" <?php if ($_REQUEST["show"] == 500) { echo ' selected="selected"'; }  ?> >500</option>
					<option value="1000" <?php if ($_REQUEST["show"] == 1000) { echo ' selected="selected"'; }  ?> >1000</option>
				</select>
			</div>
		</div>
		<div class="col-md-6">
			<div class="dataTables_paginate paging_simple_numbers">
				<ul class="pagination">
				<?php 
				echo $db->rp_paginate_function($item_per_page, $page_number, $get_total_rows, $total_pages); 
				?>
				</ul>
			</div>
		</div>
	</div>
</form>
<div id="myModal" class="modal">
  <span class="close1" onclick='$("#myModal").css("display","none");'>&times;</span>
  <img class="modal-content" style="height: 80%;width: auto;" id="img01">
</div>
<?php require_once("disconnect.php"); ?>