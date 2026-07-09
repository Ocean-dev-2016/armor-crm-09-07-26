<?php
/*
 * @author Ravi Patel
 */
 $page_id=559;$page_slug='page_product';
include("connect.php");
$ctable = "product";
$ctable1 = "Product";

$ctable_where = "";
// Get the total number of rows in the table

if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
	$where11="";
	$pro_r1=$db->rp_getData("product_weight_price","product_id","catno LIKE '%".$_REQUEST['searchName']."%' AND isDelete=0","",0);
	$PROIDS1=array();
	if($pro_r1)
	{
		while($pro_d1=mysqli_fetch_assoc($pro_r1))
		{
			$PROIDS1[]=$pro_d1['product_id'];
		}
	}
	if(!empty($PROIDS1))
	{
		$PROIDS1=implode(",", $PROIDS1);
		$where11=" OR id IN (".$PROIDS1.")";
	}
	$ctable_where .= " (name like '%".$_REQUEST['searchName']."%' ".$where11.") AND ";
}

$ctable_where .= " 1=1 AND isDelete='0' AND id!='0'";

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

if(isset($_REQUEST['top_category_id']) && $_REQUEST['top_category_id']!="" && $_REQUEST['top_category_id']!=NULL)
{
 $ctable_where .= " AND tcid = '".$_REQUEST['top_category_id']."' ";
}
if(isset($_REQUEST['category_id']) && $_REQUEST['category_id']!="" && $_REQUEST['category_id']!=NULL)
{
 $ctable_where .= " AND cid = '".$_REQUEST['category_id']."' ";
}
if(isset($_REQUEST['product_type']) && $_REQUEST['product_type']!="" && $_REQUEST['product_type']!=NULL && $_REQUEST['product_type']!=undefined)
{
 	$ctable_where .= " AND product_type = '".$_REQUEST['product_type']."' ";
 	$product_type = $_REQUEST['product_type'];
}


if(isset($_REQUEST['unit_id']) && $_REQUEST['unit_id']!="" && $_REQUEST['unit_id']!=NULL && $_REQUEST['unit_id']!=undefined)
{
	$product_weight_data_r = $db->rp_getData("product_weight_price","product_id","isDelete=0 AND inner_unit='".$_REQUEST['unit_id']."' OR outer_unit='".$_REQUEST['unit_id']."'","",0);

	while($product_weight_data_d = mysqli_fetch_array($product_weight_data_r))
	{
		$product_weight_data_arr[] = $product_weight_data_d['product_id'];
	}

	$product_weight_data_str = implode(",",$product_weight_data_arr);
	// echo $product_weight_data_str;exit;

 	$ctable_where .= " AND id IN(".$product_weight_data_str.") ";
}


if(isset($_REQUEST['sales_order_unit_filter']) && $_REQUEST['sales_order_unit_filter']!="" && $_REQUEST['sales_order_unit_filter']!=NULL && $_REQUEST['sales_order_unit_filter']!=undefined)
{
 	$ctable_where .= " AND unit_id = '".$_REQUEST['sales_order_unit_filter']."' OR customer_unit_id='".$_REQUEST['sales_order_unit_filter']."' ";
 	// $sales_order_unit_filter = $_REQUEST['sales_order_unit_filter'];
}


$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"]) ) ? intval($_REQUEST["show"]) : 10;

if(isset($_REQUEST["page"])){
	$page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
	if(!is_numeric($page_number)){die('Invalid page number!');} //incase of invalid page number
}else{
	$page_number = 1; //if there's no page number, set it to 1
}

$get_total_rows = $db->rp_getTotalRecord($ctable,$ctable_where); //hold total records in variable
//break records into pages
$total_pages = ceil($get_total_rows/$item_per_page);

//get starting position to fetch the records
$page_position = (($page_number-1) * $item_per_page);

$ctable_r = $db->rp_getData($ctable,"*",$ctable_where,"id DESC",0);

