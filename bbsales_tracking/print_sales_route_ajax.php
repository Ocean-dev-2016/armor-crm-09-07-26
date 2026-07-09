<?php
$page_id=631;$page_slug='no_order_inquiry_page';
include("connect.php");
$ctable 	= "my_route";
$Where = "";

if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!="")
{
     //echo $_REQUEST['searchName'];exit();
    $phone_id = array();
    $exe_ids_r = $db->rp_getData("executive","*","cname LIKE '%".trim($_REQUEST['searchName'])."%' OR phone LIKE '%".trim($_REQUEST['searchName'])."%' OR company_name LIKE '%".trim($_REQUEST['searchName'])."%' ","",0);

   

    // $customer_ids_r = $db->rp_getData("executive","*","phone LIKE '%".trim($_REQUEST['searchName'])."%' OR cname LIKE '%".trim($_REQUEST['searchName'])."%'  OR company_name LIKE '%".trim($_REQUEST['searchName'])."%'  ","",0);

    if ($exe_ids_r)
    {
        while($exe_id_d = mysqli_fetch_assoc($exe_ids_r))
        {
          $phone_id[] = $exe_id_d['id'];
        }

        $phone_no_id = implode(",", $phone_id);

        $ctable_where.="customer_id IN (".$phone_no_id.") AND "; 
    }
    // if($order_ids_r)
    // {
    //     while($order_id_d = mysqli_fetch_assoc($order_ids_r))
    //     {
    //       $phone_id[] = $order_id_d['id'];
    //     }

    //     $phone_no_id = implode(",", $phone_id);

    //     $ctable_where.="reference_id IN (".$phone_no_id.") OR ";
    // }
    // if($customer_ids_r)
    // {
    //     while($customer_id_d = mysqli_fetch_assoc($customer_ids_r))
    //     {
    //         $cname= $db->rp_getValue("complain","customer_id","id='".$customer_id_d['id']."'",0);

    //       $phone_id[] = $cname;
    //     }

    //     $phone_no_id = implode(",", $phone_id);
    //     $phone_no_id_f = rtrim($phone_no_id,(","));
    //     // print_r($phone_no_id_f);exit;

    //     $ctable_where.="reference_id IN (".$phone_no_id_f.") OR ";
    // }
    // if($customer_ids_r)
    // {
    //     while($customer_id_d = mysqli_fetch_assoc($customer_ids_r))
    //     {
    //         $cname= $db->rp_getValue("request","customer_id","id='".$customer_id_d['id']."'",0);

    //       $phone_id[] = $cname;
    //     }

    //     $phone_no_id = implode(",", $phone_id);
    //     $phone_no_id_f = rtrim($phone_no_id,(","));
    //     $ctable_where.="reference_id IN (".$phone_no_id_f.") OR ";
    // }
    //$ctable_where.=" description LIKE '%".trim($_REQUEST['searchName'])."%' OR ";
    // $ctable_where.=" 0=1 AND ";
    else 
    {
        $ctable_where.="phone IN ('') AND ";
    }
}



$ctable_where .= " isDelete=0";

// if(isset($_REQUEST['sales_id']) && $_REQUEST['sales_id']!="" && $_REQUEST['sales_id']!=NULL)
// {
//   $ctable_where .= " AND sales_id = '".$_REQUEST['sales_id']."' ";
//   $sales_id=$_REQUEST['sales_id'];
// }

if(isset($_REQUEST['df1']) && $_REQUEST['df1']!="")
{
      //echo $_REQUEST['df'];exit;
      $date_filter_query = urldecode( $_REQUEST['df1'] );

      $date_filter_query_ex=explode(" to ",$date_filter_query);

      $ctable_where .= " AND ( DATE(date)>='".date("Y-m-d",strtotime($date_filter_query_ex['0']))."' AND DATE(date)<='".date("Y-m-d",strtotime($date_filter_query_ex['1']))."'  ) ";
}


