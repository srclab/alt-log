<?php

return [

    'server' => [
        'server_error' => 'Server error',
        'log_not_found' => 'Log not found',
        'large_log' => 'Log is too large',
        'logs_deleted' => 'Logs deleted',
        'default_log' => 'Other',
    ],

    'view' => [
        'title' => 'AltLog',
        'navbar_title' => 'AltLog',
        'back_button' => 'Back',
        'logs' => 'Project logs',
        'refresh' => 'Refresh',
        'delete' => 'Delete',
        'logs_list' => 'Logs list',
        'request' => [
            'processing' => 'Request in progress...',
            'server_error' => 'Server error',
        ],
        'table' => [
            'date' => 'Date',
            'level' => 'Level',
            'message' => 'Message',
        ],

        'datatables' => [
            'processing' => 'Processing...',
            'search' => 'Search:',
            'lengthMenu' => 'Show _MENU_ entries',
            'info' => 'Showing _START_ to _END_ of _TOTAL_ entries',
            'infoEmpty' => 'Showing 0 to 0 of 0 entries',
            'infoFiltered' => '(filtered from _MAX_ total entries)',
            'loadingRecords' => 'Loading...',
            'zeroRecords' => 'No matching records found',
            'emptyTable' => 'No data available in table',
            'paginate' => [
                'first' => 'First',
                'previous' => 'Previous',
                'next' => 'Next',
                'last' => 'Last',
            ],
            'aria' => [
                'sortAscending' => ': activate to sort column ascending',
                'sortDescending' => ': activate to sort column descending',
            ],
            'select' => [
                'rows' => [
                    '_' => 'Selected entries: %d',
                    '0' => 'Click on an entry to select',
                    '1' => 'One entry selected',
                ]
            ],
        ],
    ],
];
