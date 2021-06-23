<?php 


/**
 * Plugin Name:       Imit Recozilla
 * Plugin URI:        https://example.com/plugins/the-basics/
 * Description:       Recozilla Multivendor plugin.
 * Version:           1.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
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

if(!defined('ABSPATH')){
die(__('Direct access not allowed.', 'imit-recozilla'));
}

/**
 * create database when active plugin
 */
function imit_rz_init(){
    global $wpdb;
    $rz_answers = $wpdb->prefix.'rz_answers';
    $rz_vote = $wpdb->prefix.'rz_vote';
    $rz_answer_comment = $wpdb->prefix.'rz_answer_comments';
    $rz_answer_comment_vote = $wpdb->prefix.'rz_answer_comment_votes';
    $rz_hashtags = $wpdb->prefix.'rz_hashtags';
    $rz_comment_replays = $wpdb->prefix.'rz_comment_replays';
    $rz_comment_replys_likes = $wpdb->prefix.'rz_comment_reply_likes';
    $rz_discussion_comment_table = $wpdb->prefix.'rz_discussion_comments';
    $rz_discuss_comment_likes = $wpdb->prefix.'rz_discuss_comment_likes';
    $rz_discuss_comment_replays = $wpdb->prefix.'rz_discuss_comment_replays';
    $rz_discuss_reply_likes = $wpdb->prefix.'rz_discuss_reply_likes';
    $rz_discuss_likes = $wpdb->prefix.'rz_discuss_likes';
    $rz_followers = $wpdb->prefix.'rz_followers';
    $rz_user_profile_data = $wpdb->prefix.'rz_user_profile_data';
    $rz_user_work = $wpdb->prefix.'rz_user_work';
    $rz_user_education = $wpdb->prefix.'rz_user_education';
    $rz_partner_program = $wpdb->prefix.'rz_user_programs';
    $rz_point_table = $wpdb->prefix.'rz_point_table';
    $rz_quizzes = $wpdb->prefix.'rz_quizzes';

    require_once (ABSPATH.'wp-admin/includes/upgrade.php');

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



    dbDelta( $sql );


    add_option('imit_rz_db_version', IMIT_RZ_DB_VERSION);
    
}

register_activation_hook( __FILE__, 'imit_rz_init' );


/**
 * theme support
 */
