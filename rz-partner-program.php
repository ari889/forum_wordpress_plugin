<?php

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
            echo '<div class="alert alert-warning alert-dismissible fade show imit-font fz-16" role="alert">
                  <strong>Warning!</strong> Please write something about you.
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
        }else if(count($is_user_requested)){
            echo '<div class="alert alert-warning alert-danger fade show imit-font fz-16" role="alert">
                  <strong>Stop!</strong> You are already requested. Please hold on.
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
        }else{
            $wpdb->insert($rz_user_table, [
                'user_id' => $user_id,
                'request_text' => $request_text,
                'status' => '0'
            ]);
            echo '<div class="alert alert-success alert-success fade show imit-font fz-16" role="alert">
                  <strong>Success!</strong> Your request has been submitted. We will ping you soon.
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
        }
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

    $imit_recozilla_program = $wpdb->get_results("SELECT * FROM {$program_table} ORDER BY id DESC", ARRAY_A);
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
        $status = sanitize_text_field($_POST['status']);
        $id = sanitize_key($_POST['id']);
        $program_table = $wpdb->prefix.'rz_user_programs';

        if(!empty($status) && !empty($id)){
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