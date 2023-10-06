$(document).ready(function() {
    let homeURL = '/task/list';

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });


    $('.cancel').on('click', () => {
        history.back(1);
    });

    $('.create, .update').on('click', (e) => {
        let data = {},
            action = $(e.target).hasClass('update') ? 'update' : 'create',
            taskId = $('#task-form').attr('task-id') ?? '',
            parent_id    = $('#task-form').attr('task-parent_id');

        if (parent_id > 0) {
            data['parent_id'] = parent_id;
        }

        $('#task-form').find('.task-data').each((i, d) => {
            data[d.id] = $(d).val();
        });

        $.ajax({
            url: '/task/' + action + '/' + taskId,
            type: 'POST',
            dataType: 'json',
            data: data,
            success: (res) => {
                if (res.status === true) {
                    window.location.href = homeURL;
                }
            }
        })
    })

    $(document).find('#complete, #delete').on('click', (e) => {
        let data    = {},
            action  = $(e.target).attr('id');

        data['id']  = $(e.target).attr('task-id');

        $.ajax({
            url: '/task/' + action,
            type: 'POST',
            dataType: 'json',
            data: data,
            success: (res) => {
                if (res.status === true) {
                    if (action == 'complete') {
                        location.reload();
                    } else {
                        window.location.href = homeURL;
                    }
                } else {
                    if (res.message.length > 0 ) {
                        alert(res.message);
                    }
                }
            }
        })
    })

    //*** Set priority on edit task ***//
    let priority = $(document).find('#priority').attr('priority');

    if (priority > 0) {
        $('#priority option').each((i, d) => {
            if ($(d).val() == priority) {
                $(d).attr('selected', true)
            }
        })
    }
    //*** Set priority on edit task ***//

});