function imit_theme_support(){

    /**
     * register post type
     */
    register_post_type( 'rz_post_question', [
        'public' => true,
        'labels' => [
            'name' => __('Question', 'imit-recozilla'),
            'all_items' => __('All questions', 'imit-recozilla'),
            'add_new_items' => __('Add new question', 'imit-recozilla'),
        ],
        'menu_icon' => 'dashicons-edit-large',
        'supports' => ['title', 'editor', 'thumbnail'],
    ] );

    register_taxonomy( 'question_tags', 'rz_post_question', [
        'public'       => true,
    ] );

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
add_action('wp_enqueue_scripts', function(){
    wp_enqueue_style( 'imit-bootstrap', PLUGINS_URL('css/bootstrap.min.css', __FILE__));
    wp_enqueue_style( 'imit-fontawesome', PLUGINS_URL('css/all.min.css', __FILE__));
    wp_enqueue_style( 'imit-stylesheet', PLUGINS_URL('css/style.css', __FILE__));

    wp_enqueue_script( 'jQuery', PLUGINS_URL('js/jquery-3.6.0.min.js', __FILE__), [], true, true);
    wp_enqueue_script( 'imit-bootstrap', PLUGINS_URL('js/bootstrap.bundle.min.js', __FILE__), ['jQuery'], true, true);
    wp_enqueue_script( 'imit-sweetalert', PLUGINS_URL('js/sweetalert.min.js', __FILE__), ['jQuery'], true, true);
    wp_enqueue_script( 'imit-recozilla', PLUGINS_URL('js/recozilla.js', __FILE__), ['jQuery'], true, true);

    if(is_page('discuss')){
        wp_enqueue_script( 'imit-rz-discuss', PLUGINS_URL('js/rz-discuss.js', __FILE__), ['jQuery'], true, true );
    }else if(is_page('questions')){
        wp_enqueue_script( 'imit-rz-question', PLUGINS_URL('js/rz-question.js', __FILE__), ['jQuery'], true, true );
    }else if(is_page('user')){
        wp_enqueue_script( 'imit-rz-profile', PLUGINS_URL('js/rz-profile.js', __FILE__), ['jQuery'], true, true );
    }else if(is_page( 'users' )){
        wp_enqueue_script( 'imit-rz-users', PLUGINS_URL('js/rz-users.js', __FILE__), ['jQuery'], true, true );
    }

    /**
     * login user
     */
    $recozilla_login_nonce = wp_create_nonce( 'rz-login-nonce' );
    wp_localize_script( 'imit-recozilla', 'rzLogin', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'recozilla_login_nonce' => $recozilla_login_nonce
    ] );

    /**
     * register user
     */
    $recozilla_register_nonce = wp_create_nonce('rz-registe-nonce');
    wp_localize_script( 'imit-recozilla', 'rzRegister', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'recozilla_register_nonce' => $recozilla_register_nonce
    ] );

    /**
     * add question
     */
    $recozilla_add_question_nonce = wp_create_nonce('rz-add-question-nonce');
    wp_localize_script( 'imit-recozilla', 'rzAddQuestion', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'recozilla_add_question_nonce' => $recozilla_add_question_nonce
    ] );

    /**
     * add answers
     */
    $recozilla_add_answer_nonce = wp_create_nonce('rz-add-answer-nonce');
    wp_localize_script( 'imit-recozilla', 'rzAddAnswer', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'recozilla_add_answer_nonce' => $recozilla_add_answer_nonce
    ] );

    /**
     * add vote
     */
    $recozilla_add_vote_nonce = wp_create_nonce('rz-add-vote-nonce');
    wp_localize_script( 'imit-recozilla', 'rzAddVote', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'recozilla_add_vote_nonce' => $recozilla_add_vote_nonce
    ] );

    /**
     * add comment on answer
     */
    $rz_add_comment_on_answer_nonce = wp_create_nonce( 'rz-add-comment-on-answer' );
    wp_localize_script( 'imit-recozilla', 'rzAddAnswerOnComment', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_add_comment_on_answer_nonce' => $rz_add_comment_on_answer_nonce
    ] );

    /**
     * add comment up vote
     */
    $rz_add_comment_on_up_vote_nonce = wp_create_nonce( 'rz-add-comment-up-vote' );
    wp_localize_script( 'imit-recozilla', 'rzAddCommentUpVote', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_add_comment_on_up_vote_nonce' => $rz_add_comment_on_up_vote_nonce
    ] );


    /**
     * user activity view nonce
     */
    $rz_activity_view_nonce = wp_create_nonce( 'rz-activity-view' );
    wp_localize_script( 'imit-recozilla', 'rzActivity', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_activity_view_nonce' => $rz_activity_view_nonce
    ] );

    /**
     * user hashtag suggestion
     */
    $rz_hashtag_show_nonce = wp_create_nonce( 'rz-hashtag-show' );
    wp_localize_script( 'imit-recozilla', 'rzHashtagShow', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_hashtag_show_nonce' => $rz_hashtag_show_nonce
    ] );

    /**
     * add comment replay
     */
    $rz_add_comment_replay_nonce = wp_create_nonce( 'rz-add-comment-replay' );
    wp_localize_script( 'imit-recozilla', 'rzCommentReplay', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_add_comment_replay_nonce' => $rz_add_comment_replay_nonce
    ] );

    /**
     * like replay
     */
    $rz_add_replay_like_none = wp_create_nonce( 'rz-add-replay-like' );
    wp_localize_script( 'imit-recozilla', 'rzAddReplayLike', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_add_replay_like_none' => $rz_add_replay_like_none
    ] );

    /**
     * add discussion
     */
    $rz_add_discussion = wp_create_nonce( 'rz-add-add-discussion' );
    wp_localize_script( 'imit-recozilla', 'rzAddDiscussion', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_add_discussion' => $rz_add_discussion
    ] );

    /**
     * add comment on discussion
     */
    $rz_add_comment_in_discussion_nonce = wp_create_nonce( 'rz-add-comment-on-discussion' );
    wp_localize_script( 'imit-recozilla', 'rzAddCommentDis', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_add_comment_in_discussion_nonce' => $rz_add_comment_in_discussion_nonce
    ] );

    /**
     * like discussion comment
     */
    $rz_like_discussion_comment = wp_create_nonce( 'rz-like-discussion-comment' );
    wp_localize_script( 'imit-recozilla', 'rzLikeDislikeDiscussComment', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_like_discussion_comment' => $rz_like_discussion_comment
    ] );

    /**
     * add replay on discussion comment
     */
    $rz_add_replay_on_discussion_nonce = wp_create_nonce( 'rz-add-replay-on-discussion-comment' );
    wp_localize_script( 'imit-recozilla', 'rzAddReplayOnDiscussComment', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_add_replay_on_discussion_nonce' => $rz_add_replay_on_discussion_nonce
    ] );

    /**
     * add or remove like on reply
     */
    $rz_add_like_on_reply = wp_create_nonce( 'rz-add-like-on-reply' );
    wp_localize_script( 'imit-recozilla', 'rzAddRemoveLikeReply', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_add_like_on_reply' => $rz_add_like_on_reply
    ] );

    /**
     * like or dislike discussion post
     */
    $rz_like_dis_or_dislike_discuss_post_nonce = wp_create_nonce( 'rz-add-like-or-dislike-on-discuss' );
    wp_localize_script( 'imit-recozilla', 'rzAddLikeOrDislikeOnDiscuss', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_like_dis_or_dislike_discuss_post_nonce' => $rz_like_dis_or_dislike_discuss_post_nonce
    ] );

    /**
     * user follow unfollow
     */
    $rz_user_follow_unfollow = wp_create_nonce( 'rz-user-follow-unfollow-nonce' );
    wp_localize_script( 'imit-recozilla', 'rzUserFollowUnfollow', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_user_follow_unfollow' => $rz_user_follow_unfollow
    ] );

    /**
     * change profile image
     */
    $rz_change_profile_image_nonce = wp_create_nonce( 'rz-change-profile-user-nonce' );
    wp_localize_script( 'imit-recozilla', 'rzChangeProfileImage', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_change_profile_image_nonce' => $rz_change_profile_image_nonce
    ] );


    /**
     * change profile image
     */
    $rz_profile_update_nonce = wp_create_nonce( 'rz-profile-update-nonce' );
    wp_localize_script( 'imit-recozilla', 'rzProfileUpdate', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_profile_update_nonce' => $rz_profile_update_nonce
    ] );

    /**
     * delete wokplace
     */
    $rz_delete_workplace_nonce = wp_create_nonce( 'rz-delete-workplace-nonce' );
    wp_localize_script( 'imit-recozilla', 'rzDeleteWorkplace', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_delete_workplace_nonce' => $rz_delete_workplace_nonce
    ] );

    /**
     * delete educational info
     */
    $rz_delete_education_nonce = wp_create_nonce( 'rz-delete-educational-nonce' );
    wp_localize_script( 'imit-recozilla', 'rzDeleteEducational', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_delete_education_nonce' => $rz_delete_education_nonce
    ] );

    /**
     * add dairy
     */
    $rz_add_dairy_nonce = wp_create_nonce( 'rz-add-dairy-nonce' );
    wp_localize_script( 'imit-recozilla', 'rzAddDairy', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_add_dairy_nonce' => $rz_add_dairy_nonce
    ] );

    /**
     * add partner program
     */
    $rz_add_partner_program = wp_create_nonce( 'rz-add-partner-program-nonce' );
    wp_localize_script( 'imit-recozilla', 'rzAddPartnerProgram', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_add_partner_program' => $rz_add_partner_program
    ] );

    /**
     * search user
     */
    $rz_search_user_nonce = wp_create_nonce( 'rz-search-user-nonce' );
    wp_localize_script( 'imit-recozilla', 'rzSearchUser', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_search_user_nonce' => $rz_search_user_nonce
    ] );

    /**
     * get news feed posts
     */
    $rz_news_feed_posts_nonce = wp_create_nonce( 'rz-news-feed-posts-nonce' );
    wp_localize_script( 'imit-recozilla', 'rzNewsFeed', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_news_feed_posts_nonce' => $rz_news_feed_posts_nonce
    ] );

    /**
     * popular question
     */
    $rz_popular_question_nonce = wp_create_nonce( 'rz-popular-question-nonce' );
    wp_localize_script( 'imit-rz-question', 'rzPopularQuestion', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_popular_question_nonce' => $rz_popular_question_nonce
    ] );


    /**
     * get most answered question nonce
     */
    $rz_most_commented_nonce = wp_create_nonce( 'rz-most-commented-nonce' );
    wp_localize_script( 'imit-rz-question', 'rzMostCommented', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_most_commented_nonce' => $rz_most_commented_nonce
    ] );

    /**
     * user asked question
     */
    $rz_asked_question_nonce = wp_create_nonce( 'rz-asked-question-nonce' );
    wp_localize_script( 'imit-rz-profile', 'rzAskedQuestion', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_asked_question_nonce' => $rz_asked_question_nonce
    ] );

    /**
     * update banner status
     */
    $rz_update_banner_nonce = wp_create_nonce( 'rz-update-banner-nonce' );
    wp_localize_script( 'imit-recozilla', 'rzUpdateBanner', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_update_banner_nonce' => $rz_update_banner_nonce
    ] );

    /**
     * profile asked questions
     */
    $rz_answered_question_nonce = wp_create_nonce( 'rz-answered-question-nonce' );
    wp_localize_script( 'imit-rz-profile', 'rzAnsweredQuestion', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_answered_question_nonce' => $rz_answered_question_nonce
    ] );

    /**
     * get voted questions
     */
    $rz_voted_questions_nonce = wp_create_nonce( 'rz-voted-questions-nonce' );
    wp_localize_script( 'imit-rz-profile', 'rzVotedQuestions', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_voted_questions_nonce' => $rz_voted_questions_nonce
    ] );


    /**
     * get all profile commented questions
     */
    $rz_commented_questions_nonce = wp_create_nonce( 'rz-commented-questions-nonce' );
    wp_localize_script( 'imit-rz-profile', 'rzCommentedQuestion', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_commented_questions_nonce' => $rz_commented_questions_nonce
    ] );

    /**
     * get following user
     */
    $rz_following_user_nonce = wp_create_nonce( 'rz-following-user-nonce' );
    wp_localize_script( 'imit-rz-profile', 'rzFollowingUser', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_following_user_nonce' => $rz_following_user_nonce
    ] );

    /**
     * get all dairy
     */
    $rz_user_dairy_nonce = wp_create_nonce( 'rz-user-dairy-nonce' );
    wp_localize_script( 'imit-rz-profile', 'rzUserDairy', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_user_dairy_nonce' => $rz_user_dairy_nonce
    ] );

    /**
     * discuss and debate nonce
     */
    $rz_user_discuss_and_debate = wp_create_nonce( 'rz-discuss-and-debate-nonce' );
    wp_localize_script( 'imit-rz-discuss', 'rzDiscussDebate', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_user_discuss_and_debate' => $rz_user_discuss_and_debate
    ] );

    /**
     * get newest discussion posts
     */
    $rz_newest_posts_nonce = wp_create_nonce( 'rz-newest-posts-nonce' );
    wp_localize_script( 'imit-rz-discuss', 'rzNewstPosts', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_newest_posts_nonce' => $rz_newest_posts_nonce
    ] );


    /**
     * get most viewed discussion posts
     */
    $rz_most_viwed_posts_nonce = wp_create_nonce( 'rz-most-viewed-posts-nonce' );
    wp_localize_script( 'imit-rz-discuss', 'rzMostViewedPosts', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_most_viwed_posts_nonce' => $rz_most_viwed_posts_nonce
    ] );


    /**
     * get all hotely debated discussion posts
     */
    $rz_most_hotely_debated_posts_nonce = wp_create_nonce( 'rz-most-hotely-debated-posts-nonce' );
    wp_localize_script( 'imit-rz-discuss', 'rzHoteDebated', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_most_hotely_debated_posts_nonce' => $rz_most_hotely_debated_posts_nonce
    ] );


    /**
     * get suggested users
     */
    $rz_suggested_users_nonce = wp_create_nonce( 'rz-suggested-users-nonce' );
    wp_localize_script( 'imit-rz-users', 'rzSuggestedUsers', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_suggested_users_nonce' => $rz_suggested_users_nonce
    ] );

    /**
     * send message
     */
    $rz_send_message_nonce = wp_create_nonce( 'rz-send-message-nonce' );
    wp_localize_script( 'imit-recozilla', 'rzSendMessage', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_send_message_nonce' => $rz_send_message_nonce
    ] );


});

