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
        $all_votes = $wpdb->get_row("SELECT * FROM {$rz_vote} WHERE answer_id = '$answer_id' AND user_id = '$user_id'", ARRAY_A);

        $get_author_using_answer_id = $wpdb->get_row("SELECT user_id FROM {$wpdb->prefix}rz_answers WHERE id = '$answer_id'");
        
        if(!empty($answer_id) && !empty($user_id)){
            if(!empty($all_votes)){
                $get_type= $all_votes['vote_type'];
                if($vote_type == $get_type){
                    if($vote_type == 'up-vote'){
                        $vote_id = $all_votes['id'];
                        $is_user_earned_point = $wpdb->get_row("SELECT * FROM {$rz_point_table} WHERE user_id = '{$user_id}' AND content_id = '{$vote_id}' AND point_type = 'up-vote'");
                        if(!empty($is_user_earned_point)){
                            $wpdb->delete($rz_point_table, [
                                'id' => $is_user_earned_point['id'],
                            ]);
                        }
                        $get_author = $get_author_using_answer_id->user_id;
        
                        $get_point = $wpdb->get_row("SELECT * FROM {$rz_user_profile_data} WHERE user_id = '$get_author'");
    
    
                        $user_point = $get_point->points;
                        $wpdb->update($rz_user_profile_data, [
                            'points' => ($user_point-10),
                        ], ['user_id' => $get_author_using_answer_id->user_id]);
                    }
                    $wpdb->delete($wpdb->prefix.'notification', [
                        'notification_type' => 'vote',
                        'content_id' => $all_votes['id']
                    ]);

                    $wpdb->delete($rz_vote, [
                        'user_id' => $user_id,
                        'answer_id' => $answer_id
                    ]);

                    $get_upvote = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_vote WHERE answer_id='$answer_id' AND vote_type='up-vote'", ARRAY_A);
                    $get_downvote = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_vote WHERE answer_id='$answer_id' AND vote_type='down-vote'", ARRAY_A);
                    $count_vote = (intval(count($get_upvote)) - intval(count($get_downvote)));

                    if($vote_type == 'up-vote'){
                        $response['up_vote'] = false;
                        $response['counter'] = $count_vote;
                    }else{
                        $response['down_vote'] = false;
                        $response['counter'] = $count_vote;
                    }
                }else{
                    $wpdb->update($rz_vote, [
                        'vote_type' => $vote_type
                    ], ['id' => $all_votes['id']]);


                    $get_upvote = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_vote WHERE answer_id='$answer_id' AND vote_type='up-vote'", ARRAY_A);
                    $get_downvote = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_vote WHERE answer_id='$answer_id' AND vote_type='down-vote'", ARRAY_A);
                    $count_vote = (intval(count($get_upvote)) - intval(count($get_downvote)));

                    if($vote_type == 'up-vote'){
                        $response['up_vote'] = true;
                        $response['counter'] = $count_vote;
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


                            $get_post_data_using_answers = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}rz_answers WHERE id = '{$answer_id}'");

                            $message = getUserNameById($user_id).' '.$vote_type.' on your answer '.$get_post_data_using_answers->answer_text;

                            if($get_post_data_using_answers->user_id !== $user_id){
                                $wpdb->update($wpdb->prefix.'notification', [
                                    'massage_text' => $message
                                ], [
                                    'notification_type' => 'vote',
                                    'content_id' => $all_votes['id']
                                ]);
                            }
        
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
                    }else{
                        $vote_id = $all_votes['id'];
                        $is_user_earned_point = $wpdb->get_row("SELECT * FROM {$rz_point_table} WHERE user_id = '{$user_id}' AND content_id = '{$vote_id}' AND point_type = 'up-vote'");
                        if(!empty($is_user_earned_point)){
                            $wpdb->delete($rz_point_table, [
                                'id' => $is_user_earned_point->id,
                            ]);
                        }
                        $get_author = $get_author_using_answer_id->user_id;
        
                        $get_point = $wpdb->get_row("SELECT * FROM {$rz_user_profile_data} WHERE user_id = '$get_author'");

                        $get_post_data_using_answers = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}rz_answers WHERE id = '{$answer_id}'");

                        $message = getUserNameById($user_id).' '.$vote_type.' on your answer '.$get_post_data_using_answers->answer_text;

                        if($get_post_data_using_answers->user_id !== $user_id){
                            $wpdb->update($wpdb->prefix.'notification', [
                                'massage_text' => $message
                            ], [
                                'notification_type' => 'vote',
                                'content_id' => $vote_id
                            ]);
                        }
    
    
                        $user_point = $get_point->points;
                        $wpdb->update($rz_user_profile_data, [
                            'points' => ($user_point-10),
                        ], ['user_id' => $get_author_using_answer_id->user_id]);
                        $response['down_vote'] = true;
                        $response['counter'] = $count_vote;
                    }
                }
            }else{
                $wpdb->insert($rz_vote, [
                    'user_id' => $user_id,
                    'answer_id' => $answer_id,
                    'vote_type' => $vote_type
                ]);

                $vote_id = $wpdb->insert_id;


                $get_upvote = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_vote WHERE answer_id='$answer_id' AND vote_type='up-vote'", ARRAY_A);
                $get_downvote = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_vote WHERE answer_id='$answer_id' AND vote_type='down-vote'", ARRAY_A);
                $count_vote = (intval(count($get_upvote)) - intval(count($get_downvote)));

                $get_post_data_using_answers = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}rz_answers WHERE id = '{$answer_id}'");

                $message = getUserNameById($user_id).' '.$vote_type.' on your answer '.$get_post_data_using_answers->answer_text;

                if($get_post_data_using_answers->user_id !== $user_id){
                    add_notification(get_post_permalink($get_post_data_using_answers->post_id), $user_id, $get_post_data_using_answers->user_id, 'vote', $vote_id, $message);
                }

                if($vote_type == 'up-vote'){
                    $response['up_vote'] = true;
                    $response['counter'] = $count_vote;
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
