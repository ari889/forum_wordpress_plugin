<?php


/**
 * direct access not allowed
 */
if (!defined('ABSPATH')) {
    die(__('Direct access not allowed.', 'imit-recozilla'));
}

/**
 * add comment on answer
 */
add_action('wp_ajax_imit_add_answer_on_comment', function () {
    global $wpdb;
    $nonce = $_POST['nonce'];
    if (wp_verify_nonce($nonce, 'rz-add-comment-on-answer')) {
        $answer_id = sanitize_key($_POST['answer_id']);
        $comment_text = sanitize_text_field($_POST['answer_comment_text']);
        $user_id = get_current_user_id();
        $rz_answer_comment = $wpdb->prefix . 'rz_answer_comments';


        $get_post_id = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}rz_answers WHERE id = '{$answer_id}'");

        $post_author_id = get_post_field('post_author', $get_post_id->post_id);

        if (!empty($answer_id) && !empty($comment_text)) {
            $wpdb->insert($rz_answer_comment, [
                'user_id' => $user_id,
                'answer_id' => $answer_id,
                'comment_text' => $comment_text,
                'created_at' => wpDateTime(),
                'updated_at' => wpDateTime()
            ]);

            $comment_id = $wpdb->insert_id;

            $post_id = $get_post_id->post_id;
            $answer_author = $get_post_id->user_id;

            $response['activity_id'] = 'comment-' . $comment_id;
            $response['activity_url'] = get_permalink($post_id, false);
            $get_post_data = get_post($post_id);
            $response['text_message'] = getUserNameById(get_current_user_id()) . ' commented on ' . getUserNameById($answer_author) . '\' answer for <span class="imit-font rz-color fw-500">' . $get_post_data->post_title . '</span></span>';
            $response['image_url'] = getProfileImageById(get_current_user_id());
            $response['sender_id'] = get_current_user_id();
            $response['receiver_id'] = $post_author_id;
            $response['receiver_id_2'] = $answer_author;
            $response['content_id'] = $comment_id;
            $response['message_text'] = getUserNameById(get_current_user_id()) . ' commented on your post answer for <strong>' . $get_post_data->post_title . '</strong>';
            $response['message_text_2'] = getUserNameById(get_current_user_id()) . ' commented on your answer for <strong>' . $get_post_data->post_title . '</strong>';
        }

        echo json_encode($response);
    }
    die();
});

/**
 * add comment up vote
 */
add_action('wp_ajax_imit_add_comment_up_vote', function () {
    global $wpdb;
    $nonce = $_POST['nonce'];
    if (wp_verify_nonce($nonce, 'rz-add-comment-up-vote')) {
        $comment_id = sanitize_key($_POST['comment_id']);
        $user_id = get_current_user_id();
        $vote_type = sanitize_text_field($_POST['vote_type']);
        $rz_answer_comment_vote = $wpdb->prefix . 'rz_answer_comment_votes';
        $is_user_voted = $wpdb->get_row("SELECT * FROM {$rz_answer_comment_vote} WHERE user_id = '$user_id' AND comment_id = '$comment_id'");

        $get_comment_author = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}rz_answer_comments WHERE id = '{$comment_id}'");

        $get_post_id = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}rz_answer_comments INNER JOIN {$wpdb->prefix}rz_answers ON {$wpdb->prefix}rz_answers.id = {$wpdb->prefix}rz_answer_comments.answer_id WHERE {$wpdb->prefix}rz_answer_comments.id = '{$comment_id}'");

        $vote_id = '';

        if (!empty($is_user_voted)) {
            $vote_id = $is_user_voted->id;
        }

        if (!empty($comment_id) && !empty($user_id)) {
            if (!empty($is_user_voted)) {
                if ($vote_type == $is_user_voted->vote_type) {

                    $wpdb->delete($rz_answer_comment_vote, [
                        'user_id' => $user_id,
                        'comment_id' => $comment_id,
                    ]);


                    if ($vote_type == 'up-vote') {
                        $response['up_vote'] = false;
                    } else {
                        $response['down_vote'] = false;
                    }
                } else {
                    $wpdb->update($rz_answer_comment_vote, [
                        'vote_type' => $vote_type
                    ], ['id' => $is_user_voted->id]);


                    if ($vote_type == 'up-vote') {
                        $response['up_vote'] = true;
                    } else {
                        $response['down_vote'] = true;
                    }
                }
                $response['activity_id'] = 'commentVote-' . $is_user_voted->id;
            } else {
                $wpdb->insert($rz_answer_comment_vote, [
                    'user_id' => $user_id,
                    'comment_id' => $comment_id,
                    'vote_type' => $vote_type,
                    'created_at' => wpDateTime(),
                    'updated_at' => wpDateTime()
                ]);

                $vote_id = $wpdb->insert_id;

                if ($vote_type == 'up-vote') {
                    $response['up_vote'] = true;
                } else {
                    $response['down_vote'] = true;
                }

                $response['activity_id'] = 'commentVote-' . $vote_id;
            }

            $post_id = $get_post_id->post_id;
            $get_post_data = get_post($post_id);
            $response['sender_id'] = get_current_user_id();
            $response['receiver_id'] = $get_comment_author->user_id;
            $response['link'] = get_permalink($post_id, false);
            $response['content_id'] = $vote_id;
            $response['message_text'] = getUserNameById(get_current_user_id()) . ' ' . (($vote_type == 'up-vote') ? 'upvoted' : 'downvoted') . ' on your comment for <strong>' . $get_post_data->post_title . '</strong>';;
        }

        echo json_encode($response);
    }
    die();
});


