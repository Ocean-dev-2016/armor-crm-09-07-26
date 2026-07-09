<?php
$page_id=562;$page_slug='page_category';
$ctable   = "salesexecutive_tracking";
$ctable1  = "Sales Officer Tracking";
$main_page  = $ctable;
$page     = "manage_".$ctable;
$page_title = "Manage ".$ctable1;
require_once("connect.php");
require_once("../include/class.sales_executive.php");
$id=$_REQUEST['id'];
$name=ucfirst($db->rp_getValue("sales_executive","name","id='".$id."'"));
$phone=ucfirst($db->rp_getValue("sales_executive","phone","id='".$id."'"));
$sales_id=isset($_REQUEST['sid'])?$_REQUEST['sid']:"";
$date=date('d-m-Y',strtotime($_REQUEST['date']));
$date_new=date('Y-m-d',strtotime($_REQUEST['date']));
?>
<!DOCTYPE html>
<html>
  <head>
    <title><?php echo $page_title; ?> | <?php echo SITETITLE; ?></title>
    <style type="text/css">
      html, body {
        height: 100%;
        margin: 0;
        padding: 0;
        font-family: Roboto, Noto, sans-serif;
      }

      #map {
        height: 500px;
      }

      #interpolate {
        width: 2em;
        height: 2em;
      }

      #coords {
        resize: vertical;
        min-height: 75px;
        max-height: 200px;
      }

      .block {
        clear: both;
        margin: 1.5em auto;
        text-align: center;
      }

      #legend {
        float: center;
        margin: 5px 15px;
        font-size: 13px;
      }

      .button {
      display: inline-block;
      position: relative;
      border: 0;
      padding: 0 1.7em;
      min-width: 120px;
      height: 32px;
      line-height: 32px;
      border-radius: 2px;
      font-size: 0.9em;
      background-color: #fff;
      color: #646464;
    }

    .button.narrow {
      width: 60px;
    }

    .button.grey {
      background-color: #eee;
    }

    .button.blue {
      background-color: #4285f4;
      color: #fff;
    }

    .button.green {
      background-color: #0f9d58;
      color: #fff;
    }

    .button.raised {
      transition: box-shadow 0.2s cubic-bezier(0.4, 0, 0.2, 1);
      transition-delay: 0.2s;
      box-shadow: 0 2px 5px 0 rgba(0, 0, 0, 0.26);
    }

    .button.raised:active {
      box-shadow: 0 8px 17px 0 rgba(0, 0, 0, 0.2);
      transition-delay: 0s;
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
    }

    .floating.panel {
      width: 400px;
    }

    .coords-small {
      width: 350px;
    }

    .coords-large {
      width: 400px;
    }

    .button-div {
      padding: 0px 50px;
      width: 300px;
      line-height: 40px;
    }

    #toggle {
      width: 25px;
      z-index: 10;
      cursor: default;
      font-size: 2em;
      padding: 1px;
      color: #999;
      display: none;
    }

    </style>
    <?php require_once("include_js.php"); ?>
    <?php require_once("include_css.php"); ?>
   </head>
  <body>

    <div class="row">
      <div class="col-md-6 col-xs-12 col-sm-3" style="margin-top:10px">
        <h3><?php echo $name." (".$phone.") "."date:-".date('d-m-Y',strtotime($_REQUEST['date'])).""; ?></h3>
        <input value="<?php echo date('d-m-Y',strtotime($_REQUEST['date'])); ?>" type="hidden" id="date">
      </div>
    </div>
    <table cellpadding="2">
    <tr>
      <td>Search By Executive & Date : &nbsp;&nbsp; &nbsp;&nbsp; 
      </td>
      <td>
        <select style="width:230px !important;" class="form-control" name="exicutive_id" id="exicutive_id">
        <option value="">Select Executive</option>
        <?php
          $data = $db->rp_getData("sales_executive","*","isDelete=0","",0);
          while ($data_d = mysqli_fetch_assoc($data)) {
          ?>
            <option <?php echo ($id==$data_d['id'])?"selected":"" ; ?> value="<?php echo $data_d['id'];?>"   ><?php echo $data_d['name'];?></option>
          <?php
          }
        ?>
        </select>
      </td>
      <td>
        <div class="form-group" style="width:230px !important;margin-top: -13px;margin-left:20px;padding: 0px;margin-right: 0px;">
           <label>&nbsp;&nbsp;</label>
          <input type="text" style="height: 37px;padding: 0px;" name="ToDate" class="form-control input-small" id="ToDate" value="<?php echo $date; ?>" placeholder="Date" autocomplete="off">  
        </div>
      </td>
      <td>
        <div class="form-group">
          <input class="btn btn-success btn-sm" style="height: 37px;margin-top: 10px;" type="submit" value="show last punch" onclick="getByDate('punch');">
        </div>
      </td>
      <!-- <td>
        <div class="form-group">
          <input class="btn btn-danger btn-sm" style="height: 37px;margin-top: 10px;margin-left: 10px;" type="submit" value="show route" onclick="getByDate('route');">
        </div>
      </td> -->
      <td>
        <div class="form-group">
            <!-- <button style="height: 37px;margin-top: 10px;margin-left: 10px;" onclick="clearAll()" id="eg4" class="button raised blue" >LOAD ROUTE</button> -->
            <button style="height: 37px;margin-top: 10px;margin-left: 10px;" id="eg4" class="button raised blue" onclick="getByDate('route')">LOAD ROUTE</button>
        </div>
      </td>
    </tr>
    </table>

    <div class="floating hidden" id="toggle" style="margin-top: 56px;">-</div>
    <div id="map2">
      dasdsass
    </div>  


<script type="text/javascript">
function getByDate(flag)
{
  id=$("#exicutive_id").val();
  date=$("#ToDate").val();
  
  if(flag == 'route' || flag == 'exe_id')
  {
    if(id != "")
    {
      // window.location = 'maproute.php?id='+id+'&date='+date;
      $.ajax({
        type: "POST",
        url: "maproute_get_ajax.php",
        data: 'id='+id+'&date='+date,
        success: function(result){
            alert(result);
            $("#map2").html(result);
            // $("#eg4").trigger('click');
          },
          error: function(){
             alert('assssss');
          }
      });
    }
    else
    {
      toastr.error("please select executive");
    }
  }
  if(flag == 'punch')
  {
    // window.location = 'tracking_all.php?id='+id+'&date='+date;
    $.ajax({
      type: "POST",
      url: "tracking_all_get_ajax.php",
      data: 'id='+id+'&date='+date,
      success: function(result){ 
          // displayRecords();
          
          $("#map2").html(result);
        }
    });
  }
}
  </script>
    </body>
</html>