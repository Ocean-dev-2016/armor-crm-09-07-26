$( document ).ready(function() {
    FetchTaskStatus();
});

var query = "";
var filter_status = "";
var assigned_by = "";
var label = "";
var assigned_to = "";
var date_range = "";
var task_type = "";
var show_hidden_task = "";

function FetchTaskStatus() {
    $.ajax({
        url: url,
        type: "POST",
        data: {
            m: "gtc",
            view: CurrentView,
            admin: AdminView,
            type : type,
        },
        beforeSend: function() {
            $(".task-container").html("<div class='col-sm-12 h1'><h1 class='text-center'>Loading...</h1></div>")
            $(".task-refresh-btn").find("i").addClass("fa-spin");
        },
        success: function(result) {
            $(".task-refresh-btn").find("i").removeClass("fa-spin");
            $(".task-container").html(result);
            TaskComponent();
            FetchTasks();
        },
        error: function() {
            $(".task-refresh-btn").find("i").removeClass("fa-spin");
            aj.Snackbar("Something went wrong with server try again")
        }
    })
}

function TaskComponent() {
    $("body").on('click', '.open-task', function(e) {
        var href = $(this).closest(".task-card").data("href");
        window.open(href);
    })
    $("body").on('click', '.btn-task-card-more', function(e) {
        // e.stopPropagation();
    })
    $(".task-card").draggable({
        snap: ".task-body",
        snapMode: "inner",
        appendTo: ".task-body",
        zIndex: 1000000,
    });

    $(".task-body").droppable({
        classes: {
            "ui-droppable-active": "ui-state-default"
        },
        greedy: true,
        accept: ".task-card",
        tolarance: 'touch',
        drop: function(event, ui) {
            $(ui.draggable).detach().css({ top: 0, left: 0 }).appendTo(this);
            var TaskId = $(ui.draggable).data("id");
            var TaskNewStatus = $(this).closest(".task-stage").data("id");
            UpdateTaskStatus(TaskId, TaskNewStatus);
        }
    });

    $(".task-body").sortable({
        connectWith: ".task-body"
    });
}

function FetchTasks() {
    $.ajax({
        url: url,
        type: "POST",
        data: {
            m: "gt",
            query: query,
            assigned_by: assigned_by,
            label: label,
            assigned_to: assigned_to,
            status: filter_status,
            date_range: date_range,
            show_hidden_task: show_hidden_task,
            admin: AdminView,
            task_type: task_type,
            type : type,
            customer_id : $("#cusid").val(),
            searchName : $("#searchName").val(),
            page_id : $("#page_id").val(),
            sales_id : $("#sales_id").val(),
            customer_type : $("#customer_type").val(),
            df1 : $("#material_request_filter_input").val(),
        },
        beforeSend: function() {    
            // $("#loading-modal").modal('show');
            $(".task-refresh-btn").find("i").addClass("fa-spin");
            $(".task-row-container").html("");
            $(".task-body").html("");
        },
        success: function(result) {
            $("#loading-modal").modal('hide');
            $(".task-refresh-btn").find("i").removeClass("fa-spin");
            var result = $.parseJSON(result);
            var result = (result);
            if (result.a == 1) {
                $.each(result.result, function(i, Task) {
                    CreateTaskCard(Task.total_quotation,Task.id,Task.company_name,Task.person_name,Task.grand_total,Task.inquiry_date,Task.sales_executive_id,Task.id,Task.mobile_number,Task.customer_type,Task.isActive,Task.customer_id,Task.customer_name,Task.sales_executive_name,Task.country,Task.state,Task.city,Task.status,Task.inq_assign_to,Task.email_address);
                });

                $.each(result.total_quotation, function(i, task) {
                    $(".quotation-count-"+i).html(task);
                });
                /*$.each(result.total_order, function(i, task) {
                    $(".order-count-"+i).html(task);
                });*/

                toastr.success(result.mg)
                // aj.Snackbar(result.mg)

            } else {
                $(".all-quotation-count").html("0.00");
                $(".all-order-count").html("0.00");
                toastr.error(result.mg)
                // aj.Snackbar(result.mg)
            }
        },
        error: function() {
            $("#loading-modal").modal('hide');
            $(".task-refresh-btn").find("i").removeClass("fa-spin");
            toastr.error("Something went wrong with server try again")
            // aj.Snackbar("Something went wrong with server try again")
        }
    })
}

