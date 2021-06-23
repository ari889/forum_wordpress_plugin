(function(){
    $(document).ready(function(){
        /**
         * add answer
         */
        let answer_counter = 0;
        $(document).on('click', '#add-answer', function(e){
            e.preventDefault();
            $('#answer-wrapper').append('<li class="list-group-item d-flex flex-row justify-content-between align-items-center" id="answer'+answer_counter+'">\n' +
                '                                <input name="answer[]" type="text" class="form-control" placeholder="Add answer" id="correct-answer'+answer_counter+'">\n' +
                '                                <input name="correct-answer" type="radio" class="mx-2" value="'+answer_counter+'">\n' +
                '                                <button type="button" class="btn btn-danger" data-dismiss="answer'+answer_counter+'" id="dismiss-answer">X</button>\n' +
                '                            </li>');
            answer_counter++;
        });

        /**
         * if user submit answer form
         */
        $(document).on('submit', '#answer-form', function(e){
            e.preventDefault();
            let form = $(this);
            let form_data = new FormData(this);
            form_data.append('action', 'rz_submit_user_answer');
            form_data.append('nonce', rzAdminSubmitAnswer.rz_admin_submit_answer_nonce);
            $.ajax({
                url: rzAdminSubmitAnswer.ajax_url,
                method: 'POST',
                data: form_data,
                contentType: false,
                processData: false,
                dataType: 'JSON',
                success: function(data){
                    console.log(data);
                    if(data.error == true){
                        $('#answer-form-error').html(data.message);
                    }else{
                        form[0].reset();
                        answer_counter = 0;
                        $('#answer-wrapper').html('');
                        $('#answer-form-error').html(data.message);
                    }
                }
            });
        });

        /**
         * dismiss answer
         */
        $(document).on('click', '#dismiss-answer', function(e){
           e.preventDefault();
           let dismiss = $(this).data('dismiss');
           $('#'+dismiss).remove();
        });

        /**
         * view modal data
         */
        var answer_modal = new bootstrap.Modal(document.getElementById('view-answer-modal'));
        $(document).on('click', '#view-answer', function(e){
            e.preventDefault();
            let answer_id = $(this).data('answer_id');
            let button = $(this);
            button.addClass('disabled');
            $.ajax({
                url: rzGetAnswerInfo.ajax_url,
                method: 'POST',
                data: {'action': 'rz_get_answer_info', 'nonce': rzGetAnswerInfo.rz_get_answer_info_nonce, 'answer_id' : answer_id},
                success: function(data){
                    console.log(data);
                    $('#view-answer-modal .modal-content').html(data);
                    answer_modal.show();
                    button.removeClass('disabled');
                }
            });
        });

        /**
         * change answer status
         */
        $(document).on('change', '#answer-status', function(e){
            e.preventDefault();
            let button = $(this);
            let status = button.val();
            let answer_id = button.data('answer_id');
            $.ajax({
                url: rzChangeAnswerStatus.ajax_url,
                method: 'POST',
                data: {'action': 'rz_change_answer_status', 'nonce': rzChangeAnswerStatus.rz_change_answer_status_nonce, 'status' : status, 'answer_id': answer_id},
                beforeSend: function(){
                    button.attr('disabled', true);
                },
                success: function(data){
                    console.log(data);
                    if(status == '1'){
                        $('#status'+answer_id).addClass('bg-success');
                        $('#status'+answer_id).removeClass('bg-danger');
                        $('#status'+answer_id).text('Published');
                    }else{
                        $('#status'+answer_id).addClass('bg-danger');
                        $('#status'+answer_id).removeClass('bg-success');
                        $('#status'+answer_id).text('Denied');
                    }
                    button.attr('disabled', false);
                    answer_modal.hide();
                    swal('success', 'Status updated.');
                }
            });
        });
    });
})(jQuery)