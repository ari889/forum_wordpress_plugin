(function($){
    $(document).ready(function(){


        /**
         * user activity show
         */
         let activityReachMax = false;
         let activityStart = 0;
         $('#show_user_activity').scroll(function(e){
             if(($('#show_user_activity').scrollTop() + $('#show_user_activity').height() + 100) >= $('#show_user_activity')[0].scrollHeight && activityReachMax === false){
                activityStart += 100;
                user_activity_show(activityStart);
             }
         });

         user_activity_show();
        function user_activity_show(start = 0){
            if(activityReachMax == false){
                activityReachMax = true;
                $.ajax({
                    url: rzActivity.ajax_url,
                    method: 'POST',
                    data: {'action': 'imit_fetch_activity', 'nonce': rzActivity.rz_activity_view_nonce, 'start' : start},
                    success: function(data){
                        if(data == 'activityReachmax'){
                            activityReachMax = true;
                        }else{
                            $('#show_user_activity').append(data);
                            activityReachMax = false;
                        }
                    }
                });
            }
        }

        /**
         * tab content
         */
        let target = 'news-feed';
        let page_num = 1;
        let win = $(window);
        let postReachMax = false;
        let news_feed_click = false;
        let new_question_click = false;
        let popular_question_click = false;
        let most_answer_click = false;
        let get_post_by_tag = false;
        $(document).on('click', '.tab-link', function(e){
            e.preventDefault();
            target = $(this).data('target');
            if(target == "news-feed" && news_feed_click == false){
                page_num = 1;
                postReachMax = false;
                get_news_feed_data(target);
                news_feed_click = true;
            }else if(target == 'popular-questions' && popular_question_click == false){
                page_num = 1;
                postReachMax = false;
                get_popular_question(target);
                popular_question_click = true;
            }else if(target == 'most-answered' && most_answer_click == false){
                page_num = 1;
                postReachMax = false;
                get_most_answered_question(target);
                most_answer_click = true;
            }else if(target == 'new-questions' && new_question_click == false){
                page_num = 1;
                postReachMax = false;
                get_new_questions(target);
                new_question_click = true;
            }else{
                if(target != 'news-feed' && target != 'popular-questions' && target != 'most-answered' && target != 'new-questions'){
                    page_num = 1;
                    postReachMax = false;
                    get_post_by_tags(target);
                    // get_post_by_tag = true;
                }
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
                if(target == "news-feed"){
                    get_news_feed_data(target, 'append', page_num);
                }else if(target == 'popular-questions'){
                    get_popular_question(target, 'append', page_num);
                }else if(target == 'most-answered'){
                    get_most_answered_question(target, 'append', page_num);
                }else{
                    get_post_by_tags(target, 'append', page_num);
                }
            }
        });


        /**
        * get news feed
        */
        get_news_feed_data('news-feed');
        function get_news_feed_data(target, action = 'html', page_num = 1){
            if(postReachMax === false){
                postReachMax = true;
                $('#tab-content-loader').fadeIn('fast');
                $.ajax({
                    url: rzNewsFeed.ajax_url,
                    method: 'POST',
                    data: {'action': 'rz_news_feed_data', 'nonce' : rzNewsFeed.rz_news_feed_posts_nonce, 'page_num' : page_num},
                    success: function(data){
                        if(data == 'newsReachmax'){
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
         * for new questions
         */
        function get_new_questions(target, action = 'html', page_num = 1){
            if(postReachMax === false){
                postReachMax = true;
                $('#tab-content-loader').fadeIn('fast');
                $.ajax({
                    url: rzGetNewQuestions.ajax_url,
                    method: 'POST',
                    data: {'action': 'rz_get_new_question_data', 'nonce' : rzGetNewQuestions.rz_get_new_questions, 'page_num' : page_num},
                    success: function(data){
                        if(data == 'newsReachmax'){
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
        * get popular question
        */
        function get_popular_question(target, action = 'html', page_num = 1){
            if(postReachMax === false){
                postReachMax = true;
                $('#tab-content-loader').fadeIn('fast');
                $.ajax({
                    url: rzPopularQuestion.ajax_url,
                    method: 'POST',
                    data: {'action': 'rz_popular_question_data', 'nonce' : rzPopularQuestion.rz_popular_question_nonce, 'page_num' : page_num},
                    success: function(data){
                        if(data == 'popularPostReachmax'){
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
        * get most most answered question
        */
        function get_most_answered_question(target, action = 'html', page_num = 1){
            if(postReachMax === false){
                postReachMax = true;
                $('#tab-content-loader').fadeIn('fast');
                $.ajax({
                    url: rzMostCommented.ajax_url,
                    method: 'POST',
                    data: {'action': 'rz_most_answered_data', 'nonce' : rzMostCommented.rz_most_commented_nonce, 'page_num' : page_num},
                    success: function(data){
                        if(data == 'mostAnsweredPostReachmax'){
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
         * get posts by tags
         */
        function get_post_by_tags(target, action = 'html', page_num = 1){
            if(postReachMax === false){
                postReachMax = true;
                $('#tab-content-loader').fadeIn('fast');
                $.ajax({
                    url: rzGetPostUsingTags.ajax_url,
                    method: 'POST',
                    data: {'action': 'rz_get_post_using_tags', 'nonce' : rzGetPostUsingTags.rz_get_posts_using_tags, 'page_num' : page_num, 'tag' : target},
                    success: function(data){
                        if(data == 'tagPostReachMax'){
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
    });
})(jQuery)