<?php
$PageConfig = array("id" => 598, "navigation" => false);//569
include("connecting.php");
$ctable 	= "attendance";
$ctable1 	= "Attendance";
$sales_id=$_REQUEST['sales_id'];
$ctable_where = "";
// Get the total number of rows in the table
//print_r($_REQUEST);exit();

$in = true;
$out = true;

if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
	$sales_id = $db->rp_getData("sales_executive","*","name LIKE '%".$_REQUEST['searchName']."%' OR phone LIKE '%".$_REQUEST['searchName']."%'  AND isDelete=0","",0);

    if($sales_id)
    {       
        while($K=mysqli_fetch_assoc($sales_id))
        {
            $USER_IDS[]=$K['id'];
        }
        $USER_IDS=implode(",",$USER_IDS);
        $ctable_where .="sales_id IN (".$USER_IDS.") AND ";
    }
    else
    {
        $ctable_where .="sales_id IN (0) AND ";
    }
}
if(isset($_REQUEST['se_id']) && $_REQUEST['se_id']!="" && $_REQUEST['se_id']!="null")
{
    $ctable_where .=" sales_id='".$_REQUEST['se_id']."' AND ";
}

// echo $_REQUEST['io'];exit();

if(isset($_REQUEST['io']) && $_REQUEST['io']!="" && $_REQUEST['io']!="null")
{
    $ctable_where .=" inout_status='".$_REQUEST['io']."' AND ";
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
 

if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
{
	if($rights['all_data_flag']==1)
	{
		
	}
	else
	{
	    $sales_type_r=$db->rp_getData("sales_executive","*","id='".$_SESSION[SITE_SESS.'REFERANCE_ID']."'","",0);
	    $sales_type_d = mysqli_fetch_array($sales_type_r);
	    $SALESTYPE = array('1' => "sales_manager",'2' => "area_sales_manager",'3' => "terretory_manager",'4' => "sales_officer",'5' => "sales_executive",'6' => "back_office", );

	    $sales_type['sales_type'] = explode(",", $sales_type_d['sales_type']);
	    $S_Type = array();
	    foreach ($sales_type['sales_type'] as $key => $value) {
	        $S_Type[] = $SALESTYPE[$value];
	    }
	    $sales_type['sales_type'] = implode("','", $S_Type);
	    $Se_IDS_r=$db->rp_getData("sales_executive","*","type IN ('".$sales_type['sales_type']."') ","",0);
    	$SE_IDS = array();
		while($Se_IDS_d = mysqli_fetch_array($Se_IDS_r))
		{
			$SE_IDS[] = $Se_IDS_d['id'];
		}
		$se_ids= implode(",", $SE_IDS);
		$ctable_where .="  sales_id IN (".$se_ids.") AND ";
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

if(isset($_REQUEST['month_id']) && $_REQUEST['month_id']!="" && $_REQUEST['df1']=="")
{
	$ctable_where .= " isDelete=0  AND MONTH(date_time)='".$_REQUEST['month_id']."'";
}

if(isset($_REQUEST['filter_year']) && $_REQUEST['filter_year']!="")
{
    $ctable_where .= " AND YEAR(date_time)='".$_REQUEST['filter_year']."' ";
}

$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"]) ) ? intval($_REQUEST["show"]) : 100;

if(isset($_REQUEST["page"])){
	$page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
	if(!is_numeric($page_number)){die('Invalid page number!');} //incase of invalid page number
}else{
	$page_number = 1; //if there's no page number, set it to 1
}

// print_r($_REQUEST["sales_executive"]);exit;
if(isset($_REQUEST["sales_executive"]) && $_REQUEST["sales_executive"]!="" && $_REQUEST["sales_executive"]!=undefined && $_REQUEST['sales_executive']!="null"){
	$ctable_where .= " AND sales_id='".$_REQUEST["sales_executive"]."'";
	$sid = $_REQUEST["sales_executive"];
}

$get_total_rows = $db->rp_getTotalRecord($ctable,$ctable_where); //hold total records in variable


$ctable_r = $db->rp_getData($ctable,"id,sales_id,date_time",$ctable_where." GROUP BY sales_id","date_time ASC",0);
?>
<!DOCTYPE html>
<html>
<head>
	<style type="text/css">
		.thumb img { 
			border:1px solid #000;
			margin:3px;
			float:left;
		}
		.thumb span { 
			position:absolute;
			visibility:hidden;
			/*visibility:visible;*/
			right: 30%;
			margin-top: -60px;
			background: #fff;
		}
		.thumb:hover, .thumb:hover span { 
			visibility:visible; 
			z-index:9999;
		}
		#myImg {
		  border-radius: 5px;
		  cursor: pointer;
		  transition: 0.3s;
		}

		#myImg:hover {opacity: 0.7;}

		/* The Modal (background) */
		.modal {
		  display: none; /* Hidden by default */
		  position: fixed; /* Stay in place */
		  z-index: 1; /* Sit on top */
		  padding-top: 100px; /* Location of the box */
		  left: 0;
		  top: 0;
		  width: 100%; /* Full width */
		  height: 100%; /* Full height */
		  overflow: auto; /* Enable scroll if needed */
		  background-color: rgb(0,0,0); /* Fallback color */
		  background-color: rgba(0,0,0,0.9); /* Black w/ opacity */
		}

		/* Modal Content (image) */
		.modal-content {
		  margin: auto;
		  display: block;
		  width: 80%;
		  max-width: 700px;
		}

		/* Caption of Modal Image */
		#caption {
		  margin: auto;
		  display: block;
		  width: 80%;
		  max-width: 700px;
		  text-align: center;
		  color: #ccc;
		  padding: 10px 0;
		  height: 150px;
		}

		/* Add Animation */
		.modal-content, #caption {  
		  -webkit-animation-name: zoom;
		  -webkit-animation-duration: 0.6s;
		  animation-name: zoom;
		  animation-duration: 0.6s;
		}

		@-webkit-keyframes zoom {
		  from {-webkit-transform:scale(0)} 
		  to {-webkit-transform:scale(1)}
		}

		@keyframes zoom {
		  from {transform:scale(0)} 
		  to {transform:scale(1)}
		}

		/* The Close1 Button */
		.close1 {
		  position: absolute;
		  top: 15px;
		  right: 35px;
		  color: #f1f1f1;
		  font-size: 40px;
		  font-weight: bold;
		  transition: 0.3s;
		}

		.close1:hover,
		.close1:focus {
		  color: #bbb;
		  text-decoration: none;
		  cursor: pointer;
		}

		/* 100% Image Width on Smaller Screens */
		@media only screen and (max-width: 700px){
		  .modal-content {
		    width: 100%;
		  }
		}
		</style>
