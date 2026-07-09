<?php
$page_id=572;$page_slug='no_order_inquiry_page';
include("connect.php");
$ctable 	= "document_type";
$Where = "";

// if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
//   $Where .= " (
//               name like '%".$_REQUEST['searchName']."%'         
//             ) AND ";
// }



// $Where .= " isDelete=0";
	

$ctable_r = $db->rp_getData($ctable,"*","isDelete=0","id DESC",1);
/*for log*/
$flag = "Web";
$module_name = "zone";
$log_description = $module_name." Printed By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");
$last_id = "";
$db->insertLog($ctable,$last_id,"print","",$insert,0,$log_description,$flag,$module_name,$user_id,"");
/*for log*/
?>

  </tbody>
</table>
<?php require_once "disconnect.php"; ?>