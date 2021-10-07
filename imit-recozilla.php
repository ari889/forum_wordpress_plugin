<?php


/**
 * Plugin Name:       Imit Recozilla
 * Plugin URI:        https://recozilla.com
 * Description:       Recozilla Multivendor plugin.
 * Version:           2.0
 * Requires at least: 5.2
 * Requires PHP:      5.6
 * Author:            Ideasy Corp.
 * Author URI:        https://ideasymind.com
 * License:           GPL v2 or later
 * License URI:       https://ideasymind.com
 * Text Domain:       imit-recozilla
 * Domain Path:       /languages
 */

define('IMIT_RZ_DB_VERSION', '1.0');
require_once 'class.imitMenegeProgram.php';
require_once 'class.imitManageAnswers.php';

if (!defined('ABSPATH')) {
    die(__('Direct access not allowed.', 'imit-recozilla'));
}

/**
 * create database when active plugin
 */
function imit_rz_init()
{
    global $wpdb;

    /**
     * add image cap to author
     */
    // $user_role = 'author'; // Change user role here
    // $contributor = get_role($user_role);
    // $contributor->add_cap('upload_files');

    $rz_answers = $wpdb->prefix . 'rz_answers';
    $rz_vote = $wpdb->prefix . 'rz_vote';
    $rz_answer_comment = $wpdb->prefix . 'rz_answer_comments';
    $rz_answer_comment_vote = $wpdb->prefix . 'rz_answer_comment_votes';
    $rz_hashtags = $wpdb->prefix . 'rz_hashtags';
    $rz_comment_replays = $wpdb->prefix . 'rz_comment_replays';
    $rz_comment_replys_likes = $wpdb->prefix . 'rz_comment_reply_likes';
    $rz_discussion_comment_table = $wpdb->prefix . 'rz_discussion_comments';
    $rz_discuss_comment_likes = $wpdb->prefix . 'rz_discuss_comment_likes';
    $rz_discuss_comment_replays = $wpdb->prefix . 'rz_discuss_comment_replays';
    $rz_discuss_reply_likes = $wpdb->prefix . 'rz_discuss_reply_likes';
    $rz_discuss_likes = $wpdb->prefix . 'rz_discuss_likes';
    $rz_followers = $wpdb->prefix . 'rz_followers';
    $rz_user_profile_data = $wpdb->prefix . 'rz_user_profile_data';
    $rz_user_work = $wpdb->prefix . 'rz_user_work';
    $rz_user_education = $wpdb->prefix . 'rz_user_education';
    $rz_partner_program = $wpdb->prefix . 'rz_user_programs';
    $rz_point_table = $wpdb->prefix . 'rz_point_table';
    $rz_quizzes = $wpdb->prefix . 'rz_quizzes';
    $rz_quiz_questions = $wpdb->prefix . 'rz_quiz_questions';
    $rz_following_questions = $wpdb->prefix . 'rz_following_questions';
    $rz_following_tags = $wpdb->prefix . 'rz_following_tags';
    $rz_quiz_result = $wpdb->prefix . 'rz_quiz_result';

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    $sql[] = "CREATE TABLE {$rz_answers} (
        id INT (11) NOT NULL AUTO_INCREMENT,
        user_id INT (11) NOT NULL,
        post_id INT (11) NOT NULL,
        answer_text VARCHAR (5000) NOT NULL,
        status VARCHAR (100) DEFAULT ('1'),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    );";

    $sql[] = "CREATE TABLE {$rz_vote} (
        id INT (11) NOT NULL AUTO_INCREMENT,
        user_id INT (11) NOT NULL,
        answer_id INT (11) NOT NULL,
        vote_type ENUM ('up-vote', 'down-vote') NOT NULL,
        status VARCHAR (100) DEFAULT ('1'),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    );";


    $sql[] = "CREATE TABLE {$rz_answer_comment} (
        id INT (11) NOT NULL AUTO_INCREMENT,
        user_id INT (11) NOT NULL,
        answer_id INT (11) NOT NULL,
        comment_text VARCHAR (5000) NOT NULL,
        status VARCHAR (100) DEFAULT ('1'),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    );";

    $sql[] = "CREATE TABLE {$rz_answer_comment_vote} (
        id INT (11) NOT NULL AUTO_INCREMENT,
        user_id INT (11) NOT NULL,
        comment_id INT (11) NOT NULL,
        vote_type ENUM ('up-vote', 'down-vote') NOT NULL,
        status VARCHAR (100) DEFAULT ('1'),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    );";


    $sql[] = "CREATE TABLE {$rz_hashtags} (
        id INT (11) NOT NULL AUTO_INCREMENT,
        content_id INT (11) NOT NULL,
        content_type ENUM ('question', 'answers') NOT NULL,
        hashtag VARCHAR (250) NOT NULL,
        status VARCHAR (100) DEFAULT ('1'),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    );";

    $sql[] = "CREATE TABLE {$rz_comment_replays} (
        id INT (11) NOT NULL AUTO_INCREMENT,
        user_id INT (11) NOT NULL,
        comment_id INT (11) NOT NULL,
        replay_text VARCHAR (5000) NOT NULL,
        status VARCHAR (100) DEFAULT ('1'),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    );";


    $sql[] = "CREATE TABLE {$rz_comment_replys_likes} (
        id INT (11) NOT NULL AUTO_INCREMENT,
        user_id INT (11) NOT NULL,
        reply_id INT (11) NOT NULL,
        reply_type ENUM('up-reply', 'down-reply') NOT NULL,
        status VARCHAR (100) DEFAULT ('1'),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    );";

    $sql[] = "CREATE TABLE {$rz_discussion_comment_table} (
        id INT (11) NOT NULL AUTO_INCREMENT,
        user_id INT (11) NOT NULL,
        post_id INT (11) NOT NULL,
        comment_text VARCHAR(5000) NOT NULL,
        status VARCHAR (100) DEFAULT ('1'),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    );";

    $sql[] = "CREATE TABLE {$rz_discuss_comment_likes} (
        id INT (11) NOT NULL AUTO_INCREMENT,
        user_id INT (11) NOT NULL,
        comment_id INT (11) NOT NULL,
        like_type ENUM ('up-like', 'down-like') NOT NULL,
        status VARCHAR (100) DEFAULT ('1'),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    );";

    $sql[] = "CREATE TABLE {$rz_discuss_comment_replays} (
        id INT (11) NOT NULL AUTO_INCREMENT,
        user_id INT (11) NOT NULL,
        comment_id INT (11) NOT NULL,
        replay_text VARCHAR(5000) NOT NULL,
        status VARCHAR (100) DEFAULT ('1'),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    );";

    $sql[] = "CREATE TABLE {$rz_discuss_reply_likes} (
        id INT (11) NOT NULL AUTO_INCREMENT,
        user_id INT (11) NOT NULL,
        reply_id INT (11) NOT NULL,
        reply_type ENUM('up-reply', 'down-reply') NOT NULL,
        status VARCHAR (100) DEFAULT ('1'),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    );";

    $sql[] = "CREATE TABLE {$rz_discuss_likes} (
        id INT (11) NOT NULL AUTO_INCREMENT,
        user_id INT (11) NOT NULL,
        post_id INT (11) NOT NULL,
        like_type ENUM('up-like', 'down-like') NOT NULL,
        status VARCHAR (100) DEFAULT ('1'),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    );";

    $sql[] = "CREATE TABLE {$rz_followers} (
        id INT (11) NOT NULL AUTO_INCREMENT,
        sender_id INT (11) NOT NULL,
        receiver_id INT (11) NOT NULL,
        status VARCHAR (100) DEFAULT ('1'),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    );";

    $sql[] = "CREATE TABLE {$rz_user_profile_data} (
        id INT (11) NOT NULL AUTO_INCREMENT,
        user_id INT (11) NOT NULL,
        profile_image VARCHAR (250),
        occupation VARCHAR (250),
        phone_number VARCHAR (250),
        whatsapp_alert ENUM('yes', 'no') NOT NULL,
        country VARCHAR(250),
        city VARCHAR(250),
        languages VARCHAR(250),
        notification_seen BOOLEAN DEFAULT true NOT NULL,
        skill VARCHAR(250),
        banner_status ENUM('active', 'disabled') DEFAULT ('active'),
        points INT (11) DEFAULT (0),
        status VARCHAR (100) DEFAULT ('1'),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    );";

    $sql[] = "CREATE TABLE {$rz_user_work} (
        id INT (11) NOT NULL AUTO_INCREMENT,
        user_id INT (11) NOT NULL,
        company VARCHAR (250) NOT NULL,
        position VARCHAR (250) NOT NULL,
        start_year VARCHAR (250) NOT NULL,
        end_year VARCHAR (250) NOT NULL,
        status VARCHAR (100) DEFAULT ('1'),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    );";

    $sql[] = "CREATE TABLE {$rz_user_education} (
        id INT (11) NOT NULL AUTO_INCREMENT,
        user_id INT (11) NOT NULL,
        college VARCHAR (250) NOT NULL,
        concentrations VARCHAR (250) NOT NULL,
        start_year VARCHAR (250) NOT NULL,
        end_year VARCHAR (250) NOT NULL,
        status VARCHAR (100) DEFAULT ('1'),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    );";

    $sql[] = "CREATE TABLE {$rz_partner_program} (
        id INT (11) NOT NULL AUTO_INCREMENT,
        user_id INT (11) NOT NULL,
        request_text VARCHAR (5000) NOT NULL,
        status VARCHAR (100) DEFAULT ('1'),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    );";

    $sql[] = "CREATE TABLE {$rz_point_table} (
        id INT (11) NOT NULL AUTO_INCREMENT,
        user_id INT (11) NOT NULL,
        content_id INT (11) NOT NULL,
        point_type ENUM('answer', 'post', 'up-vote') NOT NULL,
        point_earn INT (11) NOT NULL,
        status VARCHAR (100) DEFAULT ('1'),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    );";


    $sql[] = "CREATE TABLE {$rz_quizzes} (
        id INT (11) NOT NULL AUTO_INCREMENT,
        quiz_name VARCHAR (250) NOT NULL,
        status VARCHAR (100) DEFAULT ('1'),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    );";


    $sql[] = "CREATE TABLE {$rz_quiz_questions} (
        id INT (11) NOT NULL AUTO_INCREMENT,
        quiz_id INT (11) NOT NULL,
        question VARCHAR (250) NOT NULL,
        answers VARCHAR (5000) NOT NULL,
        correct_answer VARCHAR (250) NOT NULL,
        status VARCHAR (100) DEFAULT ('1'),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    );";

    $sql[] = "CREATE TABLE {$rz_following_questions} (
        id INT (11) NOT NULL AUTO_INCREMENT,
        user_id INT (11) NOT NULL,
        question_id INT (11) NOT NULL,
        status VARCHAR (100) DEFAULT ('1'),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    );";


    $sql[] = "CREATE TABLE {$rz_following_tags} (
        id INT (11) NOT NULL AUTO_INCREMENT,
        user_id INT (11) NOT NULL,
        term_id INT (11) NOT NULL,
        status VARCHAR (100) DEFAULT ('1'),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    );";

    $sql[] = "CREATE TABLE {$rz_quiz_result} (
        id INT (11) NOT NULL AUTO_INCREMENT,
        quiz_id INT (11) NOT NULL,
        user_id INT (11) NOT NULL,
        score FLOAT (11, 2) NOT NULL,
        timeSpent VARCHAR(250) NOT NULL,
        quiz_status VARCHAR (250),
        status VARCHAR (100) DEFAULT ('1'),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    );";



    dbDelta($sql);


    add_option('imit_rz_db_version', IMIT_RZ_DB_VERSION);
}