function CreateTaskCard(total_quotation=0,TaskID,company_name,person_name,grand_total,inquiry_date,sales_executive_id,id,mobile_number,customer_type,IsActive,customer_id,customer_name,sales_executive_name,country,state,city,status,inq_assign_to,email_address,TaskPriority=1,taskcolor="green",TaskBy=1,TaskName="",TaskAssignedBy="") {
        
    var Task_date = $.datepicker.formatDate("dd-M-yy", new Date(inquiry_date));
    if (CurrentView == 0) {
        var icon = "";
        var taskcolor = "";
        var followup_btn = "";
       

        if ($("#TaskCard" + TaskID).length > 0) {
            $("#TaskCard" + TaskID).remove();
        }

        if (IsActive == 1) {
            hide_btn = '<li><a data-id="' + TaskID + '" href="javascript:void(0);" class="task-archive-btn" title="Hide"><i class="fa fa-eye-slash"></i>&nbsp;Hide</a></li>';
        } else {
            hide_btn = '<li><a data-id="' + TaskID + '" href="javascript:void(0);" class="task-archive-btn1" title="Hide"><i class="fa fa-eye-slash"></i>&nbsp;UnHide</a></li>';
        }

        if(status==0)
        {
            taskcolor = "#7bd0a9";
        }
        else if(status==1)
        {
            taskcolor = "#9fc1ff";
        }
        else if(status==4)
        {
            taskcolor = "#65B237";
        }
        else if(status==5)
        {
            taskcolor = "#3787B2";
        }
        else if(status==6)
        {
            taskcolor = "#B2A137";
        }
        else if(status==-2)
        {
            taskcolor = "#126608";
        }
        else if(status==-1)
        {
            taskcolor = "#ec9b97";
        }
        else if(status==-3)
        {
            taskcolor = "grey";
        }
         

        if(status==0 || status==1 || status=='-1' || status==3)
        {
            var followup_btn = '<li><a data-id="' + TaskID + '" target="_blank" href="followup.php?mode=inquiry_followup&id=' + id + '&sales_id='+ sales_executive_id +'" class="pipeline-edit-btn" title="Edit"><i class="fa fa-eye"></i>&nbsp;Followup</a></li>';
        }

        var TaskCard = ' <li class="task-card" data-priority="' + TaskPriority + '" data-status="' + status + '" id="TaskCard' + TaskID + '" data-id="' + TaskID + '" style="min-height:184px!important;min-width:220px!important;max-width:220px!important;border-left-color:' + taskcolor + '" data-href="#">' +
            '<div class="task-card-title">' +
            '<span class="open-task" style="color:black">' + icon + " " + company_name + "</span></br>" +
            '<span class="open-task" style="color:black;font-size:10px;">' + Task_date + " - #INQ " + id + "</span></br>" +
            '<span class="open-task" style="color:black;font-size:10px;">Taken By : ' + sales_executive_name + "</span></br>" +
            '<span class="open-task" style="color:black;font-size:10px;">Assign To : ' + inq_assign_to + "</span>" +
            '<div class="dropdown" style="margin-top:-30px;">' +
            '<a class="btn-task-card-more dropdown-toggle" data-toggle="dropdown">' +
            '<i class="fa fa-ellipsis-v"></i>' +
            '</a>' +
            '<ul class="dropdown-menu">' +
            '<li><a data-id="' + TaskID + '" target="_blank" href="no_order_inquiry_crud.php?mode=edit&type='+ type +'&id=' + id + '" class="task-view-btn"><i class="fa fa-edit"></i>&nbsp;Edit</a></li>' +
            followup_btn+
           '</ul>' +
            '</div> ' +
            '</div>' +
            '<div class="task-card-body open-task">' +
            '<p class="task-card-description"></p>' +

            '<div class="" style="font-size:12px;width:100%;float:left"><span style="float:left"> <b><u>' + person_name +

            "</u></b></span>&nbsp;<br/><b><span style='font-size:10px;float:left'> - " + customer_type + "</span>&nbsp;" +
            
            "</u></b></span>&nbsp;<br/><b><span style='font-size:9px;float:left'> - " + country + " - " + state + " - " + city + " </span>&nbsp;" +
            
            "</u></b></span>&nbsp;<br/><b><span style='font-size:10px;float:left'> - " + mobile_number + "</span>&nbsp;" +
            "</u></b></span>&nbsp;<br/><b><span style='font-size:10px;float:left'> - " + email_address + "</span>&nbsp;" +
            '</div>' +
            '</li>';
        $("#Task" + status).find(".task-body").append(TaskCard);
    } else {
        if (TaskAssignedBy == DEFAULTASSIGNEE && TaskAssignedTo != DEFAULTASSIGNEE) {
            icon = '<i class="fa fa-arrow-right "></i>';
            taskcolor = "#F00";
        } else if (TaskAssignedTo == DEFAULTASSIGNEE && TaskAssignedBy != DEFAULTASSIGNEE) {
            icon = '<i class="fa fa-arrow-down "></i>';
            taskcolor = "#00F";
        } else {
            icon = '<i class="fa fa-user"></i>';
            taskcolor = "#228B22";
        }
        if (CurrentDate > TaskDeadline && TaskDeadline!='0000-00-00' && CurrentTaskStatus != 5) {
            var deadlineColor = "";
            var deadlineColorfont = " #F00";
        }
        if (CurrentTaskStatus == 5) {
            var deadlineColorfont = "#008000";
        }
        var TaskColor = TaskColors.find(o => o.id === TaskColorTag);
        if (!TaskColor) {
            TaskColor = { slug: "#00F", "id": 0, "name": "No Color" };
        }
        if(CurrentTaskStatus==1){
            var text="Assign";
        }else if(CurrentTaskStatus>=2){
            var text="Replace";
        }
        if (customer_type == 1) {
            icon = '<i class="fa fa-arrow-down "></i>';
            taskcolor = "#F00";
            Customer_text="NEW";
        } 
        else {
            icon = '<i class="fa fa-user"></i>';
            taskcolor = "#228B22";
            Customer_text="EXISTING";
        }

        var edit_btn = "";
        var delete_btn = "";
        var hide_btn = "";
        var status_change_btn = "";
        var edit_customer = "";
        var cre_date = $.datepicker.formatDate("dd-M-yy", new Date(CreatedDate));
        if (IsActive == 1) {
            hide_btn = '<li><a data-id="' + TaskID + '" href="javascript:void(0);" class="task-archive-btn" title="Hide"><i class="fa fa-eye-slash"></i>&nbsp;</a></li>';
        } else {
            hide_btn = '<li><a data-id="' + TaskID + '" href="javascript:void(0);" class="task-archive-btn1" title="Hide"><i class="fa fa-eye"></i>&nbsp;</a></li>';
        }

        if(customer_type==1)
        {
            edit_customer = '<li><a data-id="' + TaskID + '" target="_blank" href="contact_detail_crud.php?mode=edit&flag=pipeline&cid=' + customer_id + '" class="pipeline-edit-btn" title="Edit"><i class="fa fa-edit"></i>&nbsp;Customer Edit</a></li>';
        }
        if (ADMIN_FLAG.update_flag == 1) {
            edit_btn = '<li><a data-id="' + TaskID + '" target="_blank" href="sales_pipeline_edit.php?mode=edit&id=' + TaskID + '" class="task-edit-btn" title="Edit"><i class="fa fa-edit"></i>&nbsp;EDIT</a></li>';

        }
        if (ADMIN_FLAG.delete_flag == 1) {
            delete_btn = '<li><a data-id="' + TaskID + '" href="javascript:void(0);" class="task-delete-btn"  title="Delete"><i class="fa fa-trash"></i>&nbsp;DELETE</a></li>';

        }
        if(CurrentTaskStatus==5){
            modal="#StatusCreateModal";
            status_change_btn ='<li><a href="javascript:void(0);" data-toggle="modal" data-target="'+modal+'" class="task-note-btn" title=" Next Activity" data-id="'+TaskID+'"><i class="fas fa-rupee-sign"></i>&nbsp;Status Update</a></li>';
        }else{
            modal="";
        }
        var TaskAction = '<div class="dropdown">' +
            '<button class="btn btn-primary dropdown-toggle text-white" type="button" data-toggle="dropdown">More&nbsp;&nbsp;' +
            '<span class="caret"></span></button>' +
            '<ul class="dropdown-menu" style="min-width: 80px!important;position:relative!important;">' +
            '<li><a data-id="' + TaskID + '" data-name="note" href="javascript:void(0);" data-toggle="modal" data-target="#TaskNoteCreateModal" class="task-note-btn" title="Note"><i class="fa fa-plus"></i>&nbsp;NOTE</a></li>' +
            '<li><a data-id="' + TaskID + '" data-name="attachment" href="javascript:void(0);" data-toggle="modal" data-target="#TaskNoteCreateModal" class="task-note-btn" title="Attachment"><i class="fa fa-paperclip"></i>&nbsp;ATTACHMENT</a></li>'+
             '<li><a href="javascript:void(0);" data-toggle="modal" data-target="#NextActivityCreateModal" class="task-note-btn" title="Next Activity" data-id="'+TaskID+'"><i class="fas fa-rupee-sign"></i>&nbsp;NEXT ACTIVITY</a></li>' +
            '<li><a href="javascript:void(0);" target="_blank" title="Calender" class="task-note-btn"><i class="fa fa-calendar"></i>&nbsp;CALENDER</a></li>'+
            '<li><a href="expense_crud.php?mode=add&id='+user_id+'"  target="_blank" class="task-note-btn" title="Expense"><i class="fas fa-rupee-sign"></i>&nbsp;EXPENSE</a></li>' +
            '<li><a data-id="' + TaskID + '" href="salesquotation_crud.php?mode=add&id='+customer_id+'" class="task-note-btn" title="Quotation"><i class="fas fa-paperclip"></i>&nbsp;QUOTATION</a></li>' + edit_customer + status_change_btn + hide_btn + delete_btn +
            
        '</ul>' +
        '</div> ';
        var Rating = '<div class="extra"><div class="rating"><select class="task-card-priority ratingbar-readonly" disabled>' +
            '<option ' + ((TaskPriority == 1) ? "selected" : "") + ' value="1">Low</option>' +
            '<option ' + ((TaskPriority == 2) ? "selected" : "") + ' value="2">Medium</option>' +
            '<option ' + ((TaskPriority == 3) ? "selected" : "") + ' value="3">High</option>' +
            '</select></div>';
        var TaskCard = ' <tr class="task-card" data-priority="' + TaskPriority + '" data-status="' + CurrentTaskStatus + '" id="TaskCard' + TaskID + '" data-id="' + TaskID + '" style="border-left-color:' + taskcolor + ';  background-color:' + deadlineColor + '; color:' + deadlineColorfont + ';" data-href="sales_pipeline_edit.php?mode=view&id=' + TaskID + '">' +
            '<td>' +
            '<span class="open-task">' +  + "</span>" +
            '</td>' +
            '<td class="open-task">' +
            "" + inquiry_date + "" +
            '</td>' +
            '<td class="open-task">' +
            '<b>NAME:</b>'+
            "" + customer_name + "" +
            '<br/>'+
            '<b>CONTACT:</b>'+
            "" + customer_phone_1 + "" +
            '</td>' +
            '<td width="15%">' +
            '<span class="open-task">' + customer_city + "</span>" +
            '</td>' +
            '<td class="open-task">' +
            "" + Customer_text + "" +
            '</td>' +
            '<td class="open-task">' +
            "" + Rating + "" +
            '</td>' +
            '<td class="open-task">' +
            "" + deadline_date_format + "" +
            '</td>' +
            '<td class="status open-task">' +
            "" + CurrentTaskStatusName + "" +
            '</td>' +
            '<td class="action text-center">' +
            "" + TaskAction + "" +
            '</td>' +
            '</tr>';

        $(".task-row-container").append(TaskCard);
    }
    AssignReadOnlyRatingbar();
    return true;
}

