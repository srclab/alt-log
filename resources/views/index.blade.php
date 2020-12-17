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

<script type="text/javascript">

    var token = '{{ csrf_token() }}';
    var translations = JSON.parse('{!! json_encode(__('alt-log::general.view')) !!}');
    var log_date_format = '{{ $log_date_format }}';
    var route_delete_log = '{{ route('alt-log::log.delete') }}';
    var route_get_logs_list = '{{ route('alt-log::log.list') }}';
    var route_get_log_data = '{{ route('alt-log::log.get') }}';

</script>

<!-- App -->
<script src="{{ asset('/vendor/alt-log/js/app.js') }}"></script>

</body>
</html>
