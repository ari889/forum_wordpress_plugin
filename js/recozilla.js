(function(){
    $(document).ready(function(){

        /**
         * rz login
         */
        $(document).on('submit', '#rz-login', function(e){
            e.preventDefault();
            let form_data = new FormData(this);
            form_data.append('action', 'imit_recozilla_login');
            form_data.append('nonce', rzLogin.recozilla_login_nonce);
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

                    if(data.redirect == true){
                        window.location.href = data.redirect_to;
                    }

                    if(data.error == true){
                        $('#rz-login #login_error').html(data.error_message);
                    }
                    $('#rz-login button[type="submit"]').removeClass('disabled');
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
                    if(data.username_error == true){
                        $('#rz-register-form input[name="username"]').addClass('is-invalid');
                        $('#rz-register-form #reg-username-err').html(data.username_message);
                    }else{
                        $('#rz-register-form input[name="username"]').removeClass('is-invalid');
                        $('#rz-register-form #reg-username-err').html('');
                    }
                    if(data.email_error == true){
                        $('#rz-register-form input[name="email"]').addClass('is-invalid');
                        $('#rz-register-form #reg-email-err').html(data.email_message);
                    }else{
                        $('#rz-register-form input[name="email"]').removeClass('is-invalid');
                        $('#rz-register-form #reg-email-err').html('');
                    }
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
                beforeSend: function(){
                    $('#add-question-form button[type="submit"]').addClass('disabled');
                },
                success: function(data){
                    console.log(data);
                    $('#add_question_error').html(data);
                    $('#add-question-form button[type="submit"]').removeClass('disabled');
                    form[0].reset();
                }
            });
        });

        /**
         * show comment form
         */
        $(document).on('click', '#comment-button', function(e){
            e.preventDefault();

            let target = $(this).data('target');
            $('#'+target).slideDown('fast');
        });

        /**
         * submit comment form
         */
        $(document).on('submit', '#answer-form', function(e){
            e.preventDefault();
            let form = $(this);
            let post_id = $(this).data('post_id');
            let answer = form.find('textarea[name="answer'+post_id+'"]').val();
            $.ajax({
                url: rzAddAnswer.ajax_url,
                method: 'POST',
                data: {'action': 'imit_add_answer', 'nonce': rzAddAnswer.recozilla_add_answer_nonce, 'answer': answer, 'post_id': post_id},
                beforeSend: function(){
                    $('#answer-form button[type="submit"]').addClass('disabled');
                },
                success: function(data){
                    console.log(data);
                    form[0].reset();
                    $('#answer-form button[type="submit"]').removeClass('disabled');
                    swal('Your answer submitted.');
                }
            });
        });

        /**
         * up vote
         */
        $(document).on('click', '#up-vote', function(e){
            e.preventDefault();
            let answer_id = $(this).data('answer_id');
            let button = $(this);
            $.ajax({
                url: rzAddVote.ajax_url,
                method: 'POST',
                data: {'action': 'imit_add_vote', 'nonce': rzAddVote.recozilla_add_vote_nonce, 'answer_id': answer_id, 'vote_type': 'up-vote'},
                dataType: 'JSON',
                success: function(data){
                    console.log(data);
                    $('#counter'+answer_id).text(data.counter);
                    if(data.counter < 0){
                        $('#counter'+answer_id).removeClass('text-success');
                        $('#counter'+answer_id).addClass('text-danger');
                    }else{
                        $('#counter'+answer_id).addClass('text-success');
                        $('#counter'+answer_id).removeClass('text-danger');
                    }
                    if(data.up_vote == true){
                        $('#vote'+answer_id+' #up-vote').addClass('active');
                        $('#vote'+answer_id+' #down-vote').removeClass('active');
                    }else if(data.down_vote == true){
                        $('#vote'+answer_id+' #down-vote').addClass('active');
                        $('#vote'+answer_id+' #up-vote').removeClass('active');
                    }else{
                        $('#vote'+answer_id+' #up-vote').removeClass('active');
                        $('#vote'+answer_id+' #down-vote').removeClass('active');
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
            let button = $(this);
            $.ajax({
                url: rzAddVote.ajax_url,
                method: 'POST',
                data: {'action': 'imit_add_vote', 'nonce': rzAddVote.recozilla_add_vote_nonce, 'answer_id': answer_id, 'vote_type': 'down-vote'},
                dataType: 'JSON',
                success: function(data){
                    console.log(data);
                    $('#counter'+answer_id).text(data.counter);
                    if(data.counter < 0){
                        $('#counter'+answer_id).removeClass('text-success');
                        $('#counter'+answer_id).addClass('text-danger');
                    }else{
                        $('#counter'+answer_id).addClass('text-success');
                        $('#counter'+answer_id).removeClass('text-danger');
                    }
                    if(data.up_vote == true){
                        $('#vote'+answer_id+' #up-vote').addClass('active');
                        $('#vote'+answer_id+' #down-vote').removeClass('active');
                    }else if(data.down_vote == true){
                        $('#vote'+answer_id+' #down-vote').addClass('active');
                        $('#vote'+answer_id+' #up-vote').removeClass('active');
                    }else{
                        $('#vote'+answer_id+' #up-vote').removeClass('active');
                        $('#vote'+answer_id+' #down-vote').removeClass('active');
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
                beforeSend: function(){
                    $('#answer-comment-form button[type="submit"]').addClass('disabled');
                },
                success: function(data){
                    console.log(data);
                    form[0].reset();
                    $('#answer-comment-form button[type="submit"]').removeClass('disabled');
                }
            });
        });

        /**
         * comment up vote
         */
        $(document).on('click', '#up-vote-comment', function(e){
            e.preventDefault();
            let comment_id = $(this).data('comment_id');
            let button = $(this);
            $.ajax({
                url: rzAddCommentUpVote.ajax_url,
                method: 'POST',
                data: {'action': 'imit_add_comment_up_vote', 'nonce': rzAddCommentUpVote.rz_add_comment_on_up_vote_nonce, 'comment_id': comment_id, 'vote_type': 'up-vote'},
                dataType: 'JSON',
                success: function(data){
                    console.log(data);
                    $('#comment-counter'+comment_id).text(data.counter);
                    if(data.counter < 0){
                        $('#comment-counter'+comment_id).removeClass('text-success');
                        $('#comment-counter'+comment_id).addClass('text-danger');
                    }else{
                        $('#comment-counter'+comment_id).addClass('text-success');
                        $('#comment-counter'+comment_id).removeClass('text-danger');
                    }
                    if(data.up_vote == true){
                        $('#comment-action'+comment_id+' #up-vote-comment').addClass('active');
                        $('#comment-action'+comment_id+' #down-vote-comment').removeClass('active');
                    }else if(data.down_vote == true){
                        $('#comment-action'+comment_id+' #down-vote-comment').addClass('active');
                        $('#comment-action'+comment_id+' #up-vote-comment').removeClass('active');
                    }else{
                        $('#comment-action'+comment_id+' #up-vote-comment').removeClass('active');
                        $('#comment-action'+comment_id+' #down-vote-comment').removeClass('active');
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
            let button = $(this);
            let counter = $('#comment-counter'+comment_id).text();
            $.ajax({
                url: rzAddCommentUpVote.ajax_url,
                method: 'POST',
                data: {'action': 'imit_add_comment_up_vote', 'nonce': rzAddCommentUpVote.rz_add_comment_on_up_vote_nonce, 'comment_id': comment_id, 'vote_type': 'down-vote'},
                dataType: 'JSON',
                success: function(data){
                    console.log(data);
                    $('#comment-counter'+comment_id).text(data.counter);
                    if(data.counter < 0){
                        $('#comment-counter'+comment_id).removeClass('text-success');
                        $('#comment-counter'+comment_id).addClass('text-danger');
                    }else{
                        $('#comment-counter'+comment_id).addClass('text-success');
                        $('#comment-counter'+comment_id).removeClass('text-danger');
                    }
                    if(data.up_vote == true){
                        $('#comment-action'+comment_id+' #up-vote-comment').addClass('active');
                        $('#comment-action'+comment_id+' #down-vote-comment').removeClass('active');
                    }else if(data.down_vote == true){
                        $('#comment-action'+comment_id+' #down-vote-comment').addClass('active');
                        $('#comment-action'+comment_id+' #up-vote-comment').removeClass('active');
                    }else{
                        $('#comment-action'+comment_id+' #up-vote-comment').removeClass('active');
                        $('#comment-action'+comment_id+' #down-vote-comment').removeClass('active');
                    }
                }
            });
        });


        /**
         * hash tag or mention
         */
         var regex = /[#](\w+)$/ig;
         $(document).on('keyup', '#add-question-form textarea[name="content"]', function(){
             var content = $.trim($(this).val());
             var text = content.match(regex);
 
             if(text != null){
                 var dataString = 'hashtag='+text;
                 $.ajax({
                     url 	: rzHashtagShow.ajax_url,
                     type 	: "POST",
                     data 	: {'hashtag': text, 'nonce': rzHashtagShow.rz_hashtag_show_nonce, 'action': 'rz_show_hashtags_data'},
                     cache 	: false,
                     success : function(data){
                         console.log(data);
                         $('#rz-hashbox').html(data);
                         $('#rz-hashbox li').click(function(){
                             var value = $.trim($(this).find('.getValue').text());
                             var oldContent = $('#add-question-form textarea[name="content"]').val();
                             var newContent = oldContent.replace(regex, "");
 
                             $('#add-question-form textarea[name="content"]').val(newContent+value+' ');
                             $('#rz-hashbox li').hide();
                             $('#add-question-form textarea[name="content"]').focus();
 
                             // $('#count').text(content.length);
                         });
                     }
                 });
             }else{
                 $('#rz-hashbox li').hide();
             }
         });

         /**
          * for answer
          */
         $(document).on('keyup', '#answer-textarea', function(){
             let post_id = $(this).data('post_id');
            var content = $.trim($('#answer-form textarea[name="answer'+post_id+'"]').val());
            var text = content.match(regex);

            if(text != null){
                var dataString = 'hashtag='+text;
                $.ajax({
                    url 	: rzHashtagShow.ajax_url,
                    type 	: "POST",
                    data 	: {'hashtag': text, 'nonce': rzHashtagShow.rz_hashtag_show_nonce, 'action': 'rz_show_hashtags_data'},
                    cache 	: false,
                    success : function(data){
                        console.log(data);
                        $('#answer_hashtag'+post_id+'').html(data);
                        $('#answer_hashtag'+post_id+' li').click(function(e){
                            e.preventDefault();
                            var value = $.trim($(this).find('.getValue').text());
                            var oldContent = $('#answer-form textarea[name="answer'+post_id+'"]').val();
                            var newContent = oldContent.replace(regex, "");

                            $('#answer-form textarea[name="answer'+post_id+'"]').val(newContent+value+' ');
                            $('#answer_hashtag'+post_id+' li').hide();
                            $('#answer-form textarea[name="answer'+post_id+'"]').focus();

                            // $('#count').text(content.length);
                        });
                    }
                });
            }else{
                $('#answer_hashtag'+post_id+' li').hide();
            }
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
                    console.log(data);
                    form[0].reset();
                    $('#submit-replay-form button[type="submit"]').removeClass('disabled');
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
                    console.log(data);
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
                    console.log(data);
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
         * add discussion
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
                beforeSend: function(){
                    $('#add_discussion_form button[type="submit"]').addClass('disabled');
                },
                success: function(data){
                    $('#discussion-error').html(data);
                    form[0].reset();
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
                beforeSend: function(){
                    $('#add_comment_discussion button[type="submit"]').addClass('disabled');
                },
                success: function(data){
                    console.log(data);
                    $('#add_comment_discussion button[type="submit"]').removeClass('disabled');
                }
            });
        });

        /**
         * like comment
         */
        $(document).on('click', '#comment-discuss-up', function(e){
            e.preventDefault();
            let button = $(this);
            let comment_id = button.data('comment_id');
            let counter = $('#dis-counter'+comment_id).text();
            $.ajax({
                url: rzLikeDislikeDiscussComment.ajax_url,
                method: 'POST',
                data: {'comment_id': comment_id, 'like_type': 'up-like', 'nonce': rzLikeDislikeDiscussComment.rz_like_discussion_comment, 'action': 'add_like_to_discuss_comment'},
                dataType: 'JSON',
                success: function(data){
                    console.log(data);
                    if(data.data_res == true){
                        button.addClass('active');
                        counter++;
                        $('#dis-counter'+comment_id).text(counter);
                        if(counter < 0){
                            $('#dis-counter'+comment_id).removeClass('text-success');
                            $('#dis-counter'+comment_id).addClass('text-danger');
                        }else{
                            $('#dis-counter'+comment_id).addClass('text-success');
                            $('#dis-counter'+comment_id).removeClass('text-danger');
                        }
                    }else{
                        button.removeClass('active');
                        counter--;
                        $('#dis-counter'+comment_id).text(counter);
                        if(counter < 0){
                            $('#dis-counter'+comment_id).removeClass('text-success');
                            $('#dis-counter'+comment_id).addClass('text-danger');
                        }else{
                            $('#dis-counter'+comment_id).addClass('text-success');
                            $('#dis-counter'+comment_id).removeClass('text-danger');
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
            let button = $(this);
            let comment_id = button.data('comment_id');
            let counter = $('#dis-counter'+comment_id).text();
            $.ajax({
                url: rzLikeDislikeDiscussComment.ajax_url,
                method: 'POST',
                data: {'comment_id': comment_id, 'like_type': 'down-like', 'nonce': rzLikeDislikeDiscussComment.rz_like_discussion_comment, 'action': 'add_like_to_discuss_comment'},
                dataType: 'JSON',
                success: function(data){
                    console.log(data);
                    if(data.data_res == true){
                        button.addClass('active');
                        counter--;
                        $('#dis-counter'+comment_id).text(counter);
                        if(counter < 0){
                            $('#dis-counter'+comment_id).removeClass('text-success');
                            $('#dis-counter'+comment_id).addClass('text-danger');
                        }else{
                            $('#dis-counter'+comment_id).addClass('text-success');
                            $('#dis-counter'+comment_id).removeClass('text-danger');
                        }
                    }else{
                        button.removeClass('active');
                        counter++;
                        $('#dis-counter'+comment_id).text(counter);
                        if(counter < 0){
                            $('#dis-counter'+comment_id).removeClass('text-success');
                            $('#dis-counter'+comment_id).addClass('text-danger');
                        }else{
                            $('#dis-counter'+comment_id).addClass('text-success');
                            $('#dis-counter'+comment_id).removeClass('text-danger');
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
                    console.log(data);
                    $('#add_discussion_comment_replay button[type="submit"]').removeClass('disabled');
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
                    console.log(data);
                    if(data.data_res == true){
                        button.addClass('active');
                        counter++;
                        $('#reply-like-counter'+reply_id).text(counter);
                        if(counter < 0){
                            $('#reply-like-counter'+reply_id).removeClass('text-success');
                            $('#reply-like-counter'+reply_id).addClass('text-danger');
                        }else{
                            $('#reply-like-counter'+reply_id).addClass('text-success');
                            $('#reply-like-counter'+reply_id).removeClass('text-danger');
                        }
                    }else{
                        button.removeClass('active');
                        counter--;
                        $('#reply-like-counter'+reply_id).text(counter);
                        if(counter < 0){
                            $('#reply-like-counter'+reply_id).removeClass('text-success');
                            $('#reply-like-counter'+reply_id).addClass('text-danger');
                        }else{
                            $('#reply-like-counter'+reply_id).addClass('text-success');
                            $('#reply-like-counter'+reply_id).removeClass('text-danger');
                        }
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
            let counter = $('#reply-like-counter'+reply_id).text();
            $.ajax({
                url: rzAddRemoveLikeReply.ajax_url,
                method: 'POST',
                data: {'action': 'rz_add_or_remove_like_on_reply', 'nonce' : rzAddRemoveLikeReply.rz_add_like_on_reply, 'reply_id': reply_id, 'reply_type': 'down-reply'},
                dataType: 'JSON',
                success: function(data){
                    console.log(data);
                    if(data.data_res == true){
                        button.addClass('active');
                        counter--;
                        $('#reply-like-counter'+reply_id).text(counter);
                        if(counter < 0){
                            $('#reply-like-counter'+reply_id).removeClass('text-success');
                            $('#reply-like-counter'+reply_id).addClass('text-danger');
                        }else{
                            $('#reply-like-counter'+reply_id).addClass('text-success');
                            $('#reply-like-counter'+reply_id).removeClass('text-danger');
                        }
                    }else{
                        button.removeClass('active');
                        counter++;
                        $('#reply-like-counter'+reply_id).text(counter);
                        if(counter < 0){
                            $('#reply-like-counter'+reply_id).removeClass('text-success');
                            $('#reply-like-counter'+reply_id).addClass('text-danger');
                        }else{
                            $('#reply-like-counter'+reply_id).addClass('text-success');
                            $('#reply-like-counter'+reply_id).removeClass('text-danger');
                        }
                    }
                }
            });
        });

        /**
         * discuss up like
         */
        $(document).on('click', '#discuss-up-like', function(e){
           e.preventDefault();
           let button = $(this);
           let post_id = button.data('post_id');
           let counter = $('#discuss-like-counter'+post_id).text();
           $.ajax({
               url: rzAddLikeOrDislikeOnDiscuss.ajax_url,
               method: 'POST',
               data: {'action': 'imit_add_like_or_dislike_on_discussion', 'nonce' : rzAddLikeOrDislikeOnDiscuss.rz_like_dis_or_dislike_discuss_post_nonce, 'like_type' : 'up-like', 'post_id' : post_id},
               dataType: 'JSON',
               success: function(data){
                   if(data.data_res == true){
                       button.addClass('active');
                       counter++;
                       $('#discuss-like-counter'+post_id).text(counter);
                       if(counter < 0){
                           $('#discuss-like-counter'+post_id).removeClass('text-success');
                           $('#discuss-like-counter'+post_id).addClass('text-danger');
                       }else{
                           $('#discuss-like-counter'+post_id).addClass('text-success');
                           $('#discuss-like-counter'+post_id).removeClass('text-danger');
                       }
                   }else{
                       button.removeClass('active');
                       counter--;
                       $('#discuss-like-counter'+post_id).text(counter);
                       if(counter < 0){
                           $('#discuss-like-counter'+post_id).removeClass('text-success');
                           $('#discuss-like-counter'+post_id).addClass('text-danger');
                       }else{
                           $('#discuss-like-counter'+post_id).addClass('text-success');
                           $('#discuss-like-counter'+post_id).removeClass('text-danger');
                       }
                   }
               }
           })
        });


        /**
         * discuss down like
         */
        $(document).on('click', '#discuss-down-like', function(e){
           e.preventDefault();
           let button = $(this);
           let post_id = button.data('post_id');
            let counter = $('#discuss-like-counter'+post_id).text();
           $.ajax({
               url: rzAddLikeOrDislikeOnDiscuss.ajax_url,
               method: 'POST',
               data: {'action': 'imit_add_like_or_dislike_on_discussion', 'nonce' : rzAddLikeOrDislikeOnDiscuss.rz_like_dis_or_dislike_discuss_post_nonce, 'like_type' : 'down-like', 'post_id' : post_id},
               dataType: 'JSON',
               success: function(data){
                   console.log(data);
                   if(data.data_res == true){
                       button.addClass('active');
                       counter--;
                       $('#discuss-like-counter'+post_id).text(counter);
                       if(counter < 0){
                           $('#discuss-like-counter'+post_id).removeClass('text-success');
                           $('#discuss-like-counter'+post_id).addClass('text-danger');
                       }else{
                           $('#discuss-like-counter'+post_id).addClass('text-success');
                           $('#discuss-like-counter'+post_id).removeClass('text-danger');
                       }
                   }else{
                       button.removeClass('active');
                       counter++;
                       $('#discuss-like-counter'+post_id).text(counter);
                       if(counter < 0){
                           $('#discuss-like-counter'+post_id).removeClass('text-success');
                           $('#discuss-like-counter'+post_id).addClass('text-danger');
                       }else{
                           $('#discuss-like-counter'+post_id).addClass('text-success');
                           $('#discuss-like-counter'+post_id).removeClass('text-danger');
                       }
                   }
               }
           })
        });

        /**
         * if user click follow button
         */
        $(document).on('click', '#rz-follow', function(e){
            e.preventDefault();
            let button = $(this);
            let receiver_id = button.data('receiver_id');
            $.ajax({
                url: rzUserFollowUnfollow.ajax_url,
                method: 'POST',
                data: {'action': 'rz_user_follow_unfollow', 'nonce' : rzUserFollowUnfollow.rz_user_follow_unfollow, 'receiver_id' : receiver_id},
                dataType: 'JSON',
                beforeSend: function(){
                  button.addClass('disabled');
                },
                success: function(data){
                    console.log(data);
                    if(data.status == 'follow'){
                        button.addClass('btn-secondary');
                        button.removeClass('follow');
                        button.html('<i class="fas fa-minus-circle me-2"></i>Unfollow');
                    }else{
                        button.addClass('follow');
                        button.removeClass('btn-secondary');
                        button.html('<i class="fas fa-plus-circle me-2"></i>Follow');
                    }
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
                beforeSend: function(){
                    $('#update-rz-user-profile button[type="submit"]').addClass('disabled');
                },
                success: function(data){
                    console.log(data);
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
                    console.log(data);
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
                    console.log(data);
                    button.parent().children('p').removeClass('rz-secondary-color');
                    button.parent().children('p').addClass('text-danger');
                    button.parent().children('p').text('Educational info deleted.');
                }
            });
        });


        /**
         * add dairy
         */
        $(document).on('submit', '#add-dairy', function(e){
            e.preventDefault();
            let form_data = new FormData(this);
            let form = $(this);
            form_data.append('action', 'imit_rz_add_dairy');
            form_data.append('nonce', rzAddDairy.rz_add_dairy_nonce);
            $.ajax({
                url: rzAddDairy.ajax_url,
                method: 'POST',
                data: form_data,
                contentType: false,
                processData: false,
                beforeSend: function(){
                    $('#add-dairy button[type="submit"]').addClass('disabled');
                },
                success: function(data){
                    form[0].reset();
                    $('#dairy-message').html(data);
                    $('#add-dairy button[type="submit"]').removeClass('disabled');
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
           form_data.append('action', 'rz_join_partner_program');
           form_data.append('nonce', rzAddPartnerProgram.rz_add_partner_program);
           $.ajax({
               url: rzAddPartnerProgram.ajax_url,
               method: 'POST',
               data: form_data,
               contentType: false,
               processData: false,
               beforeSend: function(){
                   $('#join-partner-program button[type="submit"]').addClass('disabled');
               },
               success: function(data){
                   form[0].reset();
                   $('#partner-message').html(data);
                   $('#join-partner-program button[type="submit"]').removeClass('disabled');
               }
           });
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
                   console.log(data);
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
                    console.log(data);
                    button.parent().parent().remove();
                }
            });
        });

        /**
         * if user click create new discussion post
         */
        $(document).on('click', '.create-new-post', function(e){
            e.preventDefault();
            $(this).parent().parent().fadeOut('fast');
            $('#add_discussion_form').slideDown();
        });


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
        $(document).on('click', '#notification-bell', function(e){
            e.preventDefault();
            $('.dropdown-notification').fadeToggle('fast');
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
        $(document).on('click', '.dropdown-notification .tab-link', function(e){
            e.preventDefault();
            let target = $(this).data('target');
            if(target == 'notification-tab'){
                get_all_notification(target);
            }else if(target == 'inbox-tab'){
                get_all_message(target);
            }
            $('.dropdown-notification .tab-link').removeClass('active');
            $(this).addClass('active');
            $('.tab-content').hide();
            $('#'+target).fadeIn('fast');
        });

        /**
         * get all notification
         */
         get_all_notification('notification-tab');
        function get_all_notification(target){
            $.ajax({
                url: rzGetNotification.ajax_url,
                method: 'POST',
                data: {'action' : 'rz_get_all_notification', 'nonce' : rzGetNotification.rz_get_all_notification_nonce},
                success: function(data){
                    console.log(data);
                    $('#'+target+' #'+target+'-ul').html(data);
                }
            });
        }

        /**
         * get all message
         */
        function get_all_message(target){
            $.ajax({
                url: rzGetMessage.ajax_url,
                method: 'POST',
                data: {'action' : 'rz_get_all_message', 'nonce' : rzGetMessage.rz_get_all_message_nonce},
                success: function(data){
                    console.log(data);
                    $('#'+target+' #'+target+'-ul').html(data);
                }
            });
        }

        /**
         * live check notification
         */
         get_all_live_notification();
        function get_all_live_notification(){
            $.ajax({
                url: rzGetLiveNotifiation.ajax_url,
                method: 'POST',
                data: {'action' : 'rz_get_live_notification', 'nonce' : rzGetLiveNotifiation.rz_get_live_notification_nonce},
                success: function(data){
                    console.log(data);
                    if(data == 'exists'){
                        $('#notification-active').html('<span></span>');
                    }
                    setTimeout(get_all_live_notification, 3000);
                }
            });
        }


        /**
         * if user click see quiz button
         */
        $(document).on('click', '#show-quiz', function(e){
            e.preventDefault();
            let quiz_id = $(this).data('quiz_id');
            $('#quiz-start'+quiz_id).slideToggle();
        });

        /**
         * if user click next question
         */
        let count = 1
        $(document).on('click', '#next-question', function(e){
            e.preventDefault();
            let quiz_id = $(this).data('quiz_id');
            let target = $(this).data('target');
            let answer = $('#question'+(target - 1)+' input[type="radio"]:checked').val();
            if(typeof(answer) == 'undefined'){
                $("#question"+(target - 1)+" #message-error").text("Please chose an asnwer.");
            }else{
                $('#quiz-start'+quiz_id+' #counter').html(count + 1);
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
            let form_data = new FormData(this);
            let quiz_id = $(this).data('quiz_id');
            form_data.append('action', 'rz_submit_quiz_for_result');
            form_data.append('nonce', rzGetQuizResult.rz_get_quiz_result_nonce);
            form_data.append('quiz_id', quiz_id);
            $.ajax({
                url: rzGetQuizResult.ajax_url,
                method: 'POST',
                data: form_data,
                contentType: false,
                processData: false,
                success: function(data){
                    console.log(data);
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
                            console.log(data);
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
                            console.log(data);
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
                text: "Once deleted, you will not be able to recover this imaginary file!",
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
                            console.log(data);
                            if(data == 'done'){
                                $('#question'+question_id).html('<div class="text-center"><i class="fas fa-trash text-danger fz-20"></i><p class="mb-0 fz-16 rz-secondary-color">Question deleted!</p></div>');
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
         * opend modal with user data
         */
         let login_modal = new bootstrap.Modal(document.getElementById('send-message-modal'));
        $(document).on('click', '#send-message-button', function(e){
            e.preventDefault();
            let user_id = $(this).data('user_id');

            let username = $('#name'+user_id).text();
            $('#send-message-modal h2').text('Send message to "'+username+'"');
            $('#send-message-form').data('user_id', user_id);
            login_modal.show();
        });

        /**
         * send message
         */
        $(document).on('submit', '#send-message-form', function(e){
            e.preventDefault();

            let user_id = $(this).data('user_id');
            let form_data = new FormData(this);
            form_data.append('user_id', user_id);
            form_data.append('action', 'rz_add_message_action');
            form_data.append('nonce', rzSendMessage.rz_send_message_nonce);
            $('#send-message-form button[type="submit"]').addClass('disabled');

            $.ajax({
                url: rzSendMessage.ajax_url,
                method: "POST",
                data: form_data,
                contentType: false,
                processData: false,
                success: function(data){
                    console.log(login_modal);
                    login_modal.hide();
                    swal('success', 'Message send successfully.');
                    $('#send-message-form button[type="submit"]').removeClass('disabled');
                }
            });
        });



    });

})(jQuery)