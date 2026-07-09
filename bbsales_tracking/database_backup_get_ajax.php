<?php
$page_id=406;$page_slug='page_admin';
include("connect.php");

$main_page 	= "utility";
$page 		= "database_backup";
$page_title = "Database Backup";
$Path = SITEURL."/".ADMINFOLDER."/fonts/collection/";

/* backup the db OR just a table */
function backup_tables($host,$user,$pass,$name,$tables = '*')
{
	
	$link = mysqli_connect($host,$user,$pass);
	mysqli_select_db($name,$link);
	
	//get all of the tables
	if($tables == '*')
	{
		
		$tables = array();
		$result = mysqli_query('SHOW TABLES');
		while($row = mysqli_fetch_row($result))
		{
			$tables[] = $row[0];
		}
	}
	else
	{
		$tables = is_array($tables) ? $tables : explode(',',$tables);
	}
	
	//cycle through
	foreach($tables as $table)
	{
		$result = mysqli_query('SELECT * FROM '.$table);
		$num_fields = mysqli_num_fields($result);
		
		//$return.= 'DROP TABLE '.$table.';';
		$row2 = mysqli_fetch_row(mysqli_query('SHOW CREATE TABLE '.$table));
		$return.= "\n\n".$row2[1].";\n\n";
		
		for ($i = 0; $i < $num_fields; $i++) 
		{
			while($row = mysqli_fetch_row($result))
			{
				$return.= 'INSERT INTO '.$table.' VALUES(';
				for($j=0; $j<$num_fields; $j++) 
				{
					$row[$j] = addslashes($row[$j]);
					$row[$j] = ereg_replace("\n","\\n",$row[$j]);
					if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
					if ($j<($num_fields-1)) { $return.= ','; }
				}
				$return.= ");\n";
			}
		}
		$return.="\n\n\n";
	}
	
	//save file
	$time = time();
	$fileName 	= $time.'.sql';
	$zipfileName= $time.'.zip';
	$mysqliExportPath = "fonts/collection/".$fileName;
	$handle = fopen($mysqliExportPath,'w+');
	fwrite($handle,$return);
	fclose($handle);
	
	/**************************Zip File Creation****************************/
	$zip = new ZipArchive();
	$filename = "fonts/collection/".$time.".zip";
	if($zip->open($filename, ZIPARCHIVE::CREATE) !== TRUE) {
		exit("cannot open <$filename>n");
	}
	$zip->addFile($mysqliExportPath , $time.'.sql');
	$zip->close();
	@unlink($mysqliExportPath);
	/**************************Zip File Creation***************************/
	
	return $zipfileName;
}
if(isset($_POST['saveDB'])){
	
	$fileName = backup_tables('localhost','root','','craftbox_omkar');
	$dateDownload = date('Y-m-d H:i:s');
	
	$values = array($dateDownload,$fileName);
	$rows = array("createDate","fileUrl");
	$ps = $db->rp_insert("dbbackup",$values,$rows,0);
	$db->rp_location("database_backup.php?msg=4");	
}
if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="delete"){
	
	$where = " id =".$_REQUEST['id'];
	$del_r = $db->rp_getData("dbbackup","*",$where);
	$del_d = mysqli_fetch_array($del_r);
	$filename = $del_d['fileUrl'];
	if($filename!="" && file_exists("fonts/collection/".$filename)){
		unlink("fonts/collection/".$filename);
	}
	$where = " id='".$_REQUEST['id']."'";
	$db->rp_delete("dbbackup",$where);
	
	$db->rp_location("database_backup.php?msg=3");
	
}
$ctable_where="";
if(isset($_REQUEST['ToDate']) && $_REQUEST['ToDate']!="" && $_REQUEST['ToDate']!=NULL)
{
$todate="To Date ".$_REQUEST['ToDate'];
 $ctable_where .= "DATE(createDate)<='".date_format(date_create($_REQUEST['ToDate']),"Y-m-d")."' ";
}

if(isset($_REQUEST['FromDate']) && $_REQUEST['FromDate']!="" && $_REQUEST['FromDate']!=NULL)
{
$fromdate="From Date ".$_REQUEST['FromDate'];
 $ctable_where .= " AND DATE(createDate)>= '".date_format(date_create($_REQUEST['FromDate']),"Y-m-d")."' ";
}

//$ctable_r = $db->rp_getData($ctable,"*",$ctable_where,"id DESC limit $page_position, $item_per_page",0);
$scheck_res = $db->rp_getData("dbbackup","*",$ctable_where,"",0);
?>
    <form action="" name="frm" id="frm" method="POST">
                                    <table id="example1" class="table table-bordered table-striped dataTable">
                                        <thead>
                                            <tr>
                                            	<th>No.</th>
                                                <th>Backup Date</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php
										if(mysqli_num_rows($scheck_res)>0){
											$count = 0;
											
											while($scheck_res_d = mysqli_fetch_array($scheck_res)){
												$count++;
										?>
                                            <tr>
                                            	<td><?php echo $count; ?></td>
                                                <td>
													<?php echo $scheck_res_d['createDate']; ?>
                                                </td>
                                                <td>
												
                                                	<a href="<?php echo $Path.$scheck_res_d['fileUrl']; ?>" download class="btn btn-info btn-sm">Download</a>
                                                    <input type="button" class="btn btn-danger btn-sm" onClick="del_conf('<?php echo $scheck_res_d['id']; ?>');" value="Delete">
                                                </td>
                                                
                                            </tr>
                                    	<?php
            }
			
        }
      else{
			?>
			<tr>
			<td colspan="6"><p style="text-align:center;">No data available in table</p></td>
			</tr>
			
			<?php
		}
        ?>
										</tbody>
                                    </table>
                                    <input type="submit" value="New Backup" class="btn btn-success btn-sm" name="saveDB">
                                </form>
                                <?php require_once 'disconnect.php';  ?>