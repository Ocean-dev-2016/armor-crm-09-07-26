<?php
$page_id=596;$page_slug='expense_report_page';
/*
 * @author Ravi Patel
 */
include("connect.php");
$ctable     = "expense";
$ctable1    = "Expense";

$ctable_where = "";
// Get the total number of rows in the table

if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
    $sales_id = $db->rp_getData("sales_executive","id","name LIKE '%".$_REQUEST['searchName']."%'  AND isDelete=0","",0);
    if($sales_id){
        $SALESID = array();
        while ($sales_d=mysqli_fetch_assoc($sales_id)) {
            $SALESID[]=$sales_d['id'];
            # code...
        }
        $SALESID = implode(",", $SALESID);
        $ctable_where .=" sales_executive_id IN (".$SALESID.") AND ";
        // $ctable_where .="(sales_executive_id IN (".$SALESID.") OR ";
    }
    else
    {
        $ctable_where .=" sales_executive_id IN (0) AND ";
    }

    // $cat_id = $db->rp_getData("expence_category","id","name LIKE '%".$_REQUEST['searchName']."%'  AND isDelete=0","",0);
    // if($cat_id){
    //     $CATID = array();
    //     while ($cat_d=mysqli_fetch_assoc($cat_id)) {
    //         $CATID[]=$cat_d['id'];
    //         # code...
    //     }
    //     $CATID = implode(",", $CATID);
    //     $ctable_where .="category_id IN (".$CATID.")) AND ";
    // }
    // else
    // {
    //     $ctable_where .="category_id IN (0)) AND ";
    // }
}
if(isset($_REQUEST['sales_executive_id']) && $_REQUEST['sales_executive_id']!="" && $_REQUEST['sales_executive_id']!=NULL && $_REQUEST['sales_executive_id']!="null" )
{
    $ctable_where .=" sales_executive_id='".$_REQUEST['sales_executive_id']."' AND ";
}
if(isset($_REQUEST['c_id']) && $_REQUEST['c_id']!="" && $_REQUEST['c_id']!=NULL && $_REQUEST['c_id']!="null")
{
    $ctable_where .=" category_id='".$_REQUEST['c_id']."' AND ";
}
if(isset($_REQUEST['expense_status']) && $_REQUEST['expense_status']!="" && $_REQUEST['expense_status']!=NULL && $_REQUEST['expense_status']!="null")
{
    $ctable_where .=" expense_status='".$_REQUEST['expense_status']."' AND ";
}


$ctable_where .= " isDelete=0";

$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"]) ) ? intval($_REQUEST["show"]) : 100;

if(isset($_REQUEST["page"])){
    $page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
    if(!is_numeric($page_number)){die('Invalid page number!');} //incase of invalid page number
}else{
    $page_number = 1; //if there's no page number, set it to 1
}


if(isset($_REQUEST['ToDate']) && $_REQUEST['ToDate']!="" && $_REQUEST['ToDate']!=NULL)
{
    $ctable_where .= " AND expense_date  <= '".date_format(date_create($_REQUEST['ToDate']),"Y-m-d")."' ";
}

if(isset($_REQUEST['FromDate']) && $_REQUEST['FromDate']!="" && $_REQUEST['FromDate']!=NULL)
{
    $ctable_where .= " AND expense_date >= '".date_format(date_create($_REQUEST['FromDate']),"Y-m-d")."' ";
}
if(isset($_REQUEST['df']) && $_REQUEST['df']!=""){
    //echo $_REQUEST['df'];exit;
    $date_filter_query = urldecode( $_REQUEST['df'] );

    $date_filter_query_ex=explode(" to ",$date_filter_query);

    $ctable_where .= " AND ( DATE(created_date)>='".date("Y-m-d",strtotime($date_filter_query_ex['0']))."' AND DATE(created_date)<='".date("Y-m-d",strtotime($date_filter_query_ex['1']))."')";
}
$get_total_rows = $db->rp_getTotalRecord($ctable,$ctable_where,0); //hold total records in variable
//break records into pages
$total_pages = ceil($get_total_rows/$item_per_page);

//get starting position to fetch the records
$page_position = (($page_number-1) * $item_per_page);