register_activation_hook(__FILE__, 'imit_rz_init');


/**
 * theme support
 */
function imit_theme_support()
{

    /**
     * register post type
     */
    register_post_type('rz_post_question', [
        'public' => true,
        'labels' => [
            'name' => __('Question', 'imit-recozilla'),
            'all_items' => __('All questions', 'imit-recozilla'),
            'add_new_items' => __('Add new question', 'imit-recozilla'),
        ],
        'menu_icon' => 'dashicons-edit-large',
        'supports' => ['title', 'editor', 'thumbnail'],
    ]);

    register_taxonomy('question_tags', 'rz_post_question', [
        'public'       => true,
    ]);

    register_taxonomy('question_category', 'rz_post_question', [
        'public' => true,
        'hierarchical' => true,
        'default_term' => [
            'name' => 'Uncategorised',
            'slug' => 'uncategorised',
        ]
    ]);

    /**
     * register discuss post type
     */
    register_post_type('rz_discussion', [
        'public' => true,
        'labels' => [
            'name' => __('Discussion', 'imit-recozilla'),
            'all_items' => __('All Discussion', 'imit-recozilla'),
            'add_new_items' => __('Add new discussion', 'imit-recozilla')
        ],
        'menu_icon' => 'dashicons-welcome-write-blog',
        'supports' => ['title', 'editor', 'thumbnail']
    ]);

    register_taxonomy('discussion_tags', 'rz_discussion', [
        'public' => true
    ]);


    /**
     * register new dairy page
     */
    register_post_type('rz_dairy', [
        'public' => true,
        'labels' => [
            'name' => __('Dairy', 'imit-recozilla'),
            'all_items' => __('All dairy', 'imit-recozilla'),
            'add_new_items' => __('Add new Dairy', 'imit-recozilla'),
        ],
        'menu_icon' => 'dashicons-welcome-write-blog',
        'supports' => ['editor']
    ]);

    if (!current_user_can('administrator') && !is_admin()) {
        show_admin_bar(false);
    }
}
add_action('after_setup_theme', 'imit_theme_support');


