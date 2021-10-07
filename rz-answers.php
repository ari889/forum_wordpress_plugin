<?php


/**
 * direct access not allowed
 */
if (!defined('ABSPATH')) {
    die(__('Direct access not allowed.', 'imit-recozilla'));
}

/**
 * add answer
 */
add_action('wp_ajax_imit_add_answer', function () {
    global $wpdb;

    $nonce = $_POST['nonce'];

    if (wp_verify_nonce($nonce, 'rz-add-answer-nonce')) {
        $answer = stripslashes($_POST['answer']);
        $post_id = sanitize_key($_POST['post_id']);
        $rz_answers = $wpdb->prefix . 'rz_answers';
        $user_id = get_current_user_id();

        /**
         * insert answer
         */
        if (!empty($answer) && !empty($post_id)) {

            /**
             * insert answer
             */
            $wpdb->insert($rz_answers, [
                'user_id' => $user_id,
                'post_id' => $post_id,
                'answer_text' => $answer,
                'status' => '1',
                'created_at' => wpDateTime(),
                'updated_at' => wpDateTime()
            ]);

            $answer_id = $wpdb->insert_id;


            /**
             * get all answers
             */
            $get_all_answers = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_answers WHERE post_id = '$post_id'", ARRAY_A);

            /**
             * id user already joined partner programme
             */
            $is_user_joined_programme = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_user_programs WHERE user_id = '$user_id' AND status = '1'", ARRAY_A);


            /**
             * check is user already a partner
             */
            if (count($is_user_joined_programme) > 0) {

                /**
                 * decide the point for first answer
                 */
                if (count($get_all_answers) >= 1) {
                    $point = 10;
                } else {
                    $point = 20;
                }

                /**
                 * insert point to point table
                 */
                $wpdb->insert($wpdb->prefix . 'rz_point_table', [
                    'user_id' => $user_id,
                    'content_id' => $answer_id,
                    'point_type' => 'answer',
                    'point_earn' => $point,
                    'created_at' => wpDateTime(),
                    'updated_at' => wpDateTime()
                ]);


                /**
                 * get user point from profile
                 */
                $get_point = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}rz_user_profile_data WHERE user_id = '$user_id'");

                /**
                 * if point empty then insert a new row
                 */
                $user_point = $get_point->points;
                if (empty($get_point)) {
                    $wpdb->insert($wpdb->prefix . 'rz_user_profile_data', [
                        'points' => $point,
                        'user_id' => $user_id,
                        'created_at' => wpDateTime(),
                        'updated_at' => wpDateTime()
                    ]);
                } else {
                    $wpdb->update($wpdb->prefix . 'rz_user_profile_data', [
                        'points' => ($user_point + $point),
                    ], ['user_id' => $user_id]);
                }
            }


            $response['activity_id'] = 'answer-' . $answer_id;
            $response['activity_url'] = get_permalink($post_id, false);
            $get_post_data = get_post($post_id);
            $response['text_message'] = getUserNameById(get_current_user_id()) . ' answered <span class="imit-font rz-color fw-500">' . $get_post_data->post_title . '</span></span>';
            $response['image_url'] = getProfileImageById(get_current_user_id());
            $response['sender_id'] = get_current_user_id();
            $response['receiver_id'] = intval($get_post_data->post_author);
            $response['content_id'] = $answer_id;
            $response['message_text'] = getUserNameById(get_current_user_id()) . ' answered on your qestion <strong>' . $get_post_data->post_title . '</strong>';
        }

        echo json_encode($response);
    }

    die();
});

/**
 * add admin menu
 */

function rz_all_answers()
{
    global $wpdb;

    $imit_recozilla_answers = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_answers ORDER BY id DESC", ARRAY_A);
    $imitRecozillaAnswers = new ImitManageAnswers($imit_recozilla_answers);
    $imitRecozillaAnswers->prepare_items();
    $imitRecozillaAnswers->display();

?>
    <div class="modal fade" id="view-answer-modal">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">

            </div>
        </div>
    </div>
    <?php
}

