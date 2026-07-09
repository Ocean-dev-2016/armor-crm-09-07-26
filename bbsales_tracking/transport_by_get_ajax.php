<?php
$page_id=609;$page_slug='transport_by';
include("connect.php");
$ctable = "transport_by";
$ctable1 = "Transport By";

$ctable_where = "";
if (isset($_REQUEST['searchName']) && $_REQUEST['searchName'] != "") 
{
    $ctable_where .= " (name like '%" . $_REQUEST['searchName'] . "%') AND ";
}

$ctable_where .= " isDelete=0";
$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"])) ? intval($_REQUEST["show"]) : 100;

if (isset($_REQUEST["page"])) {
    $page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
    if (!is_numeric($page_number)) {
        die('Invalid page number!');
    } //incase of invalid page number
} else {
    $page_number = 1; //if there's no page number, set it to 1
}

$get_total_rows = $db->rp_getTotalRecord($ctable, $ctable_where); //hold total records in variable
$total_pages = ceil($get_total_rows / $item_per_page);

$page_position = (($page_number - 1) * $item_per_page);

$ctable_r = $db->rp_getData($ctable, "*", $ctable_where, "name ASC limit $page_position, $item_per_page",0);
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
                <th class="fix-th1" style="width: 5%;">No.</th>
                <th class="fix-th1">Name</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($ctable_r) 
            {
                $count = 0;
                while ($ctable_d = mysqli_fetch_array($ctable_r)) 
                {
                    $count++;
                    ?>
                    <tr>
                        <td><?php $ctable_d['id']; ?>
                            <?php
                            if ($rights['update_flag'] == 1) 
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
                                        if ($rights['delete_flag'] == 1 && $ctable_d['id'] != -1) {
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
                                    </ul>
                                </div>
                                <?php
                            }
                            ?>
                        </td>
                        <td><?php echo $count; ?></td>
                        <td><?php echo stripslashes($ctable_d['name']); ?></td>
                    </tr>
                    <?php
                }
            } 
            else 
            {
                ?>
                <tr>
                    <td colspan="3">
                        <p style="text-align:center;">No data available in table</p>
                    </td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
</div>
    <div class="row">
        <div class="col-md-6">
            <div class="dataTables_info"> Rows Limit:
                <select id="numRecords" onChange="changeDisplayRowCount(this.value);">
                    <option value="500" <?php if ($_REQUEST["show"] == 500 || $_REQUEST["show"] == "") { echo ' selected="selected"';}  ?>>500</option>
                    <option value="1000" <?php if ($_REQUEST["show"] == 1000) { echo ' selected="selected"';}  ?>>1000</option>
                    <option value="2000" <?php if ($_REQUEST["show"] == 2000) { echo ' selected="selected"';}  ?>>2000</option>
                    <option value="5000" <?php if ($_REQUEST["show"] == 5000) { echo ' selected="selected"';}  ?>>5000</option>
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
<?php require_once 'disconnect.php';  ?>