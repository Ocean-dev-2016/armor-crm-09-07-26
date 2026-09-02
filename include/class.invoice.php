<?php
require_once("main.class.php");
require_once("function.class.php");
class Invoice extends Functions
{
	public $db;
	public $ctable="invoice";
	function __construct($id="") 
	{
		$db = new Functions();
		$conn =$db->connect();
		$this->db=$db;		   
    } 
	public function InsertInvoice($detail,$file) 
	{
	    extract($detail);
		$dup_where = "invoice_no = '".$invoice_no."' AND isDelete=0";
		$r = $this->db->rp_dupCheck($this->ctable,$dup_where,0);
		if($r){
			$reply=array("ack"=>0,"developer_msg"=>"Already Exist Category Name","ack_msg"=>"Duplication! Already Exist Invoice No.");
			return $reply;
		}
		else
		{

			if (isset($file["image_path"]) ) {
				$allowedExts = array("jpg","jpeg","png","gif","JPG","JPEG");
				$temp = explode(".", $file["image_path"]["name"]);
				 $extension = end($temp);
			 
					$fileName 	= $this->db->clean($file["image_path"]["name"]);	
					if($fileName!=""){
					$fileSize 	= round($file["image_path"]["size"]); // BYTES									
					$adate 		= date('Y-m-d H:i:m');
					
					$extension	= end(explode(".", $fileName));		
					if(!in_array($extension,$allowedExts))
					{
						$file_error=true;
					}
										
					$image_path	= 'image_'.substr(sha1(time()), 0, 6).".".$extension;
					$filePath 	= INVOICE_A.$image_path;	
					$file['image_path']['tmp_name'];
					move_uploaded_file($file['image_path']['tmp_name'], $filePath);
					
					$new_image=true;
					}
					else{
						$image_path="";
					}
			}
			else
			{
				$new_image=false;
				$image_path="";
				
			}

			$invoice_date=date('Y-m-d',strtotime($detail['invoice_date']));
			$adate	= date('Y-m-d H:i:s');
			$rows 	= array(
						"invoice_no",
						"customer_id",
						"sales_executive_id",
						"amount",
						"remark",
						"invoice_date",
						"image_path",
						"isActive",
						"isDelete"
					);
			$values = array(
						$invoice_no,
						$customer_id,
						$sales_executive_id,
						$amount,
						$remark,
						$invoice_date,
						$image_path,
						$isActive,
						$isDelete
					);
					
		 	$uid = $this->db->rp_insert($this->ctable,$values,$rows,0);
			if($uid!=0)
			{
				$Add_payment=$this->Add_Invoice_Payment($uid);
				$reply=array("ack"=>1,"developer_msg"=>"Invoice Added.","ack_msg"=>"Success! Invoice Insert Successfully.");
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Invoice Insert Failed.");
				return $reply;
			}
		}
	}
	 