add_action('admin_menu', function () {
    /**
     * add main menu
     */
    add_menu_page('Manage answer', 'Manage answer', 'menage_options', 'rzManageAnswer', 'rz_manage_answer', 'dashicons-edit-page');

    /**
     * add league submenu
     */
    add_submenu_page('rzManageAnswer', 'All answers', 'All answers', 'manage_options', 'rzAllAnswers', 'rz_all_answers');
});


/**
 * get answer info for admin
 */
add_action('wp_ajax_rz_get_answer_info', function () {
    global $wpdb;
    $nonce = $_POST['nonce'];
    if (wp_verify_nonce($nonce, 'rz-get-answer-info-nonce')) {
        $answer_id = sanitize_key($_POST['answer_id']);

        $get_answer = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}rz_answers WHERE id = '{$answer_id}'");

        $user_data = get_userdata($get_answer->user_id);

        $post_data = get_post($get_answer->post_id);
    ?>
        <div class="modal-header">
            <h2 style="font-size: 24px;" class="m-0">Checked answer for "asdasdasd"</h2>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <div class="user-info d-flex flex-row justify-content-start align-items-center">
                <div class="profile-image rounded-circle" style="min-width: 42px;min-height: 42px;">
                    <img src="<?php echo getProfileImageById($get_answer->user_id); ?>" alt="" style="width: 42px;height: 42px;" class="rounded-circle">
                </div>
                <div class="user-data ms-2">
                    <a href="#" class="text-decoration-none text-dark"><?php echo getUserNameById($get_answer->user_id); ?></a>
                    <p class="mb-0 text-secondary"><?php echo $user_data->display_name; ?></p>
                </div>
            </div>
            <p class="my-3"><strong>Post title:</strong> <?php echo $post_data->post_title; ?></p>

            <h2 style="font-size: 20px;">Answer text:</h2>
            <p><?php echo $get_answer->answer_text; ?></p>

            <select name="status" id="answer-status" data-answer_id="<?php echo $get_answer->id; ?>">
                <option value="1" <?php if ($get_answer->status == '1') {
                                        echo 'selected';
                                    } ?>>Published</option>
                <option value="0" <?php if ($get_answer->status == '0') {
                                        echo 'selected';
                                    } ?>>Denied</option>
            </select>
        </div>
<?php
    }
    die();
});

/**
 * change answer status
 */
add_action('wp_ajax_rz_change_answer_status', 'imit_rz_change_answer_status');

