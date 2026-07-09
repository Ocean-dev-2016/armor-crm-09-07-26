$("#printModal").on('hidden.bs.modal', function () {
    $(".print_data").html("<div class='row text-center'><div class='col-sm-12'><h2><i class='fa fa-refresh fa-spin'></i>&nbsp;Loading..</h2></div></div>");
});
$("#printModal").on('shown.bs.modal', function () {
    $.ajax({
        url: "../ajax_get_print_field_in_register.php",
        method: "POST",
        data: {
            ctable: tableName
        },
        beforeSend: function () {
            $(".print_data").html("<div class='row text-center'><div class='col-sm-12'><h2><i class='fa fa-refresh fa-spin'></i>&nbsp;Loading..</h2></div></div>");
        },
        success: function (result) {
            $(".print_data").html(result);
        }
    });
});
$(".common_print_btn").on("click", function () {
    var checkedNum = $('input[name="pa_field[]"]:checked').length;
    if (checkedNum > 0) {
        var print1 = [];
        $(".print_data").find('.checkbox-select[type=checkbox]:checked').each(function () {
            print1.push($(this).val());
        });
        var idp = $("#printmodalid").val();
        var nmp = $("#printmodalnm").val();
        expotFunction(idp, nmp, print1);
    } else {
        toastr.error("Please Select atleast one field to get Print");
    }
});

$("#excelModal").on('hidden.bs.modal', function () {
    $(".excel_data").html("<div class='row text-center'><div class='col-sm-12'><h2><i class='fa fa-refresh fa-spin'></i>&nbsp;Loading..</h2></div></div>");
});
$("#excelModal").on('shown.bs.modal', function () {
    $.ajax({
        url: "../ajax_get_excel_field_in_register.php",
        method: "POST",
        data: {
            ctable: tableName
        },
        beforeSend: function () {
            $(".excel_data").html("<div class='row text-center'><div class='col-sm-12'><h2><i class='fa fa-refresh fa-spin'></i>&nbsp;Loading..</h2></div></div>");
        },
        success: function (result) {
            $(".excel_data").html(result);
        }
    });
});
$(".common_excel_btn").on("click", function () {
    var checkedNum = $('input[name="pa_field[]"]:checked').length;
    if (checkedNum > 0) {
        var excel1 = [];
        $(".excel_data").find('.checkbox-select[type=checkbox]:checked').each(function () {
            excel1.push($(this).val());
        });
        var idp = $("#excelmodalid").val();
        var nmp = $("#excelmodalnm").val();
        expotFunction(idp, nmp, excel1);
    } else {
        toastr.error("Please Select atleast one field to get Print");
    }
});


function download(url, filename) {
    fetch(url).then(function (t) {
        return t.blob().then((b) => {
            var a = document.createElement("a");
            a.href = URL.createObjectURL(b);
            a.target = "_blank";
            a.setAttribute("download", filename);
            a.click();
        });
    });
}

function expotFunctionModal(id = "", nm = "") {
    if (id == "printTable") {
        $("#printmodalid").val(id);
        $("#printmodalnm").val(nm);
        $("#printModal").modal("show");
    } else if (id == "excelExport") {
        $("#excelmodalid").val(id);
        $("#excelmodalnm").val(nm);
        $("#excelModal").modal("show");
    }
}

function printtag(tagid) {
    var hashid = "#" + tagid;
    var tagname = $(hashid).prop("tagName").toLowerCase();
    var attributes = "";
    var attrs = document.getElementById(tagid).attributes;
    $.each(attrs, function (i, elem) {
        attributes += " " + elem.name + " ='" + elem.value + "' ";
    })
    var divToPrint = $(hashid).html();
    var head = "<html><head>" + $("head").html() + "</head>";
    var allcontent = head + "<body  onload='window.print()' >" + "<" + tagname + attributes + ">" + divToPrint + "</" + tagname + ">" + "</body></html>";
    var newWin = window.open('', 'Print-Window');
    newWin.document.open();
    newWin.document.write(allcontent);
    newWin.document.close();
}

