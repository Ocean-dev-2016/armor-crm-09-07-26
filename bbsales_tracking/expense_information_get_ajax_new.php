<?php
$page_id=592;$page_slug='expense_page';
/*
 * @author Ravi Patel
 */
include("connect.php");

include("../include/no_to_word.php");
$id=$_REQUEST['id'];
$expense_date=$db->rp_getValue("expense","DATE(expense_date)","id='".$_REQUEST['id']."' AND isDelete=0 AND isActive=1");
$se_id=$db->rp_getValue("expense","sales_executive_id","id='".$_REQUEST['id']."' AND isDelete=0 AND isActive=1");
// $ctable_where	= "id='".$_REQUEST['id']."' AND isDelete=0 AND isActive=1";
$ctable_where	= "DATE(expense_date)='".$expense_date."' AND sales_executive_id='".$se_id."' AND isDelete=0 AND isActive=1";
$ctable_r = $db->rp_getData("expense","*",$ctable_where,"",0);
$d="";
$discount="";
$imgExts = array("gif", "jpg", "jpeg", "png", "tiff", "tif");
?>
<style>
#datatable_1
{
	align:center;
}
#lightbox,#lightboxOverlay
{
	z-index: 999999;
}
</style>
<div id="print_info ">
	<div class="row">
		<div class="col-sm-12 ">
		<h4 align="center"><b>Expense Detail</b></h4>
			<table id="datatable_1"  style="border-collapse:collapse;" align="center" class="table table-striped table-bordered table-hover">
				<thead>
					<tr>
						<td style="border-right: none;">
							<button  type="button" name="sub" value="print"  onClick="autoapproveExpensesales('<?=$expense_date?>','<?=$se_id?>')" class="btn yellow btn-sm">Approve All</button>
						</td>
						<!-- <td style="border-right: none;">
							<button  type="button" name="sub" value="print"  onClick="autodisapproveExpensesales('<?=$expense_date?>','<?=$se_id?>')" class="btn red btn-sm">DisApprove All</button>
						</td> -->
						<td colspan="10"></td>
					</tr>
					<tr>
						<td colspan="6" style="border-right: none;"><b>EXECUTIVE</b> : <?= $sales=$db->rp_getValue("sales_executive","username","id='".$se_id."'",0); ?></td>				
						<td colspan="6" style="text-align: right;border-left: none;"><b>EXPENSE DATE</b> : <?php echo date('d-m-Y',strtotime($expense_date));?></td>
						
					</tr>			
					<tr>
						<th>Category Name</th>
						<th>Sub Category Name</th>
						<th>Total Kilometer</th>
						<th>Remark</th>
						<th>Expense Date Time</th>
						<th>Request Amount</th>
						<th>Passed Amount</th>
						<th>Attachment</th>
						<th>Entry Type</th>
						<th>Action</th>
						<th>Delete</th>

					</tr>
				</thead>
				<tbody>
				<?php
		        if(mysqli_num_rows($ctable_r)>0)
		        {
		            $count = 0;
		            $total=0;
		            $total_pass_amount=0;
		            $entry_type_status = array("1"=>"Admin Panel","2"=>"customer","3"=>"Web Sales",4=>"Web Customer",5=>"Sales App",6=> "Customer App");
		            while($ctable_d = mysqli_fetch_array($ctable_r))
		            {
		            	if($ctable_d['pass_expense_amount']>$ctable_d['total'])
		            	{
		            		$style = "style='background-color: #f11125;'";
		            	}
		            	else{
		            		$style = "";	
		            	}

		            	$cat_name = $db->rp_getValue("expence_category","name","id='".$ctable_d['category_id']."'",0);
		            	// $sales_id=$db->rp_getValue("sales_executive","username","id='".$se_id."'",0);
		            	$sales_type=$db->rp_getValue("sales_executive","type","id='".$se_id."'",0);

		                $count++;
		        		?>			
						<tr <?= $style ?>>
							<td><?php echo $db->rp_getValue("expence_category","name","id='".$ctable_d['category_id']."'",0); ?></td>
							<td><?php echo $db->rp_getValue("expence_sub_category","name","id='".$ctable_d['subcategory_id']."'",0); ?></td>
							<td>
								<?php 
								if($ctable_d['category_id']=='2' || $ctable_d['category_id']="-1")
								{
                                     
							 		echo "Start KM : " .$ctable_d['start_kilometer']."<br/> END KM : ".$ctable_d['end_kilometer']."<br/> TOTAL KM : ".$db->rp_num($ctable_d['total_kilometer']); 
								}
								else
								{
									echo "";
								}
								?>
							</td>
							<td ><?php echo $ctable_d['remark'];?></td>
							<td ><?php echo date("d-m-Y h:i:s A",strtotime($ctable_d['created_date']));?></td>
							<td align="right"><?php echo $db->rp_num($ctable_d['total']);?></td>
							<td align="right"><?php echo $db->rp_num($ctable_d['pass_expense_amount']);?></td>
							<td>
								<?php
								//if($ctable_d['image_path']!="" && file_exists(EXPENCE_A.$ctable_d['image_path'])){
								?>
									<!-- <img src="<?php //echo //EXPENCE_A.$ctable_d['image_path']; ?>" width="80%" height="30%"/> -->
								<?php
								// }else{
								// 	echo "No Image Available.";
								// }
								?>

								<?php 
								
									 $img = explode(",", $ctable_d['image_path']);
									$imgpath = array();
									 for ($i=0; $i < sizeof($img); $i++)
									 { 
										 $imgpath[] = SITEURL."resource/image/".$db->rp_getValue("media","url","reference_id='".$ctable_d["id"]."' AND id='".$img[$i]."'",0);
										
									 }
									
									 for ($i=0; $i < sizeof($imgpath); $i++)
									 {
									 	$urlExt = pathinfo($imgpath[$i], PATHINFO_EXTENSION);
										if (in_array($urlExt, $imgExts)) 
										{
											if($i==0)
											{
									 			?>
											 <a href="<?=$imgpath[$i]?>" data-lightbox="EXPENSE<?=$count?>" data-title="EXPENSE <?=$ctable_d['id']?>"><img src="<?=$imgpath[$i]?>" style="height: 80px;"></a>
									  			<?php 
											}
											else
											{
												?>
											 <div class="hidden">
													<a href="<?=$imgpath[$i]?>" data-lightbox="EXPENSE<?=$count?>" data-title="EXPENSE <?=$ctable_d['id']?>"><img src="<?=$imgpath[$i]?>" style="height: 80px;"></a>
												</div> 
											<?php
									 		}
									 	}
									 }
								?>
							</td>
							 <td><?php echo $entry_type_status[$ctable_d['entry_flag']]; ?></td>
							<td>
							<?php
							if($ctable_d['expense_status']=="0")
							{ 
								?>	
								<div>
							    	<input type="hidden" id="exp_id" value="<?php echo $ctable_d['id']; ?>">
			    					<select class="form-control expense_check<?php echo $ctable_d['id']."_".$count; ?>" name="expense_status" id="expense_status" onchange="getExpense(this.value,'<?= $ctable_d['id']?>')">
			    						<option value="">Select</option>
			    						<option value="1">Pass</option>
			    						<option value="2">Reject</option>
			    					</select>
								</div>
							
								<div>
			    					<label class="bd  book_date<?php echo $ctable_d['id']; ?>">Pass Amount</label>
			    					<input type="text" class="form-control num bd book_date<?php echo $ctable_d['id']; ?>" id="pass_expense_amount<?php echo $ctable_d['id']."_".$count; ?>" name="pass_expense_amount" onkeyup="payValidation(this.value,'<?= $ctable_d['id']."_".$count; ?>')">


			    					<input type="hidden" class="form-control num bd book_date<?php echo $ctable_d['id']; ?>" id="required_amount<?php echo $ctable_d['id']."_".$count; ?>" value="<?php echo $db->rp_num($ctable_d['total']); ?>" name="required_amount">


			    					
								</div>
							
								<div>
								    <label class="Re bbb<?php echo $ctable_d['id']; ?>">Enter Remark</label>
								    <textarea type="text" class="form-control Re bbb<?php echo $ctable_d['id']; ?>" id="pass_remark<?php echo $ctable_d['id']."_".$count; ?>" name="pass_remark"></textarea>

								    <input type="hidden" class="form-control num bd book_date<?php echo $ctable_d['id']; ?>" id="cat_name<?php echo $ctable_d['id']."_".$count; ?>" value="<?php echo $cat_name; ?>" name="cat_name">


								    <input type="hidden" class="form-control num bd book_date<?php echo $ctable_d['id']; ?>" id="sales_id<?php echo $ctable_d['id']."_".$count; ?>" value="<?php echo $se_id; ?>" name="sales_id">

								    <input type="hidden" class="form-control num bd book_date<?php echo $ctable_d['id']; ?>" id="sales_type<?php echo $ctable_d['id']."_".$count; ?>" value="<?php echo $sales_type; ?>" name="sales_type">

								    
								</div>

								<div>
								    <label class="Re bbb1<?php echo $ctable_d['id']; ?>">Enter Remark</label>
								    <textarea type="text" class="form-control Re bbb1<?php echo $ctable_d['id']; ?>" id="reject_remark<?php echo $ctable_d['id']."_".$count; ?>" name="reject_remark"></textarea>
								</div>
							
							
								<button style="margin-left: 56px;" type="submit" name="submit" value="print"  onClick="ApproveExpense('<?php echo $ctable_d["id"]."_".$count; ?>')" class="btn yellow btn-sm">Save</button>
								<?php
							} 
							else
							{
								$status = array(1=>"Pass", 2=>"Reject");
								$ctable_d['expense_status'] == $status;
								echo $status[$ctable_d['expense_status']];
							}
							?>
							</td>
							<td>
								<?php if($ctable_d['expense_status'] !=1 && $ctable_d['expense_status'] !=2){ ?>
								<button style="margin-right: -5px;" type="submit" name="submit" value="print"  onClick="DeleteExpense('<?php echo $ctable_d["id"]."_".$count; ?>')" class="btn red btn-sm">
									<i class="fa fa-trash" aria-hidden="true"></i>
								</button>
							<?php } ?>
							</td>
						</tr>
						<?php
						$total+=$ctable_d['total'];
						$total_pass_amount+=$ctable_d['pass_expense_amount'];
					}
				}
				else
				{
					?>
					<tr>
						<td colspan="8" style="text-align:center;">No Data Available</td>
					</tr>
					<?php
				}
				?>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="5" style="text-align: right;">Total</th>
						<th style="text-align: right;"><?= $db->rp_num($total); ?></th>
						<th style="text-align: right;"><?= $db->rp_num($total_pass_amount); ?></th>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-2">
		<a onClick="printPDF('<?php echo $_REQUEST['id']; ?>')" class="btn btn-info" title="print"><i class="fa fa-print"></i>Print</a>	
	</div>
	<div class="col-md-2">
		<a class="btn btn-info" onClick="genReport('<?php echo $_REQUEST['id']; ?>')" title="Edit"><i class="fa fa-file-pdf-o"></i>Export</a>
	</div>
	
