<?php


/**
 * direct access not allowed
 */
if (!defined('ABSPATH')) {
    die(__('Direct access not allowed.', 'imit-recozilla'));
}

/**
 * add comment
 */
add_action('wp_ajax_imit_add_comment_on_discussion', function () {
    global $wpdb;
    $nonce = $_POST['nonce'];
    if (wp_verify_nonce($nonce, 'rz-add-comment-on-discussion')) {
        $comment_text = sanitize_text_field($_POST['comment_text']);
        $post_id = sanitize_key($_POST['post_id']);
        $user_id = get_current_user_id();
        $rz_discussion_comment_table = $wpdb->prefix . 'rz_discussion_comments';

        $post_author = get_post_field('post_author', $post_id);

        if (!empty($user_id) && !empty($post_id) && !empty($comment_text)) {
            $wpdb->insert($rz_discussion_comment_table, [
                'user_id' => $user_id,
                'post_id' => $post_id,
                'comment_text' => $comment_text,
                'created_at' => wpDateTime(),
                'updated_at' => wpDateTime()
            ]);

            $comment_id = $wpdb->insert_id;

            $response['activity_id'] = 'disComment-' . $comment_id;
            $response['activity_url'] = get_permalink($post_id, false);
            $get_post_data = get_post($post_id);
            $response['text_message'] = getUserNameById(get_current_user_id()) . ' commented on ' . getUserNameById($post_author) . '\' post <span class="imit-font rz-color fw-500">' . $get_post_data->post_title . '</span></span>';
            $response['image_url'] = getProfileImageById(get_current_user_id());
            $response['sender_id'] = get_current_user_id();
            $response['receiver_id'] = $post_author;
            $response['content_id'] = $comment_id;
            $response['message_text'] = getUserNameById(get_current_user_id()) . ' comment on your post for <strong>' . $get_post_data->post_title . '</strong>';
        }

        echo json_encode($response);
    }
    die();
});


/**
 * add like to discuss comment
 */
add_action('wp_ajax_add_like_to_discuss_comment', function () {
    global $wpdb;
    $nonce = $_POST['nonce'];
    if (wp_verify_nonce($nonce, 'rz-like-discussion-comment')) {
        $comment_id = sanitize_key($_POST['comment_id']);
        $user_id = get_current_user_id();
        $like_type = sanitize_text_field($_POST['like_type']);
        $rz_discuss_comment_likes = $wpdb->prefix . 'rz_discuss_comment_likes';

        $get_post_id = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}rz_discussion_comments WHERE id = '{$comment_id}'");

        $comment_author = $get_post_id->user_id;
        $post_author = get_post_field('post_author', $get_post_id->post_id);

        $all_likes = $wpdb->get_row("SELECT * FROM {$rz_discuss_comment_likes} WHERE user_id = '$user_id' AND comment_id = '$comment_id'");


        $like_id = '';
        if (!empty($all_likes)) {
            $like_id = $all_likes->id;
        }

        if (!empty($comment_id) && !empty($user_id)) {
            if (!empty($all_likes)) {
                if ($all_likes->like_type == $like_type) {

                    $wpdb->delete($rz_discuss_comment_likes, [
                        'comment_id' => $comment_id,
                        'user_id' => $user_id,
                        'like_type' => $like_type
                    ]);


                    if ($like_type == 'up-like') {
                        $response['up_like'] = false;
                    } else {
                        $response['down_like'] = false;
                    }
                } else {
                    $wpdb->update($rz_discuss_comment_likes, [
                        'like_type' => $like_type
                    ], [
                        'comment_id' => $comment_id,
                        'user_id' => $user_id,
                    ]);


                    if ($like_type == 'up-like') {
                        $response['up_like'] = true;
                    } else {
                        $response['down_like'] = true;
                    }
                }
            } else {
                $wpdb->insert($rz_discuss_comment_likes, [
                    'comment_id' => $comment_id,
                    'user_id' => $user_id,
                    'like_type' => $like_type,
                    'created_at' => wpDateTime(),
                    'updated_at' => wpDateTime()
                ]);

                $like_id = $wpdb->insert_id;


                if ($like_type == 'up-like') {
                    $response['up_like'] = true;
                } else {
                    $response['down_like'] = true;
                }
            }
            $post_id = $get_post_id->post_id;
            $get_post_data = get_post($post_id);
            $response['sender_id'] = get_current_user_id();
            $response['receiver_id'] = $comment_author;
            $response['link'] = get_permalink($post_id, false);
            $response['content_id'] = $like_id;
            $response['message_text'] = getUserNameById(get_current_user_id()) . ' ' . (($like_type == 'up-vote') ? 'upvoted' : 'downvoted') . ' on your post comment for <strong>' . $get_post_data->post_title . '</strong>';
        }

        echo json_encode($response);
    }
    die();
});

