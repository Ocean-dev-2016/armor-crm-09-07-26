<?php
$page_id=669;$page_slug='payment_followup_manage';
include("connect.php");
$ctable     = "payment_followup";
$ctable1    = "Payment Followup";

$ctable_where = "";

 if($_REQUEST['followup_flag']=="customer_payment_followup")
{
    $ctable_where .= "reference_id = '".$_REQUEST['executive_id']."' AND isDelete=0 AND reference_table='executive'";
}
else
{
    $ctable_where .= "visitor_id = '".$_REQUEST['visitor_id']."' AND isDelete=0 ";
}

if(isset($_REQUEST['sales_id']) && $_REQUEST['sales_id']!="" && $_REQUEST['sales_id']!=NULL)
{
    $ctable_where .= " And visitor_id='".$_REQUEST['sales_id']."'";
}

if(isset($_REQUEST['ToDate']) && $_REQUEST['ToDate']!="" && $_REQUEST['ToDate']!=NULL)
{
    $ctable_where .= " And DATE(adate)<='".date_format(date_create($_REQUEST['ToDate']),"Y-m-d")."' ";
}

if(isset($_REQUEST['FromDate']) && $_REQUEST['FromDate']!="" && $_REQUEST['FromDate']!=NULL)
{
    $ctable_where .= " AND DATE(adate)>= '".date_format(date_create($_REQUEST['FromDate']),"Y-m-d")."' ";
}
if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!="" && $_REQUEST['searchName']!=NULL)
{
    $ctable_where .= " AND name like '%".$db->clean($_REQUEST['searchName'])."%'";
}


$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"]) ) ? intval($_REQUEST["show"]) : 100;
if(isset($_REQUEST["page"])){
    $page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
    if(!is_numeric($page_number)){die('Invalid page number!');} //incase of invalid page number
}else{
    $page_number = 1; //if there's no page number, set it to 1
}


if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
{
    $loginid=$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'];
    // $loginid=$db->rp_getValue("dealer_distributor_network","sales_executive_id","isDelete=0 AND id='".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID']."'",0);
    // echo $loginid;
    if($rights['personal_flag']==1)
    {
        $ctable_where .= " AND status!=-1 AND (inquiry_assign_to='".$_SESSION[SITE_SESS.'REFERANCE_ID']."' OR inquiry_created_by='".$_SESSION[SITE_SESS.'REFERANCE_ID']."' OR user_id='".$_SESSION[SITE_SESS.'REFERANCE_ID']."' OR created_by='".$loginid."')";
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
                $ctable_where .= " AND status!=-1 AND (inquiry_assign_to IN (".$SALEID1.','.$_SESSION[SITE_SESS.'REFERANCE_ID'].") OR inquiry_created_by IN (".$SALEID1.','.$_SESSION[SITE_SESS.'REFERANCE_ID'].") OR user_id IN (".$SALEID1.','.$_SESSION[SITE_SESS.'REFERANCE_ID'].") OR created_by='".$loginid."')";      
            }
            else
            {
                $ctable_where .= " AND status!=-1 AND (inquiry_assign_to IN (".$_SESSION[SITE_SESS.'REFERANCE_ID'].") OR inquiry_created_by IN (".$_SESSION[SITE_SESS.'REFERANCE_ID'].") OR user_id IN (".$_SESSION[SITE_SESS.'REFERANCE_ID'].") OR created_by='".$loginid."')"; 
            }
        }
        else
        {
            $ctable_where .= " AND status!=-1";
        }
    }  
}
else
{
    $ctable_where .= " AND status!=-1";
}

$get_total_rows = $db->rp_getTotalRecord($ctable,$ctable_where); //hold total records in variable
//break records into pages
$total_pages = ceil($get_total_rows/$item_per_page);

//get starting position to fetch the records
$page_position = (($page_number-1) * $item_per_page);