/*for log*/
$flag = "Web";
$module_name = "Product";
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
<table id="datatable_1" class="table table-bordered table-striped dataTable">
	<thead>
		<tr>
			<th>Type</th>
			<th>Product Name</th>
			<th>Product Code</th>
			<th>Price</th>
			<th>Category</th>
			<th>Sub Category</th>
			<th>GST</th>
			<th class="text-nowrap">Order Unit</th>
			<th class="text-nowrap">Inner / Outer Unit</th>
			<th>HSN Code</th>
			<th>Image</th>
			<!-- <th>Display Order</th> -->
			<!-- <th>Action</th> -->
		</tr>
	</thead>
	<tbody>
	<?php
	if($ctable_r){
	if(mysqli_num_rows($ctable_r)>0){
		$count = 0;
		
		while($ctable_d = mysqli_fetch_array($ctable_r))
		{
			$unit_r = $db->rp_getData("product_weight_price","inner_unit,outer_unit","product_id='".$ctable_d['id']."' AND isDelete=0","",0);
			
			$unit_d = mysqli_fetch_array($unit_r);

			$unit_arr = array("1"=>"Caret","2"=>"Big Box","100"=>"Nos","-1"=>"Box","-2"=>"Strip","-3"=>"Pallet");

			$count++;
			$file=PRODUCT.$ctable_d['image_path'];

            if($ctable_d['image_path']!="" )
            {
                $img=SITEURL.PRODUCT.$ctable_d['image_path'];
                $br="";
            }
            else
            {                               
                $img=SITEURL."images/no_image_found.jpg";
                $br="border:1px solid #000";
            }
			$top_category_name=$db->rp_getValue("top_category_master","name","id='".$ctable_d['tcid']."'");
			$category_name=$db->rp_getValue("category_master","name","id='".$ctable_d['cid']."'");
			$TYPE = array('1' => "With Variant",'2' => "Without Variant" );
	?>
		<tr>
			<td><?php echo $TYPE[$ctable_d['product_type']]; ?></td>
			<td><a  href="<?php echo $ctable; ?>_crud.php?mode=edit&id=<?php echo $ctable_d['id']; ?>" ><?php echo stripslashes($ctable_d['name']); ?></a></td>
			<td>
				<?php
				$pro_r1=$db->rp_getData("product_weight_price","catno,price","product_id='".$ctable_d['id']."' AND isDelete=0","",0);
				if($pro_r1)
				{
					$PROIDS=array();
					while($pro_d1=mysqli_fetch_assoc($pro_r1))
					{
						// $PROIDS[]=$pro_d1['catno']."----"."<b>&#x20B9;".$pro_d1['price']."</b>";
						$PROIDS[]=$pro_d1['catno']."</b>";
					}
					$PROIDS=implode("<br/>", $PROIDS);
					echo $PROIDS;
				}
				?>
			</td>
			<td><?php echo $db->rp_getValue("product_weight_price","price","product_id='".$ctable_d['id']."' AND isDelete=0","",0); ?></td>
			<td><?php echo $top_category_name; ?></td>
			<td><?php echo $category_name; ?></td>
			<td><?php echo $ctable_d['igst']; ?></td>
			<td class="text-nowrap">
				<?php 
					echo "Sales Order Unit : ".$unit_arr[$ctable_d['unit_id']]."<br>";
					echo "Customer Order Unit : ".$unit_arr[$ctable_d['customer_unit_id']];
				?>
			</td>
			<td class="text-nowrap">
				<?php 
					echo "Inner Unit : ".$unit_arr[$unit_d['inner_unit']]."<br>";
					echo "Outer Unit : ".$unit_arr[$unit_d['outer_unit']];

			 	?>
			</td> 
			<!-- <td><?php echo $db->rp_getValue("unit","name","id='".$ctable_d['unit_id']."' AND isDelete=0","",0); ?></td> -->
			<td><?php echo $ctable_d['hsn_code']; ?></td>
			<td style="text-align: center;"><img src="<?= $img; ?>" style="<?= $br; ?>" width="80" height="80"></td>
			<!-- <td align="center">
				<input type="text" name="disp<?php echo $count; ?>" id="disp<?php echo $ctable_d['id']; ?>" value="<?php echo $ctable_d['display_order']; ?>" data-product_id="<?php echo $ctable_d['id'];?>" style="width:40px;text-align:center" onChange="CheckDispalyOrder('<?php echo $ctable_d['id'];?>');">
				<input type="hidden" name="b_id<?php echo $count ?>" value="<?php echo $ctable_d['id']; ?>">
			</td> -->
			<!-- <td>
				<a class="btn btn-info btn-sm" onClick="window.location.href='<?php echo $ctable; ?>_crud.php?mode=edit&id=<?php echo $ctable_d['id']; ?>'" title="Edit"><i class="fa fa-pencil"></i></a>
				<a class="btn btn-danger btn-sm" onClick="del_conf('<?php echo $ctable_d['id']; ?>');" title="Delete"><i class="fa fa-times"></i></a>
			</td> -->
		</tr>
	<?php
		}
	}
	}else{
			?>
			<tr>
			<td colspan="10"><p style="text-align:center;">No Data Found!!</p></td>
			</tr>
			<?php
		}
	?>
	</tbody>
	</table>
	<?php require_once "disconnect.php"; ?>
