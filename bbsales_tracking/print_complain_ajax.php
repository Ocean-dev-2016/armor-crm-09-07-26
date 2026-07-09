<?php 
$page_id=581;$page_slug='manage_complain';
/*
 * @author Ravi Patel
 */
include("connect.php");
$ctable 	= "complain";

$ctable_where = "";
// Get the total number of rows in the table

if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){

    $ctable_where .= " (complain_no like '%".$db->clean($_REQUEST['searchName'])."%') AND ";

    $isFillter=true;
}

$ctable_where .= " isDelete=0";

$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"]) ) ? intval($_REQUEST["show"]) : 100;

if(isset($_REQUEST["page"])){
    $page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
    if(!is_numeric($page_number)){die('Invalid page number!');} //incase of invalid page number
}else{
    $page_number = 1; //if there's no page number, set it to 1
}
if(isset($_REQUEST["customer_id"]) && $_REQUEST["customer_id"]!="" && $_REQUEST["customer_id"]!=undefined){
    $ctable_where .= " AND customer_id='".$_REQUEST["customer_id"]."'";
    $cid = $_REQUEST["customer_id"];
}
if(isset($_REQUEST["company_type"]) && $_REQUEST["company_type"]!="" && $_REQUEST["company_type"]!=undefined){
    $ctable_where .= " AND type_of_company='".$_REQUEST["company_type"]."'";
}
// print_r($_REQUEST["sales_executive"]);exit;
if(isset($_REQUEST["sales_executive"]) && $_REQUEST["sales_executive"]!="" && $_REQUEST["sales_executive"]!=undefined && $_REQUEST["sales_executive"]!="null"){
    
    //$ctable_where .= " AND user_id='".$_REQUEST["sales_executive"]."'";
    $ctable_where .= " AND user_id IN (".$_REQUEST['sales_executive'].")";
    $isFillter=true;
    
}


if(isset($_REQUEST['status_id']) && $_REQUEST['status_id']!="" && $_REQUEST['status_id']!="null")
{
    $Where .= " status='".implode(" , ", $_REQUEST['status_id'])."' AND ";
    $isFillter=true;
    
}

if(isset($_REQUEST['df']) && $_REQUEST['df']!=""){
    //echo $_REQUEST['df'];exit;
    $date_filter_query = urldecode( $_REQUEST['df'] );

    $date_filter_query_ex=explode(" to ",$date_filter_query);

    $ctable_where .= " AND ( DATE(created_date)>='".date("Y-m-d",strtotime($date_filter_query_ex['0']))."' AND DATE(created_date)<='".date("Y-m-d",strtotime($date_filter_query_ex['1']))."'  ) ";
    $isFillter=true;
}

if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
{
    if($rights['personal_flag']==1)
    {
         $check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];
        $ctable_where .= " AND (complain_assign_to = '".$check_id."' OR complain_created_by = '".$check_id."') ";
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
                    
                        $ctable_where .= "  AND (complain_assign_to IN (".$SALEID1.','.$_SESSION[SITE_SESS.'REFERANCE_ID'].") OR  complain_created_by IN (".$SALEID1.','.$_SESSION[SITE_SESS.'REFERANCE_ID'].") ) ";    
                    
                    
                }
                else
                {
                        $ctable_where .= "  AND (complain_assign_to IN (".$_SESSION[SITE_SESS.'REFERANCE_ID'].") OR complain_created_by(".$_SESSION[SITE_SESS.'REFERANCE_ID']."))";     
                }

        }
        else
        {
            
        }
    }
}

