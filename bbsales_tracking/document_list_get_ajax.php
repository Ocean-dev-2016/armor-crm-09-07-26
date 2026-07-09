<?php
$page_id=580;$page_slug='price_list_master';
include("connect.php");
//$Where=" isDelete=0 ";
$OrderBy="";
$Limit="";
$RequiredColumns="";
$RequestedData= $_REQUEST;
 $ctable_where = "";
 if(isset($_REQUEST['doc_type']) &&  $_REQUEST['doc_type']!="")
   {
      $ctable_where .= "document_type ='".$_REQUEST['doc_type']."'
               AND ";
      $type=$_REQUEST['doc_type'];
   }
   if(isset($_REQUEST['state']) &&  $_REQUEST['state']!="")
   {
      $ctable_where .= "class_id ='".$_REQUEST['state']."'
               AND ";
               $state_id=$_REQUEST['state'];

   }
   $ctable_where .= " isDelete=0";
// Response Column Name Specify
$RequiredColumns = (isset($RequestedData['columns']))?$RequestedData['columns']:array(0=>"id",1=>"pricelist_name",);
// getting total number records without any search

if( isset($_REQUEST['searchName']) && !empty($_REQUEST['searchName']) ) {
	$Query=$_REQUEST['searchName']['query'];
	$Where.=" AND (id LIKE '%".$Query."%'  OR title LIKE '%".$Query."%' )";
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
	$OrderBy="id ASC";
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
$Results=$db->rp_getData("document_list","*",$ctable_where,$OrderBy,0,$Limit);

$customer_type_arr = array(
	"1"=>"Super Stockist",
	"2"=>"Distributor",
	"3"=>"Dealer",
	"4"=>"B2B Customer",
	"6"=>"B2C Customer",
	"7"=>"Promotional Customer",
	"8"=>"Merchant Exports",
	"9"=>"Corporate Customer",
);

$sales_executive_type_arr = array(
	"area_sales_manager"=>"Business Development Manager",
	"sales_officer"=>"Area Sales Manager",
	"sales_executive"=>"Sales Officer",
	"service_executive"=>"Sales Excecutive",
	"service_executive"=>"Service Executive",
);
?> 

	
<table class="table table-striped table-hover table-bordered unit_table_class" id="country_table_id">
    <thead>
    	<tr>
    		<th></th>
    		<th></th>
    		<th>
    			<select class="form-control input-medium status" name="state" id="state">
					<option value="">Select State</option>
						<?php
						$id_r = $db->rp_getData("class","*","isDelete=0 AND isActive=1",0);
						if(mysqli_num_rows($id_r)>0){
							while($id_d = mysqli_fetch_array($id_r)){
						?>
						<option <?php echo ($type==$id_d['id'])?"selected":"" ; ?> value="<?php echo $id_d['id']; ?>"><?php echo $id_d['name']; ?></option>
						<?php
							}
						}
						?>
                </select>
    		</th>
    		<th>
    			<select class="form-control input-medium status" name="doc_type" id="doc_type" >
					<option value="">Select Document Type</option>
					<?php 
						$d_r=$db->rp_getData("document_type","*","isDelete=0");
						
						if(mysqli_num_rows($d_r)>0){
							while($d_d=mysqli_fetch_assoc($d_r)){
								?>
								<option <?php echo ($state_id==$d_d['id'])?"selected":"" ; ?> value="<?php echo $d_d['id'];?>"><?php echo $d_d['name']; ?></option>
								<?php
							}
						}
					?>
                </select>
    		</th>
    		<th></th>
    		<th></th>
    		<th></th>
    		<th></th>
    	</tr>

        <tr>
        	<th style="width: 5%;"></th>
			<th style="width: 5%;">Sr.no</th>
			<th>State</th> 
			<th>Document Type</th>
			<th>Customer Type</th>
			<th>Sales Type</th>
			<th>Document Name</th>
			<th>Image</th> 
		</tr>
    </thead>
    <tbody>
	    <?php 
		if($Results)
		{												
		  	$cnt=0;
			while($R=mysqli_fetch_assoc($Results))
			{
				// print_r($R);
				$cnt++;
				$imgpath = GST_VISITING_DETAIL_A.$R['image_path'];
				$image_url = GST_VISITING_DETAIL_A.$R['image_path'];
				$ext = strtolower(pathinfo($imgpath, PATHINFO_EXTENSION)); 
				if($ext=="pdf")
				{
					$imgpath = GST_VISITING_DETAIL_A.'pdf.png';
				}
				?>
	  			<tr class="">
	  				<td>
	  					<div class="btn-group">				
							<button aria-expanded="false" data-toggle="dropdown" type="button" class="btn btn-sm blue dropdown-toggle">
								<i class="fa fa-gear"></i>
							</button>
							<ul role="menu" class="dropdown-menu">
								<?php
								if($rights['delete_flag']==1 && $R['id']!=-1)
								{
									?>
									<li>
										<a onClick="del_conf('<?php echo $R['id']; ?>');" title="Delete">
											<span class="text-danger">
												<i class="fa fa-times"></i>
												&nbsp;Delete
											</span>
										</a>
									</li>
									<?php
								}
								?>	
							</ul>
						</div>
					</td>
	  				<td><?php echo $cnt; ?></td>
					<td><?php echo $db->rp_getValue("class","name","id='".$R['class_id']."'"); ?></td>
					<td><?php echo $db->rp_getValue("document_type","name","id='".$R['document_type']."'"); ?></td>
					<td>
						<?php
							
							$customer_type = explode(",",$R['customer_type']);
							// print_r($customer_type);exit;
							for ($ctype=0; $ctype < sizeof($customer_type) ; $ctype++) { 
								
								echo $customer_type_arr[$customer_type[$ctype]]."<br>";
							}

						?>
					</td>
					<td>
						<?php
							
							$sales_executive_type = explode(",",$R['sales_type']);
							for ($stype=0; $stype < sizeof($sales_executive_type); $stype++) { 
								
								echo $sales_executive_type_arr[$sales_executive_type[$stype]]."<br>";
							}
						?>
					</td>
					<td><?= $R['document_name'] ?></td>
					<td style="text-align: center;"><a target="_blank" href="<?php echo $image_url; ?>"><img src="<?php echo $imgpath; ?>" height="80px" width="80px" /></a></td>
				</tr> 
				<?php													 
			}
		}
		else
		{
			?>
			<tr>
				<td colspan="8" class="text-center">No Data Found!!</td>
			</tr>
			<?php
		}
		?>   
    </tbody>
</table> 

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
<script type="text/javascript">
	$("#state").select2(); 
	$("#doc_type").select2(); 
</script>
 <?php require_once("disconnect.php"); ?>