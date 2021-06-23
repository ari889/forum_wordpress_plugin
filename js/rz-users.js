(function(){
    $(document).ready(function(){
        /**
         * get all users
         */
        let userReachMax = false;
        let start = 0;
        let limit = 10;
        get_all_users();
        function get_all_users(action = 'html', start = 0, limit = 10){
            if(userReachMax === false){
                userReachMax = true;
                $('#tab-content-loader').fadeIn('fast');
                $.ajax({
                    url: rzSuggestedUsers.ajax_url,
                    method: 'POST',
                    data: {'action': 'rz_suggested_user_find', 'nonce' : rzSuggestedUsers.rz_suggested_users_nonce, 'start' : start, 'limit' : limit},
                    success: function(data){
                        console.log(data);
                        if(data == 'userReachMax'){
                            userReachMax = true;
                            $('#fetch-search-user').append('<li class="bg-light rz-br rz-border p-5 text-center list-unstyled mt-3">\n' +
                                '                                    <i class="fas fa-user"></i>\n' +
                                '                                    <p class="mb-0 imit-font fz-16 rz-secondary-color">No user to show.</p>\n' +
                                '                                </li>');
                        }else{
                            if(action == 'html'){
                                $('#fetch-search-user').html(data);
                            }else{
                                $('#fetch-search-user').append(data);
                            }
                            
                            userReachMax = false;
                        }
                        $('#tab-content-loader').fadeOut('fast');
                    }
                });
            }
        }
    });
})(jQuery)