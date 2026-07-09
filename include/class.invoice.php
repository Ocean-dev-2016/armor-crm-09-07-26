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

	public function DownloadInvoice($id)
	{
		//$customer_id=$this->db->rp_getValue("invoice_new","id","id='".$id."'",0);
		
		$uname	= str_replace(" ","-",stripslashes($this->db->rp_getValue("invoice_new","company_name","id='".$id."'",0)));
		$order_no	= str_replace("/","-",stripslashes($this->db->rp_getValue("invoice_new","invoice_no","id='".$id."'",0)));


		$uname=$this->db->rp_createSlug($uname);
		
		if($id){
			
			$count=$this->db->rp_getTotalRecord("invoice_new","id='".$id."'",0);
			
			if($count >0){
				$d=file_get_contents(ADMINSITEURL.'invoice_view_download.php?invoice_id='.$id.'&format_type=1');
		// echo "string";exit();
				//print_r($d); exit;
				//$d=file_get_contents(ADMINSITEURL.'order_view_new.php?order_id='.$order_id.'');
				//$d.=$string;
				require('../bbsales_tracking/mpdf60/mpdf.php');
				// echo $mpdf; exit();



				$mpdf = new mPDF('',    // mode - default ''

				 'A4',    // format - A4, for example, default ''

				 15,     // font size - default 0

				 'sans-serif',    // default font family

				 8,    // margin_left

				 8,    // margin right

				 8,     // margin top

				 8,    // margin bottom

				 0,     // margin header

				 0,     // margin footer

				 'P');  // L - landscape, P - portrait

				$mpdf->WriteHTML($d);
				// $exe_id = $db->rp_getValue("invoice_new","customer_id","isDelete=0 AND id='".$invoice_id."' ");

				/*LOG eNTRY*/
				$sales_id = $this->db->rp_getValue("invoice_new","sales_id","id='".$id."'",0);
				$sales_name = $this->db->rp_getValue("sales_executive","name","id='".$sales_id."'",0);
				$customer_id = $this->db->rp_getValue("invoice_new","customer_id","id='".$id."'");
				$invoice_no = $this->db->rp_getValue("invoice_new","invoice_no","id='".$id."'");

				$last_id = $order_id;
				$flag = "Application";
				$ctable = "invoice_new";
				$module_name = "Invoice";
				$log_description = $module_name." ".$order_no." PDF Download By ".$sales_name." ON ".date("Y-m-d H:i:s");
				$this->db->insertLog($ctable,$last_id,"insert","",$insert,0,$log_description,$flag,$module_name,$sales_id,$customer_id);

				/*LOG eNTRY*/

				$uname	= str_replace(" ","-",stripslashes($this->db->rp_getValue("invoice_new","company_name","id='".$id."'",0)));
				$order_no	= str_replace("/","-",stripslashes($this->db->rp_getValue("invoice_new","invoice_no","id='".$id."'",0)));

				// $fileName = "Invoice".SITENAME."_".date('d_m_Y')."_".$order_no."_".$uname.'.pdf'; 
				$fileName = $uname."_".date('d_m_Y')."_"."Invoice_".$order_no.'pdf';   
			//echo $fileName;exit;


				if(!is_dir($fileName)){

					mkdir(ORDERS_PDF.$fileName);

				}

				$pdf_file_path	= ORDERS_PDF.$fileName."/".$fileName.'.pdf';

				if(file_exists($pdf_file_path)){

					unlink($pdf_file_path);

				}

				$mpdf->Output($pdf_file_path);

				
				$file_path = $pdf_file_path;
				// echo $file_path;exit;
				$result=array();
				$result['pdf']=ADMINSITEURL."pdf/orders/".$fileName."/".$fileName.'.pdf';


				$reply=array("ack"=>1,"developer_msg"=>"Invoice Generate Successfully","ack_msg"=>"Invoice Generate Successfully","result"=>$result);
				// echo $reply;exit;
				return $reply;
			}
			else{
				$reply=array("ack"=>0,"developer_msg"=>"Invoice Not Generate!!","ack_msg"=>"Invoice Not Generate!!");
				return $reply;
			}
		}
		else{
			$reply=array("ack"=>0,"developer_msg"=>"Invoice No Require!!","ack_msg"=>"Invoice No Require!!");
			return $reply;
		}
	}

	public function DownloadPackingSlip($id)
	{
		//$customer_id=$this->db->rp_getValue("invoice_new","id","id='".$id."'",0);
			
		if($id){
			
			$count=$this->db->rp_getTotalRecord("packing_slip","id='".$id."'",0);
			
			if($count >0){
			
				//print_r($d); exit;
				//$d=file_get_contents(ADMINSITEURL.'order_view_new.php?order_id='.$order_id.'');
				//$d.=$string;
				$d=file_get_contents(ADMINSITEURL.'packing_slip_download.php?id='.$id.'');
				//$d.=$string;
				require('../bbsales_tracking/mpdf60/mpdf.php');

				$mpdf = new mPDF('',    // mode - default ''

				 'A4',    // format - A4, for example, default ''

				 15,     // font size - default 0

				 'sans-serif',    // default font family

				 3,    // margin_left

				 3,    // margin right

				 3,     // margin top

				 3,    // margin bottom

				 0,     // margin header

				 0,     // margin footer

				 'P');  // L - landscape, P - portrait

				$mpdf->WriteHTML($d);
				$exe_id = $this->db->rp_getValue("packing_slip","customer_id","isDelete=0 AND id='".$dispatch_id."' ");
				/*LOG ENTRY*/
				$sales_id = $this->db->rp_getValue("packing_slip","sales_id","id='".$id."'",0);
				$sales_name = $this->db->rp_getValue("sales_executive","name","id='".$sales_id."'",0);
				$customer_id = $this->db->rp_getValue("packing_slip","customer_id","id='".$id."'");
				$packing_slip_no = $this->db->rp_getValue("packing_slip","packing_slip_no","id='".$id."'");
				$last_id = $id;
				$flag = "Application";
				$ctable = "packing_slip";
				$module_name = "Packing Slip";
				$log_description = $module_name." ".$packing_slip_no." PDF Download By ".$sales_name." ON ".date("Y-m-d H:i:s");
				$this->db->insertLog($ctable,$last_id,"insert","",$insert,0,$log_description,$flag,$module_name,$sales_id,$customer_id);

				/*LOG ENTRY*/
				$uname	= str_replace(" ","-",stripslashes($this->db->rp_getValue("executive","company_name","id='".$exe_id."'",0)));
				$packing_slip_no	= str_replace("/","-",stripslashes($this->db->rp_getValue("packing_slip","packing_slip_no","id='".$id."'",0)));

				// $fileName = "Packing_Slip_".SITENAME."_".date('d_m_Y')."_".$dis_no."_".$uname.'.pdf';
				$fileName = $uname."_".date('d_m_Y')."_"."Packing_Slip_".$packing_slip_no.'pdf';   
				 

				if(!is_dir($fileName)){

					mkdir(DISPATCH_PDF.$fileName);

				}

				$pdf_file_path	= DISPATCH_PDF.$fileName."/".$fileName.'.pdf';

				if(file_exists($pdf_file_path)){

					unlink($pdf_file_path);

				}

				$mpdf->Output($pdf_file_path);
				$file_path = $pdf_file_path;
				
				// echo $file_path;exit;
				$result=array();
				$result['pdf']=ADMINSITEURL."pdf/dispatch/".$fileName."/".$fileName.'.pdf';


				$reply=array("ack"=>1,"developer_msg"=>"Invoice Generate Successfully","ack_msg"=>"Invoice Generate Successfully","result"=>$result);
				// echo $reply;exit;
				return $reply;
			}
			else{
				$reply=array("ack"=>0,"developer_msg"=>"Invoice Not Generate!!","ack_msg"=>"Invoice Not Generate!!");
				return $reply;
			}
		}
		else{
			$reply=array("ack"=>0,"developer_msg"=>"Invoice No Require!!","ack_msg"=>"Invoice No Require!!");
			return $reply;
		}
	}

	public function DownloadDispatch($id)
	{
		//$customer_id=$this->db->rp_getValue("invoice_new","id","id='".$id."'",0);
			
		if($id){
			
			$count=$this->db->rp_getTotalRecord("dispatch_detail","id='".$id."'",0);
			
			if($count >0){
			
				//print_r($d); exit;
				//$d=file_get_contents(ADMINSITEURL.'order_view_new.php?order_id='.$order_id.'');
				//$d.=$string;
				$d=file_get_contents(ADMINSITEURL.'dispatch_format_download.php?id='.$id.'');
//$d.=$string;
				require('../bbsales_tracking/mpdf60/mpdf.php');


				$mpdf = new mPDF('',    // mode - default ''

				 'A4',    // format - A4, for example, default ''

				 15,     // font size - default 0

				 'sans-serif',    // default font family

				 3,    // margin_left

				 3,    // margin right

				 3,     // margin top

				 3,    // margin bottom

				 0,     // margin header

				 0,     // margin footer

				 'P');  // L - landscape, P - portrait

				$mpdf->WriteHTML($d);


				$sales_id = $this->db->rp_getValue("dispatch_detail","sales_id","id='".$id."'",0);
				$sales_name = $this->db->rp_getValue("sales_executive","name","id='".$sales_id."'",0);
				$customer_id = $this->db->rp_getValue("dispatch_detail","customer_id","id='".$id."'");
				$dispatch_no = $this->db->rp_getValue("dispatch_detail","dispatch_no","id='".$id."'");

				$last_id = $id;
				$flag = "Application";
				$ctable = "dispatch_detail";
				$module_name = "Dispatch";
				$log_description = $module_name." ".$dispatch_no." PDF Download By ".$sales_name." ON ".date("Y-m-d H:i:s");
				$this->db->insertLog($ctable,$last_id,"insert","",$insert,0,$log_description,$flag,$module_name,$sales_id,$customer_id);

				$uname	= str_replace(" ","-",stripslashes($this->db->rp_getValue("dispatch_detail","company_name","id='".$id."'",0)));
				$dis_no	= str_replace("/","-",stripslashes($this->db->rp_getValue("dispatch_detail","dispatch_no","id='".$id."'",0)));

				// $fileName = "Dispatch_Order_".SITENAME."_".date('d_m_Y')."_".$dis_no."_".$uname.'.pdf';  
				$fileName = $uname."_".date('d_m_Y')."_"."Dispatch_Order_".$dis_no.'pdf'; 
				 

				if(!is_dir($fileName)){

					mkdir(DISPATCH_PDF.$fileName);

				}

				$pdf_file_path	= DISPATCH_PDF.$fileName."/".$fileName.'.pdf';

				if(file_exists($pdf_file_path)){

					unlink($pdf_file_path);

				}

				$mpdf->Output($pdf_file_path);

				$file_path = $pdf_file_path;
				
				// echo $file_path;exit;
				$result=array();
				$result['pdf']=ADMINSITEURL."pdf/dispatch/".$fileName."/".$fileName.'.pdf';


				$reply=array("ack"=>1,"developer_msg"=>"Dispatch PDF Generate Successfully","ack_msg"=>"Dispatch PDF Generate Successfully","result"=>$result);
				// echo $reply;exit;
				return $reply;
			}
			else{
				$reply=array("ack"=>0,"developer_msg"=>"Dispatch PDF Not Generate!!","ack_msg"=>"Dispatch PDF Not Generate!!");
				return $reply;
			}
		}
		else{
			$reply=array("ack"=>0,"developer_msg"=>"Invoice No Require!!","ack_msg"=>"Invoice No Require!!");
			return $reply;
		}
	}
}

?>