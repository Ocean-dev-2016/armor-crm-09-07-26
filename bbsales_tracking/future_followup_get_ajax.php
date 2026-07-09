<?php
$page_id=583;$page_slug='future_followup_manage';
include("connect.php");
$ctable 	= "followup";
$ctable1 	= "visitor";
$ctable_where = "";
// Get the total number of rows in the table

if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!="")
{
    $phone_id = array();
    $exe_ids_r = $db->rp_getData("executive","*","phone LIKE '%".trim($_REQUEST['searchName'])."%' ","",0);
    if ($exe_ids_r)
    {
        while($exe_id_d = mysqli_fetch_assoc($exe_ids_r))
        {
          $phone_id[] = $exe_id_d['id'];
        }

        $phone_no_id = implode(",", $phone_id);

        $ctable_where.="visitor_id IN (".$phone_no_id.") AND "; 
    }
    else
    {
        $ctable_where.="phone IN ('') AND ";
    }
}



// {
//     $ctable_where .= " ( title like '%".$db->clean($_REQUEST['searchName'])."%' OR response like '%".$db->clean($_REQUEST['searchName'])."%' OR description like '%".$db->clean($_REQUEST['searchName'])."%'  ) ";


//     $executive_no = array();
//       $executive_name = $db->rp_getData("executive","id","phone LIKE '%".trim($_REQUEST['searchName'])."%' ","",0);
//       if ($executive_name)
//       {
//         while($executive_name_d = mysqli_fetch_assoc($executive_name))
//         {
//           $executive_no[] = $executive_name_d['id'];
//         }
//         $executive_no = implode(",", $executive_no);
//         $ctable_where.=" OR visitor_id IN (".$executive_no.") AND "; 
//       }
//       else
//       {
//         $ctable_where.=" OR visitor_id IN ('') AND ";
//       }


// }


if(isset($_REQUEST['sales_executive']) && $_REQUEST['sales_executive']!="" && $_REQUEST['sales_executive']!=NULL)
{
    $ctable_where .=" user_id=".$_REQUEST['sales_executive']." AND ";
}

$future = date('Y-m-d');
if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
{

   $ctable_where .= "isDelete=0 AND user_id='".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID']."' AND DATE(followup_date) >= '".$future."'";
}
else{
     $ctable_where .= "isDelete=0 AND DATE(followup_date) >= '".$future."'";
}


if(isset($_REQUEST['reference_media_id']) && $_REQUEST['reference_media_id']!="")
{
    $ctable_where .=" AND refrence_media_id=".$_REQUEST['reference_media_id'];
}

if(isset($_REQUEST['executive']) && $_REQUEST['executive']!="" && $_REQUEST['executive']!=NULL)
{
    $ctable_where .= " AND visitor_id= '".$_REQUEST['executive']."' ";
}


$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"]) ) ? intval($_REQUEST["show"]) : 100;
if(isset($_REQUEST["page"])){
	$page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
	if(!is_numeric($page_number)){die('Invalid page number!');} //incase of invalid page number
}else{
	$page_number = 1; //if there's no page number, set it to 1
}

if(isset($_REQUEST['ToDate']) && $_REQUEST['ToDate']!="")
{
    $FilterDate=$_REQUEST['ToDate'];
    $FilterDate=explode("to",$FilterDate);
    //echo sizeof($FilterDate);
    if(sizeof($FilterDate)==2)
    {
        $ctable_where .= " AND (
            DATE(followup_date)>='".date("Y-m-d",strtotime($FilterDate[0]))."' AND (DATE(followup_date)<='".date("Y-m-d",strtotime($FilterDate[1]))."'))";
    }
}


$get_total_rows = $db->rp_getTotalRecord($ctable,$ctable_where); //hold total records in variable
//break records into pages
$total_pages = ceil($get_total_rows/$item_per_page);

//get starting position to fetch the records
$page_position = (($page_number-1) * $item_per_page);

$ctable_r = $db->rp_getData($ctable,"*",$ctable_where,"followup_date ASC limit $page_position, $item_per_page",0);
?>

<table id="datatable_1" class="table table-striped table-bordered table-hover table">
        <thead>
            <tr>
                <th style="width: 5%">No.</th>
                <th>Customer Name</th>
                <th>Mobile No</th>
                <th>Sales Officer Name</th>
                <th>Description</th>
                <th>Through</th>
                <th>Date and Time</th>
                <th>Response Date</th>
                <th>Response</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if(mysqli_num_rows($ctable_r)>0){
            $count = 0;
            $msg = array("1"=>"Call","2"=>"Sms",3=>"Email");
            while($ctable_d = mysqli_fetch_array($ctable_r)){
            //$followupdate = date('d-m-Y',strtotime($ctable_d['followup_date']));
            //$responsedate = date('d-m-Y',strtotime($ctable_d['response_date']));
        ?>
            <tr>
                <td><?php echo ++$count; ?></td>	
               <td>
                    <?php
                    if($ctable_d['refrence_table']=="sales_executive")
                    {
                        echo $db->rp_getValue("executive","cname","id='".$ctable_d['visitor_id']."'");
                    }
                    else if($ctable_d['refrence_table']=="no_order_inquiry")
                    {
                        echo $db->rp_getValue("no_order_inquiry","company_name","id='".$ctable_d['inquiry_id']."'");
                    }
                    ?>
                </td>
                <td>
                    <?php 
                    if($ctable_d['refrence_table']=="sales_executive")
                    {
                        echo $db->rp_getValue("executive","phone","id='".$ctable_d['visitor_id']."'");
                    }
                    else if($ctable_d['refrence_table']=="no_order_inquiry")
                    {
                        echo $db->rp_getValue("no_order_inquiry","mobile_number","id='".$ctable_d['inquiry_id']."'");
                    }
                    ?>
                </td>			
                <td><?php echo $db->rp_getValue("sales_executive","name","id='".$ctable_d['user_id']."'");?></td>            
                <td><?php echo $ctable_d['description']; ?></td>            
                <td><?php echo $msg[$ctable_d['through']]; ?></td>
                <!-- followupdate-->
                <?php if($ctable_d['followup_date']=="0000-00-00 00:00:00"){?>
                    <td></td>
                <?php 
                } else{ ?>
                    <td><?php echo date('d-m-Y H:i:s a',strtotime($ctable_d['followup_date'])); ?></td>
                <?php } ?> 
                <!-- followupdate-->

                <!-- responsedate-->
                <?php if($ctable_d['response_date']=="0000-00-00 00:00:00"){?>
                    <td></td>
                <?php 
                } else{ ?>
                    <td><?php echo date('d-m-Y',strtotime($ctable_d['response_date'])); ?></td>
                <?php } ?> 
                 <!-- responsedate-->
                <td><?php echo $ctable_d['response']; ?></td>
             </tr>
        <?php
            }
        }
        ?>
        </tbody>
    </table>
    
    <div class="row">
		<div class="col-md-6">
			<div class="dataTables_info"> Rows Limit:
				<select id="numRecords" onChange="changeDisplayRowCount(this.value);">
					<option value="100" <?php if ($_REQUEST["show"] == 100 || $_REQUEST["show"] == "" ) { echo ' selected="selected"'; }  ?> >100</option>
					<option value="500" <?php if ($_REQUEST["show"] == 500) { echo ' selected="selected"'; }  ?> >500</option>
					<option value="1000" <?php if ($_REQUEST["show"] == 1000) { echo ' selected="selected"'; }  ?> >1000</option>
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
	</div>
<?php require_once("disconnect.php"); ?>