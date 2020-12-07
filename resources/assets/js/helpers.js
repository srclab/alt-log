window.sendRequest = function (url, method, data, result_container, success_handle, enable_preloader) {

    if(enable_preloader === undefined) {
        enable_preloader = true;
    }

    $(result_container).html('');

    /**
     * Добавления токена.
     */
    try {

        if (['POST', 'post', 'DELETE', 'delete', 'PUT', 'put'].indexOf(method) >= 0 && window.token && !data._token) {
            if (Array.isArray(data)) {
                data.push({name: "_token", value: window.token});
            } else {
                data._token = window.token;
            }
        }

    } catch (e) {
        console.warn(e);
    }

    $.ajax({
        url: url,
        type: method,
        data: data,

        success: function (data) {

            preloader(false, result_container);

            if(data.operation_status !== undefined) {

                if (data.operation_status.status == 'success') {

                    viewSuccessResult(data.operation_status.message, result_container);
                    success_handle(data);

                } else {
                    viewErrorResult(data, result_container);
                }

            } else {
                viewErrorResult(data, result_container);
            }

        },

        error: function (xhr, status, error) {
            preloader(false, result_container);
            viewErrorResult(xhr.responseJSON, result_container);
        },

        fail: function () {
            preloader(false, result_container);
            viewError(result_container, translations.request.server_error);
        },

        beforeSend: function(xhr) {

            if(enable_preloader) {
                preloader(true, result_container);
            }

        }

    });
}


function viewSuccessResult(message, result_container) {
    viewSuccess(result_container, message);
}

function viewErrorResult(data, result_container) {

    if(data.validation_errors) {
        return;
    }

    var message = '';

    if(data.operation_status !== undefined) {
        if (data.operation_status.status == 'error') {
            message = data.operation_status.message + "<br>";
        }
    }

    $.each(data['errors'], function( index, value ) {
        message += value + "<br>";
    });

    if (message == '') {
        message = translations.request.server_error;
    }

    viewError(result_container, message);

}

function viewError(alert, message) {
    $(alert).html('<div class="alert alert-danger">'+message+'</div>');
}

function viewSuccess(alert, message) {
    $(alert).html('<div class="alert alert-success">'+message+'</div>');
}

function preloader(show, container) {

    if(show) {
        $(container).html('<div class="alert alert-primary">'+translations.request.processing+'</div>')
    } else {
        $(container).html('');
    }

}


window.escapeHtml = function(text) {
    var map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };

    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}
