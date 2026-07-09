<?php
$page_id=400;$page_slug='dashboard';
/*
 * @author Ravi Patel
 */
include("connect.php");
$ctable   = "visit";
$ctable1  = "User";

$ctable_where = " isDelete=0";

// print_r($_REQUEST["sales_executive"]);exit;
if(isset($_REQUEST["id"]) && $_REQUEST["id"]!="" && $_REQUEST["id"]!=undefined){
  $ctable_where .= " AND user_id='".$_REQUEST["id"]."'";
}
if(isset($_REQUEST['date']) && $_REQUEST['date']!=""){

  $ctable_where .= " AND DATE(created_date)='".date("Y-m-d",strtotime($_REQUEST['date']))."'";
}
$ctable_r = $db->rp_getData($ctable,"*",$ctable_where,"id DESC",0);
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.1/css/lightbox.css" />
<?php
if(mysqli_num_rows($ctable_r)>0){
?>
<div class="col-md-6 col-xs-12 col-sm-3" style="margin-top: -17px">
  <span style="font-size: 20px">Customer Wise Visit Report</span>
</div>
<table id="datatable_1" class="table table-striped table-bordered table-hover mt-15">
    <thead>
        <tr>
            <th>No.</th>
            <th>Customer Name</th>
            <th>Sales Officer Name</th>
            <th>Mobile No.</th>
            <th>Date and Time</th>
            <th>Latitude</th>
            <th>Longitude</th>
            <th>Remark</th>
            <th>location map</th>
            <th>visit images</th>
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
            <td><span class="<?php echo ($ctable_d['isActive']==0)?"text-danger":"text-success"; ?>"><?php echo $db->rp_getValue("executive","cname","id='".$ctable_d['customer_id']."'") ?></span></td>
            <td><?php echo $db->rp_getValue("executive","phone","id='".$ctable_d['customer_id']."'") ?></td>
            <td><?php echo date("d-m-Y H:i:s",strtotime($ctable_d['created_date'])); ?></td>
            <td><?php echo stripslashes($ctable_d['latitude']); ?></td>
            <td><?php echo stripslashes($ctable_d['longitude']); ?></td>
            <td><?php echo stripslashes($ctable_d['remark']); ?></td>
            <td>
              <!-- Trigger the modal with a button -->
              <a class="mapbtn" data-lat="<?php echo stripslashes($ctable_d['latitude']); ?>" data-long="<?php echo stripslashes($ctable_d['longitude']); ?>" data-toggle="modal" data-target="#OpenMap">
                <img src="<?=SITEURL?>resource/map.png" style="height: 80px;">
              </a>
            </td>
            <td>
            <?php 
                $img = explode(",", $ctable_d['image_path']);
                $imgpath = array();
                for ($i=0; $i < sizeof($img); $i++)
                { 
                  $imgpath[] = SITEURL."resource/image/".$db->rp_getValue("media","url","reference_id='".$ctable_d["id"]."' AND id='".$img[$i]."'",0);
                }
                  for ($i=0; $i < sizeof($imgpath); $i++)
                  {
                if($i==0){
              ?>
              <a href="<?=$imgpath[$i]?>" data-lightbox="visit<?=$count?>" data-title="visit <?=$ctable_d['id']?>"><img src="<?=$imgpath[$i]?>" style="height: 80px;"></a>
              <?php }else{
                ?>
                  <div class="hidden">
                    <a href="<?=$imgpath[$i]?>" data-lightbox="visit<?=$count?>" data-title="visit <?=$ctable_d['id']?>"><img src="<?=$imgpath[$i]?>" style="height: 80px;"></a>
                  </div>
                <?php
              }
            }
          ?>
          </td>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.1/js/lightbox.js"></script>
<script type="text/javascript">
  $(document).ready(function(){
    var screen_width = $(window).width();
    var screen_height = $(window).height();
    $(".modal-dialog").css("width",screen_width-200);
    $(".modal-dialog").css("height",screen_height-250);
  });
  var count = 0;
  $(".mapbtn").click(function(){
    lat = $(this).data("lat");
    lng = $(this).data("long");
    var uluru = {lat: lat, lng: lng};
    var screen_width = $(window).width();
    var screen_height = $(window).height();
      $("#map_canvas").css("width",screen_width-250);
      $("#map_canvas").css("height",screen_height-230);
      var map = new google.maps.Map(document.getElementById('map_canvas'), {
        zoom: 15,
        center: uluru
      });
  });
</script>
<?php
$db->disconnect(); 
?>