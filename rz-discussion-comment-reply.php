<?php


/**
 * direct access not allowed
 */
if (!defined('ABSPATH')) {
    die(__('Direct access not allowed.', 'imit-recozilla'));
}

/**
 * add replay on discussion comment
 */
add_action('wp_ajax_rz_add_replY_on_discussion_comment', function () {
    global $wpdb;
    $nonce = $_POST['nonce'];
    if (wp_verify_nonce($nonce, 'rz-add-replay-on-discussion-comment')) {
        $comment_id = sanitize_key($_POST['comment_id']);
        $replay_text = sanitize_text_field($_POST['replay_text']);
        $user_id = get_current_user_id();
        $rz_discuss_comment_replays = $wpdb->prefix . 'rz_discuss_comment_replays';

        $get_post = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}rz_discuss_comment_replays INNER JOIN {$wpdb->prefix}rz_discussion_comments ON {$wpdb->prefix}rz_discussion_comments.id = {$wpdb->prefix}rz_discuss_comment_replays.comment_id WHERE {$wpdb->prefix}rz_discuss_comment_replays.comment_id = '{$comment_id}'");

        $post_id = $get_post->post_id;

        $post_author = get_post_field('post_author', $post_id);

        $get_comment = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}rz_discussion_comments WHERE id = '{$comment_id}'");

        $comment_author = $get_comment->user_id;

        if (!empty($comment_id) && !empty($replay_text) && !empty($user_id)) {
            $wpdb->insert($rz_discuss_comment_replays, [
                'user_id' => $user_id,
                'comment_id' => $comment_id,
                'replay_text' => $replay_text,
                'created_at' => wpDateTime(),
                'updated_at' => wpDateTime()
            ]);

            $reply_id = $wpdb->insert_id;
        }
    }
    die();
});

/**
 * add or remove like on reply
 */
