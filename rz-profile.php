<?php
/**
 * user profile
 */
add_shortcode('imit-user-profile', function(){
    global $wpdb;
    $user_id = um_profile_id();
    $user_data = get_userdata($user_id);
    $all_profile_data = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}rz_user_profile_data WHERE user_id = '$user_id'");
    $rz_user_table = $wpdb->prefix.'rz_user_programs';
    $is_user_requested = $wpdb->get_results("SELECT * FROM {$rz_user_table} WHERE user_id = '$user_id'", ARRAY_A);
    ob_start();
    ?>
    <section class="profile-info">
        <div class="rz-mid">
            <div class="row">
                <div class="col-md-3">
                    <div class="card rz-border rz-br mt-3">
                        <div class="card-body rz-border rz-br">
                            <div class="profile-data">
                                <div class="profile-image mx-auto">
                                    <img id="profile_image_show" src="<?php echo getProfileImageById($user_id); ?>" alt="">
                                </div>
                                <?php
                                if(!empty($user_data->user_firstname) && !empty($user_data->user_lastname)){
                                    ?>
                                    <h3 class="name m-0 mt-3 text-center imit-font mb-1" id="name<?php echo $user_id; ?>"><?php echo ucfirst($user_data->user_firstname).' '.ucfirst($user_data->user_lastname); ?></h3>
                                        <?php
                                }else{
                                  ?>
                                    <h3 class="name m-0 mt-3 text-center imit-font mb-1" id="name<?php echo $user_id; ?>"><?php echo ucfirst($user_data->display_name); ?></h3>
                                        <?php

                                }
                                if(!empty($all_profile_data->occupation)){
                                  ?>
                                    <p class="designation mb-0 text-center imit-font fz-14 rz-secondary-color"><?php echo $all_profile_data->occupation; ?></p>
                                        <?php
                                }
                                ?>
                                <?php if(is_user_logged_in()){
                                    if(get_current_user_id() !== $user_id){
                                        $sender_id = get_current_user_id();
                                        $receiver_id = um_profile_id();
                                        $get_all_followers = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_followers WHERE (sender_id = '$sender_id' AND receiver_id = '$receiver_id') OR (sender_id = '$receiver_id' AND receiver_id = '$sender_id')", ARRAY_A);
                                        if(count($get_all_followers) > 0){
                                            ?>
                                            <a href="#" class="btn btn-secondary d-block text-center mt-2 imit-font fz-14 text-white" id="rz-follow" data-receiver_id="<?php echo um_profile_id(); ?>"><i class="fas fa-minus-circle me-2"></i>Unfollow</a>
                                            <?php
                                        }else{
                                            ?>
                                            <a href="#" class="btn follow d-block text-center mt-3 imit-font fz-14" id="rz-follow" data-receiver_id="<?php echo um_profile_id(); ?>"><i class="fas fa-plus-circle me-2"></i>Follow</a>
                                            <?php
                                        }
                                        ?>
                                        <a href="#" class="btn message d-block text-center mt-2 imit-font fz-14" data-user_id="<?php echo $receiver_id; ?>" id="send-message-button"><i class="fas fa-comments me-2"></i>Message</a>
                                        <?php
                                    }else{
                                        ?>
                                        <a href="#" class="btn follow d-block text-center mt-2 imit-font fz-14 text-white" data-bs-toggle="modal" data-bs-target="#rz-profile-edit-modal"><i class="fas fa-edit me-2"></i>Edit Profile</a>
                                        <a href="#" class="btn message d-block text-center mt-2 imit-font fz-14 tab-link" data-target="user-dairy">Scratch Pad</a>
                                            <?php
                                    }
                                }?>
                            </div>

                            <ul class="user-profile-info mb-0 ps-0 mt-3">
                                <?php
                                $user_id = um_profile_id();
                                $get_all_following = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_followers WHERE sender_id = '$user_id'", ARRAY_A);
                                $get_all_followers = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_followers WHERE receiver_id = '$user_id'", ARRAY_A);
                                $question_count = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}posts WHERE post_author = '$user_id' AND post_type='rz_post_question'", ARRAY_A);
                                ?>
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
                            <ul class="about mb-0 ps-0 mt-3 pt-3">
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
                                if(!empty($all_profile_data->city) && !empty($all_profile_data->country)){
                                    ?>
                                    <li class="about-list rz-secondary-color list-unstyled my-2">
                                        <i class="fas fa-map-marker-alt mr-1"></i>
                                        <span class="imit-font fz-14">Lives in <strong class="rz-color"><?php echo $all_profile_data->city.', '.$all_profile_data->country; ?></strong></span>
                                    </li>
                                        <?php
                                }
                                ?>
