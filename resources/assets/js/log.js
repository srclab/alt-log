var url_arameters = new URLSearchParams(window.location.search);
var current_active_log = null;
var update_log_list_by_schedule = true;

$(document).ready(function() {

    updateCsrfToken(token);

    if(updateLogList()) {
        if(hasLogParameterInUrl()) {
            getLogData(getLogParameterOfUrl());
        }
    }

});

setInterval(function () {
    if(update_log_list_by_schedule) {
        updateLogList(true);
    }
}, 60000);


$('.refresh-logs').click(function () {
    updateLogList();
});

$('.delete-logs').click(function () {
    var logs = {}, f = false;

    var j = 0;
    $(".log-checkbox").each(function(i){

        if ($(this).is(':checkbox:checked')==true) {
            logs[j++] = $(this).val();
            f = true;
        }
    });

    if(f) {

        var request_result_block = $('.status-log-list');

        clearLogTable();
        clearLogList();

        var data = {
            logs: logs
        };

        sendRequest(route_delete_log, 'post', data, request_result_block, function (result) {
            updateLogList();
            update_log_list_by_schedule = true;
        });
    }
});



$(".log-table-block").on('click', '.log-show-context', function(e) {
    var id = $(this).attr('data-log-line');

    $(".log-context-"+id).toggle();
});

$(".log-list-block").on('click', 'a', function(e) {
    getLogData($(this).attr('data-log'));
});

$(".log-list-block").on('change', '.log-checkbox', function(e) {
    var f = false;

    $(".log-checkbox").each(function(i){
        if ($(this).is(':checkbox:checked')==true) {
            f = true;
        }
    });

    if(f) {
        update_log_list_by_schedule = false;
        $('.log-list-delete').show();
    } else {
        update_log_list_by_schedule = true;
        $('.log-list-delete').hide();
    }
});


function updateLogList(by_schedule) {

    if(by_schedule === undefined) {
        by_schedule = false;
    }

    update_log_list_by_schedule = false;

    var request_result_block = $('.status-log-list');

    if(by_schedule) {
        request_result_block = null;
    } else {
        clearLogTable();
        clearLogList();
        current_active_log = null;
    }

    $('.log-list-delete').hide();

    sendRequest(route_get_logs_list, 'post', {}, request_result_block, function (result) {

        if(request_result_block !== null) {
            request_result_block.html('');
        }

        renderLogList(result.log_list, result.log_groups);

        if(by_schedule && current_active_log !== null) {
            setActiveMenuElement(current_active_log);
        }

        update_log_list_by_schedule = true;
    });

    return true;
}

function clearLogList() {
    $('.log-list-block').html('');
}

function renderLogList(log_list, log_groups) {

    var grouped_log_list = _.groupBy(log_list, function(item) {
        return item.type;
    });

    var list = '';

    for (key in grouped_log_list) {
        // console.log(key);
        // console.log(log_list[key]);

        if(key == 'laravel') {
            list += renderLaravelLogList(grouped_log_list[key]);
        }

        if(key == 'alt') {
            list += renderAltLogList(grouped_log_list[key], log_groups);
        }
    }


    $('.log-list-block').html(list);

}

function renderLaravelLogList(log_list) {

    var list = '';

    list += '<h5><i class="fab fa-laravel"></i> <span>Laravel</span></h5>';
    list += renderLogListItem(log_list);

    return list;
}

function renderAltLogList(log_list, log_groups) {

    var list = '';

    list += '<h5><i class="far fa-file-alt"></i> <span>AltLog</span></h5>';


    var grouped_log_list = _.groupBy(log_list, function(item) {
        return item.group;
    });

    var count_groups = Object.keys(grouped_log_list).length;

    for (key in log_groups) {

        if(!_.isEmpty(grouped_log_list[key])) {
            if(count_groups > 1) {
                list += '<h6>'+log_groups[key]['name']+'</h6>';
            }

            list += renderLogListItem(grouped_log_list[key]);
            // console.log(log_groups[key]);
        }
    }

    return list;
}

function renderLogListItem(log_list) {

    var list = '';

    list += '<ul>';

    for (key in log_list) {

        var log_name = log_list[key]['name'];

        if (log_name.length > 30) {
            log_name = log_name.substring(0, 30)+'...';
        }

        list += '<li>';
        list += '<input class="log-checkbox" type="checkbox" value="'+log_list[key]['log']+'">';
        list += '<a role="button" class="log-link" data-log='+log_list[key]['log']+' title='+log_list[key]['name']+'>'+log_name+'</a>';
        list += '</a>';

        list += '</li>';
    }

    list += '</ul>';

    return list;
}

