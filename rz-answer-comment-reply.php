<?php 


/**
 * add replay
 */
add_action('wp_ajax_rz_add_replay', function(){
    global $wpdb;
    $nonce = $_POST['nonce'];
    if(wp_verify_nonce( $nonce, 'rz-add-comment-replay' )){
        $replay_text = sanitize_text_field($_POST['replay_text']);
        $comment_id = sanitize_key($_POST['comment_id']);
        $user_id = get_current_user_id();
        $rz_comment_replays = $wpdb->prefix.'rz_comment_replays';

        if(!empty($replay_text) || !empty($comment_id) || !empty($user_id)){
            $wpdb->insert($rz_comment_replays, [
                'user_id' => $user_id,
                'comment_id' => $comment_id,
                'replay_text' => $replay_text
            ]);
        }
    }
    die();
});

/**
 * add replay like
 */
add_action('wp_ajax_rz_add_replay_like', function(){
    global $wpdb;
    $nonce = $_POST['nonce'];
    if(wp_verify_nonce($nonce, 'rz-add-replay-like')){
        $replay_id = sanitize_key( $_POST['replay_id'] );
        $user_id = get_current_user_id(  );
        $reply_type = sanitize_text_field( $_POST['reply_type'] );
        $rz_comment_replys_likes = $wpdb->prefix.'rz_comment_reply_likes';

        $get_all_likes = $wpdb->get_results("SELECT * FROM {$rz_comment_replys_likes} WHERE reply_id = '$replay_id' AND user_id = '$user_id'", ARRAY_A);

        if(!empty($replay_id) || !empty($user_id)){
            if(count($get_all_likes) > 0){
                $wpdb->delete($rz_comment_replys_likes, [
                    'reply_id' => $replay_id,
                    'user_id' => $user_id,
                    'reply_type' => $reply_type
                ]);
                $response['data_res'] = false;
            }else{
                $wpdb->insert($rz_comment_replys_likes, [
                    'reply_id' => $replay_id,
                    'user_id' => $user_id,
                    'reply_type' => $reply_type
                ]);
                $response['data_res'] = true;
            }
        }

        echo json_encode($response);
    }
    die();
});