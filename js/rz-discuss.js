(function(){
    $(document).ready(function(){
        /**
         * discussion page load
         */
         let target = 'discuss-and-debate';
         let page_num = 1;
         let win = $(window);
         let postReachMax = false;
         $(document).on('click', '.tab-link', function(e){
             e.preventDefault();
             target = $(this).data('target');
             page_num = 1;
             postReachMax = false;
             if(target == "discuss-and-debate"){
                discuss_and_debate(target);
             }else if(target == 'newest'){
                get_newest_posts(target);
             }else if(target == 'most-viwed'){
                get_most_viwed_posts(target);
             }else if(target == 'hotely-debated'){
                get_hotely_debated_posts(target);
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
                if(target == "discuss-and-debate"){
                    discuss_and_debate(target, 'append', page_num);
                }else if(target == "newest"){
                    get_newest_posts(target, 'append', page_num);
                }else if(target == 'most-viwed'){
                    get_most_viwed_posts(target, 'append', page_num);
                }else if(target == 'hotely-debated'){
                    get_hotely_debated_posts(target, 'append', page_num);
                }
            }
        });

         /**
          * fetch discuss and debate posts
          */
          discuss_and_debate(target);
         function discuss_and_debate(target, action = 'html', hpage_num = 1){
            if(postReachMax === false){
                postReachMax = true;
                $('#tab-content-loader').fadeIn('fast');
                $.ajax({
                    url: rzDiscussDebate.ajax_url,
                    method: 'POST',
                    data: {'action': 'rz_discuss_and_debate_posts', 'nonce' : rzDiscussDebate.rz_user_discuss_and_debate, 'page_num' : page_num},
                    success: function(data){
                        console.log(data);
                        if(data == 'discussAndDebateReachmax'){
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
          * get newest posts
          */
          function get_newest_posts(target, action = 'html', page_num = 1){
            if(postReachMax === false){
                postReachMax = true;
                $('#tab-content-loader').fadeIn('fast');
                $.ajax({
                    url: rzNewstPosts.ajax_url,
                    method: 'POST',
                    data: {'action': 'rz_get_newest_posts', 'nonce' : rzNewstPosts.rz_newest_posts_nonce, 'page_num' : page_num},
                    success: function(data){
                        console.log(data);
                        if(data == 'newestPostReachmax'){
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
          * get most viewd discussion posts
          */
         function get_most_viwed_posts(target, action = 'html', page_num = 1){
            if(postReachMax === false){
                postReachMax = true;
                $('#tab-content-loader').fadeIn('fast');
                $.ajax({
                    url: rzMostViewedPosts.ajax_url,
                    method: 'POST',
                    data: {'action': 'rz_most_viewd_posts', 'nonce' : rzMostViewedPosts.rz_most_viwed_posts_nonce, 'page_num' : page_num},
                    success: function(data){
                        console.log(data);
                        if(data == 'mostViewedPostsReachmax'){
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
          * get all hotely debated posts
          */
          function get_hotely_debated_posts(target, action = 'html', page_num = 1){
            if(postReachMax === false){
                postReachMax = true;
                $('#tab-content-loader').fadeIn('fast');
                $.ajax({
                    url: rzHoteDebated.ajax_url,
                    method: 'POST',
                    data: {'action': 'rz_hotely_debated_posts', 'nonce' : rzHoteDebated.rz_most_hotely_debated_posts_nonce, 'page_num' : page_num},
                    success: function(data){
                        console.log(data);
                        if(data == 'mostHotelyDebatedReachmax'){
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