function setActiveMenuElement(log) {
    $('.log-list-block').find(".log-link").removeClass('active');
    $('.log-list-block').find(".log-link[data-log='"+log+"']").addClass('active');
}

function getLogData(log) {
    var request_result_block = $('.status-log');

    clearLogTable();

    var data = {
        log: log
    };

    sendRequest(route_get_log_data, 'post', data, request_result_block, function (result) {
        request_result_block.html('');
        current_active_log = result.log_info.log;
        setLogParameterForUrl(result.log_info.log);
        setActiveMenuElement(result.log_info.log);
        renderLogTable(result.log_info, result.log_data);
    });
}

function clearLogTable() {
    $('.log-table-block').html('');
}

function renderLogTable(log_info, log_data) {

    var table = '';

    table += '<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom"><h1 class="h2">'+log_info.name+'</h1></div>';

    table += '<table id="log-table" class="table table-striped table-bordered" style="width:100%">';

    table += '<thead>';
    table += '<tr>';
    table += '<th class="text-center" style="width:5%">#</th>';
    table += '<th class="text-center" style="width:11%">'+translations.table.date+'</th>';
    table += '<th class="text-center" style="width:8%">'+translations.table.level+'</th>';
    table += '<th>'+translations.table.message+'</th>';
    table += '</tr>';
    table += '</thead>';

    table += '<tbody>';
    for (key in log_data) {
        table += '<tr>';
        table += '<td class="text-center">'+log_data[key]['number']+'</td>';
        table += '<td class="text-center">'+insertDate(log_data[key]['date'])+'</td>';
        table += '<td class="text-center">'+insertLevel(log_data[key]['level'])+'</td>';
        table += '<td class="log-row"><div class="log-message">'+escapeHtml(log_data[key]['message'])+'</div>'+insertContext(log_data[key]['context'], log_data[key]['number'])+'</td>';
        table += '</tr>';
    }

    table += '</tbody>';

    table += '</table>';

    $('.log-table-block').html(table);

    $('#log-table').DataTable({
        "autoWidth": false,
        "pageLength": 50,
        "order": [[ 0, "desc" ]],
        language: translations.datatables
    });
}

function insertDate(date) {
    return moment(date).format(log_date_format);
}

function insertLevel(level) {

    var color = '#b5bbc8';
    var icon = 'fas fa-bug';

    switch (level) {
        case 'DEBUG':
            color = '#b5bbc8';
            icon = 'fas fa-bug';
            break;

        case 'INFO':
            color = '#3c8dbc';
            icon = 'fas fa-info';
            break;

        case 'NOTICE':
            color = '#555299';
            icon = 'fas fa-flag';
            break;

        case 'WARNING':
            color = '#f39c12';
            icon = 'fas fa-exclamation-triangle';
            break;

        case 'ERROR':
            color = '#d33724';
            icon = 'fas fa-exclamation-triangle';
            break;

        case 'CRITICAL':
            color = '#dd4b39';
            icon = 'fas fa-exclamation-triangle';
            break;

        case 'ALERT':
            color = '#dd4b39';
            icon = 'fas fa-exclamation-triangle';
            break;

        case 'EMERGENCY':
            color = '#dd4b39';
            icon = 'fas fa-exclamation-triangle';
            break;
    }

    return '<span style="color: '+color+';"><i class="'+icon+'"></i>&nbsp;'+level+'</span>';
}

function insertContext(context, number) {

    var row = '';
    var context_block = '';

    for (key in context) {
        context_block += escapeHtml(context[key]) + "\n";
    }

    if(context_block != '') {
        row += '<div class="log-context log-context-'+number+'">'+context_block+'</div>'
        row += '<div class="log-show-context" data-log-line="'+number+'"><i class="fas fa-level-down-alt"></i></div>'
    }

    return row;
}

function hasLogParameterInUrl() {
    return url_arameters.has('log');
}

function getLogParameterOfUrl() {
    return url_arameters.get('log');
}

function setLogParameterForUrl(log) {
    url_arameters.set('log', log);
    history.replaceState(null, null, "?"+url_arameters.toString());
}
