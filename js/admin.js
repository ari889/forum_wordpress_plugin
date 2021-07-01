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
         * get quiz name for fetch
         */
        $(document).on('change', '#get_quiz_name', function(e){
            e.preventDefault();
            let quiz_id = $(this).val();
            $.ajax({
                url: rzAnsUsingQuiz.ajax_url,
                method: 'POST',
                data: {'action': 'rz_get_answer_based_on_quiz', 'nonce': rzAnsUsingQuiz.rz_answer_on_quiz_nonce, 'quiz_id': quiz_id},
                success: function(data){
                    console.log(data);
                    $('#fetch-quiz-questions').html(data);
                }
            });
        });
    });
})(jQuery)