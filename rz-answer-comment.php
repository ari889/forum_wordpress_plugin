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
        if(!empty($answer_id) && !empty($comment_text)){
            $wpdb->insert($rz_answer_comment, [
                'user_id' => $user_id,
                'answer_id' => $answer_id,
                'comment_text' => $comment_text
            ]);
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
        $is_user_voted = $wpdb->get_results("SELECT * FROM {$rz_answer_comment_vote} WHERE user_id = '$user_id' AND comment_id = '$comment_id'", ARRAY_A);

        if(!empty($comment_id) && !empty($user_id)){
            if(count($is_user_voted) > 0){
                $wpdb->delete($rz_answer_comment_vote, [
                    'user_id' => $user_id,
                    'comment_id' => $comment_id,
                    'vote_type' => $vote_type
                ]);
                $response['data_res'] = false;
            }else{
                $wpdb->insert($rz_answer_comment_vote, [
                    'user_id' => $user_id,
                    'comment_id' => $comment_id,
                    'vote_type' => $vote_type
                ]);
                $response['data_res'] = true;
            }
        }

        echo json_encode($response);
    }
    die();
});