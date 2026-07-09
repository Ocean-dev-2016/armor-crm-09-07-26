$('#ToDate').datepicker({  datepicker: true, autoclose: true, dateFormat: 'dd-mm-yy' });

// for offline offline toggle button and data
$('#panel').addClass("floating panel");
$('#panel').css("display", "none");
$('#toggle').show();

$('#toggle').click(function(e) {
    if ($('#panel').css("display") != 'none') {
        $('#toggle').html(img_path_off);
        $('#panel').hide();
    } else {
        $('#toggle').html(img_path_off);
        $('#device_toggle').html(img_path_device);
        $('#pin_toggle').html(img_path_location);
        $('#pin_panel').hide();
        $('#device_panel').hide();
        $('#panel').show();
    }
});
// end for offline offline toggle button and data

// for offline offline toggle data load more
size_li = $("#myList li").size();
all = x = 5;
if (x >= size_li) {
    $('#loadMore').hide();
}
$('#myList li:lt(' + x + ')').show();
$('#showLess').hide();
$('#loadMore').click(function() {
    x = (x + 5 <= size_li) ? x + 5 : size_li;
    $('#myList li:lt(' + x + ')').show();
    $('#showLess').show();
    if (x >= size_li) {
        $('#loadMore').hide();
    }
});
$('#showLess').click(function() {
    x = (x - 5 < 0) ? 3 : x - 5;
    if (x <= all) {
        x = all;
        $('#showLess').hide();
    }
    $('#myList li').not(':lt(' + x + ')').hide();
    $('#loadMore').show();
});
// end for offline offline toggle data load more

// for pin information toggle button and data
$('#pin_panel').addClass("floating panel");
$('#pin_panel').css("display", "none");
$('#pin_toggle').show();

$('#pin_toggle').click(function(e) {
    if ($('#pin_panel').css("display") != 'none') {
        $('#pin_toggle').html(img_path_location);
        $('#pin_panel').hide();
    } else {
        $('#pin_toggle').html(img_path_location);
        $('#toggle').html(img_path_off);
        $('#device_toggle').html(img_path_device);
        $('#panel').hide();
        $('#device_panel').hide();
        $('#pin_panel').show();
    }
});
// end for pin information toggle button and data

// for device information toggle button and data
$('#device_panel').addClass("floating panel deivcepan");
$('#device_panel').css("display", "none");
$('#device_toggle').show();

$('#device_toggle').click(function(e) {
    if ($('#device_panel').css("display") != 'none') {
        $('#device_toggle').html(img_path_device);
        $('#device_panel').hide();
    } else {
        $('#device_toggle').html(img_path_device);
        $('#toggle').html(img_path_off);
        $('#pin_toggle').html(img_path_location);
        $('#panel').hide();
        $('#pin_panel').hide();
        $('#device_panel').show();
    }
});
// end for device information toggle button and data


// dashboard data get
$(document).ready(function() {
    var map = 'last';
    var reqheight = $(window).height() - 145;
    $(".com-height").css("height", reqheight);
    $(".fix-height").css("height", reqheight - 220);//340
    // getDataMap(0, map);
    $("#ToDate").change(function(){
        getDataMap($('#selected_user').val(), $('#selected_map').val());
    });
});

