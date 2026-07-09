<?php
$page_id=555;$page_slug='page_executive';
$page_slug = "find Bill";
include("connect.php");	
?>

<select name="city_id[]" multiple="multiple" id="city_id" class="form-control city_id">
    <option value="">Select City</option>
    <option value="0" class="all">Select All </option>
    <?php
    if(!empty($_POST["class_id"]))
	{
		$area_r = $db->rp_getData("city","*","state_id IN (".$_POST['class_id'].") AND isDelete=0","",0);
	    $total_records=0;
	    while($area_d = mysqli_fetch_array($area_r))
	    {
	        $total_records++;
    ?>
    <option <?php echo (in_array($area_d['id'],$_POST['class_id']))?"selected":""; ?> value="<?php echo $area_d['id']; ?>" ><?php echo $area_d['name']; ?></option>
    <?php
    	}
    } 
    ?>  
</select>

<input type="hidden" id="total_option_cid" value="<?=$total_records?>">
<script type="text/javascript">
    $("#city_id").fSelect();
    var check_all_cid=false;    
    $("#city_id").on("change",function()
    {       
        var ab=$(this).val();
        if(ab!="")
        {           
            var a11=ab.toString();
            newArr1=a11.split(",");
            // alert(jQuery.inArray("0", newArr1));
            var length1 = $('#city_id > option:selected').length;
            var total_option_cid = $("#total_option_cid").val();
            // alert(length1);
            // alert(total_option_cid);
            if(jQuery.inArray("0", newArr1) !== -1)
            {
                
                if(length1==1 && $("#city_id").find("option:selected").attr('class')=="all")
                {
                    // alert("1");
                    selectAllCid();
                    check_all_cid=true;
                }
                else if($("#city_id").find("option:selected").attr('class')=="all" )
                {
                    // deselectAllCid();
                    // alert("2");
                    if(check_all_cid && length1==total_option_cid)
                    {                   
                        $('#city_id option[value="0"]').prop("selected", false);
                        $(".mainCityId").find('.fs-option[data-value="0"]').removeClass("selected");
                        check_all_cid=false;
                    }
                    else
                    {
                        selectAllCid();
                        check_all_cid=true;
                    }
                }
            }       
            else if(length1==total_option_cid && jQuery.inArray("0", newArr1) ==-1)
            {
                
                // alert("3");
                if(!check_all_cid)
                {
                    selectAllCid();
                    check_all_cid=true;
                }
                else
                {
                    deselectAllCid();
                    $(".mainCityId").find(".fs-label").html("Select options");
                    check_all_cid=false;
                }
            }
            else
            {
                // alert("4");  
                // deselectAllCid();
                // $('#city_id option[value="all"]').prop("selected", false);
                // $('.fs-option[data-value="0"]').removeClass("selected");
            }       
        }

        getRoute();
    })

    function selectAllCid()
    {
        $('#city_id').fSelect();
        $("#city_id").prev(".fs-dropdown").find(".fs-option").each(function()
        {
            $(this).addClass("selected");
        })
        $("#city_id").find(".other_city_id").each(function()
        {
            $(this).attr("selected","selected");
        })      
    }
    function deselectAllCid()
    {
        $('#city_id').fSelect();        
        $("#city_id").prev(".fs-dropdown").find(".fs-option").removeClass("selected");
        $("#city_id").find(".other_city_id").removeAttr("selected");
    }

    function getRoute()
    {
      	var city_id=$("#city_id").val();
        var class_id=$("#class_id").val();
      	$.ajax({
          type: "POST",
          url: "ajax_find_area.php",
          data: 'city_id='+city_id + '&class_id=' + class_id,
          success: function(data){
            $(".abc").html(data);
            $("#area_id").fSelect();
          }
        });
    }  
</script>
      

  
