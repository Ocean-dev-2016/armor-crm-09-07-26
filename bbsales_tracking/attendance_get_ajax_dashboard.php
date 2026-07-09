<?php
   $page_id=400;$page_slug='dashboard';
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
<style type="text/css">
.thumb img {
    border: 1px solid #000;
    margin: 3px;
    float: left;
}

.thumb span {
    position: absolute;
    visibility: hidden;
    /*visibility:visible;*/
    right: 30%;
    margin-top: -60px;
    background: #fff;
}

.thumb:hover,
.thumb:hover span {
    visibility: visible;
    z-index: 9999;
}

#myImg {
    border-radius: 5px;
    cursor: pointer;
    transition: 0.3s;
}

#myImg:hover {
    opacity: 0.7;
}

table
{
  /*height: <?=$_REQUEST['reqheight']?>px!important;*/
}

/* The Modal (background) */

.modal {
    display: none;
    /* Hidden by default */
    position: fixed;
    /* Stay in place */
    z-index: 1;
    /* Sit on top */
    padding-top: 100px;
    /* Location of the box */
    left: 0;
    top: 0;
    width: 100%;
    /* Full width */
    height: 100%;
    /* Full height */
    overflow: auto;
    /* Enable scroll if needed */
    background-color: rgb(0, 0, 0);
    /* Fallback color */
    background-color: rgba(0, 0, 0, 0.9);
    /* Black w/ opacity */
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

.modal-content,
#caption {
    -webkit-animation-name: zoom;
    -webkit-animation-duration: 0.6s;
    animation-name: zoom;
    animation-duration: 0.6s;
}

@-webkit-keyframes zoom {
    from {
        -webkit-transform: scale(0)
    }
    to {
        -webkit-transform: scale(1)
    }
}

@keyframes zoom {
    from {
        transform: scale(0)
    }
    to {
        transform: scale(1)
    }
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

@media only screen and (max-width: 700px) {
    .modal-content {
        width: 100%;
    }
}
</style>
<?php 
   if(mysqli_num_rows($ctable_r)>0){
     ?>

<div class="col-md-6 col-xs-12 col-sm-3" style="margin-bottom: 10px; ">
   <span style="font-size: 20px">Attendance</span>
   <button type="button" class="btn btn-sm green-haze print" style="margin-left: 20px;background-color: #f0ad4e;color: #fff;" name="print" onClick="genPrint()" id="print" href="" title="Download XL Report"><i class="fa fa-print"></i>Print</button>
</div>
<div id="print_info">
  <table class="table table-striped table-bordered mt-15 ">
     <thead>
        <tr>
           <th>Time</th>
           <th>In / Out </th>
           <!-- <th>IMEI </th> -->
           <th>Address </th>
           <!-- <th>Location Map</th> -->
           <th>Image</th>
        </tr>
     </thead>
     <tbody>
     <?php
        $count = 0;
        while($ctable_d = mysqli_fetch_assoc($ctable_r)){
          $count++;
        ?>
        <?php 
           // if ($ctable_d['image_path']!="" && file_exists(ATTENDANCE.$ctable_d['image_path'])) {
           //   $img = ATTENDANCE.$ctable_d['image_path'];
           // }
           // else
           // {
           //   $img = $ctable_d['image_path'] = DEFAULTIMG;
           // }
            $img = ATTENDANCE.$ctable_d['image_path'];
           ?>
        <tr>
           <td><?php echo date('h:i A',strtotime($ctable_d['date_time']));?></td>
           <td><?php echo $ctable_d['inout_status'];?></td>
        
           <td><?php echo $ctable_d['app_address']; ?></td>
           
           <td>
              <div id="thumbwrap">
                 <a class="thumb" href="<?=$img?>" data-lightbox="Attenndance<?=$count?>" data-title="Attenndance <?=$ctable_d['id']?>">
                 <!-- <span>
                 <img src="<?php echo $img ?>" height="200px" width="auto">
                 </span> -->
                 
                  <img src="<?=$img?>" style="height: 80px;border-radius: 5px; border:1px solid #909090;">


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
<div id="myModal" class="modal" style="z-index: 99999;">
   <span class="close1" onclick='$("#myModal").css("display","none");'>&times;</span>
   <img class="modal-content" style="height: auto;width: auto;" id="img01">
</div>
<script type="text/javascript">
   function PopUp(src){
     $("#myModal").css("display","block");
     $("#img01").attr("src",src);
   };
</script>
<script type="text/javascript">
  function genPrint()
  {
    var date="<?= $_REQUEST['date'];?>";
    var id="<?= $_REQUEST['id'];?>";
     var myWindow = window.open('print_attendance.php?id='+id+'&date='+date,'','width=700,height=800');
    
    myWindow.print();
  }
</script>
<?php
$db->disconnect(); 
?>