<?php 
$page_id=598;$page_slug='attendance_report_page';
/*
 * @author Ravi Patel
 */
include("connect.php");
$ctable   = "attendance";
$ctable1  = "Attendance";

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

// if(isset($_REQUEST["sales_executive"]) && $_REQUEST["sales_executive"]!="" && $_REQUEST["sales_executive"]!='' && $_REQUEST["sales_executive"]!=undefined){
//   // $_REQUEST['sales_executive'] = implode(",",$_REQUEST['sales_executive']);
//   $ctable_where .= " user_id='".$_REQUEST["sales_executive"]."' AND ";
// }
if(isset($_REQUEST["sales_executive"]) && $_REQUEST["sales_executive"]!="" && $_REQUEST["sales_executive"]!=undefined && $_REQUEST['sales_executive']!="null"){
    $ctable_where .= " AND sales_id='".$_REQUEST["sales_executive"]."' ";
    // $sid = $_REQUEST["sales_executive"];
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

/*if(isset($_REQUEST['io']) && $_REQUEST['io']!="" && $_REQUEST['io']!="null")
{
    $ctable_where .=" AND inout_status='".$_REQUEST['io']."' ";
} */
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

// if(isset($_REQUEST['io']) && $_REQUEST['io']!="")
// {
//     $ctable_where .=" AND inout_status='".implode(" ", $_REQUEST['io'])."' ";
// }

if(isset($_REQUEST['df']) && $_REQUEST['df']!=""){
    //echo $_REQUEST['df'];exit;
    $date_filter_query = urldecode( $_REQUEST['df'] );

    $date_filter_query_ex=explode(" to ",$date_filter_query);

    $ctable_where .= " AND ( DATE(date_time)>='".date("Y-m-d",strtotime($date_filter_query_ex['0']))."' AND DATE(date_time)<='".date("Y-m-d",strtotime($date_filter_query_ex['1']))."'  ) ";
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

$ctable_r = $db->rp_getData("attendance","*",$ctable_where." GROUP BY sales_id","date_time ASC",0);
?>
<!DOCTYPE html>
<html>
<head>
  <title></title>

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
  </style>
</head>
<body>

   <table class="table table-striped table-bordered table-hover" id="datatable_1" style="min-height: 300px!important">
        <thead>
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
                <th>Sales Person Name</th>
                <?php for($i=1;$i<=$lastdate;$i++)
                {
                    ?>
                <th style="text-align: center;">
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
                            <td><?php 
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
        </tbody>
    </table>

</body>
</html>
<?php include("disconnect.php"); ?>
