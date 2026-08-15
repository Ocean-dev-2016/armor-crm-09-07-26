<?php
 $page_id=400;$page_slug='dashboard';
 
 include("connect.php");
 $ctable   = "attendance";
 $ctable1  = "visit";
 $ctable2  = "no_order_inquiry";
 $ctable3  = "followup";
 $sales_id=$_REQUEST['id'];
 $salesexename = $db->rp_getValue("sales_executive","name","id='".$_REQUEST["id"]."'",0);
 $salesexephone = $db->rp_getValue("sales_executive","phone","id='".$_REQUEST["id"]."'",0);
 $sm_id = $db->rp_getValue("sales_executive","sm_id","id='".$_REQUEST["id"]."'",0);
 $asm_id = $db->rp_getValue("sales_executive","asm_id","id='".$_REQUEST["id"]."'",0);
 $so_id = $db->rp_getValue("sales_executive","so_id","id='".$_REQUEST["id"]."'",0);
 $se_id = $db->rp_getValue("sales_executive","se_id","id='".$_REQUEST["id"]."'",0);

 if($sm_id==0)
 {
    $type="Sales Manager";
 }
 else if($sm_id!=0 && $asm_id==0)
 {
    $type="Area Sales Manager";
 }
 else if($sm_id!=0 && $asm_id!=0 && $so_id==0)
 {
    $type="Area Sales Manager";
 }
 else
 {
    $type="Sales Officer";
 }
 $ctable_where = "";
 $ctable_where .="sales_id=".$sales_id." AND isDelete=0";
 $ctable_where .= " AND DATE(date_time) = '".date("Y-m-d",strtotime($_REQUEST['date']))."'";
 $ctable_r = $db->rp_getData($ctable,"*",$ctable_where,"date_time DESC",0);
 $ctable_r1 = $db->rp_getData($ctable,"*",$ctable_where,"date_time DESC",0);
 
 $ctable_where_visit = "";
 $ctable_where_visit .="user_id=".$sales_id." AND isDelete=0";
 $ctable_where_visit .= " AND DATE(created_date) = '".date("Y-m-d",strtotime($_REQUEST['date']))."'";
 $ctable_r2 = $db->rp_getData($ctable1,"*",$ctable_where_visit,"id DESC",0);

 $ctable_where_inquiry = "";
 $ctable_where_inquiry .="sales_executive_id=".$sales_id." ";
 $ctable_where_inquiry .= " AND DATE(datetime) = '".date("Y-m-d",strtotime($_REQUEST['date']))."'";
 $ctable_r3 = $db->rp_getData($ctable2,"*",$ctable_where_inquiry,"id DESC",0);

 $ctable_where_followup = "";
 $ctable_where_followup .="visitor_id=".$sales_id." AND isDelete=0";
 $ctable_where_followup .= " AND DATE(followup_date) = '".date("Y-m-d",strtotime($_REQUEST['date']))."'";
 $ctable_r4 = $db->rp_getData($ctable3,"*",$ctable_where_followup,"id DESC",0);


