<?php
$page_id=400;$page_slug='dashboard';
/*
 * @author Ravi Patel
 */
include("connect.php");
$ctable 	= "visit";
$ctable1 	= "User";

$ctable_where = " isDelete=0";

// print_r($_REQUEST["sales_executive"]);exit;
if(isset($_REQUEST["id"]) && $_REQUEST["id"]!="" && $_REQUEST["id"]!=undefined){
	$ctable_where .= " AND user_id='".$_REQUEST["id"]."'";
}
if(isset($_REQUEST['date']) && $_REQUEST['date']!=""){

	$ctable_where .= " AND DATE(created_date)='".date("Y-m-d",strtotime($_REQUEST['date']))."'";
}
$ctable_r = $db->rp_getData($ctable,"*",$ctable_where,"id DESC",0);
$salesexename = $db->rp_getValue("sales_executive","name","id='".$_REQUEST["id"]."'",0);
?>
<link rel="stylesheet" href="css/lightbox.css" />
<?php
if(mysqli_num_rows($ctable_r)>0){
?>
<div class="col-md-6 col-xs-12 col-sm-3" style="margin-bottom: 10px;">
  <span style="font-size: 20px">Visit</span>
  <button type="button" class="btn btn-sm green-haze print" style="margin-left: 20px;background-color: #f0ad4e;color: #fff; " name="print" onClick="genPrint()" id="print" href="" title="Download XL Report"><i class="fa fa-print"></i>Print</button>
</div>
<table id="datatable_1" class="table table-striped table-bordered table-hover mt-15">
    <thead>
        <tr>
            <th>No.</th>
            <th>Company Name</th>
            <th>Customer Name</th>
            <th>Mobile No.</th>
            <th>Date and Time</th>
			     <th>Visit  Start<br/> Address</th>
            <th>Visit Start<br/> Image</th>
            <th>Visit Start<br/> Time</th>  
            <th>Visit Stop <br/> Address</th>
            <th>Visit Stop <br/> Image</th>
            <th>Visit Stop <br/> Time</th> 
        </tr>
    </thead>
    <tbody>
    <?php
        $count = 0;
        while($ctable_d = mysqli_fetch_array($ctable_r)){
        	++$count;
    ?>
        <tr>
            <td><?php echo $ctable_d['id']; ?></td>
            <td><?php echo $db->rp_getValue("executive","company_name","id='".$ctable_d['customer_id']."'") ?></td>
            <td><span class="<?php echo ($ctable_d['isActive']==0)?"text-danger":"text-success"; ?>"><?php echo $db->rp_getValue("executive","cname","id='".$ctable_d['customer_id']."'") ?></span></td>
            <td><?php echo $db->rp_getValue("executive","phone","id='".$ctable_d['customer_id']."'") ?></td>
            <td><?php echo date("d-m-Y H:i:s",strtotime($ctable_d['created_date'])); ?></td>
             <td><?php echo $ctable_d['app_address'];/*$db->getAddress($ctable_d['latitude'],$ctable_d['longitude']);*/ ?></td>
                <td>
                  <?php 
                    $img = explode(",", $ctable_d['image_path']);
                    $imgpath = array();
                    for ($i=0; $i < sizeof($img); $i++)
                    { 
                      $imgpath[] ="../resource/image/".$db->rp_getValue("media","url","reference_id='".$ctable_d["id"]."' AND id='".$img[$i]."'",0);
                    }
                      for ($i=0; $i < sizeof($imgpath); $i++)
                      {
                    if($i==0){
                  ?>
                  <a href="<?=$imgpath[$i]?>" data-lightbox="visit<?=$count?>" data-title="visit <?=$ctable_d['id']?>"><img src="<?=$imgpath[$i]?>" style="height: 80px;border-radius: 5px;border:1px solid #909090;"></a>
                  <?php }else{
                    ?>
                      <div class="hidden">
                        <a href="<?=$imgpath[$i]?>" data-lightbox="visit<?=$count?>" data-title="visit <?=$ctable_d['id']?>"><img src="<?=$imgpath[$i]?>" style="height: 80px;border-radius: 5px;border:1px solid #909090;"></a>
                      </div>
                    <?php
                      }
                    }
                  ?>
                </td> 
                <td><?php if($ctable_d['start_date_time']!="0000-00-00 00:00:00"){echo date('d-m-Y h:i A',strtotime($ctable_d['start_date_time']));} else{echo "";}?></td> 
                   
               <td><?php echo $ctable_d['stop_app_address']; ?></td>
                <td>
                  <?php 
                  $img = explode(",", $ctable_d['stop_image_path']);
                  $imgpath = array();
                  for ($i=0; $i < sizeof($img); $i++)
                  { 
                    $imgpath[] = "../resource/image/".$db->rp_getValue("media","url","reference_id='".$ctable_d["id"]."' AND id='".$img[$i]."'",0);
                  }
                  for ($i=0; $i < sizeof($imgpath); $i++)
                  {
                    if($i==0)
                    {
                      ?>
                        <a href="<?=$imgpath[$i]?>" data-lightbox="visit<?=$count?>" data-title="stop_visit <?=$ctable_d['id']?>"><img src="<?=$imgpath[$i]?>" style="height: 80px;"></a>
                      <?php 
                    }
                    else
                    {
                      ?>
                      <div class="hidden">
                        <a href="<?=$imgpath[$i]?>" data-lightbox="visit<?=$count?>" data-title="stop_visit <?=$ctable_d['id']?>"><img src="<?=$imgpath[$i]?>" style="height: 80px;"></a>
                      </div>
                      <?php
                    }
                  }
                  ?>
                </td>
                <td><?php if($ctable_d['stop_date_time']!="0000-00-00 00:00:00"){echo date('d-m-Y h:i A',strtotime($ctable_d['stop_date_time']));} else{echo "";}?></td>          
        </tr>


		<!-- Modal -->
		<div id="OpenMap" class="modal fade" role="dialog">
		  <div class="modal-dialog" style="width: 970px;">

		    <!-- Modal content-->
		    <div class="modal-content" >
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal">&times;</button>
		        <h4 class="modal-title">Visit</h4>
		      </div>
		      <div class="modal-body">
		        <div id="map_canvas"></div>
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		      </div>
		    </div>

		  </div>
		</div>
		<!-- Modal -->
		<div id="OpenImg" class="modal fade" role="dialog">
		  <div class="modal-dialog">

		    <!-- Modal content-->
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal">&times;</button>
		        <h4 class="modal-title">Visit</h4>
		      </div>
		      <div class="modal-body">
		        <div id="imagedata" >
		        </div>
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		      </div>
		    </div>
		  </div>
		</div>
    <?php
        }
    }
    else
    {
    	?>
    	<div>Sorry No Visit Available</div>
    	<?php
    }
    ?>
    </tbody>