function UpdateTaskStatus(TaskID, NewStatus) {
    var OldStatus = $("#TaskCard" + TaskID).data("status");
    var show_hidden_task = ($("#show-hidden-task-input").prop('checked')) ? 1 : 0;
    /*if(NewStatus=="4" || NewStatus=="5")
    {
        toastr.error("You Can Not Direct Generete Order And Lost This Status");
        alert("You Can Not Direct Generete Order And Lost This Status");
        location.reload();
    }
    else
    {*/
        $.ajax
        ({
            url: url,
            type: "POST",
            data: {
                m: "uts",
                tid: TaskID,
                s: NewStatus,
                o: OldStatus,
                show_hidden_task: show_hidden_task,
            },
            beforeSend: function() {
                //$("#loading-modal").modal('show');
            },
            success: function(result) {
                var result = $.parseJSON(result);
                var result = (result);
                if (result.a == 1) {
                    if (NewStatus == 0) {
                        $("#TaskCard" + TaskID).fadeOut(100);
                    }
                    $("#TaskCard" + TaskID).data("status", NewStatus);
                    toastr.success(result.mg)
                    FetchTaskStatus();
                    FetchTasks();
                    //location.reload();
                    //aj.Snackbar(result.mg)
                    SortTask($("#Task" + NewStatus + "").find(".task-body li.task-card"), $("#Task" + NewStatus).find('.task-body'));

                    $(".quotation-count-"+OldStatus).html(result.old_stage_quotation);
                    $(".order-count-"+OldStatus).html(result.old_stage_order);

                    $(".quotation-count-"+NewStatus).html(result.new_stage_quotation);
                    $(".order-count-"+NewStatus).html(result.new_stage_order);
                } else {
                    //toastr.success(result.mg)
                }
            },
            error: function() {
                //$("#loading-modal").modal('hide');
                //toastr.error("Something went wrong with server try again")
                aj.Snackbar("Something went wrong with server try again")
            }
        })
    /*}*/
}

function AssignReadOnlyRatingbar() {
    $(".ratingbar-readonly").barrating({
        theme: 'bootstrap-stars',
        readonly: true,
        showSelectedRating: false
    });
}

function SortTask(sort, container) {
    $(sort).sort(sort_li).appendTo(container);
}

function sort_li(a, b) {
    return ($(b).data('priority')) > ($(a).data('priority')) ? 1 : -1;
}

/*$(document).ready(function() {
})*/
