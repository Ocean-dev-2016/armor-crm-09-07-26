<?php
require_once("../include/class.sales_executive.php");
$id=(isset($_REQUEST['id']) && $_REQUEST['id']!="")?$_REQUEST['id']:"";
$sales_executive=new SalesExecutive();
$phone="";
if($id=="")
{
	$name="ALL SALSE EXECUTIVE";
	// $date=date('30-03-2019');
	if($_REQUEST['date'])
	{
		$date=date('d-m-Y',strtotime($_REQUEST['date']));
	}
	else
	{
		$date=date('d-m-Y');
	}
	$response=$sales_executive->trackSalesAll($date);
}
else
{
	$name=ucfirst($db->rp_getValue("sales_executive","name","id='".$id."'"));
	$phone= " (".ucfirst($db->rp_getValue("sales_executive","phone","id='".$id."'")).") ";
	$sales_id=isset($_REQUEST['sid'])?$_REQUEST['sid']:"";
	if($_REQUEST['date'])
	{
		$date=date('d-m-Y',strtotime($_REQUEST['date']));
	}
	else
	{
		$date=date('d-m-Y');
	}
	$response=$sales_executive->trackSalesAll($date,$id);

	if($id!="")
	{
		$where="isDelete=0";
		$where .=" AND sales_executive_id=".$id."";
		$where.=" AND DATE(date)='".date("Y-m-d")."'";
		$data_d = $db->rp_getData("salesexecutive_tracking","*",$where,"id desc",0);
		$data = mysqli_fetch_assoc($data_d);
		$last_time = $data['date'];
	}
	if($last_time)
	{
		$last_time = date("G:i:s",strtotime($last_time));
	}
	$curr_time = date("G:i:s");
	$sec = ACTIVE_TIME*60;
	if(strtotime($curr_time)>strtotime($last_time)+$sec)
	{
		$status = "offline";
	}
	else
	{
		$status = "online";
	}
}
?>
<link rel="stylesheet" type="text/css" href="assets/global/plugins/jquery-ui/jquery-ui.min.css"/>

<style>
      /* Always set the map height explicitly to define the size of the div
       * element that contains the map. */
      #map {
        height: 500px;
      }
      #toggle {
	      width: 25px;
	      z-index: 10;
	      cursor: default;
	      font-size: 2em;
	      padding: 1px;
	      color: #999;
	      display: none;
	      margin-right: 10px;
	    }
	    .floating {
	      position: absolute;
	      top: 10px;
	      right: 10px;
	      z-index: 5;
	      background-color: rgba(255, 255, 255, 0.75);
	      padding: 1px;
	      border: 1px solid #999;
	      text-align: center;
	      line-height: 18px;
	      margin-right: 10px;
	    }
	    .floating.panel {
	      width: 200px;
	    }
	    .block {
	        clear: both;
	        margin: 1.5em auto;
	        text-align: left;
	      }
      /* Optional: Makes the sample page fill the window. */
      html, body {
        height: 100%;
        margin: 0;
        padding: 0;
      }
    </style>

		<div class="row">
		 <div class="col-md-12 col-xs-12 col-sm-3" style="">
			<h3>
				<?php if($id != ""){ ?>
					<a style="margin-left: 10px;height: 60px;">
		          		<img src="<?=SITEURL."images/".$status.".gif"?>" height="25px;" border="0" alt="error" />(<?=$status?>)
		        	</a>
	        	<?php } ?>
				<b><?php echo $name.$phone."</b> - LAST PUNCH - Date:-".date('d-m-Y',strtotime($date)).""; ?>
			</h3>
			<input value="<?php echo date('d-m-Y',strtotime($_REQUEST['date'])); ?>" type="hidden" id="date">
		 </div>
		</div>
		<div class="row">
			<div class="col-sm-4">
				<select style="" class="form-control" name="exicutive_id" id="exicutive_id" onChange="getMonths(this.value);">
				<option value="">Select Executive</option>
				<?php
					$data = $db->rp_getData("sales_executive","*","isDelete=0","",0);
					$offline_member = array();
					while ($data_d = mysqli_fetch_assoc($data)) {
						$last_time = 0;
						if($data_d['id']!="")
						{
							$where1="isDelete=0";
							$where1 .=" AND sales_executive_id=".$data_d['id']."";
							$where1 .=" AND DATE(date)='".date("Y-m-d")."'";
							$data_data = $db->rp_getData("salesexecutive_tracking","*",$where1,"id desc",0);
						}
						$data1 = mysqli_fetch_assoc($data_data);
						$last_time = $data1['date'];
						if($last_time)
						{
							$last_time = date("G:i:s",strtotime($last_time));
						}
						$curr_time = date("G:i:s");
						$sec = ACTIVE_TIME*60;
						if(strtotime($curr_time)>strtotime($last_time)+$sec)
						{
							array_push($offline_member,$data_d['name']);
						}
					?>
						<option <?php echo ($id==$data_d['id'])?"selected":"" ; ?> value="<?php echo $data_d['id'];?>"   ><?php echo $data_d['name'];?></option>
					<?php
					}
				?>
				</select>
			</div>
			<div class="col-md-2">
				<div class="form-group">
					<input type="text" name="ToDate" class="form-control " id="ToDate" value="<?php echo $date; ?>" placeholder="Date" autocomplete="off">
				</div>
			</div>
			<div class="col-md-2">
				<div class="form-group">
				<input class="btn btn-success btn-sm" style="" type="submit" value="show last punch" onclick="getByDate('punch');">
				</div>
			</div>
			<div class="col-md-2">
				<div class="form-group">
				<a target="_blank" class="btn btn-danger btn-sm" style="" type="submit" onclick="getByDate('route');">Go To Route Map</a>
				</div>
			</div>
		</div>
		<div class="loading-div" style="display:none;"> <img src="assets/admin/layout/img/ajax-loader.gif" alt="" style="margin-bottom: 10%;;margin-top:10%;padding-left:48%;" > </div>
		<div id="results">
		 <div id="map"></div>

		</div>

		<div class="floating" id="toggle"><img src="<?=SITEURL."images/on.png"?>" width="20px;"></div>
		<div id="panel" >
		  <div class="block">
		   <div style="margin-left: 20%;">
		    <strong style="color: red">All Ofline User List:</strong><br/>
		    <?php 
		    	$n = sizeof($offline_member);
		    	for($i = 0; $i< $n; $i++)
		    	{
		    ?>
		    	<?=($i+1)." - ".$offline_member[$i];?><br/>
		    <?php
				}
		    ?>
		  </div>
		  </div>
		</div>

    