/**
 * add css js file here
 */
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('imit-bootstrap', PLUGINS_URL('css/bootstrap.min.css', __FILE__));
    wp_enqueue_style('imit-fontawesome', PLUGINS_URL('css/all.min.css', __FILE__));
    wp_enqueue_style('imit-stylesheet', PLUGINS_URL('css/style.css', __FILE__));

    wp_enqueue_script('jQuery', PLUGINS_URL('js/jquery-3.6.0.min.js', __FILE__), [], true, true);
    wp_enqueue_script('imit-bootstrap', PLUGINS_URL('js/bootstrap.bundle.min.js', __FILE__), ['jQuery'], true, true);
    wp_enqueue_script('imit-sweetalert', PLUGINS_URL('js/sweetalert.min.js', __FILE__), ['jQuery'], true, true);
    wp_enqueue_script('imit-recozilla', PLUGINS_URL('js/recozilla.js', __FILE__), ['imit-message-script'], true, true);

    global $post;
    $post_type = $post->post_type;

    if (is_page('discuss')) {
        wp_enqueue_script('imit-rz-discuss', PLUGINS_URL('js/rz-discuss.js', __FILE__), ['jQuery'], true, true);
    } else if (is_page('questions') || $post_type == 'rz_post_question') {
        wp_enqueue_script('imit-rz-question', PLUGINS_URL('js/rz-question.js', __FILE__), ['jQuery'], true, true);
    } else if (is_page('user')) {
        wp_enqueue_script('imit-rz-profile', PLUGINS_URL('js/rz-profile.js', __FILE__), ['jQuery'], true, true);
    } else if (is_page('users')) {
        wp_enqueue_script('imit-rz-users', PLUGINS_URL('js/rz-users.js', __FILE__), ['jQuery'], true, true);
    } else if (is_page('tags')) {
        wp_enqueue_script('imit-rz-tags', PLUGINS_URL('js/rz-tags.js', __FILE__), ['jQuery'], true, true);
    } else if (is_page('notifications')) {
        wp_enqueue_script('imit-rz-notification', PLUGINS_URL('js/rz-notification.js', __FILE__), ['jQuery'], true, true);
    }

    /**
     * get full notification nonce
     */
    $rz_get_full_notification_nonce = wp_create_nonce('rz-get-full-notification-nonce');
    wp_localize_script('imit-rz-notification', 'rzGetFullNotification', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_get_full_notification_nonce' => $rz_get_full_notification_nonce
    ]);

    /**
     * login user
     */
    $recozilla_login_nonce = wp_create_nonce('rz-login-nonce');
    wp_localize_script('imit-recozilla', 'rzLogin', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'recozilla_login_nonce' => $recozilla_login_nonce
    ]);

    /**
     * register user
     */
    $recozilla_register_nonce = wp_create_nonce('rz-registe-nonce');
    wp_localize_script('imit-recozilla', 'rzRegister', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'recozilla_register_nonce' => $recozilla_register_nonce
    ]);

    /**
     * add question
     */
    $recozilla_add_question_nonce = wp_create_nonce('rz-add-question-nonce');
    wp_localize_script('imit-recozilla', 'rzAddQuestion', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'recozilla_add_question_nonce' => $recozilla_add_question_nonce
    ]);

    /**
     * add answers
     */
    $recozilla_add_answer_nonce = wp_create_nonce('rz-add-answer-nonce');
    wp_localize_script('imit-recozilla', 'rzAddAnswer', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'recozilla_add_answer_nonce' => $recozilla_add_answer_nonce
    ]);

    /**
     * add vote
     */
    $recozilla_add_vote_nonce = wp_create_nonce('rz-add-vote-nonce');
    wp_localize_script('imit-recozilla', 'rzAddVote', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'recozilla_add_vote_nonce' => $recozilla_add_vote_nonce
    ]);

    /**
     * add comment on answer
     */
    $rz_add_comment_on_answer_nonce = wp_create_nonce('rz-add-comment-on-answer');
    wp_localize_script('imit-recozilla', 'rzAddAnswerOnComment', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_add_comment_on_answer_nonce' => $rz_add_comment_on_answer_nonce
    ]);

    /**
     * add comment up vote
     */
    $rz_add_comment_on_up_vote_nonce = wp_create_nonce('rz-add-comment-up-vote');
    wp_localize_script('imit-recozilla', 'rzAddCommentUpVote', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_add_comment_on_up_vote_nonce' => $rz_add_comment_on_up_vote_nonce
    ]);


    /**
     * user activity view nonce
     */
    $rz_activity_view_nonce = wp_create_nonce('rz-activity-view');
    wp_localize_script('imit-recozilla', 'rzActivity', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_activity_view_nonce' => $rz_activity_view_nonce
    ]);

    /**
     * user hashtag suggestion
     */
    $rz_hashtag_show_nonce = wp_create_nonce('rz-hashtag-show');
    wp_localize_script('imit-recozilla', 'rzHashtagShow', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_hashtag_show_nonce' => $rz_hashtag_show_nonce
    ]);

    /**
     * add comment replay
     */
    $rz_add_comment_replay_nonce = wp_create_nonce('rz-add-comment-replay');
    wp_localize_script('imit-recozilla', 'rzCommentReplay', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_add_comment_replay_nonce' => $rz_add_comment_replay_nonce
    ]);

    /**
     * like replay
     */
    $rz_add_replay_like_none = wp_create_nonce('rz-add-replay-like');
    wp_localize_script('imit-recozilla', 'rzAddReplayLike', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_add_replay_like_none' => $rz_add_replay_like_none
    ]);

    /**
     * add discussion
     */
    $rz_add_discussion = wp_create_nonce('rz-add-add-discussion');
    wp_localize_script('imit-recozilla', 'rzAddDiscussion', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_add_discussion' => $rz_add_discussion
    ]);

    /**
     * add comment on discussion
     */
    $rz_add_comment_in_discussion_nonce = wp_create_nonce('rz-add-comment-on-discussion');
    wp_localize_script('imit-recozilla', 'rzAddCommentDis', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_add_comment_in_discussion_nonce' => $rz_add_comment_in_discussion_nonce
    ]);

    /**
     * like discussion comment
     */
    $rz_like_discussion_comment = wp_create_nonce('rz-like-discussion-comment');
    wp_localize_script('imit-recozilla', 'rzLikeDislikeDiscussComment', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_like_discussion_comment' => $rz_like_discussion_comment
    ]);

    /**
     * add replay on discussion comment
     */
    $rz_add_replay_on_discussion_nonce = wp_create_nonce('rz-add-replay-on-discussion-comment');
    wp_localize_script('imit-recozilla', 'rzAddReplayOnDiscussComment', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_add_replay_on_discussion_nonce' => $rz_add_replay_on_discussion_nonce
    ]);

    /**
     * add or remove like on reply
     */
    $rz_add_like_on_reply = wp_create_nonce('rz-add-like-on-reply');
    wp_localize_script('imit-recozilla', 'rzAddRemoveLikeReply', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_add_like_on_reply' => $rz_add_like_on_reply
    ]);

    /**
     * like or dislike discussion post
     */
    $rz_like_dis_or_dislike_discuss_post_nonce = wp_create_nonce('rz-add-like-or-dislike-on-discuss');
    wp_localize_script('imit-recozilla', 'rzAddLikeOrDislikeOnDiscuss', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_like_dis_or_dislike_discuss_post_nonce' => $rz_like_dis_or_dislike_discuss_post_nonce
    ]);

    /**
     * user follow unfollow
     */
    $rz_user_follow_unfollow = wp_create_nonce('rz-user-follow-unfollow-nonce');
    wp_localize_script('imit-recozilla', 'rzUserFollowUnfollow', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_user_follow_unfollow' => $rz_user_follow_unfollow
    ]);

    /**
     * change profile image
     */
    $rz_change_profile_image_nonce = wp_create_nonce('rz-change-profile-user-nonce');
    wp_localize_script('imit-recozilla', 'rzChangeProfileImage', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_change_profile_image_nonce' => $rz_change_profile_image_nonce
    ]);


    /**
     * change profile image
     */
    $rz_profile_update_nonce = wp_create_nonce('rz-profile-update-nonce');
    wp_localize_script('imit-recozilla', 'rzProfileUpdate', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_profile_update_nonce' => $rz_profile_update_nonce
    ]);

    /**
     * delete wokplace
     */
    $rz_delete_workplace_nonce = wp_create_nonce('rz-delete-workplace-nonce');
    wp_localize_script('imit-recozilla', 'rzDeleteWorkplace', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_delete_workplace_nonce' => $rz_delete_workplace_nonce
    ]);

    /**
     * delete educational info
     */
    $rz_delete_education_nonce = wp_create_nonce('rz-delete-educational-nonce');
    wp_localize_script('imit-recozilla', 'rzDeleteEducational', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_delete_education_nonce' => $rz_delete_education_nonce
    ]);

    /**
     * add dairy
     */
    $rz_add_dairy_nonce = wp_create_nonce('rz-add-dairy-nonce');
    wp_localize_script('imit-recozilla', 'rzAddDairy', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_add_dairy_nonce' => $rz_add_dairy_nonce
    ]);

    /**
     * add partner program
     */
    $rz_add_partner_program = wp_create_nonce('rz-add-partner-program-nonce');
    wp_localize_script('imit-recozilla', 'rzAddPartnerProgram', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_add_partner_program' => $rz_add_partner_program
    ]);

    /**
     * search user
     */
    $rz_search_user_nonce = wp_create_nonce('rz-search-user-nonce');
    wp_localize_script('imit-recozilla', 'rzSearchUser', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_search_user_nonce' => $rz_search_user_nonce
    ]);

    /**
     * get news feed posts
     */
    $rz_news_feed_posts_nonce = wp_create_nonce('rz-news-feed-posts-nonce');
    wp_localize_script('imit-recozilla', 'rzNewsFeed', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_news_feed_posts_nonce' => $rz_news_feed_posts_nonce
    ]);

    /**
     * popular question
     */
    $rz_popular_question_nonce = wp_create_nonce('rz-popular-question-nonce');
    wp_localize_script('imit-rz-question', 'rzPopularQuestion', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_popular_question_nonce' => $rz_popular_question_nonce
    ]);


    /**
     * get most answered question nonce
     */
    $rz_most_commented_nonce = wp_create_nonce('rz-most-commented-nonce');
    wp_localize_script('imit-rz-question', 'rzMostCommented', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_most_commented_nonce' => $rz_most_commented_nonce
    ]);

    /**
     * user asked question
     */
    $rz_asked_question_nonce = wp_create_nonce('rz-asked-question-nonce');
    wp_localize_script('imit-rz-profile', 'rzAskedQuestion', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_asked_question_nonce' => $rz_asked_question_nonce
    ]);

    /**
     * update banner status
     */
    $rz_update_banner_nonce = wp_create_nonce('rz-update-banner-nonce');
    wp_localize_script('imit-recozilla', 'rzUpdateBanner', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_update_banner_nonce' => $rz_update_banner_nonce
    ]);

    /**
     * profile asked questions
     */
    $rz_answered_question_nonce = wp_create_nonce('rz-answered-question-nonce');
    wp_localize_script('imit-rz-profile', 'rzAnsweredQuestion', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_answered_question_nonce' => $rz_answered_question_nonce
    ]);

    /**
     * get voted questions
     */
    $rz_voted_questions_nonce = wp_create_nonce('rz-voted-questions-nonce');
    wp_localize_script('imit-rz-profile', 'rzVotedQuestions', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_voted_questions_nonce' => $rz_voted_questions_nonce
    ]);


    /**
     * get all profile commented questions
     */
    $rz_commented_questions_nonce = wp_create_nonce('rz-commented-questions-nonce');
    wp_localize_script('imit-rz-profile', 'rzCommentedQuestion', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_commented_questions_nonce' => $rz_commented_questions_nonce
    ]);

    /**
     * get following user
     */
    $rz_following_user_nonce = wp_create_nonce('rz-following-user-nonce');
    wp_localize_script('imit-rz-profile', 'rzFollowingUser', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_following_user_nonce' => $rz_following_user_nonce
    ]);

    /**
     * get all dairy
     */
    $rz_user_dairy_nonce = wp_create_nonce('rz-user-dairy-nonce');
    wp_localize_script('imit-rz-profile', 'rzUserDairy', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_user_dairy_nonce' => $rz_user_dairy_nonce
    ]);

    /**
     * discuss and debate nonce
     */
    $rz_user_discuss_and_debate = wp_create_nonce('rz-discuss-and-debate-nonce');
    wp_localize_script('imit-rz-discuss', 'rzDiscussDebate', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_user_discuss_and_debate' => $rz_user_discuss_and_debate
    ]);

    /**
     * get newest discussion posts
     */
    $rz_newest_posts_nonce = wp_create_nonce('rz-newest-posts-nonce');
    wp_localize_script('imit-rz-discuss', 'rzNewstPosts', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_newest_posts_nonce' => $rz_newest_posts_nonce
    ]);


    /**
     * get most viewed discussion posts
     */
    $rz_most_viwed_posts_nonce = wp_create_nonce('rz-most-viewed-posts-nonce');
    wp_localize_script('imit-rz-discuss', 'rzMostViewedPosts', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_most_viwed_posts_nonce' => $rz_most_viwed_posts_nonce
    ]);


    /**
     * get all hotely debated discussion posts
     */
    $rz_most_hotely_debated_posts_nonce = wp_create_nonce('rz-most-hotely-debated-posts-nonce');
    wp_localize_script('imit-rz-discuss', 'rzHoteDebated', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_most_hotely_debated_posts_nonce' => $rz_most_hotely_debated_posts_nonce
    ]);


    /**
     * get suggested users
     */
    $rz_suggested_users_nonce = wp_create_nonce('rz-suggested-users-nonce');
    wp_localize_script('imit-rz-users', 'rzSuggestedUsers', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_suggested_users_nonce' => $rz_suggested_users_nonce
    ]);

    /**
     * send message
     */
    $rz_send_message_nonce = wp_create_nonce('rz-send-message-nonce');
    wp_localize_script('imit-recozilla', 'rzSendMessage', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_send_message_nonce' => $rz_send_message_nonce
    ]);


    /**
     * search terms
     */
    $rz_search_term_by_name_nonce = wp_create_nonce('rz-search-terms-nonce');
    wp_localize_script('imit-recozilla', 'rzSearchTerm', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_search_term_by_name_nonce' => $rz_search_term_by_name_nonce
    ]);

    /**
     * get all notification
     */
    $rz_get_all_notification_nonce = wp_create_nonce('rz-get-all-notification-nonce');
    wp_localize_script('imit-recozilla', 'rzGetNotification', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_get_all_notification_nonce' => $rz_get_all_notification_nonce
    ]);


    /**
     * get all message nonce
     */
    $rz_get_all_message_nonce = wp_create_nonce('rz-get-all-message-nonce');
    wp_localize_script('imit-recozilla', 'rzGetMessage', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_get_all_message_nonce' => $rz_get_all_message_nonce
    ]);

    /**
     * get live notification
     */
    $rz_get_live_notification_nonce = wp_create_nonce('rz-live-notification-nonce');
    wp_localize_script('imit-recozilla', 'rzGetLiveNotifiation', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_get_live_notification_nonce' => $rz_get_live_notification_nonce
    ]);

    /**
     * delete question
     */
    $rz_get_quiz_result_nonce = wp_create_nonce('rz-get-quiz-result-nonce');
    wp_localize_script('imit-recozilla', 'rzGetQuizResult', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_get_quiz_result_nonce' => $rz_get_quiz_result_nonce
    ]);


    /**
     * delete reply
     */
    $rz_delete_reply_nonce = wp_create_nonce('rz-delete-reply-nonce');
    wp_localize_script('imit-recozilla', 'rzDeleteReply', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_delete_reply_nonce' => $rz_delete_reply_nonce
    ]);

    /**
     * delet question comment
     */
    $rz_delete_comment_nonce = wp_create_nonce('rz-delete-comment-nonce');
    wp_localize_script('imit-recozilla', 'rzDeleteComment', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_delete_comment_nonce' => $rz_delete_comment_nonce
    ]);

    /**
     * delete answer
     */
    $rz_delete_answer_nonce = wp_create_nonce('rz-delete-answer-nonce');
    wp_localize_script('imit-recozilla', 'rzDeleteAnswer', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_delete_answer_nonce' => $rz_delete_answer_nonce
    ]);

    /**
     * delete question nonce
     */
    $rz_delete_question_nonce = wp_create_nonce('rz-delete-question-nonce');
    wp_localize_script('imit-recozilla', 'rzDeleteQuestion', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_delete_question_nonce' => $rz_delete_question_nonce
    ]);

    /**
     * delete discuss reply
     */
    $rz_delete_discuss_reply_nonce = wp_create_nonce('rz-delete-discuss-reply-nonce');
    wp_localize_script('imit-recozilla', 'rzDeleteDiscussReply', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_delete_discuss_reply_nonce' => $rz_delete_discuss_reply_nonce
    ]);

    /**
     * delete discuss comment
     */
    $rz_delete_discuss_comment_nonce = wp_create_nonce('rz-delete-discuss-comment-nonce');
    wp_localize_script('imit-recozilla', 'rzDeleteDiscussComment', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_delete_discuss_comment_nonce' => $rz_delete_discuss_comment_nonce
    ]);

    /**
     * delete discuss post
     */
    $rz_delete_discuss_post_nonce = wp_create_nonce('rz-delete-discuss-post-nonce');
    wp_localize_script('imit-recozilla', 'rzDeleteDiscussPost', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_delete_discuss_post_nonce' => $rz_delete_discuss_post_nonce
    ]);

    /**
     * redeem point nonce
     */
    $rz_redeem_point_nonce = wp_create_nonce('rz-redeem-point-nonce');
    wp_localize_script('imit-recozilla', 'rzRedeemPoint', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_redeem_point_nonce' => $rz_redeem_point_nonce
    ]);

    /**
     * follow questions nonce
     */
    $rz_follow_question = wp_create_nonce('rz-follow-question-nonce');
    wp_localize_script('imit-recozilla', 'rzFolowQuestion', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_follow_question' => $rz_follow_question
    ]);

    /**
     * following tags
     */
    $rz_follow_tag = wp_create_nonce('rz-follow-tag-nonce');
    wp_localize_script('imit-recozilla', 'rzFolowTag', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_follow_tag' => $rz_follow_tag
    ]);



    /**
     * get all following questions
     */
    $rz_get_following_questions = wp_create_nonce('rz-get-following-questions-nonce');
    wp_localize_script('imit-recozilla', 'rzFollowingQuestions', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_get_following_questions' => $rz_get_following_questions
    ]);

    /**
     * get all following tags
     */
    $rz_get_following_tags = wp_create_nonce('rz-get-following-tags-nonce');
    wp_localize_script('imit-recozilla', 'rzFollowingTags', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_get_following_tags' => $rz_get_following_tags
    ]);

    /**
     * get post using tags nonce
     */
    $rz_get_posts_using_tags = wp_create_nonce('rz-get-post-using-tags-nonce');
    wp_localize_script('imit-recozilla', 'rzGetPostUsingTags', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_get_posts_using_tags' => $rz_get_posts_using_tags
    ]);


    /**
     * delete dairy
     */
    $rz_delete_dairy_nonce = wp_create_nonce('rz-delete-dairy-nonce');
    wp_localize_script('imit-recozilla', 'rzDeleteDairy', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_delete_dairy_nonce' => $rz_delete_dairy_nonce
    ]);

    /**
     * change post visiblity
     */
    $rz_change_dairy_visiblity_nonce = wp_create_nonce('rz-change-post-visiblity-nonce');
    wp_localize_script('imit-recozilla', 'rzChangeVisiblity', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_change_dairy_visiblity_nonce' => $rz_change_dairy_visiblity_nonce
    ]);


    /**
     * get post by id
     */
    $rz_get_post_by_id_nonce = wp_create_nonce('rz-get-post-by-id-nonce');
    wp_localize_script('imit-recozilla', 'rzGetPostById', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_get_post_by_id_nonce' => $rz_get_post_by_id_nonce
    ]);

    /**
     * edit dairy
     */
    $rz_edit_dairy_nonce = wp_create_nonce('rz-edit-dairy-nonce');
    wp_localize_script('imit-recozilla', 'rzEditDairy', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_edit_dairy_nonce' => $rz_edit_dairy_nonce
    ]);

    /**
     * fetch tags archive
     */
    $rz_fetch_tags_archive = wp_create_nonce('rz-fetch-tags-archive-nonce');
    wp_localize_script('imit-recozilla', 'rzTagsArchive', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_fetch_tags_archive' => $rz_fetch_tags_archive
    ]);

    /**
     * change password nonce
     */
    $rz_change_password = wp_create_nonce('rz-change-password-nonce');
    wp_localize_script('imit-rz-profile', 'rzChangePassword', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_change_password' => $rz_change_password
    ]);

    /**
     * get new questions
     */
    $rz_get_new_questions = wp_create_nonce('rz-get-new-questions-nonce');
    wp_localize_script('imit-rz-question', 'rzGetNewQuestions', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_get_new_questions' => $rz_get_new_questions
    ]);

    /**
     * login using modal nonce
     */
    $rz_login_using_modal = wp_create_nonce('rz-login-using-modal-nonce');
    wp_localize_script('imit-recozilla', 'rzLoginWithModal', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_login_using_modal' => $rz_login_using_modal
    ]);

    /**
     * get question answers
     */
    $rz_get_questions_answers = wp_create_nonce('rz-get-questions-answers-nonce');
    wp_localize_script('imit-recozilla', 'rzGetQuestionAnswers', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_get_questions_answers' => $rz_get_questions_answers
    ]);

    /**
     * load more comment
     */
    $rz_get_more_comment = wp_create_nonce('rz-get-more-comment-nonce');
    wp_localize_script('imit-recozilla', 'rzGetMoreComment', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_get_more_comment' => $rz_get_more_comment
    ]);

    /**
     * load discuss comment
     */
    $rz_get_discuss_comment = wp_create_nonce('rz-get-discuss-comment-nonce');
    wp_localize_script('imit-recozilla', 'rzGetDiscussComment', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_get_discuss_comment' => $rz_get_discuss_comment
    ]);

    /**
     * load more discuss reply
     */
    $rz_get_discuss_reply = wp_create_nonce('rz-get-discuss-reply-nonce');
    wp_localize_script('imit-recozilla', 'rzGetDiscussReply', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_get_discuss_reply' => $rz_get_discuss_reply
    ]);

    /**
     * update notification status
     */
    $rz_update_notification_status = wp_create_nonce('rz-update-notification-status-nonce');
    wp_localize_script('imit-recozilla', 'rzUpdateNotification', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_update_notification_status' => $rz_update_notification_status
    ]);


    /**
     * load more quiz nonce
     */
    $rz_load_more_quiz_status = wp_create_nonce('rz-get-more-quiz-nonce');
    wp_localize_script('imit-recozilla', 'rzgetMoreQuiz', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_load_more_quiz_status' => $rz_load_more_quiz_status
    ]);


    /**
     * get all anwers based on quiz
     */
    $rz_get_more_points_nonce = wp_create_nonce('rz-get-more-points-nonce');
    wp_localize_script('imit-recozilla', 'rzGetPoint', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_get_more_points_nonce' => $rz_get_more_points_nonce
    ]);

    /**
     * update notification seen status
     */
    $rz_update_notification_seen_status_nonce = wp_create_nonce('rz-update-notification-seen-status-nonce');
    wp_localize_script('imit-recozilla', 'rzUpdateNotiStatus', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_update_notification_seen_status_nonce' => $rz_update_notification_seen_status_nonce
    ]);
});