$ctable_r = $db->rp_getData($ctable,"*",$ctable_where,"id DESC",0);
$imgExts = array("gif", "jpg", "jpeg", "png", "tiff", "tif");
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
<!-- <button type="button" class="btn red-haze export" name="export" onClick="genReport(this)" id="export" href="" title="Download PDF Report"><i class="fa fa-file-pdf-o"></i>&nbsp; PDF</button>
<button type="button" class="btn green-haze export" name="export" onClick="genReportexcel(this)" id="export" href="" title="Download Excel Report"><i class="fa fa-file-excel-o"></i> &nbsp; Excel</button> -->
<form action="" name="print_info1" id="print_info1" method="post">
    <table id="datatable_1" class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th colspan="12" class="center"><h2>Expense Report <?= date("d-m-Y h:i a");?> Printed By : <?=$_SESSION[SITE_SESS.'SESS_NAME'];?></h2></th>
            </tr>
            <tr>
                <th>No.</th>
                <th>Sales Person Name</th>
                <th>Category Name</th> 
                <th>Sub Category Name</th> 
                <th>Start Km</th>
                <th>End Km</th>
                <th>Total Km</th>               
                <th>Request Amount</th>
                <th>Passed Amount</th>
                <th>Expense Status</th>
                <th>Created Date</th>
                <th>Remark</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if(mysqli_num_rows($ctable_r)>0){
            $count = 0;
            while($ctable_d = mysqli_fetch_array($ctable_r)){
            //print_r($ctable_d);
        ?>
            <tr>
                <td><?php echo ++$count; ?></td>
                <td><?php echo $db->rp_getValue("sales_executive","name","id='".$ctable_d['sales_executive_id']."'") ?></td>
                <td><?php echo $db->rp_getValue("expence_category","name","id='".$ctable_d['category_id']."'",0); ?></td>     
                 <td><?php echo $db->rp_getValue("expence_sub_category","name","id='".$ctable_d['subcategory_id']."'",0); ?></td>             
                <td><?php echo $ctable_d['start_kilometer']; ?></td>
                <td><?php echo $ctable_d['end_kilometer']; ?></td>
                <td><?php echo ($ctable_d['total_kilometer'])?$ctable_d['total_kilometer']:""; ?></td>
                <td><?php echo $db->rp_num($ctable_d['total'],2) ?></td>
                <td><?php echo $db->rp_num($ctable_d['pass_expense_amount'],2) ?></td>
                <td>
                    <?php if($ctable_d['expense_status']==0){echo "Pending"; }else if($ctable_d['expense_status']==1){echo "Passed"; } else if($ctable_d['expense_status']==2){echo "Rejected"; }?>
                </td>
                <!-- <td>
                    <?php 
                        $img = explode(",", $ctable_d['image_path']);
                        $imgpath = array();
                        for ($i=0; $i < sizeof($img); $i++)
                        { 
                            $imgpath[] = SITEURL."resource/image/".$db->rp_getValue("media","url","reference_id='".$ctable_d["id"]."' AND id='".$img[$i]."'",0);
                        }
                        // print_r($imgpath);exit;
                            for ($i=0; $i < sizeof($imgpath); $i++)
                            {
                                $urlExt = pathinfo($imgpath[$i], PATHINFO_EXTENSION);
                                if (in_array($urlExt, $imgExts)) {
                                    
                        if($i==0){
                    ?>
                    <a href="<?=$imgpath[$i]?>" data-lightbox="EXPENSE<?=$count?>" data-title="EXPENSE <?=$ctable_d['id']?>"><img src="<?=$imgpath[$i]?>" style="height: 80px;"></a>
                    <?php }else{
                        ?>
                            <div class="hidden">
                                <a href="<?=$imgpath[$i]?>" data-lightbox="EXPENSE<?=$count?>" data-title="EXPENSE <?=$ctable_d['id']?>"><img src="<?=$imgpath[$i]?>" style="height: 80px;"></a>
                            </div>
                        <?php
                            }
                        }
                        }
                    ?>
                </td>  -->
                <td><?php echo date("d-m-Y",strtotime($ctable_d['created_date'])); ?></td>
                <td><?php echo $ctable_d['remark']; ?></td>
            </tr>
        <?php
            }
        }
        ?>
        </tbody>
    </table>
</form>
<?php include("disconnect.php"); ?>