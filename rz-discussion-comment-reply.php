<?php

/**
 * add replay on discussion comment
 */
add_action('wp_ajax_rz_add_replY_on_discussion_comment', function(){
    global $wpdb;
    $nonce = $_POST['nonce'];
    if(wp_verify_nonce($nonce, 'rz-add-replay-on-discussion-comment')){
        $comment_id = sanitize_key($_POST['comment_id']);
        $replay_text = sanitize_text_field($_POST['replay_text']);
        $user_id = get_current_user_id();
        $rz_discuss_comment_replays = $wpdb->prefix.'rz_discuss_comment_replays';

        if(!empty($comment_id) && !empty($replay_text) && !empty($user_id)){
            $wpdb->insert($rz_discuss_comment_replays, [
                    'user_id' => $user_id,
                'comment_id' => $comment_id,
                'replay_text' => $replay_text
            ]);
        }
    }
    die();
});

/**
 * add or remove like on reply
 */
add_action('wp_ajax_rz_add_or_remove_like_on_reply', function(){
    global $wpdb;
    $nonce = $_POST['nonce'];
    if(wp_verify_nonce($nonce, 'rz-add-like-on-reply')){
        $reply_id = sanitize_key($_POST['reply_id']);
        $user_id = get_current_user_id();
        $reply_type = sanitize_text_field($_POST['reply_type']);
        $rz_discuss_reply_likes = $wpdb->prefix.'rz_discuss_reply_likes';

        $get_all_replays = $wpdb->get_results("SELECT * FROM {$rz_discuss_reply_likes} WHERE user_id = '$user_id' AND reply_id = '$reply_id'", ARRAY_A);

        if(!empty($reply_id) && !empty($user_id) && !empty($reply_type)){
            if(count($get_all_replays) > 0){
                $wpdb->delete($rz_discuss_reply_likes, [
                    'reply_id' => $reply_id,
                    'user_id' => $user_id,
                    'reply_type' => $reply_type
                ]);
                $response['data_res'] = false;
            }else {
                $wpdb->insert($rz_discuss_reply_likes, [
                    'reply_id' => $reply_id,
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