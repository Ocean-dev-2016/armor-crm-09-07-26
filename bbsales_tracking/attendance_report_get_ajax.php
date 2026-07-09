<?php
$page_id=598;$page_slug='attendance_report_page';
/*
 * @author Ravi Patel
 */
include("connect.php");
$ctable     = "attendance";
$ctable1    = "Attendance";

$ctable_where = "isDelete=0";
// Get the total number of rows in the table

if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
    $sales_id = $db->rp_getData("sales_executive","*","name LIKE '%".$_REQUEST['searchName']."%' OR phone LIKE '%".$_REQUEST['searchName']."%'  AND isDelete=0","",0);
    if($sales_id)
    {       
        while($K=mysqli_fetch_assoc($sales_id))
        {
            $USER_IDS[]=$K['id'];
        }
        $USER_IDS=implode(",",$USER_IDS);
        $ctable_where .=" AND sales_id IN (".$USER_IDS.") ";
    }
    else
    {
        $ctable_where .=" AND sales_id IN (0) ";
    }
}

if(isset($_REQUEST['sales_executive']) && $_REQUEST['sales_executive']!="" && $_REQUEST['sales_executive']!="null")
{
    $ctable_where .=" AND sales_id IN (".$_REQUEST['sales_executive'].") ";
}

if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
{
    if($_SESSION[SITE_SESS.'REFERANCE_TYPE']==2) //sales executive and its chain wise order
    { 
        if($rights['personal_flag']==1)
        {
            $check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];
            $ctable_where .= " AND sales_id='".$check_id."' ";
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
                    
                    $ctable_where .= "  AND sales_id IN (".$SALEID1.','.$_SESSION[SITE_SESS.'REFERANCE_ID'].")";  
                }
                else
                {
                   $ctable_where .= "  AND sales_id IN (".$_SESSION[SITE_SESS.'REFERANCE_ID'].")";       
                } 
            } 
        }
    }
}

if(isset($_REQUEST['sales_executive_type']) && $_REQUEST['sales_executive_type']!="" && $_REQUEST['sales_executive_type']!="null")
{
    $sales_id1 = $db->rp_getData("sales_executive","*","type LIKE '%".$_REQUEST['sales_executive_type']."%'  AND isDelete=0","",0);
    if($sales_id1)
    {       
        while($K1=mysqli_fetch_assoc($sales_id1))
        {
            $USER_IDS1[]=$K1['id'];
        }
        $USER_IDS1=implode(",",$USER_IDS1);
        $ctable_where .=" AND sales_id IN (".$USER_IDS1.") ";
    }
    else
    {
        $ctable_where .=" AND sales_id IN (0) ";
    }
}

$in = true;
$out = true;
if(isset($_REQUEST['io']) && $_REQUEST['io']!="" && $_REQUEST['io']!="null")
{
    $in = false;
    $out = false;
    $_REQUEST['io'] = explode(",", $_REQUEST['io']);
    if(in_array("In", $_REQUEST['io']))
    {
        $in = true;
    }
    if(in_array("Out", $_REQUEST['io']))
    {
        $out = true;
    }
}

if(isset($_REQUEST['filter_month']) && $_REQUEST['filter_month']!="")
{
    $ctable_where .= " AND MONTH(date_time)='".$_REQUEST['filter_month']."' ";
}
if(isset($_REQUEST['filter_year']) && $_REQUEST['filter_year']!="")
{
    $ctable_where .= " AND YEAR(date_time)='".$_REQUEST['filter_year']."' ";
}

$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"]) ) ? intval($_REQUEST["show"]) : 100;

$get_total_rows = $db->rp_getTotalRecord($ctable,$ctable_where,0); //hold total records in variable
//break records into pages
$total_pages = ceil($get_total_rows/$item_per_page);

//get starting position to fetch the records
$page_position = (($page_number-1) * $item_per_page);

$ctable_r = $db->rp_getData($ctable,"id,sales_id,date_time",$ctable_where." GROUP BY sales_id","date_time DESC",0);
// echo $ctable_r;exit();
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
th:nth-child(1),
td:nth-child(1) {
    position: sticky;
    left: 0;
    width: 10px;
    min-width: 10px;
}

th:nth-child(2),
td:nth-child(2) {
    position: sticky;
    /* 1st cell left/right padding + 1st cell width + 1st cell left/right border width */
    /* 0 + 5 + 150 + 5 + 1 */
    left: 40px;
    width: 10px;
    min-width: 10px;
}

td:nth-child(1),
td:nth-child(2) {
    /*background: #ffebb5;*/
}

