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

/**
 * delete dairy
 */
add_action('wp_ajax_rz_delete_dairy', function(){
    global $wpdb;
    $nonce = $_POST['nonce'];
    if(wp_verify_nonce( $nonce, 'rz-delete-dairy-nonce' )){
        $post_id = sanitize_key($_POST['post_id']);
        if(!empty($post_id)){
            wp_delete_post($post_id, false);
            exit('done');
        }
    }
    die();
});

/**
 * change dairy visiblity
 */
add_action('wp_ajax_rz_change_dairy_visiblity_status', function(){
    global $wpdb;
    $nonce = $_POST['nonce'];
    if(wp_verify_nonce($nonce, 'rz-change-post-visiblity-nonce')){
        $post_id = sanitize_key($_POST['post_id']);
        $visiblity = sanitize_text_field( $_POST['visiblity'] );


        if(!empty($visiblity) && !empty($post_id)){
            wp_update_post([
                'ID' => $post_id,
                'post_status' => $visiblity
            ]);
        }
    }
    die();
});

/**
 * get dairy post by id
 */
add_action('wp_ajax_rz_get_post_by_id', function(){
    global $wpdb;
    $nonce = $_POST['nonce'];
    if(wp_verify_nonce($nonce, 'rz-get-post-by-id-nonce')){
        $post_id = sanitize_key($_POST['post_id']);
        
        $post_data = get_post($post_id);

        echo json_encode($post_data);
    }
    die();
});

/**
 * edit dairy
 */
add_action('wp_ajax_rz_edit_dairy', function(){
    global $wpdb;
    $nonce = $_POST['nonce'];
    if(wp_verify_nonce( $nonce, 'rz-edit-dairy-nonce' )){
        $dairy_text = sanitize_text_field($_POST['dairy-text']);
        $id = sanitize_key($_POST['id']);
        $dairy_visiblity = sanitize_text_field( $_POST['dairy-visiblity'] );

        if($dairy_visiblity == 'yes'){
            $status = 'publish';
        }else{
            $status = 'private';
        }

        if(empty($dairy_text) || empty($status)){
            $response['message'] = '<div class="alert imit-font fz-16 alert-warning alert-dismissible fade show" role="alert">
            <strong>Warning!</strong> Please fill the form correctly.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
          $response['error'] = true;
        }else{
            wp_update_post([
                'ID' => $id,
                'post_status' => $status,
                'post_content' => $dairy_text
            ]);
            $response['message'] = '<div class="alert imit-font fz-16 alert-success alert-dismissible fade show" role="alert">
            <strong>Success!</strong> Dairy updated.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
          $response['error'] = false;
        }

        echo json_encode($response);

    }
    die();
});