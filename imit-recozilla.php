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
    $rz_quiz_questions = $wpdb->prefix.'rz_quiz_questions';

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


    $sql[] = "CREATE TABLE {$rz_quiz_questions} (
        id INT (11) NOT NULL AUTO_INCREMENT,
        quiz_id INT (11) NOT NULL,
        question VARCHAR (250) NOT NULL,
        answers VARCHAR (250) NOT NULL,
        correct_answer VARCHAR (250) NOT NULL,
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

    register_taxonomy( 'question_category', 'rz_post_question', [
        'public' => true,
        'hierarchical' => true,
        'default_term' => [
            'name' => 'Uncategorised',
            'slug' => 'uncategorised',
        ]
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


    /**
     * search terms
     */
    $rz_search_term_by_name_nonce = wp_create_nonce( 'rz-search-terms-nonce' );
    wp_localize_script( 'imit-recozilla', 'rzSearchTerm', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_search_term_by_name_nonce' => $rz_search_term_by_name_nonce
    ] );

    /**
     * get all notification
     */
    $rz_get_all_notification_nonce = wp_create_nonce( 'rz-get-all-notification-nonce' );
    wp_localize_script( 'imit-recozilla', 'rzGetNotification', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_get_all_notification_nonce' => $rz_get_all_notification_nonce
    ] );


    /**
     * get all message nonce
     */
    $rz_get_all_message_nonce = wp_create_nonce( 'rz-get-all-message-nonce' );
    wp_localize_script( 'imit-recozilla', 'rzGetMessage', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_get_all_message_nonce' => $rz_get_all_message_nonce
    ] );

    /**
     * get live notification
     */
    $rz_get_live_notification_nonce = wp_create_nonce( 'rz-live-notification-nonce' );
    wp_localize_script( 'imit-recozilla', 'rzGetLiveNotifiation', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_get_live_notification_nonce' => $rz_get_live_notification_nonce
    ] );

    /**
     * delete question
     */
    $rz_get_quiz_result_nonce = wp_create_nonce( 'rz-get-quiz-result-nonce' );
    wp_localize_script( 'imit-recozilla', 'rzGetQuizResult', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_get_quiz_result_nonce' => $rz_get_quiz_result_nonce
    ] );


    /**
     * delete reply
     */
    $rz_delete_reply_nonce = wp_create_nonce( 'rz-delete-reply-nonce' );
    wp_localize_script( 'imit-recozilla', 'rzDeleteReply', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_delete_reply_nonce' => $rz_delete_reply_nonce
    ] );

    /**
     * delet question comment
     */
    $rz_delete_comment_nonce = wp_create_nonce( 'rz-delete-comment-nonce' );
    wp_localize_script( 'imit-recozilla', 'rzDeleteComment', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'rz_delete_comment_nonce' => $rz_delete_comment_nonce
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

        if($hook === 'quiz_page_rzmanageQuestions'){
            wp_enqueue_script( 'imit-admin-question-js', PLUGINS_URL('js/admin-question.js', __FILE__), ['imit-jquery-js'], true, true );

            /**
             * get answer info
             */
            $rz_view_question_nonce = wp_create_nonce( 'rz-view-question-nonce' );
            wp_localize_script( 'imit-admin-js', 'rzViewQuestion', [
                'ajax_url' => admin_url('admin-ajax.php'),
                'rz_view_question_nonce' => $rz_view_question_nonce
            ] );

            /**
             * edit question
             */
            $rz_edit_question_nonce = wp_create_nonce( 'rz-edit-question-nonce' );
            wp_localize_script( 'imit-admin-js', 'rzEditQuestion', [
                'ajax_url' => admin_url('admin-ajax.php'),
                'rz_edit_question_nonce' => $rz_edit_question_nonce
            ] );

        }else if($hook === 'manage-answer_page_rzAllAnswers'){
            wp_enqueue_script( 'imit-admin-asnwer-js', PLUGINS_URL('js/admin-answer.js', __FILE__), ['imit-jquery-js'], true, true );
        }

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

        /**
         * admin submit answer
         */
        $rz_admin_submit_answer_nonce = wp_create_nonce( 'rz-admin-submit-answer-nonce' );
        wp_localize_script( 'imit-admin-js', 'rzAdminSubmitAnswer', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'rz_admin_submit_answer_nonce' => $rz_admin_submit_answer_nonce
        ] );

        /**
         * get all anwers based on quiz
         */
        $rz_answer_on_quiz_nonce = wp_create_nonce( 'rz-answer-using-quiz-nonce' );
        wp_localize_script( 'imit-admin-js', 'rzAnsUsingQuiz', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'rz_answer_on_quiz_nonce' => $rz_answer_on_quiz_nonce
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
    global $wpdb;
    ob_start();

    $get_all_quizes = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_quizzes ORDER BY id DESC", ARRAY_A);
    ?>
    <div class="rz-mid quiz_main_section">
        <div class="row">
            <div class="col-md-8">
                <!-- quiz colum start -->
                <div class="main-column">
                    <div class="quiz_heading pb-0">
                        <h2 class="text-left rz-color fz-24 fw-500">Test your Knowledge</h2>
                    </div>
                    

                    <!-- quiz close box  -->
                    <?php 
                    if(count($get_all_quizes) > 0){
                        foreach($get_all_quizes as $quiz){
                            $quiz_id = $quiz['id'];
                            ?>
                            <!-- quize open box -->
                            <div class="p-0 rz-border rounded bg-white mt-3">
                                <div class="test_heading d-flex flex-row justify-content-between align-items-center p-3" data-quiz_id="<?php echo $quiz['id']; ?>" id="show-quiz" style="cursor: pointer;">
                                    <p class="quiz-text-number rz-s-p fz-20 p-10 fw-500 mb-0 rz-secondary-color">Quiz 1: <?php echo $quiz['quiz_name']; ?></p>
                                    <p class="time_date float-end rz-s-p p-10 mb-0 rz-secondary-color">Added on: <?php echo date('F d, Y g:i a', strtotime($quiz['created_at'])); ?></p>
                                </div>


                                <form id="quiz-submission-form" data-quiz_id="<?php echo $quiz['id']; ?>">
                                    <div style="display: none;" id="quiz-start<?php echo $quiz['id']; ?>" class="p-3 pt-0">
                                        <?php
                                        $get_all_questions = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_quiz_questions WHERE quiz_id = '{$quiz_id}' ORDER BY id DESC", ARRAY_A);
                                        $q = 0;
                                        foreach($get_all_questions as $question){
                                            ?>
                                            <div id="question<?php echo $quiz_id.$q; ?>" <?php if($q != 0){echo 'style="display: none;"';} ?> class="question">
                                                <div class="quiz-question p-10 d-flex flex-row justify-content-start align-items-center">
                                                    <span class="question-img"><img src="<?php echo plugins_url('images/Vector.png', __FILE__); ?>" alt=""></span>
                                                    <h2 class="fw-500 text-dark" style="font-size: 32px;"><span class="rz-color me-2">.</span><?php echo $question['question']; ?></h2>
                                                </div>
                                                <p class="imit-font fz-14 rz-secondary-color">Find the correct answer.</p>
                                                <div id="message-error" class="text-danger"></div>
                                                <?php
                                                $answers = json_decode($question['answers']);
                                                $a = 0;
                                                foreach($answers as $answer){
                                                    ?>
                                                    <div class="form-check mt-2">
                                                        <input class="form-check-input" type="radio" name="answer<?php echo $q; ?>" id="asnwer<?php echo $question['id'].$a; ?>" value="<?php echo $answer; ?>">
                                                        <label class="form-check-label rz-secondary-color" for="asnwer<?php echo $question['id'].$a; ?>">
                                                            <?php echo $answer; ?>
                                                        </label>
                                                    </div>
                                                    <?php
                                                    $a++;
                                                }
                                                ?>
                                                <div class="row">
                                                    <div class="col-md-4"></div>
                                                    <div class="col-md-4"><p class="text-center achived_number rz-s-p mb-0"><span id="counter">1</span> of <?php echo count($get_all_questions); ?> Questions</p></div>
                                                    <div class="col-md-4 float-end">
                                                        <?php if(($q+1) == count($get_all_questions)){
                                                            ?>
                                                            <button type="submit" class="text-center next-number rz-s-p mb-0 btn rz-bg-color text-white w-100 border-0">Submit</button>
                                                            <?php
                                                        }else{
                                                            ?>
                                                            <button type="button" class="text-center next-number rz-s-p mb-0 btn rz-bg-color text-white w-100 border-0" data-target="<?php echo $quiz_id.$q+1; ?>" data-quiz_id="<?php echo $quiz_id; ?>" id="next-question">Next question</button>
                                                            <?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php
                                            $q++;
                                        }
                                        ?>
                                    </div>
                                </form>
                            </div>
                            <!-- quiz opne box end  -->
                            <?php
                        }
                    }
                    ?>
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
        <div class="rz-mid">
            <div class="row">
                <div class="col-lg-9">

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
                            <form id="search-terms">
                                <input name="search-terms" type="text" class="form-control imit-font fz-14" placeholder="Search tags">
                                <button type="submit" class="text-dark fz-14 border-0 bg-transparent"><i class="fas fa-search"></i></button>
                            </form>
                        </div>
                    </div>
                    <ul class="hash-tags ps-0 mb-0 row mt-3" id="fetch-all-terms">
                        <?php
                            $tags = get_terms(array(
                                'taxonomy' => 'question_tags',
                                'orderby' => 'count',
                                'order' => 'DESC',
                                'number' => 40,
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
                <div class="col-lg-3">
                    <div class="join rz-br rz-bg-color rounded-2 p-3" style="background-image: url('<?php echo plugins_url('images/Group 237.png', __FILE__); ?>');">
                        <h3 class="title m-0 text-white imit-font fz-20 fw-500">Join our Partner Program and earn money on Recozilla</h3>
                        <a href="<?php echo site_url(); ?>/join-partner-program/" class="btn bg-white fz-12 rz-color imit-font fw-500 mt-3">Join Now</a>
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
                'date_time' => date_i18n( "Y-m-d H:i" ),
            ]);
        }
    }
    die();
});


