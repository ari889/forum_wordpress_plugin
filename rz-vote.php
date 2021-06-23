<?php

/**
 * add vote
 */
add_action('wp_ajax_imit_add_vote', function(){
    global $wpdb;
    $nonce = $_POST['nonce'];
    if(wp_verify_nonce( $nonce, 'rz-add-vote-nonce' )){
        $answer_id = sanitize_key( $_POST['answer_id'] );
        $user_id = sanitize_key(get_current_user_id(  ));
        $vote_type = sanitize_text_field($_POST['vote_type']);
        $rz_vote = $wpdb->prefix.'rz_vote';
        $rz_point_table = $wpdb->prefix.'rz_point_table';
        $rz_user_profile_data = $wpdb->prefix.'rz_user_profile_data';
        $all_votes = $wpdb->get_results("SELECT * FROM {$rz_vote} WHERE answer_id = '$answer_id' AND user_id = '$user_id'", ARRAY_A);

        $get_author_using_answer_id = $wpdb->get_row("SELECT user_id FROM {$wpdb->prefix}rz_answers WHERE id = '$answer_id'");
        
        if(!empty($answer_id) && !empty($user_id)){
            if(count($all_votes) > 0){
                $vote_id = $wpdb->get_row("SELECT id FROM {$rz_vote} WHERE answer_id = '$answer_id' AND user_id = '$user_id'");
                $get_point = $wpdb->get_row("SELECT * FROM {$rz_user_profile_data} WHERE user_id = '$get_author_using_answer_id->user_id'");

                $user_point = $get_point->points;
                $content_id = $vote_id->id;
                if(!empty($get_point) && $get_author_using_answer_id->user_id !== $user_id){
                    $wpdb->update($rz_user_profile_data, [
                        'points' => ($user_point - 10),
                    ], ['user_id' => $get_author_using_answer_id->user_id]);

                    $wpdb->delete($rz_point_table, [
                        'content_id' => $content_id,
                        'point_type' => 'up-vote'
                    ]);
                }
                $wpdb->delete($rz_vote, [
                    'answer_id' => $answer_id,
                    'user_id' => $user_id,
                    'vote_type' => $vote_type
                ]);
                $response['data_res'] = false;
            }else{
                $wpdb->insert($rz_vote, [
                    'answer_id' => $answer_id,
                    'user_id' => $user_id,
                    'vote_type' => $vote_type
                ]);
                $vote_id = $wpdb->insert_id;
                if($vote_type == 'up-vote' && $get_author_using_answer_id->user_id !== $user_id){
                    $wpdb->insert($rz_point_table, [
                        'user_id' => $user_id,
                        'content_id' => $vote_id,
                        'point_type' => 'up-vote',
                        'point_earn' => 10
                    ]);

                    $get_author = $get_author_using_answer_id->user_id;

                    $get_point = $wpdb->get_row("SELECT * FROM {$rz_user_profile_data} WHERE user_id = '$get_author'");


                    $user_point = $get_point->points;

                    if(empty($get_point)){
                        $wpdb->insert($rz_user_profile_data, [
                            'points' => 10,
                            'user_id' => $get_author_using_answer_id->user_id
                        ]);
                    }else{
                        $wpdb->update($rz_user_profile_data, [
                            'points' => ($user_point+10),
                        ], ['user_id' => $get_author_using_answer_id->user_id]);
                    }
                }
                $response['data_res'] = true;
            }
        }

        echo json_encode($response);
    }
    die();
});
