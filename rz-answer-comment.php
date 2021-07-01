<?php

/**
 * add answer on comment
 */
add_action('wp_ajax_imit_add_answer_on_comment', function(){
    global $wpdb;
    $nonce = $_POST['nonce'];
    if(wp_verify_nonce($nonce, 'rz-add-comment-on-answer')){
        $answer_id = sanitize_key( $_POST['answer_id'] );
        $comment_text = sanitize_text_field( $_POST['answer_comment_text'] );
        $user_id = get_current_user_id(  );
        $rz_answer_comment = $wpdb->prefix.'rz_answer_comments';

        $get_post_id = $wpdb->get_row("SELECT post_id FROM {$wpdb->prefix}rz_answer_comments INNER JOIN {$wpdb->prefix}rz_answers ON {$wpdb->prefix}rz_answers.id = {$wpdb->prefix}rz_answer_comments.answer_id WHERE answer_id = '{$answer_id}'");

        $post_author_id = get_post_field('post_author', $get_post_id->post_id);

        if(!empty($answer_id) && !empty($comment_text)){
            $wpdb->insert($rz_answer_comment, [
                'user_id' => $user_id,
                'answer_id' => $answer_id,
                'comment_text' => $comment_text
            ]);

            $comment_id = $wpdb->insert_id;


            $get_answer = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}rz_answers WHERE id = '{$answer_id}'");
            if($post_author_id != $user_id){
                $message1 = getUserNameById($user_id).' comment on your post answer '.$get_answer->answer_text;
                add_notification(get_post_permalink($get_post_id -> post_id), $user_id, $post_author_id, 'answer-comment', $comment_id, $message1);
            }

            if($get_answer->user_id != $user_id){
                $message2 = getUserNameById($user_id).' comment on your answer '.$get_answer->answer_text;
                add_notification(get_post_permalink($get_post_id -> post_id), $user_id, $get_answer->user_id, 'answer-comment', $comment_id, $message2);
            }
        }

    }
    die(); 
});

/**
 * add comment up vote
 */