add_action('wp_ajax_rz_add_or_remove_like_on_reply', function () {
    global $wpdb;
    $nonce = $_POST['nonce'];
    if (wp_verify_nonce($nonce, 'rz-add-like-on-reply')) {
        $reply_id = sanitize_key($_POST['reply_id']);
        $user_id = get_current_user_id();
        $reply_type = sanitize_text_field($_POST['reply_type']);
        $rz_discuss_reply_likes = $wpdb->prefix . 'rz_discuss_reply_likes';

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


        if (!empty($reply_id) && !empty($user_id) && !empty($reply_type)) {
            if (!empty($get_all_replays)) {
                if ($get_all_replays->reply_type == $reply_type) {

                    $wpdb->delete($wpdb->prefix . 'notification', [
                        'sender_id' => $user_id,
                        'receiver_id' => $reply_author,
                        'notification_type' => 'discuss-reply-like',
                        'content_id' => $get_all_replays->id
                    ]);

                    $wpdb->delete($wpdb->prefix . 'notification', [
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
                    $get_down_reply = $wpdb->get_results("SELECT * FROM {$rz_discuss_reply_likes} WHERE reply_id = '{$reply_id}' AND reply_type = 'down-reply'", ARRAY_A);
                    $count_reply = intval($get_up_reply) - intval($get_down_reply);

                    if ($reply_type == 'up-reply') {
                        $response['up_reply'] = false;
                        $response['counter'] = $count_reply;
                    } else {
                        $response['down_reply'] = false;
                        $response['counter'] = $count_reply;
                    }
                } else {
                    $wpdb->update($rz_discuss_reply_likes, [
                        'reply_type' => $reply_type
                    ], [
                        'id' => $get_all_replays->id
                    ]);

                    if ($user_id != $post_author) {
                        $message1 = getUserNameById($user_id) . ' ' . $reply_type . ' on your post reply <strong>' . get_the_title($post_id) . '</strong>';
                        $wpdb->update($wpdb->prefix . 'notification', [
                            'massage_text' => $message1
                        ], [
                            'sender_id' => $user_id,
                            'receiver_id' => $post_author,
                            'notification_type' => 'discuss-reply-like',
                            'content_id' => $get_all_replays->id
                        ]);
                    }

                    if ($user_id != $reply_author) {
                        $message2 = getUserNameById($user_id) . ' ' . $reply_type . ' on your reply <strong>' . get_the_title($post_id) . '</strong>';
                        $wpdb->update($wpdb->prefix . 'notification', [
                            'massage_text' => $message2
                        ], [
                            'sender_id' => $user_id,
                            'receiver_id' => $reply_author,
                            'notification_type' => 'discuss-reply-like',
                            'content_id' => $get_all_replays->id
                        ]);
                    }

                    $get_up_reply = $wpdb->get_results("SELECT * FROM {$rz_discuss_reply_likes} WHERE reply_id = '{$reply_id}' AND reply_type = 'up-reply'", ARRAY_A);
                    $get_down_reply = $wpdb->get_results("SELECT * FROM {$rz_discuss_reply_likes} WHERE reply_id = '{$reply_id}' AND reply_type = 'down-reply'", ARRAY_A);
                    $count_reply = intval($get_up_reply) - intval($get_down_reply);

                    if ($reply_type == 'up-reply') {
                        $response['up_reply'] = true;
                        $response['counter'] = $count_reply;
                    } else {
                        $response['down_reply'] = true;
                        $response['counter'] = $count_reply;
                    }
                }
            } else {
                $wpdb->insert($rz_discuss_reply_likes, [
                    'reply_id' => $reply_id,
                    'user_id' => $user_id,
                    'reply_type' => $reply_type,
                    'created_at' => wpDateTime(),
                    'updated_at' => wpDateTime()
                ]);

                $like_id = $wpdb->insert_id;


                $get_up_reply = $wpdb->get_results("SELECT * FROM {$rz_discuss_reply_likes} WHERE reply_id = '{$reply_id}' AND reply_type = 'up-reply'", ARRAY_A);
                $get_down_reply = $wpdb->get_results("SELECT * FROM {$rz_discuss_reply_likes} WHERE reply_id = '{$reply_id}' AND reply_type = 'down-reply'", ARRAY_A);
                $count_reply = intval($get_up_reply) - intval($get_down_reply);

                if ($reply_type == 'up-reply') {
                    $response['up_reply'] = true;
                    $response['counter'] = $count_reply;
                } else {
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
add_action('wp_ajax_rz_delete_discuss_reply', function () {
    global $wpdb;
    $nonce = $_POST['nonce'];
    if (wp_verify_nonce($nonce, 'rz-delete-discuss-reply-nonce')) {
        $reply_id = sanitize_key($_POST['reply_id']);
        $user_id = get_current_user_id();

        if (!empty($reply_id) && !empty($user_id)) {
            $wpdb->delete($wpdb->prefix . 'rz_discuss_reply_likes', [
                'reply_id' => $reply_id
            ]);

            $wpdb->delete($wpdb->prefix . 'rz_discuss_comment_replays', [
                'id' => $reply_id
            ]);

            exit('done');
        }
    }
    die();
});

/**
 * get more discuss comment reply
 */
add_action('wp_ajax_nopriv_rz_get_discuss_reply', 'imit_rz_get_discuss_reply_data');
add_action('wp_ajax_rz_get_discuss_reply', 'imit_rz_get_discuss_reply_data');

function imit_rz_get_discuss_reply_data()
{
    global $wpdb;
    $nonce = $_POST['nonce'];
    if (wp_verify_nonce($nonce, 'rz-get-discuss-reply-nonce')) {
        $start = sanitize_key($_POST['start']);
        $comment_id = sanitize_key($_POST['comment_id']);

        $get_replys = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_discuss_comment_replays WHERE comment_id = '$comment_id' ORDER BY id DESC LIMIT {$start}, 3", ARRAY_A);
        $get_all_replys = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_discuss_comment_replays WHERE comment_id = '$comment_id'", ARRAY_A);

        if (count($get_replys) > 0) {
            $response['html'] = '';
            if (count($get_replys) < 3 || ($start + 3) >= count($get_all_replys)) {
                $response['replyReachMax'] = true;
                foreach ($get_replys as $dis_reply) {
                    $reply_user_data = get_userdata($dis_reply['user_id']);

                    $reply_id = $dis_reply['id'];
                    $user_id = get_current_user_id();
                    $user_reply_up_like = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_discuss_reply_likes WHERE reply_id = '$reply_id' AND user_id = '$user_id' AND reply_type = 'up-reply'", ARRAY_A);
                    $user_reply_down_like = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_discuss_reply_likes WHERE reply_id = '$reply_id' AND user_id = '$user_id' AND reply_type = 'down-reply'", ARRAY_A);


                    $get_up_reply = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_discuss_reply_likes WHERE user_id = '$user_id' AND reply_type = 'up-reply'", ARRAY_A);
                    $get_down_reply = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_discuss_reply_likes WHERE user_id = '$user_id' AND reply_type = 'down-reply'", ARRAY_A);

                    /**
                     * reply delete button
                     */
                    $reply_delete_button = '<ul class="comment-like ps-0 mb-0 d-flex flex-row justify-content-end align-items-center">
                                                <div class="dropdown">
                                                    <button class="rz-secondary-color fz-14 bg-transparent p-0 d-block" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="fas fa-ellipsis-h"></i>
                                                    </button>
                                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                                        <li><a class="dropdown-item imit-font fz-14" href="#" id="discuss-reply-delete" data-reply_id="' . $dis_reply['id'] . '">Delete</a></li>
                                                    </ul>
                                                </div>
                                            </ul>';

                    $response['html'] .= '
                    <li class="comment-text list-unstyled p-3" id="dis-reply' . $dis_reply['id'] . '">
                        <div class="comment-header d-flex flex-row justify-content-between align-items-center">
                            <div class="user-data d-flex flex-row justify-content-start align-items-center">
                                <div class="profile-image" style="width: 42px;height: 42px;border-radius: 50%;">
                                    <img src="' . getProfileImageById($dis_reply['user_id']) . '" alt="" style="width: 42px;height: 42px;border-radius: 50%;">
                                </div>
                                <div class="username ms-2">
                                    <a href="' . site_url() . '/user/' . $reply_user_data->user_login . '" class="imit-font fz-14 text-dark fw-500 d-block">' . getUserNameById($dis_reply['user_id']) . '</a>
                                        
                                    <p class="mb-0 imit-font fz-12 rz-secondary-color">' . $reply_user_data->user_login . '</p>
                                </div>
                            </div>
                            <p class="mb-0 rz-secondary-color fz-14 fw-500 imit-font">Reply on: ' . date('g:i a F d, Y', strtotime($dis_reply['created_at'])) . '</p>
                        </div>
                        <p class="fz-16 text-dark imit-font fz-16 mt-2">' . $dis_reply['replay_text'] . '</p>


                        <div class="d-flex flex-row justify-content-between align-items-center">
                            <ul class="comment-like ps-0 mb-0 d-flex flex-row justify-content-start align-items-center" id="reply-action' . $dis_reply['id'] . '">
                                <li class="list-unstyled me-3"><a href="#" class="d-block up-like ' . ((count($user_reply_up_like) > 0) ? 'active' : '') . '" id="discuss-replay-like-up" data-reply_id="' . $dis_reply['id'] . '"><i class="fas fa-arrow-up"></i></a></li>
                                <li class="list-unstyled me-3"><span class="imit-font fz-16 fw-500 ' . (((intval(count($get_up_reply)) - intval(count($get_down_reply))) < 0) ? 'text-danger' : 'text-success') . '" id="reply-like-counter' . $dis_reply['id'] . '">' . (intval(count($get_up_reply)) - intval(count($get_down_reply))) . '</span></li>
                                <li class="list-unstyled me-3"><a href="#" class="d-block down-like ' . ((count($user_reply_down_like) > 0) ? 'active' : '') . '" id="discuss-replay-like-down" data-reply_id="' . $dis_reply['id'] . '"><i class="fas fa-arrow-down"></i></a></li>
                                <li class="list-unstyled me-3"><a href="#" class="d-block reply imit-font fz-14 rz-color" data-comment_id="' . $comment['id'] . '" id="discuss-replay-button"><i class="fas fa-reply"></i> Reply</a></li>
                            </ul>
                            ' . ((is_user_logged_in() && $dis_reply['user_id'] == get_current_user_id()) ? $reply_delete_button : '') . '
                        </div>
                    </li>
                    ';
                }
            } else {
                $response['replyReachMax'] = false;
                foreach ($get_replys as $dis_reply) {
                    $reply_user_data = get_userdata($dis_reply['user_id']);

                    $reply_id = $dis_reply['id'];
                    $user_id = get_current_user_id();
                    $user_reply_up_like = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_discuss_reply_likes WHERE reply_id = '$reply_id' AND user_id = '$user_id' AND reply_type = 'up-reply'", ARRAY_A);
                    $user_reply_down_like = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_discuss_reply_likes WHERE reply_id = '$reply_id' AND user_id = '$user_id' AND reply_type = 'down-reply'", ARRAY_A);


                    $get_up_reply = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_discuss_reply_likes WHERE user_id = '$user_id' AND reply_type = 'up-reply'", ARRAY_A);
                    $get_down_reply = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_discuss_reply_likes WHERE user_id = '$user_id' AND reply_type = 'down-reply'", ARRAY_A);

                    /**
                     * reply delete button
                     */
                    $reply_delete_button = '<ul class="comment-like ps-0 mb-0 d-flex flex-row justify-content-end align-items-center">
                                                <div class="dropdown">
                                                    <button class="rz-secondary-color fz-14 bg-transparent p-0 d-block" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="fas fa-ellipsis-h"></i>
                                                    </button>
                                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                                        <li><a class="dropdown-item imit-font fz-14" href="#" id="discuss-reply-delete" data-reply_id="' . $dis_reply['id'] . '">Delete</a></li>
                                                    </ul>
                                                </div>
                                            </ul>';

                    $response['html'] .= '
                    <li class="comment-text list-unstyled p-3" id="dis-reply' . $dis_reply['id'] . '">
                        <div class="comment-header d-flex flex-row justify-content-between align-items-center">
                            <div class="user-data d-flex flex-row justify-content-start align-items-center">
                                <div class="profile-image" style="width: 42px;height: 42px;border-radius: 50%;">
                                    <img src="' . getProfileImageById($dis_reply['user_id']) . '" alt="" style="width: 42px;height: 42px;border-radius: 50%;">
                                </div>
                                <div class="username ms-2">
                                    <a href="' . site_url() . '/user/' . $reply_user_data->user_login . '" class="imit-font fz-14 text-dark fw-500 d-block">' . getUserNameById($dis_reply['user_id']) . '</a>
                                        
                                    <p class="mb-0 imit-font fz-12 rz-secondary-color">' . $reply_user_data->user_login . '</p>
                                </div>
                            </div>
                            <p class="mb-0 rz-secondary-color fz-14 fw-500 imit-font">Reply on: ' . date('g:i a F d, Y', strtotime($dis_reply['created_at'])) . '</p>
                        </div>
                        <p class="fz-16 text-dark imit-font fz-16 mt-2">' . $dis_reply['replay_text'] . '</p>


                        <div class="d-flex flex-row justify-content-between align-items-center">
                            <ul class="comment-like ps-0 mb-0 d-flex flex-row justify-content-start align-items-center" id="reply-action' . $dis_reply['id'] . '">
                                <li class="list-unstyled me-3"><a href="#" class="d-block up-like ' . ((count($user_reply_up_like) > 0) ? 'active' : '') . '" id="discuss-replay-like-up" data-reply_id="' . $dis_reply['id'] . '"><i class="fas fa-arrow-up"></i></a></li>
                                <li class="list-unstyled me-3"><span class="imit-font fz-16 fw-500 ' . (((intval(count($get_up_reply)) - intval(count($get_down_reply))) < 0) ? 'text-danger' : 'text-success') . '" id="reply-like-counter' . $dis_reply['id'] . '">' . (intval(count($get_up_reply)) - intval(count($get_down_reply))) . '</span></li>
                                <li class="list-unstyled me-3"><a href="#" class="d-block down-like ' . ((count($user_reply_down_like) > 0) ? 'active' : '') . '" id="discuss-replay-like-down" data-reply_id="' . $dis_reply['id'] . '"><i class="fas fa-arrow-down"></i></a></li>
                                <li class="list-unstyled me-3"><a href="#" class="d-block reply imit-font fz-14 rz-color" data-comment_id="' . $comment['id'] . '" id="discuss-replay-button"><i class="fas fa-reply"></i> Reply</a></li>
                            </ul>
                            ' . ((is_user_logged_in() && $dis_reply['user_id'] == get_current_user_id()) ? $reply_delete_button : '') . '
                        </div>
                    </li>
                    ';
                }
            }
        } else {
            $response['replyReachMax'] = true;
        }

        echo json_encode($response);
    }
    die();
}
