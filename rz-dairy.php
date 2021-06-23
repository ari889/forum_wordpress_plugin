<?php


/**
 *
 * add dairy
 */
add_action('wp_ajax_imit_rz_add_dairy', function(){
   global $wpdb;
    $nonce = $_POST['nonce'];
    if(wp_verify_nonce($nonce, 'rz-add-dairy-nonce')){
        $dairy_text = sanitize_text_field($_POST['dairy-text']);
        $dairy_visiblity = sanitize_text_field($_POST['dairy-visiblity']);
        $user_id = get_current_user_id();
        if(empty($dairy_visiblity)){
            $status = 'private';
        }else{
            $status = 'publish';
        }

        if(empty($dairy_text) || empty($status)){
            echo '<div class="alert imit-font fz-16 alert-warning alert-dismissible fade show" role="alert">
            <strong>Warning!</strong> Please fill the form correctly.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
        }else{
            $my_post = array(
                'post_type'    => 'rz_dairy',
                'post_content'  => $dairy_text,
                'post_status'   => $status,
                'post_author'   => $user_id,
            );

            // Insert the post into the database
            $post_id = wp_insert_post( $my_post );

            wp_update_post([
                'ID'           => $post_id,
                'post_title'   => $post_id,
            ]);

            echo '<div class="alert imit-font fz-16 alert-success alert-dismissible fade show" role="alert">
            <strong>Success!</strong> Dairy published.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
        }
    }
   die();
});