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

        $post_author = get_post_field('post_author', $post_id);

        if(!empty($user_id) && !empty($post_id) && !empty($comment_text)){
            $wpdb->insert($rz_discussion_comment_table, [
                    'user_id' => $user_id,
                'post_id' => $post_id,
                'comment_text' => $comment_text
            ]);

            $comment_id = $wpdb->insert_id;

            if($user_id != $post_author){
                $message = getUserNameById($user_id).' comment on your post <strong>'.get_the_title($post_id).'</strong>';
                add_notification(get_post_permalink($post_id), $user_id, $post_author, 'discuss-comment', $comment_id, $message);
            }
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

        $get_post_id = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}rz_discussion_comments WHERE id = '{$comment_id}'");

        $comment_author = $get_post_id->user_id;
        $post_author = get_post_field('post_author', $get_post_id->post_id);
 
        $all_likes = $wpdb->get_row("SELECT * FROM {$rz_discuss_comment_likes} WHERE user_id = '$user_id' AND comment_id = '$comment_id'");
 
        if(!empty($comment_id) && !empty($user_id)){
            if(!empty($all_likes)){
                if($all_likes->like_type == $like_type){

                    $wpdb->delete($wpdb->prefix.'notification', [
                        'sender_id' => $user_id,
                        'receiver_id' => $post_author,
                        'notification_type' => 'discuss-comment-like',
                        'content_id' => $all_likes->id
                    ]);

                    $wpdb->delete($wpdb->prefix.'notification', [
                        'sender_id' => $user_id,
                        'receiver_id' => $comment_author,
                        'notification_type' => 'discuss-comment-like',
                        'content_id' => $all_likes->id
                    ]);

                    $wpdb->delete($rz_discuss_comment_likes, [
                        'comment_id' => $comment_id,
                        'user_id' => $user_id,
                        'like_type' => $like_type
                    ]);


                    $get_up_like = $wpdb->get_results("SELECT * FROM {$rz_discuss_comment_likes} WHERE comment_id = '{$comment_id}' AND like_type = 'up-like'", ARRAY_A);
                    $get_down_like = $wpdb->get_results("SELECT * FROM {$rz_discuss_comment_likes} WHERE comment_id = '{$comment_id}' AND like_type = 'down-like'", ARRAY_A);
                    $count_like = intval($get_up_like) - intval($get_down_like);


                    if($like_type == 'up-like'){
                        $response['up_like'] = false;
                        $response['counter'] = $count_like;
                    }else{
                        $response['down_like'] = false;
                        $response['counter'] = $count_like;
                    }
                }else{
                    $wpdb->update($rz_discuss_comment_likes, [
                        'like_type' => $like_type
                    ], [
                        'comment_id' => $comment_id,
                        'user_id' => $user_id,
                    ]);


                    if($user_id != $post_author){
                        $message = getUserNameById($user_id).' '.$like_type.' on your post comment <strong>'.get_the_title($get_post_id->post_id).'</strong>';
                        $wpdb->update($wpdb->prefix.'notification', [
                            'massage_text' => $message
                        ], [
                            'sender_id' => $user_id,
                            'receiver_id' => $post_author,
                            'notification_type' => 'discuss-comment-like',
                            'content_id' => $all_likes->id
                        ]);
                    }

                    if($user_id != $comment_author){
                        $message = getUserNameById($user_id).' '.$like_type.' on your comment <strong>'.get_the_title($get_post_id->post_id).'</strong>';
                        $wpdb->update($wpdb->prefix.'notification', [
                            'massage_text' => $message
                        ], [
                            'sender_id' => $user_id,
                            'receiver_id' => $comment_author,
                            'notification_type' => 'discuss-comment-like',
                            'content_id' => $all_likes->id
                        ]);
                    }


                    $get_up_like = $wpdb->get_results("SELECT * FROM {$rz_discuss_comment_likes} WHERE comment_id = '{$comment_id}' AND like_type = 'up-like'", ARRAY_A);
                    $get_down_like = $wpdb->get_results("SELECT * FROM {$rz_discuss_comment_likes} WHERE comment_id = '{$comment_id}' AND like_type = 'down-like'", ARRAY_A);
                    $count_like = intval($get_up_like) - intval($get_down_like);


                    if($like_type == 'up-like'){
                        $response['up_like'] = true;
                        $response['counter'] = $count_like;
                    }else{
                        $response['down_like'] = true;
                        $response['counter'] = $count_like;
                    }
                }
            }else{
                $wpdb->insert($rz_discuss_comment_likes, [
                    'comment_id' => $comment_id,
                    'user_id' => $user_id,
                    'like_type' => $like_type
                ]);

                $like_id = $wpdb->insert_id;

                if($user_id != $post_author){
                    $message = getUserNameById($user_id).' '.$like_type.' on your post comment <strong>'.get_the_title($get_post_id->post_id).'</strong>';
                    add_notification(get_post_permalink($get_post_id->post_id), $user_id, $post_author, 'discuss-comment-like', $like_id, $message);
                }

                if($comment_author != $user_id){
                    $message = getUserNameById($user_id).' '.$like_type.' on your comment <strong>'.get_the_title($get_post_id->post_id).'</strong>';
                    add_notification(get_post_permalink($get_post_id->post_id), $user_id, $comment_author, 'discuss-comment-like', $like_id, $message);
                }

                $get_up_like = $wpdb->get_results("SELECT * FROM {$rz_discuss_comment_likes} WHERE comment_id = '{$comment_id}' AND like_type = 'up-like'", ARRAY_A);
                $get_down_like = $wpdb->get_results("SELECT * FROM {$rz_discuss_comment_likes} WHERE comment_id = '{$comment_id}' AND like_type = 'down-like'", ARRAY_A);
                $count_like = intval($get_up_like) - intval($get_down_like);


                if($like_type == 'up-like'){
                    $response['up_like'] = true;
                    $response['counter'] = $count_like;
                }else{
                    $response['down_like'] = true;
                    $response['counter'] = $count_like;
                }
            }
        }
 
        echo json_encode($response);
    }
    die();
 });

/**
 * delete discuss comment
 */
add_action('wp_ajax_rz_delete_discuss_comment', function(){
   global $wpdb;
    $nonce = $_POST['nonce'];
    if(wp_verify_nonce($nonce, 'rz-delete-discuss-comment-nonce')){
        $comment_id = sanitize_key($_POST['comment_id']);
        $user_id = get_current_user_id();

        if(!empty($comment_id) && !empty($user_id)){
            $wpdb->query("DELETE FROM {$wpdb->prefix}rz_discuss_reply_likes WHERE reply_id IN (SELECT id FROM {$wpdb->prefix}rz_discuss_comment_replays WHERE comment_id = '{$comment_id}')");

            $wpdb->delete($wpdb->prefix.'rz_discuss_comment_replays', [
                'comment_id' => $comment_id
            ]);

            $wpdb->delete($wpdb->prefix.'rz_discuss_comment_likes', [
                'comment_id' => $comment_id
            ]);

            $wpdb->delete($wpdb->prefix.'rz_discussion_comments', [
                'id' => $comment_id
            ]);

            exit('done');
        }
    }
   die();
});