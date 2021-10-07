<?php


/**
 * direct access not allowed
 */
if (!defined('ABSPATH')) {
    die(__('Direct access not allowed.', 'imit-recozilla'));
}


/**
 * rz fetch notification
 */
add_shortcode('imit-rz-fetch-notification', function () {
    global $wpdb;
    ob_start();
    if (is_user_logged_in()) {
        $receiver_id = get_current_user_id();
        $rz_user_profile_data = $wpdb->prefix . 'rz_user_profile_data';
        $get_profile_data = $wpdb->get_row("SELECT * FROM {$rz_user_profile_data} WHERE user_id = '$receiver_id'");
        $count_notification = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}notification WHERE receiver_id = '$receiver_id'", ARRAY_A);
        $count_message = $wpdb->get_results("select DISTINCT if( received_id={$receiver_id},sender_id,received_id) 
        AS id from wp_massages  WHERE (sender_id = {$receiver_id} OR received_id = {$receiver_id}) AND status = 0
         ORDER by id DESC");
?>
        <div class="position-relative d-table">
            <a href="#" class="p-0 border-0 text-dark fz-20 notification-button d-table position-relative notification-bell" data-target="desktop-dropdown" id="notification-bell">
                <i class="fas fa-bell"></i>
                <div id="notification-active">
                    <span class="<?php echo (($get_profile_data->notification_seen == true) ? 'd-none' : 'd-block'); ?>"></span>
                </div>
            </a>
            <div class="dropdown-notification rz-border shadow" id="desktop-dropdown">
                <ul class="notification-tab-nav mb-0 ps-0 d-flex flex-row justify-content-between align-items-ceneter">
                    <li class="notification-tab-list list-unstyled">
                        <a href="#" class="notification-link imit-font active notification-counter" id="notification-tab-link" data-target="notification-tab">Notification (<span><?php echo count($count_notification); ?></span>)</a>
                    </li>
                    <li class="notification-tab-list list-unstyled">
                        <a href="#" class="notification-link imit-font message-counter" id="notification-tab-link" data-target="inbox-tab">Inbox <span>(<?php echo count($count_message); ?>)</span></a>
                    </li>
                </ul>
                <div class="notification-tab-content" id="notification-tab">
                    <ul class="notifications ps-0 mb-0" id="notification-tab-ul">

                    </ul>
                </div>
                <div class="notification-tab-content" style="display: none;" id="inbox-tab">
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
 * rz fetch notification for mobile
 */
add_shortcode('imit-rz-fetch-notification-mobile', function () {
    global $wpdb;
    ob_start();
    if (is_user_logged_in()) {
        $receiver_id = get_current_user_id();
        $rz_user_profile_data = $wpdb->prefix . 'rz_user_profile_data';
        $get_profile_data = $wpdb->get_row("SELECT * FROM {$rz_user_profile_data} WHERE user_id = '$receiver_id'");
        $count_notification = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}notification WHERE receiver_id = '$receiver_id'", ARRAY_A);
        $count_message = $wpdb->get_results("select DISTINCT if( received_id={$receiver_id},sender_id,received_id) 
        AS id from wp_massages  WHERE (sender_id = {$receiver_id} OR received_id = {$receiver_id}) AND status = 0
         ORDER by id DESC");
    ?>
        <div class="position-relative d-table">
            <a href="#" class="p-0 border-0 text-dark fz-20 notification-button d-table position-relative notification-bell" data-target="mobile-dropdown" id="notification-bell-mobile">
                <i class="fas fa-bell"></i>
                <div id="notification-active-mobile">
                    <span class="<?php echo (($get_profile_data->notification_seen == true) ? 'd-none' : 'd-block'); ?>"></span>
                </div>
            </a>
        </div>
        <?php
    }
    return ob_get_clean();
});


/**
 * get all notification
 */
add_action('wp_ajax_rz_get_all_notification', function () {
    global $wpdb;
    $nonce = $_POST['nonce'];
    if (wp_verify_nonce($nonce, 'rz-get-all-notification-nonce')) {
        $receiver_id = get_current_user_id();
        $start = sanitize_key($_POST['start']);
        $all_notification = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}notification WHERE receiver_id = '{$receiver_id}' ORDER BY id DESC LIMIT {$start}, 10", ARRAY_A);

        $current_time = time();

        if (count($all_notification) > 0) {
            foreach ($all_notification as $notification) {
        ?>
                <li class="notifications-lists list-unstyled">
                    <a href="<?php echo $notification['url_link']; ?>" id="single-notification" data-notification_id="<?php echo $notification['id']; ?>" class="notification-link imit-font fz-12 rz-secondary-color fw-500 d-flex flex-row justify-content-start align-items-start <?php if ($notification['status'] == 0) {
                                                                                                                                                                                                                                                                                            echo 'active';
                                                                                                                                                                                                                                                                                        } ?>">
                        <span class="sender-image">
                            <img src="<?php echo getProfileImageById($notification['sender_id']); ?>" alt="">
                        </span>
                        <span class="ms-2"><span class="d-block"><?php echo $notification['massage_text']; ?></span> <span class="d-block"><?php echo human_time_diff(intval($notification['date_time']),  intval($current_time)) ?> ago</span></span>
                    </a>
                </li>
    <?php
            }
        } else {
            exit('notificationReachMax');
        }
    }
    die();
});

/**
 * update notification status
 */
add_action('wp_ajax_rz_update_notification_status', function () {
    global $wpdb;
    $nonce = $_POST['nonce'];
    if (wp_verify_nonce($nonce, 'rz-update-notification-status-nonce')) {
        $id = sanitize_key($_POST['id']);

        $get_notification = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}notification WHERE id = '{$id}'");

        if (!empty($id) && $get_notification->status == 0) {
            $wpdb->update($wpdb->prefix . 'notification', [
                'status' => 1
            ], ['id' => $id]);
            exit('done');
        }
    }
    die();
});