/**
 * add admin script
 */
add_action('admin_enqueue_scripts', function ($hook) {
    if ($hook === 'partner_page_rzPartnerRequests') {
        wp_enqueue_style('imit-admin-style', PLUGINS_URL('css/admin.css', __FILE__));
    } else if ($hook === 'manage-answer_page_rzAllAnswers' || $hook === 'quiz_page_rzAddQuiz' || $hook === 'quiz_page_rzmanageQuestions') {
        wp_enqueue_style('imit-admin-bootstrap', PLUGINS_URL('css/bootstrap.min.css', __FILE__));

        wp_enqueue_script('imit-jquery-js', PLUGINS_URL('js/jquery-3.6.0.min.js', __FILE__), [], true, true);
        wp_enqueue_script('imit-bootstrap-js', PLUGINS_URL('js/bootstrap.bundle.min.js', __FILE__), ['imit-jquery-js'], true, true);
        wp_enqueue_script('imit-sweet-alert-js', PLUGINS_URL('js/sweetalert.min.js', __FILE__), ['imit-jquery-js'], true, true);
        wp_enqueue_script('imit-admin-js', PLUGINS_URL('js/admin.js', __FILE__), ['imit-jquery-js'], true, true);

        if ($hook === 'quiz_page_rzmanageQuestions') {
            wp_enqueue_script('imit-admin-question-js', PLUGINS_URL('js/admin-question.js', __FILE__), ['imit-jquery-js'], true, true);

            /**
             * get answer info
             */
            $rz_view_question_nonce = wp_create_nonce('rz-view-question-nonce');
            wp_localize_script('imit-admin-js', 'rzViewQuestion', [
                'ajax_url' => admin_url('admin-ajax.php'),
                'rz_view_question_nonce' => $rz_view_question_nonce
            ]);

            /**
             * edit question
             */
            $rz_edit_question_nonce = wp_create_nonce('rz-edit-question-nonce');
            wp_localize_script('imit-admin-js', 'rzEditQuestion', [
                'ajax_url' => admin_url('admin-ajax.php'),
                'rz_edit_question_nonce' => $rz_edit_question_nonce
            ]);
        } else if ($hook === 'manage-answer_page_rzAllAnswers') {
            wp_enqueue_script('imit-admin-asnwer-js', PLUGINS_URL('js/admin-answer.js', __FILE__), ['imit-jquery-js'], true, true);
        }

        /**
         * get answer info
         */
        $rz_get_answer_info_nonce = wp_create_nonce('rz-get-answer-info-nonce');
        wp_localize_script('imit-admin-js', 'rzGetAnswerInfo', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'rz_get_answer_info_nonce' => $rz_get_answer_info_nonce
        ]);


        /**
         * get answer info
         */
        $rz_change_answer_status_nonce = wp_create_nonce('rz-change-answer-status-nonce');
        wp_localize_script('imit-admin-js', 'rzChangeAnswerStatus', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'rz_change_answer_status_nonce' => $rz_change_answer_status_nonce
        ]);

        /**
         * admin submit answer
         */
        $rz_admin_submit_answer_nonce = wp_create_nonce('rz-admin-submit-answer-nonce');
        wp_localize_script('imit-admin-js', 'rzAdminSubmitAnswer', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'rz_admin_submit_answer_nonce' => $rz_admin_submit_answer_nonce
        ]);

        /**
         * get all anwers based on quiz
         */
        $rz_answer_on_quiz_nonce = wp_create_nonce('rz-answer-using-quiz-nonce');
        wp_localize_script('imit-admin-js', 'rzAnsUsingQuiz', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'rz_answer_on_quiz_nonce' => $rz_answer_on_quiz_nonce
        ]);
    }
});

