<?php
/**
 * Shared product list filter builder for grid, print and PDF.
 */
function productListBuildWhere($db, $request)
{
	$ctable_where = "";

	if(isset($request['searchName']) && trim($request['searchName']) != "")
	{
		$search = $db->clean(trim($request['searchName']));
		$where11 = "";
		$pro_r1 = $db->rp_getData("product_weight_price","product_id","catno LIKE '%".$search."%' AND isDelete=0","",0);
		$PROIDS1 = array();
		if($pro_r1)
		{
			while($pro_d1 = mysqli_fetch_assoc($pro_r1))
			{
				$PROIDS1[] = $pro_d1['product_id'];
			}
		}
		if(!empty($PROIDS1))
		{
			$PROIDS1 = implode(",", $PROIDS1);
			$where11 = " OR id IN (".$PROIDS1.")";
		}
		$ctable_where .= " (LOWER(name) like '%".strtolower($search)."%' ".$where11.") AND ";
	}

	$ctable_where .= " 1=1 AND isDelete='0' AND id!='0'";

	if(isset($_SESSION[SITE_SESS.'_ADMIN_TYPE']) && $_SESSION[SITE_SESS.'_ADMIN_TYPE'] != 0)
	{
		global $rights;
		if(isset($rights['personal_flag']) && $rights['personal_flag'] == 1)
		{
			$ctable_where .= " AND created_by='".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID']."'";
		}
		else if(isset($rights['chain_vise_flag']) && $rights['chain_vise_flag'] == 1)
		{
			$WhereCondition = "";
			$check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];
			$get_sales_type = $db->rp_getValue("sales_executive","type","isDelete=0 AND id='". $check_id."'",0);
			if ($get_sales_type == "sales_manager")
			{
				$WhereCondition .= ' sm_id='.$check_id;
			}
			else if ($get_sales_type == "area_sales_manager")
			{
				$WhereCondition .= ' asm_id='.$check_id;
			}
			else if ($get_sales_type == "sales_officer")
			{
				$WhereCondition .= ' so_id='.$check_id;
			}
			else if ($get_sales_type == "sales_executive")
			{
				$WhereCondition .= ' se_id='.$check_id;
			}
			else
			{
				$WhereCondition .= ' type = "service_engineer"';
			}

			$data = $db->rp_getData("sales_executive","id",$WhereCondition,"",0);
			$SALEID1 = array();
			if($data)
			{
				while($data_d = mysqli_fetch_assoc($data))
				{
					$SALEID1[] = $data_d['id'];
				}
			}
			if(!empty($SALEID1))
			{
				$SALEID1 = implode(",", $SALEID1);
				$ctable_where .= " AND created_by IN (".$SALEID1.','.$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'].")";
			}
			else
			{
				$ctable_where .= " AND created_by IN (".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'].")";
			}
		}
	}

	if(isset($request['top_category_id']) && $request['top_category_id'] != "" && $request['top_category_id'] != "undefined" && $request['top_category_id'] != "-1")
	{
		$ctable_where .= " AND tcid = '".$db->clean($request['top_category_id'])."' ";
	}

	if(isset($request['product_type']) && $request['product_type'] != "" && $request['product_type'] != "undefined")
	{
		$ctable_where .= " AND product_type = '".$db->clean($request['product_type'])."' ";
	}

	if(isset($request['unit_id']) && $request['unit_id'] != "" && $request['unit_id'] != "undefined")
	{
		$product_weight_data_arr = array();
		$unit_id = $db->clean($request['unit_id']);
		$product_weight_data_r = $db->rp_getData("product_weight_price","product_id","isDelete=0 AND (inner_unit='".$unit_id."' OR outer_unit='".$unit_id."')","",0);
		if($product_weight_data_r)
		{
			while($product_weight_data_d = mysqli_fetch_array($product_weight_data_r))
			{
				$product_weight_data_arr[] = $product_weight_data_d['product_id'];
			}
		}
		if(!empty($product_weight_data_arr))
		{
			$product_weight_data_str = implode(",", $product_weight_data_arr);
			$ctable_where .= " AND id IN(".$product_weight_data_str.") ";
		}
	}

	if(isset($request['sales_order_unit_filter']) && $request['sales_order_unit_filter'] != "" && $request['sales_order_unit_filter'] != "undefined")
	{
		$unit_filter = $db->clean($request['sales_order_unit_filter']);
		$ctable_where .= " AND (unit_id = '".$unit_filter."' OR customer_unit_id='".$unit_filter."') ";
	}

	if(isset($request['category_id']) && $request['category_id'] != "" && $request['category_id'] != "0" && $request['category_id'] != "undefined")
	{
		$ctable_where .= " AND cid = '".$db->clean($request['category_id'])."' ";
	}

	return $ctable_where;
}

function productListFilterLabels($db, $request)
{
	$TYPE = array('1' => "With Variant", '2' => "Without Variant");
	$filter_parts = array();

	if(isset($request['top_category_id']) && $request['top_category_id'] != "" && $request['top_category_id'] != "-1" && $request['top_category_id'] != "undefined")
	{
		$filter_parts[] = "Category: ".$db->rp_getValue("top_category_master","name","id='".$db->clean($request['top_category_id'])."'",0);
	}
	if(isset($request['category_id']) && $request['category_id'] != "" && $request['category_id'] != "0" && $request['category_id'] != "undefined")
	{
		$filter_parts[] = "Sub Category: ".$db->rp_getValue("category_master","name","id='".$db->clean($request['category_id'])."'",0);
	}
	if(isset($request['product_type']) && $request['product_type'] != "" && $request['product_type'] != "undefined")
	{
		$filter_parts[] = "Type: ".$TYPE[$request['product_type']];
	}
	if(isset($request['searchName']) && trim($request['searchName']) != "")
	{
		$filter_parts[] = "Search: ".trim($request['searchName']);
	}

	return $filter_parts;
}
?>