$ctable_r = $db->rp_getData($ctable,"*",$ctable_where,"followup_date DESC limit $page_position, $item_per_page",0);
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
<table id="datatable_1" class="table table-striped table-bordered table-hover1 table">
        <thead class="fix-th">
            
            <tr>
                <?php               
                if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
                {
                ?>  
                <th class="fix-th1"></th>
                <?php
                }
                ?>
                <th class="fix-th1" style="width: 5%">No.</th>
                <th class="fix-th1">Customer Name</th>
                <th class="fix-th1">Mobile No</th>
                <th class="fix-th1">Admin Name</th>
                <th class="fix-th1">Followup Date</th>
                <th class="fix-th1">Description</th>
                <th class="fix-th1">Through</th>
                <th class="fix-th1">Response Date</th>
                <th class="fix-th1">Response</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if(mysqli_num_rows($ctable_r)>0)
        {
            $count = 0;
            while($ctable_d = mysqli_fetch_array($ctable_r))
            {
                $fdt = date('d-m-Y',strtotime($ctable_d['followup_date']));
                $cl="";
                if($ctable_d['status']==0 && $fdt < date('d-m-Y'))
                {
                    $cl='style="background-color:#E63A3A;color:#FFF"';
                }
                ?>
                <tr>
                    <?php               
                    if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
                    {
                        ?>  
                        <td><?php $ctable_d['id']; ?>               
                            <div class="btn-group">             
                                <button aria-expanded="false" data-toggle="dropdown" type="button" class="btn btn-sm blue dropdown-toggle"> <i class="fa fa-gear"></i>
                                </button>
                                <ul role="menu" class="dropdown-menu">
                                    <?php
                                    /*if($rights['delete_flag']==1 && $ctable_d['id']!=-1)
                                    {*/
                                    ?>
                                    <li>
                                        <a  onClick="del_conf('<?php echo $ctable_d['id']; ?>','<?php echo $ctable_d['reference_id']; ?>');" title="Delete"><span class="text-danger"><i class="fa fa-times"></i> &nbsp;Delete</span></a>
                                    </li>
                                    <?php
                                    // }
                                    ?>  
                                </ul>
                            </div>
                        </td>
                        <?php
                    }
                    ?>
                    <td><?php echo ++$count; ?></td>    
                    <td>
                        <?php
                        if($ctable_d['reference_table']=="sales_executive")
                        {
                            echo $db->rp_getValue("executive","cname","id='".$ctable_d['visitor_id']."'");
                        }
                        else if($ctable_d['reference_table']=="no_order_inquiry")
                        {
                            echo $db->rp_getValue("no_order_inquiry","company_name","id='".$ctable_d['reference_id']."'");
                        }
                        else if($ctable_d['reference_table']=="quotation_detail")
                        {
                            $cid = $db->rp_getValue("quotation_detail","customer_id","id='".$ctable_d['reference_id']."'");
                            echo $db->rp_getValue("executive","company_name","id='".$cid."'",0);
                        }
                        else if($ctable_d['reference_table']=="customer_inquiry")
                        {
                            echo $db->rp_getValue("customer_inquiry","company_name","id='".$ctable_d['reference_id']."'");
                        }
                        else if($ctable_d['reference_table']=="executive")
                        {
                            echo $db->rp_getValue("executive","company_name","id='".$ctable_d['reference_id']."'");
                        }
                        ?>
                    </td>
                    <td>
                        <?php 
                        if($ctable_d['reference_table']=="sales_executive")
                        {
                            echo $db->rp_getValue("executive","mobile_no1","id='".$ctable_d['visitor_id']."'");
                        }
                        else if($ctable_d['reference_table']=="no_order_inquiry")
                        {
                            echo $db->rp_getValue("no_order_inquiry","mobile_number","id='".$ctable_d['reference_id']."'");
                        }
                        else if($ctable_d['reference_table']=="quotation_detail")
                        {
                            $cid = $db->rp_getValue("quotation_detail","customer_id","id='".$ctable_d['reference_id']."'",0);
                            echo $db->rp_getValue("executive","mobile_no1","id='".$cid."'",0);
                        }
                        else if($ctable_d['reference_table']=="customer_inquiry")
                        {
                            echo $db->rp_getValue("customer_inquiry","mobile_number","id='".$ctable_d['reference_id']."'");
                        }
                        else if($ctable_d['reference_table']=="executive")
                        {
                            echo $db->rp_getValue("executive","mobile_no1","id='".$ctable_d['reference_id']."'");
                        }
                        ?>
                    </td>
                    <td><?php echo $db->rp_getValue("dealer_distributor_network","name","id='".$ctable_d['user_id']."'");?></td>  
                    <!-- followupdate-->
                    <?php 
                    if($ctable_d['followup_date']=="0000-00-00 00:00:00")
                    {
                        ?>
                        <td></td>
                        <?php 
                    } 
                    else
                    { 
                        ?>
                        <td <?= $cl; ?>><?php echo date('d-m-Y h:i A',strtotime($ctable_d['followup_date'])); ?></td>
                        <?php 
                    } 
                    ?> 
                    <!-- followupdate-->
                    <td><?php echo $ctable_d['description']; ?></td>            
                    <td>
                    <?php 
                    if($ctable_d['through']=='1')
                    {
                        $slug="call";
                    }
                    else if($ctable_d['through']=='2')
                    {
                        $slug="sms";
                    }
                    else if($ctable_d['through']=='3')
                    {
                        $slug="email";
                    }
                    else if($ctable_d['through']=='4')
                    {
                        $slug="Whatsapp";
                    }
                    echo $slug;
                    ?>
                    </td>             
                    <!-- responsedate-->
                    <?php if($ctable_d['response_date']=="0000-00-00 00:00:00")
                    {
                        ?>
                        <td></td>
                        <?php 
                    } 
                    else 
                    { 
                        ?>
                        <td><?php echo date('d-m-Y',strtotime($ctable_d['response_date'])); ?></td>
                        <?php 
                    } 
                    ?> 
                    <!-- responsedate-->           
                    <td><?php echo $ctable_d['response']; ?>
                    <?php if($ctable_d['status']==0) 
                    { 
                        ?>
                        <a type="button" id="response_followup_btn" href="#FollowupResponse" data-toggle="modal" target="#FollowupResponse" class="btn btn-circle btn-sm yellow" data-mode="add" data-sales-id="<?= $ctable_d['sales_executive_id']; ?>" data-date="<?php echo date('d-m-Y H:i:s',strtotime($ctable_d['followup_date'])); ?>" data-id="<?php echo $ctable_d['id']; ?>" data-next_action="<?php echo $ctable_d['next_action']; ?>">Response</a>
                        <?php 
                    } 
                    ?>
                    </td>
                </tr>
                <?php
            }
        }
        ?>
        </tbody>
    </table>
</div>
    
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
    <script type="text/javascript">
        $("#sales_executive").select2();
        $("#through").select2();
    </script><script type="text/javascript">
    $(".filterBtn").on("click",function()
    {
        df=$("#material_request_filter_input").val();
        // alert(df);
        df = encodeURI(df)
        displayRecords(100,1);
    })
    $(".datetimerange-picker-btn").on("click",function(){
        $(".datetimerange-picker-input",$(this).closest(".date")).focus();
    });
    $(".datetimerange-picker-input").daterangepicker({"format":"dd-mm-yy ",autoUpdateInput: false,timePicker:false,ranges: {
           'Today': [moment(), moment()],
           'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
           'Last 7 Days': [moment().subtract(6, 'days'), moment()],
           'Last 30 Days': [moment().subtract(29, 'days'), moment()],
           'This Month': [moment().startOf('month'), moment().endOf('month')],
           'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
    }});
        $('.datetimerange-picker-input').on('apply.daterangepicker', function(ev, picker) {
     $(".datetimerange-picker-input").val(picker.startDate.format('DD-MM-YYYY')+" to "+picker.endDate.format('DD-MM-YYYY'));
    });
</script>
<?php require_once("disconnect.php"); ?>