/**
 * add admin script
 */
add_action('admin_enqueue_scripts', function($hook){
    if($hook === 'partner_page_rzPartnerRequests'){
        wp_enqueue_style('imit-admin-style', PLUGINS_URL('css/admin.css', __FILE__));
    }else if($hook === 'manage-answer_page_rzAllAnswers' || $hook === 'quiz_page_rzAddQuiz' || $hook === 'quiz_page_rzmanageQuestions'){
        wp_enqueue_style('imit-admin-bootstrap', PLUGINS_URL('css/bootstrap.min.css', __FILE__));

        wp_enqueue_script( 'imit-jquery-js', PLUGINS_URL('js/jquery-3.6.0.min.js', __FILE__), [], true, true );
        wp_enqueue_script( 'imit-bootstrap-js', PLUGINS_URL('js/bootstrap.bundle.min.js', __FILE__), ['imit-jquery-js'], true, true );
        wp_enqueue_script( 'imit-sweet-alert-js', PLUGINS_URL('js/sweetalert.min.js', __FILE__), ['imit-jquery-js'], true, true );
        wp_enqueue_script( 'imit-admin-js', PLUGINS_URL('js/admin.js', __FILE__), ['imit-jquery-js'], true, true );

        /**
         * get answer info
         */
        $rz_get_answer_info_nonce = wp_create_nonce( 'rz-get-answer-info-nonce' );
        wp_localize_script( 'imit-admin-js', 'rzGetAnswerInfo', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'rz_get_answer_info_nonce' => $rz_get_answer_info_nonce
        ] );


        /**
         * get answer info
         */
        $rz_change_answer_status_nonce = wp_create_nonce( 'rz-change-answer-status-nonce' );
        wp_localize_script( 'imit-admin-js', 'rzChangeAnswerStatus', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'rz_change_answer_status_nonce' => $rz_change_answer_status_nonce
        ] );
    }

});

