<?php

/**
 * add comment
 */
add_action('wp_ajax_imit_add_comment_on_discussion', function(){
    global $wpdb;
    $nonce = $_POST['nonce'];
    if(wp_verify_nonce($nonce, 'rz-add-comment-on-discussion')){
        $comment_text = sanitize_text_field($_POST['comment_text']);
        $post_id = sanitize_key($_POST['post_id']);
        $user_id = get_current_user_id();
        $rz_discussion_comment_table = $wpdb->prefix.'rz_discussion_comments';

        if(!empty($user_id) && !empty($post_id) && !empty($comment_text)){
            $wpdb->insert($rz_discussion_comment_table, [
                    'user_id' => $user_id,
                'post_id' => $post_id,
                'comment_text' => $comment_text
            ]);
        }
    }
    die();
});


/**
 * add like to discuss comment
 */
add_action('wp_ajax_add_like_to_discuss_comment', function(){
    global $wpdb;
    $nonce = $_POST['nonce'];
    if(wp_verify_nonce($nonce, 'rz-like-discussion-comment')){
        $comment_id = sanitize_key($_POST['comment_id']);
        $user_id = get_current_user_id();
        $like_type = sanitize_text_field($_POST['like_type']);
        $rz_discuss_comment_likes = $wpdb->prefix.'rz_discuss_comment_likes';
 
        $all_likes = $wpdb->get_results("SELECT * FROM {$rz_discuss_comment_likes} WHERE user_id = '$user_id' AND comment_id = '$comment_id'", ARRAY_A);
 
        if(!empty($comment_id) && !empty($user_id)){
            if(count($all_likes) > 0){
                $wpdb->delete($rz_discuss_comment_likes, [
                    'comment_id' => $comment_id,
                    'user_id' => $user_id,
                    'like_type' => $like_type
                ]);
                $response['data_res'] = false;
            }else{
                $wpdb->insert($rz_discuss_comment_likes, [
                    'comment_id' => $comment_id,
                    'user_id' => $user_id,
                    'like_type' => $like_type
                ]);
                $response['data_res'] = true;
            }
        }
 
        echo json_encode($response);
    }
    die();
 });