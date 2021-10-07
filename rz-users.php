<?php


/**
 * direct access not allowed
 */
if(!defined('ABSPATH')){
    die(__('Direct access not allowed.', 'imit-recozilla'));
}

/**
 * add users
 */
add_shortcode('imit-rz-users', function(){
    ob_start();
    global $wpdb;
    ?>
    <section class="users">
        <div class="rz-mid">
            <div class="row mx-lg-0 mx-1">
                <?php 
                $user_id = get_current_user_id(  );
                $is_user_already_a_partner = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_user_programs WHERE user_id = '$user_id' AND (status = '1' OR status = '0')", ARRAY_A);
                ?>
                <div class="<?php echo (is_user_logged_in(  ) == false || count($is_user_already_a_partner) < 1) ? 'col-lg-9' : 'col-12'; ?>">

                    <div class="user-header d-flex flex-sm-row flex-column justify-content-between align-items-center">
                        <div class="user-header-left">
                            <ul class="ps-0 mb-0 bread-crumb d-flex flex-row justify-content-between align-items-center">
                                <li class="list-unstyled">
                                    <a href="#" class="imit-font fz-14 rz-secondary-color text-decoration-none">Home<span class="mx-1">/</span></a>
                                </li>
                                <li class="list-unstyled">
                                    <a href="#" class="imit-font fz-14 rz-secondary-color text-decoration-none active">Users<span class="mx-1">/</span></a>
                                </li>
                            </ul>
                        </div>
                        <div class="user-header-right mt-sm-0 mt-2">
                            <form id="user-live-search">
                                <input name="search-user" type="text" class="form-control imit-font fz-14 w-100 w-sm-auto" placeholder="Search Users">
                                <button type="submit" class="text-dark fz-14 border-0 bg-transparent"><i class="fas fa-search"></i></button>
                            </form>
                        </div>
                    </div>
                    <ul class="row user-list ps-0 mb-0" id="fetch-search-user">
                        
                    </ul>
                    <div id="tab-content-loader" style="display: none;">
                       <div class="d-flex justify-content-center mt-2">
                           <div class="spinner-border" role="status">
                               <span class="visually-hidden">Loading...</span>
                           </div>
                       </div>
                   </div>
                </div>
                <div class="col-lg-3 mt-3 mt-lg-0">
                    <?php echo do_shortcode( '[join-partner-program]' ); ?>
                </div>
            </div>
        </div>
    </section>

    <?php if(is_user_logged_in(  )){
        ?>
        <div class="modal fade" id="send-message-modal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="imit-font fz-14 fw-500 text-dark m-0"></h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form action="" id="send-message-form" data-user_id="">
                            <textarea name="message" id="" cols="30" rows="10" class="form-control fw-400 fz-16 imit-font" placeholder="Send messgae to"></textarea>
                            <button type="submit" class="btn rz-bg-color text-white imit-font fz-14 fw-500 mt-2 d-table ms-auto">Send</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    return ob_get_clean();
});


/**
 * search user
 */
add_action('wp_ajax_rz_search_user', 'rzSearchUser');
add_action('wp_ajax_nopriv_rz_search_user', 'rzSearchUser');

function rzSearchUser(){
    global $wpdb;
    $nonce = $_POST['nonce'];
    if(wp_verify_nonce($nonce, 'rz-search-user-nonce')){
        $search_key = sanitize_text_field(strtolower($_POST['search_key']));
        $current_user = get_current_user_id();

        /**
         * get search user data
         */
        $get_all_users = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}users WHERE (LOWER(user_login) LIKE '%{$search_key}%' OR LOWER(user_nicename) = '%{$search_key}%' OR LOWER(user_email) LIKE '%{$search_key}%' OR LOWER(display_name) LIKE '%{$search_key}%') AND ID NOT IN (SELECT ID FROM {$wpdb->prefix}users WHERE ID = '{$current_user}') UNION SELECT * FROM {$wpdb->prefix}users WHERE ID IN(SELECT user_id FROM {$wpdb->prefix}usermeta WHERE (meta_key = 'first_name' AND LOWER(meta_value) LIKE '%{$search_key}%') OR (meta_key = 'last_name' AND LOWER(meta_value) LIKE '%{$search_key}%')) AND ID NOT IN (SELECT ID FROM wp_users WHERE ID = '{$current_user}') ORDER BY RAND()", ARRAY_A);

        /**
         * check current is partner or not
         */
        $is_user_already_a_partner = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_user_programs WHERE user_id = '$user_id' AND (status = '1' OR status = '0')", ARRAY_A);
        foreach($get_all_users as $user){
            $user_id = $user['ID'];
            $get_user_data = get_userdata($user['ID']);
            $get_profile_data = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}rz_user_profile_data WHERE user_id = '$user_id'");
            ?>
            <li class="<?php echo (is_user_logged_in(  ) == false || count($is_user_already_a_partner) < 1) ? 'col-sm-6' : 'col-sm-4'; ?> list-unstyled mt-3">
                <div class="card rz-br rz-border">
                    <div class="card-body rz-br rz-border p-0">
                        <div class="d-flex flex-row justify-content-start align-items-center py-3 px-4 pb-0">
                            <a href="<?php echo site_url().'/user/'.$get_user_data->user_login; ?>" class="user-avatar d-block">
                                <img src="<?php echo getProfileImageById($user['ID']); ?>" alt="">
                            </a>
                            <div class="ms-2">
                                <a href="<?php echo site_url().'/user/'.$get_user_data->user_login; ?>" class="username fz-16 text-dark fw-500 imit-font text-decoration-none" id="name<?php echo $user_id; ?>"><?php echo getUserNameById($user_id); ?></a>
                                <?php
                                if(!empty($get_profile_data->occupation)){
                                    echo '<p class="mb-0 designation rz-secondary-color fz-12 imit-font">'.$get_profile_data->occupation.'</p>';
                                }
                                ?>
                            </div>
                        </div>
                        <ul class="user-profile-info mb-0 py-3 px-4 pb-0">
                            <?php $get_all_following = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_followers WHERE sender_id = '$user_id'", ARRAY_A);
                            $get_all_followers = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_followers WHERE receiver_id = '$user_id'", ARRAY_A);
                            $question_count = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}posts WHERE post_author = '$user_id' AND post_type='rz_post_question'", ARRAY_A); ?>
                            <li class="list-unstyled d-flex flex-row my-2 justify-content-between align-items-center">
                                <p class="mb-0 imit-font rz-secondary-color fz-14">Following</p>
                                <p class="mb-0 counter imit-font rz-secondary-color fz-14"><?php echo count($get_all_following); ?></p>
                            </li>
                            <li class="list-unstyled d-flex flex-row my-2 justify-content-between align-items-center">
                                <p class="mb-0 imit-font rz-secondary-color fz-14">Followers</p>
                                <p class="mb-0 counter imit-font rz-secondary-color fz-14"><?php echo count($get_all_followers); ?></p>
                            </li>
                            <li class="list-unstyled d-flex flex-row my-2 justify-content-between align-items-center">
                                <p class="mb-0 imit-font rz-secondary-color fz-14">Questions</p>
                                <p class="mb-0 counter imit-font rz-secondary-color fz-14"><?php echo count($question_count); ?></p>
                            </li>
                        </ul>

                        <div class="profile-action d-flex flex-sm-row flex-column justify-content-between align-items-center py-3 px-4">
                            <?php 
                                if(get_current_user_id() !== $user_id){
                                    $sender_id = get_current_user_id();
                                    $receiver_id = $user_id;
                                    $get_all_followers = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_followers WHERE (sender_id = '$sender_id' AND receiver_id = '$receiver_id') OR (sender_id = '$receiver_id' AND receiver_id = '$sender_id')", ARRAY_A);
                                    ?>
                                    <a href="#" class="btn rz-bg-color d-block text-center imit-font fz-14 text-white w-sm-50 w-100 me-sm-1 me-0 <?php echo ( (is_user_logged_in() && count($get_all_followers) > 0) ? 'following' : '' ); ?>" <?php echo ( (is_user_logged_in()) ? 'id="rz-follow" data-receiver_id="'.$receiver_id.'"' : 'data-bs-toggle="modal" data-bs-target="#login-modal"' ); ?>><?php echo ( (is_user_logged_in() && count($get_all_followers) > 0) ? '<i class="fas fa-check-square me-2"></i>Following' : '<i class="fas fa-plus-circle me-2"></i>Follow' ); ?></a>
                                    <a href="#" class="btn message d-block text-center imit-font fz-14 w-sm-50 w-100 ms-sm-1 ms-0 mt-sm-0 mt-2" <?php echo ( (is_user_logged_in()) ? 'data-user_id="'.$receiver_id.'" id="send-message-button"' : 'data-bs-toggle="modal" data-bs-target="#login-modal"' ); ?>><i class="fas fa-comments me-2"></i>Message</a>
                                    <?php
                                }else{
                                    if(is_user_logged_in()){
                                    ?>
                                        <a href="#" class="btn follow d-block text-center imit-font fz-14 text-whit w-sm-50 w-100 me-sm-1 me-0" data-bs-toggle="modal" data-bs-target="#rz-profile-edit-modal"><i class="fas fa-edit me-2"></i>Edit Profile</a>
                                        <a href="#" class="btn message d-block text-center imit-font fz-14 w-sm-50 w-100 ms-sm-1 ms-0 mt-sm-0 mt-2">Search Pad</a>
                                    <?php
                                    }
                                }
                            ?>
                        </div>
                            <ul class="about mb-0 py-3 px-4">
                                <?php
                                $all_workplaces_data = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_user_work WHERE user_id = '$user_id'", ARRAY_A);
                                foreach($all_workplaces_data as $workplace){
                                    ?>
                                    <li class="about-list rz-secondary-color list-unstyled my-2">
                                        <i class="fas fa-briefcase mr-1"></i>
                                        <span class="imit-font fz-14"><?php echo $workplace['position']; ?> at <strong class="rz-color"><?php echo $workplace['company']; ?></strong> <?php echo $workplace['start_year']; ?> - <?php echo $workplace['end_year']; ?></span>
                                    </li>
                                    <?php
                                }

                                $all_education_data = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_user_education WHERE user_id = '$user_id'", ARRAY_A);
                                foreach($all_education_data as $education){
                                    ?>
                                    <li class="about-list rz-secondary-color list-unstyled my-2">
                                        <i class="fas fa-graduation-cap mr-1"></i>
                                        <span class="imit-font fz-14"><?php echo $education['concentrations']; ?> at <strong class="rz-color"><?php echo $education['college']; ?></strong> <?php echo $education['start_year']; ?> - <?php echo $education['end_year']; ?></span>
                                    </li>
                                    <?php
                                }
                                if(!empty($get_profile_data->city) && !empty($get_profile_data->country)){
                                    ?>
                                    <li class="about-list rz-secondary-color list-unstyled my-2">
                                        <i class="fas fa-map-marker-alt mr-1"></i>
                                        <span class="imit-font fz-14">Lives in <strong class="rz-color"><?php echo $get_profile_data->city.', '.$get_profile_data->country; ?></strong></span>
                                    </li>
                                    <?php
                                }
                                if(!empty($get_profile_data->languages)){
                                    ?>
                                    <li class="about-list rz-secondary-color list-unstyled my-2">
                                        <i class="fas fa-globe mr-1"></i>
                                        <span class="imit-font fz-14">Knows <strong class="rz-color">
                                <?php
                                $lan_exp = explode(',', $get_profile_data->languages);

                                echo implode(' - ', $lan_exp);
                                ?>
                            </strong></span>
                                    </li>
                                    <?php
                                }
                                ?>

                                <li class="about-list rz-secondary-color list-unstyled my-2">
                                    <i class="fas fa-clock mr-1"></i>
                                    <span class="imit-font fz-14">Joined <strong class="rz-color"><?php echo date('F, Y', strtotime($get_user_data->user_registered)); ?></strong></span>
                                </li>
                            </ul>
                    </div>
                </div>
            </li>
            <?php
        }
    }
    die();
}


