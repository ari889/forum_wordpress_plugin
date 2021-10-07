(function($){
    $(document).ready(function(){

        let tagReachmax = false;
        let start = 1;
        let limit = 40;
        let win = $(window);


        win.on('scroll', function(){
            if($(document).height() <= (win.height() + win.scrollTop() + 1000) && tagReachmax == false){
                start += 1;
                fetchTagsArchives('append', start);
            }
        });


        /**
         * fetch tag function
         */
         fetchTagsArchives();
        function fetchTagsArchives(action = 'html', start = 1, limit = 40){
            if(tagReachmax === false){
                tagReachmax = true;
                $('#tag-archive-loader').fadeIn('fast');
                $.ajax({
                    url: rzTagsArchive.ajax_url,
                    method: 'POST',
                    data: {'action': 'rz_fetch_tags_archive_data', 'nonce' : rzTagsArchive.rz_fetch_tags_archive, 'start' : start, 'limit' : limit},
                    success: function(data){
                        if(data == 'tagReachMax'){
                            tagReachmax = true;
                            $('#fetch-all-terms').append('<li class="list-unstyled col-12">\n' +
                        '                                    <p class="mb-0 imit-font fz-16 rz-secondary-color bg-light rz-br rz-border p-5 text-center">No more tags to show.</p>\n' +
                        '                                </li>');
                        }else{
                            if(action == 'html'){
                                $('#fetch-all-terms').html(data);
                            }else{
                                $('#fetch-all-terms').append(data);
                            }
                            
                            tagReachmax = false;
                        }
                        $('#tag-archive-loader').fadeOut('fast');
                    }
                });
            }
        }
    });
})(jQuery)