/**
 * @param $user_id
 * get user profile image
 */
function getProfileImageById($user_id, $size = 'mobile')
{
    global $wpdb;
    $id = sanitize_key($user_id);
    $get_profile_image = $wpdb->get_row("SELECT profile_image FROM {$wpdb->prefix}rz_user_profile_data where user_id = '$id'");

    if (!empty($get_profile_image->profile_image)) {
        $profile_image = json_decode($get_profile_image->profile_image);

        if ($size == 'mobile') {
            if (file_exists(str_replace(site_url() . '/', ABSPATH, $profile_image->mobile)) == true) {
                return $profile_image->mobile;
            } else {
                return plugins_url('imit-recozilla/images/avatar.png');
            }
        } else {
            if (file_exists(str_replace(site_url() . '/', ABSPATH, $profile_image->full)) == true) {
                return $profile_image->full;
            } else {
                return plugins_url('imit-recozilla/images/avatar.png');
            }
        }
    } else {
        return plugins_url('imit-recozilla/images/avatar.png');
    }
}


/**
 * create initial avatar
 */
if (!function_exists('make_avatar')) {
    function make_avatar(
        string $text = 'DEV',
        int $width = 300,
        int $height = 300
    ) {
        if (!file_exists(ABSPATH . 'wp-content/uploads/initialAvatar')) {
            mkdir(ABSPATH . 'wp-content/uploads/initialAvatar', 0777, true);
        }
        $path = "wp-content/uploads/initialAvatar/" . md5(time() . rand()) . ".png";
        $font = dirname(__FILE__) . '/fonts/Arial.TTF';
        $red    = rand(0, 255);
        $green  = rand(0, 255);
        $blue   = rand(0, 255);
        $image = @imagecreate($width, $height)
            or die("Cannot Initialize new GD image stream");

        imagecolorallocate($image, $red, $green, $blue);

        $fontColor = imagecolorallocate($image, 255, 255, 255);

        $textBoundingBox = imagettfbbox(120, 0, $font, $text);

        $y = abs(ceil(($height - $textBoundingBox[5]) / 2));
        $x = abs(ceil(($width - $textBoundingBox[2]) / 2));

        imagettftext($image, 120, 0, $x, $y, $fontColor, $font, $text);

        imagepng($image, ABSPATH . $path);
        imagedestroy($image);
        return json_encode([
            'full' => site_url() . '/' . $path,
            'mobile' => site_url() . '/' . $path
        ]);
    }
}