<!--                                <li class="about-list rz-secondary-color list-unstyled my-2">-->
<!--                                    <i class="fas fa-eye mr-1"></i>-->
<!--                                    <span class="imit-font fz-14">85.7k content views 2.5k this month</span>-->
<!--                                </li>-->
                                <?php
                                if(!empty($all_profile_data->languages)){
                                    ?>
                                    <li class="about-list rz-secondary-color list-unstyled my-2">
                                        <i class="fas fa-globe mr-1"></i>
                                        <span class="imit-font fz-14">Knows <strong class="rz-color">
                                            <?php
                                            $lan_exp = explode(',', $all_profile_data->languages);

                                            echo implode(' - ', $lan_exp);
                                            ?>
                                        </strong></span>
                                    </li>
                                        <?php
                                }
                                ?>
                            </ul>
                        </div>
                    </div>

                    <div class="join rz-br rz-bg-color rounded-2 p-3 mt-3" style="background-image: url('<?php echo plugins_url('images/Group 237.png', __FILE__); ?>');">
                        <h3 class="title m-0 text-white imit-font fz-20 fw-500">Join our Partner Program and earn money on Recozilla</h3>
                        <a href="<?php echo site_url(); ?>/join-partner-program/" class="btn bg-white fz-12 rz-color imit-font fw-500 mt-3">Join Now</a>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="imit-tabs rz-bg-color px-3 py-2 rounded mt-3">
                        <div class="d-flex flex-row justify-content-between align-items-center">
                            <ul class="tab-menu ps-0 mb-0 d-flex flex-row justify-content-start align-items-center">
                                <li class="tab-list list-unstyled">
                                    <a href="#" class="tab-link text-decoration-none me-3 pt-3 pb-2 d-block imit-font text-white fz-14 active" data-target="rz-profile-user-answers">Answers</a>
                                </li>
                                <li class="tab-list list-unstyled">
                                    <a href="#" class="tab-link text-decoration-none me-3 pt-3 pb-2 d-block imit-font text-white fz-14" data-target="profile-feed">Question asked</a>
                                </li>
                                <li class="tab-list list-unstyled">
                                    <a href="#" class="tab-link text-decoration-none me-3 pt-3 pb-2 d-block imit-font text-white fz-14" data-target="rz-profile-user-vote">Question Voted</a>
                                </li>
                                <li class="tab-list list-unstyled">
                                    <a href="#" class="tab-link text-decoration-none me-3 pt-3 pb-2 d-block imit-font text-white fz-14" data-target="rz-profile-user-comment">Question commented</a>
                                </li>
                                
                            </ul>

                            <div class="dropdown custom-dropdown">
                                <a class="see-more text-white fz-16" href="#" role="button" id="feed-more-tabs" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-ellipsis-h"></i>
                                </a>

                                <ul class="dropdown-menu" aria-labelledby="feed-more-tabs">
                                    <?php 
                                    if(is_user_logged_in() && get_current_user_id() == um_profile_id()){
                                        if(!count($is_user_requested) > 0){
                                            ?>
                                            <li><a class="dropdown-item imit-font fz-14 tab-link" href="#" data-target="partner-program">Join partner program</a></li>
                                            <?php
                                        }
                                    }

                                    if(is_user_logged_in() && get_current_user_id() == um_profile_id()){
                                        ?>
                                        <li><a class="tab-link dropdown-item imit-font fz-14" href="#" data-target="user-points">Points earned</a></li>
                                        <?php
                                    }


                                    if(is_user_logged_in() && get_current_user_id() == um_profile_id()){
                                        ?>
                                        <li><a class="dropdown-item imit-font fz-14 tab-link" href="#" data-target="redeem-points">Redeem Points</a></li>
                                        <?php
                                    }
                                    ?>
                                    <!-- <li class="tab-list list-unstyled">
                                    <a href="#" class="tab-link text-decoration-none me-3 pt-3 pb-2 d-block imit-font text-white fz-14" data-target="following-user" data-user_id="<?php echo $user_id; ?>">Following</a>
                                </li> -->
                                    
                                    <li><a class="tab-link dropdown-item imit-font fz-14" href="#" data-target="following-question">Following</a></li>
                                    <?php if(is_user_logged_in(  ) && um_profile_id() == get_current_user_id(  )){
                                        ?>
                                        <li><a class="dropdown-item imit-font fz-14" href="<?php echo site_url(); ?>/message">Message</a></li>
                                        <?php
                                    } ?>
                                </ul>
                            </div>
                        </div>
                    </div>


                    <div class="tab-content" id="rz-profile-user-answers">
                        <ul class="imit-news-feed ps-0 mb-0 runded-3" id="rz-profile-user-answers-ul">

                        </ul>
                    </div>  

                    <div class="tab-content" id="profile-feed" style="display: none;">
                        <ul class="imit-news-feed ps-0 mb-0 runded-3" id="profile-feed-ul">

                        </ul>
                    </div>           


                    <div class="tab-content" id="rz-profile-user-vote" style="display: none;">
                        <ul class="imit-news-feed ps-0 mb-0 runded-3" id="rz-profile-user-vote-ul">
                            
                        </ul>
                    </div>

                    <div class="tab-content" id="rz-profile-user-comment" style="display: none;">
                        <ul class="imit-news-feed ps-0 mb-0 runded-3" id="rz-profile-user-comment-ul">

                        </ul>
                    </div>



                    <div class="tab-content" id="following" style="display: none;">
                        <ul class="following-tab p-3 rz-light-bg rounded d-flex flex-row justify-content-start align-items-center mt-3">
                            <li class="tab-list list-unstyled">
                                <a href="#" class="following-link me-3 imit-font rz-color fw-500 fz-14 pb-2 active" data-target="following-question">Questions</a>
                            </li>
                            <li class="tab-list list-unstyled">
                                <a href="#" class="following-link me-3 imit-font rz-color fw-500 fz-14 pb-2" data-target="following-tags">Topics</a>
                            </li>
                            <li class="tab-list list-unstyled">
                                <a href="#" class="following-link me-3 imit-font rz-color fw-500 fz-14 pb-2" data-target="following-users">Users</a>
                            </li>
                        </ul>

                        <!-- following questions start  -->
                        <div id="following-question" class="following-content">
                            <ul class="ps-0 mb-0" id="following-question-ul">
                                
                            </ul>
                        </div>
                        <!-- follwoing question end -->

                        <!-- follwoing tags start -->
                        <div class="following-content" id="following-tags" style="display: none;">
                            <ul class="hash-tags ps-0 mb-0 row mt-3" id="following-tags-ul">
                                                      
                            </ul>
                        </div>
                        <!-- following tags end -->


                        <!-- following users start -->
                        <div id="following-users" class="following-content" style="display: none;">
                            <ul class="ps-0 mb-0" id="following-users-ul">
                                    
                            </ul>
                        </div>
                        <!-- following end start -->
                    </div>
                    <div class="tab-content" id="user-dairy" style="display: none;">
                        <?php if(is_user_logged_in() && get_current_user_id() == um_profile_id()){
                            ?>
                            <p class="imit-font fz-16 text-dark dairy-title mt-3"><span class="rz-color fw-500">Scratch Pad:</span> Your daily diary . Write here and save it for yourself. All posts are private by default and visible to you only. If you wish, you can make a post publicly visible.</p>
                            <div class="card rz-border dairy-add-card">
                                <div class="card-header border-0">
                                    <h2 class="dairy-add-title m-0 imif-font fz-16 py-2 fw-500">Your daily diary</h2>
                                </div>
                                <div class="card-body bg-white">
                                    <form id="add-dairy">
                                        <div id="dairy-message"></div>
                                        <?php //wp_editor( '', 'dairy-text', [
                                            //'media_buttons' => false
                                        //] ); ?>
                                        <textarea name="dairy-text" id="" cols="30" rows="10" class="form-control fz-14 imit-font" placeholder="Write Something..."></textarea>
                                        <div class="d-flex flex-row justify-content-between align-items-center mt-3">
                                            <div class="form-check">
                                                <input name="dairy-visiblity" class="form-check-input" type="checkbox" value="yes" id="dairy-visiblity" checked>
                                                <label class="form-check-label rz-secondary-color imit-font fz-14" for="dairy-visiblity">
                                                    Make it Public
                                                </label>
                                            </div>
                                            <button type="submit" class="btn rz-bg-color text-light fz-16 imit-font">Save</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <?php
                        }?>

                        <ul class="all-dairy ps-0 mb-0" id="user-dairy-ul">
                            
                        </ul>
                    </div>

                    <?php if(is_user_logged_in() && get_current_user_id() == um_profile_id()){
                        if(!count($is_user_requested) > 0){
                            ?>
                            <div class="tab-content" id="partner-program" style="display: none;">
                                <div class="card rz-border rz-br mt-3 programme">
                                    <div class="card-header rz-br py-3 rz-light-bg p-3">
                                        <h3 class="m-0 title rz-color fw-500">Join Partner Program</h3>
                                    </div>
                                    <div class="card-body">
                                        <form id="join-partner-program">
                                            <div id="partner-message"></div>
                                            <div class="d-flex flex-row justify-content-start align-items-center">
                                                <label class="imit-font fz-14 text-dark mb-0 fw-500 me-2" for="join">Join Partner Program?</label>
                                                <input type="checkbox" class="form-check-input" name="join" value="yes" id="join">
                                            </div>
                                            <p class="imit-font rz-secondary-color fz-14 mt-2">Read our <strong class="rz-color">Guidlines</strong> for Partner Program</p>

                                            <p class="imit-font fz-14 rz-secondary-color description">Give us some information about yourself-links to blogs you have writter in past ( on any website), links of answers you wrote on other Q&A websites or any other example depicting your writing skills. Our team will review these to approve your request.</p>

                                            <textarea name="partner_message" cols="30" rows="10" placeholder="Write here..." class="form-control fz-14 imit-font"></textarea>
                                            <button type="submit" class="btn rz-bg-color text-white imit-font fz-14 mt-2 d-table ms-auto">Submit</button>
                                        </form>
                                    </div>
                                </div>
                                <div class="card rz-border rz-br mt-3 programme">
                                    <div class="card-header rz-br py-3 rz-light-bg">
                                        <h3 class="m-0 title rz-color fw-500">Join Partner Program Guidlines</h3>
                                    </div>
                                    <div class="card-body">
                                        <p class="imit-font fz-14 rz-secondary-color description">Volutpat vel vestibulum, urna, facilisi ullamcorper vestibulum, consectetur. Etiam duis amet, ipsum sit interdum. Volutpat egestas elit id porta id nisi sit orci. Facilisi nullam faucibus tellus ac erat. Lacus convallis varius massa id ut massa. Maecenas lorem tellus pellentesque tellus consequat tempor, non enim, nibh. Arcu integer gravida amet at sit sed. Dolor odio in arcu, ut sem risus. In faucibus nulla proin fermentum odio nunc vestibulum. Sagittis pharetra massa congue praesent egestas etiam odio. At pharetra lorem id lobortis elit commodo nunc. Congue risus magna in pulvinar pharetra ac. Etiam viverra at turpis malesuada.
                                            Id id duis faucibus aliquet est nunc. Est urna, lobortis tristique in scelerisque dignissim. Sed nunc at senectus.</p>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    }?>
<!--                    for user points table-->
                    <?php 
                    if(is_user_logged_in() && get_current_user_id() == um_profile_id()){
                        ?>
                        <div class="tab-content" id="user-points" style="display: none;">
                            <div class="card rz-border mt-3 rz-br">
                                <div class="card-header rz-light-bg border-0 p-3" style="border-top-left-radius: inherit !important;border-top-right-radious: inherit !important;">
                                    <h3 class="m-0 rz-color fz-20 fw-500 imit-font">Points Earned</h3>
                                </div>
                                <div class="card-body">
                                    <ul class="point-list d-flex flex-row justify-content-between align-content-center p-0 m-0">

                                        <?php
                                        $current_user = get_current_user_id();
                                        $get_point_list = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_point_table WHERE user_id = '{$current_user}' ORDER BY id DESC", ARRAY_A);

                                        $total_point = 0;

                                        foreach($get_point_list as $point){
                                            $total_point += $point['point_earn'];
                                        }

                                        $user_profile_data = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}rz_user_profile_data where user_id = '{$current_user}'");

                                        ?>
                                        <li class="point-list-item rounded py-2 px-3 bg-light text-center list-unstyled w-100">
                                            <p class="mb-0 rz-color imit-font rz-color fw-500"><?php echo $total_point; ?></p>
                                            <span class="text-secondary imit-font fz-14 d-block">Total Point</span>
                                        </li>
                                        <li class="point-list-item rounded py-2 px-3 bg-light text-center list-unstyled mx-3 w-100">
                                            <p class="mb-0 rz-color imit-font rz-color fw-500"><?php echo ($total_point - $user_profile_data->points); ?></p>
                                            <span class="text-secondary imit-font fz-14 d-block">Pointes Redeemed</span>
                                        </li>
                                        <li class="point-list-item rounded py-2 px-3 bg-light text-center list-unstyled w-100">
                                            <p class="mb-0 rz-color imit-font rz-color fw-500"><?php if(!empty($user_profile_data)){echo $user_profile_data->points;}else{echo 0;}; ?></p>
                                            <span class="text-secondary imit-font fz-14 d-block">Pointes Outstanding</span>
                                        </li>
                                    </ul>

                                    <div class="points-history bg-light p-3 mt-3 rounded">
                                        <h3 class="title rz-color fz-20 m-0 imit-font mb-3">Points History</h3>
                                        <ul class="point-history-list p-0 m-0">
                                            <?php
                                            foreach($get_point_list as $point){
                                                $start_time = date('Y-m-d G:i:s');
                                                if($point['point_type'] == 'answer'){
                                                    $answer_id = $point['content_id'];
                                                    $get_post_by_answer = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}posts WHERE ID IN (SELECT post_id FROM {$wpdb->prefix}rz_answers WHERE id = '{$answer_id}')");
                                                    ?>
                                                    <li class="point-history-items list-unstyled">
                                                        <span class="text-secondary imit-font fz-14"><?php echo human_time_diff(strtotime($point['created_at']), strtotime($start_time)); ?></span>
                                                        <p class="m-0 imit-font fz-14 textdark">Added answer for “<?php echo $get_post_by_answer->post_title; ?>”  <strong class="rz-color">+<?php echo $point['point_earn']; ?> points</strong></p>
                                                    </li>
                                                    <?php
                                                }else if($point['point_type'] == 'post'){
                                                    $post_id = $point['content_id'];
                                                    $posts_data = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}posts WHERE ID = '{$post_id}'");
                                                    ?>
                                                    <li class="point-history-items list-unstyled">
                                                        <span class="text-secondary imit-font fz-14"><?php echo human_time_diff(strtotime($point['created_at']), strtotime($start_time)); ?></span>
                                                        <p class="m-0 imit-font fz-14 textdark">Added post for “<?php echo $posts_data->post_title; ?>”  <strong class="rz-color">+<?php echo $point['point_earn']; ?> points</strong></p>
                                                    </li>
                                                    <?php
                                                }else{
                                                    $vote_id = $point['content_id'];
                                                    $get_post_data = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}posts WHERE ID IN (SELECT post_id FROM {$wpdb->prefix}rz_answers WHERE id IN (SELECT answer_id FROM {$wpdb->prefix}rz_vote WHERE id = '$vote_id'))");
                                                    ?>
                                                    <li class="point-history-items list-unstyled">
                                                        <span class="text-secondary imit-font fz-14"><?php echo human_time_diff(strtotime($point['created_at']), strtotime($start_time)); ?></span>
                                                        <p class="m-0 imit-font fz-14 textdark">Added upvote for “<?php echo $get_post_data->post_title; ?>”  <strong class="rz-color">+<?php echo $point['point_earn']; ?> points</strong></p>
                                                    </li>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }

                    if(is_user_logged_in() && get_current_user_id() == um_profile_id()){
                        ?>
                        <div class="tab-content" id="redeem-points" style="display: none;">
                            <div class="card rz-border mt-3 rz-br">
                                <div class="card-header rz-light-bg border-0 p-3" style="border-top-left-radius: inherit !important;border-top-right-radious: inherit !important;">
                                    <h3 class="m-0 rz-color fz-20 fw-500 imit-font">Redeem Points</h3>
                                </div>
                                <div class="card-body redeem-points-body p-4">
                                    <form id="redeem-point-form">
                                        <div id="redeem-point-message"></div>
                                        <p class="imit-font fz-16 text-dark">Points Outstanding :  <span class="rz-color fw-500"><?php if(!empty($user_profile_data)){echo $user_profile_data->points;}else{echo 0;}; ?></span></p>

                                        <p class="fz-14 imit-font rz-secondary-color">How may points you want to redeem?</p>

                                        <input name="point" type="text" class="form-control imit-font border border-1 fz-14" placeholder="Redeem Points">

                                        <h2 class="imit-font text-dark mt-5" style="font-size: 18px;">Mode of Payment</h2>

                                        <div class="d-flex flex-row justify-content-start align-items-center">
                                            <div class="radio-button">
                                                <input name="payment-method" class="d-none" type="radio" id="paytm" value="Paytm" checked>
                                                <label for="paytm"><img src="<?php echo plugins_url( 'images/paytm.png', __FILE__ ); ?>" alt=""></label>
                                            </div>

                                            <div class="radio-button">
                                                <input name="payment-method" class="d-none" type="radio" id="google-pay" value="Google Pay">
                                                <label for="google-pay"><img src="<?php echo plugins_url( 'images/google-pay.png', __FILE__ ); ?>" alt=""></label>
                                            </div>

                                            <div class="radio-button">
                                                <input name="payment-method" class="d-none" type="radio" id="upi" value="UPI">
                                                <label for="upi"><img src="<?php echo plugins_url( 'images/upi.png', __FILE__ ); ?>" alt=""></label>
                                            </div>

                                            <div class="radio-button">
                                                <input name="payment-method" class="d-none" type="radio" id="bank-transfer" value="Bank Transfer">
                                                <label for="bank-transfer"><img src="<?php echo plugins_url( 'images/bank-transfer.png', __FILE__ ); ?>" alt=""></label>
                                            </div>
                                        </div>

                                        <div class="get-data-for-selected-payment mt-3" id="payment-details">
                                            <div class="row mb-3">
                                                <label for="paytm-name" class="col-sm-3 col-form-label imit-font fz-16">Name</label>
                                                <div class="col-sm-9">
                                                    <input name="name" type="text" class="form-control border fz-14 border-1 imit-font" id="paytm-name">
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <label for="paytm-mobile" class="col-sm-3 col-form-label imit-font fz-16">Mobile number</label>
                                                <div class="col-sm-9">
                                                    <input name="mobile" type="text" class="form-control border fz-14 border-1 imit-font" id="paytm-mobile">
                                                </div>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn rz-bg-color text-white px-5 fz-14 d-table ms-auto imitt-font fw-500" style="border-radius: 6px;">Submit</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                    ?>


                    <div id="tab-content-loader" style="display: none;">
                        <div class="d-flex justify-content-center mt-2">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php if(is_user_logged_in() && um_profile_id() == get_current_user_id()){
       ?>
        <div class="modal fade" id="rz-profile-edit-modal">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-body p-4">
                        <button type="button" class="btn-close d-block ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
                        <div class="profile-image mx-auto mb-4">
                            <?php
                            $user_id = get_current_user_id();
                            $all_profile_data = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_user_profile_data WHERE user_id = '$user_id'", ARRAY_A);
                            if(count($all_profile_data)){
                                foreach($all_profile_data as $profile_data){
                                    if(empty($profile_data['profile_image'])){
                                        ?>
                                        <img id="profile_image_show_modal" src="<?php echo plugins_url('images/avatar.png', __FILE__); ?>" alt="">
                                        <?php
                                    }else{
                                        ?>
                                        <img id="profile_image_show_modal" src="<?php echo $profile_data['profile_image']; ?>" alt="">
                                        <?php
                                    }
                                }
                            }else{
                                ?>
                                <img id="profile_image_show_modal" src="<?php echo plugins_url('images/avatar.png', __FILE__); ?>" alt="">
                                    <?php
                            }
                            ?>
                            <form enctype="multipart/form-data" id="change-rz-avatar">
                                <div class="image-upload">
                                    <label for="upload-profile-image" class="rz-color"><i class="fas fa-camera"></i></label>
                                    <input name="user-avatar" type="file" class="d-none" id="upload-profile-image">
                                </div>
                            </form>
                        </div>

                        <form id="update-rz-user-profile">
                            <?php $profile_data_modal = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}rz_user_profile_data WHERE user_id = '$user_id'");
                            $user_data = wp_get_current_user();
                            ?>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="first_name" class="imit-font fw-500 text-dark fz-16">Fast name <span class="text-danger">*</span></label>
                                    <input name="first_name" type="text" id="first_name" class="form-control imit-font fz-14 text-dark mt-1 rz-border" placeholder="Jhon" value="<?php echo $user_data->user_firstname; ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="last_name" class="imit-font fw-500 text-dark fz-16">Last name <span class="text-danger">*</span></label>
                                    <input name="last_name" type="text" id="last_name" class="form-control imit-font fz-14 text-dark mt-1 rz-border" placeholder="Doe" value="<?php echo $user_data->user_lastname; ?>">
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="bio" class="imit-font fw-500 text-dark fz-16">Occupation / Bio <span class="text-danger">*</span></label>
                                    <input name="bio" type="text" id="bio" class="form-control imit-font fz-14 text-dark mt-1 rz-border" placeholder="UI & UX designer" value="<?php echo $profile_data_modal->occupation; ?>">
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="email" class="imit-font fw-500 text-dark fz-16">Email <span class="text-danger">*</span></label>
                                    <input type="text" id="email" class="form-control imit-font fz-14 text-dark mt-1 rz-border" placeholder="email@gmail.com" value="<?php echo $user_data->user_email; ?>" disabled>
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="" class="imit-font fw-500 text-dark fz-16">Mobile Number</label>
                                    <div class="input-group mt-1">
                                        <span class="input-group-text rz-light-bg imit-font fz-14 border-0 rz-color fw-500" id="basic-addon1">+91</span>
                                        <input name="cell" type="text" class="form-control imit-font fz-14 text-dark rz-border" placeholder="EX “16945 950146”" aria-label="Username" aria-describedby="basic-addon1" value="<?php echo $profile_data_modal->phone_number; ?>">
                                    </div>
                                </div>
                                <div class="col-12 mb-3">
                                    <div class="form-check">
                                        <input name="whatsapp_alert" class="form-check-input" type="checkbox" id="rz-alert" value="yes" <?php if($profile_data_modal->whatsapp_alert == 'yes'){echo 'checked';} ?>>
                                        <label class="form-check-label rz-secondary-color imit-font fz-14" for="rz-alert">
                                            Whatsapp alerts to their mobile number.
                                        </label>
                                    </div>
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="location" class="imit-font fw-500 text-dark fz-16">Location <span class="text-danger">*</span></label>
                                    <div class="row mt-1">
                                        <div class="col-md-6">
                                            <select id="country" name="country" class="form-select rz-border imit-font fz-14">
                                                <option value="Afganistan" <?php if($profile_data_modal->country == 'Afganistan'){echo 'selected';} ?>>Afghanistan</option>
                                                <option value="Albania" <?php if($profile_data_modal->country == 'Albania'){echo 'selected';} ?>>Albania</option>
                                                <option value="Algeria" <?php if($profile_data_modal->country == 'Algeria'){echo 'selected';} ?>>Algeria</option>
                                                <option value="American Samoa" <?php if($profile_data_modal->country == 'American Samoa'){echo 'selected';} ?>>American Samoa</option>
                                                <option value="Andorra" <?php if($profile_data_modal->country == 'Andorra'){echo 'selected';} ?>>Andorra</option>
                                                <option value="Angola" <?php if($profile_data_modal->country == 'Angola'){echo 'selected';} ?>>Angola</option>
                                                <option value="Anguilla" <?php if($profile_data_modal->country == 'Anguilla'){echo 'selected';} ?>>Anguilla</option>
                                                <option value="Antigua & Barbuda" <?php if($profile_data_modal->country == 'Antigua & Barbuda'){echo 'selected';} ?>>Antigua & Barbuda</option>
                                                <option value="Argentina" <?php if($profile_data_modal->country == 'Argentina'){echo 'selected';} ?>>Argentina</option>
                                                <option value="Armenia" <?php if($profile_data_modal->country == 'Armenia'){echo 'selected';} ?>>Armenia</option>
                                                <option value="Aruba" <?php if($profile_data_modal->country == 'Aruba'){echo 'selected';} ?>>Aruba</option>
                                                <option value="Australia" <?php if($profile_data_modal->country == 'Australia'){echo 'selected';} ?>>Australia</option>
                                                <option value="Austria" <?php if($profile_data_modal->country == 'Austria'){echo 'selected';} ?>>Austria</option>
                                                <option value="Azerbaijan" <?php if($profile_data_modal->country == 'Azerbaijan'){echo 'selected';} ?>>Azerbaijan</option>
                                                <option value="Bahamas" <?php if($profile_data_modal->country == 'Bahamas'){echo 'selected';} ?>>Bahamas</option>
                                                <option value="Bahrain" <?php if($profile_data_modal->country == 'Bahrain'){echo 'selected';} ?>>Bahrain</option>
                                                <option value="Bangladesh" <?php if($profile_data_modal->country == 'Bangladesh'){echo 'selected';} ?>>Bangladesh</option>
                                                <option value="Barbados" <?php if($profile_data_modal->country == 'Barbados'){echo 'selected';} ?>>Barbados</option>
                                                <option value="Belarus" <?php if($profile_data_modal->country == 'Belarus'){echo 'selected';} ?>>Belarus</option>
                                                <option value="Belgium" <?php if($profile_data_modal->country == 'Belgium'){echo 'selected';} ?>>Belgium</option>
                                                <option value="Belize" <?php if($profile_data_modal->country == 'Benin'){echo 'selected';} ?>>Belize</option>
                                                <option value="Benin" <?php if($profile_data_modal->country == 'American Samoa'){echo 'selected';} ?>>Benin</option>
                                                <option value="Bermuda" <?php if($profile_data_modal->country == 'Bermuda'){echo 'selected';} ?>>Bermuda</option>
                                                <option value="Bhutan" <?php if($profile_data_modal->country == 'Bhutan'){echo 'selected';} ?>>Bhutan</option>
                                                <option value="Bolivia" <?php if($profile_data_modal->country == 'Bolivia'){echo 'selected';} ?>>Bolivia</option>
                                                <option value="Bonaire" <?php if($profile_data_modal->country == 'Bonaire'){echo 'selected';} ?>>Bonaire</option>
                                                <option value="Bosnia & Herzegovina" <?php if($profile_data_modal->country == 'Bosnia & Herzegovina'){echo 'selected';} ?>>Bosnia & Herzegovina</option>
                                                <option value="Botswana" <?php if($profile_data_modal->country == 'Botswana'){echo 'selected';} ?>>Botswana</option>
                                                <option value="Brazil" <?php if($profile_data_modal->country == 'Brazil'){echo 'selected';} ?>>Brazil</option>
                                                <option value="British Indian Ocean Ter" <?php if($profile_data_modal->country == 'British Indian Ocean Ter'){echo 'selected';} ?>>British Indian Ocean Ter</option>
                                                <option value="Brunei" <?php if($profile_data_modal->country == 'Brunei'){echo 'selected';} ?>>Brunei</option>
                                                <option value="Bulgaria" <?php if($profile_data_modal->country == 'Bulgaria'){echo 'selected';} ?>>Bulgaria</option>
                                                <option value="Burkina Faso" <?php if($profile_data_modal->country == 'Burkina Faso'){echo 'selected';} ?>>Burkina Faso</option>
                                                <option value="Burundi" <?php if($profile_data_modal->country == 'Burundi'){echo 'selected';} ?>>Burundi</option>
                                                <option value="Cambodia" <?php if($profile_data_modal->country == 'Cambodia'){echo 'selected';} ?>>Cambodia</option>
                                                <option value="Cameroon" <?php if($profile_data_modal->country == 'Cameroon'){echo 'selected';} ?>>Cameroon</option>
                                                <option value="Canada" <?php if($profile_data_modal->country == 'Canada'){echo 'selected';} ?>>Canada</option>
                                                <option value="Canary Islands" <?php if($profile_data_modal->country == 'Canary Islands'){echo 'selected';} ?>>Canary Islands</option>
                                                <option value="Cape Verde" <?php if($profile_data_modal->country == 'Cape Verde'){echo 'selected';} ?>>Cape Verde</option>
                                                <option value="Cayman Islands" <?php if($profile_data_modal->country == 'Cayman Islands'){echo 'selected';} ?>>Cayman Islands</option>
                                                <option value="Central African Republic" <?php if($profile_data_modal->country == 'Central African Republic'){echo 'selected';} ?>>Central African Republic</option>
                                                <option value="Chad" <?php if($profile_data_modal->country == 'Chad'){echo 'selected';} ?>>Chad</option>
                                                <option value="Channel Islands" <?php if($profile_data_modal->country == 'Channel Islands'){echo 'selected';} ?>>Channel Islands</option>
                                                <option value="Chile" <?php if($profile_data_modal->country == 'Chile'){echo 'selected';} ?>>Chile</option>
                                                <option value="China" <?php if($profile_data_modal->country == 'China'){echo 'selected';} ?>>China</option>
                                                <option value="Christmas Island" <?php if($profile_data_modal->country == 'Christmas Island'){echo 'selected';} ?>>Christmas Island</option>
                                                <option value="Cocos Island" <?php if($profile_data_modal->country == 'Cocos Island'){echo 'selected';} ?>>Cocos Island</option>
                                                <option value="Colombia" <?php if($profile_data_modal->country == 'Colombia'){echo 'selected';} ?>>Colombia</option>
                                                <option value="Comoros" <?php if($profile_data_modal->country == 'Comoros'){echo 'selected';} ?>>Comoros</option>
                                                <option value="Congo" <?php if($profile_data_modal->country == 'Congo'){echo 'selected';} ?>>Congo</option>
                                                <option value="Cook Islands" <?php if($profile_data_modal->country == 'Cook Islands'){echo 'selected';} ?>>Cook Islands</option>
                                                <option value="Costa Rica" <?php if($profile_data_modal->country == 'Costa Rica'){echo 'selected';} ?>>Costa Rica</option>
                                                <option value="Cote DIvoire" <?php if($profile_data_modal->country == 'Cote DIvoire'){echo 'selected';} ?>>Cote DIvoire</option>
                                                <option value="Croatia" <?php if($profile_data_modal->country == 'Croatia'){echo 'selected';} ?>>Croatia</option>
                                                <option value="Cuba" <?php if($profile_data_modal->country == 'Cuba'){echo 'selected';} ?>>Cuba</option>
                                                <option value="Curaco" <?php if($profile_data_modal->country == 'Curaco'){echo 'selected';} ?>>Curacao</option>
                                                <option value="Cyprus" <?php if($profile_data_modal->country == 'Cyprus'){echo 'selected';} ?>>Cyprus</option>
                                                <option value="Czech Republic" <?php if($profile_data_modal->country == 'Czech Republic'){echo 'selected';} ?>>Czech Republic</option>
                                                <option value="Denmark" <?php if($profile_data_modal->country == 'Denmark'){echo 'selected';} ?>>Denmark</option>
                                                <option value="Djibouti" <?php if($profile_data_modal->country == 'Djibouti'){echo 'selected';} ?>>Djibouti</option>
                                                <option value="Dominica" <?php if($profile_data_modal->country == 'Dominica'){echo 'selected';} ?>>Dominica</option>
                                                <option value="Dominican Republic" <?php if($profile_data_modal->country == 'Dominican Republic'){echo 'selected';} ?>>Dominican Republic</option>
                                                <option value="East Timor" <?php if($profile_data_modal->country == 'East Timor'){echo 'selected';} ?>>East Timor</option>
                                                <option value="Ecuador" <?php if($profile_data_modal->country == 'Ecuador'){echo 'selected';} ?>>Ecuador</option>
                                                <option value="Egypt" <?php if($profile_data_modal->country == 'Egypt'){echo 'selected';} ?>>Egypt</option>
                                                <option value="El Salvador" <?php if($profile_data_modal->country == 'El Salvador'){echo 'selected';} ?>>El Salvador</option>
                                                <option value="Equatorial Guinea" <?php if($profile_data_modal->country == 'Equatorial Guinea'){echo 'selected';} ?>>Equatorial Guinea</option>
                                                <option value="Eritrea" <?php if($profile_data_modal->country == 'Eritrea'){echo 'selected';} ?>>Eritrea</option>
                                                <option value="Estonia" <?php if($profile_data_modal->country == 'Estonia'){echo 'selected';} ?>>Estonia</option>
                                                <option value="Ethiopia" <?php if($profile_data_modal->country == 'Ethiopia'){echo 'selected';} ?>>Ethiopia</option>
                                                <option value="Falkland Islands" <?php if($profile_data_modal->country == 'Falkland Islands'){echo 'selected';} ?>>Falkland Islands</option>
                                                <option value="Faroe Islands" <?php if($profile_data_modal->country == 'Faroe Islands'){echo 'selected';} ?>>Faroe Islands</option>
                                                <option value="Fiji" <?php if($profile_data_modal->country == 'Fiji'){echo 'selected';} ?>>Fiji</option>
                                                <option value="Finland" <?php if($profile_data_modal->country == 'Finland'){echo 'selected';} ?>>Finland</option>
                                                <option value="France" <?php if($profile_data_modal->country == 'France'){echo 'selected';} ?>>France</option>
                                                <option value="French Guiana" <?php if($profile_data_modal->country == 'French Polynesia'){echo 'selected';} ?>>French Guiana</option>
                                                <option value="French Polynesia" <?php if($profile_data_modal->country == 'American Samoa'){echo 'selected';} ?>>French Polynesia</option>
                                                <option value="French Southern Ter" <?php if($profile_data_modal->country == 'French Southern Ter'){echo 'selected';} ?>>French Southern Ter</option>
                                                <option value="Gabon" <?php if($profile_data_modal->country == 'Gabon'){echo 'selected';} ?>>Gabon</option>
                                                <option value="Gambia" <?php if($profile_data_modal->country == 'Gambia'){echo 'selected';} ?>>Gambia</option>
                                                <option value="Georgia" <?php if($profile_data_modal->country == 'Georgia'){echo 'selected';} ?>>Georgia</option>
                                                <option value="Germany" <?php if($profile_data_modal->country == 'Germany'){echo 'selected';} ?>>Germany</option>
                                                <option value="Ghana" <?php if($profile_data_modal->country == 'Ghana'){echo 'selected';} ?>>Ghana</option>
                                                <option value="Gibraltar" <?php if($profile_data_modal->country == 'Gibraltar'){echo 'selected';} ?>>Gibraltar</option>
                                                <option value="Great Britain" <?php if($profile_data_modal->country == 'Great Britain'){echo 'selected';} ?>>Great Britain</option>
                                                <option value="Greece" <?php if($profile_data_modal->country == 'Greece'){echo 'selected';} ?>>Greece</option>
                                                <option value="Greenland" <?php if($profile_data_modal->country == 'Greenland'){echo 'selected';} ?>>Greenland</option>
                                                <option value="Grenada" <?php if($profile_data_modal->country == 'Grenada'){echo 'selected';} ?>>Grenada</option>
                                                <option value="Guadeloupe" <?php if($profile_data_modal->country == 'Guadeloupe'){echo 'selected';} ?>>Guadeloupe</option>
                                                <option value="Guam" <?php if($profile_data_modal->country == 'Guam'){echo 'selected';} ?>>Guam</option>
                                                <option value="Guatemala" <?php if($profile_data_modal->country == 'Guatemala'){echo 'selected';} ?>>Guatemala</option>
                                                <option value="Guinea" <?php if($profile_data_modal->country == 'Guinea'){echo 'selected';} ?>>Guinea</option>
                                                <option value="Guyana" <?php if($profile_data_modal->country == 'Guyana'){echo 'selected';} ?>>Guyana</option>
                                                <option value="Haiti" <?php if($profile_data_modal->country == 'Haiti'){echo 'selected';} ?>>Haiti</option>
                                                <option value="Hawaii" <?php if($profile_data_modal->country == 'Hawaii'){echo 'selected';} ?>>Hawaii</option>
                                                <option value="Honduras" <?php if($profile_data_modal->country == 'Honduras'){echo 'selected';} ?>>Honduras</option>
                                                <option value="Hong Kong" <?php if($profile_data_modal->country == 'Hong Kong'){echo 'selected';} ?>>Hong Kong</option>
                                                <option value="Hungary" <?php if($profile_data_modal->country == 'Hungary'){echo 'selected';} ?>>Hungary</option>
                                                <option value="Iceland" <?php if($profile_data_modal->country == 'Iceland'){echo 'selected';} ?>>Iceland</option>
                                                <option value="Indonesia" <?php if($profile_data_modal->country == 'Indonesia'){echo 'selected';} ?>>Indonesia</option>
                                                <option value="India" <?php if($profile_data_modal->country == 'India'){echo 'selected';} ?>>India</option>
                                                <option value="Iran" <?php if($profile_data_modal->country == 'Iran'){echo 'selected';} ?>>Iran</option>
                                                <option value="Iraq" <?php if($profile_data_modal->country == 'Iraq'){echo 'selected';} ?>>Iraq</option>
                                                <option value="Ireland" <?php if($profile_data_modal->country == 'Ireland'){echo 'selected';} ?>>Ireland</option>
                                                <option value="Isle of Man" <?php if($profile_data_modal->country == 'Isle of Man'){echo 'selected';} ?>>Isle of Man</option>
                                                <option value="Israel" <?php if($profile_data_modal->country == 'Israel'){echo 'selected';} ?>>Israel</option>
                                                <option value="Italy" <?php if($profile_data_modal->country == 'Italy'){echo 'selected';} ?>>Italy</option>
                                                <option value="Jamaica" <?php if($profile_data_modal->country == 'Jamaica'){echo 'selected';} ?>>Jamaica</option>
                                                <option value="Japan" <?php if($profile_data_modal->country == 'Japan'){echo 'selected';} ?>>Japan</option>
                                                <option value="Jordan" <?php if($profile_data_modal->country == 'Jordan'){echo 'selected';} ?>>Jordan</option>
                                                <option value="Kazakhstan" <?php if($profile_data_modal->country == 'Kazakhstan'){echo 'selected';} ?>>Kazakhstan</option>
                                                <option value="Kenya" <?php if($profile_data_modal->country == 'Kenya'){echo 'selected';} ?>>Kenya</option>
                                                <option value="Kiribati" <?php if($profile_data_modal->country == 'Kiribati'){echo 'selected';} ?>>Kiribati</option>
                                                <option value="Korea North" <?php if($profile_data_modal->country == 'Korea North'){echo 'selected';} ?>>Korea North</option>
                                                <option value="Korea Sout" <?php if($profile_data_modal->country == 'Korea Sout'){echo 'selected';} ?>>Korea South</option>
                                                <option value="Kuwait" <?php if($profile_data_modal->country == 'Kuwait'){echo 'selected';} ?>>Kuwait</option>
                                                <option value="Kyrgyzstan" <?php if($profile_data_modal->country == 'Laos'){echo 'selected';} ?>>Kyrgyzstan</option>
                                                <option value="Laos" <?php if($profile_data_modal->country == 'Laos'){echo 'selected';} ?>>Laos</option>
                                                <option value="Latvia" <?php if($profile_data_modal->country == 'Latvia'){echo 'selected';} ?>>Latvia</option>
                                                <option value="Lebanon" <?php if($profile_data_modal->country == 'Lebanon'){echo 'selected';} ?>>Lebanon</option>
                                                <option value="Lesotho" <?php if($profile_data_modal->country == 'Lesotho'){echo 'selected';} ?>>Lesotho</option>
                                                <option value="Liberia" <?php if($profile_data_modal->country == 'Liberia'){echo 'selected';} ?>>Liberia</option>
                                                <option value="Libya" <?php if($profile_data_modal->country == 'Liechtenstein'){echo 'selected';} ?>>Libya</option>
                                                <option value="Liechtenstein" <?php if($profile_data_modal->country == 'American Samoa'){echo 'selected';} ?>>Liechtenstein</option>
                                                <option value="Lithuania" <?php if($profile_data_modal->country == 'Lithuania'){echo 'selected';} ?>>Lithuania</option>
                                                <option value="Luxembourg" <?php if($profile_data_modal->country == 'Luxembourg'){echo 'selected';} ?>>Luxembourg</option>
                                                <option value="Macau" <?php if($profile_data_modal->country == 'Macau'){echo 'selected';} ?>>Macau</option>
                                                <option value="Macedonia" <?php if($profile_data_modal->country == 'Macedonia'){echo 'selected';} ?>>Macedonia</option>
                                                <option value="Madagascar" <?php if($profile_data_modal->country == 'Malaysia'){echo 'selected';} ?>>Madagascar</option>
                                                <option value="Malaysia" <?php if($profile_data_modal->country == 'American Samoa'){echo 'selected';} ?>>Malaysia</option>
                                                <option value="Malawi" <?php if($profile_data_modal->country == 'Malawi'){echo 'selected';} ?>>Malawi</option>
                                                <option value="Maldives" <?php if($profile_data_modal->country == 'Maldives'){echo 'selected';} ?>>Maldives</option>
                                                <option value="Mali" <?php if($profile_data_modal->country == 'American Samoa'){echo 'selected';} ?>>Mali</option>
                                                <option value="Malta" <?php if($profile_data_modal->country == 'Malta'){echo 'selected';} ?>>Malta</option>
                                                <option value="Marshall Islands" <?php if($profile_data_modal->country == 'Marshall Islands'){echo 'selected';} ?>>Marshall Islands</option>
                                                <option value="Martinique" <?php if($profile_data_modal->country == 'Martinique'){echo 'selected';} ?>>Martinique</option>
                                                <option value="Mauritania" <?php if($profile_data_modal->country == 'Mauritania'){echo 'selected';} ?>>Mauritania</option>
                                                <option value="Mauritius" <?php if($profile_data_modal->country == 'Mauritius'){echo 'selected';} ?>>Mauritius</option>
                                                <option value="Mayotte" <?php if($profile_data_modal->country == 'Mayotte'){echo 'selected';} ?>>Mayotte</option>
                                                <option value="Mexico" <?php if($profile_data_modal->country == 'Mexico'){echo 'selected';} ?>>Mexico</option>
                                                <option value="Midway Islands" <?php if($profile_data_modal->country == 'Midway Islands'){echo 'selected';} ?>>Midway Islands</option>
                                                <option value="Moldova" <?php if($profile_data_modal->country == 'Moldova'){echo 'selected';} ?>>Moldova</option>
                                                <option value="Monaco" <?php if($profile_data_modal->country == 'Monaco'){echo 'selected';} ?>>Monaco</option>
                                                <option value="Mongolia" <?php if($profile_data_modal->country == 'Mongolia'){echo 'selected';} ?>>Mongolia</option>
                                                <option value="Montserrat" <?php if($profile_data_modal->country == 'Montserrat'){echo 'selected';} ?>>Montserrat</option>
                                                <option value="Morocco" <?php if($profile_data_modal->country == 'Morocco'){echo 'selected';} ?>>Morocco</option>
                                                <option value="Mozambique" <?php if($profile_data_modal->country == 'Mozambique'){echo 'selected';} ?>>Mozambique</option>
                                                <option value="Myanmar" <?php if($profile_data_modal->country == 'Myanmar'){echo 'selected';} ?>>Myanmar</option>
                                                <option value="Nambia" <?php if($profile_data_modal->country == 'Nambia'){echo 'selected';} ?>>Nambia</option>
                                                <option value="Nauru" <?php if($profile_data_modal->country == 'Nauru'){echo 'selected';} ?>>Nauru</option>
                                                <option value="Nepal" <?php if($profile_data_modal->country == 'Nepal'){echo 'selected';} ?>>Nepal</option>
                                                <option value="Netherland Antilles" <?php if($profile_data_modal->country == 'Netherland Antilles'){echo 'selected';} ?>>Netherland Antilles</option>
                                                <option value="Netherlands" <?php if($profile_data_modal->country == 'Netherlands'){echo 'selected';} ?>>Netherlands (Holland, Europe)</option>
                                                <option value="Nevis" <?php if($profile_data_modal->country == 'Nevis'){echo 'selected';} ?>>Nevis</option>
                                                <option value="New Caledonia" <?php if($profile_data_modal->country == 'New Caledonia'){echo 'selected';} ?>>New Caledonia</option>
                                                <option value="New Zealand" <?php if($profile_data_modal->country == 'New Zealand'){echo 'selected';} ?>>New Zealand</option>
                                                <option value="Nicaragua" <?php if($profile_data_modal->country == 'Nicaragua'){echo 'selected';} ?>>Nicaragua</option>
                                                <option value="Niger" <?php if($profile_data_modal->country == 'Niger'){echo 'selected';} ?>>Niger</option>
                                                <option value="Nigeria" <?php if($profile_data_modal->country == 'Nigeria'){echo 'selected';} ?>>Nigeria</option>
                                                <option value="Niue" <?php if($profile_data_modal->country == 'Niue'){echo 'selected';} ?>>Niue</option>
                                                <option value="Norfolk Island" <?php if($profile_data_modal->country == 'Norfolk Island'){echo 'selected';} ?>>Norfolk Island</option>
                                                <option value="Norway" <?php if($profile_data_modal->country == 'Morocco'){echo 'selected';} ?>>Norway</option>
                                                <option value="Oman" <?php if($profile_data_modal->country == 'Oman'){echo 'selected';} ?>>Oman</option>
                                                <option value="Pakistan" <?php if($profile_data_modal->country == 'Pakistan'){echo 'selected';} ?>>Pakistan</option>
                                                <option value="Palau Island" <?php if($profile_data_modal->country == 'Palau Island'){echo 'selected';} ?>>Palau Island</option>
                                                <option value="Palestine" <?php if($profile_data_modal->country == 'Palestine'){echo 'selected';} ?>>Palestine</option>
                                                <option value="Panama" <?php if($profile_data_modal->country == 'Panama'){echo 'selected';} ?>>Panama</option>
                                                <option value="Papua New Guinea" <?php if($profile_data_modal->country == 'Papua New Guinea'){echo 'selected';} ?>>Papua New Guinea</option>
                                                <option value="Paraguay" <?php if($profile_data_modal->country == 'Paraguay'){echo 'selected';} ?>>Paraguay</option>
                                                <option value="Peru" <?php if($profile_data_modal->country == 'Peru'){echo 'selected';} ?>>Peru</option>
                                                <option value="Phillipines" <?php if($profile_data_modal->country == 'Phillipines'){echo 'selected';} ?>>Philippines</option>
                                                <option value="Pitcairn Island" <?php if($profile_data_modal->country == 'Pitcairn Island'){echo 'selected';} ?>>Pitcairn Island</option>
                                                <option value="Poland" <?php if($profile_data_modal->country == 'Poland'){echo 'selected';} ?>>Poland</option>
                                                <option value="Portugal" <?php if($profile_data_modal->country == 'Portugal'){echo 'selected';} ?>>Portugal</option>
                                                <option value="Puerto Rico" <?php if($profile_data_modal->country == 'Puerto Rico'){echo 'selected';} ?>>Puerto Rico</option>
                                                <option value="Qatar" <?php if($profile_data_modal->country == 'Qatar'){echo 'Qatar';} ?>>Qatar</option>
                                                <option value="Republic of Montenegro" <?php if($profile_data_modal->country == 'Republic of Serbia'){echo 'selected';} ?>>Republic of Montenegro</option>
                                                <option value="Republic of Serbia" <?php if($profile_data_modal->country == 'Morocco'){echo 'selected';} ?>>Republic of Serbia</option>
                                                <option value="Reunion" <?php if($profile_data_modal->country == 'Reunion'){echo 'selected';} ?>>Reunion</option>
                                                <option value="Romania" <?php if($profile_data_modal->country == 'Romania'){echo 'selected';} ?>>Romania</option>
                                                <option value="Russia" <?php if($profile_data_modal->country == 'Russia'){echo 'selected';} ?>>Russia</option>
                                                <option value="Rwanda" <?php if($profile_data_modal->country == 'Rwanda'){echo 'selected';} ?>>Rwanda</option>
                                                <option value="St Barthelemy" <?php if($profile_data_modal->country == 'St Barthelemy'){echo 'selected';} ?>>St Barthelemy</option>
                                                <option value="St Eustatius" <?php if($profile_data_modal->country == 'St Eustatius'){echo 'selected';} ?>>St Eustatius</option>
                                                <option value="St Helena" <?php if($profile_data_modal->country == 'St Helena'){echo 'selected';} ?>>St Helena</option>
                                                <option value="St Kitts-Nevis" <?php if($profile_data_modal->country == 'St Kitts-Nevis'){echo 'selected';} ?>>St Kitts-Nevis</option>
                                                <option value="St Lucia" <?php if($profile_data_modal->country == 'St Lucia'){echo 'selected';} ?>>St Lucia</option>
                                                <option value="St Maarten" <?php if($profile_data_modal->country == 'St Maarten'){echo 'selected';} ?>>St Maarten</option>
                                                <option value="St Pierre & Miquelon" <?php if($profile_data_modal->country == 'St Pierre & Miquelon'){echo 'selected';} ?>>St Pierre & Miquelon</option>
                                                <option value="St Vincent & Grenadines" <?php if($profile_data_modal->country == 'St Vincent & Grenadines'){echo 'selected';} ?>>St Vincent & Grenadines</option>
                                                <option value="Saipan" <?php if($profile_data_modal->country == 'Saipan'){echo 'selected';} ?>>Saipan</option>
                                                <option value="Samoa" <?php if($profile_data_modal->country == 'Samoa'){echo 'selected';} ?>>Samoa</option>
                                                <option value="Samoa American" <?php if($profile_data_modal->country == 'Samoa American'){echo 'selected';} ?>>Samoa American</option>
                                                <option value="San Marino" <?php if($profile_data_modal->country == 'San Marino'){echo 'selected';} ?>>San Marino</option>
                                                <option value="Sao Tome & Principe" <?php if($profile_data_modal->country == 'Sao Tome & Principe'){echo 'selected';} ?>>Sao Tome & Principe</option>
                                                <option value="Saudi Arabia" <?php if($profile_data_modal->country == 'Saudi Arabia'){echo 'selected';} ?>>Saudi Arabia</option>
                                                <option value="Senegal" <?php if($profile_data_modal->country == 'Senegal'){echo 'selected';} ?>>Senegal</option>
                                                <option value="Seychelles" <?php if($profile_data_modal->country == 'Seychelles'){echo 'selected';} ?>>Seychelles</option>
                                                <option value="Sierra Leone" <?php if($profile_data_modal->country == 'Singapore'){echo 'selected';} ?>>Sierra Leone</option>
                                                <option value="Singapore" <?php if($profile_data_modal->country == 'Morocco'){echo 'selected';} ?>>Singapore</option>
                                                <option value="Slovakia" <?php if($profile_data_modal->country == 'Slovakia'){echo 'selected';} ?>>Slovakia</option>
                                                <option value="Slovenia" <?php if($profile_data_modal->country == 'Slovenia'){echo 'selected';} ?>>Slovenia</option>
                                                <option value="Solomon Islands" <?php if($profile_data_modal->country == 'Solomon Islands'){echo 'selected';} ?>>Solomon Islands</option>
                                                <option value="Somalia" <?php if($profile_data_modal->country == 'Somalia'){echo 'selected';} ?>>Somalia</option>
                                                <option value="South Africa" <?php if($profile_data_modal->country == 'South Africa'){echo 'selected';} ?>>South Africa</option>
                                                <option value="Spain" <?php if($profile_data_modal->country == 'Spain'){echo 'selected';} ?>>Spain</option>
                                                <option value="Sri Lanka" <?php if($profile_data_modal->country == 'Sri Lanka'){echo 'selected';} ?>>Sri Lanka</option>
                                                <option value="Sudan" <?php if($profile_data_modal->country == 'Sudan'){echo 'selected';} ?>>Sudan</option>
                                                <option value="Suriname" <?php if($profile_data_modal->country == 'Suriname'){echo 'selected';} ?>>Suriname</option>
                                                <option value="Swaziland" <?php if($profile_data_modal->country == 'Swaziland'){echo 'selected';} ?>>Swaziland</option>
                                                <option value="Sweden" <?php if($profile_data_modal->country == 'Sweden'){echo 'selected';} ?>>Sweden</option>
                                                <option value="Switzerland" <?php if($profile_data_modal->country == 'Switzerland'){echo 'selected';} ?>>Switzerland</option>
                                                <option value="Syria" <?php if($profile_data_modal->country == 'Syria'){echo 'selected';} ?>>Syria</option>
                                                <option value="Tahiti" <?php if($profile_data_modal->country == 'Tahiti'){echo 'selected';} ?>>Tahiti</option>
                                                <option value="Taiwan" <?php if($profile_data_modal->country == 'Taiwan'){echo 'selected';} ?>>Taiwan</option>
                                                <option value="Tajikistan" <?php if($profile_data_modal->country == 'Tajikistan'){echo 'selected';} ?>>Tajikistan</option>
                                                <option value="Tanzania" <?php if($profile_data_modal->country == 'Tanzania'){echo 'selected';} ?>>Tanzania</option>
                                                <option value="Thailand" <?php if($profile_data_modal->country == 'Thailand'){echo 'selected';} ?>>Thailand</option>
                                                <option value="Togo" <?php if($profile_data_modal->country == 'Togo'){echo 'selected';} ?>>Togo</option>
                                                <option value="Tokelau" <?php if($profile_data_modal->country == 'Tokelau'){echo 'selected';} ?>>Tokelau</option>
                                                <option value="Tonga" <?php if($profile_data_modal->country == 'Tonga'){echo 'selected';} ?>>Tonga</option>
                                                <option value="Trinidad & Tobago" <?php if($profile_data_modal->country == 'Trinidad & Tobago'){echo 'selected';} ?>>Trinidad & Tobago</option>
                                                <option value="Tunisia" <?php if($profile_data_modal->country == 'Tunisia'){echo 'selected';} ?>>Tunisia</option>
                                                <option value="Turkey" <?php if($profile_data_modal->country == 'Turkey'){echo 'selected';} ?>>Turkey</option>
                                                <option value="Turkmenistan" <?php if($profile_data_modal->country == 'Turkmenistan'){echo 'selected';} ?>>Turkmenistan</option>
                                                <option value="Turks & Caicos Is" <?php if($profile_data_modal->country == 'Turks & Caicos Is'){echo 'selected';} ?>>Turks & Caicos Is</option>
                                                <option value="Tuvalu" <?php if($profile_data_modal->country == 'Tuvalu'){echo 'selected';} ?>>Tuvalu</option>
                                                <option value="Uganda" <?php if($profile_data_modal->country == 'Uganda'){echo 'selected';} ?>>Uganda</option>
                                                <option value="United Kingdom" <?php if($profile_data_modal->country == 'United Kingdom'){echo 'selected';} ?>>United Kingdom</option>
                                                <option value="Ukraine" <?php if($profile_data_modal->country == 'Ukraine'){echo 'selected';} ?>>Ukraine</option>
                                                <option value="United Arab Erimates" <?php if($profile_data_modal->country == 'United Arab Erimates'){echo 'selected';} ?>>United Arab Emirates</option>
                                                <option value="United States of America" <?php if($profile_data_modal->country == 'United States of America'){echo 'selected';} ?>>United States of America</option>
                                                <option value="Uraguay" <?php if($profile_data_modal->country == 'Uraguay'){echo 'selected';} ?>>Uruguay</option>
                                                <option value="Uzbekistan" <?php if($profile_data_modal->country == 'Uzbekistan'){echo 'selected';} ?>>Uzbekistan</option>
                                                <option value="Vanuatu" <?php if($profile_data_modal->country == 'Vanuatu'){echo 'selected';} ?>>Vanuatu</option>
                                                <option value="Vatican City State" <?php if($profile_data_modal->country == 'Vatican City State'){echo 'selected';} ?>>Vatican City State</option>
                                                <option value="Venezuela" <?php if($profile_data_modal->country == 'Venezuela'){echo 'selected';} ?>>Venezuela</option>
                                                <option value="Vietnam" <?php if($profile_data_modal->country == 'Vietnam'){echo 'selected';} ?>>Vietnam</option>
                                                <option value="Virgin Islands (Brit)" <?php if($profile_data_modal->country == 'Virgin Islands (Brit)'){echo 'selected';} ?>>Virgin Islands (Brit)</option>
                                                <option value="Virgin Islands (USA)" <?php if($profile_data_modal->country == 'Virgin Islands (USA)'){echo 'selected';} ?>>Virgin Islands (USA)</option>
                                                <option value="Wake Island" <?php if($profile_data_modal->country == 'Wake Island'){echo 'selected';} ?>>Wake Island</option>
                                                <option value="Wallis & Futana Is" <?php if($profile_data_modal->country == 'Wallis & Futana Is'){echo 'selected';} ?>>Wallis & Futana Is</option>
                                                <option value="Yemen" <?php if($profile_data_modal->country == 'Yemen'){echo 'selected';} ?>>Yemen</option>
                                                <option value="Zaire" <?php if($profile_data_modal->country == 'Zaire'){echo 'selected';} ?>>Zaire</option>
                                                <option value="Zambia" <?php if($profile_data_modal->country == 'Zambia'){echo 'selected';} ?>>Zambia</option>
                                                <option value="Zimbabwe" <?php if($profile_data_modal->country == 'Zimbabwe'){echo 'selected';} ?>>Zimbabwe</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <input name="city" type="text" class="form-control imit-font fz-14 text-dark fw-400" placeholder="Dhaka" value="<?php echo $profile_data_modal->city; ?>">
                                        </div>
                                    </div>
                                </div>

                                <ul class="list-group">
                                    <?php
                                    $user_id = get_current_user_id();
                                    $get_all_workplaces = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_user_work where user_id = '$user_id'", ARRAY_A);

                                    foreach($get_all_workplaces as $workplace){
                                        ?>
                                        <li class="list-group-item d-flex flex-row justify-content-between align-items-center">
                                            <p class="m-0 imit-font rz-secondary-color fz-14 fw-400"><?php echo $workplace['position']; ?> at <strong class="rz-color fw-500"><?php echo $workplace['company']; ?></strong> from <?php echo $workplace['start_year']; ?> to <?php echo $workplace['end_year']; ?></p>
                                            <a href="#" class="text-secondary bg-transparent fz-16" data-workplace_id="<?php echo $workplace['id']; ?>" id="delete-workplace"><i class="fas fa-times"></i></a>
                                        </li>
                                            <?php
                                    }
                                    ?>
                                </ul>
                                <div class="col-12 mb-3">
                                    <div id="more-workplace"></div>
                                    <button type="button" class="rz-color fz-14 imit-font border-0 bg-transparent mt-2" id="add-more-workplace"><i class="fas fa-plus-circle"></i> Add Workplace</button>
                                </div>

                                <ul class="list-group">
                                    <?php
                                    $user_id = get_current_user_id();
                                    $get_all_edu = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_user_education where user_id = '$user_id'", ARRAY_A);

                                    foreach($get_all_edu as $education){
                                        ?>
                                        <li class="list-group-item d-flex flex-row justify-content-between align-items-center">
                                            <p class="m-0 imit-font rz-secondary-color fz-14 fw-400"><?php echo $education['concentrations']; ?> from <strong class="rz-color fw-500"><?php echo $education['college']; ?></strong> from <?php echo $workplace['start_year']; ?> to <?php echo $workplace['end_year']; ?></p>
                                            <a href="#" class="text-secondary bg-transparent fz-16" data-education_id="<?php echo $education['id'] ?>" id="delete-education"><i class="fas fa-times"></i></a>
                                        </li>
                                        <?php
                                    }
                                    ?>
                                </ul>
                                <div class="col-12 mb-3">
                                    <div id="more-education"></div>
                                    <button type="button" class="rz-color fz-14 imit-font border-0 bg-transparent mt-2" id="add-more-education"><i class="fas fa-plus-circle"></i> Add Education</button>
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="" class="imit-font fz-14 fw-500 fz-16">Language</label>
                                    <span class="rz-secondary-color fz-12 imit-font">Seperate with (,) coma.</span>
                                    <input name="language" type="text" class="form-control imit-font fz-14 mt-1 rz-border" id="input-tags" placeholder="Ex: English,German,Italian" value="<?php echo $profile_data_modal->languages; ?>">
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="" class="imit-font fz-14 fw-500 fz-16">Knows</label>
                                    <span class="rz-secondary-color fz-12 imit-font">Seperate with (,) coma.</span>
                                    <input name="skill" type="text" class="form-control imit-font fz-14 mt-1 rz-border" placeholder="Ex: php,wordpress,laravel" value="<?php echo $profile_data_modal->skill; ?>">
                                </div>
                            </div>
                            <button type="submit" class="btn rz-bg-color text imit-font fz-14 fw-500 text-white d-table ms-auto">Save</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
            <?php
    }

    ?>

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


        if(is_user_logged_in(  )){
            ?>
            <div class="modal fade" id="dairy-edit-modal">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2 class="imit-font fz-14 fw-500 text-dark m-0">Edit dairy</h2>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form id="edit-dairy-form">
                                <div id="edit-dairy-message"></div>
                                <?php //wp_editor( '', 'dairy-text', [
                                    //'media_buttons' => false
                                //] ); ?>
                                <textarea name="dairy-text" id="" cols="30" rows="10" class="form-control fz-14 imit-font" placeholder="Write Something..."></textarea>
                                <input type="hidden" value="" name="id">
                                <div class="d-flex flex-row justify-content-between align-items-center mt-3">
                                    <div class="form-check">
                                        <input name="dairy-visiblity" class="form-check-input" type="checkbox" value="yes" id="edit-dairy-visiblity">
                                        <label class="form-check-label rz-secondary-color imit-font fz-14" for="edit-dairy-visiblity">
                                            Make it Public
                                        </label>
                                    </div>
                                    <button type="submit" class="btn rz-bg-color text-light fz-16 imit-font">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>
        <script type="text/javascript">
            let profile_user_id = <?php echo um_profile_id(); ?>;
        </script>
<?php
    return ob_get_clean();
});