if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
{
    $check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];
    $ctable_where .= " AND (complain_assign_to = '".$check_id."' OR complain_created_by = '".$check_id."') ";
}
// echo "<pre>";
// print_r($_REQUEST);die;
$ctable_r = $db->rp_getData($ctable,"*",$ctable_where,"id DESC",0);
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
        		<th colspan="18" class="center"><h2>Complain Report <?= date("d-m-Y h:i a");?> Printed By : <?=$_SESSION[SITE_SESS.'SESS_NAME'];?></h2></th>
        	</tr>
        	<tr>
            <th>No.</th>
            <th>Complain No.</th>
            <th>Complain Date</th>
            <th>Sales Person Name</th>
            <th>Company Type</th>
            <th>Customer Name</th>
           <!--  <th>Title </th> -->
            <!-- <th>Source of complain</th> -->
            <th>Complain Category</th>
            <th>Complain Sub Category</th>
            <th>Description</th>
           <!--  <th>Latitude</th>
            <th>Longitude</th> -->
            <th>Address</th>
            <!-- <th>State</th>
            <th>City</th>
            <th>Image</th> -->
            <th>Status</th>
            <th>Compalain Assign To</th>
            <!-- <th>Entry Type</th>  -->
            <!-- <th>Update Entry Type</th>   -->
			</tr>
        </thead>
        <tbody>
        <?php
        if(mysqli_num_rows($ctable_r)>0){
            $count = 0;
            
            while($ctable_d = mysqli_fetch_array($ctable_r)){
            //print_r($ctable_d);
            $ENTRY_FLAG = array("1"=>"Admin Panel","2"=>"customer","3"=>"Web Sales",4=>"Web Customer",5=>"Sales App",6=> "Customer App");
            $status_array = array("0"=>"Generate","1"=>"In Progress","2"=>"Complete","-1"=>"Reject","-2"=>"Not Done","-3"=>"Cancel");
            $complain_type_array = array("1"=>"Email","2"=>"Call","3"=>"Whatsapp");
        ?>
            <tr>
                <td><?php echo ++$count; ?></td>
                <td><?php echo "#".stripslashes($ctable_d['complain_no']); ?></td>
                <td><?php if($ctable_d['created_date']=="1970-01-01" || $ctable_d['created_date']=="0000-00-00"){ echo "";}else{
                    echo date("d-m-Y",strtotime($ctable_d['created_date']));}?>
                  </td>
                <td><span class="<?php echo ($ctable_d['isActive']==0)?"text-danger":"text-success"; ?>"><?php echo $db->rp_getValue("sales_executive","name","id='".$ctable_d['user_id']."'") ?></span></td>
                <td><?= $db->rp_getValue("company_master","name","id = '".$ctable_d['type_of_company']."'") ?></td>
                <td><span class="<?php echo ($ctable_d['isActive']==0)?"text-danger":"text-success"; ?>"><?php echo $db->rp_getValue("executive","cname","id='".$ctable_d['customer_id']."'")."</span><span><br/>".$db->rp_getValue("executive","phone","id='".$ctable_d['customer_id']."'") ?></span></td>
			<!-- 	<td><?php echo stripslashes($ctable_d['title']); ?></td> -->
                <!-- <td><?php echo stripslashes($complain_type_array[$ctable_d['complain_type']]); ?></td> -->
                <td><?php echo  $db->rp_getValue("complain_category","name","id='".$ctable_d['complain_cat_id']."'"); ?></td>
                <td><?php echo  $db->rp_getValue("complain_sub_category","name","id='".$ctable_d['complain_subcat_id']."'");?></td>
				<td><?php echo stripslashes($ctable_d['remark']); ?></td>
       <!--  <td><?php echo stripslashes($ctable_d['latitude']); ?></td>
				<td><?php echo stripslashes($ctable_d['longitude']); ?></td> -->
				<td><?php echo $ctable_d['app_address']; ?></td>

                <!-- <td><?php echo $ctable_d['state']; ?></td>
                <td><?php echo $ctable_d['city']; ?></td>
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
                    <a href="<?=$imgpath[$i]?>" data-lightbox="complain<?=$count?>" data-title="complain <?=$ctable_d['id']?>"><img src="<?=$imgpath[$i]?>" style="height: 80px; width: 120px;"></a>
                    <?php }else{
                        ?>
                            <div class="hidden">
                                <a href="<?=$imgpath[$i]?>" data-lightbox="complain<?=$count?>" data-title="complain <?=$ctable_d['id']?>"><img src="<?=$imgpath[$i]?>" style="height: 80px;"></a>
                            </div>
                        <?php
                            }
                        }
                    ?>
                </td> -->
                <td><?php echo $status_array[$ctable_d['status']]; ?></td> 
                <td><span class="<?php echo ($ctable_d['isActive']==0)?"text-danger":"text-success"; ?>"><?php echo $db->rp_getValue("sales_executive","name","id='".$ctable_d['complain_assign_to']."'") ?></span></td>
                <!-- <td><?php echo $ENTRY_FLAG[$ctable_d['entry_flag']]; ?></td>  -->
              <!-- <td><?php echo $db->rp_getValue("class","name","id='".$ctable_d['class_id']."'") ?></td>
               
              <td><?php echo $db->rp_getValue("area","name","id='".$ctable_d['area_id']."'") ?>
                    
                </td> -->       
               
              <!-- <td><?php echo $ctable_d['main_city']; ?></td> -->        
                
                <!-- <td><?php echo $ENTRY_FLAG[$ctable_d['update_entry_flag']]; ?></td> -->
        			</tr>
                <?php
                    }
                }
                ?>
        </tbody>
    </table>
        <?php require_once 'disconnect.php';  ?>