	 public function UpdateInvoice($detail,$file)
	  {
			extract($detail);
			$dup_where = "invoice_no = '".$invoice_no."' AND id!='".$_REQUEST['id']."' AND isDelete=0";
			$r = $this->db->rp_dupCheck($this->ctable,$dup_where);
			if($r){
				$reply=array("ack"=>0,"developer_msg"=>"Already Exist Invoice Name","ack_msg"=>"Duplication! Already Exist Invoice No.");
				return $reply;
				
			}else{


				if(isset($file["image_path"]) && $file["image_path"]['size']!=0) 
					{
						$allowedExts = array("jpg","jpeg","png","gif","JPG","JPEG");
						$temp = explode(".", $file["image_path"]["name"]);
					 	$extension = end($temp);
				 
						$fileName 	= $this->db->clean($file["image_path"]["name"]);	
						if($fileName!=""){
							$fileSize 	= round($file["image_path"]["size"]); // BYTES									
							$adate 		= date('Y-m-d H:i:m');
							
							$extension	= end(explode(".", $fileName));		
							if(!in_array($extension,$allowedExts))
							{
								$file_error=true;
							}
												
							$image_path	= 'image_'.substr(sha1(time()), 0, 6).".".$extension;
							$filePath 	= INVOICE_A.$image_path;	
							$file['image_path']['tmp_name'];
							move_uploaded_file($file['image_path']['tmp_name'], $filePath);
							
							$new_image=true;
						}
						else{
							$image_path=$detail['old_image_path'];
							$image_path="";
						}
					}
					else
					{
						$image_path=$detail['old_image_path'];
  						unset($detail['old_image_path']);
					}

					$invoice_date=date('Y-m-d',strtotime($detail['invoice_date']));
				$rows 	= array(
						"invoice_no"			=> $invoice_no,
						"customer_id"			=> $customer_id,
						"sales_executive_id"    => $sales_executive_id,
						"amount"				=> $amount,
						"remark"				=> $remark,
						"invoice_date"			=> $invoice_date,
						"image_path"			=> $image_path,
						"isDelete"				=> $isDelete,
						);
				$where	= "id='".$_REQUEST['id']."'";
				$uid=$this->db->rp_update($this->ctable,$rows,$where,0);
				if($uid!=0)
				{
					$reply=array("ack"=>1,"developer_msg"=>"Invoice Update Successfull!!.","ack_msg"=>"Success! Invoice Update Successfully.");
					return $reply;
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Category Update Failed.");
					return $reply;
				}
			}	
		}	
	public function GetEditDataCategory($detail)
	{		
		$where = " id='".$detail['id']."' AND isDelete=0";
		$ctable_r = $this->db->rp_getData($this->ctable,"*",$where,0);
		$ctable_d = mysqli_fetch_array($ctable_r);
		$result=array();
		
		$result['invoice_no']		      = htmlentities($ctable_d['invoice_no']);
		$result['sales_executive_id']	  = explode(",",$ctable_d['sales_executive_id']);
		$result['customer_id']		      = htmlentities($ctable_d['customer_id']);
		$result['amount']			      = htmlentities($ctable_d['amount']);
		$result['remark']		       	  = htmlentities($ctable_d['remark']);
		$result['invoice_date']		      = date('d-m-Y',strtotime($ctable_d['invoice_date']));
		$result['image_path'] 		      = $ctable_d['image_path'];
		
		$reply=array("ack"=>1,"developer_msg"=>"Invoice detail fetched!!.","ack_msg"=>"Success! Invoice Edit Successfully.","result"=>$result);
		return $reply;
	
	}
	
	public function DeleteInvoice($detail)
	{
		$rows 	= array(
		"isDelete"	=> "1"
		);
			$where	= "id='".$_REQUEST['id']."'";
			$uid=$this->db->rp_update($this->ctable,$rows,$where);
			if($uid!=0)
			{
			    $where1	= "reference_id='".$_REQUEST['id']."'";
			    $update = $this->db->rp_update("account_transaction",$rows,$where1,0);
				$reply=array("ack"=>1,"developer_msg"=>"deleted data.","ack_msg"=>"Success! Delete Invoice Successfully.");
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Delete Invoice Failed.");
				return $reply;
			}
	}


	/*AUTO ENTRY TO ACCOUNT TRANSACTION*/
	public function Add_Invoice_Payment($id)
	{
		$InvoiceDetail=$this->rp_getData("invoice","*","id='".$id."'","",0);
		if($InvoiceDetail)
		{
			$Data_d = mysqli_fetch_assoc($InvoiceDetail);
			$account_id = $this->db->rp_getValue("account","id","cid='".$Data_d['customer_id']."'",0);
			$account_no = $this->db->rp_getValue("account","acc_no","cid='".$Data_d['customer_id']."'",0);
			$description = "Invoice Payment Of Invoice No is :#".$Data_d['invoice_no']." Amount of ".$Data_d['amount']." RS";

			// add in account transaction

			$row1 = array("reference_table","reference_id","cid","account_id","account_no","amount","credit","type","description");
			$value1 = array('invoice',$id,$Data_d['customer_id'],$account_id,$account_no,$Data_d['amount'],$Data_d['amount'],'1',$description);
			$insert = $this->db->rp_insert("account_transaction",$value1,$row1,0);

			if($insert)
			{
				$result=array("ack"=>1,"ack_msg"=>"Purchase Invoice Payment Add Successfully");
			}
			else
			{
				$result=array("ack"=>1,"ack_msg"=>"Purchase Invoice Payment Not Add");
			}
		}
	}
	/*AUTO ENTRY TO ACCOUNT TRANSACTION*/

