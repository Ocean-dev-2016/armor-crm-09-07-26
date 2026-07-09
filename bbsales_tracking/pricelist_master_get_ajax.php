<?php
$page_id=580;$page_slug='price_list_master';
include("connect.php");
$Where="isDelete=0";
$OrderBy="";
$Limit="";
$RequiredColumns="";
$RequestedData= $_REQUEST;
 
// Response Column Name Specify
$RequiredColumns = (isset($RequestedData['columns']))?$RequestedData['columns']:array(0=>"id",1=>"pricelist_name",);
// getting total number records without any search

if( isset($_REQUEST['searchName']) && !empty($_REQUEST['searchName']) ) {
	$Query=$_REQUEST['searchName'];
	$Where.=" AND (id LIKE '%".$Query."%'  OR pricelist_name LIKE '%".$Query."%' )";
}

if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
{

    if($rights['personal_flag']==1)
    {
    	$Where.=" AND created_by='".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID']."'";
    	
	}
    else
    {
    	if($rights['chain_vise_flag'] == 1)
	 	{
				

				$check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];

			    $get_sales_type=$db->rp_getValue("sales_executive","type","isDelete=0 AND id='". $check_id."'",0);
			    if ($get_sales_type== "sales_manager") 
			    {
			        $sales_executive_type = "General Manager";
			        $key="sm_id";
			        $WhereCondition.=' ' .$key.'='.$check_id;
			    }

			    else if ($get_sales_type == "area_sales_manager") 
			    {
			        $sales_executive_type = "National Sales Manager";//Regional Manager
			        $key="asm_id";
			        $WhereCondition.=' ' .$key.'='.$check_id;
			    }

			    else if ($get_sales_type == "sales_officer") 
			    {
			        $sales_executive_type = "Area Sales Manager";//Sales Officer
			        $key="so_id";
			        $WhereCondition.=' ' .$key.'='.$check_id;
			    }
			    else if ($get_sales_type == "sales_executive") 
			    {
			        $sales_executive_type = "Sales Executive";
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
					while($data_d=mysql_fetch_assoc($data))
					{
						$SALEID1[]=$data_d['id'];
					}
				}
				if(!empty($SALEID1))
				{
					$SALEID1=implode(",", $SALEID1);
					$Where .= "  AND  (created_by IN (".$SALEID1.','.$_SESSION[SITE_SESS.'REFERANCE_ID'].") ) ";	

					
					
				}
				else
				{
					// $check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];
					$Where.=" AND created_by='".$_SESSION[SITE_SESS.'REFERANCE_ID']."'";
	 				// $ctable_where.= " AND isDelete=0 AND (complain_assign_to = '".$check_id."' OR complain_created_by = '".$check_id."') ";
						// $ctable_where .= "  AND sales_executive_id IN (".$_SESSION[SITE_SESS.'REFERANCE_ID'].")";		
				}
		}
    }
}



