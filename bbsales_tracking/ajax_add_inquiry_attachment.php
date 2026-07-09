<?php
$page_id=400;$page_slug='dashboard';
require_once("connect.php");
$insertid = "";
if($_POST['mode']=="add_inquiry_attachment")
{
	if (isset($_FILES["file_path"]))
    {
    	// $allowedExts = array("jpg","jpeg","png","gif","JPG","JPEG","doc","xlsx","docx","pdf","csv");
        $allowedExts = array("jpg","jpeg","png","gif","JPG","JPEG");
    	$temp = explode(".", $_FILES["file_path"]["name"]);
    	$extension = end($temp);
    	$fileName 	= $db->clean($_FILES["file_path"]["name"]);	
    	if($fileName!="")
    	{
    		$fileSize 	= round($_FILES["file_path"]["size"]); // BYTES
    		$adate 		= date('Y-m-d H:i:m');
    		$extension	= end(explode(".", $fileName));
    		if(!in_array($extension,$allowedExts))
    		{
    			$file_error=true;
    		}
	    	$image_path	= 'inquiry_document_'.substr(sha1(time()), 0, 6).".".$extension;
	    	$filePath 	= INQUIRY_ATTACH_IMAGE_A.$image_path;
	    	$_FILES['file_path']['tmp_name'];
	    	move_uploaded_file($_FILES['file_path']['tmp_name'], $filePath);
	    	$new_image=true;
    	}
    	else
    	{
    		$image_path="";
    	}
    }
    else
    {
    	$new_image=false;
    	$image_path="";
    }
    $rows=array("inquiry_id","image_path");
	$values=array($_POST['inquiry_id'],$image_path);
	$insertid=$db->rp_insert("no_order_inquiry_attachment",$values,$rows,0);
    echo $insertid;
}
else if($_POST['mode']=='get_attachment')
{
    ?>
        <div class="portlet-body" style="margin-top: 20px;">
            <?php
            $QuickNotesR = $db->rp_getData("no_order_inquiry_attachment","*","inquiry_id='".$_POST['id']."' AND isDelete=0","id DESC",0);
            ?>
            <table class="table table-striped table-hover table-bordered" id="quick_notes_table_id">
                <thead>
                    <tr>
                        <th>File Name</th>
                        <th class="text-center">Download / View Attachment</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if($QuickNotesR)
                    {
                        while($QuickNotesD = mysqli_fetch_array($QuickNotesR)) 
                        {
                            if($QuickNotesD['image_path']!="")
                            {
                                $file_path = ADMINSITEURL.INQUIRY_ATTACH_IMAGE_A.$QuickNotesD['image_path'];
                            } 
                            else
                            {
                                $file_path = "";
                            }
                            ?>
                            <tr class="">
                                <?php
                                if($QuickNotesD['image_path']!="")
                                { 
                                    ?>
                                    <td><?= $QuickNotesD['image_path']; ?></td>
                                    <td class="text-center">
                                        <a href="<?= $file_path ?>" download  class="text-warning" title="View"><i class="fa fa-download" style="font-size: 15px;"></i></a>&nbsp;&nbsp;&nbsp;
                                        <a href="<?= $file_path ?>" target="_blank"  class="text-sucess" title="View"><i class="fa fa-eye" style="font-size: 15px;"></i></a>
                                    </td>
                                    <?php
                                } 
                                else
                                { 
                                    ?>
                                    <td></td>
                                    <?php
                                }
                                ?> 
                            </tr>
                            <?php
                        }
                    } 
                    else
                    {
                        ?>
                        <tr><td colspan="4" class="text-center">No data found!!</td></tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    <?php
}
require_once 'disconnect.php'; 
?>