/**
 * delete discuss comment
 */
add_action('wp_ajax_rz_delete_discuss_comment', function () {
    global $wpdb;
    $nonce = $_POST['nonce'];
    if (wp_verify_nonce($nonce, 'rz-delete-discuss-comment-nonce')) {
        $comment_id = sanitize_key($_POST['comment_id']);
        $user_id = get_current_user_id();

        if (!empty($comment_id) && !empty($user_id)) {
            $wpdb->query("DELETE FROM {$wpdb->prefix}rz_discuss_reply_likes WHERE reply_id IN (SELECT id FROM {$wpdb->prefix}rz_discuss_comment_replays WHERE comment_id = '{$comment_id}')");

            $wpdb->delete($wpdb->prefix . 'rz_discuss_comment_replays', [
                'comment_id' => $comment_id
            ]);

            $wpdb->delete($wpdb->prefix . 'rz_discuss_comment_likes', [
                'comment_id' => $comment_id
            ]);

            $wpdb->delete($wpdb->prefix . 'rz_discussion_comments', [
                'id' => $comment_id
            ]);

            exit('done');
        }
    }
    die();
});

/**
 * load more discuss comments
 */
add_action('wp_ajax_nopriv_rz_get_discuss_comments', 'imit_rz_get_more_discussion_comment');
add_action('wp_ajax_rz_get_discuss_comments', 'imit_rz_get_more_discussion_comment');

