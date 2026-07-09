<?php
class Admin extends Functions
{
	/*
		*** Cart Function List By Ravi Patel :) <<<
			-> getAddButton()
				- get Add Button for manage page
			-> getUpdateButton()
				- get Update Button for manage page
	*/
	public function getAddButton($ctable,$type="",$url=null)
    {
		$rights=$_SESSION['rights'];	
		if($ctable!="" && $rights['insert_flag']==1 ){
			if($url!=null){
				?>
				<div class="btn-group">
					<a class="btn sbold blue-ebonyclay" href="<?php echo $url; ?>"> Add New
						<i class="fa fa-plus"></i>
					</a>
				</div> 
				<?php
			}else{
				?>
				<div class="btn-group">
					<a class="btn sbold blue-ebonyclay" href="<?php echo $ctable; ?>_crud.php?mode=add&c_type=<?php echo $type; ?>"> Add New
						<i class="fa fa-plus"></i>
					</a>
				</div>
				<?php
			}
		}	
    }
	public function checkUserPermission($page_id,$user_id,$permission_type)
	{	
		$user_type=$this->getUserType($user_id);
		if($user_type==0)
		{
			return true;
		}
		$flag=array("view"=>"view_flag","insert"=>"insert_flag","update"=>"update_flag","delete"=>"delete_flag");
		// need to add permission type validation
		if(array_key_exists($permission_type,$flag))
		{ 
			$ack=$this->rp_getValue("page_admin_right","".$flag[$permission_type]."","page_id='".$page_id."' AND admin_id='".$user_type."'",0);
			if($ack==1)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
		
	}
	public function getUserType($user_id)
	{
		
		if(!isset($_SESSION[SITE_SESS.'_ADMIN_TYPE']))
		{
			return $this->rp_getValue(CTABLE_ADMIN,"admin_type","id='".$user_id."'");
		}
		else
		{
			return $_SESSION[SITE_SESS.'_ADMIN_TYPE'];
		}
	}
	public function getUpdateButton($frmId=null)
    {
		if($frmId!=null && $rights['update_flag']==1){
			?>
			<button class="btn btn-primary btn-flat sidebar" onClick="document.<?php echo $frmId; ?>.submit();">Update</button>
			<?php
		}else{
			?>
			<button class="btn btn-primary btn-flat sidebar" onClick="document.frm.submit();">Update</button>
			<?php
		}
    }
	public function getAddApplicationPageButton()
	{
		?>
		<a class="btn btn-primary btn-flat sidebar" href="page_table_manage.php" >Application Pages</a>
		<?php
	}
	public function getLabel($content,$href,$type)
	{
		
		$label_type=array("danger"=>"label label-danger","success"=>"label label-success","warning"=>"label label-warning","info"=>"label label-info","default"=>"label label-default");
		if($type=='auto')
		{
			$header=$this->checkPageResponse($href);
			if($header=='200')
			{
				$class=$label_type['success'];
			}
			else if($header=='302')
			{
				$class=$label_type['success'];
			}
			else if($header=='404')
			{
				$class=$label_type['danger'];
			}
			else
			{
				$class=$label_type['info'];
			}
		}
		else if($type=='random')
		{
			$key = array_rand($label_type);
			$class = $label_type[$key];
		}
		else
		{
			if(array_key_exists($type,$label_type))
			{
				$class=$label_type[$type];
			}
			else
			{
				$class=$label_type['default'];
			}
		}
		
		
		return '<a class="'.$class .' col-sm-12" style="margin-top:10px" href="'. $href.'" >'.$content.'</a>';
		
	}
	public function rp_paginate_function($item_per_page, $current_page, $total_records, $total_pages)
	{
		$pagination = '';
		if($total_pages > 0 && $total_pages != 1 && $current_page <= $total_pages){ //verify total pages and current page number
			$right_links    = $current_page + 3; 
			$previous       = $current_page - 3; //previous link 
			$next           = $current_page + 1; //next link
			$first_link     = true; //boolean var to decide our first link
			
			if($current_page > 1){
				$previous_link = ($previous<=0)?1:$previous;
				$pagination .= '<li class="paginate_button "><a href="#" aria-controls="datatable1" data-page="1" title="First">&laquo;</a></li>'; //first link
				$pagination .= '<li class="paginate_button "><a href="#" aria-controls="datatable1" data-page="'.$previous_link.'" title="Previous">&lt;</a></li>'; //previous link
					for($i = ($current_page-2); $i < $current_page; $i++){ //Create left-hand side links
						if($i > 0){
							$pagination .= '<li class="paginate_button "><a href="#"  data-page="'.$i.'" aria-controls="datatable1" title="Page'.$i.'">'.$i.'</a></li>';
						}
					}   
				$first_link = false; //set first link to false
			}
			
			if($first_link){ //if current active page is first link
				$pagination .= '<li class="paginate_button active"><a aria-controls="datatable1">'.$current_page.'</a></li>';
			}elseif($current_page == $total_pages){ //if it's the last active link
				$pagination .= '<li class="paginate_button active"><a aria-controls="datatable1">'.$current_page.'</a></li>';
			}else{ //regular current link
				$pagination .= '<li class="paginate_button active"><a aria-controls="datatable1">'.$current_page.'</a></li>';
			}
					
			for($i = $current_page+1; $i < $right_links ; $i++){ //create right-hand side links
				if($i<=$total_pages){
					$pagination .= '<li class="paginate_button "><a href="#" aria-controls="datatable1" data-page="'.$i.'" title="Page '.$i.'">'.$i.'</a></li>';
				}
			}
			if($current_page < $total_pages){ 
				$next_link = ($i > $total_pages)? $total_pages : $i;
				$pagination .= '<li class="paginate_button "><a href="#" aria-controls="datatable1" data-page="'.$next_link.'" title="Next">&gt;</a></li>'; //next link
				$pagination .= '<li class="paginate_button "><a href="#" aria-controls="datatable1" data-page="'.$total_pages.'" title="Last">&raquo;</a></li>'; //last link
			}
		}
		return $pagination; //return pagination links
	}
	function checkPageResponse( $url )
	{
		
		$post=["skip_security"=>1224];
		 if($url == NULL) return false;  
    $ch = curl_init($url);  
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);  
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);  
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);	
	curl_setopt( $ch, CURLOPT_POSTFIELDS, $post );
	
 $data = curl_exec($ch);  
  $redirectURL = curl_getinfo($ch,CURLINFO_EFFECTIVE_URL );
	$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);  
    curl_close($ch);	
        return $httpcode;  
    /* file_get_contents($url);		
		foreach( $http_response_header as $k=>$v )
		{
			$t = explode( ':', $v, 2 );
			if( isset( $t[1] ) )
				$head[ trim($t[0]) ] = trim( $t[1] );
			else
			{
				$head[] = $v;
				if( preg_match( "#HTTP/[0-9\.]+\s+([0-9]+)#",$v, $out ) )
					$head['reponse_code'] = intval($out[1]);
			}
		}
		return $head;*/
	}
	
	public function getAdminDetail($admin_id)
	{
		if($admin_id!="")
		{
			$admin_resource=$this->rp_getData(CTABLE_ADMIN,"*","id='".$admin_id."'","",0);
			if($admin_resource)
			{
				$admin_detail=mysqli_fetch_assoc($admin_resource);
				// Admin Type
				$admin_type=$admin_detail['type'];
				$admin_type_title=$this->rp_getValue("admin_type","name","id='".$admin_type."'");
				
				// Department ID and Name
				$admin_department_id=$this->rp_getValue("admin_type","department_id","id='".$admin_type."'");
				$admin_department_name=$this->rp_getValue("department","name","id='".$admin_department_id."'");
				
				// Process
				$admin_process=array();
				$admin_process_r=$this->rp_getData("department_map_process","*","department_id='".$admin_department_id."'");
				if($admin_process_r)
				{
					while($process=mysqli_fetch_assoc($admin_process_r))
					{
						$process['name']=$this->rp_getValue("production_process","process_name","id='".$process['id']."'");
						$admin_process[]=$process;
					}
				}
				
				$admin_detail['type_slug']=$admin_type;
				$admin_detail['type']=$admin_type_title;
				$admin_detail['department_id']=$admin_department_id;
				$admin_detail['admin_process']=$admin_process;
				return $admin_detail;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	public function checkRightFlag($flag){
		if(isset($_SESSION['rights']) && array_key_exists($flag,$_SESSION['rights']) && $_SESSION['rights'][$flag]==1)
		{
			// Access Granted
		}
		else
		{
			$this->rp_location('access_denied.php?msg=access_denied');
		}
	}
	
	public function getMessageBlock()
    {
       
        ?>
        <div class="row">
            <div class="col-md-12">
				<?php $this->printErrorMessage(); ?>
			   <?php $this->printSuccessMessage(); ?>
            </div>
		</div>
        <?php

    }
}
?>