/**
 * get all suggested users
 */
add_action('wp_ajax_nopriv_rz_suggested_user_find', 'get_all_suggested_users');
add_action('wp_ajax_rz_suggested_user_find', 'get_all_suggested_users');

function get_all_suggested_users(){
    global $wpdb;
    $nonce = $_POST['nonce'];
    if(wp_verify_nonce( $nonce, 'rz-suggested-users-nonce' )){
        $start = sanitize_key( $_POST['start'] );
        $limit = sanitize_key( $_POST['limit'] );
        $current_user = get_current_user_id();

        /**
         * get all user data
         */
        $get_all_users = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}users WHERE ID NOT IN (SELECT sender_id FROM {$wpdb->prefix}rz_followers WHERE receiver_id = '$current_user' UNION SELECT receiver_id FROM {$wpdb->prefix}rz_followers WHERE sender_id = '$current_user') ORDER BY RAND() LIMIT $start, $limit", ARRAY_A);

         /**
         * check current is partner or not
         */
        $is_user_already_a_partner = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_user_programs WHERE user_id = '$current_user' AND (status = '1' OR status = '0')", ARRAY_A);

        if(count($get_all_users) > 0){
            foreach($get_all_users as $user){
                $user_id = $user['ID'];
                $get_user_data = get_userdata($user['ID']);
                $get_profile_data = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}rz_user_profile_data WHERE user_id = '$user_id'");
                ?>
                <li class="<?php echo ((is_user_logged_in(  ) == false || count($is_user_already_a_partner) < 1) ? 'col-md-6' : 'col-lg-4 col-md-6'); ?> list-unstyled mt-3">
                    <div class="card rz-br rz-border">
                        <div class="card-body rz-br rz-border p-0">
                            <div class="d-flex flex-row justify-content-start align-items-center py-3 px-4 pb-0">
                                <a href="<?php echo site_url().'/user/'.$get_user_data->user_login; ?>" class="user-avatar d-block">
                                    <img src="<?php echo getProfileImageById($user['ID']); ?>" alt="">
                                </a>
                                <div class="ms-2">
                                    <a href="<?php echo site_url().'/user/'.$get_user_data->user_login; ?>" class="username fz-16 text-dark fw-500 imit-font text-decoration-none" id="name<?php echo $user_id; ?>"><?php echo getUserNameById($user_id); ?></a>
                                    <?php    
                                    if(!empty($get_profile_data->occupation)){
                                        echo '<p class="mb-0 designation rz-secondary-color fz-12 imit-font">'.$get_profile_data->occupation.'</p>';
                                    }
                                    ?>
                                </div>
                            </div>
                            <ul class="user-profile-info mb-0 py-3 px-4 pb-0">
                                <?php $get_all_following = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_followers WHERE sender_id = '$user_id'", ARRAY_A);
                                $get_all_followers = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_followers WHERE receiver_id = '$user_id'", ARRAY_A);
                                $question_count = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}posts WHERE post_author = '$user_id' AND post_type='rz_post_question'", ARRAY_A); ?>
                                <li class="list-unstyled d-flex flex-row my-2 justify-content-between align-items-center">
                                    <p class="mb-0 imit-font rz-secondary-color fz-14">Following</p>
                                    <p class="mb-0 counter imit-font rz-secondary-color fz-14"><?php echo count($get_all_following); ?></p>
                                </li>
                                <li class="list-unstyled d-flex flex-row my-2 justify-content-between align-items-center">
                                    <p class="mb-0 imit-font rz-secondary-color fz-14">Followers</p>
                                    <p class="mb-0 counter imit-font rz-secondary-color fz-14"><?php echo count($get_all_followers); ?></p>
                                </li>
                                <li class="list-unstyled d-flex flex-row my-2 justify-content-between align-items-center">
                                    <p class="mb-0 imit-font rz-secondary-color fz-14">Questions</p>
                                    <p class="mb-0 counter imit-font rz-secondary-color fz-14"><?php echo count($question_count); ?></p>
                                </li>
                            </ul>
    
                            <div class="profile-action d-flex flex-sm-row flex-column justify-content-between align-items-center py-3 px-4">
                                <?php
                                if(get_current_user_id() !== $user_id){
                                    $sender_id = get_current_user_id();
                                    $receiver_id = $user_id;
                                    $get_all_followers = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_followers WHERE (sender_id = '$sender_id' AND receiver_id = '$receiver_id') OR (sender_id = '$receiver_id' AND receiver_id = '$sender_id')", ARRAY_A);
                                    ?>
                                        <a href="#" class="btn rz-bg-color d-block text-center imit-font fz-14 text-white w-sm-50 w-100 me-sm-1 me-0 <?php echo ( (is_user_logged_in() && count($get_all_followers) > 0) ? 'following' : '' ); ?>" <?php echo ( (is_user_logged_in()) ? 'id="rz-follow" data-receiver_id="'.$receiver_id.'"' : 'data-bs-toggle="modal" data-bs-target="#login-modal"' ); ?>><?php echo ( (is_user_logged_in() && count($get_all_followers) > 0) ? '<i class="fas fa-check-square me-2"></i>Following' : '<i class="fas fa-plus-circle me-2"></i>Follow' ); ?></a>
                                        <a href="#" class="btn message d-block text-center imit-font fz-14 w-sm-50 w-100 ms-0 ms-sm-1 mt-sm-0 mt-2" <?php echo ( (is_user_logged_in()) ? 'data-user_id="'.$receiver_id.'" id="send-message-button"' : 'data-bs-toggle="modal" data-bs-target="#login-modal"' ) ?>><i class="fas fa-comments me-2"></i>Message</a>
                                    <?php
                                }else{
                                    if(is_user_logged_in(  )){
                                        ?>
                                            <a href="#" class="btn follow d-block text-center imit-font fz-14 text-whit w-sm-50 w-100 me-sm-1 me-0" data-bs-toggle="modal" data-bs-target="#rz-profile-edit-modal"><i class="fas fa-edit me-2"></i>Edit Profile</a>
                                            <a href="#" class="btn message d-block text-center imit-font fz-14 w-sm-50 w-100 ms-sm-1 ms-0 mt-sm-0 mt-2">Search Pad</a>
                                        <?php
                                    }
                                }
                                ?>
                            </div>
                                <ul class="about mb-0 py-3 px-4">
                                    <?php
                                    $all_workplaces_data = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_user_work WHERE user_id = '$user_id'", ARRAY_A);
                                    foreach($all_workplaces_data as $workplace){
                                        ?>
                                        <li class="about-list rz-secondary-color list-unstyled my-2">
                                            <i class="fas fa-briefcase mr-1"></i>
                                            <span class="imit-font fz-14"><?php echo $workplace['position']; ?> at <strong class="rz-color"><?php echo $workplace['company']; ?></strong> <?php echo $workplace['start_year']; ?> - <?php echo $workplace['end_year']; ?></span>
                                        </li>
                                        <?php
                                    }
        
                                    $all_education_data = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_user_education WHERE user_id = '$user_id'", ARRAY_A);
                                    foreach($all_education_data as $education){
                                        ?>
                                        <li class="about-list rz-secondary-color list-unstyled my-2">
                                            <i class="fas fa-graduation-cap mr-1"></i>
                                            <span class="imit-font fz-14"><?php echo $education['concentrations']; ?> at <strong class="rz-color"><?php echo $education['college']; ?></strong> <?php echo $education['start_year']; ?> - <?php echo $education['end_year']; ?></span>
                                        </li>
                                        <?php
                                    }
                                    if(!empty($get_profile_data->city) && !empty($get_profile_data->country)){
                                        ?>
                                        <li class="about-list rz-secondary-color list-unstyled my-2">
                                            <i class="fas fa-map-marker-alt mr-1"></i>
                                            <span class="imit-font fz-14">Lives in <strong class="rz-color"><?php echo $get_profile_data->city.', '.$get_profile_data->country; ?></strong></span>
                                        </li>
                                        <?php
                                    }
                                    if(!empty($get_profile_data->languages)){
                                        ?>
                                        <li class="about-list rz-secondary-color list-unstyled my-2">
                                            <i class="fas fa-globe mr-1"></i>
                                            <span class="imit-font fz-14">Knows <strong class="rz-color">
                                    <?php
                                    $lan_exp = explode(',', $get_profile_data->languages);
        
                                    echo implode(' - ', $lan_exp);
                                    ?>
                                </strong></span>
                                        </li>
                                        <?php
                                    }
                                    ?>
                                    <li class="about-list rz-secondary-color list-unstyled my-2">
                                        <i class="fas fa-clock mr-1"></i>
                                        <span class="imit-font fz-14">Joined <strong class="rz-color"><?php echo date('F, Y', strtotime($get_user_data->user_registered)); ?></strong></span>
                                    </li>
                                </ul>
                        </div>
                    </div>
                </li>
                <?php
            }
        }else{
            exit('userReachMax');
        }
    }
    die();
}