/**
 * get terms by name
 */
add_action('wp_ajax_nopriv_rz_search_terms_by_name', 'rz_get_terms_by_name');
add_action('wp_ajax_rz_search_terms_by_name', 'rz_get_terms_by_name');

function rz_get_terms_by_name(){
    global $wpdb;
    $nonce = $_POST['nonce'];
    if(wp_verify_nonce( $nonce, 'rz-search-terms-nonce' )){
        $search_terms = sanitize_text_field( $_POST['search-terms'] );

        if(!empty($search_terms)){
            $args = array(
                'taxonomy'      => ['question_tags', 'discussion_tags'], // taxonomy name
                'orderby'       => 'count', 
                'order'         => 'DESC',
                'hide_empty'    => true,
                'fields'        => 'all',
                'name__like'    => $search_terms,
                'number'        => 20
            ); 
            
            $terms = get_terms( $args );
            foreach($terms as $tag){
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
                                <a href="<?php echo get_term_link($tag->term_id, $tag->taxonomy); ?>" class="imit-font fw-500 fz-16 text-dark d-block">#<?php echo $tag->name; ?></a>
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
        }
    }
    die();
}

/**
 * rz fetch notification
 */
add_shortcode('imit-rz-fetch-notification', function(){
    global $wpdb;
    ob_start();
    if(is_user_logged_in(  )){
        $receiver_id = get_current_user_id(  );
        $count_notification = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}notification WHERE receiver_id = '$receiver_id'", ARRAY_A);
        $count_message = $wpdb->get_results("select DISTINCT if( received_id={$receiver_id},sender_id,received_id) 
        AS id from wp_massages  WHERE (sender_id = {$receiver_id} OR received_id = {$receiver_id}) AND status = 0
         ORDER by id DESC");
        ?>
        <div class="position-relative d-table">
            <a href="#" class="p-0 border-0 text-dark fz-20 notification-button d-table position-relative" id="notification-bell">
                <i class="fas fa-bell"></i>
                <div id="notification-active">
                </div>
            </a>
            <div class="dropdown-notification rz-border shadow">
                <ul class="tab-nav mb-0 ps-0 d-flex flex-row justify-content-between align-items-ceneter">
                    <li class="tab-list list-unstyled">
                        <a href="#" class="tab-link  imit-font active" data-target="notification-tab">Notification <span>(<?php echo count($count_notification); ?>)</span></a>
                    </li>
                    <li class="tab-list list-unstyled">
                        <a href="#" class="tab-link imit-font" data-target="inbox-tab">Inbox <span>(<?php echo count($count_message); ?>)</span></a>
                    </li>
                </ul>
                <div class="tab-content" id="notification-tab">
                    <ul class="notifications ps-0 mb-0" id="notification-tab-ul">
                        
                    </ul>
                </div>
                <div class="tab-content" style="display: none;" id="inbox-tab">
                    <ul class="message-users ps-0 mb-0" id="inbox-tab-ul">
                    </ul>
                </div>
            </div>
        </div>
        <?php
    }
    return ob_get_clean();
});


/**
 * get all notification
 */
add_action('wp_ajax_rz_get_all_notification', function(){
    global $wpdb;
    $nonce = $_POST['nonce'];
    if(wp_verify_nonce($nonce, 'rz-get-all-notification-nonce')){
        $receiver_id = get_current_user_id(  );
        $all_notification = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}notification WHERE receiver_id = '{$receiver_id}' ORDER BY id DESC LIMIT 10", ARRAY_A);

        foreach($all_notification as $notification){
            ?>
            <li class="notifications-lists list-unstyled">
                <a href="<?php echo $notification['url_link']; ?>" class="notification-link imit-font fz-12 rz-secondary-color fw-500 <?php if($notification['status'] == 0){echo 'active';} ?>"><?php echo $notification['massage_text']; ?></a>
            </li>
            <?php
        }
    }
    die();
});