/**
 * update notification status
 */
add_action('wp_ajax_rz_update_notification_seen', function () {
    global $wpdb;
    $nonce = $_POST['nonce'];
    if (wp_verify_nonce($nonce, 'imit-notification-dot-visible')) {
        $rz_user_profile_data = $wpdb->prefix . 'rz_user_profile_data';
        $user_id = sanitize_key($_POST['user_id']);

        $get_profile_data = $wpdb->get_row("SELECT * FROM {$rz_user_profile_data} WHERE user_id = '$user_id'");

        if (empty($get_profile_data)) {
            $wpdb->insert($rz_user_profile_data, [
                'notification_seen' => false,
                'user_id' => $user_id,
                'created_at' => wpDateTime(),
                'updated_at' => wpDateTime()
            ]);
        } else {
            $wpdb->update($rz_user_profile_data, [
                'notification_seen' => false
            ], ['user_id' => $user_id]);
        }
    }
    die();
});



/**
 * update notification status when user click on the bell icon
 */
add_action('wp_ajax_rz_update_notification_seen_status', function () {
    global $wpdb;
    $nonce = $_POST['nonce'];
    if (wp_verify_nonce($nonce, 'rz-update-notification-seen-status-nonce')) {
        $rz_user_profile_data = $wpdb->prefix . 'rz_user_profile_data';
        $user_id = get_current_user_id();

        $wpdb->update($rz_user_profile_data, [
            'notification_seen' => true,
        ], ['user_id' => $user_id]);


        $wpdb->update($wpdb->prefix . 'notification', [
            'status' => 1
        ], ['receiver_id' => $user_id]);

        echo esc_url(site_url() . '/notifications');
    }
    die();
});


/**
 * all notification
 */
