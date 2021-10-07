<?php


/**
 * direct access not allowed
 */
if (!defined('ABSPATH')) {
    die(__('Direct access not allowed.', 'imit-recozilla'));
}

/**
 * add vote
 */
add_action('wp_ajax_imit_add_vote', function () {
    global $wpdb;
    $nonce = $_POST['nonce'];
    if (wp_verify_nonce($nonce, 'rz-add-vote-nonce')) {
        $answer_id = sanitize_key($_POST['answer_id']);
        $user_id = sanitize_key(get_current_user_id());
        $vote_type = sanitize_text_field($_POST['vote_type']);
        $rz_vote = $wpdb->prefix . 'rz_vote';
        $rz_point_table = $wpdb->prefix . 'rz_point_table';
        $rz_user_profile_data = $wpdb->prefix . 'rz_user_profile_data';
        $vote_id = '';
        $all_votes = $wpdb->get_row("SELECT * FROM {$rz_vote} WHERE answer_id = '$answer_id' AND user_id = '$user_id'", ARRAY_A);

        if (!empty($all_votes)) {
            $vote_id = $all_votes['id'];
        }

        $get_author_using_answer_id = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}rz_answers WHERE id = '$answer_id'");

        if (!empty($answer_id) && !empty($user_id)) {
            if (!empty($all_votes)) {
                $get_type = $all_votes['vote_type'];
                if ($vote_type == $get_type) {
                    if ($vote_type == 'up-vote') {
                        $is_user_earned_point = $wpdb->get_row("SELECT * FROM {$rz_point_table} WHERE user_id = '{$user_id}' AND content_id = '{$vote_id}' AND point_type = 'up-vote'");
                        if (!empty($is_user_earned_point)) {
                            $wpdb->delete($rz_point_table, [
                                'id' => $is_user_earned_point['id'],
                            ]);
                        }
                        $get_author = $get_author_using_answer_id->user_id;

                        $get_point = $wpdb->get_row("SELECT * FROM {$rz_user_profile_data} WHERE user_id = '$get_author'");


                        $user_point = $get_point->points;
                        $wpdb->update($rz_user_profile_data, [
                            'points' => ($user_point - 10),
                        ], ['user_id' => $get_author_using_answer_id->user_id]);
                    }

                    $wpdb->delete($rz_vote, [
                        'user_id' => $user_id,
                        'answer_id' => $answer_id
                    ]);

                    if ($vote_type == 'up-vote') {
                        $response['up_vote'] = false;
                    } else {
                        $response['down_vote'] = false;
                    }
                } else {
                    $wpdb->update($rz_vote, [
                        'vote_type' => $vote_type
                    ], ['id' => $all_votes['id']]);

                    if ($vote_type == 'up-vote') {
                        $response['up_vote'] = true;
                        if ($vote_type == 'up-vote' && $get_author_using_answer_id->user_id !== $user_id) {
                            $wpdb->insert($rz_point_table, [
                                'user_id' => $user_id,
                                'content_id' => $vote_id,
                                'point_type' => 'up-vote',
                                'point_earn' => 10,
                                'created_at' => wpDateTime(),
                                'updated_at' => wpDateTime()
                            ]);

                            $get_author = $get_author_using_answer_id->user_id;

                            $get_point = $wpdb->get_row("SELECT * FROM {$rz_user_profile_data} WHERE user_id = '$get_author'");


                            $user_point = $get_point->points;

                            if (empty($get_point)) {
                                $wpdb->insert($rz_user_profile_data, [
                                    'points' => 10,
                                    'user_id' => $get_author_using_answer_id->user_id,
                                    'created_at' => wpDateTime(),
                                    'updated_at' => wpDateTime()
                                ]);
                            } else {
                                $wpdb->update($rz_user_profile_data, [
                                    'points' => ($user_point + 10),
                                ], ['user_id' => $get_author_using_answer_id->user_id]);
                            }
                        }
                    } else {
                        $is_user_earned_point = $wpdb->get_row("SELECT * FROM {$rz_point_table} WHERE user_id = '{$user_id}' AND content_id = '{$vote_id}' AND point_type = 'up-vote'");
                        if (!empty($is_user_earned_point)) {
                            $wpdb->delete($rz_point_table, [
                                'id' => $is_user_earned_point->id,
                            ]);
                        }
                        $get_author = $get_author_using_answer_id->user_id;

                        $get_point = $wpdb->get_row("SELECT * FROM {$rz_user_profile_data} WHERE user_id = '$get_author'");


                        $user_point = $get_point->points;
                        $wpdb->update($rz_user_profile_data, [
                            'points' => ($user_point - 10),
                        ], ['user_id' => $get_author_using_answer_id->user_id]);
                        $response['down_vote'] = true;
                    }
                }
                $response['activity_id'] = 'vote-' . $get_author_using_answer_id->id;
            } else {
                $wpdb->insert($rz_vote, [
                    'user_id' => $user_id,
                    'answer_id' => $answer_id,
                    'vote_type' => $vote_type,
                    'created_at' => wpDateTime(),
                    'updated_at' => wpDateTime()
                ]);

                $vote_id = $wpdb->insert_id;

                if ($vote_type == 'up-vote') {
                    $response['up_vote'] = true;
                    if ($vote_type == 'up-vote' && $get_author_using_answer_id->user_id !== $user_id) {
                        $wpdb->insert($rz_point_table, [
                            'user_id' => $user_id,
                            'content_id' => $vote_id,
                            'point_type' => 'up-vote',
                            'point_earn' => 10,
                            'created_at' => wpDateTime(),
                            'updated_at' => wpDateTime()
                        ]);

                        $get_author = $get_author_using_answer_id->user_id;

                        $get_point = $wpdb->get_row("SELECT * FROM {$rz_user_profile_data} WHERE user_id = '$get_author'");


                        $user_point = $get_point->points;

                        if (empty($get_point)) {
                            $wpdb->insert($rz_user_profile_data, [
                                'points' => 10,
                                'user_id' => $get_author_using_answer_id->user_id,
                                'created_at' => wpDateTime(),
                                'updated_at' => wpDateTime()
                            ]);
                        } else {
                            $wpdb->update($rz_user_profile_data, [
                                'points' => ($user_point + 10),
                            ], ['user_id' => $get_author_using_answer_id->user_id]);
                        }
                    }
                } else {
                    $response['down_vote'] = true;
                }

                $response['activity_id'] = 'vote-' . $vote_id;
            }
        }

        $post_id = $get_author_using_answer_id->post_id;
        $answer_author = $get_author_using_answer_id->user_id;

        $response['activity_url'] = get_permalink($post_id, false);
        $get_post_data = get_post($post_id);
        $response['text_message'] = getUserNameById(get_current_user_id()) . ' ' . (($vote_type == 'up-vote') ? 'upvoted' : 'downvoted') . ' ' . getUserNameById($answer_author) . '\'s answer for <span class="imit-font rz-color fw-500">' . $get_post_data->post_title . '</span></span>';
        $response['image_url'] = getProfileImageById(get_current_user_id());
        $response['sender_id'] = get_current_user_id();
        $response['receiver_id'] = $answer_author;
        $response['content_id'] = strval($vote_id);
        $response['message_text'] = getUserNameById(get_current_user_id()) . ' ' . (($vote_type == 'up-vote') ? 'upvoted' : 'downvoted') . ' on your answer for <strong>' . $get_post_data->post_title . '</strong>';

        echo json_encode($response);
    }
    die();
});