if (!function_exists('wpDateTime')) {
    function wpDateTime()
    {
        $time = date_i18n("Y-m-d H:i:s");

        return $time;
    }
};

/**
 * get user name by id
 */
function getUserNameById($user_id)
{
    $get_userdata = get_userdata($user_id);

    if (!empty($get_userdata->user_firstname) && !empty($get_userdata->user_lastname)) {
        return $get_userdata->user_firstname . ' ' . $get_userdata->user_lastname;
    } else {
        return $get_userdata->display_name;
    }
}

function isUserAlreadyPartner($user_id)
{
    global $wpdb;
    $partner = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_user_programs WHERE user_id = '$user_id' AND status = '1'", ARRAY_A);

    if (count($partner) > 0 && is_user_logged_in()) {
        return true;
    } else {
        return false;
    }
}

/**
 * send message with html formats
 */
function wpse27856_set_content_type()
{
    return "text/html";
}
add_filter('wp_mail_content_type', 'wpse27856_set_content_type');


/**
 * get post views
 */
function getPostViews($postID)
{
    $count_key = 'post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if ($count == '') {
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
        return "0";
    }
    return $count;
}

/**
 * questions files
 */
require_once 'rz-questions.php';

/**
 * add discussion file
 */
require_once 'rz-discussion.php';