function imit_rz_change_answer_status()
{
    global $wpdb;
    $nonce = $_POST['nonce'];
    if (wp_verify_nonce($nonce, 'rz-change-answer-status-nonce')) {

        $answer_id = sanitize_key($_POST['answer_id']);
        $status = sanitize_text_field($_POST['status']);
        $rz_point_table = $wpdb->prefix . 'rz_point_table';
        $rz_user_profile_data = $wpdb->prefix . 'rz_user_profile_data';
        $rz_partner_program = $wpdb->prefix . 'rz_user_programs';


        /**
         * get answer author id
         */
        $get_user_id_by_answer = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}rz_answers WHERE id = '{$answer_id}'");

        $user_id = $get_user_id_by_answer->user_id;

        $post_id = $get_user_id_by_answer->post_id;


        /**
         * check is it first answer or not (if first answer then get 20 pint or not then get 10 point)
         */
        $get_all_answers = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}rz_answers WHERE post_id = '$post_id' ORDER BY id ASC LIMIT 1", ARRAY_A);


        /**
         * check is user is an partner
         */
        $is_user_joined_programme = $wpdb->get_results("SELECT * FROM {$rz_partner_program} WHERE user_id = '$user_id' AND status = '1'", ARRAY_A);

        $get_point = $wpdb->get_row("SELECT * FROM {$rz_user_profile_data} WHERE user_id = '$user_id'");

        $user_point = $get_point->points;

        /**
         * decide the point
         */
        if ($get_all_answers->id == $answer_id) {
            $point = 20;
        } else {
            $point = 10;
        }

        /**
         * check is user a partner
         */
        if (count($is_user_joined_programme) > 0) {
            if ($status == '1') {

                /**
                 * add answer pint to point table
                 */
                $wpdb->insert($rz_point_table, [
                    'user_id' => $user_id,
                    'content_id' => $answer_id,
                    'point_type' => 'answer',
                    'point_earn' => $point,
                    'created_at' => wpDateTime(),
                    'updated_at' => wpDateTime()
                ]);

                /**
                 * add point to profile
                 */
                if (empty($get_point)) {
                    $wpdb->insert($rz_user_profile_data, [
                        'points' => $point,
                        'user_id' => $user_id,
                        'created_at' => wpDateTime(),
                        'updated_at' => wpDateTime()
                    ]);
                } else {
                    $wpdb->update($rz_user_profile_data, [
                        'points' => ($user_point + $point),
                    ], ['user_id' => $user_id]);
                }
            } else {

                /**
                 * if status not published then delete point that user earned
                 */
                $wpdb->delete($rz_point_table, [
                    'user_id' => $user_id,
                    'content_id' => $answer_id,
                    'point_type' => 'answer',
                ]);

                /**
                 * remove point from profile
                 */
                $wpdb->update($rz_user_profile_data, [
                    'points' => ($user_point - $point),
                ], ['user_id' => $user_id]);
            }
        }

        /**
         * update answer status
         */
        $wpdb->update($wpdb->prefix . 'rz_answers', [
            'status' => $status
        ], ['id' => $answer_id]);
    }
    die();
}


/**
 * delete answer
 */
add_action('wp_ajax_rz_delete_answer', function () {
    global $wpdb;
    $nonce = $_POST['nonce'];

    if (wp_verify_nonce($nonce, 'rz-delete-answer-nonce')) {
        $answer_id = sanitize_key($_POST['answer_id']);
        $user_id = get_current_user_id();


        if (!empty($answer_id) && !empty($user_id)) {
            $wpdb->query("DELETE FROM {$wpdb->prefix}rz_comment_reply_likes WHERE reply_id IN (SELECT id FROM {$wpdb->prefix}rz_comment_replays WHERE comment_id IN( SELECT id FROM {$wpdb->prefix}rz_answer_comments WHERE answer_id = '{$answer_id}'))");

            $wpdb->query("DELETE FROM {$wpdb->prefix}rz_comment_replays WHERE comment_id IN( SELECT id FROM {$wpdb->prefix}rz_answer_comments WHERE answer_id = '{$answer_id}')");

            $wpdb->query("DELETE FROM {$wpdb->prefix}rz_answer_comment_votes WHERE comment_id IN (SELECT id FROM {$wpdb->prefix}rz_answer_comments WHERE answer_id = '{$answer_id}')");

            $wpdb->delete($wpdb->prefix . 'rz_answer_comments', [
                'answer_id' => $answer_id
            ]);

            $wpdb->delete($wpdb->prefix . 'rz_vote', [
                'answer_id' => $answer_id
            ]);

            $wpdb->delete($wpdb->prefix . 'rz_answers', [
                'id' => $answer_id
            ]);

            exit('done');
        }
    }
    die();
});


/**
 * load more answers
 */
add_action('wp_ajax_nopriv_rz_get_questions_answers', 'load_more_questions_answers');
add_action('wp_ajax_rz_get_questions_answers', 'load_more_questions_answers');