</div>
<script type="text/javascript" src="js/jquery.numeric.min.js"></script>


<script type="text/javascript">
	$(".num").numeric();
	$(document).ready(function() 
	{
	    var b_id=$("#expense_status").val();
	   	if(b_id=="")
		{
			$("td").find("input.bd").addClass("hidden");
			$("td").find("label.bd").addClass("hidden");
			$("td").find("textarea.Re").addClass("hidden");
			$("td").find("label.Re").addClass("hidden");
		}
		else
		{
			$("td").find("input.bd").removeClass("hidden");
			$("td").find("label.bd").removeClass("hidden");
			$("td").find("textarea.Re").removeClass("hidden");
			$("td").find("label.Re").removeClass("hidden");
		}
	})
	

	function payValidation(b_id,id)
	{

		var required_amount = $("#required_amount"+id).val();
		if(parseFloat(required_amount) < parseFloat(b_id))
		{
			toastr.error("The passed amount entered is exceeded the requested amount");
			$("#pass_expense_amount"+id).val("");

		}
	}

	function getExpense(b_id,id)
	{
		if(b_id==1)
		{
		    $("td").find("input.book_date"+id).removeClass("hidden");
			$("td").find("label.book_date"+id).removeClass("hidden");
			$("td").find("textarea.bbb"+id).removeClass("hidden");
			$("td").find("label.bbb"+id).removeClass("hidden");	
			$("td").find("label.bbb1"+id).addClass("hidden");	
			$("td").find("textarea.bbb1"+id).addClass("hidden");	
		}
		else if(b_id==2)
		{
			$("td").find("input.book_date"+id).addClass("hidden");
			$("td").find("label.book_date"+id).addClass("hidden");
			$("td").find("textarea.bbb"+id).addClass("hidden");
			$("td").find("label.bbb"+id).addClass("hidden");
			$("td").find("label.bbb1"+id).removeClass("hidden");	
			$("td").find("textarea.bbb1"+id).removeClass("hidden");	
		}
		else
		{
		    $("td").find("input.book_date"+id).addClass("hidden");
			$("td").find("label.book_date"+id).addClass("hidden");
			$("td").find("textarea.bbb"+id).addClass("hidden");
			$("td").find("label.bbb"+id).addClass("hidden");
			$("td").find("label.bbb1"+id).addClass("hidden");
			$("td").find("textarea.bbb1"+id).addClass("hidden");
		}
	}
	
    </script>
    
    <script type="text/javascript">
    function ApproveExpense(id)
    {
        var expense_status1 = $(".expense_check"+id).find("option:selected").val();
        var pass_expense_amount = $("#pass_expense_amount"+id).val();
        var pass_remark = $("#pass_remark"+id).val();
        var reject_remark = $("#reject_remark"+id).val();	
        var cat_name = $("#cat_name"+id).val();	

        var sales_id = $("#sales_id"+id).val();	
        var sales_type = $("#sales_type"+id).val();	
        
        var check_remarks=true; 
        var check_amount=true; 
        if(expense_status1!="")      
        {        	
	        if(expense_status1==2)
	        {
	        	if(reject_remark=="")
	        	{
	        		check_remarks=false
	        	}
	        	var txt="Reject";
	        }
	        else if(expense_status1==1)
	        {
	        	var txt="Pass";
	        	
	        	if(pass_remark=="")
	        	{
	        		check_remarks=false
	        	}
	        	if(pass_expense_amount=="")
	        	{
	        		check_amount=false
	        	}
	        }
	        if(check_amount)
	        {	        	
		        if(check_remarks)
		        {        	
			        var r = confirm('Are You Sure Want To '+txt+' Expense?');
			        if(r)
			        {
			            $.ajax({
			                url:"expense_ajax_function.php",
			                type:"POST",
			                data:{
			                    m:'send_to_all',
			                    id:id,                
			                    expense_status1:expense_status1,                
			                    pass_expense_amount:pass_expense_amount, 
			                    pass_remark:pass_remark,
			                    reject_remark:reject_remark,
			                    cat_name:cat_name,
			                    sales_id:sales_id,
			                    sales_type:sales_type,
			                },
			                beforeSend: function() {
								// $("#loading-modal").modal('show');
								$('.preloader').fadeIn('slow');
							},
			                success:function(result) 
			                {
			                	console.log(result);
			                    var result=JSON.parse(result);
			                    // $("#loading-modal").modal('hide');
			                    $('.preloader').fadeOut('slow');
			                    //alert(result['ack']);
			                    // msg=result['ack_msg'];
			                    if(result.ack==1)
			                    {                       
			                        toastr.success(result.ack_msg,"Success!!"); 
			                        $("#requesting_ajax").click();
			                    }
			                    else
			                    {
			                        toastr.error(result.ack_msg, 'Error!!');
			                        $("#requesting_ajax").click();
			                    }
			                },            
			            })
			        }
		        }
		     	else
		     	{
		     		toastr.error("Please Enter Remark");
		     	}
	        }
	        else
	     	{
	     		toastr.error("Please Enter Amount");
	     	}
        }
        else
     	{
     		toastr.error("Please Select Action");
     	}
   	 }


   	 function DeleteExpense(id)
	    {
	        var r = confirm('Are You Sure Want To Delete Expense?');
	        if(r)
	        {
	            $.ajax({
	                url:"expense_ajax_function.php",
	                type:"POST",
	                data:{
	                    m:'delete_expense',
	                    id:id,                
	                    
	                },
	                beforeSend: function() {
						$('.preloader').fadeIn('slow');
					},
	                success:function(result) 
	                {
	                	console.log(result);
	                    var result=JSON.parse(result);
	                    $('.preloader').fadeOut('slow');
	                    if(result.ack==1)
	                    {                       
	                        toastr.error(result.ack_msg,"Success!!"); 
	                        $("#requesting_ajax").click();
	                    }
	                },            
	            })
	        }
	    }

	    
		     	
    </script>

    <script type="text/javascript">
	function autoapproveExpensesales(date,sales_id)
    {
		var r = confirm('Are You Sure Want To Approve All Expense ?');
        if(r)
        {
            $.ajax({
                url:"expense_approve_ajax.php",
                type:"POST",
                data:{
                    date:date,
                    sales_id:sales_id,
                },
                beforeSend: function() {
								// $("#loading-modal").modal('show');
								$('.preloader').fadeIn('slow');
								},
                success:function(result) 
                {
                	console.log(result);
                    var result=JSON.parse(result);
                    // $("#loading-modal").modal('hide');
                    //alert(result['ack']);
                    // msg=result['ack_msg'];
                    	$('.preloader').fadeOut('slow');
                    if(result.ack==1)
                    {                       
                        toastr.success(result.ack_msg,"Success!!");
                        $("#requesting_ajax").click(); 
                      	displayRecords(100,1);
                    }
                    else
                    {
                    	$("#requesting_ajax").click();
                        toastr.error(result.ack_msg, 'Error!!');
                         displayRecords(100,1);
                    }
                },            
            })
        }     
 	}
</script>
<?php require_once("disconnect.php"); ?>
