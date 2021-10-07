(function(){
    $(document).ready(function(){


        /**
         * dynamic dependent tabs
         */
        let imit_tabs = document.getElementsByClassName('imit-tabs');
        let tab_list = document.querySelectorAll('.tab-menu .tab-list');

        if(imit_tabs.length > 0 && tab_list.length > 0){
            checkTabs(imit_tabs[0].clientWidth - 32, tab_list);

            $(document).resize(function(){
                checkTabs(imit_tabs[0].clientWidth - 32, tab_list);
            });
    
            function checkTabs(imit_tabs, tab_list){
                let tab_list_length = 0;
                let node_count = 0;
    
                for(let i = 0;i < tab_list.length;i++){
                    tab_list_length += tab_list[i].clientWidth;
                    if(tab_list_length > imit_tabs){
                        break;
                    }
                    node_count++;
                }
    
                for(let j=(node_count + 1);j<=tab_list.length;j++){
                    $('.imit-tabs .tab-menu .tab-list:nth-child('+j+')').hide();
                }
    
                let dropdown_lists = $('.imit-tabs .custom-dropdown .dropdown-menu .imit-dropdown-tabs');
    
                if(dropdown_lists.length > 0){
                    for(let j=0;j<=node_count;j++){
                        $('.imit-tabs .custom-dropdown .dropdown-menu .imit-dropdown-tabs:nth-child('+j+')').hide();
                    }
                }else{
                    dropdown_lists.remove();
                }

                if(tab_list.length - node_count <= 0){
                    $('.imit-tabs .custom-dropdown').remove();
                }
            }
        }

        /**
         * activity function
         */
         function activity( activity_id, img_url, activity_url, text_massage  ) {
            var myTimestamp = new Date();
            db.collection( "activity" ).add( {
                id: activity_id,
                img_url: img_url,
                activity_url: activity_url,
                text_massage: text_massage,
                date_time: myTimestamp.getTime(),
            } )
                .then( ( docRef ) => {
                    console.log( "Document written with ID: ", docRef.id );
                } )
                .catch( ( error ) => {
                    console.error( "Error adding document: ", error );
                } );
        }


        /**
         * add notification
         */
         function add_notification( user_id, link, sender_id = '', receiver_id = '', notification_type = '', content_id = '', massage = '' ) {
            $.ajax( {
                url: data.ajax_url,
                method: 'POST',
                data: {
                    action: 'rz_add_notification',
                    user_id: user_id,
                    link: link,
                    sender_id: sender_id,
                    receiver_id: receiver_id,
                    notification_type: notification_type,
                    content_id: content_id,
                    massage: massage,
                },
                dataType: 'JSON',
                success: function ( data ) {

                    var myTimestamp = new Date();
    
                    db.collection( "notification" ).add( {
                        id: data.id,
                        link: link,
                        receiver_id: parseInt(receiver_id),
                        img_url: data.img_url,
                        massage: massage,
                        date_time: myTimestamp.getTime(),
                        status: data.status
                    } )
                        .then( ( docRef ) => {
                            console.log( "Document written with ID: ", docRef.id );
                        } )
                        .catch( ( error ) => {
                            console.error( "Error adding document: ", error );
                        } );
                }
            } )
        }

        /**
         * time count
         */
         function timeSince(date) {

            var seconds = Math.floor((new Date() - date) / 1000);
    
            var interval = seconds / 31536000;
    
            if (interval > 1) {
                return Math.floor(interval) + " years";
            }
            interval = seconds / 2592000;
            if (interval > 1) {
                return Math.floor(interval) + " months";
            }
            interval = seconds / 86400;
            if (interval > 1) {
                return Math.floor(interval) + " days";
            }
            interval = seconds / 3600;
            if (interval > 1) {
                return Math.floor(interval) + " hours";
            }
            interval = seconds / 60;
            if (interval > 1) {
                return Math.floor(interval) + " minutes";
            }
            return Math.floor(seconds) + " seconds";
        }
        
        
        /**
         * expand first answer comment form
         */
         $(document).on('click', '#first-answer-comment-expand', function(e){
            e.preventDefault();
            let answer_id = $(this).data('answer_id');
            alert(answer_id);
        });

        /**
         * rz login
         */
        $(document).on('submit', '#rz-login', function(e){
            e.preventDefault();
            let form_data = new FormData(this);
            let remember = $('#rz-login input[name="remember-me"]:checked').val();
            let login_remember;
            if(remember == 'yes'){
                login_remember = 'yes';
            }else{
                login_remember = 'no';
            }
            form_data.append('action', 'imit_recozilla_login');
            form_data.append('nonce', rzLogin.recozilla_login_nonce);
            form_data.append('remember', login_remember);
            $.ajax({
                url: rzLogin.ajax_url,
                method: 'POST',
                data: form_data,
                contentType: false,
                processData: false,
                dataType: 'JSON',
                beforeSend: function(){
                    $('#rz-login button[type="submit"]').addClass('disabled');
                },
                success: function(data){
                    if(data.email == true){
                        $('#rz-login input[name="email"]').addClass('is-invalid');
                        $('#login-email').html(data.email_message);
                    }else{
                        $('#rz-login input[name="email"]').removeClass('is-invalid');
                        $('#login-email').html('');
                    }
                    if(data.password == true){
                        $('#rz-login input[name="password"]').addClass('is-invalid');
                        $('#login-password').html(data.password_message);
                    }else{
                        $('#rz-login input[name="password"]').removeClass('is-invalid');
                        $('#login-password').html('');
                    }

                    if(data.error == true){
                        $('#rz-login #login_error').html(data.error_message);
                    }
                    $('#rz-login button[type="submit"]').removeClass('disabled');

                    if(data.redirect == true){
                        window.location.href = `${data.redirect_to}`;
                    }
                }
            });
        });

        /**
         * register
         */
        $(document).on('submit', '#rz-register-form', function(e){
            e.preventDefault();

            let form_data = new FormData(this);
            form_data.append('action', 'imit_new_user_register');
            form_data.append('nonce', rzRegister.recozilla_register_nonce);
            $.ajax({
                url: rzRegister.ajax_url,
                method: 'POST',
                data: form_data,
                contentType: false,
                processData: false,
                dataType: 'JSON',
                beforeSend: function(){
                    $('#rz-register-form button[type="submit"]').addClass('disabled');
                },
                success: function(data){
                    /**
                     * fetch error for first name
                     */
                     if(data.first_name_error == true){
                        $('#rz-register-form input[name="first_name"]').addClass('is-invalid');
                        $('#rz-register-form #first-name-err').html(data.first_name_message);
                    }else{
                        $('#rz-register-form input[name="first_name"]').removeClass('is-invalid');
                        $('#rz-register-form #first-name-err').html('');
                    }


                    /**
                     * fetch error for last name
                     */
                     if(data.last_name_error == true){
                        $('#rz-register-form input[name="last_name"]').addClass('is-invalid');
                        $('#rz-register-form #last-name-err').html(data.last_name_message);
                    }else{
                        $('#rz-register-form input[name="last_name"]').removeClass('is-invalid');
                        $('#rz-register-form #last-name-err').html('');
                    }

                    /**
                     * fetch error for user name
                     */
                    if(data.username_error == true){
                        $('#rz-register-form input[name="username"]').addClass('is-invalid');
                        $('#rz-register-form #reg-username-err').html(data.username_message);
                    }else{
                        $('#rz-register-form input[name="username"]').removeClass('is-invalid');
                        $('#rz-register-form #reg-username-err').html('');
                    }

                    /**
                     * fetch error for email
                     */
                    if(data.email_error == true){
                        $('#rz-register-form input[name="email"]').addClass('is-invalid');
                        $('#rz-register-form #reg-email-err').html(data.email_message);
                    }else{
                        $('#rz-register-form input[name="email"]').removeClass('is-invalid');
                        $('#rz-register-form #reg-email-err').html('');
                    }

                    /**
                     * fetch error for password
                     */
                    if(data.password_error == true){
                        $('#rz-register-form input[name="password"]').addClass('is-invalid');
                        $('#rz-register-form #reg-password-err').html(data.password_message);
                    }else{
                        $('#rz-register-form input[name="password"]').removeClass('is-invalid');
                        $('#rz-register-form #reg-password-err').html('');
                    }

                    if(data.redirect == true){
                        window.location.href = data.redirect_to;
                    }

                    $('#rz-register-form button[type="submit"]').removeClass('disabled');
                }
            });
        });

        /**
         * add question
         */
        $(document).on('submit', '#add-question-form', function(e){
            e.preventDefault();
            let form = $(this);
            let form_data = new FormData(this);
            form_data.append('action', 'imit_add_question');
            form_data.append('nonce', rzAddQuestion.recozilla_add_question_nonce);
            $.ajax({
                url: rzAddQuestion.ajax_url,
                method: 'POST',
                data: form_data,
                contentType: false,
                processData: false,
                dataType: 'JSON',
                beforeSend: function(){
                    $('#add-question-form button[type="submit"]').addClass('disabled');
                },
                success: function(data){
                    if(data.error == true){
                        $('#add_question_error').html(data.message); 
                        $('#add-question-form button[type="submit"]').removeClass('disabled');
                    }else{
                        $('#add_question_error').html(data.message);
                        form[0].reset();
                        if(data.activity_id != '' && data.image_url != '' && data.redirect != '', data.text_message != ''){
                            activity(data.activity_id, data.image_url, data.redirect, data.text_message);
                        }
                        window.location.href = data.redirect;
                    }
                }
            });
        });

        /**
         * show comment form
         */
        $(document).on('click', '#answer-form-button', function(e){
            e.preventDefault();
            // $("#answer-form").animate({ scrollTop: $('#answer-form').prop("scrollHeight")}, 1000);

            let target = $(this).data('target');
            $('#'+target).slideToggle('fast');
        });

        /**
         * add answer
         */
        $(document).on('submit', '#answer-form', function(e){
            e.preventDefault();
            let form = $(this);
            let post_id = form.data('post_id');
            let answer = form.find('textarea[name="answer-content"]').val();
            $.ajax({
                url: rzAddAnswer.ajax_url,
                method: 'POST',
                data: {'action': 'imit_add_answer', 'nonce': rzAddAnswer.recozilla_add_answer_nonce, 'answer': answer, 'post_id': post_id},
                dataType: 'JSON',
                beforeSend: function(){
                    $('#answer-form button[type="submit"]').addClass('disabled');
                },
                success: function(data){
                    form[0].reset();
                    if(data.img_url != '' && data.activity_url != '' && data.text_message && data.activity_id != ''){
                        activity(data.activity_id, data.image_url, data.activity_url, data.text_message);
                    }
                    if(data.sender_id != '' && data.receiver_id != '' && data.content_id != '' && data.message_text != '' && data.sender_id != data.receiver_id){
                        add_notification(data.sender_id, data.activity_url, data.sender_id, data.receiver_id, 'answer', data.content_id, data.message_text);
                    }
                    $('#answer-form button[type="submit"]').removeClass('disabled');
                    swal('Your answer submitted.');
                    window.location.reload();
                }
            });
        });


        /**
         * up vote
         */
        $(document).on('click', '#up-vote', function(e){
            e.preventDefault();
            let answer_id = $(this).data('answer_id');
            let up_vote = $(this);
            let down_vote = up_vote.parent().parent().find('#down-vote');
            let counter = up_vote.parent().parent().find('#counter'+answer_id);

            let up_vote_status = up_vote.hasClass('active');
            let down_vote_status = down_vote.hasClass('active');
            let counter_text = parseInt(counter.text());

            if(up_vote_status == true){
                up_vote.removeClass('active');
                counter.text(counter_text -= 1);
            }else{
                if(down_vote_status == true){
                    down_vote.removeClass('active');
                }
                counter.text(counter_text += 1);
                up_vote.addClass('active');
            }
            $.ajax({
                url: rzAddVote.ajax_url,
                method: 'POST',
                data: {'action': 'imit_add_vote', 'nonce': rzAddVote.recozilla_add_vote_nonce, 'answer_id': answer_id, 'vote_type': 'up-vote'},
                dataType: 'JSON',
                success: function(data){
                    if(data.up_vote == true || data.down_vote == true){
                        if(data.image_url != '' && data.activity_url != '' && data.text_message != '' && data.activity_id != ''){
                            activity(data.activity_id, data.image_url, data.activity_url, data.text_message);
                        }
                        if(data.sender_id != '' && data.receiver_id != '' && data.content_id != '' && data.message_text != '' && data.sender_id != data.receiver_id){
                            add_notification(data.sender_id, data.activity_url, data.sender_id, data.receiver_id, 'vote', data.content_id, data.message_text);
                        }                    
                    }
                }
            });
        });


        /**
         * down vote
         */
        $(document).on('click', '#down-vote', function(e){
            e.preventDefault();
            let answer_id = $(this).data('answer_id');
            let down_vote = $(this);
            let up_vote = down_vote.parent().parent().find('#up-vote');
            let counter = down_vote.parent().parent().find('#counter'+answer_id);

            let down_vote_status = down_vote.hasClass('active');
            let up_vote_status = up_vote.hasClass('active');
            let counter_text = parseInt(counter.text());


            if(down_vote_status == true){
                down_vote.removeClass('active');
            }else{
                if(up_vote_status == true){
                    up_vote.removeClass('active');
                    counter.text(counter_text -= 1);
                }
                down_vote.addClass('active');
            }
            $.ajax({
                url: rzAddVote.ajax_url,
                method: 'POST',
                data: {'action': 'imit_add_vote', 'nonce': rzAddVote.recozilla_add_vote_nonce, 'answer_id': answer_id, 'vote_type': 'down-vote'},
                dataType: 'JSON',
                success: function(data){
                    if(data.up_vote == true || data.down_vote == true){
                        if(data.image_url != '' && data.activity_url != '' && data.text_massage != '' && data.activity_id != ''){
                            activity(data.activity_id, data.image_url, data.activity_url, data.text_message);
                        }
                        if(data.sender_id != '' && data.receiver_id != '' && data.content_id != '' && data.message_text != '' && data.sender_id != data.receiver_id){
                            add_notification(data.sender_id, data.activity_url, data.sender_id, data.receiver_id, 'vote', data.content_id, data.message_text);
                        }
                    }

                }
            });
        });

        /**
         * comment on answer
         */
        $(document).on('submit', '#answer-comment-form', function(e){
            e.preventDefault();
            let answer_id = $(this).data('answer_id');
            let answer_comment_text = $('#answer-comment-form textarea[name="answer-comment'+answer_id+'"]').val();
            let form = $(this);
            $.ajax({
                url: rzAddAnswerOnComment.ajax_url,
                method: 'POST',
                data: {'action': 'imit_add_answer_on_comment', 'nonce': rzAddAnswerOnComment.rz_add_comment_on_answer_nonce, 'answer_id': answer_id, 'answer_comment_text' : answer_comment_text},
                dataType: 'JSON',
                beforeSend: function(){
                    $('#answer-comment-form button[type="submit"]').addClass('disabled');
                },
                success: function(data){
                    form[0].reset();
                    if(data.activity_id != '' && data.image_url != '' && data.activity_url != '' && data.text_message != ''){
                        activity(data.activity_id, data.image_url, data.activity_url, data.text_message);
                    }
                    if(data.sender_id != '' && data.receiver_id != '' && data.content_id != '' && data.message_text != '' && data.sender_id != data.receiver_id){
                        add_notification(data.sender_id, data.activity_url, data.sender_id, data.receiver_id, 'answer', data.content_id, data.message_text);
                    }
                    if(data.sender_id != '' && data.receiver_id_2 != '' && data.content_id != '' && data.message_text_2 != '' && data.sender_id != data.receiver_id_2){
                        add_notification(data.sender_id, data.activity_url, data.sender_id, data.receiver_id_2, 'answer', data.content_id, data.message_text_2);
                    }
                    $('#answer-comment-form button[type="submit"]').removeClass('disabled');
                    window.location.reload();
                }
            });
        });

        /**
         * comment up vote
         */
        $(document).on('click', '#up-vote-comment', function(e){
            e.preventDefault();
            let comment_id = $(this).data('comment_id');
            let up_vote_comment = $(this);
            let down_vote_comment = up_vote_comment.parent().parent().find('#down-vote-comment');
            let counter = up_vote_comment.parent().parent().find('#comment-counter'+comment_id);

            let up_vote_comment_status = up_vote_comment.hasClass('active');
            let down_vote_comment_status = down_vote_comment.hasClass('active');
            let counter_text = parseInt(counter.text());

            if(up_vote_comment_status == true){
                up_vote_comment.removeClass('active');
                counter.text(counter_text -= 1);
            }else{
                if(down_vote_comment_status == true){
                    down_vote_comment.removeClass('active');
                }
                counter.text(counter_text += 1);
                up_vote_comment.addClass('active');
            }
            $.ajax({
                url: rzAddCommentUpVote.ajax_url,
                method: 'POST',
                data: {'action': 'imit_add_comment_up_vote', 'nonce': rzAddCommentUpVote.rz_add_comment_on_up_vote_nonce, 'comment_id': comment_id, 'vote_type': 'up-vote'},
                dataType: 'JSON',
                success: function(data){
                    if(data.sender_id != '' && data.link != '' && data.receiver_id != '' && data.content_id != '' && data.message_text != '' && data.sender_id != data.receiver_id){
                        add_notification(data.sender_id, data.link, data.sender_id, data.receiver_id, 'comment-vote', data.content_id, data.message_text);
                    } 
                }
            });
        });

        /**
         * add down vote
         */
        $(document).on('click', '#down-vote-comment', function(e){
            e.preventDefault();
            let comment_id = $(this).data('comment_id');
            let down_vote_comment = $(this);
            let up_vote_comment = down_vote_comment.parent().parent().find('#up-vote-comment');
            let counter = down_vote_comment.parent().parent().find('#comment-counter'+comment_id);

            let down_vote_comment_status = down_vote_comment.hasClass('active');
            let up_vote_comment_status = up_vote_comment.hasClass('active');
            let counter_text = parseInt(counter.text());

            if(down_vote_comment_status == true){
                down_vote_comment.removeClass('active');
            }else{
                if(up_vote_comment_status == true){
                    up_vote_comment.removeClass('active');
                    counter.text(counter_text -= 1);
                }
                down_vote_comment.addClass('active');
            }
            $.ajax({
                url: rzAddCommentUpVote.ajax_url,
                method: 'POST',
                data: {'action': 'imit_add_comment_up_vote', 'nonce': rzAddCommentUpVote.rz_add_comment_on_up_vote_nonce, 'comment_id': comment_id, 'vote_type': 'down-vote'},
                dataType: 'JSON',
                success: function(data){
                    if(data.sender_id != '' && data.link != '' && data.receiver_id != '' && data.content_id != '' && data.message_text != '' && data.sender_id != data.receiver_id){
                        add_notification(data.sender_id, data.link, data.sender_id, data.receiver_id, 'comment-vote', data.content_id, data.message_text);
                    } 
                }
            });
        });

        /**
         * if user click replay button
         */
        $(document).on('click', '#comment-replay', function(e){
            e.preventDefault();
            let comment_id = $(this).data('comment_id');
            $('#replay-form'+comment_id).slideToggle('fast');
        });

        /**
         * if user submit a replay
         */
        $(document).on('submit', '#submit-replay-form', function(e){
            e.preventDefault();
            let comment_id = $(this).data('comment_id');
            let replay = $('#submit-replay-form textarea[name="replay-text'+comment_id+'"]').val();
            let form = $(this);
            $.ajax({
                url: rzCommentReplay.ajax_url,
                method: 'POST',
                data: {'action': 'rz_add_replay', 'nonce': rzCommentReplay.rz_add_comment_replay_nonce, 'replay_text': replay, 'comment_id': comment_id},
                beforeSend: function(){
                    $('#submit-replay-form button[type="submit"]').addClass('disabled');
                },
                success:function(data){
                    form[0].reset();
                    $('#submit-replay-form button[type="submit"]').removeClass('disabled');
                    window.location.reload();
                }
            });
        });

        /**
         * like replay
         */
        $(document).on('click', '#like-replay', function(e){
            e.preventDefault();
            let replay_id = $(this).data('replay_id');

            let button = $(this);
            $.ajax({
                url: rzAddReplayLike.ajax_url,
                method: 'POST',
                data: {'action': 'rz_add_replay_like', 'nonce': rzAddReplayLike.rz_add_replay_like_none, 'replay_id' : replay_id, 'reply_type' : 'up-reply'},
                dataType: 'JSON',
                success: function(data){
                    $('#replay-counter'+replay_id).text(data.counter);
                    if(data.counter < 0){
                        $('#replay-counter'+replay_id).removeClass('text-success');
                        $('#replay-counter'+replay_id).addClass('text-danger');
                    }else{
                        $('#replay-counter'+replay_id).addClass('text-success');
                        $('#replay-counter'+replay_id).removeClass('text-danger');
                    }
                    if(data.up_reply == true){
                        $('#question-reply-action'+replay_id+' #like-replay').addClass('active');
                        $('#question-reply-action'+replay_id+' #dislike-replay').removeClass('active');
                    }else if(data.down_reply == true){
                        $('#question-reply-action'+replay_id+' #dislike-replay').addClass('active');
                        $('#question-reply-action'+replay_id+' #like-replay').removeClass('active');
                    }else{
                        $('#question-reply-action'+replay_id+' #like-replay').removeClass('active');
                        $('#question-reply-action'+replay_id+' #dislike-replay').removeClass('active');
                    }
                }
            });
        });


        /**
         * dislike replay
         */
        $(document).on('click', '#dislike-replay', function(e){
            e.preventDefault();
            let replay_id = $(this).data('replay_id');

            let button = $(this);
            $.ajax({
                url: rzAddReplayLike.ajax_url,
                method: 'POST',
                data: {'action': 'rz_add_replay_like', 'nonce': rzAddReplayLike.rz_add_replay_like_none, 'replay_id' : replay_id, 'reply_type' : 'down-reply'},
                dataType: 'JSON',
                success: function(data){
                    $('#replay-counter'+replay_id).text(data.counter);
                    if(data.counter < 0){
                        $('#replay-counter'+replay_id).removeClass('text-success');
                        $('#replay-counter'+replay_id).addClass('text-danger');
                    }else{
                        $('#replay-counter'+replay_id).addClass('text-success');
                        $('#replay-counter'+replay_id).removeClass('text-danger');
                    }
                    if(data.up_reply == true){
                        $('#question-reply-action'+replay_id+' #like-replay').addClass('active');
                        $('#question-reply-action'+replay_id+' #dislike-replay').removeClass('active');
                    }else if(data.down_reply == true){
                        $('#question-reply-action'+replay_id+' #dislike-replay').addClass('active');
                        $('#question-reply-action'+replay_id+' #like-replay').removeClass('active');
                    }else{
                        $('#question-reply-action'+replay_id+' #like-replay').removeClass('active');
                        $('#question-reply-action'+replay_id+' #dislike-replay').removeClass('active');
                    }
                }
            });
        });

        /**
         * add post on discussion
         */
        $(document).on('submit', '#add_discussion_form', function(e){
            e.preventDefault();
            let form_data = new FormData(this);
            form_data.append('action', 'rz_add_discussion');
            form_data.append('nonce', rzAddDiscussion.rz_add_discussion);
            let form = $(this);
            $.ajax({
                url: rzAddDiscussion.ajax_url,
                method: 'POST',
                data: form_data,
                contentType: false,
                processData: false,
                dataType: 'JSON',
                beforeSend: function(){
                    $('#add_discussion_form button[type="submit"]').addClass('disabled');
                },
                success: function(data){
                    if(data.error == true){
                        $('#discussion-error').html(data.message);
                    }else{
                        form[0].reset();
                        if(data.activity_id != '' && data.image_url != '' && data.activity_url != '' && data.text_message != ''){
                            activity(data.activity_id, data.image_url, data.activity_url, data.text_message);
                        }
                        window.location.reload();
                    }
                    $('#add_discussion_form button[type="submit"]').removeClass('disabled');
                }
            });
        });

        /**
         * add comment in discussion
         */
        $(document).on('submit', '#add_comment_discussion', function(e){
            e.preventDefault();
            let post_id = $(this).data('post_id');
            let comment_text = $('#add_comment_discussion textarea[name="comment'+post_id+'"]').val();
            $.ajax({
                url: rzAddCommentDis.ajax_url,
                method: 'POST',
                data: {'action': 'imit_add_comment_on_discussion', 'nonce': rzAddCommentDis.rz_add_comment_in_discussion_nonce, 'comment_text': comment_text, 'post_id' : post_id},
                dataType: 'JSON',
                beforeSend: function(){
                    $('#add_comment_discussion button[type="submit"]').addClass('disabled');
                },
                success: function(data){
                    if(data.image_url != '' && data.activity_url != '' && data.text_message != '' && data.activity_id != ''){
                        activity(data.activity_id, data.image_url, data.activity_url, data.text_message);
                    }
                    if(data.user_id != '' && data.sender_id != '' && data.receiver_id != '' && data.content_id != '' && data.message_text != '' && data.sender_id != data.receiver_id){
                        add_notification(data.sender_id, data.activity_url, data.sender_id, data.receiver_id, 'discuss-comment', data.content_id, data.message_text);
                    }
                    $('#add_comment_discussion button[type="submit"]').removeClass('disabled');
                    window.location.reload();
                }
            });
        });

        /**
         * like comment
         */
        $(document).on('click', '#comment-discuss-up', function(e){
            e.preventDefault();
            let comment_id = $(this).data('comment_id');
            let up_vote = $(this);
            let down_vote = up_vote.parent().parent().find('#comment-discuss-down');
            let counter = up_vote.parent().parent().find('#dis-counter'+comment_id);

            let up_vote_status = up_vote.hasClass('active');
            let down_vote_status = down_vote.hasClass('active');
            let counter_text = parseInt(counter.text());


            if(up_vote_status == true){
                up_vote.removeClass('active');
                counter.text(counter_text -= 1);
            }else{
                if(down_vote_status == true){
                    down_vote.removeClass('active');
                }
                counter.text(counter_text += 1);
                up_vote.addClass('active');
            }
            $.ajax({
                url: rzLikeDislikeDiscussComment.ajax_url,
                method: 'POST',
                data: {'comment_id': comment_id, 'like_type': 'up-like', 'nonce': rzLikeDislikeDiscussComment.rz_like_discussion_comment, 'action': 'add_like_to_discuss_comment'},
                dataType: 'JSON',
                success: function(data){
                    if(data.user_id != '' && data.link != '' && data.sender_id != '' && data.receiver_id != '' && data.content_id != '' && data.message_text != '' && data.sender_id != data.receiver_id){
                        if(data.up_like == true || data.down_like == true){
                            add_notification(data.sender_id, data.link, data.sender_id, data.receiver_id, 'discuss-comment-like', data.content_id, data.message_text);
                        }
                    }
                }
            });
        });

        /**
         * down like
         */
        $(document).on('click', '#comment-discuss-down', function(e){
            e.preventDefault();
            let comment_id = $(this).data('comment_id');
            let down_vote = $(this);
            let up_vote = down_vote.parent().parent().find('#comment-discuss-up');
            let counter = up_vote.parent().parent().find('#dis-counter'+comment_id);

            let down_vote_status = down_vote.hasClass('active');
            let up_vote_status = up_vote.hasClass('active');
            let counter_text = parseInt(counter.text());


            if(down_vote_status == true){
                down_vote.removeClass('active');
            }else{
                if(up_vote_status == true){
                    up_vote.removeClass('active');
                    counter.text(counter_text -= 1);
                }
                down_vote.addClass('active');
            }

            $.ajax({
                url: rzLikeDislikeDiscussComment.ajax_url,
                method: 'POST',
                data: {'comment_id': comment_id, 'like_type': 'down-like', 'nonce': rzLikeDislikeDiscussComment.rz_like_discussion_comment, 'action': 'add_like_to_discuss_comment'},
                dataType: 'JSON',
                success: function(data){
                    if(data.user_id != '' && data.link != '' && data.sender_id != '' && data.receiver_id != '' && data.content_id != '' && data.message_text != '' && data.sender_id != data.receiver_id){
                        if(data.up_like == true || data.down_like == true){
                            add_notification(data.sender_id, data.link, data.sender_id, data.receiver_id, 'discuss-comment-like', data.content_id, data.message_text);
                        }
                    }
                }
            });
        });

        /**
         * add replay to discussion comment
         */
        $(document).on('submit', '#add_discussion_comment_replay', function(e){
            e.preventDefault();
            let comment_id = $(this).data('comment_id');
            let replay_text = $('#add_discussion_comment_replay textarea[name="reply'+comment_id+'"]').val();

            $.ajax({
                url: rzAddReplayOnDiscussComment.ajax_url,
                method: 'POST',
                data: {'action': 'rz_add_replY_on_discussion_comment', 'nonce': rzAddReplayOnDiscussComment.rz_add_replay_on_discussion_nonce, 'comment_id': comment_id, 'replay_text': replay_text},
                beforeSend: function(){
                    $('#add_discussion_comment_replay button[type="submit"]').addClass('disabled');
                },
                success: function(data){
                    $('#add_discussion_comment_replay button[type="submit"]').removeClass('disabled');
                    window.location.reload();
                }
            });
        });


        /**
         * like replay
         */
        $(document).on('click', '#discuss-replay-like-up', function(e){
           e.preventDefault();
           let button = $(this);
           let reply_id = button.data('reply_id');
           let counter = $('#reply-like-counter'+reply_id).text();
            $.ajax({
                url: rzAddRemoveLikeReply.ajax_url,
                method: 'POST',
                data: {'action': 'rz_add_or_remove_like_on_reply', 'nonce' : rzAddRemoveLikeReply.rz_add_like_on_reply, 'reply_id': reply_id, 'reply_type': 'up-reply'},
                dataType: 'JSON',
                success: function(data){
                    $('#reply-action'+reply_id+' #reply-like-counter'+reply_id).text(data.counter);
                    if(data.counter < 0){
                        $('#reply-action'+reply_id+' #reply-like-counter'+reply_id).removeClass('text-success');
                        $('#reply-action'+reply_id+' #reply-like-counter'+reply_id).addClass('text-danger');
                    }else{
                        $('#reply-action'+reply_id+' #reply-like-counter'+reply_id).addClass('text-success');
                        $('#reply-action'+reply_id+' #reply-like-counter'+reply_id).removeClass('text-danger');
                    }
                    if(data.up_reply == true){
                        $('#reply-action'+reply_id+' #discuss-replay-like-up').addClass('active');
                        $('#reply-action'+reply_id+' #discuss-replay-like-down').removeClass('active');
                    }else if(data.down_reply == true){
                        $('#reply-action'+reply_id+' #discuss-replay-like-down').addClass('active');
                        $('#reply-action'+reply_id+' #discuss-replay-like-up').removeClass('active');
                    }else{
                        $('#reply-action'+reply_id+' #discuss-replay-like-up').removeClass('active');
                        $('#reply-action'+reply_id+' #discuss-replay-like-down').removeClass('active');
                    }
                }
            });
        });

        /**
         * dislike reply
         */
        $(document).on('click', '#discuss-replay-like-down', function(e){
            e.preventDefault();
            let button = $(this);
            let reply_id = button.data('reply_id');
            $.ajax({
                url: rzAddRemoveLikeReply.ajax_url,
                method: 'POST',
                data: {'action': 'rz_add_or_remove_like_on_reply', 'nonce' : rzAddRemoveLikeReply.rz_add_like_on_reply, 'reply_id': reply_id, 'reply_type': 'down-reply'},
                dataType: 'JSON',
                success: function(data){
                    $('#reply-action'+reply_id+' #reply-like-counter'+reply_id).text(data.counter);
                    if(data.counter < 0){
                        $('#reply-action'+reply_id+' #reply-like-counter'+reply_id).removeClass('text-success');
                        $('#reply-action'+reply_id+' #reply-like-counter'+reply_id).addClass('text-danger');
                    }else{
                        $('#reply-action'+reply_id+' #reply-like-counter'+reply_id).addClass('text-success');
                        $('#reply-action'+reply_id+' #reply-like-counter'+reply_id).removeClass('text-danger');
                    }
                    if(data.up_reply == true){
                        $('#reply-action'+reply_id+' #discuss-replay-like-up').addClass('active');
                        $('#reply-action'+reply_id+' #discuss-replay-like-down').removeClass('active');
                    }else if(data.down_reply == true){
                        $('#reply-action'+reply_id+' #discuss-replay-like-down').addClass('active');
                        $('#reply-action'+reply_id+' #discuss-replay-like-up').removeClass('active');
                    }else{
                        $('#reply-action'+reply_id+' #discuss-replay-like-up').removeClass('active');
                        $('#reply-action'+reply_id+' #discuss-replay-like-down').removeClass('active');
                    }
                }
            });
        });

        /**
         * discuss up like
         */
        $(document).on('click', '#discuss-up-like', function(e){
           e.preventDefault();
           let post_id = $(this).data('post_id');
           let up_like = $(this);
           let down_like = up_like.parent().parent().find('#discuss-down-like');
           let counter = up_like.parent().parent().find('#discuss-like-counter'+post_id);

           let up_like_status = up_like.hasClass('active');
           let down_like_status = down_like.hasClass('active');
           let counter_text = parseInt(counter.text());


           if(up_like_status == true){
                up_like.removeClass('active');
                counter.text(counter_text -= 1);
           }else{
               if(down_like_status == true){
                    down_like.removeClass('active');
               }
               counter.text(counter_text += 1);
                up_like.addClass('active');
           }
           $.ajax({
               url: rzAddLikeOrDislikeOnDiscuss.ajax_url,
               method: 'POST',
               data: {'action': 'imit_add_like_or_dislike_on_discussion', 'nonce' : rzAddLikeOrDislikeOnDiscuss.rz_like_dis_or_dislike_discuss_post_nonce, 'like_type' : 'up-like', 'post_id' : post_id},
               dataType: 'JSON',
               success: function(data){
                   if(data.up_like == true || data.down_like == true){
                       if(data.activity_id != '' && data.image_url != '' && data.activity_url != '' && data.text_massage != ''){
                           activity(data.image_url, data.activity_url, data.text_message);
                       }
                   }

                   if(data.sender_id != '' && data.activity_url != '' && data.receiver_id != '' && data.content_id != '' && data.message_text != '' && data.sender_id != data.receiver_id){
                        if(data.up_like == true || data.down_like == true){
                            add_notification(data.sender_id, data.activity_url, data.sender_id, data.receiver_id, 'discuss-like', data.content_id, data.message_text);
                        }
                   }


               }
           });
        });


        /**
         * discuss down like
         */
        $(document).on('click', '#discuss-down-like', function(e){
           e.preventDefault();
           let post_id = $(this).data('post_id');
           let down_like = $(this);
           let up_like = down_like.parent().parent().find('#discuss-up-like');
           let counter = down_like.parent().parent().find('#discuss-like-counter'+post_id);

           let up_like_status = up_like.hasClass('active');
           let down_like_status = down_like.hasClass('active');
           let counter_text = parseInt(counter.text());

           if(down_like_status == true){
                down_like.removeClass('active');
            }else{
                if(up_like_status == true){
                    up_like.removeClass('active');
                    counter.text(counter_text -= 1);
                }
                down_like.addClass('active');
            }
           $.ajax({
               url: rzAddLikeOrDislikeOnDiscuss.ajax_url,
               method: 'POST',
               data: {'action': 'imit_add_like_or_dislike_on_discussion', 'nonce' : rzAddLikeOrDislikeOnDiscuss.rz_like_dis_or_dislike_discuss_post_nonce, 'like_type' : 'down-like', 'post_id' : post_id},
               dataType: 'JSON',
               success: function(data){
                    if(data.up_like == true || data.down_like == true){
                        if(data.activity_id != '' && data.image_url != '' && data.activity_url != '' && data.text_massage != ''){
                            activity(data.image_url, data.activity_url, data.text_message);
                        }
                    }

                    if(data.sender_id != '' && data.activity_url != '' && data.receiver_id != '' && data.content_id != '' && data.message_text != '' && data.sender_id != data.receiver_id){
                        if(data.up_like == true || data.down_like == true){
                            add_notification(data.sender_id, data.activity_url, data.sender_id, data.receiver_id, 'discuss-like', data.content_id, data.message_text);
                        }
                   }
               }
           });
        });

        /**
         * if user click follow button
         */
        $(document).on('click', '#rz-follow', function(e){
            e.preventDefault();
            let button = $(this);
            let receiver_id = button.data('receiver_id');
            if(button.hasClass('following')){
                button.removeClass('following');
                button.html('<i class="fas fa-plus-circle me-1"></i> Follow');
            }else{
                button.addClass('following');
                button.html('<i class="fas fa-check-square me-2"></i>Following');
            }
            $.ajax({
                url: rzUserFollowUnfollow.ajax_url,
                method: 'POST',
                data: {'action': 'rz_user_follow_unfollow', 'nonce' : rzUserFollowUnfollow.rz_user_follow_unfollow, 'receiver_id' : receiver_id},
                dataType: 'JSON',
                beforeSend: function(){
                  button.addClass('disabled');
                },
                success: function(data){
                    button.removeClass('disabled');
                }
            });
        });

        /**
         * change user avatar
         */
        $(document).on('change', '#change-rz-avatar', function(e){
            e.preventDefault();
            let form_data = new FormData(this);
            form_data.append('action', 'imit_rz_change_profile_image');
            form_data.append('nonce', rzChangeProfileImage.rz_change_profile_image_nonce);
            $.ajax({
                url: rzChangeProfileImage.ajax_url,
                method: 'POST',
                data: form_data,
                contentType: false,
                processData: false,
                success: function(data){
                    $('#profile_image_show').attr('src', data);
                    $('#profile_image_show_modal').attr('src', data);
                }
            });
        });

        /**
         * add more wokplace
         */
        let work_counter = 1;
        $(document).on('click', '#add-more-workplace', function(e){
            e.preventDefault();
            let d= new Date();
            let y = d.getFullYear();
            let year = y;
            let s;
            for(s = y; s >= 1920; s--){
                year += '<option value="'+s+'">'+s+'</option>';
            }
            $('#more-workplace').append('<div id="work'+work_counter+'">\n' +
                '                                        <div class="d-flex flex-row justify-content-between align-items-center">\n' +
                '                                            <label for="" class="imit-font fz-14 fw-500 fz-16">Work</label>\n' +
                '                                            <button type="button" class="text-secondary bg-transparent border-0 fz-16 p-0" data-target="#work'+work_counter+'" id="dismiss-workplace"><i class="fas fa-times"></i></button>\n' +
                '                                        </div>\n' +
                '                                        <div class="row mt-1">\n' +
                '                                            <div class="col-md-4">\n' +
                '                                                <input name="company[]" type="text" class="form-control imit-font fz-14 rz-border" placeholder="Company">\n' +
                '                                            </div>\n' +
                '                                            <div class="col-md-4">\n' +
                '                                                <input name="position[]" type="text" class="form-control imit-font fz-14 rz-border" placeholder="Position">\n' +
                '                                            </div>\n' +
                '                                            <div class="col-md-4 d-flex flex-row justify-content-between align-items-center">\n' +
                '                                                <select name="work_start_year[]" id="" class="form-select imit-font fz-14 rz-secondary-color rz-border">\n' +
                year+
                '                                                </select>\n' +
                '                                                <span class="imit-font fz-14 rz-secondary-color mx-2">To</span>\n' +
                '                                                <select name="work_end_year[]" id="" class="form-select imit-font fz-14 rz-secondary-color rz-border">\n' +
                year+
                '                                                </select>\n' +
                '                                            </div>\n' +
                '                                        </div>\n' +
                '                                    </div>');
            work_counter++;
        });

        /**
         * close workplace
         */
        $(document).on('click', '#dismiss-workplace', function(e){
            e.preventDefault();
            let target = $(this).data('target');
            $(target).remove();
        });

        /**
         * add more education
         */
        let education_counter = 1;
        $(document).on('click', '#add-more-education', function(e){
            e.preventDefault();
            let d= new Date();
            let y = d.getFullYear();
            let year = y;
            let s;
            for(s = y; s >= 1920; s--){
                year += '<option value="'+s+'">'+s+'</option>';
            }
            $('#more-education').append('<div id="work'+education_counter+'">\n' +
                '                                        <div class="d-flex flex-row justify-content-between align-items-center">\n' +
                '                                            <label for="" class="imit-font fz-14 fw-500 fz-16">Education</label>\n' +
                '                                            <button type="button" class="text-secondary bg-transparent border-0 fz-16 p-0" data-target="#work'+education_counter+'" id="dismiss-education"><i class="fas fa-times"></i></button>\n' +
                '                                        </div>\n' +
                '                                        <div class="row mt-1">\n' +
                '                                            <div class="col-md-4">\n' +
                '                                                <input name="college[]" type="text" class="form-control imit-font fz-14 rz-border" placeholder="College">\n' +
                '                                            </div>\n' +
                '                                            <div class="col-md-4">\n' +
                '                                                <input name="concentrations[]" type="text" class="form-control imit-font fz-14 rz-border" placeholder="Concentrations">\n' +
                '                                            </div>\n' +
                '                                            <div class="col-md-4 d-flex flex-row justify-content-between align-items-center">\n' +
                '                                                <select name="edu_start_year[]" id="" class="form-select imit-font fz-14 rz-secondary-color rz-border">\n' +
                year+
                '                                                </select>\n' +
                '                                                <span class="imit-font fz-14 rz-secondary-color mx-2">To</span>\n' +
                '                                                <select name="edu_end_year[]" id="" class="form-select imit-font fz-14 rz-secondary-color rz-border">\n' +
                year+
                '                                                </select>\n' +
                '                                            </div>\n' +
                '                                        </div>\n' +
                '                                    </div>');
            education_counter++;
        });

        /**
         * close workplace
         */
        $(document).on('click', '#dismiss-education', function(e){
            e.preventDefault();
            let target = $(this).data('target');
            $(target).remove();
        });

        /**
         * submit profile edit form
         */
        $(document).on('submit', '#update-rz-user-profile', function(e){
            e.preventDefault();
            let form_data = new FormData(this);
            form_data.append('action', 'imit_update_profile_data');
            form_data.append('nonce', rzProfileUpdate.rz_profile_update_nonce);
            $.ajax({
                url: rzProfileUpdate.ajax_url,
                method: 'POST',
                data: form_data,
                contentType: false,
                processData: false,
                dataType: 'JSON',
                beforeSend: function(){
                    $('#update-rz-user-profile button[type="submit"]').addClass('disabled');
                },
                success: function(data){
                    if(data.error == true){
                        swal('Stop', data.message, 'warning');
                    }else{
                        swal('Great Job', ' Your changes are saved', 'success');
                        window.location.reload();
                    }
                    $('#update-rz-user-profile button[type="submit"]').removeClass('disabled');
                }
            });
        });

        /**
         * delete workplace
         */
        $(document).on('click', '#delete-workplace', function(e){
            e.preventDefault();
            let button = $(this);
            let workplace_id = button.data('workplace_id');
            $.ajax({
                url: rzDeleteWorkplace.ajax_url,
                method: 'POST',
                data: {'action': 'rz_delete_workplace_nonce', 'nonce': rzDeleteWorkplace.rz_delete_workplace_nonce, 'workplace_id' : workplace_id},
                success: function(data){
                    button.parent().children('p').removeClass('rz-secondary-color');
                    button.parent().children('p').addClass('text-danger');
                    button.parent().children('p').text('Work deleted.');
                }
            });
        });

        /**
         * delete education
         */
        $(document).on('click', '#delete-education', function(e){
            e.preventDefault();
            let button = $(this);
            let education_id = button.data('education_id');
            $.ajax({
                url: rzDeleteEducational.ajax_url,
                method: 'POST',
                data: {'action': 'rz_delete_education', 'nonce': rzDeleteEducational.rz_delete_education_nonce, 'education_id' : education_id},
                success: function(data){
                    button.parent().children('p').removeClass('rz-secondary-color');
                    button.parent().children('p').addClass('text-danger');
                    button.parent().children('p').text('Educational info deleted.');
                }
            });
        });


        /**
         * join partner program
         */
        $(document).on('submit', '#join-partner-program', function(e){
           e.preventDefault();
           let form_data = new FormData(this);
           let form = $(this);
           let permit = $('#join-partner-program input[name="join"]:checked').val();
           if(permit == 'yes'){
            form_data.append('action', 'rz_join_partner_program');
            form_data.append('nonce', rzAddPartnerProgram.rz_add_partner_program);
            $.ajax({
                url: rzAddPartnerProgram.ajax_url,
                method: 'POST',
                data: form_data,
                contentType: false,
                processData: false,
                dataType: 'JSON',
                beforeSend: function(){
                    $('#join-partner-program button[type="submit"]').addClass('disabled');
                },
                success: function(data){
                    if(data.error == true){
                        $('#partner-message').html(data.message);
                        $('#join-partner-program button[type="submit"]').removeClass('disabled');
                    }else{
                        $('#partner-message').parent().parent().html('<p class="imit-font fz-14 rz-secondary-color description mb-0">Your joining request is under consideration and you will be informed soon about it.</p>');
                    }
                }
            });
           }else{
                $('#partner-message').html(`<div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>Warning!</strong> Please make sure that you are accepted to join our partner programme.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>`);
           }
        });


        /**
         * live search user
         */
        $(document).on('submit', '#user-live-search', function(e){
           e.preventDefault();
           let search_key = $('#user-live-search input[name="search-user"]').val();
           $.ajax({
               url: rzSearchUser.ajax_url,
               method: 'POST',
               data: {'search_key': search_key, 'nonce': rzSearchUser.rz_search_user_nonce, 'action' : 'rz_search_user'},
               beforeSend: function(){
                   $('#user-live-search input[name="search-user"]').attr('disabled', true);
               },
               success: function(data){
                   $('#fetch-search-user').html(data);
                   $('#user-live-search input[name="search-user"]').attr('disabled', false);
               }
           });
        });

        /**
         * dismiss banner
         */
        $(document).on('click', '#dismiss-banner', function(e){
           e.preventDefault();
           let button = $(this);
            $.ajax({
                url: rzUpdateBanner.ajax_url,
                method: 'POST',
                data: {'action' : 'rz_update_banner_status', 'nonce' : rzUpdateBanner.rz_update_banner_nonce},
                success: function(data){
                    button.parent().parent().slideUp("normal", function(){
                        $(this).remove();
                    });
                }
            });
        });

        /**
         * if user click create new discussion post
         */
        $(document).on('click', '.create-new-post', function(e){
            e.preventDefault();
            $('#add_discussion_form').slideToggle();
        });

        // $(document).mouseup(function(e)
        // {
        //     var container = $(".add-new-discussion");

        //     // if the target of the click isn't the container nor a descendant of the container
        //     if (!container.is(e.target) && container.has(e.target).length === 0)
        //     {
        //         $('.create-new-post').parent().parent().fadeIn('fast');
        //         $('#add_discussion_form').slideUp();
        //     }
        // });


        /**
         * if user click discuss replay button
         */
        $(document).on('click', '#discuss-replay-button', function(e){
            e.preventDefault();
            let comment_id = $(this).data('comment_id');
            $('.discuss-replay-form'+comment_id).slideToggle('fast');
        });

        /**
         * search terms
         */
        $(document).on('submit', '#search-terms', function(e){
            e.preventDefault();
            let form_data = new FormData(this);
            form_data.append('action', 'rz_search_terms_by_name');
            form_data.append('nonce', rzSearchTerm.rz_search_term_by_name_nonce);
            $('#search-terms input[name="search-terms"]').attr('disabled', true);

            $.ajax({
                url: rzSearchTerm.ajax_url,
                method: 'POST',
                data: form_data,
                contentType: false,
                processData: false,
                success: function(data){
                    $('#fetch-all-terms').html(data);
                    $('#search-terms input[name="search-terms"]').attr('disabled', false);
                }
            });
        });


        /**
         * if user click notification bell
         */
        $(document).on('click', '.notification-bell', function(e){
            e.preventDefault();
            let target = $(this).data('target');
            $('#'+target).fadeToggle('fast');

            /**
             * for desktop
             */
            $('#notification-bell #notification-active span').removeClass('d-block');
            $('#notification-bell #notification-active span').addClass('d-none');

            /**
             * for mobile
             */
             $('#notification-bell-mobile #notification-active-mobile span').removeClass('d-block');
             $('#notification-bell-mobile #notification-active-mobile span').addClass('d-none');

            $.ajax({
                url: rzUpdateNotiStatus.ajax_url,
                method: 'POST',
                data: {'action' : 'rz_update_notification_seen_status', 'nonce' : rzUpdateNotiStatus.rz_update_notification_seen_status_nonce},
                success: function(data){
                    if(target == 'mobile-dropdown'){
                        window.location.href = data;
                    }
                    $('#single-notification').removeClass('active');
                }
            });
        });

        $(document).mouseup(function(e) 
        {
            var container = $(".dropdown-notification");

            // if the target of the click isn't the container nor a descendant of the container
            if (!container.is(e.target) && container.has(e.target).length === 0) 
            {
                container.hide();
            }
        });

        /**
         * if user click dropdown-notification
         */
        let notification_tab_click = false;
        let inbox_tab_click = false;
        $(document).on('click', '#notification-tab-link', function(e){
            e.preventDefault();
            let target = $(this).data('target');
            if(target == 'inbox-tab' && inbox_tab_click == false){
                inbox_tab_click = true;
                get_all_message(target);
            }
            $('.dropdown-notification #notification-tab-link').removeClass('active');
            $(this).addClass('active');
            $('.notification-tab-content').hide();
            $('#'+target).fadeIn('fast');
        });

        /**
         * get all notification
         */
        let notification_start = 0;
        let notification_reachmax = false;

        /**
         * for desktop
         */
        get_all_notification('notification-tab');
        get_all_notification('notification-tab-mobile');


         $('#notification-tab-ul').scroll(function(e){
            if(($('#notification-tab-ul').scrollTop() + $('#notification-tab-ul').height() + 100) >= $('#notification-tab-ul')[0].scrollHeight && notification_reachmax === false){
                notification_start += 10;
                get_all_notification('notification-tab', notification_start);
            }
        });




        /**
         * for mobile
         */
         let notification_start_mobile = 0;
         let notification_reachmax_mobile = false;
         $('#notification-tab-mobile-ul').scroll(function(e){
            if(($('#notification-tab-mobile-ul').scrollTop() + $('#notification-tab-mobile-ul').height() + 100) >= $('#notification-tab-mobile-ul')[0].scrollHeight && notification_reachmax_mobile === false){
                notification_start_mobile += 10;
                get_all_notification('notification-tab-mobile', notification_start_mobile);
            }
        });

        function get_all_notification(target, start = 0){
            if(notification_reachmax == false){
                notification_reachmax = true;
                $.ajax({
                    url: rzGetNotification.ajax_url,
                    method: 'POST',
                    data: {'action' : 'rz_get_all_notification', 'nonce' : rzGetNotification.rz_get_all_notification_nonce, 'start' : start},
                    success: function(data){
                        if(data == 'notificationReachMax'){
                            notification_reachmax = true;
                        }else{
                            $('#'+target+' #'+target+'-ul').append(data);
                            notification_reachmax = false;
                        }
                    }
                });
            }
        }

        /**
         * make notification status active
         */
        $(document).on('click', '#single-notification', function(e){
            e.preventDefault();
            let button = $(this);
            let id = button.data('notification_id');
            let link = button.attr('href');
            $.ajax({
                url: rzUpdateNotification.ajax_url,
                method: 'POST',
                data: {'action' : 'rz_update_notification_status', 'nonce' : rzUpdateNotification.rz_update_notification_status, 'id' : id},
                success: function(data){
                    window.location.href = link;
                }
            });
        });

        /**
         * get all message
         */
        function get_all_message(target){
            $.ajax({
                url: rzGetMessage.ajax_url,
                method: 'POST',
                data: {'action' : 'rz_get_all_message', 'nonce' : rzGetMessage.rz_get_all_message_nonce},
                success: function(data){
                    $('#'+target+' #'+target+'-ul').html(data);
                }
            });
        }


        /**
         * if user click see quiz button
         */
        $(document).on('click', '#show-quiz', function(e){
            e.preventDefault();
            let quiz = $(this);
            let quiz_id = quiz.data('quiz_id');
            $('.single-quiz').removeClass('bg-white');
            quiz.parent().addClass('bg-white');
            $('.show-quiz-form').slideUp();
            $('#quiz-start'+quiz_id).slideToggle();
        });

        /**
         * login modal form
         */
        $(document).on('submit', '#rz-login-modal-form', function(e){
            e.preventDefault();
            let form_data = new FormData(this);
            form_data.append('action', 'rz_login_form_using_modal');
            form_data.append('nonce', rzLoginWithModal.rz_login_using_modal);
            $.ajax({
                url: rzLoginWithModal.ajax_url,
                method: 'POST',
                data: form_data,
                contentType: false,
                processData: false,
                dataType: 'JSON',
                beforeSend: function(){
                    $('#rz-login-modal-form button[type="submit"]').addClass('disabled');
                },
                success: function(data){
                    if(data.email == true){
                        $('#rz-login-modal-form input[name="email"]').addClass('is-invalid');
                        $('#login-email').html(data.email_message);
                    }else{
                        $('#rz-login-modal-form input[name="email"]').removeClass('is-invalid');
                        $('#login-email').html('');
                    }
                    if(data.password == true){
                        $('#rz-login-modal-form input[name="password"]').addClass('is-invalid');
                        $('#login-password').html(data.password_message);
                    }else{
                        $('#rz-login-modal-forminput[name="password"]').removeClass('is-invalid');
                        $('#login-password').html('');
                    }

                    if(data.redirect == true){
                        window.location.reload();
                    }

                    if(data.error == true){
                        $('#rz-login-modal-form #login_error').html(data.error_message);
                    }
                    $('#rz-login-modal-form button[type="submit"]').removeClass('disabled');
                }
            });
        });

        /**
         * if user click next question
         */
        let count = 1
        $(document).on('click', '#next-question', function(e){
            e.preventDefault();
            if(count == 1){
                let date = new Date();
                localStorage.setItem('quiz_start', date.getTime());
            }
            let quiz_id = $(this).data('quiz_id');
            let target = $(this).data('target');
            let answer = $('#question'+(target - 1)+' input[type="radio"]:checked').val();
            if(typeof(answer) == 'undefined'){
                $("#question"+(target - 1)+" #message-error").text("Please chose an asnwer.");
            }else{
                $('#quiz-start'+quiz_id+' #counter').html(count += 1);
                $("#question"+(target - 1)+" #message-error").text('');
                $('.question').hide();
                $('#question'+target).fadeIn('fast');
            }
        });

        /**
         * if user submit quiz form
         */
        $(document).on('submit', '#quiz-submission-form', function(e){
            e.preventDefault();

            let start = localStorage.getItem('quiz_start');
            let time = new Date(parseInt(start));
            let time_spent = timeSince(time);

            let form_data = new FormData(this);
            let quiz_id = $(this).data('quiz_id');
            form_data.append('action', 'rz_submit_quiz_for_result');
            form_data.append('nonce', rzGetQuizResult.rz_get_quiz_result_nonce);
            form_data.append('quiz_id', quiz_id);
            form_data.append('timeSpent', time_spent);
            $.ajax({
                url: rzGetQuizResult.ajax_url,
                method: 'POST',
                data: form_data,
                contentType: false,
                processData: false,
                success: function(data){
                }
            });
        });


        /**
         * delete replay
         */
        $(document).on('click', '#delete-reply', function(e){
            e.preventDefault();

            let reply_id = $(this).data('reply_id');
            swal({
                title: "Are you sure to delete this reply?",
                text: "Once deleted, you will not be able to recover this imaginary file!",
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if(willDelete){
                    $.ajax({
                        url: rzDeleteReply.ajax_url,
                        method: 'POST',
                        data: {'action': 'rz_delete_replay', 'nonce': rzDeleteReply.rz_delete_reply_nonce, 'reply_id' : reply_id},
                        success: function(data){
                            if(data == 'done'){
                                $('#reply'+reply_id).html('<div class="text-center"><i class="fas fa-trash text-danger fz-20"></i><p class="mb-0 fz-16 rz-secondary-color">Reply deleted!</p></div>');
                                swal("Poof! Your imaginary file has been deleted!", {
                                    icon: "success",
                                });
                            }else{
                                swal("Failed! Something went wrong.", {
                                    icon: "warning",
                                });
                            }
                        }
                    });
                }else{
                    swal("Your imaginary file is safe!");
                }
            });
        });

        /**
         * delete comment
         */
        $(document).on('click', '#delete-comment', function(e){
            e.preventDefault();
            let comment_id = $(this).data('comment_id');
            swal({
                title: "Are you sure to delete this comment?",
                text: "Once deleted, you will not be able to recover this imaginary file!",
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if(willDelete){
                    $.ajax({
                        url: rzDeleteComment.ajax_url,
                        method: 'POST',
                        data: {'action': 'rz_delete_comment', 'nonce': rzDeleteComment.rz_delete_comment_nonce, 'comment_id' : comment_id},
                        success: function(data){
                            if(data == 'done'){
                                $('#comment'+comment_id).html('<div class="text-center"><i class="fas fa-trash text-danger fz-20"></i><p class="mb-0 fz-16 rz-secondary-color">Comment deleted!</p></div>');
                                swal("Poof! Your imaginary file has been deleted!", {
                                    icon: "success",
                                });
                            }else{
                                swal("Failed! Something went wrong.", {
                                    icon: "warning",
                                });
                            }
                        }
                    });
                }else{
                    swal("Your imaginary file is safe!");
                }
            });
        });


        /**
         * if user click delete answer
         */
        $(document).on('click', '#delete-answer', function(e){
            e.preventDefault();
            let answer_id = $(this).data('answer_id');
            swal({
                title: "Are you sure to delete this answer?",
                text: "Once deleted, you will not be able to recover this imaginary file!",
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if(willDelete){
                    $.ajax({
                        url: rzDeleteAnswer.ajax_url,
                        method: 'POST',
                        data: {'action': 'rz_delete_answer', 'nonce': rzDeleteAnswer.rz_delete_answer_nonce, 'answer_id' : answer_id},
                        success: function(data){
                            if(data == 'done'){
                                $('#answer'+answer_id).html('<div class="text-center"><i class="fas fa-trash text-danger fz-20"></i><p class="mb-0 fz-16 rz-secondary-color">Answer deleted!</p></div>');
                                swal("Poof! Your imaginary file has been deleted!", {
                                    icon: "success",
                                });
                            }else{
                                swal("Failed! Something went wrong.", {
                                    icon: "warning",
                                });
                            }
                        }
                    });
                }else{
                    swal("Your imaginary file is safe!");
                }
            });
        });

        /**
         * if user click delete question
         */
        $(document).on('click', '#delete-question', function(e){
            e.preventDefault();
            let question_id = $(this).data('question_id');

            swal({
                title: "Are you sure to delete this Question?",
                text: "Once deleted, you will not be able to recover this question!",
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if(willDelete){
                    $.ajax({
                        url: rzDeleteQuestion.ajax_url,
                        method: 'POST',
                        data: {'action': 'rz_delete_question', 'nonce': rzDeleteQuestion.rz_delete_question_nonce, 'question_id' : question_id},
                        success: function(data){
                            if(data == 'done'){
                                $('#question'+question_id).html('<div class="text-center"><i class="fas fa-trash text-danger fz-20"></i><p class="mb-0 fz-16 rz-secondary-color">Question deleted!</p></div>');
                                swal("Poof! Your question has been deleted!", {
                                    icon: "success",
                                });
                            }else{
                                swal("Failed! Something went wrong.", {
                                    icon: "warning",
                                });
                            }
                        }
                    });
                }
            });
        });

        /**
         * delete discuss reply
         */
        $(document).on('click', '#discuss-reply-delete', function(e){
            e.preventDefault();
            let reply_id = $(this).data('reply_id');
            swal({
                title: "Are you sure to delete this reply?",
                text: "Once deleted, you will not be able to recover this imaginary file!",
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if(willDelete){
                    $.ajax({
                        url: rzDeleteDiscussReply.ajax_url,
                        method: 'POST',
                        data: {'action': 'rz_delete_discuss_reply', 'nonce': rzDeleteDiscussReply.rz_delete_discuss_reply_nonce, 'reply_id' : reply_id},
                        success: function(data){
                            if(data == 'done'){
                                $('#dis-reply'+reply_id).html('<div class="text-center"><i class="fas fa-trash text-danger fz-20"></i><p class="mb-0 fz-16 rz-secondary-color">Reply deleted!</p></div>');
                                swal("Poof! Your imaginary file has been deleted!", {
                                    icon: "success",
                                });
                            }else{
                                swal("Failed! Something went wrong.", {
                                    icon: "warning",
                                });
                            }
                        }
                    });
                }
            });
        });

        /**
         * delete discuss comment
         */
        $(document).on('click', '#delete-discuss-comment', function(e){
            e.preventDefault();
            let comment_id = $(this).data('comment_id');
            swal({
                title: "Are you sure to delete this comment?",
                text: "Once deleted, you will not be able to recover this imaginary file!",
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if(willDelete){
                    $.ajax({
                        url: rzDeleteDiscussComment.ajax_url,
                        method: 'POST',
                        data: {'action': 'rz_delete_discuss_comment', 'nonce': rzDeleteDiscussComment.rz_delete_discuss_comment_nonce, 'comment_id' : comment_id},
                        success: function(data){
                            if(data == 'done'){
                                $('#dis-comment'+comment_id).html('<div class="text-center"><i class="fas fa-trash text-danger fz-20"></i><p class="mb-0 fz-16 rz-secondary-color">Comment deleted!</p></div>');
                                swal("Poof! Your imaginary file has been deleted!", {
                                    icon: "success",
                                });
                            }else{
                                swal("Failed! Something went wrong.", {
                                    icon: "warning",
                                });
                            }
                        }
                    });
                }
            });
        });

        /**
         * delete discuss post
         */
        $(document).on('click', '#delete-discuss-post', function(e){
           e.preventDefault();
           let post_id = $(this).data('post_id');
            swal({
                title: "Are you sure to delete this post?",
                text: "Once deleted, you will not be able to recover this imaginary file!",
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if(willDelete){
                    $.ajax({
                        url: rzDeleteDiscussPost.ajax_url,
                        method: 'POST',
                        data: {'action': 'rz_delete_discuss_post', 'nonce': rzDeleteDiscussPost.rz_delete_discuss_post_nonce, 'post_id' : post_id},
                        success: function(data){
                            if(data == 'done'){
                                $('#dis-post'+post_id).html('<div class="text-center"><i class="fas fa-trash text-danger fz-20"></i><p class="mb-0 fz-16 rz-secondary-color">Post deleted!</p></div>');
                                swal("Poof! Your imaginary file has been deleted!", {
                                    icon: "success",
                                });
                            }else{
                                swal("Failed! Something went wrong.", {
                                    icon: "warning",
                                });
                            }
                        }
                    });
                }
            });
        });

        /**
         * get payment method data
         */
        $(document).on('change', '.redeem-points-body .radio-button input[type="radio"]', function(e){
            e.preventDefault();
            let payment = $('.redeem-points-body .radio-button input[type="radio"]:checked').val();

            if(payment == 'Paytm'){
                $('.redeem-points-body #payment-details').html('<div class="row mb-3">\n' +
                    '                                            <label for="paytm-name" class="col-sm-3 col-form-label imit-font fz-16">Name</label>\n' +
                    '                                            <div class="col-sm-9">\n' +
                    '                                            <input name="name" type="text" class="form-control border fz-14 border-1 imit-font" id="paytm-name">\n' +
                    '                                            </div>\n' +
                    '                                        </div>\n' +
                    '\n' +
                    '                                        <div class="row mb-3">\n' +
                    '                                            <label for="paytm-mobile" class="col-sm-3 col-form-label imit-font fz-16">Mobile number</label>\n' +
                    '                                            <div class="col-sm-9">\n' +
                    '                                            <input name="mobile" type="text" class="form-control border fz-14 border-1 imit-font" id="paytm-mobile">\n' +
                    '                                            </div>\n' +
                    '                                        </div>');
            }else if(payment == 'Google Pay'){
                $('.redeem-points-body #payment-details').html('<div class="row mb-3">\n' +
                    '                                            <label for="paytm-name" class="col-sm-3 col-form-label imit-font fz-16">Name</label>\n' +
                    '                                            <div class="col-sm-9">\n' +
                    '                                            <input name="name" type="text" class="form-control border fz-14 border-1 imit-font" id="paytm-name">\n' +
                    '                                            </div>\n' +
                    '                                        </div>\n' +
                    '\n' +
                    '                                        <div class="row mb-3">\n' +
                    '                                            <label for="paytm-mobile" class="col-sm-3 col-form-label imit-font fz-16">Mobile number</label>\n' +
                    '                                            <div class="col-sm-9">\n' +
                    '                                            <input name="mobile" type="text" class="form-control border fz-14 border-1 imit-font" id="paytm-mobile">\n' +
                    '                                            </div>\n' +
                    '                                        </div>');
            }else if(payment == 'UPI'){
                $('.redeem-points-body #payment-details').html('<div class="row mb-3">\n' +
                    '                                            <label for="paytm-name" class="col-sm-3 col-form-label imit-font fz-16">Name</label>\n' +
                    '                                            <div class="col-sm-9">\n' +
                    '                                            <input name="name" type="text" class="form-control border fz-14 border-1 imit-font" id="paytm-name">\n' +
                    '                                            </div>\n' +
                    '                                        </div>\n' +
                    '\n' +
                    '                                        <div class="row mb-3">\n' +
                    '                                            <label for="paytm-mobile" class="col-sm-3 col-form-label imit-font fz-16">Mobile number</label>\n' +
                    '                                            <div class="col-sm-9">\n' +
                    '                                            <input name="mobile" type="text" class="form-control border fz-14 border-1 imit-font" id="paytm-mobile">\n' +
                    '                                            </div>\n' +
                    '                                        </div>\n' +
                    '\n' +
                    '\n' +
                    '                                        <div class="row mb-3">\n' +
                    '                                            <label for="upi-id" class="col-sm-3 col-form-label imit-font fz-16">UPI ID</label>\n' +
                    '                                            <div class="col-sm-9">\n' +
                    '                                            <input name="upi-id" type="text" class="form-control border fz-14 border-1 imit-font" id="upi-id">\n' +
                    '                                            </div>\n' +
                    '                                        </div>');
            }else if(payment == 'Bank Transfer'){
                $('.redeem-points-body #payment-details').html('<div class="row mb-3">\n' +
                    '                                            <label for="account-name" class="col-sm-3 col-form-label imit-font fz-16">Name of Account</label>\n' +
                    '                                            <div class="col-sm-9">\n' +
                    '                                            <input name="account-name" type="text" class="form-control border fz-14 border-1 imit-font" id="account-name">\n' +
                    '                                            </div>\n' +
                    '                                        </div>\n' +
                    '\n' +
                    '\n' +
                    '                                        <div class="row mb-3">\n' +
                    '                                            <label for="account-number" class="col-sm-3 col-form-label imit-font fz-16">Account Number</label>\n' +
                    '                                            <div class="col-sm-9">\n' +
                    '                                            <input name="account-number" type="text" class="form-control border fz-14 border-1 imit-font" id="account-number">\n' +
                    '                                            </div>\n' +
                    '                                        </div>\n' +
                    '\n' +
                    '\n' +
                    '                                        <div class="row mb-3">\n' +
                    '                                            <label for="ifsc-code" class="col-sm-3 col-form-label imit-font fz-16">IFSC Code</label>\n' +
                    '                                            <div class="col-sm-9">\n' +
                    '                                            <input name="ifsc-code" type="text" class="form-control border fz-14 border-1 imit-font" id="ifsc-code">\n' +
                    '                                            </div>\n' +
                    '                                        </div>\n' +
                    '\n' +
                    '\n' +
                    '                                        <div class="row mb-3">\n' +
                    '                                            <label for="bank-name" class="col-sm-3 col-form-label imit-font fz-16">Bank Name</label>\n' +
                    '                                            <div class="col-sm-9">\n' +
                    '                                            <input name="bank-name" type="text" class="form-control border fz-14 border-1 imit-font" id="bank-name">\n' +
                    '                                            </div>\n' +
                    '                                        </div>\n' +
                    '\n' +
                    '\n' +
                    '                                        <div class="row mb-3">\n' +
                    '                                            <label for="branch-name" class="col-sm-3 col-form-label imit-font fz-16">Branch Name</label>\n' +
                    '                                            <div class="col-sm-9">\n' +
                    '                                            <input name="branch-name" type="text" class="form-control border fz-14 border-1 imit-font" id="branch-name">\n' +
                    '                                            </div>\n' +
                    '                                        </div>');
            }
        });

        /**
         * if user submit reedem point form
         */
        $(document).on('submit', '#redeem-point-form', function(e){
            e.preventDefault();
            let form_data = new FormData(this);
            form_data.append('action', 'rz_redeem_point_action');
            form_data.append('nonce', rzRedeemPoint.rz_redeem_point_nonce);
            $.ajax({
                url: rzRedeemPoint.ajax_url,
                method: 'POST',
                data: form_data,
                contentType: false,
                processData: false,
                success: function(data){
                    $('#redeem-point-message').html(data);
                }
            });
        });

        /**
         * follow question
         */
        $(document).on('click', '#follow-question', function(e){
            e.preventDefault();
            let question_id = $(this).data('question_id');
            let button = $(this);
            if(button.hasClass('active')){
                button.removeClass('active');
                button.html('<i class="fas fa-plus-circle"></i> Follow');
            }else{
                button.addClass('active');
                button.html('<i class="fas fa-check-square"></i> Following');
            }
            followQuestion(question_id, button);
        });

        $(document).on('click', '#follow-question-profile', function(e){
            e.preventDefault();
            let question_id = $(this).data('question_id');
            let button = $(this);
            if(button.hasClass('rz-color')){
                button.removeClass('rz-color');
                button.addClass('rz-secondary-color');
                button.html('<i class="fas fa-plus-circle"></i>');
            }else{
                button.removeClass('rz-secondary-color');
                button.addClass('rz-color');
                button.html('<i class="fas fa-check-square"></i>');
            }
            followQuestion(question_id, button);
        });

        /**
         * following question ajax function 
         */
        function followQuestion(question_id, button){
            $.ajax({
                url: rzFolowQuestion.ajax_url,
                method: 'POST',
                data: {'action': 'rz_follow_question', 'nonce': rzFolowQuestion.rz_follow_question, 'question_id' : question_id},
                dataType: 'JSON',
                beforeSend: function(){
                    button.addClass('disabled');
                },
                success: function(data){
                    button.removeClass('disabled');
                }
            });
        }

        /**
         * follow tag
         */
        $(document).on('click', '#follow-tag', function(e){
            e.preventDefault();
            let button = $(this);
            let term_id = button.data('term_id');
            $.ajax({
                url: rzFolowTag.ajax_url,
                method: 'POST',
                data: {'action': 'rz_follwoing_tags_action', 'nonce' : rzFolowTag.rz_follow_tag, 'term_id' : term_id},
                dataType: 'JSON',
                beforeSend: function(){
                    button.addClass('disabled');
                },
                success: function(data){
                    if(data.response == true){
                        button.removeClass('rz-secondary-color');
                        button.addClass('rz-color');
                        button.html('<i class="fas fa-check-square"></i>');
                    }else{
                        button.addClass('rz-secondary-color');
                        button.removeClass('rz-color');
                        button.html('<i class="fas fa-plus-circle"></i>');
                    }
                    button.removeClass('disabled');
                }
            });
        });


        /**
         * if user click show password on login page
         */
        $(document).on('click', '#show-password', function(e){
            e.preventDefault();
            $(this).toggleClass("fa-eye fa-eye-slash");
            var input = $(".password-toggle");
            if (input.attr("type") === "password") {
                input.attr("type", "text");
            } else {
                input.attr("type", "password");
            }
        });

        /**
         * expand answer comment
         */
        $(document).on('click', '#comment-expand', function(e){
            e.preventDefault();
            let answer_id = $(this).data('answer_id');
            $('#answer'+answer_id+' .comment-section').slideToggle('fast');
        });

        /**
         * if user trying to delete dairy
         */
        $(document).on('click', '#delete-dairy', function(e){
            e.preventDefault();
            let post_id = $(this).data('post_id');
            swal({
                title: "Are you sure to delete this dairy?",
                text: "Once deleted, you will not be able to recover this imaginary file!",
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if(willDelete){
                    $.ajax({
                        url: rzDeleteDairy.ajax_url,
                        method: "POST",
                        data: {'action': 'rz_delete_dairy', nonce: rzDeleteDairy.rz_delete_dairy_nonce, 'post_id' : post_id},
                        success: function(data){
                            if(data == 'done'){
                                $('#dairy'+post_id).html('<div class="text-center"><i class="fas fa-trash text-danger fz-20"></i><p class="mb-0 fz-16 rz-secondary-color">Dairy deleted!</p></div>');
                                swal("Poof! Your imaginary file has been deleted!", {
                                    icon: "success",
                                });
                            }else{
                                swal("Failed! Something went wrong.", {
                                    icon: "warning",
                                });
                            }
                        }
                    });
                }
            });
        });

        /**
         * dairy visiblity option
         */
        $(document).on('change', '#dairy-visiblity', function(e){
            e.preventDefault();
            let post_id = $(this).data('post_id');
            let dairy_visiblity;
            if($(this).prop("checked") == true){
                dairy_visiblity = 'publish';
            }
            else if($(this).prop("checked") == false){
                dairy_visiblity = 'private';
            }
            $.ajax({
                url: rzChangeVisiblity.ajax_url,
                method: 'POST',
                data: {'action' : 'rz_change_dairy_visiblity_status', 'nonce' : rzChangeVisiblity.rz_change_dairy_visiblity_nonce, 'post_id' : post_id, 'visiblity': dairy_visiblity},
                success: function(data){
                }
            });
        });

        /**
         * if user click dairy read more button
         */
        $(document).on('click', '#dairy-read-more', function(e){
            e.preventDefault();
            let post_id = $(this).data('post_id');
            $(this).parent().hide();
            $('#dairy-text-expand'+post_id).show();
        });

        /**
         * facebook share
         */
        $(document).on('click', '#share-facebook', function(e) 
         {
             e.preventDefault();
             let url = $(this).data('shareurl');
             window.open('https://www.facebook.com/sharer/sharer.php?u='+escape(url)+'&t='+document.title, '', 
             'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=300,width=600');
         });

         /**
          * twitter share
          */
         $(document).on('click', '#tweet-data', function(e){
            e.preventDefault();
            let url = $(this).data('shareurl');
            window.open("https://twitter.com/share?url="+ encodeURIComponent(url)+"&text="+document.title, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=300,width=600');
         });

         /**
          * share linkedin
          */
         $(document).on('click', '#share-linkedin', function(e){
            e.preventDefault();
            let url = $(this).data('shareurl');
            window.open("https://www.linkedin.com/sharing/share-offsite/?url=" + encodeURIComponent(url));
         });

         /**
          * load more answers questions
          */
         let answer_start = 0;
         $(document).on('click', '#load-more-answers', function(e){
            e.preventDefault();
            answer_start += 10;
            let button = $(this);
            let post_id = $(this).data('post_id');
            $.ajax({
                url: rzGetQuestionAnswers.ajax_url,
                method: 'POST',
                data: {'action' : 'rz_get_questions_answers', 'nonce' : rzGetQuestionAnswers.rz_get_questions_answers, 'post_id' : post_id, 'answer_start' : answer_start},
                dataType: 'JSON',
                beforeSend: function(){
                    button.addClass('disabled');
                    $('#load-more-answer-spinner').fadeIn('fast');
                },
                success: function(data){
                    if(data.answerReachMax == true){
                        button.remove();
                        $('#answers'+post_id).append(data.html);
                    }else{
                        $('#answers'+post_id).append(data.html);
                    }
                    $('#load-more-answer-spinner').fadeOut('fast');
                    button.removeClass('disabled');
                }
            });
         });


         /**
         * load more comment
         */
        $(document).on('click', '#load-more-comment', function(e){
            e.preventDefault();
            let button = $(this);
            let answer_id = button.data('answer_id');
            let start = button.data('start');
            if(start != 'commentReachMax'){
                $.ajax({
                    url: rzGetMoreComment.ajax_url,
                    method: 'POST',
                    data: {'action' : 'rz_get_more_comment_data', 'nonce' : rzGetMoreComment.rz_get_more_comment, 'answer_id' : answer_id, 'start' : start},
                    dataType: 'JSON',
                    beforeSend: function(){
                        button.addClass('disabled');
                        $('#comment-load-more-loader'+answer_id).fadeIn('fast');
                    },
                    success: function(data){
                        if(data.commentReachMax == true){
                            button.data('start', 'commentReachMax');
                            button.remove();
                            $('#comment'+answer_id).append(data.html);
                        }else{
                            button.data('start', start += 3);
                            $('#comment'+answer_id).append(data.html);
                        }
                        $('#comment-load-more-loader'+answer_id).fadeOut('fast');
                        button.removeClass('disabled');
                    }
                });
            }
        });

         /**
          * load more discuss comment
          */
         let discuss_start = 0;
         $(document).on('click', '#load-more-discuss-comment', function(e){
            e.preventDefault();
            let post_id = $(this).data('post_id');
            discuss_start += 10;
            let button = $(this);
            $.ajax({
                url: rzGetDiscussComment.ajax_url,
                method: 'POST',
                data: {'action' : 'rz_get_discuss_comments', 'nonce' : rzGetDiscussComment.rz_get_discuss_comment, 'start' : discuss_start, 'post_id' : post_id},
                dataType: 'JSON',
                beforeSend: function(){
                    button.addClass('disabled');
                    $('#load-more-discuss-comment .spinner-grow').fadeIn('fast');
                },
                success: function(data){
                    if(data.discussCommentReachmax == true){
                        button.remove();
                        $('#discuss-comment-data'+post_id).append(data.html);
                    }else{
                        $('#discuss-comment-data'+post_id).append(data.html);
                    }
                    button.removeClass('disabled');
                    $('#load-more-discuss-comment .spinner-grow').fadeOut('fast');
                }
            });
         });

         /**
          * load more reply
          */
         $(document).on('click', '#load_more_reply', function(e){
            e.preventDefault();
            let button = $(this);

            let comment_id = button.data('comment_id');

            let start = button.data('start');

            if(start != 'replyReachMax'){
                $.ajax({
                    url: rzGetDiscussReply.ajax_url,
                    method: 'POST',
                    data: {'action' : 'rz_get_discuss_reply', 'nonce' : rzGetDiscussReply.rz_get_discuss_reply, 'comment_id' : comment_id, 'start' : start},
                    dataType: 'JSON',
                    beforeSend: function(){
                        button.addClass('disabled');
                        $('#load-more-reply-spinner'+comment_id).fadeIn('fast');
                    },
                    success: function(data){
                        if(data.replyReachMax == true){
                            button.data('start', 'replyReachMax');
                            button.remove();
                            $('#discuss-reply'+comment_id).append(data.html);
                        }else{
                            button.data('start', start += 3);
                            $('#discuss-reply'+comment_id).append(data.html);
                        }
                        $('#load-more-reply-spinner'+comment_id).fadeOut('fast');
                        button.removeClass('disabled');
                    }
                });
            }
         });

        /**
        * load more quiz
         */
         let quiz_start = 0;
         $(document).on('click', '#load-more-quiz', function(e){
             e.preventDefault();
             quiz_start += 10;
             let button = $(this);
             button.addClass('disabled');
             $('#load-more-quiz .spinner-grow').fadeIn();
             $.ajax({
                 url: rzgetMoreQuiz.ajax_url,
                 method: 'POST',
                 data: {'action' : 'rz_get_more_quiz', 'nonce' : rzgetMoreQuiz.rz_load_more_quiz_status, 'start' : quiz_start},
                 dataType: 'JSON',
                 success: function(data){
                     if(data.quizReachMax == true){
                        button.remove();
                        $('#quiz_data').append(data.html);
                     }else{
                        $('#quiz_data').append(data.html);
                     }
                    button.removeClass('disabled');
                    $('#load-more-quiz .spinner-grow').fadeOut();
                 }
             });
         });


         /**
         * load more point earned data
         */
        let point_start = 0;
        let point_reachmax = false;
         $('#points-earned-data').scroll(function(e){
            if(($('#points-earned-data').scrollTop() + $('#points-earned-data').height() + 100) >= $('#points-earned-data')[0].scrollHeight && point_reachmax === false){
                point_start += 10;
                get_all_points(point_start);
            }
        });


        function get_all_points(start){
            if(point_reachmax == false){
                point_reachmax = true;
                $('#points-earned-data #point-loader').fadeIn('fast');
                $.ajax({
                    url: rzGetPoint.ajax_url,
                    method: 'POST',
                    data: {'action' : 'rz_get_morePoint', nonce: rzGetPoint.rz_get_more_points_nonce, 'start' : start},
                    success: function(data){
                        if(data == 'pointReachmax'){
                            point_reachmax = true;
                        }else{
                            point_reachmax = false;
                            $('#points-earned-data #load-more-point').append(data);
                        }
                        $('#points-earned-data #point-loader').fadeOut('fast');
                    }
                });
            }
        }


        /**
         * if user click popup dismiss button
         */
         $(document).on('click', '.dismiss-popup-notification', function(e){
            e.preventDefault();
            let button = $(this);
            button.parent().fadeOut(300, function(){
                $(this).remove();
            });
        });

        /**
         * edit dairy
         */
        let dairy_edit_element = document.getElementById('dairy-edit-modal');
        if(dairy_edit_element != null){
            let dairy_edit = new bootstrap.Modal(dairy_edit_element);
           $(document).on('click', '#edit-dairy', function(e){
               e.preventDefault();
               let post_id = $(this).data('post_id');
               $.ajax({
                   url: rzGetPostById.ajax_url,
                   method: "POST",
                   data: {'action' : 'rz_get_post_by_id', 'nonce' : rzGetPostById.rz_get_post_by_id_nonce, 'post_id' : post_id},
                   dataType: 'JSON',
                   success: function(data){
                       $('#edit-dairy-form textarea[name="dairy-text"]').text(data.post_content);
                       $('#edit-dairy-form input[name="id"]').val(data.ID);
                       if(data.post_status == 'publish'){
                           $('#edit-dairy-form input[name="dairy-visiblity"]').attr('checked', true);
                       }else{
                           $('#edit-dairy-form input[name="dairy-visiblity"]').removeAttr('checked'); 
                       }
                       dairy_edit.show();
                   }
               });
           });
        }

        /**
         * if user update dairy
         */
        $(document).on('submit', '#edit-dairy-form', function(e){
            e.preventDefault();
            let post_id = $('#edit-dairy-form input[name="id"]').val();
            let text = $('#edit-dairy-form textarea[name="dairy-text"]').val();
            let form_data = new FormData(this);
            form_data.append('action', 'rz_edit_dairy');
            form_data.append('nonce', rzEditDairy.rz_edit_dairy_nonce);
            $.ajax({
                url: rzEditDairy.ajax_url,
                method: 'POST',
                data: form_data,
                contentType: false,
                processData: false,
                dataType: 'JSON',
                beforeSend: function(){
                    $('#edit-dairy-form button[type="submit"]').addClass('disabled');
                },
                success: function(data){
                    if(data.error == true){
                        $('#edit-dairy-form #edit-dairy-message').html(data.message);
                    }else{
                        $('#dairy'+post_id+' .dairy-text').text(text);
                        $('#edit-dairy-form #edit-dairy-message').html(data.message);
                        dairy_edit.hide();
                    }
                    $('#edit-dairy-form button[type="submit"]').removeClass('disabled'); 
                }
            });
        });          


        /**
         * opend modal with user data
         */
        let get_login_modal_el = document.getElementById('send-message-modal');
        if(get_login_modal_el != null){
            let login_modal = new bootstrap.Modal(get_login_modal_el);
           $(document).on('click', '#send-message-button', function(e){
               e.preventDefault();
               let user_id = $(this).data('user_id');
   
               let username = $('#name'+user_id).text();
               $('#send-message-modal h2').text('Send message to "'+username+'"');
               $('#send-message-form').attr('data-user_id', user_id);
               login_modal.show();
           });
        }



    });

})(jQuery)