if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
{
  if($rights['personal_flag']==1)
  {
      $ctable_where .= " AND sales_id = '".$_SESSION[SITE_SESS.'REFERANCE_ID']."' ";

  }
  else
  {
    if($rights['all_data_flag']==1)
    {
      if(isset($_REQUEST['sales_id']) && $_REQUEST['sales_id']!="" && $_REQUEST['sales_id']!=NULL)
      {
        $ctable_where .= " AND sales_id = '".$_REQUEST['sales_id']."' ";
        $sales_id=$_REQUEST['sales_id'];
      }
    }
    else
    {
        $ctable_where .= " AND sales_id = '".$_SESSION[SITE_SESS.'REFERANCE_ID']."' ";

    } 
  }
}
else
{

  if(isset($_REQUEST['sales_id']) && $_REQUEST['sales_id']!="" && $_REQUEST['sales_id']!=NULL)
  {
    $ctable_where .= " AND sales_id = '".$_REQUEST['sales_id']."' ";
    $sales_id=$_REQUEST['sales_id'];
  }
}


	

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
      <th colspan="12" class="center">
        <h2>Route Report <?= date("d-m-Y h:i a");?> Printed By : <?=$_SESSION[SITE_SESS.'SESS_NAME'];?></h2>
      </th>
    </tr>
    <tr>
        <th>Sr No.</th>
       <th>Person Name</th>
        <th>Sales Person Name</th>
         <th>Company Name</th>
        <th>Mobile No</th>
        <th>Date</th>
        
        <th>State</th>  
        <th>City</th>
        <th>Remark</th>
    </tr>
  </thead>
  <tbody>
    <?php
  	if(mysqli_num_rows($ctable_r)>0)
  	{
      $count = 0;
      while($ctable_d = mysqli_fetch_array($ctable_r))
      {
        $sales_executive_type = "";
        if($ctable_d['type']=="sales_manager")
            {
        $sales_executive_type="Regional Sales Manager";
      }

      if($ctable_d['type']=="area_sales_manager")
      {
        $sales_executive_type="Business Development Manager";
      }

      if($ctable_d['type']=="dispatch_sales_manager")
      {
        $sales_executive_type="Dispatch Manager";
      }
      
      if($ctable_d['type']=="sales_officer")
      {
        $sales_executive_type="Area Sales Manager";
      }
      
      if($ctable_d['type']=="sales_executive")
      {
        $sales_executive_type="Sales Officer";
      }
      if($ctable_d['type']=="service_executive")
      {
        $sales_executive_type="Service Executive";
      }
  			?>
          <tr>
        		<td><?php echo ++$count; ?></td>
			     <td><?php echo $db->rp_getValue("executive","cname","isDelete=0 AND id='".$ctable_d['customer_id']."'") ?></td>
              <td><?php echo $db->rp_getValue("sales_executive","name","isDelete=0 AND id='".$ctable_d['sales_id']."'") ?></td>
              <td><?php echo $db->rp_getValue("executive","company_name","isDelete=0 AND id='".$ctable_d['customer_id']."'") ?></td>
              <td><?php echo $db->rp_getValue("executive","phone","isDelete=0 AND id='".$ctable_d['customer_id']."'") ?></td>
        <td><?php echo date('d-m-Y',strtotime($ctable_d['date'])); ?></td>

        <td><?php echo $db->rp_getValue("master_route","state","isDelete=0 AND id='".$ctable_d['route_id']."'") ?></td>
        <td><?php echo $db->rp_getValue("master_route","city","isDelete=0 AND id='".$ctable_d['route_id']."'") ?></td>
        <td><?= $ctable_d['remark'] ?></td>
    			</tr>
  		    <?php
      }
	  }
	  else
	  {
		 ?>
		  <tr>
			  <th colspan="8" style="text-align: center;">No Data Found</th>
		  </tr>
	    <?php
	  }
	  ?>
	</tbody>
</table>
<?php require_once("disconnect.php"); ?>