/**
 * @param $user_id
 * get user profile image
 */
function getProfileImageById($user_id){
    global $wpdb;
    $id = sanitize_key($user_id);
    $get_profile_image = $wpdb->get_row("SELECT profile_image FROM {$wpdb->prefix}rz_user_profile_data where user_id = '$id'");

    if(!empty($get_profile_image->profile_image)){
        echo $get_profile_image -> profile_image;
    }else{
        echo plugins_url('imit-recozilla/images/avatar.png');
    }
}

/**
 * get user name by id
 */
function getUserNameById($user_id){
    $get_userdata = get_userdata($user_id);

    if(!empty($get_userdata->user_firstname) && !empty($get_userdata->user_lastname)){
        return $get_userdata->user_firstname.' '.$get_userdata->user_lastname;
    }else{
        return $get_userdata->display_name;
    }
}


/**
 * get post views
 */
function getPostViews($postID){
    $count_key = 'post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if($count==''){
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
        return "0 View";
    }
    return $count.' Views';
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
require_once 'rz-quiz.php';


/**
 * quiez shortcode
 */
add_shortcode('rz-quiz', function(){
    ob_start();
    ?>
    <div class="container quiz_main_section">
        <div class="row">
            <div class="col-md-8">
                <!-- quiz colum start -->
                <div class="main-column">
                    <div class="quiz_heading">
                        <h2 class="text-left rz-color fz-24 fw-500">Test your Knowledge</h2>
                    </div>
                    <!-- quize open box -->
                    <div class="quiz_result_box p-3 rz-border rounded">
                        <div class="test_heading">
                            <p class="quiz-text-number rz-s-p fz-20 p-10 fw-500">Quiz 1: Yoga Test</p>
                            <p class="time_date float-end rz-s-p p-10">Added on: 3pm April 25 ,2021</p>
                        </div>

                        <div class="quiz-question p-10">
                            <span class="question-img float-start"><img src="asset/img/Vector.png" alt=""></span>
                            <h2 class="fw-500 fz-32 rz-color">. “ How many timesa year doesmoon revolved earth”?</h2>
                        </div>
                        <p class="quiz-ans-title rz-s-p mb-0">Your answer</p>
                        <p class="quiz-time"><i class="fa fa-times-circle"></i><span class="rz-s-p">60 Times</span></p>
                        <p class="quiz-ans-title  mb-0">Your answer</p>
                        <p class="quiz-time"><i class="fa fa-times-circle"></i><span class="rz-s-p" >40 Times</span></p>
                        <div class="row">
                            <div class="col-md-4"></div>
                            <div class="col-md-4"><p class="text-center achived_number rz-s-p">1 of 10 Questions</p></div>
                            <div class="col-md-4 float-end"><p class="text-center next-number rz-s-p"> Next Question</p></div>
                        </div>
                    </div>
                    <!-- quiz opne box end  -->

                    <!-- quiz close box  -->
                    <div class="qiz_close_box">
                        <div class="test_heading">
                            <p class="quiz-text-number rz-s-p fz-20 p-10 fw-500">Quiz 1: Yoga Test</p>
                            <p class="time_date float-end rz-s-p p-10">Added on: 3pm April 25 ,2021</p>
                        </div>
                    </div>
                    <div class="qiz_close_box">
                        <div class="test_heading">
                            <p class="quiz-text-number rz-s-p fz-20 p-10 fw-500">Quiz 1: Yoga Test</p>
                            <p class="time_date float-end rz-s-p p-10">Added on: 3pm April 25 ,2021</p>
                        </div>
                    </div>
                    <div class="qiz_close_box">
                        <div class="test_heading">
                            <p class="quiz-text-number rz-s-p fz-20 p-10 fw-500">Quiz 1: Yoga Test</p>
                            <p class="time_date float-end rz-s-p p-10">Added on: 3pm April 25 ,2021</p>
                        </div>
                    </div>
                    <div class="qiz_close_box">
                        <div class="test_heading">
                            <p class="quiz-text-number rz-s-p fz-20 p-10 fw-500">Quiz 1: Yoga Test</p>
                            <p class="time_date float-end rz-s-p p-10">Added on: 3pm April 25 ,2021</p>
                        </div>
                    </div>
                    <div class="qiz_close_box">
                        <div class="test_heading">
                            <p class="quiz-text-number rz-s-p fz-20 p-10 fw-500">Quiz 1: Yoga Test</p>
                            <p class="time_date float-end rz-s-p p-10">Added on: 3pm April 25 ,2021</p>
                        </div>
                    </div>
                    <div class="qiz_close_box">
                        <div class="test_heading">
                            <p class="quiz-text-number rz-s-p fz-20 p-10 fw-500">Quiz 1: Yoga Test</p>
                            <p class="time_date float-end rz-s-p p-10">Added on: 3pm April 25 ,2021</p>
                        </div>
                    </div>
                    <div class="qiz_close_box">
                        <div class="test_heading">
                            <p class="quiz-text-number rz-s-p fz-20 p-10 fw-500">Quiz 1: Yoga Test</p>
                            <p class="time_date float-end rz-s-p p-10">Added on: 3pm April 25 ,2021</p>
                        </div>
                    </div>
                    <div class="qiz_close_box">
                        <div class="test_heading">
                            <p class="quiz-text-number rz-s-p fz-20 p-10 fw-500">Quiz 1: Yoga Test</p>
                            <p class="time_date float-end rz-s-p p-10">Added on: 3pm April 25 ,2021</p>
                        </div>
                    </div>
                    <div class="qiz_close_box">
                        <div class="test_heading">
                            <p class="quiz-text-number rz-s-p fz-20 p-10 fw-500">Quiz 1: Yoga Test</p>
                            <p class="time_date float-end rz-s-p p-10">Added on: 3pm April 25 ,2021</p>
                        </div>
                    </div>
                    <div class="qiz_close_box">
                        <div class="test_heading">
                            <p class="quiz-text-number rz-s-p fz-20 p-10 fw-500">Quiz 1: Yoga Test</p>
                            <p class="time_date float-end rz-s-p p-10">Added on: 3pm April 25 ,2021</p>
                        </div>
                    </div>
                    <div class="qiz_close_box">
                        <div class="test_heading">
                            <p class="quiz-text-number rz-s-p fz-20 p-10 fw-500">Quiz 1: Yoga Test</p>
                            <p class="time_date float-end rz-s-p p-10">Added on: 3pm April 25 ,2021</p>
                        </div>
                    </div>

                    <!-- quiz close box end  -->
                </div>
                <!-- quiz colum end -->
            </div>
        </div>
    </div>
<?php
    return ob_get_clean();
});


