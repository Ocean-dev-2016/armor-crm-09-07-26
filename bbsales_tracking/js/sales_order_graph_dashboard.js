var Graph1=function() {


		// alert("tet");
	var data=[];
    return {
            	// alert("test");
    	
        initAmChart5: function(quotation="") {
            am4core.ready(function() {

			// Themes begin
			am4core.useTheme(am4themes_animated);
			// Themes end

			// Create chart instance
			var chart = am4core.create("quotation", am4charts.PieChart);

			// Add data
			chart.data = quotation;

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
            var quotation_year = $('#quotation_year').val();
            var quotation_month = $('#quotation_month').val();
            var quotation_sales_id=$('#quotation_sales_id').val();
            var quotation_customer_id=$('#quotation_customer_id').val();
            
            var report="2";
            // alert(quotation_year);




            var obj=this;
            $.ajax( {
                url:"statical_graph_ajax_function.php", 
                data: {
                    mode: "statistical_chart", 
                    year: quotation_year,
                    report:report,
                    month:quotation_month,
                    quotation_sales_id:quotation_sales_id,
                    quotation_customer_id:quotation_customer_id,

                }
                , success:function(result) {
                    
                    // $("#quick_notes").html(result);
                    result=$.parseJSON(result);
                         // alert(JSON.stringify(result));
                    
                    if(result.ack==1) {
                        var quotation=[];
                        var graph_data=result.result.quotation;
                        	$.each(graph_data, function(index, value) {
	                            quotation.push(value);
	                        }
                        );
	                }
                    else {
                    	quotation=[];
                    }
                    obj.initAmChart5(quotation);
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
    Graph1.init1();
});



// for order pie chart start

    var graph_order_pie=function() {
        // alert("tet");
    var data=[];
    return {
                // alert("test");
        
        initAmChart_order_pie: function(orders="") {
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
        companyData_order_pie:function() {
            //var year = $("#year_company").val();
            var order_year = $('#order_year').val();
            var order_month =$('#order_month').val();
            var order_sales_id=$('#order_sales_id').val();
            var order_customer_id=$('#order_customer_id').val();
          
            var report="1";
            var obj=this;
            $.ajax( {
                url:"statical_graph_ajax_function.php", 
                data: {
                    mode: "statistical_chart", 
                    year: order_year,
                    report:report,
                    month:order_month,
                    order_sales_id:order_sales_id,
                    order_customer_id:order_customer_id,

                }
                , success:function(result) {
                    
                    // $("#quick_notes").html(result);
                    result=$.parseJSON(result);
                         // alert(JSON.stringify(result));
                    
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
                    obj.initAmChart_order_pie(orders);
                }
            }
            )
        }
        ,
        init_order_pie: function() {
            this.companyData_order_pie();
        }
    }
    ;
}
();
jQuery(document).ready(function() {
    graph_order_pie.init_order_pie();
});
    
// for order pie chart end



// for dispatch pie chart start

    var graph_dispatch_pie=function() {
        // alert("tet");
    var data=[];
    return {
                // alert("test");
        
        initAmChart_dispatch_pie: function(dispatch="") {
            am4core.ready(function() {

            // Themes begin
            am4core.useTheme(am4themes_animated);
            // Themes end

            // Create chart instance
            var chart = am4core.create("dispatch", am4charts.PieChart);

            // Add data
            chart.data = dispatch;

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
        companyData_dispatch_pie:function() {
            //var year = $("#year_company").val();
            var dispatch_year = $('#dispatch_year').val();
            var dispatch_month = $('#dispatch_month').val();
            var dispatch_sales_id=$('#dispatch_sales_id').val();
            var dispatch_customer_id=$('#dispatch_customer_id').val();
            var report="19";
            var obj=this;
            $.ajax( {
                url:"statical_graph_ajax_function.php", 
                data: {
                    mode: "statistical_chart", 
                    year: dispatch_year,
                    report:report,
                    month:dispatch_month,
                    dispatch_sales_id:dispatch_sales_id,
                    dispatch_customer_id:dispatch_customer_id,

                }
                , success:function(result) {
                    
                    // $("#quick_notes").html(result);
                    result=$.parseJSON(result);
                         // alert(JSON.stringify(result));
                    
                    if(result.ack==1) {
                        var dispatch=[];
                        var graph_data=result.result.dispatch;
                            $.each(graph_data, function(index, value) {
                                dispatch.push(value);
                            }
                        );
                    }
                    else {
                        dispatch=[];
                    }
                    obj.initAmChart_dispatch_pie(dispatch);
                }
            }
            )
        }
        ,
        init_dispatch_pie: function() {
            this.companyData_dispatch_pie();
        }
    }
    ;
}
();
jQuery(document).ready(function() {
    graph_dispatch_pie.init_dispatch_pie();
});
    
    




// for dispatch pie chart end








// for invoice pie chart start

    var graph_invoice_pie=function() {
        // alert("tet");
    var data=[];
    return {
                // alert("test");
        
        initAmChart_invoice_pie: function(invoice="") {
            am4core.ready(function() {

            // Themes begin
            am4core.useTheme(am4themes_animated);
            // Themes end

            // Create chart instance
            var chart = am4core.create("invoice", am4charts.PieChart);

            // Add data
            chart.data = invoice;

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
        companyData_invoice_pie:function() {
            //var year = $("#year_company").val();
            var invoice_year = $('#invoice_year').val();
            var invoice_month = $('#invoice_month').val();
            var invoice_sales_id=$('#invoice_sales_id').val();
            var invoice_customer_id=$('#invoice_customer_id').val();
            var report="3";
            var obj=this;
            $.ajax( {
                url:"statical_graph_ajax_function.php", 
                data: {
                    mode: "statistical_chart", 
                    year: invoice_year,
                    report:report,
                    month:invoice_month,
                    invoice_sales_id:invoice_sales_id,
                    invoice_customer_id:invoice_customer_id,

                }
                , success:function(result) {
                    
                    // $("#quick_notes").html(result);
                    result=$.parseJSON(result);
                         // alert(JSON.stringify(result));
                    
                    if(result.ack==1) {
                        var invoice=[];
                        var graph_data=result.result.invoice;
                            $.each(graph_data, function(index, value) {
                                invoice.push(value);
                            }
                        );
                    }
                    else {
                        invoice=[];
                    }
                    obj.initAmChart_invoice_pie(invoice);
                }
            }
            )
        }
        ,
        init_invoice_pie: function() {
            this.companyData_invoice_pie();
        }
    }
    ;
}
();
jQuery(document).ready(function() {
    graph_invoice_pie.init_invoice_pie();
});
    
    




// for invoice pie chart end


// for prospect pie chart start

    var graph_prospect_pie=function() {
        // alert("tet");
    var data=[];
    return {
                // alert("test");
        
        initAmChart_prospect_pie: function(prospect="") {
            am4core.ready(function() {

            // Themes begin
            am4core.useTheme(am4themes_animated);
            // Themes end

            // Create chart instance
            var chart = am4core.create("prospect", am4charts.PieChart);

            // Add data
            chart.data = prospect;

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
        companyData_prospect_pie:function() {
            //var year = $("#year_company").val();
            var prospect_year = $('#prospect_year').val();
            var prospect_month = $('#prospect_month').val();
            var prospect_inquiry_created_by= $('#prospect_inquiry_created_by').val();
            var prospect_inquiry_assigned_to= $('#prospect_inquiry_assigned_to').val();
            var report="20";
            var obj=this;
            $.ajax( {
                url:"statical_graph_ajax_function.php", 
                data: {
                    mode: "statistical_chart", 
                    year: prospect_year,
                    report:report,
                    month:prospect_month,
                    prospect_inquiry_created_by:prospect_inquiry_created_by,
                    prospect_inquiry_assigned_to:prospect_inquiry_assigned_to,

                }
                , success:function(result) {
                    
                    // $("#quick_notes").html(result);
                    result=$.parseJSON(result);
                         // alert(JSON.stringify(result));
                    
                    if(result.ack==1) {
                        var prospect=[];
                        var graph_data=result.result.prospect;
                            $.each(graph_data, function(index, value) {
                                prospect.push(value);
                            }
                        );
                    }
                    else {
                        prospect=[];
                    }
                    obj.initAmChart_prospect_pie(prospect);
                }
            }
            )
        }
        ,
        init_prospect_pie: function() {
            this.companyData_prospect_pie();
        }
    }
    ;
}
();
jQuery(document).ready(function() {
    graph_prospect_pie.init_prospect_pie();
});
    
    




// for prospect pie chart end


// for inquiry pie chart start

    var graph_inquiry_pie=function() {
        // alert("tet");
    var data=[];
    return {
                // alert("test");
        
        initAmChart_inquiry_pie: function(inquiry="") {
            am4core.ready(function() {

            // Themes begin
            am4core.useTheme(am4themes_animated);
            // Themes end

            // Create chart instance
            var chart = am4core.create("inquiry", am4charts.PieChart);

            // Add data
            chart.data = inquiry;

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
        companyData_inquiry_pie:function() {
            //var year = $("#year_company").val();

            var inquiry_year = $('#inquiry_year').val();
            var inquiry_month = $('#inquiry_month').val();
            var inquiry_inquiry_created_by= $('#inquiry_inquiry_created_by').val();
            var inquiry_inquiry_assigned_to= $('#inquiry_inquiry_assigned_to').val();
            // var inquiry_todate=$('#inquiry_todate').val();
            // var inquiry_fromdate=$('#inquiry_fromdate').val();
            var report="6";
            var obj=this;
            $.ajax( {
                url:"statical_graph_ajax_function.php", 
                data: {
                    mode: "statistical_chart", 
                    year: inquiry_year,
                    report:report,
                    month:inquiry_month,
                    inquiry_inquiry_created_by:inquiry_inquiry_created_by,
                    inquiry_inquiry_assigned_to:inquiry_inquiry_assigned_to,
                    // inquiry_todate:inquiry_todate,
                    // inquiry_fromdate:inquiry_fromdate


                }
                , success:function(result) {
                    
                    // $("#quick_notes").html(result);
                    result=$.parseJSON(result);
                         // alert(JSON.stringify(result));
                    
                    if(result.ack==1) {
                        var inquiry=[];
                        var graph_data=result.result.inquiry;
                            $.each(graph_data, function(index, value) {
                                inquiry.push(value);
                            }
                        );
                    }
                    else {
                        inquiry=[];
                    }
                    obj.initAmChart_inquiry_pie(inquiry);
                }
            }
            )
        }
        ,
        init_inquiry_pie: function() {
            this.companyData_inquiry_pie();
        }
    }
    ;
}
();
jQuery(document).ready(function() {
    graph_inquiry_pie.init_inquiry_pie();
});
    
    




// for inquiry pie chart end


// for lead pie chart start

    var graph_lead_pie=function() {
        // alert("tet");
    var data=[];
    return {
                // alert("test");
        
        initAmChart_lead_pie: function(lead="") {
            am4core.ready(function() {

            // Themes begin
            am4core.useTheme(am4themes_animated);
            // Themes end

            // Create chart instance
            var chart = am4core.create("lead", am4charts.PieChart);

            // Add data
            chart.data = lead;

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
        companyData_lead_pie:function() {
            //var year = $("#year_company").val();
          var lead_year = $('#lead_year').val();
            var lead_month = $('#lead_month').val();
            var lead_inquiry_created_by= $('#lead_inquiry_created_by').val();
            var lead_inquiry_assigned_to= $('#lead_inquiry_assigned_to').val();
            var report="7";
            var obj=this;
            $.ajax( {
                url:"statical_graph_ajax_function.php", 
                data: {
                    mode: "statistical_chart", 
                    year: lead_year,
                    report:report,
                    month:lead_month,
                    lead_inquiry_created_by:lead_inquiry_created_by,
                    lead_inquiry_assigned_to:lead_inquiry_assigned_to,
                }
                , success:function(result) {
                    
                    // $("#quick_notes").html(result);
                    result=$.parseJSON(result);
                         // alert(JSON.stringify(result));
                    
                    if(result.ack==1) {
                        var lead=[];
                        var graph_data=result.result.lead;
                            $.each(graph_data, function(index, value) {
                                lead.push(value);
                            }
                        );
                    }
                    else {
                        lead=[];
                    }
                    obj.initAmChart_lead_pie(lead);
                }
            }
            )
        }
        ,
        init_lead_pie: function() {
            this.companyData_lead_pie();
        }
    }
    ;
}
();
jQuery(document).ready(function() {
    graph_lead_pie.init_lead_pie();
});
    
    




// for lead pie chart end



// for visit pie chart start

    var graph_visit_pie=function() {
        // alert("tet");
    var data=[];
    return {
                // alert("test");
        
        initAmChart_visit_pie: function(visit="") {
            am4core.ready(function() {

            // Themes begin
            am4core.useTheme(am4themes_animated);
            // Themes end

            // Create chart instance
            var chart = am4core.create("visit", am4charts.PieChart);

            // Add data
            chart.data = visit;

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
        companyData_visit_pie:function() {
            //var year = $("#year_company").val();
            var visit_year = $('#visit_year').val();
            var visit_month = $('#visit_month').val();
            var visit_sales_id=$('#visit_sales_id').val();
            var visit_customer_id=$('#visit_customer_id').val();
            var report="4";
            var obj=this;
            $.ajax( {
                url:"statical_graph_ajax_function.php", 
                data: {
                    mode: "statistical_chart", 
                    year: visit_year,
                    report:report,
                    month:visit_month,
                    visit_sales_id:visit_sales_id,
                    visit_customer_id:visit_customer_id,

                }
                , success:function(result) {
                    
                    // $("#quick_notes").html(result);
                    result=$.parseJSON(result);
                         // alert(JSON.stringify(result));
                    
                    if(result.ack==1) {
                        var visit=[];
                        var graph_data=result.result.visit;
                            $.each(graph_data, function(index, value) {
                                visit.push(value);
                            }
                        );
                    }
                    else {
                        visit=[];
                    }
                    obj.initAmChart_visit_pie(visit);
                }
            }
            )
        }
        ,
        init_visit_pie: function() {
            this.companyData_visit_pie();
        }
    }
    ;
}
();
jQuery(document).ready(function() {
    graph_visit_pie.init_visit_pie();
});
    
    




// for visit pie chart end


// for complain pie chart start

    var graph_complain_pie=function() {
        // alert("tet");
    var data=[];
    return {
                // alert("test");
        
        initAmChart_complain_pie: function(complain="") {
            am4core.ready(function() {

            // Themes begin
            am4core.useTheme(am4themes_animated);
            // Themes end

            // Create chart instance
            var chart = am4core.create("complain", am4charts.PieChart);

            // Add data
            chart.data = complain;

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
        companyData_complain_pie:function() {
            //var year = $("#year_company").val();
            var complain_year = $('#complain_year').val();
            var complain_month = $('#complain_month').val();
            var complain_sales_id=$('#complain_sales_id').val();
            var complain_customer_id=$('#complain_customer_id').val();
            var report="5";
            var obj=this;
            $.ajax( {
                url:"statical_graph_ajax_function.php", 
                data: {
                    mode: "statistical_chart", 
                    year: complain_year,
                    report:report,
                    month:complain_month,
                    complain_sales_id:complain_sales_id,
                    complain_customer_id:complain_customer_id,
                }
                , success:function(result) {
                    
                    // $("#quick_notes").html(result);
                    result=$.parseJSON(result);
                         // alert(JSON.stringify(result));
                    
                    if(result.ack==1) {
                        var complain=[];
                        var graph_data=result.result.complain;
                            $.each(graph_data, function(index, value) {
                                complain.push(value);
                            }
                        );
                    }
                    else {
                        complain=[];
                    }
                    obj.initAmChart_complain_pie(complain);
                }
            }
            )
        }
        ,
        init_complain_pie: function() {
            this.companyData_complain_pie();
        }
    }
    ;
}
();
jQuery(document).ready(function() {
    graph_complain_pie.init_complain_pie();
});
    
    




// for complain pie chart end



// for expense pie chart start

    var graph_expense_pie=function() {
        // alert("tet");
    var data=[];
    return {
                // alert("test");
        
        initAmChart_expense_pie: function(expense="") {
            am4core.ready(function() {

            // Themes begin
            am4core.useTheme(am4themes_animated);
            // Themes end

            // Create chart instance
            var chart = am4core.create("expense", am4charts.PieChart);

            // Add data
            chart.data = expense;

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
        companyData_expense_pie:function() {
            //var year = $("#year_company").val();
            var expense_year = $('#expense_year').val();
            var expense_month = $('#expense_month').val();
            var expense_sales_id=$('#expense_sales_id').val();
            var report="21";
            var obj=this;
            $.ajax( {
                url:"statical_graph_ajax_function.php", 
                data: {
                    mode: "statistical_chart", 
                    year: expense_year,
                    report:report,
                    month:expense_month,
                    expense_sales_id:expense_sales_id,
                }
                , success:function(result) {
                    
                    // $("#quick_notes").html(result);
                    result=$.parseJSON(result);
                         // alert(JSON.stringify(result));
                    
                    if(result.ack==1) {
                        var expense=[];
                        var graph_data=result.result.expense;
                            $.each(graph_data, function(index, value) {
                                expense.push(value);
                            }
                        );
                    }
                    else {
                        expense=[];
                    }
                    obj.initAmChart_expense_pie(expense);
                }
            }
            )
        }
        ,
        init_expense_pie: function() {
            this.companyData_expense_pie();
        }
    }
    ;
}
();
jQuery(document).ready(function() {
    graph_expense_pie.init_expense_pie();
});
    
    




// for expense pie chart end


// for leave pie chart start

    var graph_leave_pie=function() {
        // alert("tet");
    var data=[];
    return {
                // alert("test");
        
        initAmChart_leave_pie: function(leave="") {
            am4core.ready(function() {

            // Themes begin
            am4core.useTheme(am4themes_animated);
            // Themes end

            // Create chart instance
            var chart = am4core.create("leave", am4charts.PieChart);

            // Add data
            chart.data = leave;

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
        companyData_leave_pie:function() {
            //var year = $("#year_company").val();
            var leave_year = $('#leave_year').val();
            var leave_month = $('#leave_month').val();
            var leave_sales_id=$('#leave_sales_id').val();
            var report="22";
            var obj=this;
            $.ajax( {
                url:"statical_graph_ajax_function.php", 
                data: {
                    mode: "statistical_chart", 
                    year: leave_year,
                    report:report,
                    month:leave_month,
                    leave_sales_id:leave_sales_id,

                }
                , success:function(result) {
                    
                    // $("#quick_notes").html(result);
                    result=$.parseJSON(result);
                         // alert(JSON.stringify(result));
                    
                    if(result.ack==1) {
                        var leave=[];
                        var graph_data=result.result.leave;
                            $.each(graph_data, function(index, value) {
                                leave.push(value);
                            }
                        );
                    }
                    else {
                        leave=[];
                    }
                    obj.initAmChart_leave_pie(leave);
                }
            }
            )
        }
        ,
        init_leave_pie: function() {
            this.companyData_leave_pie();
        }
    }
    ;
}
();
jQuery(document).ready(function() {
    graph_leave_pie.init_leave_pie();
});
    
    




// for leave pie chart end



// for followup pie chart start

    var graph_followup_pie=function() {
        // alert("tet");
    var data=[];
    return {
                // alert("test");
        
        initAmChart_followup_pie: function(followup="") {
            am4core.ready(function() {

            // Themes begin
            am4core.useTheme(am4themes_animated);
            // Themes end

            // Create chart instance
            var chart = am4core.create("followup", am4charts.PieChart);

            // Add data
            chart.data = followup;

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
        companyData_followup_pie:function() {
            //var year = $("#year_company").val();
            var followup_year = $('#followup_year').val();
            var followup_month = $('#followup_month').val();
            var followup_sales_id=$('#followup_sales_id').val();
            var followup_customer_id=$('#followup_customer_id').val();
            var report="23";
            var obj=this;
            $.ajax( {
                url:"statical_graph_ajax_function.php", 
                data: {
                    mode: "statistical_chart", 
                    year: followup_year,
                    report:report,
                    month:followup_month,
                    followup_sales_id:followup_sales_id,
                    followup_customer_id:followup_customer_id,
                }
                , success:function(result) {
                    
                    // $("#quick_notes").html(result);
                    result=$.parseJSON(result);
                         // alert(JSON.stringify(result));
                    
                    if(result.ack==1) {
                        var followup=[];
                        var graph_data=result.result.followup;
                            $.each(graph_data, function(index, value) {
                                followup.push(value);
                            }
                        );
                    }
                    else {
                        followup=[];
                    }
                    obj.initAmChart_followup_pie(followup);
                }
            }
            )
        }
        ,
        init_followup_pie: function() {
            this.companyData_followup_pie();
        }
    }
    ;
}
();
jQuery(document).ready(function() {
    graph_followup_pie.init_followup_pie();
});
    
    




// for followup pie chart end


// for attendance pie chart start

    var graph_attendance_pie=function() {
        // alert("tet");
    var data=[];
    return {
                // alert("test");
        
        initAmChart_attendance_pie: function(attendance="") {
            am4core.ready(function() {

            // Themes begin
            am4core.useTheme(am4themes_animated);
            // Themes end

            // Create chart instance
            var chart = am4core.create("attendance", am4charts.PieChart);

            // Add data
            chart.data = attendance;

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
        companyData_attendance_pie:function() {
            //var year = $("#year_company").val();
            var attendance_year = $('#attendance_year').val();
            var attendance_month = $('#attendance_month').val();
            var attendance_sales_id=$('#attendance_sales_id').val();
            var attendance_customer_id=$('#attendance_customer_id').val();
            var report="24";
            var obj=this;
            $.ajax( {
                url:"statical_graph_ajax_function.php", 
                data: {
                    mode: "statistical_chart", 
                    year: attendance_year,
                    report:report,
                    month:attendance_month,
                    attendance_sales_id:attendance_sales_id,
                    attendance_customer_id:attendance_customer_id,
                }
                , success:function(result) {
                    
                    // $("#quick_notes").html(result);
                    result=$.parseJSON(result);
                         // alert(JSON.stringify(result));
                    
                    if(result.ack==1) {
                        var attendance=[];
                        var graph_data=result.result.attendance;
                            $.each(graph_data, function(index, value) {
                                attendance.push(value);
                            }
                        );
                    }
                    else {
                        attendance=[];
                    }
                    obj.initAmChart_attendance_pie(attendance);
                }
            }
            )
        }
        ,
        init_attendance_pie: function() {
            this.companyData_attendance_pie();
        }
    }
    ;
}
();
jQuery(document).ready(function() {
    graph_attendance_pie.init_attendance_pie();
});
    
    




// for attendance pie chart end










// var Graph2=function() {
//         // alert("tet");
//     var data=[];
//     return {
                
        
//         initAmChart6: function(invoice="") {
//             am4core.ready(function() {
//             // Themes begin
//             am4core.useTheme(am4themes_animated);
//             // Themes end

//             // Create chart instance
//             var chart = am4core.create("quotation", am4charts.PieChart);
//                 // alert("test");

//             // Add data
//             chart.data = quotation;

//             // alert(JSON.stringify(chart_quotation.data));
//             // alert(chart.data);

//             // Add and configure Series
//             var pieSeries = chart_quotation.series.push(new am4charts.PieSeries());
//             pieSeries.dataFields.value = "value";
//             pieSeries.dataFields.category = "month";
//             pieSeries.slices.template.stroke = am4core.color("#fff");
//             pieSeries.slices.template.strokeWidth = 2;
//             pieSeries.slices.template.strokeOpacity = 1;

//             // This creates initial animation
//             pieSeries.hiddenState.properties.opacity = 1;
//             pieSeries.hiddenState.properties.endAngle = -90;
//             pieSeries.hiddenState.properties.startAngle = -90;
//                 // alert("test");

//             }); // end am4core.ready()
//         },
//         companyData2:function() {
//             //var year = $("#year_company").val();
//             var year_quotation = "2021";
//             // var month_quotation = $('#month').val();
//             var report_quotation="1";
//             var obj_quotation=this;
//             $.ajax( {
//                 url:"statical_graph_ajax_function.php", 
//                 data: {
//                     mode: "statistical_chart", 
//                     year: year_quotation,
//                     report:report_quotation,
//                     // month:month_quotation,
//                 }
//                 , success:function(result) {
                    
//                     // $(".datesso").html('STATISTICAL '+report+' REPORT OF :'+year );
//                     // $("#quick_notes").html(result);
//                     result=$.parseJSON(result);
                    
//                     if(result.ack==1) {
//                         // alert(JSON.stringify(result));
//                         var quotation=[];
//                         var graph_data_quotation= result.result.quotation;
//                         // alert(JSON.stringify(graph_data_quotation));
//                             $.each(graph_data_quotation, function(index,value) {
//                                 quotation.push(value);
//                             }
//                         );
//                     }
//                     else {
//                         quotation=[];
//                     }
//                     obj_quotation.initAmChart6(quotation);
//                 }
//             }
//             )
//         }
//         ,
//         init2: function() {
//             this.companyData2();
//         }
//     }
//     ;
// }
// ();
// jQuery(document).ready(function() {
//     Graph2.init2();
// });


// for Quotation chart start

var Graph2=function() {
    var data=[];
    return {
        initAmChart6: function(graph_data_result) {
            if (typeof(AmCharts)==='undefined' || $('#quotation').size()===0) {
                return;
            }
            // alert("test");
            var data=graph_data_result;
           // alert(JSON.stringify(data));
            var chart=AmCharts.makeChart("quotation", {
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
            var month=$('#quotation_month').val();
            var m = month*1;
            var year=$('#quotation_year').val();
            var report="9";
            var quotation_sales_id=$('#quotation_sales_id').val();
            var quotation_customer_id=$('#quotation_customer_id').val();
            var quotation_todate=$('#quotation_todate').val();
            var quotation_fromdate=$('#quotation_fromdate').val();

            var obj=this;
            $.ajax( {
                url:"statical_graph_ajax_function.php", data: {
                    mode: "purchaseorder_chart", month: month,
                     year: year,
                      report:report,
                      quotation_sales_id:quotation_sales_id,
                      quotation_customer_id:quotation_customer_id,
                      quotation_todate:quotation_todate,
                      quotation_fromdate:quotation_fromdate,
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
// jQuery(document).ready(function() {
//     Graph2.init2();
// });

// for Quotation chart start


// for orders chart start
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
        companyData2:function() {
            var mL_order = ['','January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            var month_order=$('#order_month').val();
            var m_order = month_order*1;
            var year_order=$('#order_year').val();
            var order_sales_id=$('#order_sales_id').val();
            var order_customer_id=$('#order_customer_id').val();
            var order_todate=$('#order_todate').val();
            var order_fromdate=$('#order_fromdate').val();
            var report_order="8";

            var obj=this;
            // alert("test");
            $.ajax( {
                url:"statical_graph_ajax_function.php", data: {
                    mode: "purchaseorder_chart", month: month_order,
                     year: year_order,
                      report:report_order,
                      order_sales_id:order_sales_id,
                      order_customer_id:order_customer_id,
                       order_todate:order_todate,
                      order_fromdate:order_fromdate,
                }
                , success:function(result) {
                    // $(".datesso").html(""+mL[m]+" - "+year);

                    result=$.parseJSON(result);
                    // alert(JSON.stringify(result));

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
// jQuery(document).ready(function() {
//     Graph3.init3();
// });
// for orders chart end


// For Inquiry Chart start

var Graph_inquiry=function() {

    var data=[];
    return {
        initAmChart_inquiry: function(graph_data_result) {
            if (typeof(AmCharts)==='undefined' || $('#inquiry').size()===0) {
                return;
            }
            // alert("test");
            var data=graph_data_result;
           // alert(JSON.stringify(data));
            var chart=AmCharts.makeChart("inquiry", {
                "type": "serial", "maximum":10000000, "fontSize":12, "addClassNames": true, "theme": "light", "path": "../assets/global/plugins/amcharts/ammap/images/", "autoMargins": true, "marginLeft": 30, "marginRight": 8, "marginTop": 10, "marginBottom": 26, "balloon": {
                    "adjustBorderColor": false, "horizontalPadding": 10, "verticalPadding": 8, "color": "#ffffff"
                }
                , "dataProvider": data, "valueAxes": [ {
                    "axisAlpha": 0, "position": "left"
                }
                ], "startDuration": 1, "graphs": [ {
                    "alphaField": "alpha", "balloonText": "<span style='font-size:12px;'>[[title]] on [[category]]:<br><span style='font-size:20px;'>[[value]]</span> [[additional]]</span>", "fillAlphas": 1, "title": "Total Inquiry", "type": "column", "valueField": "revenue", "dashLengthField": "dashLengthColumn"
                }
                ], "categoryField": "date", "categoryAxis": {
                    "gridPosition": "end", "axisAlpha": 0, "tickLength": 0
                }
                , "export": {
                    "enabled": true
                }, "valueAxes": [ {
                    "title": "Total Inquiry"
                }
                , ], "categoryAxis": {
                    "title": "Days",
                }
                ,
            }
            );
        }
        ,
        companyData_inquiry:function() {
            var mL_inquiry = ['','January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            var month_inquiry=$('#inquiry_month').val();
            var m_inquiry = month_inquiry*1;
            var year_inquiry=$('#inquiry_year').val();
            var inquiry_inquiry_created_by= $('#inquiry_inquiry_created_by').val();
            var inquiry_inquiry_assigned_to= $('#inquiry_inquiry_assigned_to').val();
            var inquiry_todate=$('#inquiry_todate').val();
            var inquiry_fromdate=$('#inquiry_fromdate').val();
            var report_inquiry="13";
            var obj=this;
            // alert("test");
            $.ajax( {
                url:"statical_graph_ajax_function.php", data: {
                    mode: "purchaseorder_chart", month: month_inquiry,
                     year: year_inquiry,
                      report:report_inquiry,
                      inquiry_inquiry_created_by:inquiry_inquiry_created_by,
                      inquiry_inquiry_assigned_to:inquiry_inquiry_assigned_to,
                      inquiry_todate:inquiry_todate,
                      inquiry_fromdate:inquiry_fromdate,
                }
                , success:function(result) {
                    // $(".datesso").html(""+mL[m]+" - "+year);

                    result=$.parseJSON(result);
                    // alert(JSON.stringify(result));

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
                    obj.initAmChart_inquiry(graph_data_result);
                }
            }
            )
        }
        ,
        init_inquiry: function() {
            this.companyData_inquiry();
        }
    }
    ;
}
();
// jQuery(document).ready(function() {
//     Graph_inquiry.init_inquiry();
// });
// For Inquiry Chart end


// For Lead Chart start

var Graph_lead=function() {

    var data=[];
    return {
        initAmChart_lead: function(graph_data_result) {
            if (typeof(AmCharts)==='undefined' || $('#lead').size()===0) {
                return;
            }
            // alert("test");
            var data=graph_data_result;
           // alert(JSON.stringify(data));
            var chart=AmCharts.makeChart("lead", {
                "type": "serial", "maximum":10000000, "fontSize":12, "addClassNames": true, "theme": "light", "path": "../assets/global/plugins/amcharts/ammap/images/", "autoMargins": true, "marginLeft": 30, "marginRight": 8, "marginTop": 10, "marginBottom": 26, "balloon": {
                    "adjustBorderColor": false, "horizontalPadding": 10, "verticalPadding": 8, "color": "#ffffff"
                }
                , "dataProvider": data, "valueAxes": [ {
                    "axisAlpha": 0, "position": "left"
                }
                ], "startDuration": 1, "graphs": [ {
                    "alphaField": "alpha", "balloonText": "<span style='font-size:12px;'>[[title]] on [[category]]:<br><span style='font-size:20px;'>[[value]]</span> [[additional]]</span>", "fillAlphas": 1, "title": "Total Lead", "type": "column", "valueField": "revenue", "dashLengthField": "dashLengthColumn"
                }
                ], "categoryField": "date", "categoryAxis": {
                    "gridPosition": "end", "axisAlpha": 0, "tickLength": 0
                }
                , "export": {
                    "enabled": true
                }, "valueAxes": [ {
                    "title": "Total Lead"
                }
                , ], "categoryAxis": {
                    "title": "Days",
                }
                ,
            }
            );
        }
        ,
        companyData_lead:function() {
            var mL_lead = ['','January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            var month_lead=$('#lead_month').val();
            var m_lead = month_lead*1;
            var year_lead=$('#lead_year').val();
            var lead_inquiry_created_by= $('#lead_inquiry_created_by').val();
            var lead_inquiry_assigned_to= $('#lead_inquiry_assigned_to').val();
            var lead_todate=$('#lead_todate').val();
            var lead_fromdate=$('#lead_fromdate').val();
            var report_lead="14";
            var obj=this;
            // alert("test");
            $.ajax( {
                url:"statical_graph_ajax_function.php", data: {
                    mode: "purchaseorder_chart", month: month_lead,
                     year: year_lead,
                      report:report_lead,
                      lead_inquiry_created_by:lead_inquiry_created_by,
                      lead_inquiry_assigned_to:lead_inquiry_assigned_to,
                      lead_todate:lead_todate,
                      lead_fromdate:lead_fromdate,
                }
                , success:function(result) {
                    // $(".datesso").html(""+mL[m]+" - "+year);

                    result=$.parseJSON(result);
                    // alert(JSON.stringify(result));

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
                    obj.initAmChart_lead(graph_data_result);
                }
            }
            )
        }
        ,
        init_lead: function() {
            this.companyData_lead();
        }
    }
    ;
}
();
// jQuery(document).ready(function() {
//     Graph_lead.init_lead();
// });
// For Lead Chart end



// For dispatch Chart start

var Graph_dispatch=function() {

    var data=[];
    return {
        initAmChart_dispatch: function(graph_data_result) {
            if (typeof(AmCharts)==='undefined' || $('#dispatch').size()===0) {
                return;
            }
            // alert("test");
            var data=graph_data_result;
           // alert(JSON.stringify(data));
            var chart=AmCharts.makeChart("dispatch", {
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
        companyData_dispatch:function() {
            var mL_dispatch = ['','January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            var month_dispatch=$('#dispatch_month').val();
            var m_dispatch = month_dispatch*1;
            var year_dispatch=$('#dispatch_year').val();
            var dispatch_todate=$('#dispatch_todate').val();
            var dispatch_sales_id=$('#dispatch_sales_id').val();
            var dispatch_customer_id=$('#dispatch_customer_id').val();
            var dispatch_fromdate=$('#dispatch_fromdate').val();
            var report_dispatch="16";
            var obj=this;
            // alert("test");
            $.ajax( {
                url:"statical_graph_ajax_function.php", data: {
                    mode: "purchaseorder_chart", month: month_dispatch,
                    year: year_dispatch,
                    report:report_dispatch,
                    dispatch_todate:dispatch_todate,
                    dispatch_fromdate:dispatch_fromdate,
                    dispatch_sales_id:dispatch_sales_id,
                    dispatch_customer_id:dispatch_customer_id,


                }
                , success:function(result) {
                    // $(".datesso").html(""+mL[m]+" - "+year);

                    result=$.parseJSON(result);
                    // alert(JSON.stringify(result));

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
                    obj.initAmChart_dispatch(graph_data_result);
                }
            }
            )
        }
        ,
        init_dispatch: function() {
            this.companyData_dispatch();
        }
    }
    ;
}
();
// jQuery(document).ready(function() {
//     Graph_dispatch.init_dispatch();
// });
// For dispatch Chart end



// For invoice Chart start

var Graph_invoice=function() {

    var data=[];
    return {
        initAmChart_invoice: function(graph_data_result) {
            if (typeof(AmCharts)==='undefined' || $('#invoice').size()===0) {
                return;
            }
            // alert("test");
            var data=graph_data_result;
           // alert(JSON.stringify(data));
            var chart=AmCharts.makeChart("invoice", {
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
        companyData_invoice:function() {
            var mL_invoice = ['','January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            var month_invoice=$('#invoice_month').val();
            var m_invoice = month_invoice*1;
            var year_invoice=$('#invoice_year').val();
            var invoice_sales_id=$('#invoice_sales_id').val();
            var invoice_customer_id=$('#invoice_customer_id').val();
            var invoice_todate=$('#invoice_todate').val();
            var invoice_fromdate=$('#invoice_fromdate').val();
            var report_invoice="10";
            var obj=this;
            // alert("test");
            $.ajax( {
                url:"statical_graph_ajax_function.php", data: {
                    mode: "purchaseorder_chart", month: month_invoice,
                     year: year_invoice,
                      report:report_invoice,
                      invoice_sales_id:invoice_sales_id,
                      invoice_customer_id:invoice_customer_id,
                      invoice_todate:invoice_todate,
                      invoice_fromdate:invoice_fromdate,
                }
                , success:function(result) {
                    // $(".datesso").html(""+mL[m]+" - "+year);

                    result=$.parseJSON(result);
                    // alert(JSON.stringify(result));

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
                    obj.initAmChart_invoice(graph_data_result);
                }
            }
            )
        }
        ,
        init_invoice: function() {
            this.companyData_invoice();
        }
    }
    ;
}
();
// jQuery(document).ready(function() {
//     Graph_invoice.init_invoice();
// });
// For invoice Chart end


// For visit Chart start

var Graph_visit=function() {

    var data=[];
    return {
        initAmChart_visit: function(graph_data_result) {
            if (typeof(AmCharts)==='undefined' || $('#visit').size()===0) {
                return;
            }
            // alert("test");
            var data=graph_data_result;
           // alert(JSON.stringify(data));
            var chart=AmCharts.makeChart("visit", {
                "type": "serial", "maximum":10000000, "fontSize":12, "addClassNames": true, "theme": "light", "path": "../assets/global/plugins/amcharts/ammap/images/", "autoMargins": true, "marginLeft": 30, "marginRight": 8, "marginTop": 10, "marginBottom": 26, "balloon": {
                    "adjustBorderColor": false, "horizontalPadding": 10, "verticalPadding": 8, "color": "#ffffff"
                }
                , "dataProvider": data, "valueAxes": [ {
                    "axisAlpha": 0, "position": "left"
                }
                ], "startDuration": 1, "graphs": [ {
                    "alphaField": "alpha", "balloonText": "<span style='font-size:12px;'>[[title]] on [[category]]:<br><span style='font-size:20px;'>[[value]]</span> [[additional]]</span>", "fillAlphas": 1, "title": "Total Visit", "type": "column", "valueField": "revenue", "dashLengthField": "dashLengthColumn"
                }
                ], "categoryField": "date", "categoryAxis": {
                    "gridPosition": "end", "axisAlpha": 0, "tickLength": 0
                }
                , "export": {
                    "enabled": true
                }, "valueAxes": [ {
                    "title": "Total Visit"
                }
                , ], "categoryAxis": {
                    "title": "Days",
                }
                ,
            }
            );
        }
        ,
        companyData_visit:function() {
            var mL_visit = ['','January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            var month_visit=$('#visit_month').val();
            var m_visit = month_visit*1;
            var year_visit=$('#visit_year').val();
            var visit_customer_id=$('#visit_customer_id').val();
            var visit_sales_id=$('#visit_sales_id').val();
            var visit_todate=$('#visit_todate').val();
            var visit_fromdate=$('#visit_fromdate').val();

            var report_visit="11";
            var obj=this;
            // alert("test");
            $.ajax( {
                url:"statical_graph_ajax_function.php", data: {
                    mode: "purchaseorder_chart", month: month_visit,
                     year: year_visit, report:report_visit,
                     visit_customer_id:visit_customer_id,
                     visit_sales_id:visit_sales_id,
                     visit_todate:visit_todate,
                     visit_fromdate:visit_fromdate,
                }
                , success:function(result) {
                    // $(".datesso").html(""+mL[m]+" - "+year);

                    result=$.parseJSON(result);
                    // alert(JSON.stringify(result));

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
                    obj.initAmChart_visit(graph_data_result);
                }
            }
            )
        }
        ,
        init_visit: function() {
            this.companyData_visit();
        }
    }
    ;
}
();
// jQuery(document).ready(function() {
//     Graph_visit.init_visit();
// });
// For visit Chart end



// For complain Chart start

var Graph_complain=function() {

    var data=[];
    return {
        initAmChart_complain: function(graph_data_result) {
            if (typeof(AmCharts)==='undefined' || $('#complain').size()===0) {
                return;
            }
            // alert("test");
            var data=graph_data_result;
           // alert(JSON.stringify(data));
            var chart=AmCharts.makeChart("complain", {
                "type": "serial", "maximum":10000000, "fontSize":12, "addClassNames": true, "theme": "light", "path": "../assets/global/plugins/amcharts/ammap/images/", "autoMargins": true, "marginLeft": 30, "marginRight": 8, "marginTop": 10, "marginBottom": 26, "balloon": {
                    "adjustBorderColor": false, "horizontalPadding": 10, "verticalPadding": 8, "color": "#ffffff"
                }
                , "dataProvider": data, "valueAxes": [ {
                    "axisAlpha": 0, "position": "left"
                }
                ], "startDuration": 1, "graphs": [ {
                    "alphaField": "alpha", "balloonText": "<span style='font-size:12px;'>[[title]] on [[category]]:<br><span style='font-size:20px;'>[[value]]</span> [[additional]]</span>", "fillAlphas": 1, "title": "Total Complain", "type": "column", "valueField": "revenue", "dashLengthField": "dashLengthColumn"
                }
                ], "categoryField": "date", "categoryAxis": {
                    "gridPosition": "end", "axisAlpha": 0, "tickLength": 0
                }
                , "export": {
                    "enabled": true
                }, "valueAxes": [ {
                    "title": "Total Complain"
                }
                , ], "categoryAxis": {
                    "title": "Days",
                }
                ,
            }
            );
        }
        ,
        companyData_complain:function() {
            var mL_complain = ['','January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            var month_complain=$('#complain_month').val();
            var m_complain = month_complain*1;
            var year_complain=$('#complain_year').val();
            var complain_customer_id=$('#complain_customer_id').val();
            var complain_sales_id=$('#complain_sales_id').val();
            var complain_todate=$('#complain_todate').val();
            var complain_fromdate=$('#complain_fromdate').val();
            var report_complain="12";
            var obj=this;
            // alert("test");
            $.ajax( {
                url:"statical_graph_ajax_function.php", data: {
                    mode: "purchaseorder_chart", month: month_complain,
                    year: year_complain,
                    report:report_complain,
                    complain_customer_id:complain_customer_id,
                    complain_sales_id:complain_sales_id,
                    complain_todate:complain_todate,
                     complain_fromdate:complain_fromdate,
                }
                , success:function(result) {
                    // $(".datesso").html(""+mL[m]+" - "+year);

                    result=$.parseJSON(result);
                    // alert(JSON.stringify(result));

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
                    obj.initAmChart_complain(graph_data_result);
                }
            }
            )
        }
        ,
        init_complain: function() {
            this.companyData_complain();
        }
    }
    ;
}
();
// jQuery(document).ready(function() {
//     Graph_complain.init_complain();
// });
// For complain Chart end


// For prospect Chart start

var Graph_prospect=function() {

    var data=[];
    return {
        initAmChart_prospect: function(graph_data_result) {
            if (typeof(AmCharts)==='undefined' || $('#prospect').size()===0) {
                return;
            }
            // alert("test");
            var data=graph_data_result;
           // alert(JSON.stringify(data));
            var chart=AmCharts.makeChart("prospect", {
                "type": "serial", "maximum":10000000, "fontSize":12, "addClassNames": true, "theme": "light", "path": "../assets/global/plugins/amcharts/ammap/images/", "autoMargins": true, "marginLeft": 30, "marginRight": 8, "marginTop": 10, "marginBottom": 26, "balloon": {
                    "adjustBorderColor": false, "horizontalPadding": 10, "verticalPadding": 8, "color": "#ffffff"
                }
                , "dataProvider": data, "valueAxes": [ {
                    "axisAlpha": 0, "position": "left"
                }
                ], "startDuration": 1, "graphs": [ {
                    "alphaField": "alpha", "balloonText": "<span style='font-size:12px;'>[[title]] on [[category]]:<br><span style='font-size:20px;'>[[value]]</span> [[additional]]</span>", "fillAlphas": 1, "title": "Total Raw Data", "type": "column", "valueField": "revenue", "dashLengthField": "dashLengthColumn"
                }
                ], "categoryField": "date", "categoryAxis": {
                    "gridPosition": "end", "axisAlpha": 0, "tickLength": 0
                }
                , "export": {
                    "enabled": true
                }, "valueAxes": [ {
                    "title": "Total Raw Data"
                }
                , ], "categoryAxis": {
                    "title": "Days",
                }
                ,
            }
            );
        }
        ,
        companyData_prospect:function() {
            var mL_prospect = ['','January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            var month_prospect=$('#prospect_month').val();
            var m_prospect = month_prospect*1;
            var year_prospect= $('#prospect_year').val();
            var prospect_inquiry_created_by= $('#prospect_inquiry_created_by').val();
            var prospect_inquiry_assigned_to= $('#prospect_inquiry_assigned_to').val();
            var todate=$('#todate').val();
            var fromdate=$('#fromdate').val();

            var report_prospect="15";
            var obj=this;
            // alert("test");
            $.ajax( {
                url:"statical_graph_ajax_function.php", data: {
                    mode: "purchaseorder_chart", month: month_prospect, year: year_prospect, report:report_prospect,prospect_inquiry_created_by:prospect_inquiry_created_by,prospect_inquiry_assigned_to:prospect_inquiry_assigned_to,todate:todate,fromdate:fromdate,
                }
                , success:function(result) {
                    // $(".datesso").html(""+mL[m]+" - "+year);

                    result=$.parseJSON(result);
                    // alert(JSON.stringify(result));

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
                    obj.initAmChart_prospect(graph_data_result);
                }
            }
            )
        }
        ,
        init_prospect: function() {
            this.companyData_prospect();
        }
    }
    ;
}
();
/*jQuery(document).ready(function() {
    Graph_prospect.init_prospect();
});*/
// For prospect Chart end




// For expense Chart start

var Graph_expense=function() {

    var data=[];
    return {
        initAmChart_expense: function(graph_data_result) {
            if (typeof(AmCharts)==='undefined' || $('#expense').size()===0) {
                return;
            }
            // alert("test");
            var data=graph_data_result;
           // alert(JSON.stringify(data));
            var chart=AmCharts.makeChart("expense", {
                "type": "serial", "maximum":10000000, "fontSize":12, "addClassNames": true, "theme": "light", "path": "../assets/global/plugins/amcharts/ammap/images/", "autoMargins": true, "marginLeft": 30, "marginRight": 8, "marginTop": 10, "marginBottom": 26, "balloon": {
                    "adjustBorderColor": false, "horizontalPadding": 10, "verticalPadding": 8, "color": "#ffffff"
                }
                , "dataProvider": data, "valueAxes": [ {
                    "axisAlpha": 0, "position": "left"
                }
                ], "startDuration": 1, "graphs": [ {
                    "alphaField": "alpha", "balloonText": "<span style='font-size:12px;'>[[title]] on [[category]]:<br><span style='font-size:20px;'>[[value]]</span> [[additional]]</span>", "fillAlphas": 1, "title": "Amount In INR(₹)", "type": "column", "valueField": "revenue", "dashLengthField": "dashLengthColumn"
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
        companyData_expense:function() {
            var mL_expense = ['','January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            var month_expense=$('#expense_month').val();
            var m_expense = month_expense*1;
            var year_expense= $('#expense_year').val();
            var expense_sales_id=$('#expense_sales_id').val();
            var expense_todate=$('#expense_todate').val();
            var expense_fromdate=$('#expense_fromdate').val();
            var report_expense="17";
            var obj=this;
            // alert("test");
            $.ajax( {
                url:"statical_graph_ajax_function.php", data: {
                    mode: "purchaseorder_chart", month: month_expense,
                    year: year_expense,
                    report:report_expense,
                    expense_sales_id:expense_sales_id,
                    expense_todate:expense_todate,
                    expense_fromdate:expense_fromdate,

                }
                , success:function(result) {
                    // $(".datesso").html(""+mL[m]+" - "+year);

                    result=$.parseJSON(result);
                    // alert(JSON.stringify(result));

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
                    obj.initAmChart_expense(graph_data_result);
                }
            }
            )
        }
        ,
        init_expense: function() {
            this.companyData_expense();
        }
    }
    ;
}
();
/*jQuery(document).ready(function() {
    Graph_expense.init_expense();
});*/
// For expense Chart end



// For leave Chart start

var Graph_leave=function() {

    var data=[];
    return {
        initAmChart_leave: function(graph_data_result) {
            if (typeof(AmCharts)==='undefined' || $('#leave').size()===0) {
                return;
            }
            // alert("test");
            var data=graph_data_result;
           // alert(JSON.stringify(data));
            var chart=AmCharts.makeChart("leave", {
                "type": "serial", "maximum":10000000, "fontSize":12, "addClassNames": true, "theme": "light", "path": "../assets/global/plugins/amcharts/ammap/images/", "autoMargins": true, "marginLeft": 30, "marginRight": 8, "marginTop": 10, "marginBottom": 26, "balloon": {
                    "adjustBorderColor": false, "horizontalPadding": 10, "verticalPadding": 8, "color": "#ffffff"
                }
                , "dataProvider": data, "valueAxes": [ {
                    "axisAlpha": 0, "position": "left"
                }
                ], "startDuration": 1, "graphs": [ {
                    "alphaField": "alpha", "balloonText": "<span style='font-size:12px;'>[[title]] on [[category]]:<br><span style='font-size:20px;'>[[value]]</span> [[additional]]</span>", "fillAlphas": 1, "title": "Total Leave", "type": "column", "valueField": "revenue", "dashLengthField": "dashLengthColumn"
                }
                ], "categoryField": "date", "categoryAxis": {
                    "gridPosition": "end", "axisAlpha": 0, "tickLength": 0
                }
                , "export": {
                    "enabled": true
                }, "valueAxes": [ {
                    "title": "Total Leave"
                }
                , ], "categoryAxis": {
                    "title": "Days",
                }
                ,
            }
            );
        }
        ,
        companyData_leave:function() {
            var mL_leave = ['','January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            var month_leave=$('#leave_month').val();
            var m_leave = month_leave*1;
            var year_leave= $('#leave_year').val();
            var leave_sales_id= $('#leave_sales_id').val();
            var leave_todate=$('#leave_todate').val();
            var leave_fromdate=$('#leave_fromdate').val();

            var report_leave="18";
            var obj=this;
            // alert("test");
            $.ajax( {
                url:"statical_graph_ajax_function.php", data: {
                    mode: "purchaseorder_chart", month: month_leave,
                     year: year_leave,
                      report:report_leave,
                      leave_sales_id:leave_sales_id,
                       leave_todate:leave_todate,
                    leave_fromdate:leave_fromdate,
                }
                , success:function(result) {
                    // $(".datesso").html(""+mL[m]+" - "+year);

                    result=$.parseJSON(result);
                    // alert(JSON.stringify(result));

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
                    obj.initAmChart_leave(graph_data_result);
                }
            }
            )
        }
        ,
        init_leave: function() {
            this.companyData_leave();
        }
    }
    ;
}
();
/*jQuery(document).ready(function() {
    Graph_leave.init_leave();
});*/
// For leave Chart end




// For followup Chart start

var Graph_followup=function() {

    var data=[];
    return {
        initAmChart_followup: function(graph_data_result) {
            if (typeof(AmCharts)==='undefined' || $('#followup').size()===0) {
                return;
            }
            // alert("test");
            var data=graph_data_result;
           // alert(JSON.stringify(data));
            var chart=AmCharts.makeChart("followup", {
                "type": "serial", "maximum":10000000, "fontSize":12, "addClassNames": true, "theme": "light", "path": "../assets/global/plugins/amcharts/ammap/images/", "autoMargins": true, "marginLeft": 30, "marginRight": 8, "marginTop": 10, "marginBottom": 26, "balloon": {
                    "adjustBorderColor": false, "horizontalPadding": 10, "verticalPadding": 8, "color": "#ffffff"
                }
                , "dataProvider": data, "valueAxes": [ {
                    "axisAlpha": 0, "position": "left"
                }
                ], "startDuration": 1, "graphs": [ {
                    "alphaField": "alpha", "balloonText": "<span style='font-size:12px;'>[[title]] on [[category]]:<br><span style='font-size:20px;'>[[value]]</span> [[additional]]</span>", "fillAlphas": 1, "title": "Total followup", "type": "column", "valueField": "revenue", "dashLengthField": "dashLengthColumn"
                }
                ], "categoryField": "date", "categoryAxis": {
                    "gridPosition": "end", "axisAlpha": 0, "tickLength": 0
                }
                , "export": {
                    "enabled": true
                }, "valueAxes": [ {
                    "title": "Total followup"
                }
                , ], "categoryAxis": {
                    "title": "Days",
                }
                ,
            }
            );
        }
        ,
        companyData_followup:function() {
            var mL_followup = ['','January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            var month_followup=$('#followup_month').val();
            var m_followup = month_followup*1;
            var year_followup=$('#followup_year').val();
            var followup_customer_id=$('#followup_customer_id').val();
            var followup_sales_id=$('#followup_sales_id').val();
            var followup_todate=$('#followup_todate').val();
            var followup_fromdate=$('#followup_fromdate').val();
            var report_followup="24";
            var obj=this;
            // alert("test");
            $.ajax( {
                url:"statical_graph_ajax_function.php", data: {
                    mode: "purchaseorder_chart", month: month_followup,
                     year: year_followup,
                      report:report_followup,
                      followup_customer_id:followup_customer_id,
                      followup_sales_id:followup_sales_id,
                      followup_todate:followup_todate,
                    followup_fromdate:followup_fromdate,
                }
                , success:function(result) {
                    // $(".datesso").html(""+mL[m]+" - "+year);

                    result=$.parseJSON(result);
                    // alert(JSON.stringify(result));

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
                    obj.initAmChart_followup(graph_data_result);
                }
            }
            )
        }
        ,
        init_followup: function() {
            this.companyData_followup();
        }
    }
    ;
}
();
// jQuery(document).ready(function() {
//     Graph_followup.init_followup();
// });
// For followup Chart end


// For attendance Chart start

var Graph_attendance=function() {

    var data=[];
    return {
        initAmChart_attendance: function(graph_data_result) {
            if (typeof(AmCharts)==='undefined' || $('#attendance').size()===0) {
                return;
            }
            // alert("test");
            var data=graph_data_result;
           // alert(JSON.stringify(data));
            var chart=AmCharts.makeChart("attendance", {
                "type": "serial", "maximum":10000000, "fontSize":12, "addClassNames": true, "theme": "light", "path": "../assets/global/plugins/amcharts/ammap/images/", "autoMargins": true, "marginLeft": 30, "marginRight": 8, "marginTop": 10, "marginBottom": 26, "balloon": {
                    "adjustBorderColor": false, "horizontalPadding": 10, "verticalPadding": 8, "color": "#ffffff"
                }
                , "dataProvider": data, "valueAxes": [ {
                    "axisAlpha": 0, "position": "left"
                }
                ], "startDuration": 1, "graphs": [ {
                    "alphaField": "alpha", "balloonText": "<span style='font-size:12px;'>[[title]] on [[category]]:<br><span style='font-size:20px;'>[[value]]</span> [[additional]]</span>", "fillAlphas": 1, "title": "Total attendance", "type": "column", "valueField": "revenue", "dashLengthField": "dashLengthColumn"
                }
                ], "categoryField": "date", "categoryAxis": {
                    "gridPosition": "end", "axisAlpha": 0, "tickLength": 0
                }
                , "export": {
                    "enabled": true
                }, "valueAxes": [ {
                    "title": "Total attendance"
                }
                , ], "categoryAxis": {
                    "title": "Days",
                }
                ,
            }
            );
        }
        ,
        companyData_attendance:function() {
            var mL_attendance = ['','January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            var month_attendance=$('#attendance_month').val();
            var m_attendance = month_attendance*1;
            var year_attendance=$('#attendance_year').val();
            var attendance_customer_id=$('#attendance_customer_id').val();
            var attendance_sales_id=$('#attendance_sales_id').val();
            var attendance_todate=$('#attendance_todate').val();
            var attendance_fromdate=$('#attendance_fromdate').val();
            var report_attendance="25";
            var obj=this;
            // alert("test");
            $.ajax( {
                url:"statical_graph_ajax_function.php", data: {
                    mode: "purchaseorder_chart", month: month_attendance,
                     year: year_attendance,
                      report:report_attendance,
                      attendance_customer_id:attendance_customer_id,
                      attendance_sales_id:attendance_sales_id,
                      attendance_todate:attendance_todate,
                    attendance_fromdate:attendance_fromdate,
                }
                , success:function(result) {
                    // $(".datesso").html(""+mL[m]+" - "+year);

                    result=$.parseJSON(result);
                    // alert(JSON.stringify(result));

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
                    obj.initAmChart_attendance(graph_data_result);
                }
            }
            )
        }
        ,
        init_attendance: function() {
            this.companyData_attendance();
        }
    }
    ;
}
();
// jQuery(document).ready(function() {
//     Graph_attendance.init_attendance();
// });
// For attendance Chart end






















