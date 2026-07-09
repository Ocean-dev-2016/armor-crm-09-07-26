<?php
$page_id=565;$page_slug='page_order';
include("connect_in.php");
// print_r($_FILES);exit;
$order_id = $_REQUEST['order_id'];
$pdf_atachments=array();
if (sizeof($_FILES["pdf_attachment"]['name']) > 0)
{
  // echo "hello";exit;
  $ri = $order_id;
  $rt = "orders";
  $tc = "pdf_attachment";

  for ($i=0; $i < sizeof($_FILES["pdf_attachment"]['name']) ; $i++) 
  {
                // $count++;
    $file_name = $_FILES['pdf_attachment']['name'][$i];
    $file_size = $_FILES['pdf_attachment']['size'][$i];
    $file_tmp = $_FILES['pdf_attachment']['tmp_name'][$i];
    $file_type =$_FILES['pdf_attachment']['type'][$i];
    $allowedExts = array(strtolower(pathinfo($file_name, PATHINFO_EXTENSION)));
    $temp = explode(".", $file_name);
    $extension = end($temp);
    if ($file_name != "") 
    {
      $adate    = date('Y-m-d H:i:m');

      // if (in_array($extension, $allowedExts)) 
      // {
          $file_error = true;  // all type na attachment add thava joye etle 
      //   // echo "897415353";exit;
      // }
      if ($file_error)
      {
        $extension  = end(explode(".", $file_name));

        $uploadDir = "order_documents/"; // Specify the directory where you want to store the uploaded media files.
        /*$customer_name = $db->rp_getValue("orders","customer_name","isDelete=0 AND id='".$_REQUEST['order_id']."'",0);
        $customer_name = $db->rp_createSlug($customer_name);*/

        $pdf_atachment = $db->rp_createslug($file_name).$i.substr(sha1(time()).rand(), 0, 6) . "." . $extension;
        $filePath   = $uploadDir . $pdf_atachment;
        move_uploaded_file($file_tmp, $filePath);

        $MediaTitle=$pdf_atachment;
        $MediaOrignalTitle=$pdf_atachment;
        $MediaFileName=$pdf_atachment;
        $UploadDate=date("Y-m-d H:i:s");

        $Values=array($MediaTitle,$MediaOrignalTitle,$MediaFileName,$extension,$UploadDate,$ri,$rt,$tc);

        $Columns=array("title","orignal_title","url","ext","upload_date","reference_id","reference_table","reference_column");

        $MediaID=$db->rp_insert("media",$Values,$Columns,0);
        $pdf_atachment1[] = $MediaID;
          
      }
    }
  }
  if (sizeof($pdf_atachment1) > 0) 
  {

    $pdf_atachment_ids = implode(",", $pdf_atachment1);
    if ($order_id!="") 
    {
      $pdf_atachment_r = $db->rp_getData("orders","pdf_attachment","isDelete=0 AND id='".$order_id."'",0);
      while($pdf_atachment_d=mysqli_fetch_assoc($pdf_atachment_r))
      { 
        $pdf_old_atachment[]=$pdf_atachment_d['pdf_attachment'];
      }
      $pdf_old_atachment_ids =implode(",", $pdf_old_atachment);
    }

    if ($pdf_old_atachment_ids!="") 
    {
      $mergedArray = array_merge([$pdf_old_atachment_ids],[$pdf_atachment_ids]);
      $merged_arrpdf_atach =implode(",", $mergedArray);
    }
    else
    {
      $merged_arrpdf_atach = $pdf_atachment_ids;
    }

    $rows   = array(
    "pdf_attachment"  => $merged_arrpdf_atach
    );
    $where  = "id='".$order_id."'";
    $upadateid=$db->rp_update("orders",$rows,$where,0);
    if ($upadateid != 0) 
    {
      $ack = array("ack"=>1,"ack_msg"=>"File uploaded successfully");
      $iamge_detail = array("pdf_ids"=>$pdf_atachment_ids,"order_id"=>$order_id);
    }
    else 
    {
      $ack = array("ack"=>0,"ack_msg"=>"Error occurred while uploading the file.");
    }
  }
}
echo json_encode($ack);
?>
