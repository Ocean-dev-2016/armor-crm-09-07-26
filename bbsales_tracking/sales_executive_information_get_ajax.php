<?php
$page_id=556;$page_slug='page_sales_executive';
/*
 * @author Ravi Patel
 */
include("connect.php");
$ctable_where	= "id='".$_REQUEST['id']."' AND isDelete=0 ";
$uid = $_REQUEST['id'];
$ctable_r = $db->rp_getData("sales_executive","*",$ctable_where,"",0);
?>
<div id="print_info">
<div class="row">
<div class="col-sm-12">
			<h4><b>Personal Detail</b><b>-<?php echo $name = $db->rp_getValue("sales_executive","name","id='".$_REQUEST['id']."' AND isDelete=0",0);  ?></b></h4>
			<table id="datatable_1" class="table table-striped table-bordered table-hover">
			<tbody>
			<?php
        if(mysqli_num_rows($ctable_r)>0){
            $count = 0;
            
            while($ctable_d = mysqli_fetch_array($ctable_r)){
                $count++;
        ?>
			<tr>
			<th>Name</th>
			<td><?php echo $ctable_d['name'];  ?></td>
			<th>Sales Excecutive Type</th>
			<td><?php echo  $ctable_d['type']; ?></td>
			</tr>
			
			<tr>
			<th>Phone</th>
			<td><?php echo  $ctable_d['phone']; ?></td>									
			<th>Email</th>
			<td><?php echo  $ctable_d['email'];  ?></td>
			</tr>
			
			<tr>
			<th>Address</th>
			<td><?php echo  $ctable_d['address']; ?></td>
			<th>Pin Code</th>
			<td><?php echo  $ctable_d['zip']; ?></td>
			</tr>
			
			<tr>
			<th>Country</th>
			<td><?php echo  $ctable_d['country']; ?></td>
			<th>State</th>
			<td><?php echo  $ctable_d['state']; ?></td>
			</tr>
			
			<tr>
			<th>City</th>
			<td><?php echo  $ctable_d['city']; ?></td>
			<!-- <th>IMEI</th> -->
			<!-- <td><?php echo  $ctable_d['imei']; ?></td> -->
			<th></th>
			<td></td>
			</tr>
			<?php
				}
			}
			?>
			</tbody>
			</table>
			</div>

			</div>
			
</div>
<div class="row">
	<div class="col-md-2">
		<a onClick="printPDF()" class="btn btn-info" title="print"><i class="fa fa-print"></i>Print</a>
	</div>
	<div class="col-md-2">
		<a class="btn btn-info" onClick="genReport('<?php echo $_REQUEST['id']; ?>')" title="Edit"><i class="fa fa-file-pdf-o"></i>Export</a>
	</div>
</div>
<script type="text/javascript">
	function genReport(){
    	var searchName     = $("#searchName").val();
      	searchName     	   = searchName.trim();
      	// searchName     	   = encodeURIComponent(searchName.trim());
      	var state          = $("#state").val();
      	var city          = $("#city").val();
      	var zone          = $("#zone").val();
      	var id          = "<?php echo $_REQUEST['id']; ?>"
      	$.ajax({
	        method: "POST",
	        url: "sales_executive_information_excel.php",
	        data:{
        		searchName:searchName,
				state:state,
				city:city,
				zone:zone,
				id:id
			},	
			dataType : 'json',
			beforeSend: function()
			{
				// $("#loading-modal").modal('show');
				$('.preloader').fadeIn('slow');
			},
        	success: function(result){
        		// $("#loading-modal").modal('hide');
        		$('.preloader').fadeOut('slow');
        		window.location.href="<?=SITEURL?>"+result.file_path;
        	},
			/*error:function(result){
				window.location.href="<?=SITEURL?>"+result.file_path;
			}*/
    	});
    }

    function printPDF() {

    	var searchName     = $("#searchName").val();
      	searchName     	   = encodeURIComponent(searchName.trim());
      	var state          = $("#state").val();
      	var city          = $("#city").val();
      	var id          = "<?php echo $_REQUEST['id']; ?>"

    	var myWindow = window.open('print_sales_executive_information_ajax.php?searchName='+searchName+ "&id=" + id + "&state=" + state + "&city=" + city + "&zone=" + zone ,'','width=700,height=800');
     	myWindow.print();
  //    	setTimeout(function () 
		// {
		// 	myWindow.print();
		// 	var ival = setInterval(function() 
		// 	{
		// 	    myWindow.close();
		// 	    clearInterval(ival);
		// 	}, 200);
		// }, 500);
    }
</script>
<?php require_once("disconnect.php"); ?>