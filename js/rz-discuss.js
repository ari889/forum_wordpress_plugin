(function(){
    $(document).ready(function(){
        /**
         * discussion page load
         */
         let target = 'newest';
         let page_num = 1;
         let win = $(window);
         let postReachMax = false;
         let newest_click = false;
         let most_view = false;
         let hotely_debated = false;
         $(document).on('click', '.tab-link', function(e){
             e.preventDefault();
             target = $(this).data('target');
             if(target == 'newest' && newest_click == false){
                page_num = 1;
                postReachMax = false;
                get_newest_posts(target);
                newest_click = true;
             }else if(target == 'most-viwed' && most_view == false){
                page_num = 1;
                postReachMax = false;
                get_most_viwed_posts(target);
                most_view = true;
             }else if(target == 'hotely-debated' && hotely_debated == false){
                page_num = 1;
                postReachMax = false;
                get_hotely_debated_posts(target);
                hotely_debated = true;
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
                if(target == "newest"){
                    get_newest_posts(target, 'append', page_num);
                }else if(target == 'most-viwed'){
                    get_most_viwed_posts(target, 'append', page_num);
                }else if(target == 'hotely-debated'){
                    get_hotely_debated_posts(target, 'append', page_num);
                }
            }
        });

         /**
          * get newest posts
          */
          get_newest_posts(target);
          function get_newest_posts(target, action = 'html', page_num = 1){
            if(postReachMax === false){
                postReachMax = true;
                $('#tab-content-loader').fadeIn('fast');
                $.ajax({
                    url: rzNewstPosts.ajax_url,
                    method: 'POST',
                    data: {'action': 'rz_get_newest_posts', 'nonce' : rzNewstPosts.rz_newest_posts_nonce, 'page_num' : page_num},
                    success: function(data){
                        if(data == 'newestPostReachmax'){
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
                        if(data == 'mostViewedPostsReachmax'){
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
                        if(data == 'mostHotelyDebatedReachmax'){
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