	public function DownloadInvoice($id, $format_type = 1)
	{
		$id = $this->db->clean($id);
		if (!empty($id)) {
			$count = $this->db->rp_getTotalRecord("invoice_new", "id='" . $id . "'", 0);
			if ($count > 0) {
				if (function_exists('session_write_close')) {
					@session_write_close();
				}
				require_once dirname(__FILE__) . '/armor_pdf_export_helper.php';

				$company_name = (string) $this->db->rp_getValue("invoice_new", "company_name", "id='" . $id . "'", 0);
				$invoice_no = (string) $this->db->rp_getValue("invoice_new", "invoice_no", "id='" . $id . "'", 0);
				$invoice_no_clean = str_replace(array('/', '\\', ' '), '-', $invoice_no);
				$company_slug = $this->db->rp_createSlug($company_name);
				$fileName = date('d_m_Y') . "_Invoice_" . ($company_slug ? $company_slug . "_" : "") . $invoice_no_clean;

				$gen = armor_pdf_export_generate(
					'invoice_view_download.php',
					array('invoice_id' => (int) $id, 'format_type' => (int) $format_type),
					array('TAX INVOICE', 'Invoice', 'invoice', 'table', 'body', 'html'),
					$fileName . '.pdf'
				);

				if (!$gen['ok']) {
					return array(
						"ack" => 0,
						"developer_msg" => isset($gen['error']) ? $gen['error'] : 'Invoice HTML could not be loaded for PDF.',
						"ack_msg" => "Invoice PDF Not Generate!!"
					);
				}

				$sales_id = (int) $this->db->rp_getValue("invoice_new", "sales_id", "id='" . $id . "'", 0);
				$sales_name = (string) $this->db->rp_getValue("sales_executive", "name", "id='" . $sales_id . "'", 0);
				$customer_id = (int) $this->db->rp_getValue("invoice_new", "customer_id", "id='" . $id . "'", 0);
				$log_description = "Invoice " . $invoice_no . " PDF Download By " . $sales_name . " ON " . date("Y-m-d H:i:s");
				@$this->db->insertLog("invoice_new", $id, "insert", "", array(), 0, $log_description, "Application", "Invoice", $sales_id, $customer_id);

				$pdfUrl = $gen['url'];
				$result = array();
				$result['pdf'] = $pdfUrl;
				$result['file_url'] = $pdfUrl;
				$result['file_name'] = $fileName . '.pdf';
				$result['pdf_ok'] = 1;

				return array(
					"ack" => 1,
					"developer_msg" => "Invoice PDF Generate Successfully",
					"ack_msg" => "Invoice PDF Generate Successfully",
					"pdf" => $pdfUrl,
					"file_url" => $pdfUrl,
					"result" => $result
				);
			} else {
				return array("ack" => 0, "developer_msg" => "Invoice Not Found!!", "ack_msg" => "Invoice Not Generate!!");
			}
		} else {
			return array("ack" => 0, "developer_msg" => "Invoice Id Required!!", "ack_msg" => "Invoice Id Required!!");
		}
	}

	public function DownloadPackingSlip($id)
	{
		$id = $this->db->clean($id);
		if (!empty($id)) {
			$count = $this->db->rp_getTotalRecord("packing_slip", "id='" . $id . "'", 0);
			if ($count > 0) {
				if (function_exists('session_write_close')) {
					@session_write_close();
				}
				require_once dirname(__FILE__) . '/armor_pdf_export_helper.php';

				$packing_slip_no = (string) $this->db->rp_getValue("packing_slip", "packing_slip_no", "id='" . $id . "'", 0);
				$packing_slip_clean = str_replace(array('/', '\\', ' '), '-', $packing_slip_no);
				$fileName = date('d_m_Y') . "_Packing_Slip_" . $packing_slip_clean;

				$gen = armor_pdf_export_generate(
					'packing_slip_download.php',
					array('id' => (int) $id),
					array('PACKING SLIP', 'Packing Slip', 'packing', 'table', 'body', 'html'),
					$fileName . '.pdf'
				);

				if (!$gen['ok']) {
					return array(
						"ack" => 0,
						"developer_msg" => isset($gen['error']) ? $gen['error'] : 'Packing Slip HTML could not be loaded for PDF.',
						"ack_msg" => "Packing Slip PDF Not Generate!!"
					);
				}

				$sales_id = (int) $this->db->rp_getValue("packing_slip", "sales_id", "id='" . $id . "'", 0);
				$sales_name = (string) $this->db->rp_getValue("sales_executive", "name", "id='" . $sales_id . "'", 0);
				$customer_id = (int) $this->db->rp_getValue("packing_slip", "customer_id", "id='" . $id . "'", 0);
				$log_description = "Packing Slip " . $packing_slip_no . " PDF Download By " . $sales_name . " ON " . date("Y-m-d H:i:s");
				@$this->db->insertLog("packing_slip", $id, "insert", "", array(), 0, $log_description, "Application", "Packing Slip", $sales_id, $customer_id);

				$pdfUrl = $gen['url'];
				$result = array();
				$result['pdf'] = $pdfUrl;
				$result['file_url'] = $pdfUrl;
				$result['file_name'] = $fileName . '.pdf';
				$result['pdf_ok'] = 1;

				return array(
					"ack" => 1,
					"developer_msg" => "Packing Slip PDF Generate Successfully",
					"ack_msg" => "Packing Slip PDF Generate Successfully",
					"pdf" => $pdfUrl,
					"file_url" => $pdfUrl,
					"result" => $result
				);
			} else {
				return array("ack" => 0, "developer_msg" => "Packing Slip Not Found!!", "ack_msg" => "Packing Slip Not Generate!!");
			}
		} else {
			return array("ack" => 0, "developer_msg" => "Packing Slip Id Required!!", "ack_msg" => "Packing Slip Id Required!!");
		}
	}

