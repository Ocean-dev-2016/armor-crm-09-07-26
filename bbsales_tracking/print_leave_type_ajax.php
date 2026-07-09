<?php
$page_id=644;$page_slug='leave_type';
include("connect.php");
$ctable 	= "leave_type";
$Where = "";

if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
  $ctable_where .= " name like '%".$_REQUEST['searchName']."%' AND ";
}

$ctable_where .= " isDelete='0' AND id!='0'";
if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
{

   if($rights['personal_flag']==1)
   {

    $ctable_where .= " AND created_by='".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID']."'";

   }
   else
   {


    if($rights['chain_vise_flag'] == 1)
    {
        

        $check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];

          $get_sales_type=$db->rp_getValue("sales_executive","type","isDelete=0 AND id='". $check_id."'",0);
          if ($get_sales_type== "sales_manager") 
          {
              $sales_executive_type = "Regional Sales Manager";
              $key="sm_id";
              $WhereCondition.=' ' .$key.'='.$check_id;
          }

          else if ($get_sales_type == "area_sales_manager") 
          {
              $sales_executive_type = "National Sales Manager";//Business Development Manager
              $key="asm_id";
              $WhereCondition.=' ' .$key.'='.$check_id;
          }

          else if ($get_sales_type == "sales_officer") 
          {
              $sales_executive_type = "Area Sales Manager";//Area Sales Manager
              $key="so_id";
              $WhereCondition.=' ' .$key.'='.$check_id;
          }
          else if ($get_sales_type == "sales_executive") 
          {
              $sales_executive_type = "Sales Officer";
              $key="se_id";
              $WhereCondition.=' ' .$key.'='.$check_id;
          }
          else
          {
            $WhereCondition.=' type = "service_engineer"';
          }

          $data = $db->rp_getData("sales_executive","id",$WhereCondition,"",0);

          $SALEID1=array();
        if($data)
        {
          while($data_d=mysqli_fetch_assoc($data))
          {
            $SALEID1[]=$data_d['id'];
          }
        }
        if(!empty($SALEID1))
        {
          $SALEID1=implode(",", $SALEID1);
          
            $ctable_where .= " AND created_by IN (".$SALEID1.','.$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'].")"; 
          
          
        }
        else
        {
            $ctable_where .= " AND  created_by IN (".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'].")";   
        }
    }
    else
    {
      $ctable_where .= " ";
    }
  }
  
}
else
{

  $ctable_where .= " ";

}
	

$ctable_r = $db->rp_getData($ctable,"*",$ctable_where,"id DESC",0);
/*for log*/
$flag = "Web";
$module_name = "Leave Type";
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
        <h2>Leave Type Master <?= date("d-m-Y h:i a");?> Printed By : <?=$_SESSION[SITE_SESS.'SESS_NAME'];?></h2>
      </th>
    </tr>
    <tr>
      <th>Sr No.</th>
    <th> Leave Type</th>
    <!-- <th> Code</th> -->
  <!--   <th>Tax Category</th> -->
      <!-- <th>Image</th> -->
    </tr>
  </thead>
 <tbody>
  <?php
  if(mysqli_num_rows($ctable_r)>0){
    $count = 0;
    
    while($ctable_d = mysqli_fetch_array($ctable_r)){
      $count++;
  ?>
    <tr>
    
      <td><?php echo $count; ?></td>
      <td>
      <?php 
      echo stripslashes($ctable_d['name']); 
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