/**
 * delete comment
 */


add_action('wp_ajax_rz_delete_comment', function () {
    global $wpdb;
    $nonce = $_POST['nonce'];
    if (wp_verify_nonce($nonce, 'rz-delete-comment-nonce')) {
        $comment_id = sanitize_key($_POST['comment_id']);
        $user_id = get_current_user_id();

        if (!empty($comment_id) && !empty($user_id)) {
            $wpdb->query("DELETE FROM {$wpdb->prefix}rz_comment_reply_likes WHERE reply_id IN (SELECT id FROM {$wpdb->prefix}rz_comment_replays WHERE comment_id = '{$comment_id}')");

            $wpdb->delete($wpdb->prefix . 'rz_comment_replays', [
                'comment_id' => $comment_id
            ]);

            $wpdb->delete($wpdb->prefix . 'rz_answer_comment_votes', [
                'comment_id' => $comment_id
            ]);


            $wpdb->delete($wpdb->prefix . 'rz_answer_comments', [
                'id' => $comment_id,
                'user_id' => $user_id
            ]);

            exit('done');
        }
    }
    die();
});


/**
 * get answer comment
 */
add_action('wp_ajax_nopriv_rz_get_more_comment_data', 'get_more_comment_data');
add_action('wp_ajax_rz_get_more_comment_data', 'get_more_comment_data');

