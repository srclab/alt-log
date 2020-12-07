<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('alt-log::general.view.title') }}</title>

    <link href="{{ asset('/vendor/alt-log/css/app.css') }}" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0">
    <a class="navbar-brand col-sm-3 col-md-2 mr-0" href="{{ route('alt-log::index') }}">{{ __('alt-log::general.view.navbar_title') }}</a>
    <ul class="navbar-nav px-3">
        <li class="nav-item text-nowrap">
            <a class="nav-link" href="{{ $back_url }}">{{ __('alt-log::general.view.back_button') }}</a>
        </li>
    </ul>
</nav>

<div class="container-fluid">
    <div class="row">
        <nav class="col-md-2 d-none d-md-block bg-light sidebar">
            <div class="sidebar-sticky">

                <div class="log-list-title">
                    <h5>{{ __('alt-log::general.view.logs') }}</h5>
                    <button type="button" class="btn btn-outline-dark btn-sm refresh-logs"><i class="fas fa-sync-alt"></i></button>
                </div>

                <div class="log-list-block">

                </div>

                <div class="log-list-delete">
                    <button type="button" class="btn btn-outline-danger btn-sm  delete-logs"><i class="fas fa-trash"></i> {{ __('alt-log::general.view.delete') }}</button>
                </div>

            </div>
        </nav>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 pt-3 px-4">

            <div class="status-log-list"></div>
            <div class="status-log"></div>

            <div class="log-table-block"></div>

        </main>
    </div>
</div>

<!-- App -->
<script src="{{ asset('/vendor/alt-log/js/app.js') }}"></script>

<script type="text/javascript">

    var token = '{{ csrf_token() }}';
    var translations = JSON.parse('{!! json_encode(__('alt-log::general.view')) !!}');
    var url_arameters = new URLSearchParams(window.location.search);


    $(document).ready(function() {

        if(updateLogList()) {
            if(hasLogParameterInUrl()) {
                getLogData(getLogParameterOfUrl());
            }
        }

    });


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
            var log_table_block = $('.log-table-block');
            var log_list_block = $('.log-list-block');
            var request_result_block = $('.status-log-list');

            log_table_block.html('');
            log_list_block.html('');

            var data = {
                logs: logs
            };

            sendRequest("{{ route('alt-log::log.delete') }}", 'post', data, request_result_block, function (result) {
                updateLogList();
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
            $('.log-list-delete').show();
        } else {
            $('.log-list-delete').hide();
        }
    });


    function updateLogList() {

        var log_table_block = $('.log-table-block');
        var log_list_block = $('.log-list-block');
        var request_result_block = $('.status-log-list');

        log_table_block.html('');
        log_list_block.html('');
        $('.log-list-delete').hide();

        sendRequest("{{ route('alt-log::log.list') }}", 'post', {}, request_result_block, function (result) {

            request_result_block.html('');

            renderLogList(result.log_list, result.log_groups, log_list_block);
        });

        return true;
    }

    function renderLogList(log_list, log_groups, data_result_area) {

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


        data_result_area.html(list);

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

            list += '<li>';
            list += '<input class="log-checkbox" type="checkbox" value="'+log_list[key]['log']+'">';
            list += '<a role="button" class="log-link" data-log='+log_list[key]['log']+'>'+log_list[key]['name']+'</a>';
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
        var log_table_block = $('.log-table-block');
        var request_result_block = $('.status-log');

        log_table_block.html('');

        var data = {
            log: log
        };

        sendRequest("{{ route('alt-log::log.get') }}", 'post', data, request_result_block, function (result) {
            request_result_block.html('');
            setLogParameterForUrl(result.log_info.log);
            setActiveMenuElement(result.log_info.log);
            renderLogTable(result.log_info, result.log_data, log_table_block);
        });
    }

    function renderLogTable(log_info, log_data, data_result_area) {

        var table = '';

        table += '<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom"><h1 class="h2">'+log_info.name+'</h1></div>';

        table += '<table id="log-table" class="table table-striped table-bordered" style="width:100%">';

        table += '<thead>';
        table += '<tr>';
        table += '<th class="text-center" style="width:5%">#</th>';
        table += '<th class="text-center" style="width:10%">'+translations.table.date+'</th>';
        table += '<th class="text-center" style="width:6%">'+translations.table.level+'</th>';
        table += '<th>'+translations.table.message+'</th>';
        table += '</tr>';
        table += '</thead>';

        table += '<tbody>';
        for (key in log_data) {
            table += '<tr>';
            table += '<td class="text-center">'+log_data[key]['number']+'</td>';
            table += '<td class="text-center">'+log_data[key]['date']+'</td>';
            table += '<td class="text-center">'+log_data[key]['level']+'</td>';
            table += '<td class="log-row"><div class="log-message">'+escapeHtml(log_data[key]['message'])+'</div>'+insertContext(log_data[key]['context'], log_data[key]['number'])+'</td>';
            table += '</tr>';
        }

        table += '</tbody>';

        table += '</table>';

        data_result_area.html(table);

        $('#log-table').DataTable({
            "autoWidth": false,
            "pageLength": 50,
            "order": [[ 0, "desc" ]],
            language: translations.datatables
        });
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


</script>

</body>
</html>