/**
 * change profile image
 */
add_action('wp_ajax_imit_rz_change_profile_image', function(){
    global $wpdb;
     $nonce = $_POST['nonce'];
     if(wp_verify_nonce($nonce, 'rz-change-profile-user-nonce')){
         $image = $_FILES['user-avatar']['name'];
         $image_tmp = $_FILES['user-avatar']['tmp_name'];
         $user_id = get_current_user_id();
 
         $exp = explode('.', $image);
 
         $ext = strtolower(end($exp));
 
         $format = ['jpg', 'png', 'gif', 'jpeg'];
 
         $unique_name = md5(time().rand()).'.'.$ext;
 
         if(!empty($image) && in_array($ext, $format)){
             $rz_user_profile_data = $wpdb->prefix.'rz_user_profile_data';
 
             $get_profile_data = $wpdb->get_results("SELECT * FROM {$rz_user_profile_data} WHERE user_id = '$user_id'", ARRAY_A);
 
             if(count($get_profile_data) > 0){
                 $image_data = wp_upload_bits($unique_name, null, file_get_contents($image_tmp));
 
                 $image_url = $image_data['url'];
 
                 $wpdb->update($rz_user_profile_data, [
                         'profile_image' => $image_url
                 ], ['user_id' => $user_id]);
 
                 foreach($get_profile_data as $data){
                     unlink($data['profile_image']);
                 }
 
                 echo $image_url;
             }else{
                 $image_data = wp_upload_bits($unique_name, null, file_get_contents($image_tmp));
 
                 $image_url = $image_data['url'];
 
                 $wpdb->insert($rz_user_profile_data, [
                         'user_id' => $user_id,
                     'profile_image' => $image_url
                 ]);
 
                 echo $image_url;
             }
         }
     }
    die();
 });


 /**
 * update profile
 */
