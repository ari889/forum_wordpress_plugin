<?php

/**
 * show hashtags data
 */
add_action('wp_ajax_rz_show_hashtags_data', function(){
    global $wpdb;
    $nonce = $_POST['nonce'];
    if(wp_verify_nonce($nonce, 'rz-hashtag-show')){
        $hashtag = $_POST['hashtag'];
        
        if(isset($hashtag)){
            if(substr($hashtag[0], 0, 1) === '#'){
                $trend = str_replace('#', '', $hashtag[0]);
                $all_trends = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_hashtags WHERE hashtag LIKE '$trend%' LIMIT 5", ARRAY_A);
                foreach ($all_trends as $data) {
                    echo ' <li class="list-group-item"><a href="#" class="rz-color imit-font fz-14 fw-500 getValue">#'.$data['hashtag'].'</a></li>';
                }
            }
        }
    }
    die();
});