function imit_rz_get_more_discussion_comment()
{
    global $wpdb;
    $nonce = $_POST['nonce'];
    if (wp_verify_nonce($nonce, 'rz-get-discuss-comment-nonce')) {
        $start = sanitize_key($_POST['start']);
        $post_id = sanitize_key($_POST['post_id']);

        $all_comments = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_discussion_comments WHERE post_id = '$post_id' ORDER BY id DESC LIMIT {$start}, 10", ARRAY_A);

        $get_all_comments = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_discussion_comments WHERE post_id = '$post_id'", ARRAY_A);

        if (count($all_comments) > 0) {
            $response['html'] = '';
            if (count($all_comments) < 10 || ($start + 10) >= count($get_all_comments)) {
                $response['discussCommentReachmax'] = true;
                foreach ($all_comments as $comment) {
                    $user_data = get_userdata($comment['user_id']);

                    $user_id = get_current_user_id();
                    $comment_id = $comment['id'];


                    /**
                     * check discuss comment like
                     */
                    $user_up_like = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_discuss_comment_likes WHERE user_id = '$user_id' AND comment_id = '$comment_id' AND like_type = 'up-like'", ARRAY_A);
                    $user_down_like = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_discuss_comment_likes WHERE user_id = '$user_id' AND comment_id = '$comment_id' AND like_type= 'down-like'", ARRAY_A);

                    $up_like = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_discuss_comment_likes WHERE comment_id = '$comment_id' AND like_type = 'up-like'", ARRAY_A);
                    $down_like = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_discuss_comment_likes WHERE comment_id = '$comment_id' AND like_type= 'down-like'", ARRAY_A);

                    /**
                     * comment delete button
                     */
                    $discuss_comment_delete = '<ul class="comment-like ps-0 mb-0 d-flex flex-row justify-content-end align-items-center pe-4">
                                <div class="dropdown">
                                    <button class="rz-secondary-color fz-14 bg-transparent p-0 d-block" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-ellipsis-h"></i>
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                        <li><a class="dropdown-item imit-font fz-14" href="#" id="delete-discuss-comment" data-comment_id="' . $comment_id . '">Delete</a></li>
                                    </ul>
                                </div>
                            </ul>';

                    /**
                     * reply form
                     */
                    $comment_reply_form = '<form id="add_discussion_comment_replay" class="mt-3 px-4 discuss-replay-form' . $comment['id'] . '" data-comment_id="' . $comment['id'] . '" style="display: none;">
                                            <div class="d-flex flex-row justify-content-between align-items-center">
                                                <div class="profile-image me-2" style="min-width: 42px;height: 42px;border-radius: 50%;">
                                                    <img src="' . getProfileImageById(get_current_user_id()) . '" alt="" style="width: 42px;height: 42px;border-radius: 50%;">
                                                </div>
                                                <textarea name="reply' . $comment['id'] . '" class="form-control imit-font fz-16" id="" cols="1" rows="1" placeholder="Add your reply..."></textarea>
                                            </div>
                                            <button type="submit" class="btn rz-bg-color text-white fz-16 fw-500 d-table ms-auto mt-2">Add Reply</button>
                                        </form>';

                    $response['html'] .= '<li class="comment-text list-unstyled pt-4" id="dis-comment' . $comment['id'] . '">
                    <div class="comment-header d-flex flex-row justify-content-between align-items-center px-4">
                        <div class="user-data d-flex flex-row justify-content-start align-items-center">
                            <div class="profile-image" style="width: 42px;height: 42px;border-radius: 50%;">
                                <img src="' . getProfileImageById($comment['user_id']) . '" alt="" style="width: 42px;height: 42px;border-radius: 50%;">
                            </div>
                            <div class="username ms-2">
                                <a href="' . site_url() . '/user/' . $user_data->user_login . '" class="imit-font fz-14 text-dark fw-500 d-block">' . getUserNameById($comment['user_id']) . '</a>
                                     
                                <p class="mb-0 imit-font fz-12 rz-secondary-color">' . $user_data->user_login . '</p>
                            </div>
                        </div>
                        <p class="mb-0 rz-secondary-color fz-14 fw-500 imit-font">Comment on: ' . date('g:i a F d, Y', strtotime($comment['created_at'])) . '</p>
                    </div>
                    <p class="fz-16 text-dark imit-font fz-16 my-4 px-4">' . $comment['comment_text'] . '</p>
        
        
                    <div class="d-flex flex-row justify-content-between align-items-center bg-white py-3" id="comment-action' . $comment['id'] . '">
                        
                        <ul class="comment-like ps-0 mb-0 d-flex flex-row justify-content-start align-items-center ps-4">
                            <li class="list-unstyled me-3"><a href="#" class="d-block up-like ' . ((count($user_up_like) > 0) ? 'active' : '') . '" id="comment-discuss-up" data-comment_id="' . $comment['id'] . '"><i class="fas fa-arrow-up"></i></a></li>
                            <li class="list-unstyled me-3"><span class="imit-font fz-16 fw-500 ' . ((intval(count($up_like)) - intval(count($down_like)) < 0) ? 'text-danger' : 'text-success') . '" id="dis-counter' . $comment_id . '">' . (intval(count($up_like)) - intval(count($down_like))) . '</span></li>
                            <li class="list-unstyled me-3"><a href="#" class="d-block down-like ' . ((count($user_down_like) > 0) ? 'active' : '') . '" id="comment-discuss-down" data-comment_id="' . $comment['id'] . '"><i class="fas fa-arrow-down"></i></a></li>
                            <li class="list-unstyled me-3"><a href="#" class="d-block reply imit-font fz-14 rz-color" data-comment_id="' . $comment['id'] . '" id="discuss-replay-button"><i class="fas fa-reply"></i> Reply</a></li>
                        </ul>
                        ' . ((is_user_logged_in() && $comment['user_id'] == get_current_user_id()) ? $discuss_comment_delete : '') . '
                    </div>
                </li>';
                }
            } else {
                $response['discussCommentReachmax'] = false;
                foreach ($all_comments as $comment) {
                    $user_data = get_userdata($comment['user_id']);

                    $user_id = get_current_user_id();
                    $comment_id = $comment['id'];


                    /**
                     * check discuss comment like
                     */
                    $user_up_like = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_discuss_comment_likes WHERE user_id = '$user_id' AND comment_id = '$comment_id' AND like_type = 'up-like'", ARRAY_A);
                    $user_down_like = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_discuss_comment_likes WHERE user_id = '$user_id' AND comment_id = '$comment_id' AND like_type= 'down-like'", ARRAY_A);

                    $up_like = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_discuss_comment_likes WHERE comment_id = '$comment_id' AND like_type = 'up-like'", ARRAY_A);
                    $down_like = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_discuss_comment_likes WHERE comment_id = '$comment_id' AND like_type= 'down-like'", ARRAY_A);

                    /**
                     * comment delete button
                     */
                    $discuss_comment_delete = '<ul class="comment-like ps-0 mb-0 d-flex flex-row justify-content-end align-items-center pe-4">
                                <div class="dropdown">
                                    <button class="rz-secondary-color fz-14 bg-transparent p-0 d-block" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-ellipsis-h"></i>
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                        <li><a class="dropdown-item imit-font fz-14" href="#" id="delete-discuss-comment" data-comment_id="' . $comment_id . '">Delete</a></li>
                                    </ul>
                                </div>
                            </ul>';

                    /**
                     * reply form
                     */
                    $comment_reply_form = '<form id="add_discussion_comment_replay" class="mt-3 px-4 discuss-replay-form' . $comment['id'] . '" data-comment_id="' . $comment['id'] . '" style="display: none;">
                                            <div class="d-flex flex-row justify-content-between align-items-center">
                                                <div class="profile-image me-2" style="min-width: 42px;height: 42px;border-radius: 50%;">
                                                    <img src="<?php echo getProfileImageById(get_current_user_id()); ?>" alt="" style="width: 42px;height: 42px;border-radius: 50%;">
                                                </div>
                                                <textarea name="reply' . $comment['id'] . '" class="form-control imit-font fz-16" id="" cols="1" rows="1" placeholder="Add your reply..."></textarea>
                                            </div>
                                            <button type="submit" class="btn rz-bg-color text-white fz-16 fw-500 d-table ms-auto mt-2">Add Reply</button>
                                        </form>';

                    $response['html'] .= '<li class="comment-text list-unstyled pt-4" id="dis-comment' . $comment['id'] . '">
                    <div class="comment-header d-flex flex-row justify-content-between align-items-center px-4">
                        <div class="user-data d-flex flex-row justify-content-start align-items-center">
                            <div class="profile-image" style="width: 42px;height: 42px;border-radius: 50%;">
                                <img src="' . getProfileImageById($comment['user_id']) . '" alt="" style="width: 42px;height: 42px;border-radius: 50%;">
                            </div>
                            <div class="username ms-2">
                                <a href="' . site_url() . '/user/' . $user_data->user_login . '" class="imit-font fz-14 text-dark fw-500 d-block">' . getUserNameById($comment['user_id']) . '</a>
                                     
                                <p class="mb-0 imit-font fz-12 rz-secondary-color">' . $user_data->user_login . '</p>
                            </div>
                        </div>
                        <p class="mb-0 rz-secondary-color fz-14 fw-500 imit-font">Comment on: ' . date('g:i a F d, Y', strtotime($comment['created_at'])) . '</p>
                    </div>
                    <p class="fz-16 text-dark imit-font fz-16 my-4 px-4">' . $comment['comment_text'] . '</p>
        
        
                    <div class="d-flex flex-row justify-content-between align-items-center bg-white py-3" id="comment-action' . $comment['id'] . '">
                        
                        <ul class="comment-like ps-0 mb-0 d-flex flex-row justify-content-start align-items-center ps-4">
                            <li class="list-unstyled me-3"><a href="#" class="d-block up-like ' . ((count($user_up_like) > 0) ? 'active' : '') . '" id="comment-discuss-up" data-comment_id="' . $comment['id'] . '"><i class="fas fa-arrow-up"></i></a></li>
                            <li class="list-unstyled me-3"><span class="imit-font fz-16 fw-500 ' . ((intval(count($up_like)) - intval(count($down_like)) < 0) ? 'text-danger' : 'text-success') . '" id="dis-counter' . $comment_id . '">' . (intval(count($up_like)) - intval(count($down_like))) . '</span></li>
                            <li class="list-unstyled me-3"><a href="#" class="d-block down-like ' . ((count($user_down_like) > 0) ? 'active' : '') . '" id="comment-discuss-down" data-comment_id="' . $comment['id'] . '"><i class="fas fa-arrow-down"></i></a></li>
                            <li class="list-unstyled me-3"><a href="#" class="d-block reply imit-font fz-14 rz-color" data-comment_id="' . $comment['id'] . '" id="discuss-replay-button"><i class="fas fa-reply"></i> Reply</a></li>
                        </ul>
                        ' . ((is_user_logged_in() && $comment['user_id'] == get_current_user_id()) ? $discuss_comment_delete : '') . '
                    </div>
                </li>';
                }
            }
        } else {
            $response['discussCommentReachmax'] = true;
        }

        echo json_encode($response);
    }
    die();
}