add_action('wp_ajax_imit_update_profile_data', function(){
    global $wpdb;
    $nonce = $_POST['nonce'];
    print_r($_POST);
    if(wp_verify_nonce($nonce, 'rz-profile-update-nonce')){
        $first_name = sanitize_text_field($_POST['first_name']);
        $last_name = sanitize_text_field($_POST['last_name']);
        $bio = sanitize_text_field($_POST['bio']);
        $cell = sanitize_text_field($_POST['cell']);
        $whatsapp_alert = sanitize_text_field($_POST['whatsapp_alert']);
        $country = sanitize_text_field($_POST['country']);
        $city = sanitize_text_field($_POST['city']);
        $language = sanitize_text_field($_POST['language']);
        $skill = sanitize_text_field($_POST['skill']);
        $user_id = get_current_user_id();
        $workplace_count = count($_POST['company']);
        $education_count = count($_POST['college']);
        if(empty($whatsapp_alert)){
            $alert = 'no';
        }else{
            $alert = $whatsapp_alert;
        }

        $rz_user_profile_data = $wpdb->prefix.'rz_user_profile_data';

        $get_all_profile_data = $wpdb->get_results("SELECT * FROM {$rz_user_profile_data} WHERE user_id = '$user_id'", ARRAY_A);

        wp_update_user([
            'ID' => $user_id, // this is the ID of the user you want to update.
            'first_name' => $first_name,
            'last_name' => $last_name,
        ]);

        if(count($get_all_profile_data) > 0){
            $wpdb->update($rz_user_profile_data, [
                   'occupation' => $bio,
                'phone_number' => $cell,
                'whatsapp_alert' => $alert,
                'country' => $country,
                'city' => $city,
                'languages' => $language,
                'skill' => $skill
            ], ['user_id' => $user_id]);
        }else{
            $wpdb->insert($rz_user_profile_data, [
                    'user_id' => $user_id,
                'occupation' => $bio,
                'phone_number' => $cell,
                'whatsapp_alert' => $alert,
                'country' => $country,
                'city' => $city,
                'languages' => $language,
                'skill' => $skill
            ]);
        }

        for($i = 0; $i < $workplace_count; $i++){
            $company = sanitize_text_field($_POST['company'][$i]);
            $position = sanitize_text_field($_POST['position'][$i]);
            $start_year = sanitize_text_field($_POST['work_start_year'][$i]);
            $end_year= sanitize_text_field($_POST['work_end_year'][$i]);
            $work_table = $wpdb->prefix.'rz_user_work';

            if(!empty($company) && !empty($position) && !empty($start_year) && !empty($end_year)){
                $wpdb->insert($work_table, [
                        'user_id' => $user_id,
                    'company' => $company,
                    'position' => $position,
                    'start_year' => $start_year,
                    'end_year' => $end_year
                ]);
            }
        }

        for($i = 0; $i < $education_count; $i++){
            $college = sanitize_text_field($_POST['college'][$i]);
            $concentrations = sanitize_text_field($_POST['concentrations'][$i]);
            $start_year = sanitize_text_field($_POST['edu_start_year'][$i]);
            $end_year= sanitize_text_field($_POST['edu_end_year'][$i]);
            $edu_table = $wpdb->prefix.'rz_user_education';

            if(!empty($college) && !empty($concentrations) && !empty($start_year) && !empty($end_year)){
                $wpdb->insert($edu_table, [
                    'user_id' => $user_id,
                    'college' => $college,
                    'concentrations' => $concentrations,
                    'start_year' => $start_year,
                    'end_year' => $end_year
                ]);
            }
        }

    }
    die();
});