add_action('wp_ajax_imit_add_comment_up_vote', function(){
    global $wpdb;
    $nonce = $_POST['nonce'];
    if(wp_verify_nonce( $nonce, 'rz-add-comment-up-vote' )){
        $comment_id = sanitize_key($_POST['comment_id']);
        $user_id = get_current_user_id();
        $vote_type = sanitize_text_field( $_POST['vote_type'] );
        $rz_answer_comment_vote = $wpdb->prefix.'rz_answer_comment_votes';
        $is_user_voted = $wpdb->get_row("SELECT * FROM {$rz_answer_comment_vote} WHERE user_id = '$user_id' AND comment_id = '$comment_id'");

        if(!empty($comment_id) && !empty($user_id)){
            if(!empty($is_user_voted)){
                if($vote_type == $is_user_voted->vote_type){


                    $get_post_id = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}rz_answer_comment_votes INNER JOIN {$wpdb->prefix}rz_answer_comments ON {$wpdb->prefix}rz_answer_comment_votes.comment_id = {$wpdb->prefix}rz_answer_comments.id INNER JOIN {$wpdb->prefix}rz_answers ON {$wpdb->prefix}rz_answers.id = {$wpdb->prefix}rz_answer_comments.answer_id WHERE {$wpdb->prefix}rz_answer_comment_votes.comment_id = '{$comment_id}' AND wp_rz_answer_comment_votes.user_id = '{$user_id}'");
                

                    $get_comment_user_id = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}rz_answer_comments WHERE id = '{$comment_id}'");
                    $post_author = get_post_field('post_author', $get_post_id->post_id);
                    $comment_author = $get_comment_user_id->user_id;
                    $wpdb->delete($wpdb->prefix.'notification', [
                        'sender_id' => $user_id,
                        'receiver_id' => $post_author,
                        'notification_type' => 'answer-comment-vote',
                        'content_id' => $is_user_voted->id
                    ]);


                    $wpdb->update($wpdb->prefix.'notification', [
                        'massage_text' => $message2
                    ],[
                        'sender_id' => $user_id,
                        'receiver_id' => $comment_author,
                        'notification_type' => 'answer-comment-vote',
                        'content_id' => $is_user_voted->id
                    ]);

                    $wpdb->delete($rz_answer_comment_vote, [
                        'user_id' => $user_id,
                        'comment_id' => $comment_id,
                    ]);
                    
                    
                    $get_upvote = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_answer_comment_votes WHERE comment_id='$comment_id' AND vote_type='up-vote'", ARRAY_A);
                    $get_downvote = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_answer_comment_votes WHERE comment_id='$comment_id' AND vote_type='down-vote'", ARRAY_A);
                    $count_vote = (intval(count($get_upvote)) - intval(count($get_downvote)));


                    if($vote_type == 'up-vote'){
                        $response['up_vote'] = false;
                        $response['counter'] = $count_vote;
                    }else{
                        $response['down_vote'] = false;
                        $response['counter'] = $count_vote;
                    }
                }else{                    
                    $wpdb->update($rz_answer_comment_vote, [
                        'vote_type' => $vote_type
                    ], ['id' => $is_user_voted->id]);


                    $get_upvote = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_answer_comment_votes WHERE comment_id='$comment_id' AND vote_type='up-vote'", ARRAY_A);
                    $get_downvote = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_answer_comment_votes WHERE comment_id='$comment_id' AND vote_type='down-vote'", ARRAY_A);
                    $count_vote = (intval(count($get_upvote)) - intval(count($get_downvote)));


                    $get_post_id = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}rz_answer_comment_votes INNER JOIN {$wpdb->prefix}rz_answer_comments ON {$wpdb->prefix}rz_answer_comment_votes.comment_id = {$wpdb->prefix}rz_answer_comments.id INNER JOIN {$wpdb->prefix}rz_answers ON {$wpdb->prefix}rz_answers.id = {$wpdb->prefix}rz_answer_comments.answer_id WHERE {$wpdb->prefix}rz_answer_comment_votes.comment_id = '{$comment_id}' AND wp_rz_answer_comment_votes.user_id = '{$user_id}'");
                

                    $get_comment_user_id = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}rz_answer_comments WHERE id = '{$comment_id}'");
                    $post_author = get_post_field('post_author', $get_post_id->post_id);
                    $comment_author = $get_comment_user_id->user_id;

                    if($post_author != $user_id){
                        $message1 = getUserNameById($user_id).' '.$vote_type.' on your post comment '.$get_comment_user_id->comment_text;
                        $wpdb->update($wpdb->prefix.'notification', [
                            'massage_text' => $message1
                        ],[
                            'sender_id' => $user_id,
                            'receiver_id' => $post_author,
                            'notification_type' => 'answer-comment-vote',
                            'content_id' => $is_user_voted->id
                        ]);
                    }

                    if($comment_author != $user_id){
                        $message2 = getUserNameById($user_id).' '.$vote_type.' on your comment '.$get_comment_user_id->comment_text;
                        $wpdb->update($wpdb->prefix.'notification', [
                            'massage_text' => $message2
                        ],[
                            'sender_id' => $user_id,
                            'receiver_id' => $comment_author,
                            'notification_type' => 'answer-comment-vote',
                            'content_id' => $is_user_voted->id
                        ]);
                    }

                    if($vote_type == 'up-vote'){
                        $response['up_vote'] = true;
                        $response['counter'] = $count_vote;
                    }else{
                        $response['down_vote'] = true;
                        $response['counter'] = $count_vote;
                    }
                }
            }else{
                $wpdb->insert($rz_answer_comment_vote, [
                    'user_id' => $user_id,
                    'comment_id' => $comment_id,
                    'vote_type' => $vote_type
                ]);

                $vote_id = $wpdb->insert_id;

                $get_post_id = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}rz_answer_comment_votes INNER JOIN {$wpdb->prefix}rz_answer_comments ON {$wpdb->prefix}rz_answer_comment_votes.comment_id = {$wpdb->prefix}rz_answer_comments.id INNER JOIN {$wpdb->prefix}rz_answers ON {$wpdb->prefix}rz_answers.id = {$wpdb->prefix}rz_answer_comments.answer_id WHERE {$wpdb->prefix}rz_answer_comment_votes.comment_id = '{$comment_id}' AND wp_rz_answer_comment_votes.user_id = '{$user_id}'");
                

                $get_comment_user_id = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}rz_answer_comments WHERE id = '{$comment_id}'");
                $post_author = get_post_field('post_author', $get_post_id->post_id);
                $comment_author = $get_comment_user_id->user_id;

                if($post_author != $user_id){
                    $message1 = getUserNameById($user_id).' '.$vote_type.' on your post comment '.$get_comment_user_id->comment_text;
                    add_notification(get_post_permalink($get_post_id -> post_id), $user_id, $post_author, 'answer-comment-vote', $vote_id, $message1);
                }

                if($comment_author != $user_id){
                    $message2 = getUserNameById($user_id).' '.$vote_type.' on your comment '.$get_comment_user_id->comment_text;
                    add_notification(get_post_permalink($get_post_id -> post_id), $user_id, $comment_author, 'answer-comment-vote', $vote_id, $message2);
                }


                $get_upvote = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_answer_comment_votes WHERE comment_id='$comment_id' AND vote_type='up-vote'", ARRAY_A);
                $get_downvote = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_answer_comment_votes WHERE comment_id='$comment_id' AND vote_type='down-vote'", ARRAY_A);
                $count_vote = (intval(count($get_upvote)) - intval(count($get_downvote)));
                if($vote_type == 'up-vote'){
                    $response['up_vote'] = true;
                    $response['counter'] = $count_vote;
                }else{
                    $response['down_vote'] = true;
                    $response['counter'] = $count_vote;
                }
            }
        }

        echo json_encode($response);
    }
    die();
});


/**
 * delete comment
 */


 add_action('wp_ajax_rz_delete_comment', function(){
     global $wpdb;
    $nonce = $_POST['nonce'];
    if(wp_verify_nonce( $nonce, 'rz-delete-comment-nonce' )){
        $comment_id = sanitize_key( $_POST['comment_id'] );
        $user_id = get_current_user_id();

        if(!empty($comment_id) && !empty($user_id)){
            $wpdb->query("DELETE FROM {$wpdb->prefix}rz_comment_reply_likes WHERE reply_id IN (SELECT id FROM {$wpdb->prefix}rz_comment_replays WHERE comment_id = '{$comment_id}')");

            $wpdb->delete($wpdb->prefix.'rz_comment_replays', [
                'comment_id' => $comment_id
            ]);

            $wpdb->delete($wpdb->prefix.'rz_answer_comments', [
                'id' => $comment_id,
                'user_id' => $user_id
            ]);

            exit('done');
        }
    }
     die();
 });