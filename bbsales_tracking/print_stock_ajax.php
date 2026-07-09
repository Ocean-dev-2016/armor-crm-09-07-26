  <?php
$page_id=640;$page_slug='product_stock_page';
include("connect_in.php");
include('../include/product.class.php');
$product=new Product();
$ctable     = "product";
 
$ctable_where = "";
 
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
    $isFillter=true;
}

if(isset($_REQUEST['item_name']) && $_REQUEST['item_name']!="")
{
    $ctable_where .= " (name like '%".$db->clean($_REQUEST['item_name'])."%') AND ";
    $isFillter=true;
}

if(isset($_REQUEST['category_id']) && $_REQUEST['category_id']!="" && $_REQUEST['category_id']!=NULL && $_REQUEST['category_id']!=undefined)
{
    if ($_REQUEST['category_id'] != '-1') 
    {
        $ctable_where .= " tcid='".$_REQUEST['category_id']."' AND ";
    }
    else
    {
        $top_category_id = $_REQUEST['top_category_id'];
    }
    $isFillter=true;
}
if(isset($_REQUEST['sub_category_id']) && $_REQUEST['sub_category_id']!="" && $_REQUEST['sub_category_id']!=NULL && $_REQUEST['sub_category_id']!=undefined)
{
    
    if ($_REQUEST['category_id'] != '-2') 
    {
        $ctable_where .= " cid='".$_REQUEST['sub_category_id']."' AND ";
    }
    else
    {
        $sub_category_id = $_REQUEST['sub_category_id'];
    }
    $isFillter=true;
}
 
