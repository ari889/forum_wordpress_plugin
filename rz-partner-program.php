<?php


/**
 * direct access not allowed
 */
if(!defined('ABSPATH')){
    die(__('Direct access not allowed.', 'imit-recozilla'));
}

/**
 * add partner program
 */
add_action('wp_ajax_rz_join_partner_program', function(){
    global $wpdb;
    $nonce = $_POST['nonce'];
    if(wp_verify_nonce($nonce, 'rz-add-partner-program-nonce')){
        $request_text = sanitize_text_field($_POST['partner_message']);
        $user_id = get_current_user_id();
        $rz_user_table = $wpdb->prefix.'rz_user_programs';

        $is_user_requested = $wpdb->get_results("SELECT * FROM {$rz_user_table} WHERE user_id = '$user_id'", ARRAY_A);

        if(empty($request_text) || empty($user_id)){
            $response['message'] = '<div class="alert alert-warning alert-dismissible fade show imit-font fz-16" role="alert">
                  <strong>Warning!</strong> Please write something about you.
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
            $response['error'] = true;
        }else{
            $time = date_i18n( "Y-m-d H:i:s" );

            if($is_user_requested[0]['status'] == '2'){
                $wpdb->update($rz_user_table, [
                    'request_text' => $request_text,
                    'status' => '0',
                    'updated_at' => $time
                ], ['id' => $is_user_requested[0]['id']]);
            }else{
                $wpdb->insert($rz_user_table, [
                    'user_id' => $user_id,
                    'request_text' => $request_text,
                    'status' => '0'
                ]);
            }
            $response['error'] = false;
        }

        echo json_encode($response);
    }
    die();
});

/**
 * fetch all partner
 */
function rz_partner_requests(){
    global $wpdb;

    $pid = $_GET['pid']??0;
    $pid = sanitize_key($pid);
    $program_table = $wpdb->prefix.'rz_user_programs';
    if(isset($pid) && $pid != 0){
        if(!isset($_GET['n']) || !wp_verify_nonce($_GET['n'], 'imit-change-user-status')){
            wp_die(__('Sorry you aren\'t allowed to do this', 'imit-recozilla'));
        }
    }

    if($pid && $pid !== 0){
        $result = $wpdb->get_row("SELECT * FROM {$program_table} WHERE id='{$pid}'");
        $user_data = get_userdata($result->user_id);

        if(!empty($user_data->user_firstname) && !empty($user_data->user_lastname)){
            $username = $user_data->user_firstname.' '.$user_data->user_lastname;
        }else{
            $username = $user_data->display_name;
        }
        ?>
        <div class="edit-form">
            <div class="edit-form-header">
                <h2 class="edit-form-title"><?php echo __($username.'\'s Request', 'imit-recozilla'); ?></h2>
            </div>
            <div class="edit-form-body">
                <form action="<?php echo admin_url('admin-post.php'); ?>" method="POST">
                    <p><?php echo $result->request_text; ?></p>

                    <?php echo wp_nonce_field('imit-change-user-status', 'nonce'); ?>

                    <input type="hidden" name="action" value="imit_update_user_joining_request">

                    <select name="status">
                        <option value="1" <?php if($result->status == '1'){echo 'selected';} ?>>Accept</option>
                        <option value="0" <?php if($result->status == '0'){echo 'selected';} ?>>Pending</option>
                        <option value="2" <?php if($result->status == '2'){echo 'selected';} ?>>Denied</option>
                    </select>

                    <input type="hidden" name="id" value="<?php echo $result->id; ?>">

                    <?php submit_button('Update status'); ?>
                </form>
            </div>
        </div>
        <?php
    }

    $imit_recozilla_program = $wpdb->get_results("SELECT * FROM {$program_table} ORDER BY updated_at DESC", ARRAY_A);
    $imitRecozillaProgram = new ImitManageProgram($imit_recozilla_program);
    $imitRecozillaProgram->prepare_items();
    $imitRecozillaProgram->display();
}

/**
 * update user status
 */
add_action('admin_post_imit_update_user_joining_request', function(){
    global $wpdb;
    $nonce = $_POST['nonce'];
    if(wp_verify_nonce($nonce, 'imit-change-user-status')){
        $status = sanitize_key($_POST['status']);
        $id = sanitize_key($_POST['id']);
        $program_table = $wpdb->prefix.'rz_user_programs';

        $get_partner_data = $wpdb->get_row("SELECT * FROM {$program_table} WHERE id = '{$id}'");

        if(isset($status) && !empty($id)){
            $get_user_data = get_userdata( $get_partner_data->user_id );
            if($status == '1'){
                $message = "
                    Hello ".getUserNameById($get_partner_data->user_id).",<br>
                    <p>Your partner request has been accepted. <a href='".site_url().'/user/?ref=partner-program'."'>Click here</a> to check your profile.</p>
                ";
                wp_mail( $get_user_data->user_email, 'Your partner program request has been accepted', $message, '', '' );
            }else if($status == '2'){
                $message = "
                    Hello ".getUserNameById($get_partner_data->user_id).",<br>
                    <p>Your partner request has been denied. <a href='".site_url().'/user/?ref=partner-program'."'>Click here</a> to check your profile.</p>
                    ";
                wp_mail( $get_user_data->user_email, 'Your partner program request has been denied', $message, '', '' );
            }
            $wpdb->update($program_table, [
                'status' => $status
            ], ['id' => $id]);
            wp_redirect('admin.php?page=rzPartnerRequests&pid='.$id.'&n='.$nonce);
        }
    }
    die();
});

/**
 * add admin menu
 */
add_action('admin_menu', function(){
    /**
     * add main menu
     */
    add_menu_page('Partner', 'Partner', 'menage_options', 'rzPartner', 'rz_partner', 'dashicons-admin-users');

    /**
     * add league submenu
     */
    add_submenu_page('rzPartner', 'All requests', 'All requests', 'manage_options', 'rzPartnerRequests', 'rz_partner_requests');
});

/**
 * partner program join button
 */
add_shortcode('partner-program-button', function($atts){
    ob_start();
    $class = esc_attr( $atts['class'] );
    $text = esc_attr( $atts['text'] );

    if(is_user_logged_in(  )){
        $get_user_data = wp_get_current_user(  );
        $referance_url = site_url()."/user/".$get_user_data->user_login.'?ref=partner-program';
        echo '<a href="'.$referance_url.'" class="'.$class.'">'.$text.'</a>';
    }else{
        echo '<a href="#" class="'.$class.'" data-bs-toggle="modal" data-bs-target="#login-modal">'.$text.'</a>';
    }
    return ob_get_clean();
});

/**
 * partner program joining ui
 */
add_shortcode('join-partner-program', function(){
    global $wpdb;
    ob_start();
    $user_id = get_current_user_id(  );
    $is_user_already_a_partner = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_user_programs WHERE user_id = '$user_id' AND (status = '1' OR status = '0')", ARRAY_A);
    if(is_user_logged_in() == false || count($is_user_already_a_partner) < 1){
        ?>
        <div class="join rz-br rz-bg-color rounded-2 p-3" style="background-image: url('<?php echo plugins_url('images/Group 237.png', __FILE__); ?>');">
            <h3 class="title m-0 text-white imit-font fz-20 fw-500">Join our Partner Program and earn money on Recozilla</h3>
            <?php echo do_shortcode( '[partner-program-button class="btn bg-white fz-12 rz-color imit-font fw-500 mt-3" text="Join Now"]' ); ?>
        </div>
        <?php
    }
    return ob_get_clean();
});