// visit
 $in_time = $db->rp_getData("attendance","date_time","sales_id='".$_REQUEST['id']."' AND DATE(date_time)='".date('Y-m-d',strtotime($_REQUEST['date']))."' AND inout_status='In'","",0);
  $in = array();
  while($in_time_d = mysqli_fetch_assoc($in_time))
  {
    $in[] = $in_time_d['date_time'];
  }
  // print_r($in); 

  $out_time = $db->rp_getData("attendance","date_time","sales_id='".$_REQUEST['id']."' AND DATE(date_time)='".date('Y-m-d',strtotime($_REQUEST['date']))."' AND inout_status='Out'","",0);
  $out = array();
  while($out_time_d = mysqli_fetch_assoc($out_time))
  {
    $out[] = $out_time_d['date_time'];
  }
  // print_r($out);
  $diffrence ="00:00:00";
  $first_in=$in[0];
  $first_out=$out[0];
  $total_in=sizeof($in);
  $total_out=sizeof($out);
  $last_out=$out[$total_out-1];
  $total_in_out=$total_in+$total_out;

  for($i=0;$i<sizeof($in);$i++)
  {
    for($j=0;$j<sizeof($out);$j++)
    {
      if($i==$j)
      { 
        $in_date = date("Y-m-d",strtotime($in[$i]));        
        $out_date = date("Y-m-d",strtotime($out[$j]));       
        if(strtotime($in_date)==strtotime($out_date))
        {

          $in_time=$in[$i];
          $out_time=$out[$j];                   
          // Formulate the Difference between two dates                           
          $diff = abs(strtotime($out_time) - strtotime($in_time));  
            
          // To get the year divide the resultant date into 
          // total seconds in a year (365*60*60*24) 
          $years = floor($diff / (365*60*60*24));  

          // To get the month, subtract it with years and 
          // divide the resultant date into 
          // total seconds in a month (30*60*60*24) 
          $months = floor(($diff - $years * 365*60*60*24)  / (30*60*60*24)); 
            
          // To get the day, subtract it with years and  
          // months and divide the resultant date into 
          // total seconds in a days (60*60*24) 
          $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24)); 
            
          // To get the hour, subtract it with years,  
          // months & seconds and divide the resultant 
          // date into total seconds in a hours (60*60) 
          $hours = floor(($diff-$years*365*60*60*24-$months*30*60*60*24-$days*60*60*24)/(60*60));  
            
          // To get the minutes, subtract it with years, 
          // months, seconds and hours and divide the  
          // resultant date into total seconds i.e. 60 
          $minutes = floor(($diff - $years * 365*60*60*24-$months*30*60*60*24-$days*60*60*24-$hours*60*60)/ 60);  
            
          // To get the minutes, subtract it with years, 
          // months, seconds, hours and minutes  
          $seconds = floor(($diff-$years*365*60*60*24-$months*30*60*60*24-$days*60*60*24-$hours*60*60-$minutes*60));  
          
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
          $months = floor(($diff - $years * 365*60*60*24)  / (30*60*60*24)); 
            
          // To get the day, subtract it with years and  
          // months and divide the resultant date into 
          // total seconds in a days (60*60*24) 
          $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24)); 
            
          // To get the hour, subtract it with years,  
          // months & seconds and divide the resultant 
          // date into total seconds in a hours (60*60) 
          $hours = floor(($diff-$years*365*60*60*24-$months*30*60*60*24-$days*60*60*24)/(60*60));  
            
          // To get the minutes, subtract it with years, 
          // months, seconds and hours and divide the  
          // resultant date into total seconds i.e. 60 
          $minutes = floor(($diff - $years * 365*60*60*24-$months*30*60*60*24-$days*60*60*24-$hours*60*60)/ 60);  
            
          // To get the minutes, subtract it with years, 
          // months, seconds, hours and minutes  
          $seconds = floor(($diff-$years*365*60*60*24-$months*30*60*60*24-$days*60*60*24-$hours*60*60-$minutes*60));  
      
          $diff=$hours.":".$minutes.":".$seconds;
          

          $secs = strtotime($diffrence)-strtotime("00:00:00");
          $diffrence = date("H:i:s",strtotime($diff)+$secs)." Running";
    }
  }

// visit
 ?>
<!-- zoom css -->
<!-- <link rel="stylesheet" type="text/css" href="zoom-image/css/style.css" />
<link rel="stylesheet" type="text/css" href="zoom-image/cloud-zoom/cloud-zoom.css" />
<link rel="stylesheet" type="text/css" href="zoom-image/fancybox/jquery.fancybox-1.3.4.css" />
<script src="zoom-image/js/cufon-yui.js" type="text/javascript"></script> --> 
<?php include("include_css.php"); ?>
<style type="text/css">
  /*.no-border td
  {
    border:none!important;   
    font-size: 20px;
    font-weight: 700;
  }
  .no-border td.value
  {
     font-size: 20px;
    font-weight: 700;
  }*/
  .pad-0
  {
    padding: 0px!important;
  }