/**
 * update banner status
 */
add_action('wp_ajax_rz_update_banner_status', function(){
   global $wpdb;
    $nonce = $_POST['nonce'];

    if(wp_verify_nonce($nonce, 'rz-update-banner-nonce')){
        $rz_user_profile_data = $wpdb->prefix.'rz_user_profile_data';
        $current_user_id = get_current_user_id();
        $profile_data = $wpdb->get_row("SELECT * FROM {$rz_user_profile_data} WHERE user_id = '{$current_user_id}'");

        if(empty($profile_data)){
            $wpdb->insert($rz_user_profile_data, [
                'user_id' => $current_user_id,
                'banner_status' => 'disabled'
            ]);
        }else{
            $wpdb->update($rz_user_profile_data, [
                'banner_status' => 'disabled'
            ], ['user_id' => $current_user_id]);
        }
    }
   die();
});

/**
 * tags archive page
 */
add_shortcode('imit-tags-archive', function(){
    ob_start();
    global $wpdb;
    ?>
    <section class="users">
        <div class="container">
            <div class="row">
                <div class="col-md-9">

                    <div class="user-header d-flex flex-row justify-content-between align-items-center">
                        <div class="user-header-left">
                            <ul class="ps-0 mb-0 bread-crumb d-flex flex-row justify-content-between align-items-center">
                                <li class="list-unstyled">
                                    <a href="<?php echo site_url(  ); ?>" class="imit-font fz-14 rz-secondary-color text-decoration-none">Home<span class="mx-1">/</span></a>
                                </li>
                                <li class="list-unstyled">
                                    <a href="#" class="imit-font fz-14 rz-secondary-color text-decoration-none active">tags<span class="mx-1">/</span></a>
                                </li>
                            </ul>
                        </div>
                        <div class="user-header-right">
                            <form >
                                <input name="search-user" type="text" class="form-control imit-font fz-14" placeholder="Search tags">
                                <button type="submit" class="text-dark fz-14 border-0 bg-transparent"><i class="fas fa-search"></i></button>
                            </form>
                        </div>
                    </div>
                    <ul class="hash-tags ps-0 mb-0 row">
                        <?php
                            $tags = get_terms(array(
                                'taxonomy' => 'question_tags',
                                'orderby' => 'count',
                                'order' => 'DESC',
                                'number' => 40,
                                'offset' => $offset,
                            ));

                            foreach($tags as $tag){
                                $posts_array = get_posts(
                                    array(
                                        'posts_per_page' => -1,
                                        'post_type' => 'rz_post_question',
                                        'fields' => 'ids',
                                        'tax_query' => array(
                                            array(
                                                'taxonomy' => 'question_tags',
                                                'field' => 'term_id',
                                                'terms' => $tag->term_id,
                                            )
                                        )
                                    )
                                );

                                $count_answer = 0;
                                $total_view = 0;
                                foreach($posts_array as $post_ids){
                                    $all_answers = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_answers WHERE post_id = '$post_ids'", ARRAY_A);

                                    $count_answer += count($all_answers);

                                    $count_key = 'post_views_count';
                                    $count = get_post_meta($post_ids, $count_key, true);
                                    if($count==''){
                                        delete_post_meta($post_ids, $count_key);
                                        add_post_meta($post_ids, $count_key, '0');
                                        $total_view += 0;
                                    }
                                    $total_view += $count;
                                }
                                ?>
                                <li class="hash-list list-unstyled col-md-4 my-2">
                                    <div class="bg-white py-2 px-3 rounded border">
                                        <div class="hash-top d-flex flex-row justify-content-between align-items-center">
                                            <a href="<?php echo get_term_link($tag->term_id, 'question_tags'); ?>" class="imit-font fw-500 fz-16 text-dark d-block">#<?php echo $tag->name; ?></a>
        <!--                                            <button type="button" class="add-post-by-tag p-0 rz-secondary-color bg-transparent fz-14"><i class="fas fa-plus-circle"></i></button>-->
                                        </div>
                                        <div class="d-flex flex-row justify-content-between align-items-center me-2">
                                            <p class="rz-secondary-color imit-font fz-12 fw-400 mb-0"><?php echo $tag->count; ?> Question</p>
                                            <p class="rz-secondary-color imit-font fz-12 fw-400 mb-0"><?php echo $count_answer; ?> Answers</p>
                                            <p class="rz-secondary-color imit-font fz-12 fw-400 mb-0"><?php echo $total_view; ?> Views</p>
                                        </div>
                                    </div>
                                </li>
                                    <?php
                            }
                            ?>
                        </ul>
                </div>
                <div class="col-md-3">
                    <div class="join rz-br rz-bg-color rounded-2 p-3" style="background-image: url('<?php echo plugins_url('images/Group 237.png', __FILE__); ?>');">
                        <h3 class="title m-0 text-white imit-font fz-20 fw-500">Join our Partner Program and earn money on Recozilla</h3>
                        <a href="#" class="btn bg-white fz-12 rz-color imit-font fw-500 mt-3">Join Now</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php
    return ob_get_clean();
});

/**
 * send message
 */
add_action('wp_ajax_rz_add_message_action', function(){
    global $wpdb;
    $nonce = $_POST['nonce'];
    if(wp_verify_nonce( $nonce, 'rz-send-message-nonce' )){
        $message = sanitize_text_field( $_POST['message'] );
        $receiver_id = sanitize_key( $_POST['user_id'] );
        $sender_id = get_current_user_id(  );


        if(empty($message) || empty($receiver_id) || empty($sender_id)){
            echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">
            <strong>Wait!</strong> Please type something.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
        }else{
            $wpdb->insert($wpdb->prefix.'massages', [
                'sender_id' => $sender_id,
                'received_id' => $receiver_id,
                'massage_text' => $message,
                'status' => 1
            ]);
        }
    }
    die();
});