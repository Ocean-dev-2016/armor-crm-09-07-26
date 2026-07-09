<?php
$page_id=577;$page_slug='add_to_cart_orders';
/*
 * @author Ravi Patel
 */
include("connect.php");
$ctable 	= "orders";
$ctable1 	= "Orders";
$uid=isset($_REQUEST['uid'])?$_REQUEST['uid']:"";
$order_type=isset($_REQUEST['order_type'])?$_REQUEST['order_type']:"";
$ctable_where = "";
// Get the total number of rows in the table
if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!="")
{
	$ctable_where .= " (
							customer_name like '%".$db->clean($_REQUEST['searchName'])."%' OR
							company_name like '%".$db->clean($_REQUEST['searchName'])."%' OR
							order_no like '%".$db->clean($_REQUEST['searchName'])."%'
						) AND ";
}

if(isset($_REQUEST['sales_id']) && $_REQUEST['sales_id']!="" && $_REQUEST['sales_id']!=NULL && $_REQUEST['sales_id']!=undefined)
{
	$ctable_where .= " sales_id = '".$_REQUEST['sales_id']."' AND";
}

if(isset($_REQUEST['o_type']) && $_REQUEST['o_type']!="" && $_REQUEST['o_type']!=NULL && $_REQUEST['o_type']!=undefined)
{
	$ctable_where .= "customer_type = '".$_REQUEST['o_type']."' AND  ";
}

if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
{
	/*if($_REQUEST['order_type'])
	{
	$ctable_where .= " isDelete=0 AND customer_type='".$_REQUEST['order_type']."'";
	}
	else
	{*/
	$ctable_where .= " isDelete=0 AND status=-1";	
	//}
	
}
// for customer login
else
{
	if($_REQUEST['order_type'] && $_REQUEST['uid'])
	{
	$ctable_where .= " isDelete=0 AND customer_type='".$_REQUEST['order_type']."' AND customer_id='".$_REQUEST['uid']."'";
	}
	else if($_REQUEST['order_type'])
	{
		$ctable_where .= " isDelete=0 AND customer_type='".$_REQUEST['order_type']."' AND status=-1";
	}
	else{
		$ctable_where .= " isDelete=0 AND status=-1";
	}
}

if(isset($_REQUEST['status']) && $_REQUEST['status']!="" && $_REQUEST['status']!=NULL)
{
 $ctable_where .= " AND status = '".$_REQUEST['status']."' ";
}

if(isset($_REQUEST['ToDate']) && $_REQUEST['ToDate']!="" && $_REQUEST['ToDate']!=NULL && $_REQUEST['ToDate']!=undefined)
{
  $ctable_where .= " AND order_date <= '".date_format(date_create($_REQUEST['ToDate']),"Y-m-d")."' ";
}

if(isset($_REQUEST['FromDate']) && $_REQUEST['FromDate']!="" && $_REQUEST['FromDate']!=NULL && $_REQUEST['FromDate']!=undefined)
{
     $ctable_where .= " AND order_date >= '".date_format(date_create($_REQUEST['FromDate']),"Y-m-d")."' ";
}

