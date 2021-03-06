(function(){
    $(document).ready(function(){

        if(typeof(profile_user_id) !== 'undefined'){
            /**
             * get current user asked questions
             */
            let start = 0;
            let target = 'rz-profile-user-answers';
            let page_num = 1;
            let win = $(window);
            let postReachMax = false;
            let profile_feed_click = false;
            let profile_user_answers_click = false;
            let profile_user_vote_click = false;
            let profile_user_comment_click = false;
            let following_user_click = false;
            let user_dairy_click = false;
            let following_question = false;
            let following_tags = false;
            $(document).on('click', '.tab-link', function(e){
                e.preventDefault();
                target = $(this).data('target');
                if(target == "profile-feed" && profile_feed_click == false){
                    page_num = 1;
                    postReachMax = false;
                    question_asked(target);
                    profile_feed_click = true;
                }else if(target == "rz-profile-user-answers" && profile_user_answers_click == false){
                    page_num = 1;
                    postReachMax = false;
                    get_answered_questions(target);
                    profile_user_answers_click = true;
                }else if(target == 'rz-profile-user-vote' && profile_user_vote_click == false){
                    page_num = 1;
                    postReachMax = false;
                    get_voted_questions(target);
                    profile_user_vote_click = true;
                }else if(target == 'rz-profile-user-comment' && profile_user_comment_click == false){
                    page_num = 1;
                    postReachMax = false;
                    get_question_commented_posts(target);
                    profile_user_comment_click = true;
                }else if(target == 'following-question' && following_question == false){
                    start = 0;
                    postReachMax = false;
                    get_all_following_questions(target, profile_user_id);
                    following_question = true;
                }else if(target == 'user-dairy' && user_dairy_click == false){
                    page_num = 1;
                    postReachMax = false;
                    get_user_dairy(target);
                    user_dairy_click = true;
                }
                $('.tab-link').removeClass('active');
                $(this).addClass('active');
                $('.tab-content').hide();
                $('#'+target).fadeIn('fast');
                if(target == 'following-question'){
                    $('#following').fadeIn('fast');
                }
            });

            /**
             * if user click following tab
             */
            $(document).on('click', '#following .following-tab .following-link', function(e){
                e.preventDefault();
                target = $(this).data('target');
                $('#following .following-tab .following-link').removeClass('active');
                $(this).addClass('active');
                $('.following-content').hide('fast');
                $('#'+target).fadeIn('fast');
                if(target == "following-question" && following_question == false){
                    start = 0;
                    postReachMax = false;
                    get_all_following_questions(target, profile_user_id);
                    following_question = true;
                }else if(target == 'following-tags' && following_tags == false){
                    start = 0;
                    postReachMax = false;
                    get_all_following_tags(target, profile_user_id);
                    following_tags = false;
                }else if(target == 'following-users' && following_user_click == false){
                    start = 0;
                    postReachMax = false;
                    get_following_user(target, profile_user_id);
                    following_user_click = true;
                }
            });

            /**
            * news feed scroll
            */
            win.on('scroll', function(){
                if($(document).height() <= (win.height() + win.scrollTop() + 1000) && postReachMax == false){
                    page_num += 1;
                    if(target == "profile-feed"){
                        question_asked(target, 'append', page_num);
                    }else if(target == "rz-profile-user-answers"){
                        get_answered_questions(target, 'append', page_num);
                    }else if(target == 'rz-profile-user-vote'){
                        get_voted_questions(target, 'append', page_num);
                    }else if(target == 'rz-profile-user-comment'){
                        get_question_commented_posts(target, page_num);
                    }else if(target == 'following-user'){
                        start += 10;
                        get_following_user(target, profile_user_id, 'append', start, 10);
                    }else if(target == 'user-dairy'){
                        get_user_dairy(target, 'append', page_num);
                    }else if(target == 'following-question'){
                        start += 10;
                        get_all_following_questions(target, profile_user_id, 'append', start, 10);
                    }else if(target == 'following-tags'){
                        start += 10;
                        get_all_following_tags(target, profile_user_id, 'append', start, 10);
                    }else if(target == 'following-users'){
                        start += 10;
                        get_following_user(target, profile_user_id, 'append' , start, 10);
                    }
                }
            });

            /**
            * get user all asked question
            */
            function question_asked(target, action = 'html', page_num = 1){
                if(postReachMax === false){
                    postReachMax = true;
                    $('#tab-content-loader').fadeIn('fast');
                    $.ajax({
                        url: rzAskedQuestion.ajax_url,
                        method: 'POST',
                        data: {'action': 'rz_question_asked_posts', 'nonce' : rzAskedQuestion.rz_asked_question_nonce, 'page_num' : page_num, 'user_id' : profile_user_id},
                        success: function(data){
                            if(data == 'profileFeedReachmax'){
                                postReachMax = true;
                                if(action == 'html'){
                                    $('#'+target+' #'+target+'-ul').html('<li class="bg-light rz-br rz-border p-5 text-center list-unstyled mt-3">\n' +
                                    '                                    <p class="mb-0 imit-font fz-16 rz-secondary-color">No more posts available to show.</p>\n' +
                                    '                                </li>');
                                }else{
                                    $('#'+target+' #'+target+'-ul').append('<li class="bg-light rz-br rz-border p-5 text-center list-unstyled mt-3">\n' +
                                    '                                    <p class="mb-0 imit-font fz-16 rz-secondary-color">No more posts available to show.</p>\n' +
                                    '                                </li>');
                                }
                            }else{
                                if(action == 'html'){
                                    $('#'+target+' #'+target+'-ul').html(data);
                                }else{
                                    $('#'+target+' #'+target+'-ul').append(data);
                                }
                                postReachMax = false;
                            }
                            $('#tab-content-loader').fadeOut('fast');
                        }
                    });
                }
            }

            /**
            * get user answered questions
            */
             get_answered_questions(target);
            function get_answered_questions(target, action = 'html', page_num = 1){
                if(postReachMax === false){
                    postReachMax = true;
                    $('#tab-content-loader').fadeIn('fast');
                    $.ajax({
                        url: rzAnsweredQuestion.ajax_url,
                        method: 'POST',
                        data: {'action': 'rz_question_answered_posts', 'nonce' : rzAnsweredQuestion.rz_answered_question_nonce, 'page_num' : page_num, 'user_id': profile_user_id},
                        success: function(data){
                            if(data == 'answeredFeedReachmax'){
                                postReachMax = true;
                                if(action == 'html'){
                                    $('#'+target+' #'+target+'-ul').html('<li class="bg-light rz-br rz-border p-5 text-center list-unstyled mt-3">\n' +
                                    '                                    <p class="mb-0 imit-font fz-16 rz-secondary-color">No more posts available to show.</p>\n' +
                                    '                                </li>');
                                }else{
                                    $('#'+target+' #'+target+'-ul').append('<li class="bg-light rz-br rz-border p-5 text-center list-unstyled mt-3">\n' +
                                    '                                    <p class="mb-0 imit-font fz-16 rz-secondary-color">No more posts available to show.</p>\n' +
                                    '                                </li>');
                                }
                            }else{
                                if(action == 'html'){
                                    $('#'+target+' #'+target+'-ul').html(data);
                                }else{
                                    $('#'+target+' #'+target+'-ul').append(data);
                                }
                                postReachMax = false;
                            }
                            $('#tab-content-loader').fadeOut('fast');
                        }
                    });
                }
            }


            /**
            * get all voted questions
            */
            function get_voted_questions(target, action = 'html', page_num = 1){
                if(postReachMax === false){
                    postReachMax = true;
                    $('#tab-content-loader').fadeIn('fast');
                    $.ajax({
                        url: rzVotedQuestions.ajax_url,
                        method: 'POST',
                        data: {'action': 'rz_voted_questions_posts', 'nonce' : rzVotedQuestions.rz_voted_questions_nonce, 'page_num' : page_num, 'user_id' : profile_user_id},
                        success: function(data){
                            if(data == 'profileVOtedQuestionsReachmax'){
                                postReachMax = true;
                                if(action == 'html'){
                                    $('#'+target+' #'+target+'-ul').html('<li class="bg-light rz-br rz-border p-5 text-center list-unstyled mt-3">\n' +
                                    '                                    <p class="mb-0 imit-font fz-16 rz-secondary-color">No more posts available to show.</p>\n' +
                                    '                                </li>');
                                }else{
                                    $('#'+target+' #'+target+'-ul').append('<li class="bg-light rz-br rz-border p-5 text-center list-unstyled mt-3">\n' +
                                    '                                    <p class="mb-0 imit-font fz-16 rz-secondary-color">No more posts available to show.</p>\n' +
                                    '                                </li>');
                                }
                            }else{
                                if(action == 'html'){
                                    $('#'+target+' #'+target+'-ul').html(data);
                                }else{
                                    $('#'+target+' #'+target+'-ul').append(data);
                                }
                                postReachMax = false;
                            }
                            $('#tab-content-loader').fadeOut('fast');
                        }
                    });
                }
            }


            /**
            * questions commented
            */
            function get_question_commented_posts(target, action = 'html', page_num = 1){
                if(postReachMax === false){
                    postReachMax = true;
                    $('#tab-content-loader').fadeIn('fast');
                    $.ajax({
                        url: rzCommentedQuestion.ajax_url,
                        method: 'POST',
                        data: {'action': 'rz_commented_questions_posts', 'nonce' : rzCommentedQuestion.rz_commented_questions_nonce, 'page_num' : page_num, 'user_id' : profile_user_id},
                        success: function(data){
                            if(data == 'profileCommentedQeustionsReachmax'){
                                postReachMax = true;
                                if(action == 'html'){
                                    $('#'+target+' #'+target+'-ul').html('<li class="bg-light rz-br rz-border p-5 text-center list-unstyled mt-3">\n' +
                                    '                                    <p class="mb-0 imit-font fz-16 rz-secondary-color">No more posts available to show.</p>\n' +
                                    '                                </li>');
                                }else{
                                    $('#'+target+' #'+target+'-ul').append('<li class="bg-light rz-br rz-border p-5 text-center list-unstyled mt-3">\n' +
                                    '                                    <p class="mb-0 imit-font fz-16 rz-secondary-color">No more posts available to show.</p>\n' +
                                    '                                </li>');
                                }
                            }else{
                                if(action == 'html'){
                                    $('#'+target+' #'+target+'-ul').html(data);
                                }else{
                                    $('#'+target+' #'+target+'-ul').append(data);
                                }
                                postReachMax = false;
                            }
                            $('#tab-content-loader').fadeOut('fast');
                        }
                    });
                }
            }


            /**
            * get following user
            */
            function get_following_user(target, user_id, action = 'html', start = 0, limit = 10){
                if(postReachMax === false){
                    postReachMax = true;
                    $('#tab-content-loader').fadeIn('fast');
                    $.ajax({
                        url: rzFollowingUser.ajax_url,
                        method: 'POST',
                        data: {'action': 'rz_following_users_posts', 'nonce' : rzFollowingUser.rz_following_user_nonce, 'start' : start, 'limit' : limit, 'user_id' : user_id},
                        success: function(data){
                            if(data == 'profileFollowingUserReachmax'){
                                postReachMax = true;
                                if(action == 'html'){
                                    $('#'+target+' #'+target+'-ul').append('<li class="col-md-12 list-unstyled text-center"><div class="bg-light p-4 rz-br rz-border mt-3"><p class="mb-0 imit-font fz-16 rz-secondary-color">No users to show.</p></div></li>');
                                }else{
                                    $('#'+target+' #'+target+'-ul').append('<li class="col-md-12 list-unstyled text-center"><div class="bg-light p-4 rz-br rz-border mt-3"><p class="mb-0 imit-font fz-16 rz-secondary-color">No users to show.</p></div></li>');
                                }
                            }else{
                                if(action == 'html'){
                                    $('#'+target+' #'+target+'-ul').html(data);
                                }else{
                                    $('#'+target+' #'+target+'-ul').append(data);
                                }
                                postReachMax = false;
                            }
                            $('#tab-content-loader').fadeOut('fast');
                        }
                    });
                }
            }


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
                    page_num = 1;
                    postReachMax = false;
                    get_user_dairy('user-dairy');
                    $([document.documentElement, document.body]).animate({
                        scrollTop: $("#user-dairy-ul").offset().top
                    }, 1000);
                }
            });
        });


            /***
            *  get user dairy
            */
            function get_user_dairy(target, action = 'html', page_num = 1){
                if(postReachMax === false){
                    postReachMax = true;
                    $('#tab-content-loader').fadeIn('fast');
                    $.ajax({
                        url: rzUserDairy.ajax_url,
                        method: 'POST',
                        data: {'action': 'rz_user_dairy_posts', 'nonce' : rzUserDairy.rz_user_dairy_nonce, 'page_num' : page_num, 'user_id': profile_user_id},
                        success: function(data){
                            if(data == 'userDairyReachmax'){
                                postReachMax = true;
                                if(action == 'html'){
                                    $('#'+target+' #'+target+'-ul').html('<li class="bg-light rz-br rz-border p-5 text-center list-unstyled mt-3">\n' +
                                    '                                    <p class="mb-0 imit-font fz-16 rz-secondary-color">No more posts available to show.</p>\n' +
                                    '                                </li>');
                                }else{
                                    $('#'+target+' #'+target+'-ul').append('<li class="bg-light rz-br rz-border p-5 text-center list-unstyled mt-3">\n' +
                                    '                                    <p class="mb-0 imit-font fz-16 rz-secondary-color">No more posts available to show.</p>\n' +
                                    '                                </li>');
                                }
                            }else{
                                if(action == 'html'){
                                    $('#'+target+' #'+target+'-ul').html(data);
                                }else{
                                    $('#'+target+' #'+target+'-ul').append(data);
                                }
                                postReachMax = false;
                            }
                            $('#tab-content-loader').fadeOut('fast');
                        }
                    });
                }
            }

            /**
             * get all following questions
             */
            function get_all_following_questions(target, profile_user_id, action = 'html', start = 0, limit = 10){
                if(postReachMax === false){
                    postReachMax = true;
                    $('#tab-content-loader').fadeIn('fast');
                    $.ajax({
                        url: rzFollowingQuestions.ajax_url,
                        method: 'POST',
                        data: {'action': 'rz_get_all_following_questions', 'nonce' : rzFollowingQuestions.rz_get_following_questions, 'start' : start, 'limit' : limit, 'user_id': profile_user_id},
                        success: function(data){
                            if(data == 'followingQuestionReachmax'){
                                postReachMax = true;
                                if(action == 'html'){
                                    $('#'+target+' #'+target+'-ul').html('<li class="bg-light rz-br rz-border p-5 text-center list-unstyled mt-3">\n' +
                                    '                                    <p class="mb-0 imit-font fz-16 rz-secondary-color">No more posts available to show.</p>\n' +
                                    '                                </li>');
                                }else{
                                    $('#'+target+' #'+target+'-ul').append('<li class="bg-light rz-br rz-border p-5 text-center list-unstyled mt-3">\n' +
                                    '                                    <p class="mb-0 imit-font fz-16 rz-secondary-color">No more posts available to show.</p>\n' +
                                    '                                </li>');
                                }
                            }else{
                                if(action == 'html'){
                                    $('#'+target+' #'+target+'-ul').html(data);
                                }else{
                                    $('#'+target+' #'+target+'-ul').append(data);
                                }
                                postReachMax = false;
                            }
                            $('#tab-content-loader').fadeOut('fast');
                        }
                    });
                }
            }


            /**
             * get all following tags
             */
             function get_all_following_tags(target, profile_user_id, action = 'html', start = 0, limit = 10){
                if(postReachMax === false){
                    postReachMax = true;
                    $('#tab-content-loader').fadeIn('fast');
                    $.ajax({
                        url: rzFollowingTags.ajax_url,
                        method: 'POST',
                        data: {'action': 'rz_get_all_following_tags', 'nonce' : rzFollowingTags.rz_get_following_tags, 'start' : start, 'limit' : limit, 'user_id': profile_user_id},
                        success: function(data){
                            if(data == 'followingTagReachMax'){
                                postReachMax = true;
                                if(action == 'html'){
                                    $('#'+target+' #'+target+'-ul').html('<li class="bg-light rz-br rz-border p-5 text-center list-unstyled mt-3">\n' +
                                    '                                    <p class="mb-0 imit-font fz-16 rz-secondary-color">No more posts available to show.</p>\n' +
                                    '                                </li>');
                                }else{
                                    $('#'+target+' #'+target+'-ul').append('<li class="bg-light rz-br rz-border p-5 text-center list-unstyled mt-3">\n' +
                                    '                                    <p class="mb-0 imit-font fz-16 rz-secondary-color">No more posts available to show.</p>\n' +
                                    '                                </li>');
                                }
                            }else{
                                if(action == 'html'){
                                    $('#'+target+' #'+target+'-ul').html(data);
                                }else{
                                    $('#'+target+' #'+target+'-ul').append(data);
                                }
                                postReachMax = false;
                            }
                            $('#tab-content-loader').fadeOut('fast');
                        }
                    });
                }
            }





        }


        $(document).on('submit', '#change_password_form', function(e){
            e.preventDefault();
            let form_data = new FormData(this);
            form_data.append('action', 'rz_change_password_data');
            form_data.append('nonce', rzChangePassword.rz_change_password);


            let logged_in = $('#change_password_form input[name="logged-in"]:checked').val();


            let redirect;
            if(logged_in == 'yes'){
                redirect = false;
            }else{
                redirect = true;
            }

            $.ajax({
                url: rzChangePassword.ajax_url,
                method: 'POST',
                data: form_data,
                contentType: false,
                processData: false,
                dataType: 'JSON',
                beforeSend: function(){
                    $('#change_password_form button[type="submit"]').addClass('disabled');
                },
                success: function(data){
                    if(data.error == true){
                        $('#change_password_form #change_password_error').html(data.message);
                    }else{
                        $('#change_password_form #change_password_error').html(data.message);
                        if(redirect == true){
                            window.location.href = data.logout_url;
                        }
                    }
                    $('#change_password_form button[type="submit"]').removeClass('disabled');
                }
            });
        });


    });
})(jQuery)