$ctable_where .= " isDelete=0 ";
 $warehouse_id=$_REQUEST['warehouse_id'];
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
<table id="datatable_attandence" class="table table-striped table-bordered table-hover">
    <thead>
        <tr>
            <tr>
                <th class="fix-th1" style="width: 5%;">No</th>
                <th class="fix-th1">Category</th>
                <th class="fix-th1">Sub Category</th>
                <th class="fix-th1" colspan="3">Item Name</th>
                <th class="fix-th1" colspan="3">Item Code</th> 
                <th class="fix-th1">Availbale Stock</th>
                <th class="fix-th1">Manual Stock</th>
                <th class="fix-th1">Dispatch Qty</th> 
                <th class="fix-th1">Min Stock Qty</th>
                <th class="fix-th1">Max Stock Qty</th>
                <th class="fix-th1">Price (Rs.)</th>
                 <th class="fix-th1">Amount(Rs.)</th>  
            </tr>
    </thead>
    
    <tbody>
        <?php
        if(mysqli_num_rows($ctable_r)>0)
        {
            $count = 0;
            $actual_Stock_count = 0;
            while($ctable_d = mysqli_fetch_assoc($ctable_r))
            { 
                $current_prodcuts=$product->aj_getProductDetail($ctable_d['id'],$uid);      
                if(!empty($current_prodcuts))
                {
                    foreach($current_prodcuts as $product_detail)
                    {  
                        $inward_where="warehouse_id='".$warehouse_id."' AND isDelete=0 AND pro_id='".$ctable_d['id']."' AND weight_id='".$product_detail['weight_id']."' AND reference_table!='dispatch_detail'";
                         
                        $disR=$db->rp_getData("dispatch_detail","id","warehouse_id='".$warehouse_id."' AND isDelete=0","",0);
                        $dispatchIds=array();
                        if($disR)
                        {
                            while($disD=mysqli_fetch_assoc($disR))
                            {
                                $dispatchIds[]=$disD['id'];
                            }
                        }
                         
                        $dispatchIds=implode(",", $dispatchIds); 
                        $dispatch_qty1 = $db->rp_getValue("dispatch_item","SUM(qty)","pro_id='".$ctable_d['id']."' AND weight_id='".$product_detail['weight_id']."' AND dispatch_id IN(".$dispatchIds.") AND isDelete=0",0);

                        $manual_stock_qty = $db->rp_getValue("inward_stock","SUM(pro_qty)",$inward_where,0);

                        $get_available_stock=$db->get_available_stock($ctable_d['id'],$product_detail['weight_id'],$warehouse_id);
                        $actual_Stock=$get_available_stock;

                        $min_Stock = $db->rp_getValue("product_weight_price","min_stock_qty","isDelete=0 AND product_id='".$ctable_d['id']."' AND weight_id='".$product_detail['weight_id']."'",0);

                        $max_Stock = $db->rp_getValue("product_weight_price","max_stock_qty","isDelete=0 AND product_id='".$ctable_d['id']."' AND weight_id='".$product_detail['weight_id']."'",0);   

                        
                        if($actual_Stock>$min_Stock AND $actual_Stock<$max_Stock)
                        {
                            // echo "GG";exit();
                            $color1 ='#FFFFFF';
                        } 
                        if($actual_Stock < $min_Stock)
                        {
                            $color1='#f23b38';
                        }
                        if($actual_Stock > $max_Stock)
                        {
                            $color1='#09d917';
                        }
                        
                        if(!empty($orderids))
                        { 
                            // $dispatch_qty = $db->rp_getValue("dispatch_qty","SUM(qty)","isDelete=0 AND pro_id='".$ctable_d['id']."' AND weight_id='".$product_detail['weight_id']."'",0);

                            if($reorder_point>$actual_Stock)
                            {
                                $color = '#D17272';
                            }
                            else
                            {
                                $color = '#fff';
                            } 
                        }
        ?>            
        <tr style="background-color: <?= $color; ?>">
            <td><?php echo ++$count; ?></td>
            <td><?php echo $db->rp_getValue("top_category_master","name","id='".$ctable_d['tcid']."'",0);?></td>
            <td><?php echo $db->rp_getValue("category_master","name","id='".$ctable_d['cid']."'",0);?></td>
            <?php
            if($product_detail['title']=="")
            {
            ?>
            <td colspan="3"><?php echo $ctable_d['name']; ?> </td>
            <?php
            }
            else
            {
            ?>
            <td colspan="3"><?php echo $ctable_d['name']; ?> </td>
            <!-- <td><?php echo $ctable_d['name']." (".$product_detail['title'].")"; ?> </td> -->
            <?php
            }
            ?>
            <td colspan="3"><?php echo $product_detail['catno']; ?></td> 
            <td style="background-color: <?= $color1; ?>"><?php echo $actual_Stock; ?></td>
            <td><?php echo $manual_stock_qty; ?></td>
            <td><?php echo $dispatch_qty1; ?></td> 
            <td><?php echo $min_Stock;?></td>
            <td><?php echo $max_Stock;?></td>
            <td><?php echo $product_detail['price']; ?></td>
            <td><?php echo $product_detail['price']*$actual_Stock?></td>  
        </tr>
        <?php
                    $actual_Stock_count += $actual_Stock;
                    $final_price_count += $product_detail['price'];
                    $total_price_of_available_stock +=  $actual_Stock*$product_detail['price'];

                    }
                }
            }
        }
        else
        {
        ?>
        <tr>
            <td align="center" colspan="7"><?php echo "No Data Found";?></td>
        </tr>
        <?php
        }
        ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="9" align="right"><b>Total</b></td>
            <td align="right"><b><?php echo $actual_Stock_count; ?></b></td>
            <td></td>
            <td></td> 
            <td></td>
            <td></td>
            <td><b><?php echo $final_price_count; ?></b></td>
            <td></td> 
        </tr>
        <tr>
            <td colspan="15" align="right"><b>Total Price of Available Stock </b></td> 
            <td align="right"><b><?php echo ($total_price_of_available_stock); ?></b></td> 
        </tr>
    </tfoot>
</table>
<?php require_once "disconnect.php"; ?>