/**
 * get all message
 */
add_action('wp_ajax_rz_get_all_message', function(){
    global $wpdb;
    $nonce = $_POST['nonce'];
    if(wp_verify_nonce( $nonce, 'rz-get-all-message-nonce')){
        foreach ( get_conversion_chat_list() as $row ) {
            $userinfo = get_user_by( 'id', $row->id );
            $last_massage = get_last_massage($row->id);
            ?>
            <li class="message-user-list list-unstyled">
                <a href="<?php echo site_url(); ?>/message" class="message-user-link <?php if($last_massage->status == 0){echo 'active';} ?>">
                    <div class="profile-info d-flex flex-row justify-content-start align-items-center">
                        <div class="profile-image">
                            <img src="<?php echo getProfileImageById($row->id); ?>" alt="">
                        </div>
                        <div class="info ms-2 w-100">
                            <div class="d-flex flex-row justify-content-between align-items-center">
                                <h2 class="name fz-14 imit-font m-0"><?php echo getUserNameById($row->id); ?><!-- <span></span>--></h2>
                                <p class="imit-font rz-color timeago fw-500 mb-0"><?php echo convert_time_to_days($last_massage->date_time); ?></p>
                            </div>
                            <p class="rz-secondary-color imit-font fz-12 mb-0"><?php echo substr( $last_massage->massage_text, 0, 37);  ?></p>
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
add_action('wp_ajax_rz_get_live_notification', function(){
    global $wpdb;
    $nonce = $_POST['nonce'];
    if(wp_verify_nonce( $nonce, 'rz-live-notification-nonce' )){
        $receiver_id = get_current_user_id(  );
        $count_notification = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}notification WHERE receiver_id = '$receiver_id'", ARRAY_A);
        $count_message = $wpdb->get_results("select DISTINCT if( received_id={$receiver_id},sender_id,received_id) 
        AS id from wp_massages  WHERE (sender_id = {$receiver_id} OR received_id = {$receiver_id}) AND status = 0
         ORDER by id DESC");

         if((count($count_notification) + count($count_message)) > 0){
             exit('exists');
         }
    }
    die();
});


/**
 * update feature post image category
 */
add_action('um_hourly_scheduled_events', function(){
    $get_feature_post = new WP_Query([
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
    while($get_feature_post->have_posts()):$get_feature_post->the_post();
    $post_id = get_the_ID();
    wp_set_post_terms($post_id, 'uncategorised', 'question_category');
    endwhile;
});