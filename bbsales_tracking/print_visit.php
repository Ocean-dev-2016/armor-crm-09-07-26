<?php 
   $page_id=569;$page_slug='dispatch_pages';
   /*
    * @author Ravi Patel
    */
   include("connect.php");
   $ctable   = "visit";
   $ctable1  = "Attendance";
   $sales_id=$_REQUEST['id'];
   $ctable_where = "";
   $ctable_where .="user_id=".$sales_id." AND isDelete=0";
   $ctable_where .= " AND DATE(created_date)='".date("Y-m-d",strtotime($_REQUEST['date']))."'";
   
   $ctable_r = $db->rp_getData($ctable,"*",$ctable_where,"created_date DESC",0);
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
</style>   
<?php 
   if(mysqli_num_rows($ctable_r)>0){
     ?>

<div class="col-md-12 col-xs-12 col-sm-12" style="margin-bottom:20px;text-align: center; ">
   <span style="font-size: 20px"><?php echo strtoupper($db->rp_getValue("sales_executive","name","id='".$sales_id."'")."'s"); ?> VISIT ON DATE <?= $_REQUEST['date']; ?></span>  
</div>
<div id="print_info">
  <table class="table table-striped table-bordered mt-15 ">
     <thead>
     
        <tr>
           <th>No.</th>
            <th>Customer Name</th>
            <th>Mobile No.</th>
            <th>Date and Time</th>
            <th>Visit Start <br/> Address</th>
            <th>Visit Start <br/> Image</th>
            <th>Visit Start <br/> Time</th>  
            <th>Visit Stop <br/> Address</th>
            <th>Visit Stop <br/> Image</th>
            <th>Visit Stop <br/> Time</th>
        </tr>
     </thead>
     <tbody>
     <?php
        while($ctable_d = mysqli_fetch_array($ctable_r)){
        ?>
        <tr>
            <td><?php echo $ctable_d['id']; ?></td>
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
                      $imgpath[] = SITEURL."resource/image/".$db->rp_getValue("media","url","reference_id='".$ctable_d["id"]."' AND id='".$img[$i]."'",0);
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
                    $imgpath[] = SITEURL."resource/image/".$db->rp_getValue("media","url","reference_id='".$ctable_d["id"]."' AND id='".$img[$i]."'",0);
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
     <?php
        }
        ?>
     </tbody>
  </table>
</div>

<?php
   }
   else{
   ?>
<tr>
   <td align="center" colspan="3"><?php echo "No Visit Found";?></td>
</tr>
<?php
   }
   ?>
