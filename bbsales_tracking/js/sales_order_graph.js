var Graph1=function() {
		// alert("tet");
	var data=[];
    return {
            	// alert("test");
    	
        initAmChart5: function(orders="") {
            am4core.ready(function() {

			// Themes begin
			am4core.useTheme(am4themes_animated);
			// Themes end

			// Create chart instance
			var chart = am4core.create("orders", am4charts.PieChart);

			// Add data
			chart.data = orders;

            // alert(JSON.stringify(chart.data));
			// alert(chart.data);

			// Add and configure Series
			var pieSeries = chart.series.push(new am4charts.PieSeries());
			pieSeries.dataFields.value = "value";
			pieSeries.dataFields.category = "month";
			pieSeries.slices.template.stroke = am4core.color("#fff");
			pieSeries.slices.template.strokeWidth = 2;
			pieSeries.slices.template.strokeOpacity = 1;

			// This creates initial animation
			pieSeries.hiddenState.properties.opacity = 1;
			pieSeries.hiddenState.properties.endAngle = -90;
			pieSeries.hiddenState.properties.startAngle = -90;
            	// alert("test");

			}); // end am4core.ready()
        },
        companyData:function() {
        	//var year = $("#year_company").val();
            var year = $('#year').val();
            var month = $('#month').val();
            var report=$('#report').val();
            var obj=this;
            $.ajax( {
                url:"statical_graph_ajax.php", 
                data: {
                    mode: "statistical_chart", 
                    year: year,
                    report:report,
                    month:month,
                }
                , success:function(result) {
                    if(report=="1")
                    {
                        report="SALES ORDER";
                    }
                    if(report=="2")
                    {
                        report="QUOTATION ";
                    }
                    if(report=="3")
                    {
                        report="INVOICE ";
                    }
                    if(report=="4")
                    {
                        report="VISIT ";
                    }
                    if(report=="5")
                    {
                        report="COMPLAIN ";
                    }
                    if(report=="6")
                    {
                        report="INQUIRY ";
                    }
                    if(report=="7")
                    {
                        report="LEAD ";
                    }

                	$(".datesso").html('STATISTICAL '+report+' REPORT OF :'+year );
                    // $("#quick_notes").html(result);
                    result=$.parseJSON(result);
                    
                    if(result.ack==1) {
                        var orders=[];
                        var graph_data=result.result.orders;
                        	$.each(graph_data, function(index, value) {
	                            orders.push(value);
	                        }
                        );
	                }
                    else {
                    	orders=[];
                    }
                    obj.initAmChart5(orders);
                }
            }
            )
        }
        ,
        init1: function() {
            this.companyData();
        }
    }
    ;
}
();
jQuery(document).ready(function() {
    // Graph1.init1();
});


