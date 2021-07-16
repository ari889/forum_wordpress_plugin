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

        $get_post = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}rz_discuss_comment_replays INNER JOIN {$wpdb->prefix}rz_discussion_comments ON {$wpdb->prefix}rz_discussion_comments.id = {$wpdb->prefix}rz_discuss_comment_replays.comment_id WHERE {$wpdb->prefix}rz_discuss_comment_replays.comment_id = '{$comment_id}'");

        $post_id = $get_post->post_id;

        $post_author = get_post_field('post_author', $post_id);

        $get_comment = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}rz_discussion_comments WHERE id = '{$comment_id}'");

        $comment_author = $get_comment->user_id;

        if(!empty($comment_id) && !empty($replay_text) && !empty($user_id)){
            $wpdb->insert($rz_discuss_comment_replays, [
                    'user_id' => $user_id,
                'comment_id' => $comment_id,
                'replay_text' => $replay_text
            ]);

            $reply_id = $wpdb->insert_id;

            if($post_author != $user_id){
                $message1 = getUserNameById($user_id).' reply on your post comment <strong>'.get_the_title($post_id).'</strong>';
                add_notification(get_post_permalink($post_id), $user_id, $post_author, 'discuss-comment-reply', $reply_id, $message1);
            }

            if($comment_author != $user_id){
                $message2 = getUserNameById($user_id).' reply on your comment <strong>'.get_the_title($post_id).'</strong>';
                add_notification(get_post_permalink($post_id), $user_id, $comment_author, 'discuss-comment-reply', $reply_id, $message2);
            }
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

        /**
         * is user already liked this post
         */
        $get_all_replays = $wpdb->get_row("SELECT * FROM {$rz_discuss_reply_likes} WHERE user_id = '$user_id' AND reply_id = '$reply_id'");


        /**
         * get post_id using replay id
         */
        $get_post_author = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}rz_discuss_comment_replays INNER JOIN {$wpdb->prefix}rz_discussion_comments ON {$wpdb->prefix}rz_discussion_comments.id = {$wpdb->prefix}rz_discuss_comment_replays.comment_id WHERE {$wpdb->prefix}rz_discuss_comment_replays.id = '{$reply_id}'");


        /**
         * get post_id
         */
        $post_id = $get_post_author->post_id;


        /**
         * get post author by id
         */
        $post_author = get_post_field('post_author', $post_id);


        /**
         * get reply all data
         */
        $replay_data = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}rz_discuss_comment_replays WHERE id = '{$reply_id}'");


        /**
         * get reply author
         */
        $reply_author = $replay_data->user_id;


        if(!empty($reply_id) && !empty($user_id) && !empty($reply_type)){
            if(!empty($get_all_replays)){
                if($get_all_replays->reply_type == $reply_type){

                    $wpdb->delete($wpdb->prefix.'notification', [
                        'sender_id' => $user_id,
                        'receiver_id' => $reply_author,
                        'notification_type' => 'discuss-reply-like',
                        'content_id' => $get_all_replays->id
                    ]);

                    $wpdb->delete($wpdb->prefix.'notification', [
                        'sender_id' => $user_id,
                        'receiver_id' => $post_author,
                        'notification_type' => 'discuss-reply-like',
                        'content_id' => $get_all_replays->id
                    ]);

                    $wpdb->delete($rz_discuss_reply_likes, [
                        'reply_id' => $reply_id,
                        'user_id' => $user_id,
                        'reply_type' => $reply_type
                    ]);


                    $get_up_reply = $wpdb->get_results("SELECT * FROM {$rz_discuss_reply_likes} WHERE reply_id = '{$reply_id}' AND reply_type = 'up-reply'", ARRAY_A);
                    $get_down_reply= $wpdb->get_results("SELECT * FROM {$rz_discuss_reply_likes} WHERE reply_id = '{$reply_id}' AND reply_type = 'down-reply'", ARRAY_A);
                    $count_reply = intval($get_up_reply) - intval($get_down_reply);

                    if($reply_type == 'up-reply'){
                        $response['up_reply'] = false;
                        $response['counter'] = $count_reply;
                    }else{
                        $response['down_reply'] = false;
                        $response['counter'] = $count_reply;
                    }
                }else{
                    $wpdb->update($rz_discuss_reply_likes, [
                        'reply_type' => $reply_type
                    ],[
                        'id' => $get_all_replays->id
                    ]);

                    if($user_id != $post_author){
                        $message1 = getUserNameById($user_id).' '.$reply_type.' on your post reply <strong>'.get_the_title($post_id).'</strong>';
                        $wpdb->update($wpdb->prefix.'notification', [
                            'massage_text' => $message1
                        ], [
                            'sender_id' => $user_id,
                            'receiver_id' => $post_author,
                            'notification_type' => 'discuss-reply-like',
                            'content_id' => $get_all_replays->id
                        ]);
                    }

                    if($user_id != $reply_author){
                        $message2 = getUserNameById($user_id).' '.$reply_type.' on your reply <strong>'.get_the_title($post_id).'</strong>';
                        $wpdb->update($wpdb->prefix.'notification', [
                            'massage_text' => $message2
                        ], [
                            'sender_id' => $user_id,
                            'receiver_id' => $reply_author,
                            'notification_type' => 'discuss-reply-like',
                            'content_id' => $get_all_replays->id
                        ]);
                    }

                    $get_up_reply = $wpdb->get_results("SELECT * FROM {$rz_discuss_reply_likes} WHERE reply_id = '{$reply_id}' AND reply_type = 'up-reply'", ARRAY_A);
                    $get_down_reply= $wpdb->get_results("SELECT * FROM {$rz_discuss_reply_likes} WHERE reply_id = '{$reply_id}' AND reply_type = 'down-reply'", ARRAY_A);
                    $count_reply = intval($get_up_reply) - intval($get_down_reply);

                    if($reply_type == 'up-reply'){
                        $response['up_reply'] = true;
                        $response['counter'] = $count_reply;
                    }else{
                        $response['down_reply'] = true;
                        $response['counter'] = $count_reply;
                    }
                }
            }else {
                $wpdb->insert($rz_discuss_reply_likes, [
                    'reply_id' => $reply_id,
                    'user_id' => $user_id,
                    'reply_type' => $reply_type
                ]);

                $like_id = $wpdb->insert_id;

                if($post_author != $user_id){
                    $message1 = getUserNameById($user_id).' '.$reply_type.' on your post reply <strong>'.get_the_title($post_id).'</strong>';
                    add_notification(get_post_permalink($post_id), $user_id, $post_author, 'discuss-reply-like', $like_id, $message1);
                }

                if($reply_author != $user_id){
                    $message2 = getUserNameById($user_id).' '.$reply_type.' on your reply <strong>'.get_the_title($post_id).'</strong>';
                    add_notification(get_post_permalink($post_id), $user_id, $reply_author, 'discuss-reply-like', $like_id, $message2);
                }

                $get_up_reply = $wpdb->get_results("SELECT * FROM {$rz_discuss_reply_likes} WHERE reply_id = '{$reply_id}' AND reply_type = 'up-reply'", ARRAY_A);
                $get_down_reply= $wpdb->get_results("SELECT * FROM {$rz_discuss_reply_likes} WHERE reply_id = '{$reply_id}' AND reply_type = 'down-reply'", ARRAY_A);
                $count_reply = intval($get_up_reply) - intval($get_down_reply);

                if($reply_type == 'up-reply'){
                    $response['up_reply'] = true;
                    $response['counter'] = $count_reply;
                }else{
                    $response['down_reply'] = true;
                    $response['counter'] = $count_reply;
                }
            }
        }
        echo json_encode($response);
    }
    die();
});

/**
 * delete reply
 */
add_action('wp_ajax_rz_delete_discuss_reply', function(){
    global $wpdb;
    $nonce = $_POST['nonce'];
    if(wp_verify_nonce($nonce, 'rz-delete-discuss-reply-nonce')){
        $reply_id = sanitize_key($_POST['reply_id']);
        $user_id = get_current_user_id();

        if(!empty($reply_id) && !empty($user_id)){
            $wpdb->delete($wpdb->prefix.'rz_discuss_reply_likes', [
                'reply_id' => $reply_id
            ]);

            $wpdb->delete($wpdb->prefix.'rz_discuss_comment_replays', [
                'id' => $reply_id
            ]);

            exit('done');
        }
    }
    die();
});