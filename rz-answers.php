<?php

 /**
   * add answer
   */
  add_action('wp_ajax_imit_add_answer', function(){
    global $wpdb;

    $nonce = $_POST['nonce'];

    if(wp_verify_nonce( $nonce, 'rz-add-answer-nonce' )){
        $answer = sanitize_text_field( $_POST['answer'] );
        $post_id = sanitize_key($_POST['post_id']);
        $rz_answers = $wpdb->prefix.'rz_answers';
        $user_id = get_current_user_id();
        $rz_point_table = $wpdb->prefix.'rz_point_table';
        $rz_user_profile_data = $wpdb->prefix.'rz_user_profile_data';
        $rz_partner_program = $wpdb->prefix.'rz_user_programs';

        $get_all_answers = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_answers WHERE post_id = '$post_id' AND user_id != '$user_id'", ARRAY_A);

        $get_post_author_id = $wpdb->get_row("SELECT post_author FROM {$wpdb->prefix}posts WHERE ID = '$post_id'");

        $is_user_joined_programme = $wpdb->get_results("SELECT * FROM {$rz_partner_program} WHERE user_id = '$user_id' AND status = '1'", ARRAY_A);

        if(count($get_all_answers) >= 1){
            $point = 10;
        }else{
            $point = 20;
        }

        if(!empty($answer) && !empty($post_id)){
            $wpdb->insert($rz_answers, [
                'user_id' => $user_id,
                'post_id' => $post_id,
                'answer_text' => $answer,
                'status' => '0'
            ]);

            $answer_id = $wpdb->insert_id;
            if(count($is_user_joined_programme) && $get_post_author_id->post_author != $user_id){
                $wpdb->insert($rz_point_table, [
                    'user_id' => $user_id,
                    'content_id' => $answer_id,
                    'point_type' => 'answer',
                    'point_earn' => $point
                ]);

                $get_point = $wpdb->get_row("SELECT * FROM {$rz_user_profile_data} WHERE user_id = '$user_id'");


                $user_point = $get_point->points;
                if(empty($get_point)){
                    $wpdb->insert($rz_user_profile_data, [
                        'points' => $point,
                        'user_id' => $user_id
                    ]);
                }else{
                    $wpdb->update($rz_user_profile_data, [
                        'points' => ($user_point+$point),
                    ], ['user_id' => $user_id]);
                }
            }

            preg_match_all("/#+([a-zA-Z0-9_]+)/i", $answer, $tags);

            if(!empty($tags)){
                $rz_hashtags = $wpdb->prefix.'rz_hashtags';
                preg_match_all("/#+([a-zA-Z0-9_]+)/i", $answer, $matches);
                if($matches){
                    $result = array_values($matches[1]);
                }

                foreach($result as $trends){
                    $wpdb->insert($rz_hashtags, [
                        'content_id' => $answer_id,
                        'content_type' => 'answers',
                        'hashtag' => $trends
                    ]);
                }
            }
        }
    }

    die();
});

/**
 * add admin menu
 */

function rz_all_answers(){
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

add_action('admin_menu', function(){
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
add_action( 'wp_ajax_rz_get_answer_info', function(){
    global $wpdb;
    $nonce = $_POST['nonce'];
    if(wp_verify_nonce( $nonce, 'rz-get-answer-info-nonce' )){
        $answer_id = sanitize_key( $_POST['answer_id'] );

        $get_answer = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}rz_answers WHERE id = '{$answer_id}'");

        $user_data = get_userdata( $get_answer->user_id );

        $post_data = get_post($get_answer->post_id);
        ?>
                <div class="modal-header">
                    <h2 style="font-size: 24px;" class="m-0">Checked answer for "asdasdasd"</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="user-info d-flex flex-row justify-content-start align-items-center">
                        <div class="profile-image rounded-circle" style="min-width: 42px;min-height: 42px;">
                            <img src="<?php getProfileImageById($get_answer->user_id); ?>" alt="" style="width: 42px;height: 42px;" class="rounded-circle">
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
                        <option value="1" <?php if($get_answer->status == '1'){echo 'selected';} ?>>Published</option>
                        <option value="0" <?php if($get_answer->status == '0'){echo 'selected';} ?>>Denied</option>
                    </select>
                </div>
        <?php
    }
    die();
} );

/**
 * change answer status
 */
add_action('wp_ajax_rz_change_answer_status', 'imit_rz_change_answer_status');

function imit_rz_change_answer_status(){
    global $wpdb;
    $nonce = $_POST['nonce'];
    if(wp_verify_nonce( $nonce, 'rz-change-answer-status-nonce' )){
        $answer_id = sanitize_key( $_POST['answer_id'] );
        $status = sanitize_text_field( $_POST['status'] );


        $wpdb->update($wpdb->prefix.'rz_answers', [
            'status' => $status
        ], ['id' => $answer_id]);
    }
    die();
}