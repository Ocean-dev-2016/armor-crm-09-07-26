<?php
$page_id="17";
require_once('connect.php');
$ctable="api_table";
$ctable_where = "";
// Get the total number of rows in the table


if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
    $ctable_where .= " (
                            api_title like '%".$_REQUEST['searchName']."%' OR 
							api_slug like '%".$_REQUEST['searchName']."%' OR
							id like '%".$_REQUEST['searchName']."%'
                        ) AND ";
}

$ctable_where .= " isDelete=0";
//status

$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"]) ) ? intval($_REQUEST["show"]) : 500;

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

$api_r = $db->rp_getData($ctable,"*",$ctable_where,"id ASC limit $page_position, $item_per_page",0);

?>

<html>
<head>

<style type="text/css">
.mainDiv, table{
    height: auto;
    width:190mm;
	font-family: Calibri,sans-serif;
    font-style: normal;
    font-weight: 400;
    padding: 0;
    text-decoration: none;
	font-size: 8pt;
	margin:auto;
	padding:auto;
}
table , td, th {
	border: 1px solid #595959;
	border-collapse: collapse;
}

    }
.bolder
{
	font-weight: 700 !important;
}
.center
{
	text-align:center;

}
.font8{	font-size: 8pt !important;}
.righter
{
	text-align:right;

}
td, th {
	padding: 3px;
	height:23px;
	text-align:left;
	vertical-align:middle;
	color: black;
    font-family: Calibri,sans-serif;
    font-style: normal;
    font-weight: 400;
    text-decoration: none;
	font-size: 8pt;
	
}
th {
	//background: #f0e6cc;
}

.title {
	//background: #f0f0f0;
}
.odd {
	//background: #fefcf9;
}
input{
	height:auto;
	width:100%;
	margin:0px !import.
	
	
}
.header_logo
{
	background-image: url("lakhmi.jpg");
    background-position: center center;
    background-repeat: no-repeat;
    background-size: contain;
    height: 161px;
    width: 190mm;
}
 
</style>
<script>
document.find('td').contentEditable = true;
</script>
</head>

<body>
<div class="mainDiv">

<table id="design_demo" style="overflow: wrap" >
	<tbody style="padding:10px;">
	    <colgroup width="75"/>
		    <colgroup width="75"/>
			    <colgroup width="75"/>
				    <colgroup width="75"/>
					    <colgroup width="75"/>
						    <colgroup width="75"/>
							    <colgroup width="75"   />
								    <colgroup width="75"   />
									    <colgroup width="75"   />
										    
		<tr>
			<td colspan="9">
			<h1>&nbsp;<?php echo  SITETITLE." APIs";?></h1>
			</td>
		</tr>
		<tr>
			<td colspan="9">
			</td>
		
		</tr>
		<?php

			$count=1;
			while($api=mysqli_fetch_array($api_r)){
			
			?>
			<tr>
			<td colspan="6">
			<h4><?php echo $count;?>.<?php echo $api['api_title']?></h4>
			</td>
			<td colspan="3" style="border-left:hidden;">
			
			<a class="btn btn-info btn-sm" onClick="window.location.href='api_crud.php?mode=e&id=<?php echo $api['id'];?>'" title="Edit"><i class="fa fa-pencil"></i></a>
			<a class="btn btn-danger btn-sm" onClick="del_conf('<?php echo $api['id']; ?>');" title="Delete" style="margin-left:15px;"><i class="fa fa-trash"></i></a>
			<a  data-toggle="collapse" data-target="#<?php echo "P".$api['id']; ?>" class="accordion-toggle btn btn-success btn-sm" style="margin-left:15px;"><i class="fa fa-eye "></i>&nbsp;<i class="fa fa-chevron-down "></i></a>
			</td>
		
		</tr>
		<tr>
			<td colspan="9" class="hiddenRow" style="padding:0px">
					
				<div class="accordian-body collapse" id="<?php echo "P".$api['id']; ?>" >
					<table style="border-left:hidden;border-right:hidden;border-top:hidden;border-bottom:hidden;">
						<tr>
							<td colspan="1"><b>API Code</b>		
								
							</td>
							<td colspan="3"><?php echo $api['id']?></td>
							<td colspan="1"><b>API Slug</b>		
								
							</td>
							<td colspan="4"><?php echo $api['api_slug']?></td>
							
						</tr>
						<tr>
							<td colspan="1"><b>Url</b>			
							</td>
							<td colspan="8"><a target="_blank" href="<?php echo SITEURL."service/".$api['api_url']?>"><?php echo ($api['api_url']!="")?SITEURL."service/".$api['api_url']:"#"; ?></a></td>
						</tr>
						<tr>
							<td colspan="9" class=""><b>DETAILS OF APIs		</b>						
							</td>
						</tr>
						<tr>
							<td colspan="9" class=""><?php echo $api['api_description']?>						
							</td>
						</tr>
						<tr>
							<td colspan="9" class=""><b>Required Parameters OF API		</b>						
							</td>
						</tr>
						<?php  
			
							$api_url=explode("?",$api['api_url']);
							if(sizeof($api_url)==2)
							{
								$sub_count=0;
								$parmas=explode("&",$api_url[1]);
								foreach($parmas as $p)
								{
									?>
									<tr colspan="9">
									
									<td colspan="8" class="">
									<?php
									$key_value=explode("=",$p);
									echo $key_value[0]."=".$key_value[1]."<br>";
									
									?>
										</td>
										<?php if($sub_count==0){?>
											<td colspan="1" width="12%" class="center" rowspan="<?php echo sizeof($parmas);?>" >
											<h1><?php echo sizeof($parmas);?></h1>
										</td>
										<?php }?>
									</tr>
									<?php
									$sub_count++;
								}
							}
							else
							{
								?>
								<tr>
								<td colspan="8" class="">
								<?php				
								echo "Hurrah! No Parameters Required!!";	
								?>
								</td>
								</tr>
								<?php
							}	
							
						?>
					</table>
					
				</div>
			</td>
		</tr>
		<?php
			
			$count++;
		} ?>
	</tbody>
</table>		
<br/>
<!--<div class="row">
		<div class="col-md-6">
			<div class="dataTables_info"> Rows Limit:
				<select id="numRecords" onChange="changeDisplayRowCount(this.value);">
					<option value="100" <?php if ($_REQUEST["show"] == 100 || $_REQUEST["show"] == "" ) { echo ' selected="selected"'; }  ?> >100</option>
					<option value="200" <?php if ($_REQUEST["show"] == 200) { echo ' selected="selected"'; }  ?> >200</option>
					<option value="500" <?php if ($_REQUEST["show"] == 500) { echo ' selected="selected"'; }  ?> >500</option>
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
	</div>-->
	</div>
</body>
</html>