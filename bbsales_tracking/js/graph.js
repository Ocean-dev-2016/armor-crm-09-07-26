var Graph = function() {
	var data=[];
    return {	
		initAmChart3: function(graph_data_result) {
		  
            if (typeof(AmCharts) === 'undefined' || $('#dashboard_amchart_3').size() === 0) {
                return;
            }
			var data=graph_data_result;
			
            var chart = AmCharts.makeChart("dashboard_amchart_3", {
                "type": "serial",
				"maximum":10000000,
				"fontSize":12,
                "addClassNames": true,
                "theme": "light",
                "path": "assets/global/plugins/amcharts/ammap/images/",
                "autoMargins": true,
                "marginLeft": 30,
                "marginRight": 8,
                "marginTop": 10,
                "marginBottom": 26,
                "balloon": {
                    "adjustBorderColor": false,
                    "horizontalPadding": 10,
                    "verticalPadding": 8,
                    "color": "#ffffff"
                },

                "dataProvider": data,
                "valueAxes": [{
                    "axisAlpha": 0,
                    "position": "left"
                }],
                "startDuration": 1,
                "graphs": [{
                    "alphaField": "alpha",
                    "balloonText": "<span style='font-size:12px;'>[[title]] on [[category]]:<br><span style='font-size:20px;'>[[value]]</span> [[additional]]</span>",
                    "fillAlphas": 1,
                    "title": "Revenue",
                    "type": "column",
                    "valueField": "revenue",
                    "dashLengthField": "dashLengthColumn"
                }, {
                    "id": "graph2",
                    "balloonText": "<span style='font-size:12px;'>[[title]] on [[category]]:<br><span style='font-size:20px;'>[[value]]</span> [[additional]]</span>",
                    "bullet": "round",
                    "lineThickness": 3,
                    "bulletSize": 7,
                    "bulletBorderAlpha": 1,
                    "bulletColor": "#FFFFFF",
                    "useLineColorForBulletBorder": true,
                    "bulletBorderThickness": 3,
                    "fillAlphas": 0,
                    "lineAlpha": 1,
                    "title": "No.of Orders",
                    "valueField": "orders"
                }],
                "categoryField": "date",
                "categoryAxis": {
                    "gridPosition": "end",
                    "axisAlpha": 0,
                    "tickLength": 0
                },
                "export": {
                    "enabled": true
                }
            });
        },
	  orderData:function(){
		 var month=$("#month_order").val();
		 var year=$("#year_order").val();
		 var obj=this;
		  $.ajax({
			  url:"graph_ajax_function.php",
			  data:{
				  mode:"order_data",
				  month:month,
				  year:year,
			  },
			  success:function(result){
				    
				  $("#dashboard_amchart_3").html(result);		
			  }
			 /* success:function(result){
				    
				  result=$.parseJSON(result);
				 
				  if(result.ack==1)
				  {  
					  var graph_data_result=[];
					  var graph_data=result.result;
					  $.each(graph_data,function(index,value){
						  
						  graph_data_result.push(value);
					  });
					 
				  }
				  else
				  {
					  graph_data_result=[];
					  toastr.error("No Orders found!!");
				  }
				    obj.initAmChart3(graph_data_result);
				  
			  }*/
		  })
	  },
	   salesItemData:function(){
		 var month=$("#month_sales_item").val();
		 var year=$("#year_sales_item").val();
		 var product_name=$("#product_name").val();
		 var obj=this;
		  $.ajax({
			  url:"graph_ajax_function.php",
			  data:{
				  mode:"sales_item_data",
				  month:month,
				  year:year,
				  product_name:product_name,
			  },
			  success:function(result){
				    
				  $("#sales_item_container").html(result);		
			  }
		  })
	  },
	  productItemData:function(){
		 var month=$("#month_sales_item").val();
		 var year=$("#year_sales_item").val();
		 var order_product_name=$("#order_product_name").val();
		 var customer_id=$("#customer_id").val();
		 var obj=this;
		  $.ajax({
			  url:"graph_ajax_function.php",
			  data:{
				  mode:"product_item_data",
				  month:month,
				  year:year,
				  order_product_name:order_product_name,
				  customer_id:customer_id,
			  },
			  success:function(result){
				    
				  $("#product_item_container").html(result);		
			  }
		  })
	  },
       init: function() {
		  
            this.orderData();
			this.salesItemData();
			this.productItemData();
        }
    };

}();

 jQuery(document).ready(function() {
        Graph.init(); // init metronic core componets
    });