/**
 * delete workplace
 */
add_action('wp_ajax_rz_delete_workplace_nonce', function(){
    global $wpdb;
     $nonce = $_POST['nonce'];
     if(wp_verify_nonce($nonce, 'rz-delete-workplace-nonce')){
         $workplace_id = sanitize_key($_POST['workplace_id']);
         $user_id = get_current_user_id();
         $workplace_table = $wpdb->prefix.'rz_user_work';
         if(!empty($workplace_id) && !empty($user_id)){
             $wpdb->delete($workplace_table, [
                     'id' => $workplace_id,
                 'user_id' => $user_id
             ]);
         }
     }
     die();
 });


 /**
 * delete education
 */
add_action('wp_ajax_rz_delete_education', function(){
    global $wpdb;
    $nonce = $_POST['nonce'];
    if(wp_verify_nonce($nonce, 'rz-delete-educational-nonce')){
        $education_id = sanitize_key($_POST['education_id']);
        $user_id = get_current_user_id();
        $education_table = $wpdb->prefix.'rz_user_education';
 
        if(!empty($education_id) && !empty($user_id)){
            $wpdb->delete($education_table, [
                    'id' => $education_id,
                'user_id' => $user_id
            ]);
        }
    }
    die();
 });

/**
 * user profile image for header
 */
add_shortcode('imit-user-profile-image', function(){
    ob_start();
    $user_id = get_current_user_id();
    $user_data = get_userdata( $user_id );
    if(is_user_logged_in(  )){
        ?>
        <div class="dropdown">
            <a href="#" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false"><img src="<?php getProfileImageById($user_id); ?>" alt="" id="imit-header-profile-image"></a>
            <ul class="dropdown-menu profile-menu mt-2" aria-labelledby="dropdownMenuButton1">
                <li class="profile-info">
                    <a href="<?php echo site_url(); ?>/user" class="dropdown-item d-flex flex-row justify-content-start align-items-center">
                        <div class="profile-image">
                            <img src="<?php getProfileImageById($user_id); ?>" alt="" id="imit-header-profile-image">
                        </div>
                        <div class="user-data ms-2">
                            <?php 
                            if(!empty($user_data->user_firstname) && !empty($user_data->user_lastname)){
                                echo '<h3 class="username m-0 imit-font fw-500 fz-20 text-dark">'.$user_data->user_firstname.' '.$user_data->user_lastname.'</h3>';
                            }else{
                                echo '<h3 class="username m-0 imit-font fw-500 fz-20 text-dark">'.$user_data->display_name.'</h3>';
                            }
                            ?>
                            <span class="imit-font fz-14 rz-secondary-color d-block"><?php echo $user_data->user_login; ?></span>
                        </div>
                    </a>
                </li>
                <li><a class="dropdown-item imit-font fz-14 text-dark" href="<?php echo wp_logout_url(site_url().'/login'); ?>"><i class="fas fa-sign-out-alt me-1"></i>Logout</a></li>
            </ul>
        </div>
    
        <?php
    }
return ob_get_clean();
});

/**
 * question asked
 */
add_action('wp_ajax_nopriv_rz_question_asked_posts', 'imit_rz_question_asked_posts');
add_action('wp_ajax_rz_question_asked_posts', 'imit_rz_question_asked_posts');

function imit_rz_question_asked_posts(){
    global $wpdb;
    $nonce = $_POST['nonce'];
    if(wp_verify_nonce($nonce, 'rz-asked-question-nonce')){
        $page_number = sanitize_key($_POST['page_num']);
        $user_id = sanitize_key( $_POST['user_id'] );
        $questions_data = new WP_Query([
                'author' => $user_id,
            'post_type' => 'rz_post_question',
            'posts_per_page' => 10,
            'paged' => $page_number
        ]);
        if($questions_data->have_posts()){
            while($questions_data->have_posts()):$questions_data->the_post();
            $post_id = get_the_ID();
            $answer_count = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_answers WHERE post_id = '$post_id' ORDER BY id DESC", ARRAY_A);
            ?>
            <li class="news-feed-list mt-3">
                <div class="card rz-br">
                    <div class="card-body p-0">
                        <div class="p-4">
                            <div class="created-at rz-secondary-color fz-14 imit-font">Asked on: <?php the_time(); ?></div>
                            <a href="<?php the_permalink(); ?>" class="question-title imit-font fw-500 text-decoration-none text-dark"><span class="rz-color mr-1">Q.</span> <?php the_title(); ?></a>
                            <div class="rz-br my-3">
                                <?php the_post_thumbnail('full', ['img-fluid']); ?>
                            </div>
                            <ul class="tags ps-0 d-flex flex-row justify-content-start align-items-center">
                                <?php
                                $tags = wp_get_post_terms(get_the_ID(), 'question_tags');
                                foreach($tags as $tag){
                                    echo '<li class="tag-list"><a href="'.get_term_link($tag->term_id, 'question_tags').'" class="tag-link imit-font fz-12 fw-500">'.$tag->name.'</a></li>';
                                }
                                ?>
                            </ul>
                        </div>
                        <?php 
                        $all_answers = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_answers WHERE post_id = '$post_id' AND status = '1'", ARRAY_A);
                        if(count($all_answers) > 0){
                            $get_first_answer = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}rz_answers WHERE post_id = '$post_id' ORDER BY id ASC LIMIT 1");
                            $get_profile_data = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}rz_user_profile_data WHERE user_id = '{$get_first_answer->user_id}'");
                            ?>
                            <div class="question-first-answer">
                                <div class="answer-info d-flex flex-row justify-content-between align-items-center py-2 px-4">
                                    <p class="mb-0 rz-color imit-font count-answer rz-color fw-500"><?php echo count($all_answers); ?> Answers</p>
                                    <a href="<?php the_permalink(); ?>" class="imit-font rz-color fw-500 fz-16">See all</a>
                                </div>
                                <ul class="answers py-3 px-4 mb-0">
                                <li class="answer-list list-unstyled">
                                    <div class="answer-header border-bottom-0 d-flex flex-row justify-content-between align-items-center">
                                        <div class="user-data d-flex flex-row justify-content-start align-items-center">
                                            <div class="profile-image">
                                                <img src="<?php getProfileImageById($get_first_answer->user_id); ?>" alt="">
                                            </div>
                                            <div class="user-info ms-1">
                                                <a href="<?php echo site_url().'/'.get_user_by('user_login', $get_first_answer->user_id); ?>" class="imit-font fz-16"><?php echo getUserNameById($get_first_answer->user_id); ?></a>
                                                <p class="mb-0 rz-secondary-color imit-font fz-12"><?php echo $get_profile_data->occupation; ?></p>
                                            </div>
                                        </div>
                                        <p class="rz-secondary-color imit-font fz-14 mb-0">Answered: 3pm April 25 ,2021</p>
                                    </div>
                                    <div class="answer-body mt-3 d-flex flex-row justify-content-start align-items-start">
                                        <?php 
                                        $current_user_id = get_current_user_id();
                                        $answer_id = $get_first_answer->id;
                                        $get_upvote = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_vote WHERE user_id = '$current_user_id' AND answer_id='$answer_id' AND vote_type='up-vote'", ARRAY_A);
                                        $get_downvote = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_vote WHERE user_id = '$current_user_id' AND answer_id='$answer_id' AND vote_type='down-vote'", ARRAY_A);

                                        $count_upvote = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_vote WHERE answer_id='$answer_id' AND vote_type='up-vote'", ARRAY_A);
                                        $count_downvote = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_vote WHERE answer_id='$answer_id' AND vote_type='down-vote'", ARRAY_A);
                                        ?>
                                        <ul class="body-left d-flex flex-column justify-content-start align-items-center ps-0 mb-0" id="vote<?php echo $answer_id; ?>">
                                            <li class="list-unstyled">
                                                <a href="#" class="vote d-block <?php if(count($get_upvote) > 0){echo 'active';} ?>" data-answer_id="<?php echo $answer_id; ?>" id="up-vote"><i class="fas fa-arrow-up"></i></a>
                                            </li>
                                            <li class="list-unstyled">
                                                <p class="counter fz-16 imit-font my-1 <?php if((count($count_upvote) - count($count_downvote)) < 0){echo 'text-danger';}else{echo 'text-success';} ?>" id="counter<?php echo $answer_id ; ?>"><?php echo count($count_upvote) - count($count_downvote); ?></p>
                                            </li>
                                            <li class="list-unstyled">
                                                <a href="#" class="vote d-block <?php if(count($get_downvote) > 0){echo 'active';} ?>" data-answer_id="<?php echo $answer_id; ?>" id="down-vote"><i class="fas fa-arrow-down"></i></a>
                                            </li>
                                        </ul>
                                        <div class="answer-details ms-3">
                                            <?php 
                                            if(str_word_count($get_first_answer->answer_text) > 20){
                                                ?>
                                                <p class="imit-font fz-16 answer-text" id="answer-text"><?php echo wp_trim_words($get_first_answer->answer_text, 20, ' ...'); ?></p>
                                                <a href="#" class="rz-color fw-500 imit-font fz-16" data-answer_id="<?php echo $get_first_answer->id; ?>" id="read-more-answer">Read More</a>
                                                <p class="imit-font fz-16 answer-text" id="answer-text<?php echo $get_first_answer->id; ?>" style="display: none;"><?php echo $get_first_answer->answer_text; ?></p>
                                                <?php
                                            }else{
                                                ?>
                                                <p class="imit-font fz-16 answer-text"><?php echo $get_first_answer->answer_text; ?></p>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                            </div>
                            <?php
                        }
                    ?>
                    </div>
                    <div class="card-footer d-flex flex-row justify-content-between align-items-center p-3 border-top-0">
                        <div class="views text-dark fz-14">
                            <i class="fas fa-eye"></i>
                            <span class="counter imit-font fw-500"><span class="counter"><?php echo getPostViews(get_the_ID()) ?></span></span>
                        </div>
                        <div class="other text-dark d-flex flex-row justify-content-end align-items-center">
                            <!-- <a href="#" class="fz-14 text-dark me-2"><i class="fas fa-share"></i></a> -->
                            <div class="dropdown">
                                <a class="text-dark fz-16" href="#" role="button" id="more-option-feed" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-ellipsis-h"></i>
                                </a>

                                <ul class="dropdown-menu" aria-labelledby="more-option-feed">
                                    <li><a class="dropdown-item imit-font fz-14 text-dark" href="#">Action</a></li>
                                    <li><a class="dropdown-item imit-font fz-14 text-dark" href="#">Another action</a></li>
                                    <li><a class="dropdown-item imit-font fz-14 text-dark" href="#">Something else here</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
            <?php endwhile;
        }else{
            exit('profileFeedReachmax');
        }
    }
    die();
}


