<?php
$page_id=608;$page_slug='inquiry_cancel_report';
include("connect_in.php");
$ctable 	= "no_order_inquiry";
$ctable1 	= "Inquiry";
$ctable_where = "";
// Get the total number of rows in the table
if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
	
	$ctable_where .="company_name like '%".$_REQUEST['searchName']."%' OR mobile_number like '%".$_REQUEST['searchName']."%' OR id like '%".$_REQUEST['searchName']."%' OR person_name like '%".$_REQUEST['searchName']."%' AND ";
}
if(isset($_REQUEST['status_id']) && $_REQUEST['status_id']!=""  && $_REQUEST['status_id']!="null")
{
	$ctable_where .= " status='".$_REQUEST['status_id']."' AND ";
	$status_id=$_REQUEST['status_id'];
}

if(isset($_REQUEST['state']) && $_REQUEST['state']!="" && $_REQUEST['state']!=NULL && $_REQUEST['state']!="null")
{
	$ctable_where .= " state = '".$_REQUEST['state']."' AND ";
}

if(isset($_REQUEST['city']) && $_REQUEST['city']!="" && $_REQUEST['city']!=NULL && $_REQUEST['city']!="null")
{
	$ctable_where .= " city = '".$_REQUEST['city']."' AND ";
}

if(isset($_REQUEST['df1']) && $_REQUEST['df1']!=""){
	//echo $_REQUEST['df1'];exit;
	$date_filter_query = urldecode( $_REQUEST['df1'] );

	$date_filter_query_ex=explode(" to ",$date_filter_query);

	$ctable_where .= " ( DATE(datetime)>='".date("Y-m-d",strtotime($date_filter_query_ex['0']))."' AND DATE(datetime)<='".date("Y-m-d",strtotime($date_filter_query_ex['1']))."'  ) AND";
}

$ctable_where .= " isDelete=0 AND status= -2";

$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"]) ) ? intval($_REQUEST["show"]) : 100;

if(isset($_REQUEST["page"])){
	$page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
	if(!is_numeric($page_number)){die('Invalid page number!');} //incase of invalid page number
}else{
	$page_number = 1; //if there's no page number, set it to 1
}

$get_total_rows = $db->rp_getTotalRecord($ctable,$ctable_where); //hold total records in variable

if(isset($_REQUEST['type']) && $_REQUEST['type']!="" && $_REQUEST['type']!=NULL  && $_REQUEST['type']!="null")
{
 $ctable_where .= " AND sales_executive_id = '".$_REQUEST['type']."' ";
}

	
$get_total_rows = $db->rp_getTotalRecord($ctable,$ctable_where,0); //hold total records in variable

//break records into pages
$total_pages = ceil($get_total_rows/$item_per_page);

//get starting position to fetch the records
$page_position = (($page_number-1) * $item_per_page);

$ctable_r = $db->rp_getData($ctable,"*",$ctable_where,"id DESC limit $page_position, $item_per_page",0);
?>
<style>
table{
    height: auto;   
    width:190mm;
    font-family: Calibri,sans-serif;
    font-style: normal;
    font-weight: 400;
    padding: 0;
    text-decoration: none;
    font-size: 16px;
    margin:auto;
    padding:auto;
}
table{
    height: auto;   
    width:190mm;
}
table , td, th {
 border: 1px solid #595959;
 border-collapse: collapse;
}
td, th {
 padding: 2px;
 width: 50px;
 height: 45px;
}
h4{
    padding-left:40px;
}
th {
}
.center{
    text-align:center;
}
.left{
    text-align:left;
    padding-left:15px;
}
.right{
    text-align:right;
    padding-right:15px;
}
</style>

	<table id="datatable_1" class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
            	<th>Sr No.</th>
            	<th>Source Of Inquiry</th>
                <th>Inquiry No.</th>
                <th>Company Name</th>
                <th>Persoan Name</th>
                <th>Phone</th>
                <th>State</th>
				<th>City</th>				
				<th>Description</th>
				<th>Inquiry Date</th>
				<th>Inquiry Taken By</th>
				<th>Status</th>                
				<!-- <th>Inquiry Followup</th>                 -->
            </tr>
        </thead>
        <tbody>
        <?php
		
        if(mysqli_num_rows($ctable_r)>0){
            $count = 0;
            
            while($ctable_d = mysqli_fetch_array($ctable_r)){
            	$inquiry_type_array = array("1"=>"Email","2"=>"Call","3"=>"Whatsapp");
        ?>
            <tr>
            	<td><?php echo ++$count; ?></td>
            	<td><?php  echo $inquiry_type_array[$ctable_d['source_of_inquiry']]; ?></td>
                <td>#INQ/<?php  echo $ctable_d['id']; ?></td>
                
				<td><span class="<?php echo ($ctable_d['isActive']==0)?"text-danger":"text-success"; ?>"><?php
					echo $ctable_d['company_name']; ?></span>
				</td>
				<td><span class="<?php echo ($ctable_d['isActive']==0)?"text-danger":"text-success"; ?>"><?php
					echo $ctable_d['person_name']; ?></span>
				</td>
				<td><?php
					echo $ctable_d['mobile_number']; ?>
				</td>
				<td><?php echo $ctable_d['state']; ?></td>
				<td><?php echo $ctable_d['city']; ?></td>
				<td><?php echo $ctable_d['description']; ?></td>
				<td><?php if($ctable_d['datetime']!="0000-00-00 00:00:00"){ echo date('d-m-Y',strtotime($ctable_d['datetime'])); } else{ echo "";} ?></td>
				<?php $action=$db->rp_getValue("no_order_inquiry_action","name","id='".$ctable_d['action']."'");?>
				
				<td>
				<?php				
				$sales_executive=$db->rp_getValue("sales_executive","name","id='".$ctable_d['sales_executive_id']."'");
				?>
				<?php echo stripslashes($sales_executive); ?></td>
				<td>
					<?php
					if ($ctable_d['status']==0)
					{
						echo $ctable_d['status']="Generate";
					}
					else if ($ctable_d['status']==1)
					{
						echo $ctable_d['status']="Followup";
					}
					else if ($ctable_d['status']==2)
					{
						echo $ctable_d['status']="Interested";
					}
					else if ($ctable_d['status']==-1)
					{
						echo $ctable_d['status']="Not Interested";
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
				<th colspan="11" style="text-align: center;">No Data Found</th>
			</tr>
			<?php
		}
		
		?>
	
        </tbody>
    </table>
<?php require_once "disconnect.php"; ?>   