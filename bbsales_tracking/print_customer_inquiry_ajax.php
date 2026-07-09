<?php
$page_id=572;$page_slug='customer_inquiry_page';

include("connect.php");
$ctable     = "customer_inquiry";
$ctable1    = "Inquiry";
$Where = "";

if( isset($_REQUEST['searchName']) && !empty($_REQUEST['searchName']) ) {
  $Query=$_REQUEST['searchName'];
  $Where.=" (company_name like '%".$Query."%' OR mobile_number like '%".$Query."%' OR id like '%".$Query."%' OR person_name like '%".$Query."%' OR country like '%".$Query."%' OR state like '%".$Query."%' OR city like '%".$Query."%') AND";
}

if(isset($_REQUEST['c_type']) && $_REQUEST['c_type']!="" && $_REQUEST['c_type']!=NULL)
{
  $Where .= "executive_type = '".$_REQUEST['c_type']."' AND ";
}

if(isset($_REQUEST['status_id']) && $_REQUEST['status_id']!="")
{
  $Where .= "status='".$_REQUEST['status_id']."' AND ";
}

if(isset($_REQUEST['type']) && $_REQUEST['type']!="" && $_REQUEST['type']!=NULL)
{
 $Where .= "sales_executive_id = '".$_REQUEST['type']."' AND ";
}

if(isset($_REQUEST['country']) && $_REQUEST['country']!="" && $_REQUEST['country']!=NULL)
{
  $Where .= "country = '".$_REQUEST['country']."' AND ";
}

if(isset($_REQUEST['state']) && $_REQUEST['state']!="" && $_REQUEST['state']!=NULL)
{
  $Where .= "state = '".$_REQUEST['state']."' AND ";
}

if(isset($_REQUEST['city']) && $_REQUEST['city']!="" && $_REQUEST['city']!=NULL)
{
  $Where .= "city = '".$_REQUEST['city']."' AND ";
}
if(isset($_REQUEST['assigned_to']) && $_REQUEST['assigned_to']!="" && $_REQUEST['assigned_to']!=NULL)
{
    $ctable_where .= " AND inquiry_assign_to = '".$_REQUEST['assigned_to']."' ";
    $assigned_to=$_REQUEST['assigned_to'];
}

$Where .= " isDelete=0";
    

$ctable_r = $db->rp_getData($ctable,"*",$Where,"id DESC",0);
exit;
?>
<!-- <style type="text/css">
.table-scrollable 
{
    width: auto;
    height: 810px;
    overflow-x: scroll;
    overflow-y: scroll;
}
</style> -->
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
h2
{
    text-transform: uppercase;
    margin-bottom: 0px;
}
</style>           
<table id="datatable_1" class="table table-striped table-bordered table-hover">
    <thead>
        <tr>
            <th colspan="22" class="center">
                <h2>Customer Inquiry Report <?= date("d-m-Y h:i a");?> Printed By : <?=$_SESSION[SITE_SESS.'SESS_NAME'];?></h2>
            </th>
        </tr>
        <tr>
            <th>Sr No.</th>      
            <th>Source Medium</th>
            <th>Customer Type</th>
            <th>Company Name</th>
            <th>Person Name</th>
            <th>Mobile Number</th>
            <th>Country</th>
            <th>State</th>
            <th>City</th>               
            <th>Date Of Call</th>
            <th>Inquiry Taken By</th>
            <th>Inquiry Assigned to</th>
            <th>Image Path</th>                         
        </tr>
    </thead>
    <tbody>
        <?php
        if(mysqli_num_rows($ctable_r)>0)
        {
            $u_w_flag_arr = array('1' =>"YES",'0' =>"NO");
            $quotation_flag_arr = array('1' =>"YES",'2' =>"NO");
            $count = 0;
            while($ctable_d = mysqli_fetch_array($ctable_r))
            { 
                $inquiry_type_array = array("1"=>"Email","2"=>"Call","3"=>"Whatsapp");
                ?>
                <tr>
                   <td><?php echo ++$count; ?></td>
                
                <td><?php  echo $inquiry_type_array[$ctable_d['source_of_inquiry']]; ?></td>
                <td><?php echo $db->rp_getValue("customer_type","name","id='".$ctable_d['executive_type']."'"); ?></td>
                 <td><span class="<?php echo ($ctable_d['isActive']==0)?"text-danger":"text-success"; ?>"><?php echo $ctable_d['company_name']; ?></span></td>
                <td><span class="<?php echo ($ctable_d['isActive']==0)?"text-danger":"text-success"; ?>"><?php echo $ctable_d['person_name']; ?></span></td>
                <td><i class="fa fa-phone"></i>&nbsp;<a target="_blank" href="https://api.whatsapp.com/send?phone=91<?php echo stripslashes($ctable_d['mobile_number']); ?>&text=<?= $sms; ?>"><?php echo $ctable_d['mobile_number']; ?></a></td>
                <td><?php echo $ctable_d['country']; ?></td>
                <td><?php echo $ctable_d['state']; ?></td>
                <td><?php echo $ctable_d['city']; ?></td>
                <td><?php if($ctable_d['date_of_call']!="0000-00-00 00:00:00"){ echo date('d-m-Y',strtotime($ctable_d['date_of_call'])); } else{ echo "";} ?></td>
                <?php $action=$db->rp_getValue("no_order_inquiry_action","name","id='".$ctable_d['action']."'");?>
                
                
                <td>
                <?php               
                $sales_executive=$db->rp_getValue("sales_executive","name","id='".$ctable_d['sales_executive_id']."'");?>
                <?php echo stripslashes($sales_executive); ?></td>
                <td>
                <?php               
                $inquiry_assign_to=$db->rp_getValue("sales_executive","name","id='".$ctable_d['inquiry_assign_to']."'");?>
                <?php echo stripslashes($inquiry_assign_to); ?></td>
                <td>
                <?php 
                    if($ctable_d['image_path']!="")
                    {
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
                                <a href="<?=$imgpath[$i]?>" data-lightbox="complain<?=$count?>" data-title="complain <?=$ctable_d['id']?>"><img src="<?=$imgpath[$i]?>" style="height: 80px;"></a>
                            <?php 
                            }else{
                            ?>
                                <div class="hidden">
                                    <a href="<?=$imgpath[$i]?>" data-lightbox="complain<?=$count?>" data-title="complain <?=$ctable_d['id']?>"><img src="<?=$imgpath[$i]?>" style="height: 80px;"></a>
                                </div>
                            <?php
                            }
                        }
                    }
                    else
                    {
                        $img = $ctable_d['image_path'] = DEFAULTIMG;
                    ?>
                        <a href="<?=$img?>" data-lightbox="attendance<?=$count?>" data-title="attendance <?=$ctable_d['id']?>"><img src="<?=$img?>" style="height: 80px;"></a>
                    <?php
                    }
                    ?>
                </td>

            </tr>
        <?php
            }
        }
        else
        {
            ?>
            <tr>
                <th colspan="13" style="text-align: center;">No Data Found</th>
            </tr>
            <?php
        }
        
        ?>
    </tbody>
</table>
<?php require_once "disconnect.php"; ?>    