</style>
<!-- <link rel="stylesheet" href="<?=ADMINSITEURL?>css/lightbox.css" /> -->
<!-- zoom css -->

  <div id="print_info">
  <div class="col-md-12 col-xs-12 col-sm-12 pad-0" style="margin: 0">
     <h3><b style="margin-left: 10px">Daily Report</b></h3>
  </div>
  <div class="col-md-12 col-xs-12 col-sm-12 pad-0">
    <div class="col-md-6 col-xs-6 col-sm-6 pad-0">
      <table class="table table-striped table-bordered mt-15 ">
        <tr class="no-border">
          <td>Name</td>
          <td>:-</td>
          <td class="value"><?= ucfirst($salesexename);?></td>
        </tr>
        <tr class="no-border">
            <td>Type</td>
            <td>:-</td>
            <td class="value"><?= $type;?></td>
          </tr>
        <tr class="no-border">
            <td>Mobile No.</td>
            <td>:-</td>
            <td class="value"><?= $salesexephone;?></td>
          </tr>
        <tr class="no-border">
            <td>Total Visit</td>
            <td>:-</td>
            <td class="value"><?= $db->rp_getTotalRecord($ctable1,$ctable_where_visit);?></td>
          </tr>
      </table>
    </div>
    <div class="col-md-6 col-xs-6 col-sm-6 pad-0">
       <table class="table table-striped table-bordered mt-15 ">
        <tr class="no-border">
          <td>First In</td>
          <td>:-</td>
          <td class="value"><?= (!empty($in))?date("d-m-Y h:i A",strtotime($first_in)):"-";?></td>
        </tr>
        <tr class="no-border">
            <td>Last Out</td>
            <td>:-</td>
            <td class="value"><?= (!empty($out))?date("d-m-Y h:i A",strtotime($last_out)):"-";?></td>
          </tr>
        <tr class="no-border">
            <td>Total In & Out</td>
            <td>:-</td>
            <td class="value"><?= $total_in_out;?></td>
          </tr>
        <tr class="no-border">
            <td>Total Working Time</td>
            <td>:-</td>
            <td class="value"><?= $diffrence;?></td>
          </tr>
      </table>
    </div>
  </div>
  

  <!-- attendance --> 
  <div class="col-md-12 col-xs-12 col-sm-12 pad-0" style="margin-bottom: 20px;">
     <span style="font-size: 20px;font-weight:600;margin-left: 10px">Attendance</span>
  </div>

  <table class="table table-striped table-bordered mt-15 ">
     <thead>
        <tr>
          <th>No.</th>
          <th>In/Out</th>
          <th>Date and Time</th>
          <th>Address</th>
          <th>Image</th>
        </tr>
     </thead>
     <tbody>
     <?php
      if(mysqli_num_rows($ctable_r)>0)
      {
        while($ctable_d = mysqli_fetch_array($ctable_r))
        {
        ?>
        <?php 
           $img = armor_attendance_image(isset($ctable_d['image_path']) ? $ctable_d['image_path'] : '');
           ?>
          <tr>
            <td><?php echo ++$count; ?></td>
            <td><?php echo $ctable_d['inout_status']; ?></td>
            <td><?php echo date("d-m-Y H:i:s a",strtotime($ctable_d['created_date'])); ?></td>
            <td><?php echo $ctable_d['app_address'];/*$db->getAddress($ctable_d['latitude'],$ctable_d['longitude']);*/ ?></td>
            <td>
              <img src="<?=$img?>" style="height: 120px;margin-bottom: 10px;border:1px solid #909090;float: left;margin-left: 10px;border-radius: 5px;">
            </td>
        </tr>
        <?php
        }
       }
       else
       {
       ?>
      <tr>
         <td align="center" colspan="5"><?php echo "No Attendance Found";?></td>
      </tr>
    <?php
     }
     ?>
     </tbody>
  </table>
  <!-- attendance -->

  <!-- total working time -->
  <!-- <hr/>
  <div class="col-md-12 col-xs-12 col-sm-12 pad-0" style="margin-bottom: 20px;">
     <span style="font-size: 20px;font-weight:600;">Total Working Time</span>
  </div>

  <table id="datatable_attandence" class="table table-striped table-bordered table-hover">
      <thead>
          <tr>
              <th>No</th>
              <th>Date</th>
              <th>Total Time</th>
          </tr>
      </thead>
      <tbody>
      <?php
      if(mysqli_num_rows($ctable_r1)>0)
      {
        $count = 0;
        $previous_date="";
        while($ctable_d = mysqli_fetch_array($ctable_r1))
        {
            $current_date=date("Y-m-d",strtotime($ctable_d['date_time']));
            if($previous_date==$current_date)
            {
              continue;
            }
            else
            {
              $previous_date=$current_date;
            }
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
                    // Formulate the Difference between two dates                           
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
                    $hours = floor(($diff - $years * 365*60*60*24  
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
            
          </tr>
          <?php 
        }
      }
      else
      {
      ?>
        <tr>
          <td align="center" colspan="3"><?php echo "No Attendance Found";?></td>
        </tr>
        <?php
      }

      ?>
      </tbody>
  </table> -->
  <!-- total working time -->

  <!-- total Visit -->
  <hr/>
  <div class="col-md-12 col-xs-12 col-sm-12 pad-0" style="margin-bottom: 20px;">
     <span style="font-size: 20px;font-weight:600;margin-left: 10px">Visit (<?= $db->rp_getTotalRecord($ctable1,$ctable_where_visit); ?>)</span>
  </div>
  <table id="datatable_1" class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th>No.</th>
            <th>Customer Name</th>
            <th>Date and Time</th>
            <!-- <th>Location Map</th>  -->
            <th>Visit Start <br/> Address</th>
            <th>Visit Start <br/> Image</th>
            <th>Visit Start <br/> Time</th>  
            <th>Visit Stop <br/> Address</th>
            <th>Visit Stop <br/> Image</th>
            <th>Visit Stop <br/> Time</th>
          </tr>
        </thead>
        <?php
        if(mysqli_num_rows($ctable_r2)>0)
        {
            $count = 0;            
            while($ctable_d = mysqli_fetch_array($ctable_r2))
            {
              ?>
              <tr>
                <td><?php echo ++$count; ?></td>                
                <td><span class="<?php echo ($ctable_d['isActive']==0)?"text-danger":"text-success"; ?>"><?php echo $db->rp_getValue("executive","cname","id='".$ctable_d['customer_id']."'") ?></span></td>
               <td><?php echo date("d-m-Y H:i:s",strtotime($ctable_d['created_date'])); ?></td>
               <td><?php echo $ctable_d['app_address'];/*$db->getAddress($ctable_d['latitude'],$ctable_d['longitude']);*/ ?></td>
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
                  <img src="<?=$imgpath[$i]?>" style="width: 80px;">
                  <?php }else{
                    ?>
                      <!-- <div class="hidden">
                        <a href="<?=$imgpath[$i]?>" data-lightbox="visit<?=$count?>" data-title="visit <?=$ctable_d['id']?>"><img src="<?=$imgpath[$i]?>" style="height: 80px;border-radius: 5px;border:1px solid #909090;"></a>
                      </div> -->
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
                    $imgpath[] = SITEURL."resource/image/".$db->rp_getValue("media","url","reference_id='".$ctable_d["id"]."' AND id='".$img[$i]."'",0);
                  }
                  for ($i=0; $i < sizeof($imgpath); $i++)
                  {
                    if($i==0)
                    {
                    ?>
                    <img src="<?=$imgpath[$i]?>" style="width: 80px;">
                    <?php 
                    }
                    else
                    {
                    ?>
                    <!-- <div class="hidden" hidden>
                      <a href="<?=$imgpath[$i]?>" data-lightbox="visit<?=$count?>" data-title="stop_visit <?=$ctable_d['id']?>"><img src="<?=$imgpath[$i]?>" style="height: 80px;"></a>
                    </div> -->
                    <?php
                    }
                  }
                  ?>
                </td>
                <td><?php if($ctable_d['stop_date_time']!="0000-00-00 00:00:00"){echo date('d-m-Y h:i A',strtotime($ctable_d['stop_date_time']));} else{echo "";}?></td>                       
              </tr>
              <?php
            }
        }           
        else
        {
          ?>
          <tr>
            <td colspan="5" class="text-center">No Visit Found</td>
          </tr>
          <?php
        }
        ?>
  </table>
  <!-- total Visit -->

    <!-- total Survey Customer -->
    <hr/>
  <div class="col-md-12 col-xs-12 col-sm-12 pad-0" style="margin-bottom: 20px;">
      <span style="font-size: 20px;font-weight:600;margin-left: 10px">Survey Customer (<?= $db->rp_getTotalRecord($ctable2,$ctable_where_inquiry); ?>)</span>
  </div>
    <table id="datatable_1" class="table table-striped table-bordered table-hover">
        <thead>
           <tr>
            <th>No.</th>
            <th>Survey No.</th>
            <th>Company Name</th>
            <th>Persoan Name</th>
            <th>Phone</th>
            <th>Country</th>
            <th>State</th>
            <th>City</th>
            <th>Survey Date</th>
          </tr>
        </thead>
        <?php
        if(mysqli_num_rows($ctable_r3)>0)
        {
            $count = 0;            
            while($ctable_d3 = mysqli_fetch_array($ctable_r3))
            {
              ?>
              <tr>
                <td><?php echo ++$count; ?></td>                
                <td>#INQ/<?php  echo $ctable_d3['id']; ?></td>
               <td><?php echo $ctable_d3['company_name'] ?></td>
               <td><?php echo $ctable_d3['person_name'] ?></td>              
               <td><?php echo $ctable_d3['mobile_number'] ?></td>              
               <td><?php echo $ctable_d3['country'] ?></td>              
               <td><?php echo $ctable_d3['state'] ?></td>              
               <td><?php echo $ctable_d3['city'] ?></td>              
               <td><?php if($ctable_d3['datetime']!="0000-00-00 00:00:00"){ echo date('d-m-Y',strtotime($ctable_d3['datetime'])); } else{ echo "";} ?></td>          
              </tr>
              <?php
            }
        }           
        else
        {
          ?>
          <tr>
            <td colspan="9" class="text-center">No Survey Customer Found</td>
          </tr>
          <?php
        }
        ?>
  </table>
    <!-- total Survey Customer -->

    <!-- total Followup -->
    <hr/>
  <div class="col-md-12 col-xs-12 col-sm-12 pad-0" style="margin-bottom: 20px;">
      <span style="font-size: 20px;font-weight:600;margin-left: 10px">Followup (<?= $db->rp_getTotalRecord($ctable3,$ctable_where_followup); ?>)</span>
  </div>
    <table id="datatable_1" class="table table-striped table-bordered table-hover">
        <thead>
           <tr>
            <th>No.</th>
            <th>Customer Name</th>
            <th>Mobile No </th>
            <th>Sales Person</th>
            <th>Description</th>
            <th>Through</th>
            <th>Date and Time</th>
          </tr>
        </thead>
        <?php
        if(mysqli_num_rows($ctable_r4)>0)
        {
            $count = 0;            
            while($ctable_d4 = mysqli_fetch_array($ctable_r4))
            {
              $msg = array("1"=>"Call","2"=>"Sms",3=>"Email");
              ?>
              <tr>
                <td><?php echo ++$count; ?></td>                
                <td><?php echo $db->rp_getValue("executive","cname","id='".$ctable_d4['visitor_id']."'");?></td>
                <td><?php echo $db->rp_getValue("executive","phone","id='".$ctable_d4['visitor_id']."'");?></td>
                <td><?php echo $db->rp_getValue("sales_executive","name","id='".$ctable_d4['user_id']."'");?></td>              
                <td><?php echo $ctable_d4['description'] ?></td>              
                <td><?php echo $msg[$ctable_d4['through']]?></td>              
                <?php if($ctable_d4['followup_date']=="0000-00-00 00:00:00"){?>
                    <td></td>
                <?php 
                } else{ ?>
                    <td><?php echo date('d-m-Y H:i:s a',strtotime($ctable_d4['followup_date'])); ?></td>
                <?php } ?>           
              </tr>
              <?php
            }
        }           
        else
        {
          ?>
          <tr>
            <td colspan="9" class="text-center">No Survey Customer Found</td>
          </tr>
          <?php
        }
        ?>
  </table>
    <!-- total Survey Followup -->

   <!-- Current Date Path -->
  <hr/>
  <div class="col-md-12 col-xs-12 col-sm-12 pad-0" style="margin-bottom: 20px;page-break-before: always!important;">
     <span style="font-size: 20px;font-weight:600;margin-left: 10px"><?= $_REQUEST['date']?> Google Map Route</span>
  </div>
  <div>    
    <div class="map_route" style="height: 600px;">
    </div>
  </div>
   <!-- Current Date Path -->
   
</div>  
<!-- Modal -->
<div id="OpenMap" class="modal fade" role="dialog">
  <div class="modal-dialog" style="width: 970px;">

    <!-- Modal content-->
    <div class="modal-content" >
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Attendance</h4>
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

 <!-- Modal -->
  <div id="OpenVisitMap" class="modal fade" role="dialog">
    <div class="modal-dialog" style="width: 970px;">

      <!-- Modal content-->
      <div class="modal-content" >
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Visit</h4>
        </div>
        <div class="modal-body">
          <div id="visit_map_canvas"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>

    </div>
  </div>
  <!-- Modal -->

<div id="myModal" class="modal">
  <span class="close1" onclick='$("#myModal").css("display","none");'>&times;</span>
  <img class="modal-content" style="height: 80%;width: auto;" id="img01">
</div>
<?php include("include_js.php"); ?>
<script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js">
      </script>
<script async defer
  src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDklPuT2SCmcmlflaoZ4B0WywYK_em79x4&callback=initMap">
  </script>
<script src="<?=ADMINSITEURL?>js/lightbox.js"></script>
</script>
<script type="text/javascript">
  $(document).ready(function()
  {
    var id="<?= $_REQUEST['id']; ?>";
    var date="<?= $_REQUEST['date']; ?>";
    $.ajax(
    {
      type: "POST",
      url: "all_pin_d.php",
      data: {
        id:id, 
        date:date, 
        reqheight:"900",
        flag:"report",
      },
      cache: false,
      beforeSend: function() {
        
      },
      success: function(result) {
        $(".map_route").html(result);
      }
    });
  });
</script>
<?php require_once 'disconnect.php';  ?>