/**
 * require discussion comment file
 */
require_once 'rz-discussion-comment.php';


/**
 * add register file
 */
require_once 'rz-register.php';



/**
 * require activity file
 */
require_once 'rz-activity.php';

/**
 * require answer comment file
 */
require_once 'rz-answer-comment.php';


/**
 * require answer comment replay file
 */
require_once 'rz-answer-comment-reply.php';

/**
 * require answer file
 */
require_once 'rz-answers.php';

/**
 * require discussion comment
 */
require_once 'rz-discussion-comment-reply.php';

/**
 * require follow unfollow file
 */
require_once 'rz-follow-unfollow.php';

/**
 * require hashtags file
 */
require_once 'rz-hashtags.php';

/**
 * require login file
 */
require_once 'rz-login.php';

/**
 * require profile file
 */
require_once 'rz-profile.php';


/**
 * require vote
 */
require_once 'rz-vote.php';

/**
 * add dairy file
 */
require_once 'rz-dairy.php';


/**
 * add partner programme file
 */
require_once 'rz-partner-program.php';


/**
 * add user file
 */
require_once 'rz-users.php';

/**
 * add quiz file
 */
// require_once 'rz-quiz.php';

/**
 * require tags file
 */
require_once 'rz-tags.php';

/**
 * require reset password page
 */
require_once 'rz-reset-password.php';

/**
 * require notification file
 */
require_once 'rz-notification.php';

/**
 * require search file
 */
require_once 'rz-search.php';



/**
 * update banner status
 */
add_action('wp_ajax_rz_update_banner_status', function () {
    global $wpdb;
    $nonce = $_POST['nonce'];

    if (wp_verify_nonce($nonce, 'rz-update-banner-nonce')) {
        $rz_user_profile_data = $wpdb->prefix . 'rz_user_profile_data';
        $current_user_id = get_current_user_id();
        $profile_data = $wpdb->get_row("SELECT * FROM {$rz_user_profile_data} WHERE user_id = '{$current_user_id}'");

        if (empty($profile_data)) {
            $wpdb->insert($rz_user_profile_data, [
                'user_id' => $current_user_id,
                'banner_status' => 'disabled',
                'created_at' => wpDateTime(),
                'updated_at' => wpDateTime()
            ]);
        } else {
            $wpdb->update($rz_user_profile_data, [
                'banner_status' => 'disabled'
            ], ['user_id' => $current_user_id]);
        }
    }
    die();
});

/**
 * send message
 */
add_action('wp_ajax_rz_add_message_action', function () {
    global $wpdb;
    $nonce = $_POST['nonce'];
    if (wp_verify_nonce($nonce, 'rz-send-message-nonce')) {
        $message = sanitize_text_field($_POST['message']);
        $receiver_id = sanitize_key($_POST['user_id']);
        $sender_id = get_current_user_id();


        if (empty($message) || empty($receiver_id) || empty($sender_id)) {
            echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">
            <strong>Wait!</strong> Please type something.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
        } else {
            $wpdb->insert($wpdb->prefix . 'massages', [
                'sender_id' => $sender_id,
                'received_id' => $receiver_id,
                'massage_text' => $message,
                'date_time' => wpDateTime(),
            ]);
        }
    }
    die();
});

/**
 * get all message
 */
add_action('wp_ajax_rz_get_all_message', function () {
    global $wpdb;
    $nonce = $_POST['nonce'];
    if (wp_verify_nonce($nonce, 'rz-get-all-message-nonce')) {
        foreach (get_conversion_chat_list() as $row) {
            $userinfo = get_user_by('id', $row->id);
            $last_massage = get_last_massage($row->id);
?>
            <li class="message-user-list list-unstyled">
                <a href="<?php echo site_url(); ?>/message" class="message-user-link <?php if ($last_massage->status == 0) {
                                                                                            echo 'active';
                                                                                        } ?>">
                    <div class="profile-info d-flex flex-row justify-content-start align-items-center">
                        <div class="profile-image">
                            <img src="<?php echo getProfileImageById($row->id); ?>" alt="">
                        </div>
                        <div class="info ms-2 w-100">
                            <div class="d-flex flex-row justify-content-between align-items-center">
                                <h2 class="name fz-14 imit-font m-0"><?php echo getUserNameById($row->id); ?>
                                    <!-- <span></span>-->
                                </h2>
                                <p class="imit-font rz-color timeago fw-500 mb-0"><?php echo convert_time_to_days($last_massage->date_time); ?></p>
                            </div>
                            <p class="rz-secondary-color imit-font fz-12 mb-0"><?php echo substr($last_massage->massage_text, 0, 37);  ?></p>
                        </div>
                    </div>
                </a>
            </li>
            <?php
        }
    }
    die();
});

/**
 * get live notification
 */
add_action('wp_ajax_rz_get_live_notification', function () {
    global $wpdb;
    $nonce = $_POST['nonce'];
    if (wp_verify_nonce($nonce, 'rz-live-notification-nonce')) {
        $receiver_id = get_current_user_id();
        $count_notification = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}notification WHERE receiver_id = '$receiver_id'", ARRAY_A);
        $count_message = $wpdb->get_results("select DISTINCT if( received_id={$receiver_id},sender_id,received_id) 
        AS id from wp_massages  WHERE (sender_id = {$receiver_id} OR received_id = {$receiver_id}) AND status = 0
         ORDER by id DESC");

        if ((count($count_notification) + count($count_message)) > 0) {
            exit('exists');
        }
    }
    die();
});


/**
 * update feature post image category
 */
