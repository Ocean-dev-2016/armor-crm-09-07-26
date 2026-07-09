<?php 
   $page_id=400;$page_slug='dashboard';
   $ctable  = "salesexecutive_tracking";
   $ctable1   = "Sales Officer Tracking";
   $main_page   = $ctable;
   $page    = "manage_".$ctable;
   $page_title = "Manage ".$ctable1;
   include("connect.php");
   $id=(isset($_REQUEST['id']) && $_REQUEST['id']!="")?$_REQUEST['id']:"";
   $date=date('Y-m-d',strtotime($_REQUEST['date']));
   

    function formatBytes($size, $precision = 2)
    {
        $base = log($size, 1024);
        $suffixes = array('', 'KB', 'MB', 'GB', 'TB');   

        return round(pow(1024, $base - floor($base)), $precision) .' '. $suffixes[floor($base)];
    }

   if($id==0)
   {
    ?>
      <strong style="color: red">Device Information:</strong><br/>
      <h3>please select executive</h3>
    <?php
   }
   else{
     $originalR=$db->rp_getData("sales_executive","*","id='".$id."'","",0);
     if ($originalR)
     {
        $LastDataR=$db->rp_getData("salesexecutive_tracking","*","sales_executive_id='".$id."'","id DESC",0,1);
        $LastData = mysqli_fetch_assoc($LastDataR);
         
       
       $originalD=mysqli_fetch_assoc($originalR);
            
          if($LastData['BatteryPercent']!="")
          {
            $originalD['BatteryPercent'] = $LastData['BatteryPercent'];
          }
          if($LastData['isGps']!="")
          {
            $originalD['isGps'] = $LastData['isGps'];
          }
          if($LastData['isWifiEnabled']!="")
          {
            $originalD['isWifiEnabled'] = $LastData['isWifiEnabled'];
          }
          if($LastData['isNetworkAvailable']!="")
          {
            $originalD['isNetworkAvailable'] = $LastData['isNetworkAvailable'];
          }
          if($LastData['NetworkType']!="")
          {
            $originalD['NetworkType'] = $LastData['NetworkType'];
          }
        ?>
          <strong style="color: red">Device Information:</strong><br/>
          <div class="table-responsive" style="height: 188px;">
            <table class="table table-striped table-bordered table-hover">
            <?php if ($originalD['BatteryPercent']!=""){ ?>
              <tr>
                <th>Battery&nbsp;&nbsp;&nbsp;</th>
                <td><?= $originalD['BatteryPercent']?>% </td>
              </tr>
            <?php } ?>
           <!--  <?php if ($originalD['isGps']!=""){ ?>
              <tr>
                <th>Gps&nbsp;&nbsp;&nbsp;</th>
                <td><?= ($originalD['isGps']=="true")?"ON":"OFF";?></td>
              </tr>
            <?php } ?>
            <?php if ($originalD['isNetworkAvailable']!=""){ ?>
              <tr>
                <th>Network&nbsp;&nbsp;&nbsp;</th>
                <td><?= ($originalD['isNetworkAvailable']=="true")?"AVAILABLE":"NOT AVAILABLE"; ?></td>
              </tr>
            <?php } ?>
            <?php if ($originalD['isWifiEnabled']!=""){ ?>
              <tr>
                <th>Wifi&nbsp;&nbsp;&nbsp;</th>
                <td><?= ($originalD['isWifiEnabled']=="true")?"ON":"OFF"; ?></td>
              </tr>
            <?php } ?>
            <?php if ($originalD['NetworkType']!=""){ ?>
              <tr>
                <th>Connected Network&nbsp;&nbsp;&nbsp;</th>
                <td><?= $originalD['NetworkType']?></td>
              </tr>
            <?php } ?>
            </table>
            <table class="table table-striped table-bordered table-hover">
              <?php if ($originalD['PhoneNumber']!=""){ ?>
                <tr>
                  <th>Mobile Number&nbsp;&nbsp;&nbsp;</th>
                  <td><?= $db->formatPhoneNumber($originalD['PhoneNumber'])?></td>
                </tr>
              <?php } ?> -->
              <?php if ($originalD['Operator']!=""){ ?>
                <tr>
                  <th>Operator&nbsp;&nbsp;&nbsp;</th>
                  <td><?= $originalD['Operator']?></td>
                </tr>
              <?php } ?>
              <?php if ($originalD['AvailableInternalMemorySize']!="" && $originalD['TotalInternalMemorySize']!=""){ ?>
                <tr>
                  <th>Internal Memory &nbsp;&nbsp;&nbsp;</th>
                  <td><?= formatBytes($originalD['AvailableInternalMemorySize']); ?>/<?= formatBytes($originalD['TotalInternalMemorySize']); ?></td>
                </tr>
              <?php } ?>
             <!--  <?php if ($originalD['imei']!=""){ ?>
                <tr>
                  <th>IMEI No.&nbsp;&nbsp;&nbsp;</th>
                  <td><?= $originalD['imei']?></td>
                </tr>
              <?php } ?> -->
              <?php if ($originalD['AppVersionName']!=""){ ?>
                <tr>
                  <th>App Version &nbsp;&nbsp;&nbsp;</th>
                  <td><?= $originalD['AppVersionName']?></td>
                </tr>
              <?php } ?>
              <?php if ($originalD['AppName']!=""){ ?>
                <tr>
                  <th>App Name&nbsp;&nbsp;&nbsp;</th>
                  <td><?= $originalD['AppName']?></td>
                </tr>
              <?php } ?>
              <!-- <?php if ($originalD['Device']!=""){ ?>
                <tr>
                  <th>Device&nbsp;&nbsp;&nbsp;</th>
                  <td><?= $originalD['Device']?></td>
                </tr>
              <?php } ?>
              <?php if ($originalD['Hardware']!=""){ ?>
                <tr>
                  <th>Hardware&nbsp;&nbsp;&nbsp;</th>
                  <td><?= $originalD['Hardware']?></td>
                </tr>
              <?php } ?>
              <?php if ($originalD['Manufacturer']!=""){ ?>
                <tr>
                  <th>Manufacturer&nbsp;&nbsp;&nbsp;</th>
                  <td><?= $originalD['Manufacturer']?></td>
                </tr>
              <?php } ?>
              <?php if ($originalD['Model']!=""){ ?>
                <tr>
                  <th>Model&nbsp;&nbsp;&nbsp;</th>
                  <td><?= $originalD['Model']?></td>
                </tr>
              <?php } ?>
              <?php if ($originalD['OsVersion']!=""){ ?>
                <tr>
                  <th>Os Version&nbsp;&nbsp;&nbsp;</th>
                  <td><?= $originalD['OsVersion']?></td>
                </tr>
              <?php } ?>
              <?php if ($originalD['SdkVersion']!=""){ ?>
                <tr>
                  <th>Sdk Version&nbsp;&nbsp;&nbsp;</th>
                  <td><?= $originalD['SdkVersion']?></td>
                </tr>
              <?php } ?>
              <?php if ($originalD['sIMSerial']!=""){ ?>
                <tr>
                  <th>sIMSerial&nbsp;&nbsp;&nbsp;</th>
                  <td><?= $originalD['sIMSerial']?></td>
                </tr>
              <?php } ?> -->
            </table>
          </div>
<?php
      }
      else
      {
        ?>
        <strong style="color: red">Device Information:</strong><br/>
        <h3>No Data Available</h3>
    <?php
      }
   }
?>