add_shortcode('imit-get-all-notification', function () {
    ob_start();
    global $wpdb;
    $receiver_id = get_current_user_id();
    $start = 0;
    $all_notification = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}notification WHERE receiver_id = '$receiver_id' ORDER BY id DESC LIMIT {$start}, 15", ARRAY_A);

    $current_time = time();
    ?>
    <?php $get_current_userdata = get_userdata(get_current_user_id()); ?>

    <div class="d-flex flex-row justify-content-between align-items-center mb-3">
        <a href="<?php echo wp_get_referer(); ?>" class="d-block rz-color imit-font fz-14 fw-500 text-decoration-none"><i class="fas fa-arrow-left me-2 rz-color"></i>Back</a>
        <ul class="bread-crumb ps-0 mb-0 d-flex flex-row justify-content-start align-items-center">
            <li class="bread-crumb-list list-unstyled">
                <a href="<?php bloginfo('home'); ?>" class="bread-crumb-link imit-font fz-14 text-decoration-none">Home <span>/</span></a>
            </li>
            <li class="bread-crumb-list list-unstyled">
                <a href="<?php echo site_url() . '/user/' . $get_current_userdata->user_login; ?>" class="bread-crumb-link imit-font fz-14 text-decoration-none">Profile <span>/</span></a>
            </li>
            <li class="bread-crumb-list list-unstyled">
                <a href="<?php echo site_url() . '/notifications'; ?>" class="bread-crumb-link imit-font fz-14 text-decoration-none active">Notifications<span>/</span></a>
            </li>
        </ul>
    </div>
    <div class="full-notification rz-mid">
        <ul class="notifications ps-0 mb-0" id="full-notification-data">
            <?php
            if (count($all_notification) > 0) {
                foreach ($all_notification as $notification) {
            ?>
                    <li class="notifications-lists list-unstyled">
                        <a href="<?php echo $notification['url_link']; ?>" id="single-notification" data-notification_id="<?php echo $notification['id']; ?>" class="notification-link imit-font fz-16 rz-secondary-color fw-500 d-flex flex-row justify-content-start align-items-start <?php if ($notification['status'] == 0) {
                                                                                                                                                                                                                                                                                                echo 'active';
                                                                                                                                                                                                                                                                                            } ?>">
                            <span class="sender-image">
                                <img src="<?php echo getProfileImageById($notification['sender_id']); ?>" alt="">
                            </span>
                            <span class="ms-2"><span class="d-block"><?php echo $notification['massage_text']; ?></span> <span class="d-block"><?php echo human_time_diff(intval($notification['date_time']),  intval($current_time)) ?> ago</span></span>
                        </a>
                    </li>
            <?php
                }
            } else {
                echo '<li class="bg-light rz-br rz-border p-5 text-center list-unstyled mt-3">
                        <p class="mb-0 imit-font fz-16 rz-secondary-color">No notification to show.</p>
                    </li>';
            }
            ?>
        </ul>
        <div id="notificationLoader" style="display: none;">
            <div class="d-flex justify-content-center">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
});


/**
 * get more notification
 */
add_action('wp_ajax_rz_get_full_notification', function () {
    global $wpdb;
    $nonce = $_POST['nonce'];
    if (wp_verify_nonce($nonce, 'rz-get-full-notification-nonce')) {
        $receiver_id = get_current_user_id();
        $start = sanitize_key($_POST['start']);
        $all_notification = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}notification WHERE receiver_id = '{$receiver_id}' ORDER BY id DESC LIMIT {$start}, 10", ARRAY_A);

        $current_time = time();

        if (count($all_notification) > 0) {
            foreach ($all_notification as $notification) {
    ?>
                <li class="notifications-lists list-unstyled">
                    <a href="<?php echo $notification['url_link']; ?>" id="single-notification" data-notification_id="<?php echo $notification['id']; ?>" class="notification-link imit-font fz-16 rz-secondary-color fw-500 d-flex flex-row justify-content-start align-items-start <?php if ($notification['status'] == 0) {
                                                                                                                                                                                                                                                                                            echo 'active';
                                                                                                                                                                                                                                                                                        } ?>">
                        <span class="sender-image">
                            <img src="<?php echo getProfileImageById($notification['sender_id']); ?>" alt="">
                        </span>
                        <span class="ms-2"><span class="d-block"><?php echo $notification['massage_text']; ?></span> <span class="d-block"><?php echo human_time_diff(intval($notification['date_time']),  intval($current_time)) ?> ago</span></span>
                    </a>
                </li>
<?php
            }
        } else {
            exit('notificationReachMax');
        }
    }
    die();
});