/**
 * get profile all asked questions
 */
add_action('wp_ajax_nopriv_rz_question_answered_posts', 'rz_answered_questions');
add_action('wp_ajax_rz_question_answered_posts', 'rz_answered_questions');

function rz_answered_questions(){
    global $wpdb;
    $nonce = $_POST['nonce'];
    if(wp_verify_nonce( $nonce, 'rz-answered-question-nonce' )){
        $page_number = sanitize_key( $_POST['page_num'] );
        $user_id = sanitize_key( $_POST['user_id'] );

        $answers_post = $wpdb->get_results("SELECT {$wpdb->prefix}rz_answers.post_id FROM {$wpdb->prefix}rz_answers INNER JOIN {$wpdb->prefix}posts ON {$wpdb->prefix}rz_answers.post_id = {$wpdb->prefix}posts.ID WHERE {$wpdb->prefix}rz_answers.user_id = '{$user_id}' AND {$wpdb->prefix}posts.post_type = 'rz_post_question'", ARRAY_A);
        $post_id = [];
        foreach($answers_post as $aws_p){
            array_push($post_id, $aws_p['post_id']);
        }
        $answer_data = new WP_Query([
            'author' => $user_id,
            'post_type' => 'rz_post_question',
            'posts_per_page' => 10,
            'post__in' => $post_id,
            'paged' => $page_number
        ]);
        if($answer_data->have_posts()){
            while($answer_data->have_posts()):$answer_data->the_post();
            $post_id = get_the_ID();
            $answer_count = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_answers WHERE post_id = '$post_id' ORDER BY id DESC", ARRAY_A);
            ?>
            <li class="news-feed-list mt-3">
                <div class="card rz-br">
                    <div class="card-body p-0">
                        <div class="p-4">
                            <div class="created-at rz-secondary-color fz-14 imit-font">Asked on: <?php the_time(); ?></div>
                            <a href="<?php the_permalink(); ?>" class="question-title imit-font fw-500 text-decoration-none text-dark"><span class="rz-color mr-1">Q.</span> <?php the_title(); ?></a>
                            <div class="rz-br my-3">
                                <?php the_post_thumbnail('full', ['img-fluid']); ?>
                            </div>
                            <ul class="tags ps-0 d-flex flex-row justify-content-start align-items-center">
                                <?php
                                $tags = wp_get_post_terms(get_the_ID(), 'question_tags');
                                foreach($tags as $tag){
                                    echo '<li class="tag-list"><a href="'.get_term_link($tag->term_id, 'question_tags').'" class="tag-link imit-font fz-12 fw-500">'.$tag->name.'</a></li>';
                                }
                                ?>
                            </ul>
                        </div>
                        <?php 
                        $all_answers = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_answers WHERE post_id = '$post_id' AND status = '1'", ARRAY_A);
                        if(count($all_answers) > 0){
                            $get_first_answer = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}rz_answers WHERE post_id = '$post_id' ORDER BY id ASC LIMIT 1");
                            $get_profile_data = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}rz_user_profile_data WHERE user_id = '{$get_first_answer->user_id}'");
                            ?>
                            <div class="question-first-answer">
                                <div class="answer-info d-flex flex-row justify-content-between align-items-center py-2 px-4">
                                    <p class="mb-0 rz-color imit-font count-answer rz-color fw-500"><?php echo count($all_answers); ?> Answers</p>
                                    <a href="<?php the_permalink(); ?>" class="imit-font rz-color fw-500 fz-16">See all</a>
                                </div>
                                <ul class="answers py-3 px-4 mb-0">
                                <li class="answer-list list-unstyled">
                                    <div class="answer-header border-bottom-0 d-flex flex-row justify-content-between align-items-center">
                                        <div class="user-data d-flex flex-row justify-content-start align-items-center">
                                            <div class="profile-image">
                                                <img src="<?php getProfileImageById($get_first_answer->user_id); ?>" alt="">
                                            </div>
                                            <div class="user-info ms-1">
                                                <a href="<?php echo site_url().'/'.get_user_by('user_login', $get_first_answer->user_id); ?>" class="imit-font fz-16"><?php echo getUserNameById($get_first_answer->user_id); ?></a>
                                                <p class="mb-0 rz-secondary-color imit-font fz-12"><?php echo $get_profile_data->occupation; ?></p>
                                            </div>
                                        </div>
                                        <p class="rz-secondary-color imit-font fz-14 mb-0">Answered: 3pm April 25 ,2021</p>
                                    </div>
                                    <div class="answer-body mt-3 d-flex flex-row justify-content-start align-items-start">
                                        <?php 
                                        $current_user_id = get_current_user_id();
                                        $answer_id = $get_first_answer->id;
                                        $get_upvote = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_vote WHERE user_id = '$current_user_id' AND answer_id='$answer_id' AND vote_type='up-vote'", ARRAY_A);
                                        $get_downvote = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_vote WHERE user_id = '$current_user_id' AND answer_id='$answer_id' AND vote_type='down-vote'", ARRAY_A);

                                        $count_upvote = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_vote WHERE answer_id='$answer_id' AND vote_type='up-vote'", ARRAY_A);
                                        $count_downvote = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_vote WHERE answer_id='$answer_id' AND vote_type='down-vote'", ARRAY_A);
                                        ?>
                                        <ul class="body-left d-flex flex-column justify-content-start align-items-center ps-0 mb-0" id="vote<?php echo $answer_id; ?>">
                                            <li class="list-unstyled">
                                                <a href="#" class="vote d-block <?php if(count($get_upvote) > 0){echo 'active';} ?>" data-answer_id="<?php echo $answer_id; ?>" id="up-vote"><i class="fas fa-arrow-up"></i></a>
                                            </li>
                                            <li class="list-unstyled">
                                                <p class="counter fz-16 imit-font my-1 <?php if((count($count_upvote) - count($count_downvote)) < 0){echo 'text-danger';}else{echo 'text-success';} ?>" id="counter<?php echo $answer_id ; ?>"><?php echo count($count_upvote) - count($count_downvote); ?></p>
                                            </li>
                                            <li class="list-unstyled">
                                                <a href="#" class="vote d-block <?php if(count($get_downvote) > 0){echo 'active';} ?>" data-answer_id="<?php echo $answer_id; ?>" id="down-vote"><i class="fas fa-arrow-down"></i></a>
                                            </li>
                                        </ul>
                                        <div class="answer-details ms-3">
                                            <?php 
                                            if(str_word_count($get_first_answer->answer_text) > 20){
                                                ?>
                                                <p class="imit-font fz-16 answer-text" id="answer-text"><?php echo wp_trim_words($get_first_answer->answer_text, 20, ' ...'); ?></p>
                                                <a href="#" class="rz-color fw-500 imit-font fz-16" data-answer_id="<?php echo $get_first_answer->id; ?>" id="read-more-answer">Read More</a>
                                                <p class="imit-font fz-16 answer-text" id="answer-text<?php echo $get_first_answer->id; ?>" style="display: none;"><?php echo $get_first_answer->answer_text; ?></p>
                                                <?php
                                            }else{
                                                ?>
                                                <p class="imit-font fz-16 answer-text"><?php echo $get_first_answer->answer_text; ?></p>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                            </div>
                            <?php
                        }
                    ?>
                    </div>
                    <div class="card-footer d-flex flex-row justify-content-between align-items-center p-3 border-top-0">
                        <div class="views text-dark fz-14">
                            <i class="fas fa-eye"></i>
                            <span class="counter imit-font fw-500"><span class="counter"><?php echo getPostViews(get_the_ID()) ?></span></span>
                        </div>
                        <div class="other text-dark d-flex flex-row justify-content-end align-items-center">
                            <!-- <a href="#" class="fz-14 text-dark me-2"><i class="fas fa-share"></i></a> -->
                            <div class="dropdown">
                                <a class="text-dark fz-16" href="#" role="button" id="more-option-feed" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-ellipsis-h"></i>
                                </a>

                                <ul class="dropdown-menu" aria-labelledby="more-option-feed">
                                    <li><a class="dropdown-item imit-font fz-14 text-dark" href="#">Action</a></li>
                                    <li><a class="dropdown-item imit-font fz-14 text-dark" href="#">Another action</a></li>
                                    <li><a class="dropdown-item imit-font fz-14 text-dark" href="#">Something else here</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
            <?php endwhile;
        }else{
            exit('answeredFeedReachmax');
        }
    }
    die();
}

/**
 * voted questions
 */
add_action('wp_ajax_nopriv_rz_voted_questions_posts', 'rz_voted_questions_data');
add_action('wp_ajax_rz_voted_questions_posts', 'rz_voted_questions_data');


function rz_voted_questions_data(){
    global $wpdb;
    $nonce = $_POST['nonce'];
    if(wp_verify_nonce( $nonce, 'rz-voted-questions-nonce' )){
        $page_number = sanitize_key( $_POST['page_num'] );
        $user_id = sanitize_key( $_POST['user_id'] );
        $vote_post_ids = $wpdb->get_results("SELECT DISTINCT({$wpdb->prefix}posts.ID) FROM {$wpdb->prefix}rz_vote INNER JOIN {$wpdb->prefix}rz_answers ON {$wpdb->prefix}rz_vote.answer_id = {$wpdb->prefix}rz_answers.id INNER JOIN {$wpdb->prefix}posts ON {$wpdb->prefix}posts.ID = {$wpdb->prefix}rz_answers.post_id WHERE {$wpdb->prefix}rz_vote.user_id = '{$user_id}' AND {$wpdb->prefix}posts.post_type = 'rz_post_question'", ARRAY_A);
        $v_post_id= [];
        foreach($vote_post_ids as $vp_id){
            array_push($v_post_id, $vp_id['ID']);
        }
        $vote_post_data = new WP_Query([
            'author' => $user_id,
            'post_type' => 'rz_post_question',
            'posts_per_page' => 10,
            'post__in' => $v_post_id,
            'paged' => $page_number
        ]);
        if($vote_post_data->have_posts()){
            while($vote_post_data->have_posts()):$vote_post_data->the_post();
            $post_id = get_the_ID();
            $answer_count = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_answers WHERE post_id = '$post_id' ORDER BY id DESC", ARRAY_A);
            ?>
            <li class="news-feed-list mt-3">
                <div class="card rz-br">
                    <div class="card-body p-0">
                        <div class="p-4">
                            <div class="created-at rz-secondary-color fz-14 imit-font">Asked on: <?php the_time(); ?></div>
                            <a href="<?php the_permalink(); ?>" class="question-title imit-font fw-500 text-decoration-none text-dark"><span class="rz-color mr-1">Q.</span> <?php the_title(); ?></a>
                            <div class="rz-br my-3">
                                <?php the_post_thumbnail('full', ['img-fluid']); ?>
                            </div>
                            <ul class="tags ps-0 d-flex flex-row justify-content-start align-items-center">
                                <?php
                                $tags = wp_get_post_terms(get_the_ID(), 'question_tags');
                                foreach($tags as $tag){
                                    echo '<li class="tag-list"><a href="'.get_term_link($tag->term_id, 'question_tags').'" class="tag-link imit-font fz-12 fw-500">'.$tag->name.'</a></li>';
                                }
                                ?>
                            </ul>
                        </div>
                        <?php 
                        $all_answers = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_answers WHERE post_id = '$post_id' AND status = '1'", ARRAY_A);
                        if(count($all_answers) > 0){
                            $get_first_answer = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}rz_answers WHERE post_id = '$post_id' ORDER BY id ASC LIMIT 1");
                            $get_profile_data = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}rz_user_profile_data WHERE user_id = '{$get_first_answer->user_id}'");
                            ?>
                            <div class="question-first-answer">
                                <div class="answer-info d-flex flex-row justify-content-between align-items-center py-2 px-4">
                                    <p class="mb-0 rz-color imit-font count-answer rz-color fw-500"><?php echo count($all_answers); ?> Answers</p>
                                    <a href="<?php the_permalink(); ?>" class="imit-font rz-color fw-500 fz-16">See all</a>
                                </div>
                                <ul class="answers py-3 px-4 mb-0">
                                <li class="answer-list list-unstyled">
                                    <div class="answer-header border-bottom-0 d-flex flex-row justify-content-between align-items-center">
                                        <div class="user-data d-flex flex-row justify-content-start align-items-center">
                                            <div class="profile-image">
                                                <img src="<?php getProfileImageById($get_first_answer->user_id); ?>" alt="">
                                            </div>
                                            <div class="user-info ms-1">
                                                <a href="<?php echo site_url().'/'.get_user_by('user_login', $get_first_answer->user_id); ?>" class="imit-font fz-16"><?php echo getUserNameById($get_first_answer->user_id); ?></a>
                                                <p class="mb-0 rz-secondary-color imit-font fz-12"><?php echo $get_profile_data->occupation; ?></p>
                                            </div>
                                        </div>
                                        <p class="rz-secondary-color imit-font fz-14 mb-0">Answered: 3pm April 25 ,2021</p>
                                    </div>
                                    <div class="answer-body mt-3 d-flex flex-row justify-content-start align-items-start">
                                        <?php 
                                        $current_user_id = get_current_user_id();
                                        $answer_id = $get_first_answer->id;
                                        $get_upvote = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_vote WHERE user_id = '$current_user_id' AND answer_id='$answer_id' AND vote_type='up-vote'", ARRAY_A);
                                        $get_downvote = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_vote WHERE user_id = '$current_user_id' AND answer_id='$answer_id' AND vote_type='down-vote'", ARRAY_A);

                                        $count_upvote = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_vote WHERE answer_id='$answer_id' AND vote_type='up-vote'", ARRAY_A);
                                        $count_downvote = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_vote WHERE answer_id='$answer_id' AND vote_type='down-vote'", ARRAY_A);
                                        ?>
                                        <ul class="body-left d-flex flex-column justify-content-start align-items-center ps-0 mb-0" id="vote<?php echo $answer_id; ?>">
                                            <li class="list-unstyled">
                                                <a href="#" class="vote d-block <?php if(count($get_upvote) > 0){echo 'active';} ?>" data-answer_id="<?php echo $answer_id; ?>" id="up-vote"><i class="fas fa-arrow-up"></i></a>
                                            </li>
                                            <li class="list-unstyled">
                                                <p class="counter fz-16 imit-font my-1 <?php if((count($count_upvote) - count($count_downvote)) < 0){echo 'text-danger';}else{echo 'text-success';} ?>" id="counter<?php echo $answer_id ; ?>"><?php echo count($count_upvote) - count($count_downvote); ?></p>
                                            </li>
                                            <li class="list-unstyled">
                                                <a href="#" class="vote d-block <?php if(count($get_downvote) > 0){echo 'active';} ?>" data-answer_id="<?php echo $answer_id; ?>" id="down-vote"><i class="fas fa-arrow-down"></i></a>
                                            </li>
                                        </ul>
                                        <div class="answer-details ms-3">
                                            <?php 
                                            if(str_word_count($get_first_answer->answer_text) > 20){
                                                ?>
                                                <p class="imit-font fz-16 answer-text" id="answer-text"><?php echo wp_trim_words($get_first_answer->answer_text, 20, ' ...'); ?></p>
                                                <a href="#" class="rz-color fw-500 imit-font fz-16" data-answer_id="<?php echo $get_first_answer->id; ?>" id="read-more-answer">Read More</a>
                                                <p class="imit-font fz-16 answer-text" id="answer-text<?php echo $get_first_answer->id; ?>" style="display: none;"><?php echo $get_first_answer->answer_text; ?></p>
                                                <?php
                                            }else{
                                                ?>
                                                <p class="imit-font fz-16 answer-text"><?php echo $get_first_answer->answer_text; ?></p>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                            </div>
                            <?php
                        }
                    ?>
                    </div>
                    <div class="card-footer d-flex flex-row justify-content-between align-items-center border-top-0 p-3">
                        <div class="views text-dark fz-14">
                            <i class="fas fa-eye"></i>
                            <span class="counter imit-font fw-500"><span class="counter"><?php echo getPostViews(get_the_ID()) ?></span></span>
                        </div>
                        <div class="other text-dark d-flex flex-row justify-content-end align-items-center">
                            <!-- <a href="#" class="fz-14 text-dark me-2"><i class="fas fa-share"></i></a> -->
                            <div class="dropdown">
                                <a class="text-dark fz-16" href="#" role="button" id="more-option-feed" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-ellipsis-h"></i>
                                </a>

                                <ul class="dropdown-menu" aria-labelledby="more-option-feed">
                                    <li><a class="dropdown-item imit-font fz-14 text-dark" href="#">Action</a></li>
                                    <li><a class="dropdown-item imit-font fz-14 text-dark" href="#">Another action</a></li>
                                    <li><a class="dropdown-item imit-font fz-14 text-dark" href="#">Something else here</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
            <?php endwhile;
        }else{
            exit('profileVOtedQuestionsReachmax');
        }
    }
    die();
}


