(function($){
    $(document).ready(function(){
        /**
         * tab content
         */
        let target = 'news-feed';
        let page_num = 1;
        let win = $(window);
        let postReachMax = false;
        $(document).on('click', '.tab-link', function(e){
            e.preventDefault();
            target = $(this).data('target');
            page_num = 1;
            postReachMax = false;
            if(target == "news-feed"){
                get_news_feed_data(target);
            }else if(target == 'popular-questions'){
                get_popular_question(target);
            }else if(target == 'most-answered'){
                get_most_answered_question(target);
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
                            $('#'+target+' #'+target+'-ul').append('<li class="bg-light rz-br rz-border p-5 text-center list-unstyled mt-3">\n' +
                                '                                    <i class="fas fa-blog"></i>\n' +
                                '                                    <p class="mb-0 imit-font fz-16 rz-secondary-color">No posts to show.</p>\n' +
                                '                                </li>');
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
                            $('#'+target+' #'+target+'-ul').append('<li class="bg-light rz-br rz-border p-5 text-center list-unstyled mt-3">\n' +
                                '                                    <i class="fas fa-blog"></i>\n' +
                                '                                    <p class="mb-0 imit-font fz-16 rz-secondary-color">No posts to show.</p>\n' +
                                '                                </li>');
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
                            $('#'+target+' #'+target+'-ul').append('<li class="bg-light rz-br rz-border p-5 text-center list-unstyled mt-3">\n' +
                                '                                    <i class="fas fa-blog"></i>\n' +
                                '                                    <p class="mb-0 imit-font fz-16 rz-secondary-color">No posts to show.</p>\n' +
                                '                                </li>');
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