(function(){
    $(document).ready(function(){

        if(typeof(profile_user_id) !== 'undefined'){
            /**
             * get current user asked questions
             */
            let start = 0;
            let target = 'profile-feed';
            let page_num = 1;
            let win = $(window);
            let postReachMax = false;
            let profile_feed_click = false;
            let profile_user_answers_click = false;
            let profile_user_vote_click = false;
            let profile_user_comment_click = false;
            let following_user_click = false;
            let user_dairy_click = false;
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
                }else if(target == 'following-user' && following_user_click == false){
                    page_num = 1;
                    postReachMax = false;
                    get_following_user(target, profile_user_id);
                    following_user_click = true;
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
                    }
                }
            });

            /**
            * get user all asked question
            */
            question_asked('profile-feed');
            function question_asked(target, action = 'html', page_num = 1){
                if(postReachMax === false){
                    postReachMax = true;
                    $('#tab-content-loader').fadeIn('fast');
                    $.ajax({
                        url: rzAskedQuestion.ajax_url,
                        method: 'POST',
                        data: {'action': 'rz_question_asked_posts', 'nonce' : rzAskedQuestion.rz_asked_question_nonce, 'page_num' : page_num, 'user_id' : profile_user_id},
                        success: function(data){
                            console.log(data);
                            if(data == 'profileFeedReachmax'){
                                postReachMax = true;
                                if(action == 'html'){
                                    $('#'+target+' #'+target+'-ul').html('<li class="bg-light rz-br rz-border p-5 text-center list-unstyled mt-3">\n' +
                                    '                                    <i class="fas fa-blog"></i>\n' +
                                    '                                    <p class="mb-0 imit-font fz-16 rz-secondary-color">No posts to show.</p>\n' +
                                    '                                </li>');
                                }else{
                                    $('#'+target+' #'+target+'-ul').append('<li class="bg-light rz-br rz-border p-5 text-center list-unstyled mt-3">\n' +
                                    '                                    <i class="fas fa-blog"></i>\n' +
                                    '                                    <p class="mb-0 imit-font fz-16 rz-secondary-color">No posts to show.</p>\n' +
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
            function get_answered_questions(target, action = 'html', page_num = 1){
                if(postReachMax === false){
                    postReachMax = true;
                    $('#tab-content-loader').fadeIn('fast');
                    $.ajax({
                        url: rzAnsweredQuestion.ajax_url,
                        method: 'POST',
                        data: {'action': 'rz_question_answered_posts', 'nonce' : rzAnsweredQuestion.rz_answered_question_nonce, 'page_num' : page_num, 'user_id': profile_user_id},
                        success: function(data){
                            console.log(data);
                            if(data == 'answeredFeedReachmax'){
                                postReachMax = true;
                                if(action == 'html'){
                                    $('#'+target+' #'+target+'-ul').html('<li class="bg-light rz-br rz-border p-5 text-center list-unstyled mt-3">\n' +
                                    '                                    <i class="fas fa-blog"></i>\n' +
                                    '                                    <p class="mb-0 imit-font fz-16 rz-secondary-color">No posts to show.</p>\n' +
                                    '                                </li>');
                                }else{
                                    $('#'+target+' #'+target+'-ul').append('<li class="bg-light rz-br rz-border p-5 text-center list-unstyled mt-3">\n' +
                                    '                                    <i class="fas fa-blog"></i>\n' +
                                    '                                    <p class="mb-0 imit-font fz-16 rz-secondary-color">No posts to show.</p>\n' +
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
                            console.log(data);
                            if(data == 'profileVOtedQuestionsReachmax'){
                                postReachMax = true;
                                if(action == 'html'){
                                    $('#'+target+' #'+target+'-ul').html('<li class="bg-light rz-br rz-border p-5 text-center list-unstyled mt-3">\n' +
                                    '                                    <i class="fas fa-blog"></i>\n' +
                                    '                                    <p class="mb-0 imit-font fz-16 rz-secondary-color">No posts to show.</p>\n' +
                                    '                                </li>');
                                }else{
                                    $('#'+target+' #'+target+'-ul').append('<li class="bg-light rz-br rz-border p-5 text-center list-unstyled mt-3">\n' +
                                    '                                    <i class="fas fa-blog"></i>\n' +
                                    '                                    <p class="mb-0 imit-font fz-16 rz-secondary-color">No posts to show.</p>\n' +
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
                            console.log(data);
                            if(data == 'profileCommentedQeustionsReachmax'){
                                postReachMax = true;
                                if(action == 'html'){
                                    $('#'+target+' #'+target+'-ul').html('<li class="bg-light rz-br rz-border p-5 text-center list-unstyled mt-3">\n' +
                                    '                                    <i class="fas fa-blog"></i>\n' +
                                    '                                    <p class="mb-0 imit-font fz-16 rz-secondary-color">No posts to show.</p>\n' +
                                    '                                </li>');
                                }else{
                                    $('#'+target+' #'+target+'-ul').append('<li class="bg-light rz-br rz-border p-5 text-center list-unstyled mt-3">\n' +
                                    '                                    <i class="fas fa-blog"></i>\n' +
                                    '                                    <p class="mb-0 imit-font fz-16 rz-secondary-color">No posts to show.</p>\n' +
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
                            console.log(data);
                            if(data == 'profileFollowingUserReachmax'){
                                postReachMax = true;
                                if(action == 'html'){
                                    $('#'+target+' #'+target+'-ul').append('<li class="col-md-12 list-unstyled text-center"><div class="bg-light p-4 rz-br rz-border mt-3"><i class="fas fa-user"></i><p class="mb-0 imit-font fz-16 rz-secondary-color">No users to show.</p></div></li>');
                                }else{
                                    $('#'+target+' #'+target+'-ul').append('<li class="col-md-12 list-unstyled text-center"><div class="bg-light p-4 rz-br rz-border mt-3"><i class="fas fa-user"></i><p class="mb-0 imit-font fz-16 rz-secondary-color">No users to show.</p></div></li>');
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
                            console.log(data);
                            if(data == 'userDairyReachmax'){
                                postReachMax = true;
                                if(action == 'html'){
                                    $('#'+target+' #'+target+'-ul').html('<li class="bg-light rz-br rz-border p-5 text-center list-unstyled mt-3">\n' +
                                    '                                    <i class="fas fa-blog"></i>\n' +
                                    '                                    <p class="mb-0 imit-font fz-16 rz-secondary-color">No posts to show.</p>\n' +
                                    '                                </li>');
                                }else{
                                    $('#'+target+' #'+target+'-ul').append('<li class="bg-light rz-br rz-border p-5 text-center list-unstyled mt-3">\n' +
                                    '                                    <i class="fas fa-blog"></i>\n' +
                                    '                                    <p class="mb-0 imit-font fz-16 rz-secondary-color">No posts to show.</p>\n' +
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


    });
})(jQuery)