/**
 * get all commented questions
 */
add_action('wp_ajax_nopriv_rz_commented_questions_posts', 'rz_all_commented_questions');
add_action('wp_ajax_rz_commented_questions_posts', 'rz_all_commented_questions');

function rz_all_commented_questions(){
    global $wpdb;
    $nonce = $_POST['nonce'];
    if(wp_verify_nonce( $nonce, 'rz-commented-questions-nonce' )){
        $page_number = sanitize_key( $_POST['page_num'] );
        $user_id = sanitize_key($_POST['user_id']);
        $comment_post_ids = $wpdb->get_results("SELECT {$wpdb->prefix}posts.ID FROM {$wpdb->prefix}posts INNER JOIN {$wpdb->prefix}rz_answers ON {$wpdb->prefix}rz_answers.post_id = {$wpdb->prefix}posts.ID INNER JOIN {$wpdb->prefix}rz_answer_comments ON {$wpdb->prefix}rz_answer_comments.answer_id = {$wpdb->prefix}rz_answers.id WHERE {$wpdb->prefix}rz_answer_comments.user_id = '$user_id' AND {$wpdb->prefix}posts.post_type = 'rz_post_question'", ARRAY_A);
        $c_post_id= [];
        foreach($comment_post_ids as $cp_id){
            array_push($c_post_id, $cp_id['ID']);
        }
        $vote_post_data = new WP_Query([
            'author' => $user_id,
            'post_type' => 'rz_post_question',
            'posts_per_page' => 10,
            'post__in' => $c_post_id,
            'paged' => $page_number
        ]);
        if($vote_post_data->have_posts()){
            while($vote_post_data->have_posts()):$vote_post_data->the_post();
                $post_id = get_the_ID();
                $answer_count = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_answers WHERE post_id = '$post_id' ORDER BY id DESC", ARRAY_A);
                ?>
                <li class="news-feed-list mt-3">
                    <div class="card rz-br">
                        <div class="card-body p-0">
                            <div class="p-4">
                                <div class="created-at rz-secondary-color fz-14 imit-font">Asked on: <?php the_time(); ?></div>
                                <a href="<?php the_permalink(); ?>" class="question-title imit-font fw-500 text-decoration-none text-dark"><span class="rz-color mr-1">Q.</span> <?php the_title(); ?></a>
                                <div class="rz-br my-3">
                                    <?php the_post_thumbnail('full', ['img-fluid']); ?>
                                </div>
                                <ul class="tags ps-0 d-flex flex-row justify-content-start align-items-center">
                                    <?php
                                    $tags = wp_get_post_terms(get_the_ID(), 'question_tags');
                                    foreach($tags as $tag){
                                        echo '<li class="tag-list"><a href="'.get_term_link($tag->term_id, 'question_tags').'" class="tag-link imit-font fz-12 fw-500">'.$tag->name.'</a></li>';
                                    }
                                    ?>
                                </ul>
                            </div>
                            <?php 
                            $all_answers = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_answers WHERE post_id = '$post_id' AND status = '1'", ARRAY_A);
                            if(count($all_answers) > 0){
                                $get_first_answer = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}rz_answers WHERE post_id = '$post_id' ORDER BY id ASC LIMIT 1");
                                $get_profile_data = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}rz_user_profile_data WHERE user_id = '{$get_first_answer->user_id}'");
                                ?>
                                <div class="question-first-answer">
                                    <div class="answer-info d-flex flex-row justify-content-between align-items-center py-2 px-4">
                                        <p class="mb-0 rz-color imit-font count-answer rz-color fw-500"><?php echo count($all_answers); ?> Answers</p>
                                        <a href="<?php the_permalink(); ?>" class="imit-font rz-color fw-500 fz-16">See all</a>
                                    </div>
                                    <ul class="answers py-3 px-4 mb-0">
                                    <li class="answer-list list-unstyled">
                                        <div class="answer-header border-bottom-0 d-flex flex-row justify-content-between align-items-center">
                                            <div class="user-data d-flex flex-row justify-content-start align-items-center">
                                                <div class="profile-image">
                                                    <img src="<?php getProfileImageById($get_first_answer->user_id); ?>" alt="">
                                                </div>
                                                <div class="user-info ms-1">
                                                    <a href="<?php echo site_url().'/'.get_user_by('user_login', $get_first_answer->user_id); ?>" class="imit-font fz-16"><?php echo getUserNameById($get_first_answer->user_id); ?></a>
                                                    <p class="mb-0 rz-secondary-color imit-font fz-12"><?php echo $get_profile_data->occupation; ?></p>
                                                </div>
                                            </div>
                                            <p class="rz-secondary-color imit-font fz-14 mb-0">Answered: 3pm April 25 ,2021</p>
                                        </div>
                                        <div class="answer-body mt-3 d-flex flex-row justify-content-start align-items-start">
                                            <?php 
                                            $current_user_id = get_current_user_id();
                                            $answer_id = $get_first_answer->id;
                                            $get_upvote = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_vote WHERE user_id = '$current_user_id' AND answer_id='$answer_id' AND vote_type='up-vote'", ARRAY_A);
                                            $get_downvote = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_vote WHERE user_id = '$current_user_id' AND answer_id='$answer_id' AND vote_type='down-vote'", ARRAY_A);

                                            $count_upvote = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_vote WHERE answer_id='$answer_id' AND vote_type='up-vote'", ARRAY_A);
                                            $count_downvote = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_vote WHERE answer_id='$answer_id' AND vote_type='down-vote'", ARRAY_A);
                                            ?>
                                            <ul class="body-left d-flex flex-column justify-content-start align-items-center ps-0 mb-0" id="vote<?php echo $answer_id; ?>">
                                                <li class="list-unstyled">
                                                    <a href="#" class="vote d-block <?php if(count($get_upvote) > 0){echo 'active';} ?>" data-answer_id="<?php echo $answer_id; ?>" id="up-vote"><i class="fas fa-arrow-up"></i></a>
                                                </li>
                                                <li class="list-unstyled">
                                                    <p class="counter fz-16 imit-font my-1 <?php if((count($count_upvote) - count($count_downvote)) < 0){echo 'text-danger';}else{echo 'text-success';} ?>" id="counter<?php echo $answer_id ; ?>"><?php echo count($count_upvote) - count($count_downvote); ?></p>
                                                </li>
                                                <li class="list-unstyled">
                                                    <a href="#" class="vote d-block <?php if(count($get_downvote) > 0){echo 'active';} ?>" data-answer_id="<?php echo $answer_id; ?>" id="down-vote"><i class="fas fa-arrow-down"></i></a>
                                                </li>
                                            </ul>
                                            <div class="answer-details ms-3">
                                                <?php 
                                                if(str_word_count($get_first_answer->answer_text) > 20){
                                                    ?>
                                                    <p class="imit-font fz-16 answer-text" id="answer-text"><?php echo wp_trim_words($get_first_answer->answer_text, 20, ' ...'); ?></p>
                                                    <a href="#" class="rz-color fw-500 imit-font fz-16" data-answer_id="<?php echo $get_first_answer->id; ?>" id="read-more-answer">Read More</a>
                                                    <p class="imit-font fz-16 answer-text" id="answer-text<?php echo $get_first_answer->id; ?>" style="display: none;"><?php echo $get_first_answer->answer_text; ?></p>
                                                    <?php
                                                }else{
                                                    ?>
                                                    <p class="imit-font fz-16 answer-text"><?php echo $get_first_answer->answer_text; ?></p>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                                </div>
                                <?php
                            }
                        ?>
                        </div>
                        <div class="card-footer d-flex flex-row justify-content-between align-items-center border-top-0 p-3">
                            <div class="views text-dark fz-14">
                                <i class="fas fa-eye"></i>
                                <span class="counter imit-font fw-500"><span class="counter"><?php echo getPostViews(get_the_ID()) ?></span></span>
                            </div>
                            <div class="other text-dark d-flex flex-row justify-content-end align-items-center">
                                <a href="#" class="fz-14 text-dark me-2"><i class="fas fa-share"></i></a>
                                <div class="dropdown">
                                    <a class="text-dark fz-16" href="#" role="button" id="more-option-feed" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-ellipsis-h"></i>
                                    </a>

                                    <ul class="dropdown-menu" aria-labelledby="more-option-feed">
                                        <li><a class="dropdown-item imit-font fz-14 text-dark" href="#">Action</a></li>
                                        <li><a class="dropdown-item imit-font fz-14 text-dark" href="#">Another action</a></li>
                                        <li><a class="dropdown-item imit-font fz-14 text-dark" href="#">Something else here</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            <?php endwhile;
        }else{
            exit('profileCommentedQeustionsReachmax');
        }
    }
    die();
}


/**
 * get all following users
 */
add_action('wp_ajax_nopriv_rz_following_users_posts', 'get_all_following_users');
add_action('wp_ajax_rz_following_users_posts', 'get_all_following_users');

