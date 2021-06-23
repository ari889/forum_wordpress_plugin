<?php

/**
 * add follow unfollow
 */
add_action('wp_ajax_rz_user_follow_unfollow', function(){
    global $wpdb;
    $nonce = $_POST['nonce'];
    if(wp_verify_nonce($nonce, 'rz-user-follow-unfollow-nonce')){
        $receiver_id = sanitize_key($_POST['receiver_id']).' ';
        $sender_id = get_current_user_id();
        $rz_followers = $wpdb->prefix.'rz_followers';

        $get_all_followers = $wpdb->get_results("SELECT * FROM {$rz_followers} WHERE (sender_id = '$sender_id' AND receiver_id = '$receiver_id') OR (sender_id = '$receiver_id' AND receiver_id = '$sender_id')", ARRAY_A);

        if(!empty($sender_id) && !empty($receiver_id)){
            if(count($get_all_followers) > 0){
                $wpdb->query("DELETE FROM {$rz_followers} WHERE (sender_id = '$sender_id' AND receiver_id = '$receiver_id') OR (sender_id = '$receiver_id' AND receiver_id = '$sender_id')");
                $response['status'] = 'unfollow';
            }else{
                $wpdb->insert($rz_followers, [
                   'sender_id' => $sender_id,
                   'receiver_id' => $receiver_id
                ]);
                $response['status'] = 'follow';
            }
        }

        echo json_encode($response);

    }
    die();
});