function load_more_questions_answers()
{
    global $wpdb;
    $nonce = $_POST['nonce'];
    if (wp_verify_nonce($nonce, 'rz-get-questions-answers-nonce')) {
        $post_id = sanitize_key($_POST['post_id']);
        $start = sanitize_key($_POST['answer_start']);
        $get_first_answer = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}rz_answers WHERE post_id = '$post_id' ORDER BY id ASC LIMIT 1");
        $all_answers = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_answers WHERE post_id = '$post_id' AND status = '1' AND id != '{$get_first_answer->id}' ORDER BY id DESC LIMIT {$start}, 10", ARRAY_A);

        $get_all_answers = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_answers WHERE post_id = '$post_id' AND status = '1'", ARRAY_A);

        if (count($all_answers) > 0) {
            $response['html'] = '';
            if (count($all_answers) < 10 || ($start + 10) >= count($get_all_answers)) {
                $response['answerReachMax'] = true;
                foreach ($all_answers as $answer) {
                    $answer_id = $answer['id'];
                    $get_answer_user_data = get_userdata($answer['user_id']);


                    /**
                     * get vote data
                     */
                    $current_user_id = get_current_user_id();
                    $answer_id = $answer['id'];
                    $get_upvote = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_vote WHERE user_id = '$current_user_id' AND answer_id='$answer_id' AND vote_type='up-vote'", ARRAY_A);
                    $get_downvote = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_vote WHERE user_id = '$current_user_id' AND answer_id='$answer_id' AND vote_type='down-vote'", ARRAY_A);

                    $count_upvote = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_vote WHERE answer_id='$answer_id' AND vote_type='up-vote'", ARRAY_A);

                    /**
                     * delete dropdown
                     */
                    $delete_dropdown = '<div class="dropdown">
                        <button class="mit-font border-0 fz-16 text-dark fw-500 p-0 bg-transparent d-block" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-ellipsis-h"></i>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                            <li><a class="dropdown-item" href="#" id="delete-answer" data-answer_id="' . $answer_id . '">Delete</a></li>
                        </ul>
                    </div>';


                    /**
                     * get all comments
                     */
                    $get_all_comments = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_answer_comments WHERE answer_id = '$answer_id' ORDER BY id DESC", ARRAY_A);
                    $comments_data = '';

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

                        $comments_data .= '<li class="comment-list list-unstyled" id="comment' . $comment_id . '">
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
                                </ul>
                                ' . ((is_user_logged_in() && $comment['user_id'] == get_current_user_id()) ? $comment_delete : '') . '
                            </div>
                    </li>';
                    }

                    /**
                     * get all comment list
                     */
                    $comment_list = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_answer_comments WHERE answer_id = '$answer_id'", ARRAY_A);

                    /**
                     * load more comment
                     */
                    $load_more_comment_button = '<a href="#" class="btn load-more-answer-comment d-table mx-auto my-3" data-answer_id="' . $answer['id'] . '" data-start="3" id="load-more-comment">
                            <div class="d-flex flex-row justify-content-center align-items-center">
                                <span>Load More</span>
                                <div class="spinner-grow ms-2" role="status" id="comment-load-more-loader' . $answer['id'] . '" style="display: none;">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </a>';


                    /**
                     * answer comment form
                     */
                    $answer_comment_form = '<div id="answer-comment-form' . $answer['id'] . '">
                    <form id="answer-comment-form" class="comment-form d-flex flex-row justify-content-between align-items-start" data-answer_id="' . $answer['id'] . '">
                        <div class="user-avatar">
                            <img src="' . getProfileImageById(get_current_user_id()) . '" alt="" style="max-width: 42px !important;">
                        </div>
                        <div class="d-flex flex-sm-row flex-column justify-content-between align-items-center w-100 ms-2">
                            <textarea name="answer-comment' . $answer['id'] . '" id="comment-textarea" cols="1" rows="1" class="imit-font form-control fz-14 me-sm-2 me-0" placeholder="Add your comment..." style="height: 38px;"></textarea>
                            <button type="submit" class="btn rz-bg-color text-white imit-font fz-14 d-flex flex-row justify-content-center align-items-center ms-auto" style="min-width: 120px;"><span class="me-2">Add</span> <span>Comment</span></button>
                        </div>
                    </form>
                </div>';


                    $response['html'] .= '<li class="blog-list list-unstyled mt-4" id="answer' . $answer_id . '">
                    <div class="card rz-br rz-border">
                        <div class="card-body">
                            <div class="blog-list-header d-flex flex-sm-row flex-column justify-content-between align-items-start align-items-sm-center">
                                <div class="user-info d-flex flex-row justify-content-start align-items-center">
                                    <div class="profile-image">
                                        <img src="' . getProfileImageById($answer['user_id']) . '" alt="">
                                    </div>
                                    <div class="userdetails ms-2">
                                        <a href="' . site_url() . '/user/' . $get_answer_user_data->user_login . '" class="imit-font fz-14 text-dark fw-500 d-block">' . getUserNameById($answer['user_id']) . '</a>
                                        <p class="mb-0 designation imit-font fw-400 rz-secondary-color fz-12">' . $get_answer_user_data->user_login . '</p>
                                    </div>
                                </div>
                                <p class="mb-0 imit-font fz-14 rz-secondary-color fw-400 mt-sm-0 mt-2">Answered on: ' . date('g:i a F d, Y', strtotime($answer['created_at'])) . '</p>
                            </div>
                            <div class="blog-body">
                                <p class="imit-font fz-16 answer-text px-3" style="line-height: 30px;margin: 30px 0px;">' . $answer['answer_text'] . '</p>
                            </div>
                        </div>
                        <div class="card-footer border-top-0 d-flex flex-row justify-content-between align-items-center">
                            <ul class="blog-footer-icons ps-0 mb-0 d-flex flex-row justify-content-start align-items-center" id="vote' . $answer['id'] . '">
                                <li class="blog-footer-list list-unstyled">
                                    <a href="#" class="prev imit-font fz-12 fw-400 me-3 text-decoration-none ' . ((count($get_upvote) > 0) ? 'active' : '') . '" ' . ((is_user_logged_in()) ? 'id="up-vote" data-answer_id="' . $answer['id'] . '"' : 'data-bs-toggle="modal" data-bs-target="#login-modal"') . '><i class="fas fa-arrow-up"></i></a>
                                </li>
                            
                                <li class="blog-footer-list list-unstyled">
                                    <p class="counter imit-font fz-16 fw-500 my-auto text-dark me-3" id="counter' . $answer_id . '">' . count($get_upvote) . '</p>
                                </li>
    
                                <li class="blog-footer-list list-unstyled">
                                    <a href="#" class="next imit-font fz-12 fw-400 me-3 text-decoration-none ' . ((count($get_downvote) > 0) ? 'active' : '') . '" ' . ((is_user_logged_in()) ? 'id="down-vote" data-answer_id="' . $answer['id'] . '"' : 'data-bs-toggle="modal" data-bs-target="#login-modal"') . '><i class="fas fa-arrow-down"></i></a>
                                </li>
                                <li class="blog-footer-list list-unstyled">
                                    <a href="#" class="imit-font fz-14 fw-400 me-3 text-decoration-none text-dark" id="comment-expand" data-answer_id="' . $answer['id'] . '"><i class="fas fa-comments"></i> Comments</a>
                                </li>
                            </ul>
                            ' . ((is_user_logged_in() && $answer['user_id'] == get_current_user_id()) ? $delete_dropdown : '') . '
                        </div>
                        <div class="comment-section">
                            ' . ((is_user_logged_in()) ? $answer_comment_form : '') . '
                            <ul class="comments mb-0 ps-0 mb-0" id="comment' . $answer['id'] . '">
                                ' . $comments_data . '
                            </ul>
                            ' . ((count($comment_list) > 3) ? $load_more_comment_button : '') . '
                        </div>
                    </div>
                </li>';
                }
            } else {
                $response['answerReachMax'] = false;
                foreach ($all_answers as $answer) {
                    $answer_id = $answer['id'];
                    $get_answer_user_data = get_userdata($answer['user_id']);


                    /**
                     * get vote data
                     */
                    $current_user_id = get_current_user_id();
                    $answer_id = $answer['id'];
                    $get_upvote = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_vote WHERE user_id = '$current_user_id' AND answer_id='$answer_id' AND vote_type='up-vote'", ARRAY_A);
                    $get_downvote = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_vote WHERE user_id = '$current_user_id' AND answer_id='$answer_id' AND vote_type='down-vote'", ARRAY_A);

                    $count_upvote = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_vote WHERE answer_id='$answer_id' AND vote_type='up-vote'", ARRAY_A);

                    /**
                     * delete dropdown
                     */
                    $delete_dropdown = '<div class="dropdown">
                        <button class="mit-font border-0 fz-16 text-dark fw-500 p-0 bg-transparent d-block" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-ellipsis-h"></i>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                            <li><a class="dropdown-item" href="#" id="delete-answer" data-answer_id="' . $answer_id . '">Delete</a></li>
                        </ul>
                    </div>';


                    /**
                     * get all comments
                     */
                    $get_all_comments = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_answer_comments WHERE answer_id = '$answer_id' ORDER BY id DESC", ARRAY_A);
                    $comments_data = '';

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

                        $comments_data .= '<li class="comment-list list-unstyled" id="comment' . $comment_id . '">
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
                                        <span class="counter imit-font fz-14 fw-500 me-3 text-dark d-block" id="comment-counter' . $comment['id'] . '">' . count($count_upvote_comment) . '</span>
                                    </li>
                                    <li class="comment-action-list list-unstyled">
                                        <a href="#" class="rz-next fz-14 text-white text-decoration-none me-3 d-block ' . ((count($get_comment_all_down_vote) > 0) ? 'active' : '') . '" ' . ((is_user_logged_in()) ? 'id="down-vote-comment" data-comment_id="' . $comment['id'] . '"' : 'data-bs-toggle="modal" data-bs-target="#login-modal"') . '><i class="fas fa-arrow-down"></i></a>
                                    </li>
                                </ul>
                                ' . ((is_user_logged_in() && $comment['user_id'] == get_current_user_id()) ? $comment_delete : '') . '
                            </div>
                    </li>';
                    }

                    /**
                     * get all comment list
                     */
                    $comment_list = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_answer_comments WHERE answer_id = '$answer_id'", ARRAY_A);

                    /**
                     * load more comment
                     */
                    $load_more_comment_button = '<a href="#" class="btn load-more-answer-comment d-table mx-auto my-3" data-answer_id="' . $answer['id'] . '" data-start="3" id="load-more-comment">
                            <div class="d-flex flex-row justify-content-center align-items-center">
                                <span>Load More</span>
                                <div class="spinner-grow ms-2" role="status" id="comment-load-more-loader' . $answer['id'] . '" style="display: none;">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </a>';


                    /**
                     * answer comment form
                     */
                    $answer_comment_form = '<div id="answer-comment-form' . $answer['id'] . '" style="display: none;">
                                                <form id="answer-comment-form" class="comment-form d-flex flex-row justify-content-center align-items-center" data-answer_id="' . $answer['id'] . '">
                                                    <div class="user-avatar">
                                                        <img src="' . getProfileImageById(get_current_user_id()) . '" alt="" style="max-width: 42px !important;">
                                                    </div>
                                                    <textarea name="answer-comment' . $answer['id'] . '" id="" cols="1" rows="1" class="imit-font form-control fz-14 mx-2" placeholder="Add your comment..." style="height: 38px;"></textarea>
                                                    <button type="submit" class="btn rz-bg-color text-white imit-font fz-14 d-flex flex-row justify-content-center align-items-center" style="min-width: 120px;"><span>Add</span> <span>Comment</span></button>
                                                </form>
                                            </div>';


                    $response['html'] .= '<li class="blog-list list-unstyled mt-4" id="answer' . $answer_id . '">
                    <div class="card rz-br rz-border">
                        <div class="card-body">
                            <div class="blog-list-header d-flex flex-row justify-content-between align-items-center">
                                <div class="user-info d-flex flex-row justify-content-start align-items-center">
                                    <div class="profile-image">
                                        <img src="' . getProfileImageById($answer['user_id']) . '" alt="">
                                    </div>
                                    <div class="userdetails ms-2">
                                        <a href="' . site_url() . '/user/' . $get_answer_user_data->user_login . '" class="imit-font fz-14 text-dark fw-500 d-block">' . getUserNameById($answer['user_id']) . '</a>
                                        <p class="mb-0 designation imit-font fw-400 rz-secondary-color fz-12">' . $get_answer_user_data->user_login . '</p>
                                    </div>
                                </div>
                                <p class="mb-0 imit-font fz-14 rz-secondary-color fw-400">Answered on: ' . date('g:i a F d, Y', strtotime($answer['created_at'])) . '</p>
                            </div>
                            <div class="blog-body">
                                <p class="imit-font fz-16 answer-text px-3" style="line-height: 30px;margin: 30px 0px;">' . $answer['answer_text'] . '</p>
                            </div>
                        </div>
                        <div class="card-footer border-top-0 d-flex flex-row justify-content-between align-items-center">
                            <ul class="blog-footer-icons ps-0 mb-0 d-flex flex-row justify-content-start align-items-center" id="vote' . $answer['id'] . '">
                                <li class="blog-footer-list list-unstyled">
                                    <a href="#" class="prev imit-font fz-12 fw-400 me-3 text-decoration-none ' . ((count($get_upvote) > 0) ? 'active' : '') . '" ' . ((is_user_logged_in()) ? 'id="up-vote" data-answer_id="' . $answer['id'] . '"' : 'data-bs-toggle="modal" data-bs-target="#login-modal"') . '><i class="fas fa-arrow-up"></i></a>
                                </li>
                            
                                <li class="blog-footer-list list-unstyled">
                                    <p class="counter imit-font fz-16 fw-500 my-auto text-dark me-3" id="counter' . $answer_id . '">' . count($get_upvote) . '</p>
                                </li>
    
                                <li class="blog-footer-list list-unstyled">
                                    <a href="#" class="next imit-font fz-12 fw-400 me-3 text-decoration-none ' . ((count($get_downvote) > 0) ? 'active' : '') . '" ' . ((is_user_logged_in()) ? 'id="down-vote" data-answer_id="' . $answer['id'] . '"' : 'data-bs-toggle="modal" data-bs-target="#login-modal"') . '><i class="fas fa-arrow-down"></i></a>
                                </li>
                                <li class="blog-footer-list list-unstyled">
                                    <a href="#" class="imit-font fz-14 fw-400 me-3 text-decoration-none text-dark" id="comment-expand" data-answer_id="' . $answer['id'] . '"><i class="fas fa-comments"></i> Comments</a>
                                </li>
                            </ul>
                            ' . ((is_user_logged_in() && $answer['user_id'] == get_current_user_id()) ? $delete_dropdown : '') . '
                        </div>
                        <div class="comment-section">
                            ' . ((is_user_logged_in()) ? $answer_comment_form : '') . '
                            <ul class="comments mb-0 ps-0 mb-0" id="comment' . $answer['id'] . '">
                                ' . $comments_data . '
                            </ul>
                            ' . ((count($comment_list) > 3) ? $load_more_comment_button : '') . '
                        </div>
                    </div>
                </li>';
                }
            }
        } else {
            $response['answerReachMax'] = true;
        }

        echo json_encode($response);
    }
    die();
}
