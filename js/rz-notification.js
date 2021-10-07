(function(){
    $(document).ready(function(){
        /**
         * load more notification
         */
        let win = $(window);
        fullNotificationStart = 0;
        notificationFullReachmax = false;

        win.on('scroll', function(){
            if($(document).height() <= (win.height() + win.scrollTop() + 1000) && notificationFullReachmax == false){
                fullNotificationStart += 15;
                getFullNotification(fullNotificationStart);
            }
        });

        function getFullNotification(start = 0){
            if(notificationFullReachmax == false){
                notificationFullReachmax = true;
                $('#notificationLoader').fadeIn('fast');
                $.ajax({
                    url: rzGetFullNotification.ajax_url,
                    method: 'POST',
                    data: {'action' : 'rz_get_full_notification', 'nonce' : rzGetFullNotification.rz_get_full_notification_nonce, 'start' : start},
                    success: function(data){
                        if(data == 'notificationReachMax'){
                            notificationFullReachmax = true;
                        }else{
                            $('#full-notification-data').append(data);
                            notificationFullReachmax = false;
                        }
                        $('#notificationLoader').fadeOut('fast');
                    }
                });
            }
        }
    });
})(jQuery)