var Graph2=function() {
    var data=[];
    return {
        initAmChart6: function(graph_data_result) {
            if (typeof(AmCharts)==='undefined' || $('#orders').size()===0) {
                return;
            }
            // alert("test");
            var data=graph_data_result;
           // alert(JSON.stringify(data));
            var chart=AmCharts.makeChart("orders", {
                "type": "serial", "maximum":10000000, "fontSize":12, "addClassNames": true, "theme": "light", "path": "../assets/global/plugins/amcharts/ammap/images/", "autoMargins": true, "marginLeft": 30, "marginRight": 8, "marginTop": 10, "marginBottom": 26, "balloon": {
                    "adjustBorderColor": false, "horizontalPadding": 10, "verticalPadding": 8, "color": "#ffffff"
                }
                , "dataProvider": data, "valueAxes": [ {
                    "axisAlpha": 0, "position": "left"
                }
                ], "startDuration": 1, "graphs": [ {
                    "alphaField": "alpha", "balloonText": "<span style='font-size:12px;'>[[title]] on [[category]]:<br><span style='font-size:20px;'>[[value]]</span> [[additional]]</span>", "fillAlphas": 1, "title": "Total amount", "type": "column", "valueField": "revenue", "dashLengthField": "dashLengthColumn"
                }
                ], "categoryField": "date", "categoryAxis": {
                    "gridPosition": "end", "axisAlpha": 0, "tickLength": 0
                }
                , "export": {
                    "enabled": true
                }, "valueAxes": [ {
                    "title": "Amount In INR(₹)"
                }
                , ], "categoryAxis": {
                    "title": "Days",
                }
                ,
            }
            );
        }
        ,
        companyData1:function() {
            var mL = ['','January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            var month=$("#month").val();
            var m = month*1;
            var year=$("#year").val();
            var report=$('#report').val();
            var obj=this;
            $.ajax( {
                url:"statical_graph_ajax.php", data: {
                    mode: "purchaseorder_chart", month: month, year: year, report:report,
                }
                , success:function(result) {
                    $(".datesso").html(""+mL[m]+" - "+year);
                    result=$.parseJSON(result);
                    if(result.ack==1) {
                        var graph_data_result=[];
                        var graph_data=result.result;
                        $.each(graph_data, function(index, value) {
                            graph_data_result.push(value);
                        }
                        );
                    }
                    else {
                        graph_data_result=[];
                    }
                    obj.initAmChart6(graph_data_result);
                }
            }
            )
        }
        ,
        init2: function() {
            this.companyData1();
        }
    }
    ;
}
();
jQuery(document).ready(function() {
    // Graph2.init2();
});



var Graph3=function() {
    var data=[];
    return {
        initAmChart7: function(graph_data_result) {
            if (typeof(AmCharts)==='undefined' || $('#orders').size()===0) {
                return;
            }
            // alert("test");
            var data=graph_data_result;
           // alert(JSON.stringify(data));
            var chart=AmCharts.makeChart("orders", {
                "type": "serial", "maximum":10000000, "fontSize":12, "addClassNames": true, "theme": "light", "path": "../assets/global/plugins/amcharts/ammap/images/", "autoMargins": true, "marginLeft": 30, "marginRight": 8, "marginTop": 10, "marginBottom": 26, "balloon": {
                    "adjustBorderColor": false, "horizontalPadding": 10, "verticalPadding": 8, "color": "#ffffff"
                }
                , "dataProvider": data, "valueAxes": [ {
                    "axisAlpha": 0, "position": "left"
                }
                ], "startDuration": 1, "graphs": [ {
                    "alphaField": "alpha", "balloonText": "<span style='font-size:12px;'>[[title]] on [[category]]:<br><span style='font-size:20px;'>[[value]]</span> [[additional]]</span>", "fillAlphas": 1, "title": "Total Count", "type": "column", "valueField": "revenue", "dashLengthField": "dashLengthColumn"
                }
                ], "categoryField": "date", "categoryAxis": {
                    "gridPosition": "end", "axisAlpha": 0, "tickLength": 0
                }
                , "export": {
                    "enabled": true
                }, "valueAxes": [ {
                    "title": "Total  Count :"
                }
                , ], "categoryAxis": {
                    "title": "Days",
                }
                ,
            }
            );
        }
        ,
        companyData2:function() {
            var mL = ['','January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            var month=$("#month").val();
            var m = month*1;
            var year=$("#year").val();
            var report=$('#report').val();
            var obj=this;
            $.ajax( {
                url:"statical_graph_ajax.php", data: {
                    mode: "purchaseorder_chart", month: month, year: year, report:report,
                }
                , success:function(result) {
                    $(".datesso").html(mL[m]+" - "+year);
                    result=$.parseJSON(result);
                    if(result.ack==1) {
                        var graph_data_result=[];
                        var graph_data=result.result;
                        $.each(graph_data, function(index, value) {
                            graph_data_result.push(value);
                        }
                        );
                    }
                    else {
                        graph_data_result=[];
                    }
                    obj.initAmChart7(graph_data_result);
                }
            }
            )
        }
        ,
        init3: function() {
            this.companyData2();
        }
    }
    ;
}
();
jQuery(document).ready(function() {
    // Graph3.init3();
});