</table>
<script src="js/lightbox.js"></script>
<!-- <script src="js/jssor.slider-28.0.0.min.js"></script> -->
<script type="text/javascript">
	$(document).ready(function(){
    	var screen_width = $(window).width();
		var screen_height = $(window).height();
		$(".modal-dialog").css("width",screen_width-200);
		$(".modal-dialog").css("height",screen_height-250);
	});
	var count = 0;
	$(".mapbtn").click(function(){

		var date = "<?=$_REQUEST['date']?>";
		var salesexename = "<?=$salesexename?>";
		lat = $(this).data("lat");
		lng = $(this).data("long");
		app_address = $(this).data("app_address");
		$.ajax({
            url: "get_visit_map.php",
            data: {
                lat: lat,
                lng: lng,
                date: date,
                app_address: app_address,
                salesexename: salesexename,
            },
            beforeSend: function() {
                $("#map_canvas").html("<div class='row text-center'><div class='col-sm-12'><h2><i class='fa fa-refresh fa-spin'></i>&nbsp;Loading..</h2></div></div>");
            },
            success: function(result) {
                $("#map_canvas").html(result);
            }
        });
        
		/*var uluru = {lat: lat, lng: lng};
		var screen_width = $(window).width();
		var screen_height = $(window).height();
			$("#map_canvas").css("width",screen_width-250);
			$("#map_canvas").css("height",screen_height-230);
		  var map = new google.maps.Map(document.getElementById('map_canvas'), {
		    zoom: 15,
		    center: uluru
	  	});*/
	});
</script>

<script type="text/javascript">
  function genPrint()
  {
    var date="<?= $_REQUEST['date'];?>";
    var id="<?= $_REQUEST['id'];?>";
     var myWindow = window.open('print_visit.php?id='+id+'&date='+date,'','width=700,height=800');
    
    myWindow.print();
  }
</script>
<?php require_once 'disconnect.php';  ?>