function get_all_following_users(){
    global $wpdb;
    $nonce = $_POST['nonce'];
    if(wp_verify_nonce( $nonce, 'rz-following-user-nonce' )){
        $start = sanitize_key( $_POST['start'] );
        $limit = sanitize_key( $_POST['limit'] );
        $user_id = sanitize_key($_POST['user_id']);
 
        $get_all_following_users = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_followers WHERE sender_id = '$user_id' OR receiver_id = '$user_id' ORDER BY ID DESC LIMIT $start, $limit", ARRAY_A);
        
        if(count($get_all_following_users) > 0){
            foreach($get_all_following_users as $follower){
                if($follower['sender_id'] == $user_id){
                    $follower_id = $follower['receiver_id'];
                }else{
                    $follower_id = $follower['sender_id'];
                }
                $user_data = get_userdata($follower_id);
    
                $following_profile_data = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}rz_user_profile_data WHERE user_id = '$follower_id'");
                ?>
                <li class="col-md-6 list-unstyled mt-3">
                    <div class="card rz-br rz-border">
                        <div class="card-body rz-br rz-border p-0">
                            <div class="d-flex flex-row justify-content-start align-items-center py-3 px-4 pb-0">
                                <div class="user-avatar rounded-circle" style="width: 42px;height: 42px;">
                                    <?php
                                    if(!empty($following_profile_data->profile_image)){
                                        ?>
                                        <img id="profile_image_show" src="<?php echo $following_profile_data->profile_image; ?>" alt="" class="rounded-circle" style="width: 42px;height: 42px;">
                                        <?php
                                    }else{
                                        ?>
                                        <img id="profile_image_show" src="<?php echo plugins_url('images/avatar.png', __FILE__); ?>" alt="" class="rounded-circle" style="width: 42px;height: 42px;">
                                        <?php
                                    }
                                    ?>
                                </div>
                                <div class="ms-2">
                                    <a href="<?php echo site_url().'/user/'.$user_data->user_login; ?>" class="d-block username fz-16 text-dark fw-500 imit-font text-decoration-none" id="name<?php echo $follower_id; ?>"><?php
                                        if(!empty($user_data->user_firstname) && !empty($user_data->user_lastname)){
                                            echo $user_data->user_firstname.' '.$user_data->user_lastname;
                                        }else{
                                            echo $user_data->display_name;
                                        }
                                        ?></a>
                                    <?php
    
                                    if(!empty($following_profile_data->occupation)){
                                        ?>
                                        <p class="mb-0 designation rz-secondary-color fz-12 imit-font"><?php echo $following_profile_data->occupation; ?></p>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                            <ul class="user-profile-info mb-0 py-3 px-4 pb-0">
                                <?php
                                $get_all_following = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_followers WHERE sender_id = '$follower_id'", ARRAY_A);
                                $get_all_followers = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_followers WHERE receiver_id = '$follower_id'", ARRAY_A);
                                $question_count = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}posts WHERE post_author = '$follower_id' AND post_type='rz_post_question'", ARRAY_A);
                                ?>
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
    
                            <div class="profile-action d-flex flex-row justify-content-between align-items-center py-3 px-4">
                                <?php if(is_user_logged_in()){
                                    if(get_current_user_id() != $follower_id){
                                        $sender_id = get_current_user_id();
                                        $receiver_id = $follower_id;
                                        $get_all_followers = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_followers WHERE (sender_id = '$sender_id' AND receiver_id = '$receiver_id') OR (sender_id = '$receiver_id' AND receiver_id = '$sender_id')", ARRAY_A);
                                        if(count($get_all_followers) > 0){
                                            ?>
                                            <a href="#" class="btn btn-secondary d-block text-center mt-2 imit-font fz-14 text-white w-50 me-1" id="rz-follow" data-receiver_id="<?php echo $follower_id; ?>"><i class="fas fa-minus-circle me-2"></i>Unfollow</a>
                                            <?php
                                        }else{
                                            ?>
                                            <a href="#" class="btn follow d-block text-center mt-3 imit-font fz-14 text-white mt-0 w-50 me-1" id="rz-follow" data-receiver_id="<?php echo $follower_id; ?>"><i class="fas fa-plus-circle me-2"></i>Follow</a>
                                            <?php
                                        }
                                        ?>
                                        <a href="#" class="btn message d-block text-center mt-2 imit-font fz-14 w-50 ms-1" data-user_id="<?php echo $receiver_id; ?>" id="send-message-button"><i class="fas fa-comments me-2"></i>Message</a>
                                        <?php
                                    }else{
                                        ?>
                                        <a href="#" class="btn follow d-block text-center mt-2 imit-font fz-14 text-white w-50 me-1" data-bs-toggle="modal" data-bs-target="#rz-profile-edit-modal"><i class="fas fa-edit me-2"></i>Edit Profile</a>
                                        <a href="#" class="btn message d-block text-center mt-2 imit-font fz-14 w-50 ms-1">Search Pad</a>
                                        <?php
                                    }
                                }?>
                            </div>
                            <?php 
                            $get_all_user_data = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}rz_user_profile_data WHERE user_id = '$follower_id'");
                            if(!empty($get_all_user_data)){
                                ?>
                                <ul class="about mb-0 py-3 px-4">
                                    <?php
                                    $all_workplaces_data = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_user_work WHERE user_id = '$follower_id'", ARRAY_A);
                                    foreach($all_workplaces_data as $workplace){
                                        ?>
                                        <li class="about-list rz-secondary-color list-unstyled my-2">
                                            <i class="fas fa-briefcase mr-1"></i>
                                            <span class="imit-font fz-14"><?php echo $workplace['position']; ?> at <strong class="rz-color"><?php echo $workplace['company']; ?></strong> <?php echo $workplace['start_year']; ?> - <?php echo $workplace['end_year']; ?></span>
                                        </li>
                                        <?php
                                    }
    
                                    $all_education_data = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_user_education WHERE user_id = '$follower_id'", ARRAY_A);
                                    foreach($all_education_data as $education){
                                        ?>
                                        <li class="about-list rz-secondary-color list-unstyled my-2">
                                            <i class="fas fa-graduation-cap mr-1"></i>
                                            <span class="imit-font fz-14"><?php echo $education['concentrations']; ?> at <strong class="rz-color"><?php echo $education['college']; ?></strong> <?php echo $education['start_year']; ?> - <?php echo $education['end_year']; ?></span>
                                        </li>
                                        <?php
                                    }
                                    ?>
                                    <?php 
                                    if(!empty($following_profile_data -> country) && !empty($following_profile_data->city)){
                                        ?>
                                        <li class="about-list rz-secondary-color list-unstyled my-2">
                                            <i class="fas fa-map-marker-alt mr-1"></i>
                                            <span class="imit-font fz-14">Lives in <strong class="rz-color"><?php echo $following_profile_data->city; ?>, <?php echo $following_profile_data->country; ?></strong></span>
                                        </li>
                                        <?php
                                    }
                                    ?>
            <!--                                                <li class="about-list rz-secondary-color list-unstyled my-2">-->
            <!--                                                    <i class="fas fa-eye mr-1"></i>-->
            <!--                                                    <span class="imit-font fz-14">85.7k content views 2.5k this month</span>-->
            <!--                                                </li>-->
                                    <?php if(!empty($following_profile_data->languages)){
                                        ?>
                                        <li class="about-list rz-secondary-color list-unstyled my-2">
                                            <i class="fas fa-globe mr-1"></i>
                                            <span class="imit-font fz-14">Knows <strong class="rz-color">
                                                <?php
                                                $lan_exp = explode(',', $following_profile_data->languages);
    
                                                echo implode(' - ', $lan_exp);
                                                ?>
                                            </strong></span>
                                        </li>
                                        <?php
                                    }?>
                                </ul>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </li>
                    <?php
            }
        }else{
            exit('profileFollowingUserReachmax');
        }
    }
    die();
}


/**
 * get user all dairy
 */
add_action('wp_ajax_nopriv_rz_user_dairy_posts', 'rz_user_all_dairy_posts');
add_action('wp_ajax_rz_user_dairy_posts', 'rz_user_all_dairy_posts');


function rz_user_all_dairy_posts(){
    global $wpdb;
    $nonce = $_POST['nonce'];
    if(wp_verify_nonce( $nonce, 'rz-user-dairy-nonce' )){
        $page_number = sanitize_key( $_POST['page_num'] );
        $user_id = sanitize_key( $_POST['user_id'] );


        if(is_user_logged_in() && get_current_user_id() == $user_id){
            $dairy_data = new WP_Query([
                'author' => $user_id,
                'post_type' => 'rz_dairy',
                'posts_per_page' => 10,
                'paged' => $page_number
            ]);
        }else{
            $dairy_data = new WP_Query([
                'author' => $user_id,
                'post_type' => 'rz_dairy',
                'posts_per_page' => 10,
                'post_status' => 'publish',
                'paged' => $page_number
            ]);
        }

        if($dairy_data->have_posts(  )){
            while($dairy_data->have_posts()):$dairy_data->the_post();
                ?>
                <li class="dairy-list list-unstyled rz-border rz-br p-3 mt-3 bg-white" id="dairy<?php echo get_the_ID(); ?>">
                    <div class="d-flex flex-row justify-content-between align-items-center">
                        <p class="mb-0 rz-secondary-color imit-font fz-14">Added on: <?php the_time('g a F d, Y'); ?></p>
                        <div class="dropdown">
                            <button class="text-dark border-0 bg-transparent" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-ellipsis-h"></i>
                            </button>
                            <ul class="dropdown-menu border-0 shadow" aria-labelledby="dropdownMenuButton1">
                                <li><a class="dropdown-item imit-font fz-14 fw-500" href="#" id="edit-dairy" data-post_id="<?php echo get_the_ID(); ?>">Edit</a></li>
                                <li><a class="dropdown-item imit-font fz-14 fw-500" href="#" id="delete-dairy" data-post_id="<?php echo get_the_ID(); ?>">Delete</a></li>
                                <li class="px-2">
                                    <p class="imit-font text-dark fw-12 fw-500 pt-2 px-2">Visiblity</p>
                                    <div class="switch-button">
                                        <input class="switch-button-checkbox" name="dairy-visiblity" type="checkbox" value="public" id="dairy-visiblity" data-post_id="<?php echo get_the_ID(); ?>" <?php if(get_post_status() == 'publish'){echo 'checked';} ?>/>
                                        <label class="switch-button-label imit-font fw-500 fz-14" for="dairy-visiblity"><span class="switch-button-label-span">Public</span></label>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <p class="dairy-text text-dark fz-16 mt-3 mb-0 imit-font" style="line-height: 32px;"><?php echo wp_trim_words( get_the_content(), 100, ' ...' ); ?>
                        <?php if(str_word_count(get_the_content()) > 100 ){
                            ?>
                            <a href="#" class="imit-font fz-16 fw-500 rz-color d-block mt-2" id="dairy-read-more" data-post_id="<?php echo get_the_ID(); ?>">Read More</a>
                            <?php
                        } ?>
                    </p>
                    <p class="dairy-text text-dark fz-16 mt-3 mb-0 imit-font" style="line-height: 32px;display: none;" id="dairy-text-expand<?php echo get_the_ID(); ?>"><?php echo get_the_content(); ?></p>
                </li>
            <?php
            endwhile;
        }else{
            exit('userDairyReachmax');
        }
    }
    die();
}


/**
 * redeem point
 */
add_action('wp_ajax_rz_redeem_point_action', function(){
    global $wpdb;
    $nonce = $_POST['nonce'];
    if(wp_verify_nonce($nonce, 'rz-redeem-point-nonce')){
        $point = sanitize_key($_POST['point']);
        $payment_method = sanitize_text_field($_POST['payment-method']);
        $name = sanitize_text_field($_POST['name']);
        $mobile = sanitize_text_field($_POST['mobile']);
        $upi_id = sanitize_text_field($_POST['upi-id']);
        $account_name = sanitize_text_field($_POST['account-name']);
        $account_number = sanitize_text_field($_POST['account-number']);
        $ifsc_code = sanitize_text_field($_POST['ifsc-code']);
        $bank_name = sanitize_text_field($_POST['bank-name']);
        $branch_name = sanitize_text_field($_POST['branch-name']);
        $user_id = get_current_user_id();

        $get_profiledata = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}rz_user_profile_data WHERE user_id = '{$user_id}'");

        $redeem_point = $get_profiledata->points??0;


        if(empty($point) || empty($payment_method)){
            echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                      <strong>Warning!</strong> Please enter point and select payment method.
                      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
        }else if(is_numeric($point) == false){
            echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                      <strong>Warning!</strong> Invalid point given.
                      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
        }else if($point < 1 || $point > $redeem_point){
            echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                      <strong>Warning!</strong> Please give the number between 1 to '.$redeem_point.'
                      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
        }else{
            if($payment_method == 'Paytm' && (empty($name) || empty($mobile))){
                echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                      <strong>Warning!</strong> Name and phone number required for Paytm payment.
                      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
            }else if($payment_method == 'Google Pay' && (empty($name) && empty($mobile))){
                echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                      <strong>Warning!</strong> Name and phone number required for Google Pay payment.
                      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
            }else if($payment_method == 'UPI' && (empty($name) || empty($mobile) || empty($upi_id))){
                echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                      <strong>Warning!</strong> Name, phone number and UPI id required for UPI payment.
                      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
            }else if($payment_method == 'Bank Transfer' && (empty($account_name) || empty($account_number) || empty($ifsc_code) || empty($bank_name) || empty($branch_name))){
                echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                      <strong>Warning!</strong> Account name, account number ,IFSC code, bank name and branch name required for Bank payment.
                      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
            }else{
                $subject = getUserNameById($user_id).' redeem '.$point.' on '.$payment_method;

                if($payment_method == 'Paytm'){
                    $payment_details = '
                        <tr>
                            <td>Name</td>
                            <td>'.$name.'</td>
                        </tr>
                        <tr>
                            <td>Phone number</td>
                            <td>'.$mobile.'</td>
                        </tr>
                    ';
                }else if($payment_details == 'Google Pay'){
                    $payment_details = '
                        <tr>
                            <td>Name</td>
                            <td>'.$name.'</td>
                        </tr>
                        <tr>
                            <td>Phone number</td>
                            <td>'.$mobile.'</td>
                        </tr>
                    ';
                }else if($payment_details == 'UPI'){
                    $payment_details = '
                        <tr>
                            <td>Name</td>
                            <td>'.$name.'</td>
                        </tr>
                        <tr>
                            <td>Phone number</td>
                            <td>'.$mobile.'</td>
                        </tr>
                        <tr>
                            <td>UPI id</td>
                            <td>'.$upi_id.'</td>
                        </tr>
                    ';
                }else{
                    $payment_details = '
                        <tr>
                            <td>Account Name</td>
                            <td>'.$account_name.'</td>
                        </tr>
                        <tr>
                            <td>Account number</td>
                            <td>'.$account_number.'</td>
                        </tr>
                        <tr>
                            <td>IFSC Code</td>
                            <td>'.$ifsc_code.'</td>
                        </tr>
                        <tr>
                            <td>Bank Name</td>
                            <td>'.$bank_name.'</td>
                        </tr>
                        <tr>
                            <td>Brance Name</td>
                            <td>'.$branch_name.'</td>
                        </tr>
                    ';
                }
                $message = '
                    <table style="width: 100%;">
                        <tr>
                            <th style="text-align: left;">Action</th>
                            <th style="text-align: left;">Details</th>
                        </tr>
                        <tr>
                            <td>Point</td>
                            <td>'.$point.'</td>
                        </tr>
                        <tr>
                            <td>Payment Method</td>
                            <td>'.$payment_method.'</td>
                        </tr>
                        '.$payment_details.'
                    </table>
                ';
                $headers = array('Content-Type: text/html; charset=UTF-8');
                wp_mail('arijitbanarjee889@gmail.com', $subject, $message, $headers);

                $wpdb->update($wpdb->prefix.'rz_user_profile_data', [
                    'points' => ($redeem_point - $point)
                ], ['id' => $get_profiledata->id]);

                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                      <strong>Success!</strong> Your request accepted.
                      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
            }
        }
    }
    die();
});


/**
 * get following tags
 */
add_action('wp_ajax_nopriv_rz_get_all_following_tags', 'get_all_profile_following_tags');
add_action('wp_ajax_rz_get_all_following_tags', 'get_all_profile_following_tags');

function get_all_profile_following_tags(){
    global $wpdb;
    $nonce = $_POST['nonce'];
    if(wp_verify_nonce( $nonce, 'rz-get-following-tags-nonce' )){
        $start = sanitize_key($_POST['start']);
        $limit = sanitize_key($_POST['limit']);
        $user_id = sanitize_key( $_POST['user_id'] );
        $all_following_tags = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_following_tags WHERE user_id = '{$user_id}' ORDER BY id DESC LIMIT $start, $limit", ARRAY_A);

        if(count($all_following_tags) > 0){
            foreach($all_following_tags as $tag){
                $term_d = get_term_by('id', $tag['term_id'], 'question_tags');
                $posts_array = get_posts(
                    array(
                        'posts_per_page' => -1,
                        'post_type' => 'rz_post_question',
                        'fields' => 'ids',
                        'tax_query' => array(
                            array(
                                'taxonomy' => 'question_tags',
                                'field' => 'term_id',
                                'terms' => $term_d->term_id,
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
    
                $term_id = $term_d->term_id;
                $user_id = get_current_user_id();
                $rz_following_tags = $wpdb->prefix.'rz_following_tags';
                $is_user_already_followed = $wpdb->get_row("SELECT * FROM {$rz_following_tags} WHERE user_id = '{$user_id}' AND term_id = '{$term_id}'");
                ?>
                <li class="hash-list list-unstyled col-sm-4 my-2">
                    <div class="bg-white py-2 px-3 rounded border">
                        <div class="hash-top d-flex flex-row justify-content-between align-items-center">
                            <a href="<?php echo get_term_link($term_d->term_id, 'question_tags'); ?>" class="imit-font fw-500 fz-16 text-dark d-block">#<?php echo $term_d->name; ?></a>
                            <button type="button" class="add-post-by-tag p-0 <?php if(!empty($is_user_already_followed)){echo 'rz-color';}else{echo 'rz-secondary-color';} ?> bg-transparent fz-14 border-0" data-term_id="<?php echo $term_d->term_id; ?>" id="follow-tag"><?php if(!empty($is_user_already_followed)){echo '<i class="fas fa-check-square"></i>';}else{echo '<i class="fas fa-plus-circle"></i>';} ?></button>
                        </div>
                        <div class="d-flex flex-row justify-content-between align-items-center me-2">
                            <p class="rz-secondary-color imit-font fz-12 fw-400 mb-0"><?php echo $term_d->count; ?> Question</p>
                            <p class="rz-secondary-color imit-font fz-12 fw-400 mb-0"><?php echo $count_answer; ?> Answers</p>
                            <p class="rz-secondary-color imit-font fz-12 fw-400 mb-0"><?php echo $total_view; ?> Views</p>
                        </div>
                    </div>
                </li> 
                <?php
            }
        }else{
            exit('followingTagReachMax');
        }
    }
    die();
}