$TotalFiltered = $db->rp_getTotalRecord("price_list",$Where);
if(isset($RequestedData['page']) && is_numeric($RequestedData['page']))
$PageNumber= filter_var($RequestedData["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH);
else 
$PageNumber=1;

if(isset($RequestedData['show']) && is_numeric($RequestedData['show']))
$LowerLimit= filter_var($RequestedData["show"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH);
else 
$LowerLimit=100;

if(isset($RequestedData['order']))
$OrderBy=$RequiredColumns[$RequestedData['order'][0]['column']]."   ".$RequiredColumns['order'][0]['dir'];
else
{
	$OrderBy="pricelist_name ASC";
}
$UpperLimit=($PageNumber-1)*$LowerLimit;
if($UpperLimit!="" &&  $LowerLimit!="")
{
	$Limit=$UpperLimit." ,".$LowerLimit."   ";
	
}
else if($UpperLimit!="")
$Limit=$UpperLimit;
$RequiredColumns=implode(",",$RequiredColumns);
// $Results=$Pricelist->get_all($Where,$OrderBy,$Limit,$RequiredColumns);				
$Results=$db->rp_getData("price_list","*",$Where,$OrderBy,0,$Limit);
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

 <div class="table-scrollable">
<table class="table table-striped table-hover table-bordered unit_table_class" id="country_table_id">
    <thead class="fix-th">
        <tr>
            <th class="fix-th1" style="width: 5%;"></th>
            <th class="fix-th1" style="width: 5%;">Sr.no</th>
			<th class="fix-th1">Name</th> 
			<th class="fix-th1">Action</th>
        </tr>
    </thead>
    <tbody>
	    <?php 
		if($Results)
		{												
		  	// In Items there are all objects you need here are keys you will find in this array
			// id|name|slug														
			$cnt=0;
			while($R=mysqli_fetch_assoc($Results))
			{
				// print_r($R);
				$cnt++;
				$totalPro=$db->rp_getValue("product_price_list","COUNT(id)","price_list_id='".$R['id']."' AND isDelete=0");
 		?>
	  	<tr class="">

	  		<td>
				<?php 				
				if($rights['update_flag']==1)
				{
				?>
				<div class="btn-group">				
					<button aria-expanded="false" data-toggle="dropdown" type="button" class="btn btn-sm blue dropdown-toggle">
						<i class="fa fa-gear"></i>
					</button>
					<ul role="menu" class="dropdown-menu">
						<li>
							<a href="pricelist_master_crud.php?mode=edit&id=<?php echo $R['id']; ?>" title="Edit">
								<span class="text-primary">
									<i class="fa fa-pencil"></i>
									&nbsp;Edit
								</span>
							</a>
						</li>	
					</ul>
				</div>
				<?php
				}
				?>
			</td>

			<td><?php echo $cnt; ?></td>
			<td><?php echo $R['pricelist_name']; ?></td>
			<td>		
				<?php 			
				if($rights['update_flag']==1 && $rights['insert_flag']==1)
				{
				?>
				<a href="price_table_manage.php?pid=<?php echo $R['id']; ?>" title="Manage Products" class="btn btn-info btn-sm">Manage Products (<?php echo $totalPro; ?>)</a>
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
				<td colspan="4" class="text-center">No Data Found!!</td>
			</tr>
			<?php
		}
		?>   
    </tbody>
</table> 
</div>
<div class="row">
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
			echo paginate_function($item_per_page, $page_number, $get_total_rows, $total_pages); 
			?>
			</ul>
			<span>
			<?php
			for($i=1; $i<=$last; $i++) {
				if ($i == $pagenum ) {
				?>
					<a class="paginate_button current" aria-controls="datatable1"><?php echo $i ?></a>
			<?php
				} else {  
				?>
					<a class="paginate_button" aria-controls="datatable1" onclick="displayRecords('<?php echo $page_limit;  ?>', '<?php echo $i; ?>');"><?php echo $i ?></a>
			<?php 
				}
			} 
			?>
			</span>
		</div>
		</div>
	</div>	
	<br />						

<?php
################ pagination function #########################################
function paginate_function($item_per_page, $current_page, $total_records, $total_pages)
{
    $pagination = '';
    if($total_pages > 0 && $total_pages != 1 && $current_page <= $total_pages){ //verify total pages and current page number
        $right_links    = $current_page + 3; 
        $previous       = $current_page - 3; //previous link 
        $next           = $current_page + 1; //next link
        $first_link     = true; //boolean var to decide our first link
        
        if($current_page > 1){
			$previous_link = ($previous<=0)?1:$previous;
            $pagination .= '<li class="paginate_button "><a href="#" aria-controls="datatable1" data-page="1" title="First">&laquo;</a></li>'; //first link
            $pagination .= '<li class="paginate_button "><a href="#" aria-controls="datatable1" data-page="'.$previous_link.'" title="Previous">&lt;</a></li>'; //previous link
                for($i = ($current_page-2); $i < $current_page; $i++){ //Create left-hand side links
                    if($i > 0){
                        $pagination .= '<li class="paginate_button "><a href="#"  data-page="'.$i.'" aria-controls="datatable1" title="Page'.$i.'">'.$i.'</a></li>';
                    }
                }   
            $first_link = false; //set first link to false
        }
        
        if($first_link){ //if current active page is first link
            $pagination .= '<li class="paginate_button active"><a aria-controls="datatable1">'.$current_page.'</a></li>';
        }elseif($current_page == $total_pages){ //if it's the last active link
            $pagination .= '<li class="paginate_button active"><a aria-controls="datatable1">'.$current_page.'</a></li>';
        }else{ //regular current link
            $pagination .= '<li class="paginate_button active"><a aria-controls="datatable1">'.$current_page.'</a></li>';
        }
                
        for($i = $current_page+1; $i < $right_links ; $i++){ //create right-hand side links
            if($i<=$total_pages){
                $pagination .= '<li class="paginate_button "><a href="#" aria-controls="datatable1" data-page="'.$i.'" title="Page '.$i.'">'.$i.'</a></li>';
            }
        }
        if($current_page < $total_pages){ 
			$next_link = ($i > $total_pages)? $total_pages : $i;
			$pagination .= '<li class="paginate_button "><a href="#" aria-controls="datatable1" data-page="'.$next_link.'" title="Next">&gt;</a></li>'; //next link
			$pagination .= '<li class="paginate_button "><a href="#" aria-controls="datatable1" data-page="'.$total_pages.'" title="Last">&raquo;</a></li>'; //last link
        }
    }
    return $pagination; //return pagination links
}
?>
<?php require_once 'disconnect.php';  ?>