function get_more_comment_data()
{
    global $wpdb;
    $nonce = $_POST['nonce'];
    if (wp_verify_nonce($nonce, 'rz-get-more-comment-nonce')) {
        $answer_id = sanitize_key($_POST['answer_id']);
        $start = sanitize_key($_POST['start']);

        $get_all_comments = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_answer_comments WHERE answer_id = '$answer_id' ORDER BY id DESC LIMIT {$start}, 3", ARRAY_A);

        $all_comments = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_answer_comments WHERE answer_id = '$answer_id'", ARRAY_A);

        if (count($get_all_comments) > 0) {
            $response['html'] = '';
            if (count($get_all_comments) < 3 || ($start + 3) >= count($all_comments)) {
                $response['commentReachMax'] = true;
                foreach ($get_all_comments as $comment) {
                    $comments_user = get_userdata($comment['user_id']);
                    $comment_id = $comment['id'];

                    /**
                     * get comment vote data
                     */
                    $user_id = get_current_user_id();
                    $get_comment_all_up_vote = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_answer_comment_votes WHERE user_id = '$user_id' AND comment_id = '$comment_id' AND vote_type = 'up-vote'", ARRAY_A);
                    $get_comment_all_down_vote = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_answer_comment_votes WHERE user_id = '$user_id' AND comment_id = '$comment_id' AND vote_type = 'down-vote'", ARRAY_A);


                    $count_upvote_comment = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_answer_comment_votes WHERE comment_id = '$comment_id' AND vote_type = 'up-vote'", ARRAY_A);

                    /**
                     * comment delete button
                     */
                    $comment_delete = '<ul class="comment-action-right ps-0 mb-0 d-flex flex-row justify-content-start align-items-center">
                        <li class="comment-action-list list-unstyled">
                            <div class="dropdown">
                                <button class="border-0 bg-transparent rz-color" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-ellipsis-h"></i>
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                    <li><a class="dropdown-item imit-font fz-14 text-dark" href="#" id="delete-comment" data-comment_id="' . $comment_id . '">Delete</a></li>
                                </ul>
                            </div>
                        </li>
                    </ul>';

                    $response['html'] .= '<li class="comment-list list-unstyled" id="comment' . $comment_id . '">
                    <div class="ms-5 pe-3" style="padding-top: 30px;">
                        <div class="blog-list-header d-flex flex-sm-row flex-column justify-content-between align-items-start align-items-sm-center">
                            <div class="user-info d-flex flex-row justify-content-start align-items-center">
                                <div class="profile-image">
                                    <img src="' . getProfileImageById($comment['user_id']) . '" alt="">
                                </div>
                                <div class="userdetails ms-2">
                                    <a href="' . site_url() . '/user/' . $comments_user->user_login . '" class="imit-font fz-14 text-dark fw-500 d-block">' . getUserNameById($comment['user_id']) . '</a>
                                    <p class="mb-0 designation imit-font fw-400 rz-secondary-color fz-12">' . $comments_user->user_login . '</p>
                                </div>
                            </div>
                            <p class="mb-0 imit-font fz-14 rz-secondary-color fw-400 mt-sm-0 mt-2">Comment on: ' . date('g:i a F d, Y', strtotime($comment['created_at'])) . '</p>
                        </div>
                        <p class="comment-text imit-font text-dark fz-16" style="margin: 30px 0;line-height: 30px;">' . $comment['comment_text'] . '</p>
                    </div>
    
                    <div class="ps-5 py-2 comment-action bg-white d-flex flex-row justify-content-between align-items-center px-3">
                            <ul class="comment-action-left ps-0 mb-0 d-flex flex-row justify-content-start align-items-center" id="comment-action' . $comment_id . '">
                                <li class="comment-action-list list-unstyled">
                                    <a href="#" class="rz-prev text-white fz-14 text-decoration-none me-3 text-white d-block ' . ((count($get_comment_all_up_vote) > 0) ? 'active' : '') . '" ' . ((is_user_logged_in()) ? 'id="up-vote-comment" data-comment_id="' . $comment['id'] . '"' : 'data-bs-toggle="modal" data-bs-target="#login-modal"') . '><i class="fas fa-arrow-up"></i></a>
                                </li>
                                <li class="comment-action-list list-unstyled">
                                    <span class="counter imit-font fz-14 fw-500 me-3 text-dark d-block" id="comment-counter' . $comment['id'] . '">' . count($count_upvote_comment) . '</span>
                                </li>
                                <li class="comment-action-list list-unstyled">
                                    <a href="#" class="rz-next fz-14 text-white text-decoration-none me-3 d-block ' . ((count($get_comment_all_down_vote) > 0) ? 'active' : '') . '" ' . ((is_user_logged_in()) ? 'id="down-vote-comment" data-comment_id="' . $comment['id'] . '"' : 'data-bs-toggle="modal" data-bs-target="#login-modal"') . '><i class="fas fa-arrow-down"></i></a>
                                </li>
                                <!-- <li class="comment-action-list list-unstyled">
                                    <a href="#" class="rz-color fz-14 text-decoration-none imit-font" id="comment-replay" data-comment_id="' . $comment['id'] . '"><i class="fas fa-reply"></i> Reply</a>
                                </li> -->
                            </ul>
                            ' . ((is_user_logged_in() && $comment['user_id'] == get_current_user_id()) ? $comment_delete : '') . '
                        </div>
                </li>';
                }
            } else {
                foreach ($get_all_comments as $comment) {
                    $comments_user = get_userdata($comment['user_id']);
                    $comment_id = $comment['id'];

                    /**
                     * get comment vote data
                     */
                    $user_id = get_current_user_id();
                    $get_comment_all_up_vote = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_answer_comment_votes WHERE user_id = '$user_id' AND comment_id = '$comment_id' AND vote_type = 'up-vote'", ARRAY_A);
                    $get_comment_all_down_vote = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_answer_comment_votes WHERE user_id = '$user_id' AND comment_id = '$comment_id' AND vote_type = 'down-vote'", ARRAY_A);


                    $count_upvote_comment = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_answer_comment_votes WHERE comment_id = '$comment_id' AND vote_type = 'up-vote'", ARRAY_A);

                    /**
                     * comment delete button
                     */
                    $comment_delete = '<ul class="comment-action-right ps-0 mb-0 d-flex flex-row justify-content-start align-items-center">
                        <li class="comment-action-list list-unstyled">
                            <div class="dropdown">
                                <button class="border-0 bg-transparent rz-color" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-ellipsis-h"></i>
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                    <li><a class="dropdown-item imit-font fz-14 text-dark" href="#" id="delete-comment" data-comment_id="' . $comment_id . '">Delete</a></li>
                                </ul>
                            </div>
                        </li>
                    </ul>';

                    $response['html'] .= '<li class="comment-list list-unstyled" id="comment' . $comment_id . '">
                    <div class="ms-5 pe-3" style="padding-top: 30px;">
                        <div class="blog-list-header d-flex flex-row justify-content-between align-items-center">
                            <div class="user-info d-flex flex-row justify-content-start align-items-center">
                                <div class="profile-image">
                                    <img src="' . getProfileImageById($comment['user_id']) . '" alt="">
                                </div>
                                <div class="userdetails ms-2">
                                    <a href="' . site_url() . '/user/' . $comments_user->user_login . '" class="imit-font fz-14 text-dark fw-500 d-block">' . getUserNameById($comment['user_id']) . '</a>
                                    <p class="mb-0 designation imit-font fw-400 rz-secondary-color fz-12">' . $comments_user->user_login . '</p>
                                </div>
                            </div>
                            <p class="mb-0 imit-font fz-14 rz-secondary-color fw-400">Comment on: ' . date('g:i a F d, Y', strtotime($comment['created_at'])) . '</p>
                        </div>
                        <p class="comment-text imit-font text-dark fz-16" style="margin: 30px 0;line-height: 30px;">' . $comment['comment_text'] . '</p>
                    </div>
    
                    <div class="ps-5 py-2 comment-action bg-white d-flex flex-row justify-content-between align-items-center px-3">
                            <ul class="comment-action-left ps-0 mb-0 d-flex flex-row justify-content-start align-items-center" id="comment-action' . $comment_id . '">
                                <li class="comment-action-list list-unstyled">
                                    <a href="#" class="rz-prev text-white fz-14 text-decoration-none me-3 text-white d-block ' . ((count($get_comment_all_up_vote) > 0) ? 'active' : '') . '" ' . ((is_user_logged_in()) ? 'id="up-vote-comment" data-comment_id="' . $comment['id'] . '"' : 'data-bs-toggle="modal" data-bs-target="#login-modal"') . '><i class="fas fa-arrow-up"></i></a>
                                </li>
                                <li class="comment-action-list list-unstyled">
                                    <span class="counter imit-font fz-14 fw-500 me-3 d-block" id="comment-counter' . $comment['id'] . '">' . count($count_upvote_comment) . '</span>
                                </li>
                                <li class="comment-action-list list-unstyled">
                                    <a href="#" class="rz-next fz-14 text-white text-decoration-none me-3 d-block ' . ((count($get_comment_all_down_vote) > 0) ? 'active' : '') . '" ' . ((is_user_logged_in()) ? 'id="down-vote-comment" data-comment_id="' . $comment['id'] . '"' : 'data-bs-toggle="modal" data-bs-target="#login-modal"') . '><i class="fas fa-arrow-down"></i></a>
                                </li>
                            </ul>
                            ' . ((is_user_logged_in() && $comment['user_id'] == get_current_user_id()) ? $comment_delete : '') . '
                        </div>
                </li>';
                }
                $response['commentReachMax'] = false;
            }
        } else {
            $response['commentReachMax'] = true;
        }

        echo json_encode($response);
    }
    die();
}
