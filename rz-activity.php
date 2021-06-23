<?php

/**
 * user activity show
 */
add_action('wp_ajax_nopriv_imit_fetch_activity', 'rz_user_activity');
add_action('wp_ajax_imit_fetch_activity', 'rz_user_activity');

function rz_user_activity(){
    global $wpdb;
    $all_activity = $wpdb->get_results("(SELECT ID as id, 'post' AS type, timestamp(post_date) AS date FROM {$wpdb->prefix}posts WHERE post_type = 'rz_post_question') UNION (SELECT id, 'answer' AS type, timestamp(created_at) AS date FROM {$wpdb->prefix}rz_answers) UNION (SELECT id, 'vote' AS type, timestamp(created_at) AS date FROM {$wpdb->prefix}rz_vote) ORDER BY date DESC LIMIT 10", ARRAY_A);
    foreach($all_activity as $activity){
        if($activity['type'] == 'post'){
            $activity_table = $wpdb->prefix.'post';
        }else if($activity['type'] == 'answer'){
            $activity_table = $wpdb->prefix.'rz_answers';
        }else{
            $activity_table = $wpdb->prefix.'rz_vote';
        }

        $activity_id = $activity['id'];

        if($activity['type'] == 'post'){
            $get_activity_data = get_post($activity_id);
        }else{
            $get_activity_data = $wpdb->get_row("SELECT * FROM {$activity_table} WHERE id = '$activity_id'");
        }

        if($activity['type'] == 'post'){
            $user_info = get_userdata($get_activity_data->post_author);
            $start_date = date('Y-m-d G:i:s');
            ?>
            <li>
                <a href="<?php echo get_permalink($get_activity_data->ID); ?>" class="d-flex flex-row justify-content-start align-items-center px-3 py-2">
                    <div class="activity-image">
                        <img src="<?php getProfileImageById( $get_activity_data->post_author ); ?>" alt="" style="min-width: 42px;">
                    </div>
                    <div class="activity-description ms-2">
                        <p class="mb-0 rz-secondary-color imit-font fw-400 fz-16"><?php echo $user_info->display_name; ?> asked a new question <span class="imit-font rz-color fw-500"><?php echo $get_activity_data->post_title; ?></span> <span class="created-at"><?php echo human_time_diff( strtotime($get_activity_data->post_date), strtotime($start_date)  ); ?></span></p>
                    </div>
                </a>
            </li>
            <?php
        }else if($activity['type'] == 'answer'){
            $answer_activity = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}rz_answers INNER JOIN {$wpdb->prefix}posts ON {$wpdb->prefix}posts.ID = {$wpdb->prefix}rz_answers.post_id WHERE {$wpdb->prefix}rz_answers.id = '$activity_id'");
            $user_info = get_userdata($answer_activity->user_id);
            $post_author = get_userdata($answer_activity->post_author);
            $start_date = date('Y-m-d G:i:s');
            $post_id = $wpdb->get_row("SELECT post_id FROM {$wpdb->prefix}rz_answers WHERE id = '$activity_id'");

            ?>
            <li>
                <a href="<?php echo get_permalink($post_id->post_id); ?>" class="d-flex flex-row justify-content-start align-items-center px-3 py-2">
                    <div class="activity-image">
                        <img src="<?php getProfileImageById( $answer_activity->user_id ); ?>" alt="" style="min-width: 42px;">
                    </div>
                    <div class="activity-description ms-2">
                        <p class="mb-0 rz-secondary-color imit-font fw-400 fz-16"><?php echo ucfirst($user_info->display_name); ?> answered <?php echo ucfirst($post_author->display_name); ?>'s post  <span class="imit-font rz-color fw-500"><?php echo $answer_activity->post_title; ?></span> <span class="created-at"><?php echo human_time_diff( strtotime($answer_activity->created_at), strtotime($start_date)  ); ?></span></p>
                    </div>
                </a>
            </li>
            <?php
        }else if($activity['type'] == 'vote'){
            $vote_activity = $wpdb->get_row("SELECT {$wpdb->prefix}rz_vote.user_id AS vote_user_id, {$wpdb->prefix}rz_vote.vote_type, {$wpdb->prefix}rz_answers.user_id AS answer_user_id, {$wpdb->prefix}rz_vote.created_at as time, answer_text FROM {$wpdb->prefix}rz_vote INNER JOIN {$wpdb->prefix}rz_answers ON {$wpdb->prefix}rz_vote.answer_id = {$wpdb->prefix}rz_answers.id WHERE {$wpdb->prefix}rz_vote.id = '$activity_id'");
            $vote_user = get_userdata($vote_activity->vote_user_id);
            $answer_user = get_userdata($vote_activity->answer_user_id);
            $start_date = date('Y-m-d G:i:s');
            $post_id = $wpdb->get_row("SELECT {$wpdb->prefix}rz_answers.post_id FROM {$wpdb->prefix}rz_answers INNER JOIN {$wpdb->prefix}rz_vote ON {$wpdb->prefix}rz_answers.id = {$wpdb->prefix}rz_vote.answer_id WHERE {$wpdb->prefix}rz_vote.id = '{$activity_id}'");
            ?>
            <li>
                <a href="<?php echo get_permalink($post_id->post_id); ?>" class="d-flex flex-row justify-content-start align-items-center px-3 py-2">
                    <div class="activity-image">
                        <img src="<?php getProfileImageById( $vote_activity->vote_user_id ); ?>" alt="" style="min-width: 42px;">
                    </div>
                    <div class="activity-description ms-2">
                        <p class="mb-0 rz-secondary-color imit-font fw-400 fz-16"><?php echo ucfirst($vote_user->display_name); if($vote_activity->vote_type == 'up-vote'){echo ' upvoted ';}else{echo ' downvoted ';}  echo ucfirst($answer_user->display_name); ?>'s answer  <span class="imit-font rz-color fw-500"><?php echo wp_trim_words($vote_activity->answer_text, 10, false); ?></span> <span class="created-at"><?php echo human_time_diff( strtotime($vote_activity->time), strtotime($start_date)  ); ?></span></p>
                    </div>
                </a>
            </li>
            <?php
        }
        
        ?>
        <!-- <li>
            <a href="#" class="d-flex flex-row justify-content-start align-items-center px-3 py-2">
                <div class="activity-image">
                    <img src="<?php echo plugins_url('images/avatar.png', __FILE__); ?>" alt="">
                </div>
                <div class="activity-description ms-2">
                    <p class="mb-0 rz-secondary-color imit-font fw-400 fz-16">Shruti answered <span class="imit-font rz-color fw-500">Why are vitamins good for health</span> <span class="created-at">Just Now</span></p>
                </div>
            </a>
        </li> -->
        <?php
    }

    die();
            
}