th:nth-child(1),
th:nth-child(2) {
    z-index: 2;
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
<form action="" id="print_info" name="frm" method="post">
   
    <table class="table table-striped table-bordered table-hover" id="datatable_1" style="min-height: 300px!important">
        <thead class="fix-th">
            <tr>
                <td colspan="33" style="text-align: center;"><h3><b>ATTENDANCE REPORT 
                <?php 
                if(isset($_REQUEST["filter_month"]))
                {
                    $monthNum = sprintf("%02s", $_REQUEST["filter_month"]);
                    $monthName = date("F", mktime(0, 0, 0, $monthNum, 10));
                    echo "-".$monthName ." - ".$_REQUEST["filter_year"];
                }
                else
                {
                    $date=date("m");
                    $monthNum = sprintf("%2s", $date);
                    $monthName = date("F", mktime(0, 0, 0, $monthNum, 10));
                    echo "-".$monthName." - ".$_REQUEST["filter_year"];
                }

                $lastdate = $_REQUEST["filter_year"]."-".$_REQUEST["filter_month"]."-23";
                $lastdate = date("t", strtotime($lastdate));

                ?></b></h3></td>
            </tr>
            <tr>
                <th class="fix-th1" style="background-color:#f5f0f0;">SR <br/> No</th>
                <th class="fix-th1" style="background-color:#f5f0f0;">Sales Person Name</th>
                <?php for($i=1;$i<=$lastdate;$i++)
                {
                    ?>
                <th class="fix-th1" style="text-align: center;">
                    <?php echo $i; ?></th>  
                    <?php
                }   
                ?>          
            </tr>
        </thead>
        <tbody>
            <?php
            if($ctable_r)
            {
                $count=0;
                while($ctable_d=mysqli_fetch_assoc($ctable_r))
                {
                    $R=$db->rp_getData("attendance","*","sales_id='".$ctable_d['sales_id']."' AND isDelete=0 AND MONTH(date_time)='".$_REQUEST["filter_month"]."' AND YEAR(date_time)='".$_REQUEST["filter_year"]."' AND  inout_status='In'","date_time ASC");

                    if($R)
                    {
                        $items=array();         
                        $IN_ITEM=array();         
                        while($D=mysqli_fetch_assoc($R))
                        {       
                            $items[date("j",strtotime($D['date_time']))]=$D;
                            $A['date']=date("j",strtotime($D['date_time']));
                            $A['time']=date("h:i a",strtotime($D['date_time']));
                            $A['id']=$D['id'];
                            $IN_ITEM[]=$A;
                        }
                        ?>
                        <tr>
                            <td style="background-color:#f5f0f0;"><?php echo ++$count; ?></td>
                            <td style="background-color:#f5f0f0;"><?php 
                            $sales_name=$db->rp_getValue("sales_executive","name","id='".$ctable_d['sales_id']."'",0);
                            echo $sales_name; ?>
                            </td>
                        <?php 
                        for($i=1;$i<=$lastdate;$i++)
                        {
                            $IN=array();
                            if(array_key_exists($i,$items))
                            {       
                                for($j=0;$j<sizeof($IN_ITEM);$j++)
                                {
                                    if($i==$IN_ITEM[$j]['date'])
                                    {
                                        $IN[]=$IN_ITEM[$j]['id'];
                                    }
                                }

                                echo "<td class='display' style='min-width:130px!important'>";
                                for($kk=0;$kk<sizeof($IN);$kk++)
                                {   
                                    $in_address=$db->rp_getValue("attendance","app_address","id='".$IN[$kk]."'",0);
                                    $in_time=$db->rp_getValue("attendance","date_time","id='".$IN[$kk]."'",0);
                                    $in_time=date("h:i a",strtotime($in_time));
                                    $dd=$IN[$kk]+1;
                                    
                                    $out_address=$db->rp_getValue("attendance","app_address","inout_status='Out' AND id>'".$IN[$kk]."' AND id<'".$IN[$kk+1]."' AND sales_id='".$ctable_d['sales_id']."' AND isDelete=0 AND MONTH(date_time)='".$_REQUEST['filter_month']."' AND YEAR(date_time)='".$_REQUEST['filter_year']."'",0);
                                    
                                    $dt=$db->rp_getValue("attendance","date_time","inout_status='Out' AND id>'".$IN[$kk]."' AND sales_id='".$ctable_d['sales_id']."' AND isDelete=0 AND MONTH(date_time)='".$_REQUEST['filter_month']."' AND YEAR(date_time)='".$_REQUEST['filter_year']."'",0);
                                    
                                    $outid=$db->rp_getValue("attendance","id","inout_status='Out' AND id>'".$IN[$kk]."' AND sales_id='".$ctable_d['sales_id']."' AND isDelete=0 AND MONTH(date_time)='".$_REQUEST['filter_month']."' AND YEAR(date_time)='".$_REQUEST['filter_year']."'",0);
                                    if($dt!="")
                                    {
                                        $out_time=date("h:i a",strtotime($dt));
                                    }
                                    else
                                    {
                                        $out_time = "--";
                                    }
                                    
                                    $inout = "";
                                    if($in)
                                    {
                                        $inout .= "<a href='#InTimeData' data-id=".$IN[$kk]." data-toggle='modal'><b style='color:green;'> ".$in_time."</a></b> ";
                                        $inout .= ($out)?"":"<br/>";
                                    }

                                    
                                    if($out)
                                    {
                                        $inout .= ($in)?" | ":"";
                                        $inout .= ($out_time!="--")?" <a href='#OutTimeData' data-outid=".$outid." data-toggle='modal'><b style='color:red;'>".$out_time."</a> <br/>":" | --<br/>";
                                    }

                                    $inout ."</b>";
                                    echo $inout;
                                }
                                echo "</td>";
                            }
                            else
                            {
                                echo "<td class='display'>-</td>";
                            }
                        }
                    }
                }
            }
            ?>
        </tr>
        </tbody>
    </table>
</form>
<?php include("disconnect.php"); ?>