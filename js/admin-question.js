(function(){
    $(document).ready(function(){
        /**
         * define modal
         */
         var quiz_question_modal = new bootstrap.Modal(document.getElementById('question-view-modal'));

        /**
         * if user click view question
         */
        $(document).on('click', '#view-question', function(e){
            e.preventDefault();
            let question_id = $(this).data('question_id');
            $.ajax({
                url: rzViewQuestion.ajax_url,
                method: 'POST',
                data: {'action': 'rz_view_questions', 'nonce' : rzViewQuestion.rz_view_question_nonce, 'question_id': question_id},
                success: function(data){
                    console.log(data);
                    $('#question-view-modal #edit-answer-form').html(data);
                    quiz_question_modal.show();
                }
            });
        });

        /**
         * if user submit edit form
         */
        $(document).on('submit', '#question-view-modal #edit-answer-form', function(e){
            e.preventDefault();

            let form_data = new FormData(this);
            form_data.append('action', 'rz_edit_questions_and_answers');
            form_data.append('nonce', rzEditQuestion.rz_edit_question_nonce);
            $('#question-view-modal #edit-answer-form button[type="submit"]').addClass('disabled');

            $.ajax({
                url: rzEditQuestion.ajax_url,
                method: 'POST',
                data: form_data,
                contentType: false,
                processData: false,
                success: function(data){
                    $('#edit-question-message').html(data);
                    $('#question-view-modal #edit-answer-form button[type="submit"]').removeClass('disabled');
                }
            });
        });
    });
})(jQuery)