if(isset($_REQUEST['df']) && $_REQUEST['df']!="" && $_REQUEST['df']!=undefined)
{
	//echo $_REQUEST['df'];exit;
	$date_filter_query = urldecode( $_REQUEST['df'] );

	$date_filter_query_ex=explode(" to ",$date_filter_query);

	$ctable_where .= " AND ( DATE(order_date)>='".date("Y-m-d",strtotime($date_filter_query_ex['0']))."' AND DATE(order_date)<='".date("Y-m-d",strtotime($date_filter_query_ex['1']))."'  ) ";
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
<form action="" name="frm" id="frm" method="post">
	<table id="datatable_1" class="table table-striped table-bordered table-hover">
        <thead>
        	<tr>
		      <th colspan="12" class="center">
		        <h2>Add To Cart Report <?= date("d-m-Y h:i a");?> Printed By : <?=$_SESSION[SITE_SESS.'SESS_NAME'];?></h2>
		      </th>
		    </tr>
            <tr>
                <th>No.</th>
                <th>Order No.</th>
                <th>Company Name</th>
                <th>Customer Name</th>
                <th>Sales Person Name</th>
                <th>Customer Type</th>
                <th style="text-align:right;">Order Amount</th>
				<th>Order Date</th>
				<th>Status</th>
				<!-- <th>Action</th> -->
            </tr>
        </thead>
        <tbody>
        <?php
        if(mysqli_num_rows($ctable_r)>0){
            $count = 0;
            $sales_name='';
            while($ctable_d = mysqli_fetch_array($ctable_r)){
           		
			$subtotal=$db->rp_getValue("order_product_item","SUM(totalprice)","order_id='".$ctable_d['id']."' AND isDelete=0");
			$pro_id=$db->rp_getValue("order_product_item","pro_id","order_id='".$ctable_d['id']."' AND isDelete=0");
			$GST=$db->rp_getValue("product","igst","id='".$pro_id."' AND isDelete=0");
			$gst_amount=($subtotal*$GST)/100;
			$grand_total=$subtotal+$gst_amount;
        ?>
            <tr>
				<td><?php echo ++$count; ?></td>
				<td><!-- <a href="#myModal" target="_blank" data-id="<?php echo  stripslashes($ctable_d['id']); ?>" data-toggle="modal" title="View Customer"> --><span class="text-success"><?php echo stripslashes($ctable_d['order_no']); ?><!-- </a> --></td>
				<td><?php echo $ctable_d['company_name']; ?></td>
				<td><?php echo stripslashes($ctable_d['customer_name']); ?></td>
				<?php
					$sales_name=$db->rp_getValue("sales_executive","name","id='".$ctable_d['sales_id']."'");
				?>
				<td><?php if($sales_name=="")
				{
					echo "--";
				}
				else
				{ 
					echo $sales_name;
				}
				?></td>
				<td><?php 
				if($ctable_d['customer_type']=='1')
				{
					$slug="Super Stockist";
				}
				else if($ctable_d['customer_type']=='2')
				{
					$slug="Distributor";
				}
				else if($ctable_d['customer_type']=='3')
				{
					$slug="Dealer";
				}
				else if($ctable_d['customer_type']=='4')
				{
					$slug="B2B Customer";
				}
				else if($ctable_d['customer_type']=='normal_user')
				{
					$slug="Normal Customer";
				}
				echo stripslashes($slug); ?></td>
				<td align="right"><?php echo stripslashes(CURR.$db->rp_num(round($grand_total))); ?></td>
				<td><?php echo date('d-m-Y',strtotime($ctable_d['order_date'])); ?></td>
               
				<td>Added to Cart</td>
				
             <!-- <td>
			 <?php
			 if($rights['delete_flag']==1)
			 { ?>
					<a class="btn btn-danger btn-sm" onClick="del_conf('<?php echo $ctable_d['id']; ?>');" title="Delete"><i class="fa fa-times"></i></a>
					<?php
			 }
			 ?>
                <div class="btn-group">
					<button aria-expanded="false" data-toggle="dropdown" type="button" class="btn btn-sm green dropdown-toggle"> 
						 More
					</button>
					<ul role="menu" class="dropdown-menu">
						<li>
							<a href="#myModal" data-id="<?php echo  stripslashes($ctable_d['id']); ?>" data-toggle="modal" title="View Customer"><span class="text-success"><i class="fa fa-circle"></i>&nbsp; View Order</span></a>
						
						<?php
						$file_path="customer_orders_viewer.php?order_id=".$ctable_d['id']."";
						 if($rights['update_flag']==1)
						{
							?>
						</li>
						<li>
							<a href="<?php echo $file_path; ?>" title="Download"><span class="text-success"><i class="fa fa-file-pdf-o"></i>&nbsp; Download</span></a>
							
						</li>
						<?php } ?>
					</ul>
				</div>
				
				</td> -->
            </tr>
        <?php
            }
        }
		
        ?>
        </tbody>
    </table>
    <?php require_once 'disconnect.php';  ?>
   