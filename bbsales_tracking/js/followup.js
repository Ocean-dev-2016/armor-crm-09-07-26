var ChannelAjax = function () {
	
	var orignal_container=$("#channel-container");
	var result_container=$("#followup-ajax-result-container-1");
	var ajax_url="followup_grid_get_ajax.php";
	var modification_url="#";
	var show_count=10;
	var channel_name_filter="";
	var interests=[];
	var locations=[];
	var page=1;
	var t=1;
	var handleTable = function () {
		if(CurrentView==1)
		{
			ajax_url="followup_grid_get_ajax.php";			
		}
		else
		{
			ajax_url="followup_get_ajax.php";			
		}

		$(result_container).on('click','.loadMoreBtn',function(){
			page=$(this).data('page');
			$(".loadMoreBtn").hide();
			getDataFromAJAX();
		})
	}
    var handlePagination=function(){
    	var pagination_buttons=$(orignal_container).find('.pagination').find("li.paginate_button").find("a");
    	var row_count_spinner=$(orignal_container).find("select.rowCountSpinner");
    	$(pagination_buttons).on('click',function(){
			page=$(this).data("page");
			getDataFromAJAX();
		});
		$(row_count_spinner).on("change",function(){
			show_count=$(this).val();
			getDataFromAJAX();
		});
		
    }
	var submitTo=function(args)
	{
		
        var form = $('<form></form>');
        form.attr("method", "post");
        form.attr("action", modification_url);

        $.each( args, function( key, value ) {
            var field = $('<input></input>');

            field.attr("type", "hidden");
            field.attr("name", key);
            field.attr("value", value);

            form.append(field);
        });
        $(form).appendTo('body').submit();
	}
	
	var getDataFromAJAX=function(){
		
		var visitor_id = $("#visitor_id").val();
		var followup_flag = $("#followup_flag").val();
		var inquiry_id = $("#inquiry_id").val();
		var quotation_id = $("#quotation_id").val();
		var executive_id = $("#executive_id").val();
		//alert(visitor_id);
		$.ajax({
			url:ajax_url,
			type:'GET',
			data:{
				page:page,
				visitor_id:visitor_id,
				followup_flag:followup_flag,
				inquiry_id:inquiry_id,
				quotation_id:quotation_id,
				executive_id:executive_id,
				show:show_count,
				channel_name:channel_name_filter,
				interests:interests,
				locations:locations,
			},
			success:function(result)
			{
				$(result_container).html("");
				$(result_container).append(result);
				handlePagination();
			}
		})
		
	}
	return {

        //main function to initiate the module
        init: function () { 
        	handleTable();        
            getDataFromAJAX();
        }

    };

}();

jQuery(document).ready(function() {
    ChannelAjax.init();
});

	