function getDataMap(id, map) {
    
    $('#panel').hide();
    $('#pin_panel').hide();
    $('#device_panel').hide();
    $('#toggle').html(img_path_off);
    $('#pin_toggle').html(img_path_location);
    $('#device_toggle').html(img_path_device);
    var torf = true;
    
    var selectul = $('#selected_map').val();
    // alert(selectul)
    // alert(id)
    if (id != 0)
    {
        if(map=="last")
        {
            $(".main-html-ul").html('Last <i class="fa fa-map-marker"></i><span class = "caret"></span>');
        }
        if(map=="route")
        {
            $(".main-html-ul").html('Route <i class="fa fa-sitemap"></i><span class = "caret"></span>');
        }
        if(map=="visit")
        {
            $(".main-html-ul").html('Visit <i class="fa fa-bus"></i><span class = "caret"></span>');
        }
        if(map=="attendance")
        {
            $(".main-html-ul").html('Attendance <i class="fa fa-clock-o"></i><span class = "caret"></span>');
        }
        if(map=="daily_report")
        {
            $(".main-html-ul").html('Daily Report <i class="fa fa-file"></i><span class = "caret"></span>');
        }
        if(map=="tracking_map")
        {
            $(".main-html-ul").html('Tracking Map <i class="fa fa-globe"></i><span class = "caret"></span>');
        }
    }
    else
    {
        $(".main-html-ul").html('Last <i class="fa fa-map-marker"></i><span class = "caret"></span>');
    }

    if (id == 0 && $('#selected_map').val() == 'last') {
        if (id == 0 && map == 'route') {
            var torf = false;
            toastr.error("Select At Least One Sales Officer For Route");
        } else if (id == 0 && map == 'visit') {
            var torf = false;
            toastr.error("Select At Least One Sales Officer For Visit");
        } else if (id == 0 && map == 'attendance') {
            var torf = false;
            toastr.error("Select At Least One Sales Officer For Attendance");
        }
        else if (id == 0 && map == 'daily_report') {
            var torf = false;
            toastr.error("Select At Least One Sales Officer For Daily Report");
        }else if (id == 0 && map == 'tracking_map') {
            var torf = false;
            toastr.error("Select At Least One Sales Officer For Tracking Map");
        }
    }
    if (torf) {
        var today = new Date();
        var date = today.getDate()+'-'+(today.getMonth()+1)+'-'+today.getFullYear();
        var time = today.getHours() + ":" + today.getMinutes() + ":" + today.getSeconds();
        var dateTime = date+' '+time;
        $(".lastupdate").html(dateTime);
        if (id == 0) {
            map = 'last'
        }
        var ajax_url = "";
        $(".nocol").removeClass("bg-yello");
        $(".user" + id).addClass("bg-yello");
        $('#selected_user').val(id);
        $('#selected_map').val(map);
        var date = $('#ToDate').val();

        $(".allbtn").removeClass("green-col");
        if (map == 'last') {
            $("." + map).addClass("green-col");
            ajax_url = "last_pin_d.php";
        } else if (map == 'route') {
            $("." + map).addClass("green-col");
            ajax_url = "all_pin_d.php";
        } else if (map == 'visit') {
            $("." + map).addClass("green-col");
            ajax_url = "visit_get_ajax_dashboard.php";
        } else if (map == 'attendance') {
            $("." + map).addClass("green-col");
            ajax_url = "attendance_get_ajax_dashboard.php";
        } else if (map == 'maproute') {
            $("." + map).addClass("green-col");
            ajax_url = "maproute_d.php";
        } else if (map == 'customerwise') {
            $("." + map).addClass("green-col");
            ajax_url = "customerwise_get_ajax_dashboard.php";
        }
        else if (map == 'daily_report') {
            $("." + map).addClass("green-col");
            ajax_url = "daily_report_get_ajax_dashboard.php";
        }else if (map == 'tracking_map') {
            $("." + map).addClass("green-col");
            ajax_url = "map_snaproad_d.php";
        }
        reqheight = $(window).height() - 180;
        var se = (id == 0) ? "" : id;
        $.ajax({
            url: ajax_url,
            data: {
                id: se,
                date: date,
                reqheight: reqheight,
            },
            beforeSend: function() {
                $("#mapd").html("<div class='row text-center'><div class='col-sm-12'><h2><i class='fa fa-refresh fa-spin'></i>&nbsp;Loading..</h2></div></div>");
            },
            success: function(result) {
                $("#mapd").html(result);
                 DeviceInfo(se);
            }
        });
        function DeviceInfo(id)
        {
            reqheight = $(window).height() - 1200;
            $.ajax({
                url: "get_device_info.php",
                data: {
                    id: id,
                    reqheight: reqheight,
                },
                success: function(result) {
                    $(".dvinfo").html(result);
                }
            });
        }
    }
}
