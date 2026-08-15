<?php 
   $page_id=569;$page_slug='dispatch_pages';
   /*
    * @author Ravi Patel
    */
   include("connect.php");
   $ctable   = "attendance";
   $ctable1  = "Attendance";
   $sales_id=$_REQUEST['id'];
   $ctable_where = "";
   $ctable_where .="sales_id=".$sales_id." AND isDelete=0";
   $ctable_where .= " AND DATE(date_time) = '".date("Y-m-d",strtotime($_REQUEST['date']))."'";
   
   $ctable_r = $db->rp_getData($ctable,"*",$ctable_where,"date_time DESC",0);
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
   <span style="font-size: 20px"><?php echo strtoupper($db->rp_getValue("sales_executive","name","id='".$sales_id."'")."'s"); ?> ATTENDANCE ON DATE <?= $_REQUEST['date']; ?></span>  
</div>
<div id="print_info">
  <table class="table table-striped table-bordered mt-15 ">
     <thead>
          
        <tr>
           <th>Time</th>
           <th>In / Out </th>
           <!-- <th>IMEI </th> -->
           <th>Address </th>
           <th>Image</th>
        </tr>
     </thead>
     <tbody>
     <?php
        while($ctable_d = mysqli_fetch_array($ctable_r)){
        ?>
        <?php 
           $img = armor_attendance_image(isset($ctable_d['image_path']) ? $ctable_d['image_path'] : '');
           ?>
        <tr>
           <td><?php echo date('h:i A',strtotime($ctable_d['date_time']));?></td>
           <td><?php echo $ctable_d['inout_status'];?></td>
           <!-- <td><?php echo $ctable_d['imei'];?></td>            -->
           <td><?php echo $ctable_d['app_address']; ?></td>
           <td>
             <img  id="myImg" class="myImg" src="<?php echo $img ?>" height="120px" width="120px" style="border-radius: 5px;border:1px solid #909090;" >
           </td>
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
   <td align="center" colspan="3"><?php echo "No Attendance Found";?></td>
</tr>
<?php
   }
   ?>
