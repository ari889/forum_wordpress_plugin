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


        $get_post_id = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}rz_answer_comments INNER JOIN {$wpdb->prefix}rz_answers ON {$wpdb->prefix}rz_answers.id = {$wpdb->prefix}rz_answer_comments.answer_id WHERE {$wpdb->prefix}rz_answer_comments.id = '{$comment_id}'");

        $post_id = $get_post_id->post_id;

        $post_author_id = get_post_field('post_author', $post_id);

        $comment_user_id = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}rz_answer_comments WHERE id = '{$comment_id}'");

        $comment_user = $comment_user_id->user_id;

        if(!empty($replay_text) || !empty($comment_id) || !empty($user_id)){
            $wpdb->insert($rz_comment_replays, [
                'user_id' => $user_id,
                'comment_id' => $comment_id,
                'replay_text' => $replay_text
            ]);


            $replay_id = $wpdb->insert_id;


            if($post_author_id != $user_id){
                $message1 = getUserNameById($user_id).' reply on your post comment '.$comment_user_id->comment_text;
                add_notification(get_post_permalink($get_post_id -> post_id), $user_id, $post_author_id, 'answer-comment-replay', $replay_id, $message1);
            }
    
            if($comment_user != $user_id){
                $message2 = getUserNameById($user_id).' reply on your comment '.$comment_user_id->comment_text;
                add_notification(get_post_permalink($get_post_id -> post_id), $user_id, $comment_user, 'answer-comment-replay', $replay_id, $message2);
            }
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

        $get_all_likes = $wpdb->get_row("SELECT * FROM {$rz_comment_replys_likes} WHERE reply_id = '$replay_id' AND user_id = '$user_id'", ARRAY_A);

        $get_post_id = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}rz_comment_replays INNER JOIN {$wpdb->prefix}rz_answer_comments ON {$wpdb->prefix}rz_answer_comments.id = {$wpdb->prefix}rz_comment_replays.comment_id INNER JOIN {$wpdb->prefix}rz_answers ON {$wpdb->prefix}rz_answers.id = {$wpdb->prefix}rz_answer_comments.answer_id WHERE {$wpdb->prefix}rz_comment_replays.id = '{$replay_id}'");

        $post_id = $get_post_id->post_id;

        $post_author = get_post_field('post_author', $post_id);

        $get_replay_author = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}rz_comment_replays WHERE id = '{$replay_id}'");


        $replay_author = $get_replay_author->user_id;

        if(!empty($replay_id) || !empty($user_id)){
            if(!empty($get_all_likes)){
                if($reply_type == $get_all_likes['reply_type']){

                    $wpdb->delete($wpdb->prefix.'notification', [
                        'sender_id' => $user_id,
                        'receiver_id' => $post_author,
                        'notification_type' => 'answer-replay-like',
                        'content_id' => $get_all_likes['id']
                    ]);


                    $wpdb->delete($wpdb->prefix.'notification', [
                        'sender_id' => $user_id,
                        'receiver_id' => $replay_author,
                        'notification_type' => 'answer-replay-like',
                        'content_id' => $get_all_likes['id']
                    ]);

                    $wpdb->delete($rz_comment_replys_likes, [
                        'reply_id' => $replay_id,
                        'user_id' => $user_id,
                        'reply_type' => $reply_type
                    ]);

                    $get_upvote = $wpdb->get_results("SELECT * FROM {$rz_comment_replys_likes} WHERE reply_id='$replay_id' AND reply_type='up-reply'", ARRAY_A);
                    $get_downvote = $wpdb->get_results("SELECT * FROM {$rz_comment_replys_likes} WHERE reply_id='$replay_id' AND reply_type='down-reply'", ARRAY_A);
                    $count_vote = (intval(count($get_upvote)) - intval(count($get_downvote)));

                    if($reply_type == 'up-reply'){
                        $response['up_reply'] = false;
                        $response['counter'] = $count_vote;
                    }else{
                        $response['down_reply'] = false;
                        $response['counter'] = $count_vote;
                    }
                }else{
                    $wpdb->update($rz_comment_replys_likes, [
                        'reply_type' => $reply_type
                    ], ['id' => $get_all_likes['id']]);

                    $get_upvote = $wpdb->get_results("SELECT * FROM {$rz_comment_replys_likes} WHERE reply_id='$replay_id' AND reply_type='up-reply'", ARRAY_A);
                    $get_downvote = $wpdb->get_results("SELECT * FROM {$rz_comment_replys_likes} WHERE reply_id='$replay_id' AND reply_type='down-reply'", ARRAY_A);
                    $count_vote = (intval(count($get_upvote)) - intval(count($get_downvote)));

                    if($post_author != $user_id){
                        $message1 = getUserNameById($user_id).' '.$reply_type.' on your post replay '.$get_replay_author->replay_text;
                        $wpdb->update($wpdb->prefix.'notification', [
                            'massage_text' => $message1
                        ],[
                            'sender_id' => $user_id,
                            'receiver_id' => $post_author,
                            'notification_type' => 'answer-replay-like',
                            'content_id' => $get_all_likes['id']
                        ]);
                    }

                    if($replay_author != $user_id){
                        $message2 = getUserNameById($user_id).' '.$reply_type.' on your replay '.$get_replay_author->replay_text;
                        $wpdb->update($wpdb->prefix.'notification', [
                            'massage_text' => $message2
                        ],[
                            'sender_id' => $user_id,
                            'receiver_id' => $replay_author,
                            'notification_type' => 'answer-replay-like',
                            'content_id' => $get_all_likes['id']
                        ]);
                    }

                    if($reply_type == 'up-reply'){
                        $response['up_reply'] = true;
                        $response['counter'] = $count_vote;
                    }else{
                        $response['down_reply'] = true;
                        $response['counter'] = $count_vote;
                    }
                }
            }else{
                $wpdb->insert($rz_comment_replys_likes, [
                    'reply_id' => $replay_id,
                    'user_id' => $user_id,
                    'reply_type' => $reply_type
                ]);

                $replay_like_id = $wpdb->insert_id;


                $get_upvote = $wpdb->get_results("SELECT * FROM {$rz_comment_replys_likes} WHERE reply_id='$replay_id' AND reply_type='up-reply'", ARRAY_A);
                $get_downvote = $wpdb->get_results("SELECT * FROM {$rz_comment_replys_likes} WHERE reply_id='$replay_id' AND reply_type='down-reply'", ARRAY_A);
                $count_vote = (intval(count($get_upvote)) - intval(count($get_downvote)));

                if($post_author != $user_id){
                    $message1 = getUserNameById($user_id).' '.$reply_type.' on your post replay '.$get_replay_author->replay_text;
                    add_notification(get_post_permalink($post_id), $user_id, $post_author, 'answer-replay-like', $replay_like_id, $message1);
                }

                if($replay_author != $user_id){
                    $message2 = getUserNameById($user_id).' '.$reply_type.' on your replay '.$get_replay_author->replay_text;
                    add_notification(get_post_permalink($post_id), $user_id, $replay_author, 'answer-replay-like', $replay_like_id, $message2);
                }


                if($reply_type == 'up-reply'){
                    $response['up_reply'] = true;
                    $response['counter'] = $count_vote;
                }else{
                    $response['down_reply'] = true;
                    $response['counter'] = $count_vote;
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
add_action('wp_ajax_rz_delete_replay', function(){
    global $wpdb;
    $nonce = $_POST['nonce'];
    if(wp_verify_nonce( $nonce, 'rz-delete-reply-nonce' )){
        $reply_id = sanitize_key( $_POST['reply_id'] );
        $user_id = get_current_user_id(  );

        if(!empty($reply_id) && !empty($user_id)){
            $wpdb->delete($wpdb->prefix.'rz_comment_reply_likes', [
                'reply_id' => $reply_id
            ]);
            $wpdb->delete($wpdb->prefix.'rz_comment_replays', [
                'id' => $reply_id,
                'user_id' => $user_id
            ]);

            exit('done');
        }
    }
    die();
});