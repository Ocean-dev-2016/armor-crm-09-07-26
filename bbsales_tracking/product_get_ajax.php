<?php
/*
 * @author Ravi Patel
 */
 $page_id=559;$page_slug='page_product';
include("connect.php");
// print_r($_REQUEST);exit;
$ctable = "product";
$ctable1 = "Product";

$ctable_where = "";
// Get the total number of rows in the table

if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!="")
{
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
	$ctable_where .= " (LOWER(name) like '%".strtolower(trim($_REQUEST['searchName']))."%' ".$where11.") AND ";
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
// if($_REQUEST['top_category_id']==0){

// }
if(isset($_REQUEST['top_category_id']) && $_REQUEST['top_category_id']!="" && $_REQUEST['top_category_id']!=NULL && $_REQUEST['top_category_id']!=undefined)
{
	

	if ($_REQUEST['top_category_id'] != '-1') 
	{
	 	$ctable_where .= " AND tcid = '".$_REQUEST['top_category_id']."' ";
	 	$top_category_id = $_REQUEST['top_category_id'];
	}
	else
	{
		$top_category_id = $_REQUEST['top_category_id'];
	}

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

if(isset($_REQUEST['category_id']) && $_REQUEST['category_id']!="" && $_REQUEST['category_id']!=NULL && $_REQUEST['category_id']!=undefined)
{
 	$ctable_where .= " AND cid = '".$_REQUEST['category_id']."' ";
 	$category_id = $_REQUEST['category_id'];
}

/*if(isset($_REQUEST['brand_id']) && $_REQUEST['brand_id']!="" && $_REQUEST['brand_id']!=NULL)
{
 	$ctable_where .= " AND brand_id = '".$_REQUEST['brand_id']."' ";
 	$brand_id = $_REQUEST['brand_id'];
}*/

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
// print_r($ctable_where);exit;
if($top_category_id!="")
{
  $ctable_r = $db->rp_getData($ctable,"*",$ctable_where,"id DESC limit $page_position, $item_per_page",0);
}
?>
<style>
	.table-scrollable {
		width: auto;
		height: 450px;
		overflow-x: scroll;
		overflow-y: scroll;
		border: 1px solid #e7ecf1;
		margin: 10px 0 !important;
	}

	.fix-th
    {
        background-color: #f5f5f5 !important;position: sticky;top: 0; z-index: 1;
    }
    .fix-th1
    {
        background-color: #e5e5e5 !important;position: sticky;top: 0; z-index: 1;
    }
</style>

	<div style="margin-left: -300px;" class="col-md-2">
			<!-- <div class="btn-group">
				<input type="hidden" name="disp_count" value="<?php echo $count; ?>">
				<button type="submit" name="submit" onClick="document.frm.submit();" class="btn btn-primary btn-flat" >Update</button>
			</div> -->
		</div>	
<div class="table-scrollable">
<table id="datatable_12" class="table table-bordered table-striped dataTable">
	<thead class="fix-th">
		<tr>
			<th></th>
			<th>
				<select class="form-control status" name="product_type" id="product_type">
					<option value="">Select Type</option>
					<option value="1" <?php echo ($product_type==1)?"selected":"" ; ?> >With Variant</option>
					<option value="2" <?php echo ($product_type==2)?"selected":"" ; ?> >Without Variant</option>
	            </select>
	        </th>
			<th></th>
			<th></th>
			<th>
				<select class="form-control status" name="top_category_id" id="top_category_id" onchange="getCategory(this.value);">
					<option value="">Select Category</option>
					<option value="-1">All</option>
	                 
					 <?php 
						$top_category_list_r=$db->rp_getData('top_category_master',"*","isDelete=0","",0);
						while($top_category_list_d=mysqli_fetch_assoc($top_category_list_r))
						{
							?>
							<option <?php echo ($top_category_id==$top_category_list_d['id'])?"selected":"" ; ?> value="<?php echo $top_category_list_d['id']?>">
							<?php echo $top_category_list_d['name'];?>
							</option>
							<?php
						}
					?>
	            </select>
			</th>
			<th>
				<select class="form-control status" name="category_id" id="category_id">
				<option value="">Select Sub Category</option>  
					<option value="0">All</option>
				 <!-- <?php 
					$category_list_d=$db->rp_getData('category_master',"*","isDelete=0","",0);
					while($category_list_r=mysqli_fetch_assoc($category_list_d))
					{
						?>
						<option <?php echo ($category_id==$category_list_r['id'])?"selected":"" ; ?> value="<?php echo $category_list_r['id']?>">
						<?php echo $category_list_r['name'];?>
						</option>
						<?php
					}
				?>		 
				<?php 
				$category_r=$db->rp_getData("category_master","*","isDelete=0",0);
				while($category_d=mysqli_fetch_assoc()){
				?>
				<option value="<?php echo $category_d['id'];?>"><?php echo $category_d['name'];?></option>
				<?php }?> -->
			</select>
			</th>
			<th>
				<select class="form-control unit" name="sales_order_unit_filter" id="sales_order_unit_filter">
				<option value="">Select Unit</option>  
			 	<option <?=($_REQUEST['sales_order_unit_filter']==-1)?"selected":""; ?> value="-1">Box</option>
				<option <?=($_REQUEST['sales_order_unit_filter']==-2)?"selected":""; ?> value="-2">Strip</option>
				<option <?=($_REQUEST['sales_order_unit_filter']==-3)?"selected":""; ?> value="-3">Pallet</option>
				<option <?=($_REQUEST['sales_order_unit_filter']==1)?"selected":""; ?> value="1">Caret</option>
				<option <?=($_REQUEST['sales_order_unit_filter']==2)?"selected":""; ?> value="2">Big Box</option>
				<option <?=($_REQUEST['sales_order_unit_filter']==100)?"selected":""; ?> value="100">Nos</option>	 	
			</select>
			</th>
			<th>
				<select class="form-control unit" name="unit_id" id="unit_id">
				<option value="">Select inner/outer Unit</option>  
				 <option <?=($_REQUEST['unit_id']==-1)?"selected":""; ?> value="-1">Box</option>
				<option <?=($_REQUEST['unit_id']==-2)?"selected":""; ?> value="-2">Strip</option>
				<option <?=($_REQUEST['unit_id']==-3)?"selected":""; ?> value="-3">Pallet</option>
				<option <?=($_REQUEST['unit_id']==1)?"selected":""; ?> value="1">Caret</option>
				<option <?=($_REQUEST['unit_id']==2)?"selected":""; ?> value="2">Big Box</option>
				<option <?=($_REQUEST['unit_id']==100)?"selected":""; ?> value="100">Nos</option>
			</select>
			</th> 
			<th></th>
			<!-- <th></th> -->
			<th></th>
		</tr>
		
		<tr>
			<th class="fix-th1" style="width: 5%;"></th>
			<th class="fix-th1">Type</th>
			<th class="fix-th1">Product Name</th>
			<th class="fix-th1">Product Code - Price</th>
			<th class="fix-th1">Category</th>
			<th class="fix-th1">Sub Category</th>
			<th class="text-nowrap fix-th1">Order Unit</th>
			<th class="text-nowrap fix-th1">Inner / Outer Unit</th>
			<th class="fix-th1">Image</th>
			<!-- <th>Is Visible Or Not</th> -->
			<th class="fix-th1">Display Order</th>
			<!-- <th>Action</th> -->
		</tr>
	</thead>
	<tbody>
	<?php
	if($ctable_r)
	{
	if(mysqli_num_rows($ctable_r)>0)
	{
		$count = 0;
		
		while($ctable_d = mysqli_fetch_array($ctable_r))
		{
			$count++;
			$file=PRODUCT.$ctable_d['image_path'];

            if($ctable_d['image_path']!="" )
            {
                $img=SITEURL.PRODUCT.$ctable_d['image_path'];
                $br="";
            }
            else
            {                               
                $img=SITEURL."images/no_data_found.jpg";
                $br="border:1px solid #000";
            }
			$top_category_name=$db->rp_getValue("top_category_master","name","id='".$ctable_d['tcid']."'");
			$category_name=$db->rp_getValue("category_master","name","id='".$ctable_d['cid']."'");
			$brand_name=$db->rp_getValue("brand","name","id='".$ctable_d['brand_id']."'",0);
			$unit_r = $db->rp_getData("product_weight_price","inner_unit,outer_unit","product_id='".$ctable_d['id']."' AND isDelete=0","",0);
			$unit_d = mysqli_fetch_array($unit_r);

			$unit_arr = array("1"=>"Caret","2"=>"Big Box","100"=>"Nos","-1"=>"Box","-2"=>"Strip","-3"=>"Pallet");

			$TYPE = array('1' => "With Variant",'2' => "Without Variant" );
	?>
		<tr>
			<td><?php  $ctable_d['id']; ?>
			<?php				
			if($rights['update_flag']==1)
			{
			?>	
				<div class="btn-group">				
					<button aria-expanded="false" data-toggle="dropdown" type="button" class="btn btn-sm blue dropdown-toggle"> <i class="fa fa-gear"></i>
					</button>
					<ul role="menu" class="dropdown-menu">
						<li>
							<a  href="<?php echo $ctable; ?>_crud.php?mode=edit&id=<?php echo $ctable_d['id']; ?>" title="Edit"><span class="text-primary"><i class="fa fa-pencil"></i> &nbsp;Edit</span></a>
						</li>
						<?php
						if($rights['delete_flag']==1)
						{
						?>
						<li>
							<a  onClick="del_conf('<?php echo $ctable_d['id']; ?>');" title="Delete"><span class="text-danger"><i class="fa fa-times"></i> &nbsp;Delete</span></a>
						</li>
						<?php
						}
						?>
						<?php
							if ($ctable_d["isVisible"] == 1) 
							{
								?>
								<li><a onClick="visible_or_not('<?php echo $ctable_d['id']; ?>',0);" title="Active"><span class="text-primary"><i class="fa fa-toggle-off"></i> &nbsp;Active</span></a></li>
								<?php
							}
							else
							{
								?>
								<li><a onClick="visible_or_not('<?php echo $ctable_d['id']; ?>',1);" title="Active"><span class="text-danger"><i class="fa fa-toggle-on"></i> &nbsp;Deactive</span></a></li>
								<?php
							}
						?>
					</ul>
				</div>
			<?php
			}
			?>
			</td>
			<td><?php echo $TYPE[$ctable_d['product_type']]; ?></td>
			<td><a  href="<?php echo $ctable; ?>_crud.php?mode=edit&id=<?php echo $ctable_d['id']; ?>" ><?php echo stripslashes($ctable_d['name']); ?></a></td>
			<td>
				<?php
				$pro_r1=$db->rp_getData("product_weight_price","catno,price,inner_unit,outer_unit","product_id='".$ctable_d['id']."' AND isDelete=0","",0);
				if($pro_r1)
				{
					$PROIDS=array();
					while($pro_d1=mysqli_fetch_assoc($pro_r1))
					{
						$PROIDS[]=$pro_d1['catno']."----"."<b>&#x20B9;".$pro_d1['price']."</b>";
					}
					$PROIDS=implode("<br/>", $PROIDS);
					echo $PROIDS;
				}
				?>
			</td>
			<td><?php echo $top_category_name; ?></td>
			<td><?php echo $top_category_name.' - '.$category_name; ?></td>
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
			<td style="text-align: center;"><img src="<?= $img; ?>" style="<?= $br; ?>" width="80" height="80"></td>


			<!-- <td>
	            <input type="checkbox" onClick="visible_or_not('<?php echo $ctable_d['id']; ?>');" class="form-control"  id="visible_or_not_id<?php echo $ctable_d['id']; ?>" <?php if ($ctable_d["isVisible"] == 1) echo "checked='checked'"; ?> value="<?= $ctable_d["isVisible"]; ?>">
	        </td> -->

			<td align="center">
				<input type="text" name="disp<?php echo $count; ?>" id="disp<?php echo $ctable_d['id']; ?>" value="<?php echo $ctable_d['display_order']; ?>" data-product_id="<?php echo $ctable_d['id'];?>" style="width:40px;text-align:center" onChange="CheckDispalyOrder('<?php echo $ctable_d['id'];?>');">
				<input type="hidden" name="b_id<?php echo $count ?>" value="<?php echo $ctable_d['id']; ?>">
			</td>
			<!-- <td>
				<a class="btn btn-info btn-sm" onClick="window.location.href='<?php echo $ctable; ?>_crud.php?mode=edit&id=<?php echo $ctable_d['id']; ?>'" title="Edit"><i class="fa fa-pencil"></i></a>
				<a class="btn btn-danger btn-sm" onClick="del_conf('<?php echo $ctable_d['id']; ?>');" title="Delete"><i class="fa fa-times"></i></a>
			</td> -->
		</tr>
	<?php
		}
	}
	}
	else
	{
		?>
		<tr>
		<td colspan="11"><b><h4 style="text-align:center;">Please Select Category And Sub Category</h4></b></td>
		</tr>
		<?php
	}
	?>
	</tbody>
	</table>
		
	<div class="row">
		<div class="col-md-12">
			<br>
			<!-- <?php
				echo $db->getAddButton($ctable);
			?> -->
		</div>
	</div>
</div>
<!-- <div class="row">
	<div class="col-md-6">
		<div class="dataTables_info"> Rows Limit:
			<select id="numRecords" onChange="changeDisplayRowCount(this.value);">
				<option value="500" <?php if ($_REQUEST["show"] == 500 || $_REQUEST["show"] == "") {
										echo ' selected="selected"';
									}  ?>>500</option>
				<option value="1000" <?php if ($_REQUEST["show"] == 1000) {
										echo ' selected="selected"';
									}  ?>>1000</option>
				<option value="2000" <?php if ($_REQUEST["show"] == 2000) {
											echo ' selected="selected"';
										}  ?>>2000</option>
				<option value="5000" <?php if ($_REQUEST["show"] == 5000) {
											echo ' selected="selected"';
										}  ?>>5000</option>
			</select>
		</div>
	</div>
	<div class="col-md-6">
		<div class="dataTables_paginate paging_simple_numbers">
			<ul class="pagination">
			<?php 
			echo $db->rp_paginate_function($item_per_page, $page_number, $get_total_rows, $total_pages); 
			?>
			</ul>
		</div>
	</div>
</div> -->
		
<script type="text/javascript">
	$("#product_type").select2(); 
	$("#unit_id").select2(); 
	$("#top_category_id").select2(); 
	$("#category_id").select2(); 
	$("#brand_id").select2(); 
	$("#sales_order_unit_filter").select2(); 
</script>
<?php require_once "disconnect.php"; ?>