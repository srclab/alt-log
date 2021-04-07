<?php

return [

    'server' => [
        'server_error' => 'Ошибка сервера',
        'log_not_found' => 'Лог не найден',
        'large_log' => 'Лог слишком большого размера',
        'logs_deleted' => 'Логи удалены',
        'default_log' => 'Другое',
    ],

    'view' => [
        'title' => 'AltLog',
        'navbar_title' => 'AltLog',
        'back_button' => 'Назад',
        'logs' => 'Логи проекта',
        'refresh' => 'Обновить',
        'delete' => 'Удалить',
        'logs_list' => 'Список логов',
        'request' => [
            'processing' => 'Выполняется запрос...',
            'server_error' => 'Ошибка сервера',
        ],
        'table' => [
            'date' => 'Дата',
            'level' => 'Событие',
            'message' => 'Сообщение',
        ],

        'datatables' => [
            'processing' => 'Подождите...',
            'search' => 'Поиск:',
            'lengthMenu' => 'Показать _MENU_ записей',
            'info' => 'Записи с _START_ до _END_ из _TOTAL_ записей',
            'infoEmpty' => 'Записи с 0 до 0 из 0 записей',
            'infoFiltered' => '(отфильтровано из _MAX_ записей)',
            'loadingRecords' => 'Загрузка записей...',
            'zeroRecords' => 'Записи отсутствуют',
            'emptyTable' => 'В таблице отсутствуют данные',
            'paginate' => [
                'first' => 'Первая',
                'previous' => 'Предыдущая',
                'next' => 'Следующая',
                'last' => 'Последняя',
            ],
            'aria' => [
                'sortAscending' => ': активировать для сортировки столбца по возрастанию',
                'sortDescending' => ': активировать для сортировки столбца по убыванию',
            ],
            'select' => [
                'rows' => [
                    '_' => 'Выбрано записей: %d',
                    '0' => 'Кликните по записи для выбора',
                    '1' => 'Выбрана одна запись',
                ]
            ],
        ],
    ],
];