</head>
<body>
	<table class="table table-striped table-bordered table-hover" id="datatable_1">
        <thead>
			<tr>
                <td colspan="33" style="text-align: center;"><h3><b>ATTENDANCE REPORT 
                <?php 
                if(isset($_REQUEST["month_id"]))
                {
                    $monthNum = sprintf("%02s", $_REQUEST["month_id"]);
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

                $lastdate = $_REQUEST["filter_year"]."-".$_REQUEST["month_id"]."-23";
                $lastdate = date("t", strtotime($lastdate));

                ?></b></h3></td>
            </tr>
			<tr>
                <th style="background-color:#f5f0f0;">Sales Person Name</th>
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
                    $R=$db->rp_getData("attendance","*","sales_id='".$ctable_d['sales_id']."' AND isDelete=0 AND MONTH(date_time)='".$_REQUEST["month_id"]."' AND YEAR(date_time)='".$_REQUEST["filter_year"]."' AND  inout_status='In'","date_time ASC",0);

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
                                    
                                    $out_address=$db->rp_getValue("attendance","app_address","inout_status='Out' AND id>'".$IN[$kk]."' AND id<'".$IN[$kk+1]."' AND sales_id='".$ctable_d['sales_id']."' AND isDelete=0 AND MONTH(date_time)='".$_REQUEST['month_id']."' AND YEAR(date_time)='".$_REQUEST['filter_year']."'",0);
                                    
                                    $dt=$db->rp_getValue("attendance","date_time","inout_status='Out' AND id>'".$IN[$kk]."' AND sales_id='".$ctable_d['sales_id']."' AND isDelete=0 AND MONTH(date_time)='".$_REQUEST['month_id']."' AND YEAR(date_time)='".$_REQUEST['filter_year']."'",0);

                                    $outid=$db->rp_getValue("attendance","id","inout_status='Out' AND id>'".$IN[$kk]."' AND sales_id='".$ctable_d['sales_id']."' AND isDelete=0 AND MONTH(date_time)='".$_REQUEST['month_id']."' AND YEAR(date_time)='".$_REQUEST['filter_year']."'",0);

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