add_action('um_hourly_scheduled_events', function () {
    $get_feature_current_post = new WP_Query([
        'post_type' => 'rz_post_question',
        'posts_per_page' => 1,
        'tax_query' =>  array(
            array(
                'taxonomy' => 'question_category',
                'field' => 'slug',
                'terms' => 'feature-post'
            )
        ),
        'orderby' => 'ID',
        'order' => 'ASC'
    ]);

    while ($get_feature_current_post->have_posts()) : $get_feature_current_post->the_post();
        $post_id = get_the_ID();
        wp_set_post_terms($post_id, 'uncategorised', 'question_category');
    endwhile;


    $get_feature_next_post = new WP_Query([
        'post_type' => 'rz_post_question',
        'posts_per_page' => 1,
        'paged' => 2,
        'tax_query' =>  array(
            array(
                'taxonomy' => 'question_category',
                'field' => 'slug',
                'terms' => 'feature-post'
            )
        ),
        'orderby' => 'ID',
        'order' => 'ASC'
    ]);

    while ($get_feature_next_post->have_posts()) : $get_feature_next_post->the_post();
        $post_id = get_the_ID();
        $time = current_time('mysql');
        $data = array(
            'ID' => $post_id,
            'post_status' => 'publish',
            'post_date'     => $time,
            'post_date_gmt' => get_gmt_from_date($time),
            'post_modified' => $time,
            'post_modified_gmt' => get_gmt_from_date($time)
        );

        wp_update_post($data);
    endwhile;
});

/**
 * following tags
 */
add_action('wp_ajax_rz_follwoing_tags_action', function () {
    global $wpdb;
    $nonce = $_POST['nonce'];
    if (wp_verify_nonce($nonce, 'rz-follow-tag-nonce')) {
        $rz_following_tags = $wpdb->prefix . 'rz_following_tags';
        $term_id = sanitize_key($_POST['term_id']);
        $user_id = get_current_user_id();

        $is_user_already_followed = $wpdb->get_row("SELECT * FROM {$rz_following_tags} WHERE user_id = '{$user_id}' AND term_id = '{$term_id}'");

        if (!empty($is_user_already_followed)) {
            $wpdb->delete($rz_following_tags, [
                'user_id' => $user_id,
                'term_id' => $term_id
            ]);

            $response['response'] = false;
        } else {
            $wpdb->insert($rz_following_tags, [
                'user_id' => $user_id,
                'term_id' => $term_id,
                'created_at' => wpDateTime(),
                'updated_at' => wpDateTime()
            ]);
            $response['response'] = true;
        }

        echo json_encode($response);
    }
    die();
});

/**
 * get more point
 */
add_action('wp_ajax_rz_get_morePoint', function () {
    global $wpdb;
    $nonce = $_POST['nonce'];
    if (wp_verify_nonce($nonce, 'rz-get-more-points-nonce')) {
        $start = sanitize_key($_POST['start']);
        $current_user = get_current_user_id();
        $get_point_list = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_point_table WHERE user_id = '{$current_user}' ORDER BY id DESC LIMIT $start, 10", ARRAY_A);

        if (count($get_point_list) > 0) {
            foreach ($get_point_list as $point) {
                if ($point['point_type'] == 'answer') {
                    $answer_id = $point['content_id'];
                    $get_post_by_answer = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}posts WHERE ID IN (SELECT post_id FROM {$wpdb->prefix}rz_answers WHERE id = '{$answer_id}')");
            ?>
                    <li class="point-history-items list-unstyled py-4">
                        <span class="text-secondary imit-font fz-14 mb-2 d-block"><?php echo date('ga F, Y', strtotime($point['created_at'])); ?></span>
                        <p class="m-0 imit-font fz-14 textdark">Added answer for “<?php echo $get_post_by_answer->post_title; ?>” <strong class="rz-color">+<?php echo $point['point_earn']; ?> points</strong></p>
                    </li>
                <?php
                } else if ($point['point_type'] == 'post') {
                    $post_id = $point['content_id'];
                    $posts_data = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}posts WHERE ID = '{$post_id}'");
                ?>
                    <li class="point-history-items list-unstyled py-4">
                        <span class="text-secondary imit-font fz-14 mb-2 d-block"><?php echo date('ga F, Y', strtotime($point['created_at'])); ?></span>
                        <p class="m-0 imit-font fz-14 textdark">Added post for “<?php echo $posts_data->post_title; ?>” <strong class="rz-color">+<?php echo $point['point_earn']; ?> points</strong></p>
                    </li>
                <?php
                } else {
                    $vote_id = $point['content_id'];
                    $get_post_data = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}posts WHERE ID IN (SELECT post_id FROM {$wpdb->prefix}rz_answers WHERE id IN (SELECT answer_id FROM {$wpdb->prefix}rz_vote WHERE id = '$vote_id'))");
                ?>
                    <li class="point-history-items list-unstyled py-4">
                        <span class="text-secondary imit-font fz-14 mb-2 d-block"><?php echo date('ga F, Y', strtotime($point['created_at'])); ?></span>
                        <p class="m-0 imit-font fz-14 textdark">Added upvote for “<?php echo $get_post_data->post_title; ?>” <strong class="rz-color">+<?php echo $point['point_earn']; ?> points</strong></p>
                    </li>
    <?php
                }
            }
        } else {
            exit('pointReachmax');
        }
    }
    die();
});

/**
 * email verification message page
 */
add_shortcode('rz-email-verification-message', function () {
    ob_start();
    wp_logout();
    if (!session_id()) {
        session_start();
    }
    ?>
    <section class="login overflow-hidden" style="background-image: url('<?php echo plugins_url('images/loginbg.jpeg', __FILE__) ?>');">
        <div class="rz-mid">
            <div class="row" style="min-height: 100vh;">
                <div class="col-lg-6">
                    <h3 class="title rz-color imit-font">Welcome to</h3>
                    <img class="logo" src="<?php echo plugins_url('images/logo.png', __FILE__); ?>" alt="">
                    <p class="mb-0 subtitle imit-font mt-3">A place to learn from knowledge and experiences of others and share yours</p>
                </div>
                <div class="col-lg-6">
                    <div class="rz-br bg-white rz-login-card mb-3" style="margin-top: 150px;">
                        <h3 class="imit-font fz-20 pt-5 px-5 m-0">Verify Email</h3>
                        <?php
                        if (isset($_SESSION['verify_email'])) {
                        ?>
                            <p class="logged-in-user-info imit-font text-dark fz-16 mt-2 px-5 py-3 m-0">Thank you for registering with us. We have sent a verification email to <strong>
                                    <?php echo $_SESSION['verify_email']; ?>
                                </strong></p>
                        <?php
                        }
                        ?>
                        <p class="logged-in-user-info imit-font text-dark fz-16 mt-2 px-5 pt-3 pb-4 m-0">Please click on link given in email to verify your account. You can login to Recozilla once email verification is complete.</p>
                        <p class="logged-in-user-info imit-font text-dark fz-16 mt-2 px-5 pb-4 m-0">Do check your Junk/Spam folder if email is not visible in Inbox.</p>
                        <div class="join rz-bg-color p-5" style="background-image: url('<?php echo plugins_url('images/Group 237.png', __FILE__); ?>');min-height: auto !important;">
                            <h3 class="title m-0 text-white imit-font fw-500" style="font-size: 24px;text-transform: none;">Write answers or create posts on Recozilla and earn Money</h3>
                            <a href="<?php echo site_url(); ?>/join-partner-program/" class="btn bg-white fz-14 rz-color imit-font fw-500 mt-3 py-2 px-4">Join our Partner Program </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php
    session_destroy();
    return ob_get_clean();
});