function expotFunction(id, nm, columnsArray = []) {

    var table_Name = tableName;

    // it is use only for expired_dsc_register 
    var url = window.location.pathname;
    var filename = url.substring(url.lastIndexOf('/') + 1);
    if (filename == "expired_dsc_register.php") {
        var table_Name = "expired_dsc_register";
    }
    // it is use only for expired_dsc_register 
    if (filename == "audit_query_sheet.php" || filename == "audit_query_sheet_new.php") {
        var reqid = $("#ReqId").val();
    } else {
        var reqid = "";
    }

    if (filename == "primary_bank_book.php" || filename == "secondary_bank_book.php") {
        var ac_holder = $("#account_holder").val();
    } else {
        var ac_holder = "";
    }


    if (filename == "gst_return_register_new.php") {
        var type_of_return = $("#type_of_return").val();
        var months = $("#months").val();
    } else {
        var type_of_return = ""
        var months = "";
    }

    if (filename == "summary_of_accounts.php") {
        var bank_account_type = $("#bank_account_type").val();
    } else {
        var bank_account_type = "";
    }

    if (id != "") {
        var fYear = "";
        fYear = $("#f_year").val();
        var f = 0
        var filterQstr = ""
        var filterinfo = $("#jqxgrid").jqxGrid('getfilterinformation');
        $.each(filterinfo, function (i, val) {
            filterQstr += "&filterdatafield" + f + "=" + filterinfo[i].datafield
            filterQstr += "&" + filterinfo[i].datafield + "operator=" + filterinfo[i].filter.operator
            $.each(filterinfo[i].filter.getfilters(), function (x) {
                filterQstr += "&filtervalue" + f + "=" + filterinfo[i].filter.getfilters()[x].value
                filterQstr += "&filtercondition" + f + "=" + filterinfo[i].filter.getfilters()[x].condition
                filterQstr += "&filteroperator" + f + "=" + filterinfo[i].filter.getfilters()[x].operator
                //add up filters:
                f += 1
            });
        });

        $.ajax({
            method: "POST",
            url: table_Name + "_export.php",
            data: "fYear=" + fYear + "&method=" + id + "&methodnm=" + nm + "&columnsArray=" + columnsArray + "&export=" + true + filterQstr + "&reqid=" + reqid + "&ac_holder=" + ac_holder + "&type_of_return=" + type_of_return + "&months=" + months + "&bank_account_type=" + bank_account_type,
            beforeSend: function () {
                // $.blockUI();
            },
            success: function (result) {
                // $.unblockUI();
                if (id == "excelExport") {
                    result = JSON.parse(result);
                    download(result.link, result.file_name);
                } else if (id == "printTable") {
                    $(".tbl").html(result);
                    printtag("printTable");
                }

            }
        });
    }
}

function post_to_url(path, params, method) {
    method = method || "post";

    var form = document.createElement("form");
    form.setAttribute("method", method);
    form.setAttribute("action", path);

    for (var key in params) {
        if (params.hasOwnProperty(key)) {
            var hiddenField = document.createElement("input");
            hiddenField.setAttribute("type", "hidden");
            hiddenField.setAttribute("name", key);
            hiddenField.setAttribute("value", params[key]);

            form.appendChild(hiddenField);
        }
    }

    document.body.appendChild(form);
    form.submit();
}

function LastUpdatedBy() {
    var f_year = $("#f_year").val();
    if (tableName != "summary_of_accounts") {
        $.ajax({
            method: 'POST',
            url: "ajax_get_last_update.php",
            data: {
                table: tableName,
                f_year: f_year,
            },
            success: function (result) {
                if (result == "") {
                    $(".last-updateby").parent().css("display", "none");
                } else {
                    $(".last-updateby").parent().css("display", "block");
                }
                $(".last-updateby").html(result);
            }
        });
    }
}

$(document).ready(function () {
    // LastUpdatedBy();
    $("#printModal").css("display", "");
    $("#excelModal").css("display", "");
});