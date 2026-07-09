<?php
$page_id=606;$page_slug='page_area';
include("connect.php");
$ctable 	= "area";
$ctable_where = "";

if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
    //$ctable_where .= " (name like '%".$_REQUEST['searchName']."%') AND ";
    
    $data_search = $db->rp_getData("area","class_id","name = '".$_REQUEST['searchName']."' AND isDelete=0","",0);
    if($data_search)
    {
        while($data_search_d=mysqli_fetch_assoc($data_search))
        {
            $order_ids[]=$data_search_d['class_id'];
        }
        $order_ids=implode(",",$order_ids);
        $ctable_where .= "  Id IN (".$order_ids.") AND ";
    }
    
}
$ctable_where .= " isDelete=0";

if(isset($_REQUEST['class_id']) && $_REQUEST['class_id']!="" && $_REQUEST['class_id']!=NULL)
{
 $ctable_where .= " AND id = '".$_REQUEST['class_id']."' ";
}
if(isset($_REQUEST['country_id']) && $_REQUEST['country_id']!="" && $_REQUEST['country_id']!=NULL)
{
 $ctable_where .= " AND country_id = '".$_REQUEST['country_id']."' ";
}
	

$ctable_r = $db->rp_getData("class","*",$ctable_where,"id DESC",0);
/*for log*/
$flag = "Web";
$module_name = "city";
$log_description = $module_name." Printed By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");
$last_id = "";
$db->insertLog($ctable,$last_id,"print","",$insert,0,$log_description,$flag,$module_name,$user_id,"");
/*for log*/
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
h2
{
  text-transform: uppercase;
  margin-bottom: 0px;
}
</style>
<table id="datatable_1" class="table table-striped table-bordered table-hover">
  <thead>
    <tr>
      <th colspan="12" class="center">
        <h2>Area Master <?= date("d-m-Y h:i a");?> Printed By : <?=$_SESSION[SITE_SESS.'SESS_NAME'];?></h2>
      </th>
    </tr>
    <tr>
      <th>Sr No.</th>
      <th>Country Name</th>
      <th>State Name</th>
      <th>City Name</th>
   
    </tr>
  </thead>
 <tbody>
  <?php
  if(mysqli_num_rows($ctable_r)>0){
    $count = 0;
    
    while($ctable_d = mysqli_fetch_array($ctable_r)){
      $count++;

      $country_name=$db->rp_getValue("country","name","id='".$ctable_d['country_id']."' AND isDelete=0",0);
  ?>
    <tr>
    
        <td><?php echo $count; ?></td>



        <td><span class="<?php echo ($country_isActive==0)?"text-danger":"text-success"; ?>"><?php echo stripslashes($country_name); ?></span></td>

        <td><span class="<?php echo ($ctable_d['isActive']==0)?"text-danger":"text-success"; ?>"><?php echo stripslashes($ctable_d['name']); ?></span></td>
          
        <td>
        <?php
        $area=$db->rp_getData("area","name","class_id='".$ctable_d['id']."' AND isDelete=0");
        
        while($area_d = mysqli_fetch_array($area)){
        ?>
                <span class="<?php echo ($ctable_d['isActive']==0)?"text-danger":"text-success"; ?>"><?php echo $area_d['name']; ?></span>
         <br/>
        <?php
        }
        ?>
        </td>
     
      
      <!-- <td>
      <a class="btn btn-info btn-sm" onClick="window.location.href='top_category_master_crud.php?mode=edit&id=<?php echo $ctable_d['id']; ?>'" title="Edit"><i class="fa fa-pencil"></i></a>
      <a class="btn btn-danger btn-sm" onClick="del_conf('<?php echo $ctable_d['id']; ?>');" title="Delete"><i class="fa fa-times"></i></a>
      </td> -->
    </tr>
  <?php
    }
  }
  ?>
  </tbody>
</table>
<?php require_once "disconnect.php"; ?>