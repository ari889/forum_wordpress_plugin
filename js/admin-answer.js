(function(){
    $(document).ready(function(){
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