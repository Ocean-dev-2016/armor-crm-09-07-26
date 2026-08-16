<?php
/*
 * @author Ravi Patel
 */
$page_id=406;$page_slug='page_admin';
include("connect.php");
$ctable = "dealer_distributor_network";
$ctable1 = "Admin Management";
$TYPE=array(4=>"Channel Partner",3=>"Customer",2=>"Sales Officer","0"=>"Super Admin");
$ctable_where = "";
// Get the total number of rows in the table

if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
	$ctable_where .= " (
							name like '%".$_REQUEST['searchName']."%'					
						) AND ";
}

$ctable_where .= " isDelete=0";

$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"]) ) ? intval($_REQUEST["show"]) : 100;

if(isset($_REQUEST["page"])){
	$page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
	if(!is_numeric($page_number)){die('Invalid page number!');} //incase of invalid page number
}else{
	$page_number = 1; //if there's no page number, set it to 1
}

$get_total_rows = $db->rp_getTotalRecord($ctable,$ctable_where); //hold total records in variable
//break records into pages
$total_pages = ceil($get_total_rows/$item_per_page);

//get starting position to fetch the records
$page_position = (($page_number-1) * $item_per_page);

$ctable_r = $db->rp_getData($ctable,"*",$ctable_where." AND id!=41","id DESC limit $page_position, $item_per_page",0);
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

<form action="" name="frm" id="frm" method="post">
    <div class="table-scrollable">
	<table id="datatable_1" class="table table-striped table-bordered table-hover">
        <thead class="fix-th">
            <tr>
				<th class="fix-th1" style="width: 5%;"></th>
                <th class="fix-th1">No.</th>
                <th class="fix-th1">Name</th>                			
                <th class="fix-th1">UserName</th>                			
                <th class="fix-th1">Admin Type</th>                			
                <th class="fix-th1">Type</th>                           
                <th class="fix-th1">Email</th>                			
            </tr>
        </thead>
        <tbody>
        <?php
        if(mysqli_num_rows($ctable_r)>0){
            $count = 0;
            
            while($ctable_d = mysqli_fetch_array($ctable_r)){
                $count++;
        ?>
            <tr>

                <td>
                <?php $ctable_d['id'];              
                if($rights['update_flag']==1)
                {
                    ?>
                    <div class="btn-group">             
                        <button aria-expanded="false" data-toggle="dropdown" type="button" class="btn btn-sm blue dropdown-toggle">
                            <i class="fa fa-gear"></i>
                        </button>
                        <ul role="menu" class="dropdown-menu">
                            <li>
                                <a href="<?php echo $ctable; ?>_crud.php?mode=edit&id=<?php echo $ctable_d['id']; ?>" title="Edit">
                                    <span class="text-primary">
                                        <i class="fa fa-pencil"></i>
                                        &nbsp;Edit
                                    </span>
                                </a>
                            </li>
                            <?php
                            if($rights['delete_flag']==1 && $ctable_d['id']!=-1)
                            {
                                ?>
                                <li>
                                    <a onClick="del_conf('<?php echo $ctable_d['id']; ?>');" title="Delete">
                                        <span class="text-danger">
                                            <i class="fa fa-times"></i>
                                            &nbsp;Delete
                                        </span>
                                    </a>
                                </li>
                                <?php
                            }
                            ?> 
                                 <li>
                                    <a class="" href="#changePasswordModal" data-id="<?php echo $ctable_d['id']; ?>" class="btn sbold blue-ebonyclay" data-toggle="modal" title="Change Password"> <i class="fa fa-gear"></i>&nbsp;Change Password</a>
                                </li> 
                        </ul>
                    </div>
                    <?php
                }
                ?>
            </td>
                
                <td><?php echo $count; ?></td>
                <td><?php echo stripslashes($ctable_d['name']); ?></td>
                <?php
                if($ctable_d['type']==3 || $ctable_d['type']==4)
                {
                ?>
                <td><a onclick="location.href='executive_crud.php?mode=edit&type=<?php echo $ctable_d['admin_type']?>&id=<?php echo $ctable_d['customer_id'];?>'" style="cursor: pointer;" title="Edit"><?php echo stripslashes($ctable_d['username']); ?></a></td>
                <?php
                }
                else
                {
                ?>
                <td><a onclick="location.href='sales_executive_crud.php?mode=edit&id=<?php echo $ctable_d['sales_executive_id'];?>'" style="cursor: pointer;" title="Edit"><?php echo  stripslashes($ctable_d['username']); ?></a></td>
                <?php
                }
                ?>	

                <!-- <td><?php echo stripslashes($ctable_d['username']); ?></td>				 -->
                <td><?php echo $db->rp_getValue("admin_type","name","id='".$ctable_d['admin_type']."'",0); ?></td>	
                <td><?php
					$typeLabel = isset($TYPE[$ctable_d['type']]) ? $TYPE[$ctable_d['type']] : $ctable_d['type'];
					if ($ctable_d['type'] == 3 && $ctable_d['customer_id'] > 0) {
						$cpListFlag = $db->rp_getValue("executive", "channel_partner_flag", "id='".$ctable_d['customer_id']."' AND isDelete=0", 0);
						if ((int) $cpListFlag === 1) {
							$typeLabel = "Channel Partner";
						}
					}
					echo $typeLabel;
				?></td>			
                <td><?php echo stripslashes($ctable_d['email']); ?></td>				
                <!-- <td>
                <a class="btn btn-info btn-sm" onClick="window.location.href='<?php echo $ctable; ?>_crud.php?mode=edit&id=<?php echo $ctable_d['id']; ?>'" title="Edit"><i class="fa fa-pencil"></i></a>
				<a class="btn btn-danger btn-sm" onClick="del_conf('<?php echo $ctable_d['id']; ?>');" title="Delete"><i class="fa fa-times"></i></a>
				</td> -->
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
				echo $db->rp_paginate_function($item_per_page, $page_number, $get_total_rows, $total_pages); 
				?>
				</ul>
			</div>
		</div>
	</div>
</form>
 <?php require_once("disconnect.php"); ?>