	public function DownloadDispatch($id)
	{
		$id = $this->db->clean($id);
		if (!empty($id)) {
			$count = $this->db->rp_getTotalRecord("dispatch_detail", "id='" . $id . "'", 0);
			if ($count > 0) {
				if (function_exists('session_write_close')) {
					@session_write_close();
				}
				require_once dirname(__FILE__) . '/armor_pdf_export_helper.php';

				$company_name = (string) $this->db->rp_getValue("dispatch_detail", "company_name", "id='" . $id . "'", 0);
				$dispatch_no = (string) $this->db->rp_getValue("dispatch_detail", "dispatch_no", "id='" . $id . "'", 0);
				$dispatch_clean = str_replace(array('/', '\\', ' '), '-', $dispatch_no);
				$company_slug = $this->db->rp_createSlug($company_name);
				$fileName = date('d_m_Y') . "_Dispatch_Order_" . ($company_slug ? $company_slug . "_" : "") . $dispatch_clean;

				$gen = armor_pdf_export_generate(
					'dispatch_format_download.php',
					array('id' => (int) $id),
					array('DISPATCH', 'Dispatch', 'dispatch', 'table', 'body', 'html'),
					$fileName . '.pdf'
				);

				if (!$gen['ok']) {
					return array(
						"ack" => 0,
						"developer_msg" => isset($gen['error']) ? $gen['error'] : 'Dispatch HTML could not be loaded for PDF.',
						"ack_msg" => "Dispatch PDF Not Generate!!"
					);
				}

				$sales_id = (int) $this->db->rp_getValue("dispatch_detail", "sales_id", "id='" . $id . "'", 0);
				$sales_name = (string) $this->db->rp_getValue("sales_executive", "name", "id='" . $sales_id . "'", 0);
				$customer_id = (int) $this->db->rp_getValue("dispatch_detail", "customer_id", "id='" . $id . "'", 0);
				$log_description = "Dispatch " . $dispatch_no . " PDF Download By " . $sales_name . " ON " . date("Y-m-d H:i:s");
				@$this->db->insertLog("dispatch_detail", $id, "insert", "", array(), 0, $log_description, "Application", "Dispatch", $sales_id, $customer_id);

				$pdfUrl = $gen['url'];
				$result = array();
				$result['pdf'] = $pdfUrl;
				$result['file_url'] = $pdfUrl;
				$result['file_name'] = $fileName . '.pdf';
				$result['pdf_ok'] = 1;

				return array(
					"ack" => 1,
					"developer_msg" => "Dispatch PDF Generate Successfully",
					"ack_msg" => "Dispatch PDF Generate Successfully",
					"pdf" => $pdfUrl,
					"file_url" => $pdfUrl,
					"result" => $result
				);
			} else {
				return array("ack" => 0, "developer_msg" => "Dispatch Not Found!!", "ack_msg" => "Dispatch PDF Not Generate!!");
			}
		} else {
			return array("ack" => 0, "developer_msg" => "Dispatch Id Required!!", "ack_msg" => "Dispatch Id Required!!");
		}
	}
}
?>