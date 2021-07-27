<?php


/**
 * for discussion
 */
add_shortcode('imit-discuss', function(){
    ob_start();
    global $wpdb;
    ?>
    <section class="imit-discussion">
    <div class="rz-mid">
        <div class="row">
        <div class="col-lg-9">
            <div class="d-flex flex-row justify-content-between align-items-center rz-bg-color rounded">
                <ul class="rz-tabs d-flex flex-row justify-content-start align-items-center ps-0 mb-0">
                    <li class="rz-tab-list list-unstyled">
                        <a href="#" class="rz-tab-link tab-link imit-font fz-14 d-block text-white fw-500 px-4 active" data-target="discuss-and-debate">Discuss & Debate <span></span></a>
                    </li>
                    <li class="rz-tab-list list-unstyled">
                        <a href="#" class="rz-tab-link tab-link imit-font fz-14 d-block text-white fw-500 px-4" data-target="newest">Newest <span></span></a>
                    </li>
                    <li class="rz-tab-list list-unstyled">
                        <a href="#" class="rz-tab-link tab-link imit-font fz-14 d-block text-white fw-500 px-4" data-target="most-viwed">Most Viewed <span></span></a>
                    </li>
                    <li class="rz-tab-list list-unstyled">
                        <a href="#" class="rz-tab-link tab-link imit-font fz-14 d-block text-white fw-500 px-4" data-target="hotely-debated">Hotly Debated <span></span></a>
                    </li>
                </ul>
            </div>
    
            <div class="tab-content" id="discuss-and-debate">
                <div class="add-new-discussion mt-3">
                    <div class="rz-br rz-border p-3 bg-white">
                        <div class="d-flex flex-row justify-content-between align-items-center">
                            <div class="profile-image">
                                <img src="<?php getProfileImageById(get_current_user_id(  )); ?>" alt="">
                            </div>
                            <div class="rz-border p-2 px-3 create-new-post ms-2" style="margin-right: 25px;border-radius: 6px;">
                                <span class="rz-secondary-color imit-font fz-14 fw-400 d-block">Create new post</span>
                            </div>
                            <div class="d-flex flex-row justify-content-end align-items-center point">
                                <img src="<?php echo plugins_url('images/Group (3).png', __FILE__); ?>" alt="" class="d-block">
                                <span class="imit-font fz-14 rz-color fw-500 ms-1 d-block d-flex flex-row justify-content-end align-items-center"><span class="d-block me-1">10</span> <span class="d-block">Points</span></span>
                            </div>
                        </div>
                    </div>
                    <form class="bg-white rz-border rz-br p-3 mt-3" id="add_discussion_form" enctype="multipart/form-data" style="display: none;">
                        <div id="discussion-error"></div>
                        <input type="text" name="title" class="mb-3 form-control rounded imit-font fw-400 fz-16 text-dark" placeholder="Title">
                        <input type="text" name="tag" class="mb-3 form-control rounded imit-font fw-400 fz-16 text-dark" placeholder="Enter tags Eg: wordpres, php">
                        <input type="file" name="featured-image" class="form-control imit-font fz-16 mb-3">
                        <?php wp_editor('', 'editor', [
                            'media_buttons' => false
                        ]); ?>
                        <!-- <textarea name="editor" class="form-control imit-font fz-16" id="" cols="30" rows="10" placeholder="Add description here."></textarea> -->
                        <button type="submit" class="btn rz-bg-color imit-font fw-500 fz-16 d-table ms-auto text-white mt-2">Submit</button>
                    </form>
                </div>
                <ul class="blog-feed ps-0 mb-0" id="discuss-and-debate-ul">
                                        
                </ul>
            </div>

<!--            newest discussion posts-->
            <div class="tab-content" style="display: none;" id="newest">
                <ul class="blog-feed ps-0 mb-0" id="newest-ul">
                    
                </ul>
            </div>

<!--            most viewed discussion posts-->
            <div class="tab-content" style="display: none;" id="most-viwed">
                <ul class="blog-feed ps-0 mb-0" id="most-viwed-ul">
                    
                </ul>
            </div>

<!--            hotely debated-->
            <div class="tab-content" id="hotely-debated" style="display: none;">
                <ul class="blog-feed ps-0 mb-0" id="hotely-debated-ul">
                    
                </ul>
            </div>
            <div id="tab-content-loader" style="display: none;">
                <div class="d-flex justify-content-center mt-2">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="join rz-br rz-bg-color rounded-2 p-3" style="background-image: url('<?php echo plugins_url('images/Group 237.png', __FILE__); ?>');">
                <h3 class="title m-0 text-white imit-font fz-20 fw-400">Join our Partner Program and earn money on Recozilla</h3>
                <a href="<?php echo site_url(); ?>/join-partner-program/" class="btn bg-white fz-12 rz-color imit-font fw-500 mt-3">Join Now</a>
            </div>
            <div class="card question-card rz-br rz-border mt-3">
                <div class="card-header rz-bg-color">
                    <h3 class="title imit-font text-white fw-500 m-0 p-2">Related Questions</h3>
                </div>
                <div class="card-body p-0">
                    <ul class="all-questions ps-0 mb-0">

                    <?php 
                    $related_question = new WP_Query([
                        'post_type' => 'rz_post_question',
                        'posts_per_page' => 5
                    ]);
                    while($related_question->have_posts()):$related_question->the_post();
                        ?>
                        <li class="question-list list-unstyled"><a href="<?php the_permalink(  ); ?>" class="question-link d-block text-dark imit-font fz-16 fw-500 p-3 d-block"><?php echo wp_trim_words(get_the_title(), 10, false); ?></a></li>
                        <?php
                    endwhile;
                    wp_reset_postdata(  );
                    ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    </div>
    </section>
    

    <!-- share post modal -->
    <div class="modal fade" id="share-post-modal">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="imit-font fz-20 fz-color fw-500 m-0">Share question</h2>
                    <button class="btn-close" type="button" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="share-link d-flex flex-row justify-content-between align-items-center">
                        <a href="#" class="facebook" id="share-facebook" data-shareurl = ""><i class="fab fa-facebook-f"></i></a>
                        <a class="twitter" id="tweet-data" data-shareurl = "" href="#"><i class="fab fa-twitter"></i></a>
                        <a class="linkedin" id="share-linkedin" href="#" data-shareurl = ""><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    
    return ob_get_clean();
    });


    /**
 * add discussion
 */
add_action('wp_ajax_rz_add_discussion', function(){
    global $wpdb;
    $nonce = $_POST['nonce'];
    if(wp_verify_nonce($nonce, 'rz-add-add-discussion')){

        $title = sanitize_text_field($_POST['title']);
        $tags = sanitize_text_field($_POST['tag']);
        $editor = $_POST['editor'];
        $image = $_FILES['featured-image']['name'];
        $image_tmp = $_FILES['featured-image']['tmp_name'];
        $rz_partner_program = $wpdb->prefix.'rz_user_programs';
        $rz_point_table = $wpdb->prefix.'rz_point_table';
        $rz_user_profile_data = $wpdb->prefix.'rz_user_profile_data';

        $exp = explode('.', $image);

        $ext = end($exp);

        $unique_name = md5(time().rand()).'.'.$ext;

        $format = ['png', 'jpg', 'gif', 'jpeg'];

        if(empty($title) || empty($tags) || empty($editor)){
            echo '<div class="alert imit-font fz-16 alert-warning alert-dismissible fade show" role="alert">
            <strong>Warning!</strong> All fields are required.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
        }else if(!empty($image) && in_array($ext, $format) == false){
            echo '<div class="alert imit-font fz-16 alert-danger alert-dismissible fade show" role="alert">
            <strong>Stop!</strong> Invalid Image format.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
        }else{
            $image_data = wp_upload_bits($unique_name, null, file_get_contents($image_tmp));
            $user_id = get_current_user_id();

            $my_post = array(
                'post_title'    => wp_strip_all_tags( $title ),
                'post_type'    => 'rz_discussion',
                'post_content'  => $editor,
                'post_status'   => 'publish',
                'post_author'   => $user_id,
                'tags_input'   => $tags,
            );

            // Insert the post into the database
            $post_id = wp_insert_post( $my_post );

            $is_user_joined_programme = $wpdb->get_results("SELECT * FROM {$rz_partner_program} WHERE user_id = '$user_id' AND status = '1'", ARRAY_A);

            if(count($is_user_joined_programme)){
                $wpdb->insert($rz_point_table, [
                    'user_id' => $user_id,
                    'content_id' => $post_id,
                    'point_type' => 'post',
                    'point_earn' => 10
                ]);

                $get_point = $wpdb->get_row("SELECT * FROM {$rz_user_profile_data} WHERE user_id = '$user_id'");


                $user_point = $get_point->points;

                if(empty($get_point)){
                    $wpdb->insert($rz_user_profile_data, [
                        'points' => 10,
                        'user_id' => $user_id
                    ]);
                }else{
                    $wpdb->update($rz_user_profile_data, [
                        'points' => ($user_point+10),
                    ], ['user_id' => $user_id]);
                }
            }

            $post_tags = explode(',', $tags);

            wp_set_post_terms( $post_id, $post_tags, 'discussion_tags');

            $filename = $image_data['url'];

            if(!empty($filename) && !empty($post_id)) {
                // Check the type of file. We'll use this as the 'post_mime_type'.
                $filetype = wp_check_filetype(basename($filename), null);

                // Get the path to the upload directory.
                $wp_upload_dir = wp_upload_dir();

                // Prepare an array of post data for the attachment.
                $attachment = array(
                    'guid' => $wp_upload_dir['url'] . '/' . basename($filename),
                    'post_mime_type' => $filetype['type'],
                    'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
                    'post_content' => '',
                    'post_status' => 'inherit'
                );

                // Insert the attachment.
                $attach_id = wp_insert_attachment($attachment, $filename, $post_id);

                // Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
                require_once(ABSPATH . 'wp-admin/includes/image.php');

                // Generate the metadata for the attachment, and update the database record.
                $attach_data = wp_generate_attachment_metadata($attach_id, $filename);
                wp_update_attachment_metadata($attach_id, $attach_data);

                set_post_thumbnail($post_id, $attach_id);
            }

            echo '<div class="alert imit-font fz-16 alert-success alert-dismissible fade show" role="alert">
            <strong>Success!</strong> Post added successfully.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
        }
    }
    die();
});


/**
 * add like or dislike on discussion
 */
add_action('wp_ajax_imit_add_like_or_dislike_on_discussion', function(){
    global $wpdb;
    $nonce = $_POST['nonce'];
    if(wp_verify_nonce($nonce, 'rz-add-like-or-dislike-on-discuss')){
        $post_id = sanitize_key($_POST['post_id']);
        $user_id = get_current_user_id();
        $like_type = sanitize_text_field($_POST['like_type']);
        $rz_discuss_likes = $wpdb->prefix.'rz_discuss_likes';
        $get_all_likes = $wpdb->get_row("SELECT * FROM {$rz_discuss_likes} WHERE user_id = '$user_id' AND post_id = '$post_id'");

        $post_author = get_post_field('post_author', $post_id);

        if(!empty($post_id) && !empty($user_id)){
            if(!empty($get_all_likes)){
                if($like_type == $get_all_likes->like_type){


                    $wpdb->delete($wpdb->prefix.'notification', [
                        'sender_id' => $user_id,
                        'receiver_id' => $post_author,
                        'notification_type' => 'discuss-vote',
                        'content_id' => $get_all_likes->id
                    ]);


                    $wpdb->delete($rz_discuss_likes, [
                        'id' => $get_all_likes->id
                    ]);


                    $get_up_like = $wpdb->get_results("SELECT * FROm {$wpdb->prefix}rz_discuss_likes WHERE post_id = '{$post_id}' AND like_type = 'up-like'", ARRAY_A);
                    $get_down_like = $wpdb->get_results("SELECT * FROm {$wpdb->prefix}rz_discuss_likes WHERE post_id = '{$post_id}' AND like_type = 'down-like'", ARRAY_A);
                    $count_like = intval($get_up_like) - intval($get_down_like);
                    
                    if($like_type == 'up-like'){
                        $response['up_like'] = false;
                        $response['counter'] = $count_like;
                    }else{
                        $response['down_like'] = false;
                        $response['counter'] = $count_like;
                    }
                }else{
                    $wpdb->update($rz_discuss_likes, [
                        'like_type' => $like_type
                    ],[
                        'id' => $get_all_likes->id
                    ]);

                    if($user_id != $post_author){
                        $message = getUserNameById($user_id).' '.$like_type.' on your post comment <strong>'.get_the_title($post_id).'</strong>';
                        $wpdb->update($wpdb->prefix.'notification', [
                            'massage_text' => $message
                        ], [
                            'sender_id' => $user_id,
                            'receiver_id' => $post_author,
                            'notification_type' => 'discuss-vote',
                            'content_id' => $get_all_likes->id
                        ]);
                    }

                    $get_up_like = $wpdb->get_results("SELECT * FROm {$wpdb->prefix}rz_discuss_likes WHERE post_id = '{$post_id}' AND like_type = 'up-like'", ARRAY_A);
                    $get_down_like = $wpdb->get_results("SELECT * FROm {$wpdb->prefix}rz_discuss_likes WHERE post_id = '{$post_id}' AND like_type = 'down-like'", ARRAY_A);
                    $count_like = intval($get_up_like) - intval($get_down_like);


                    if($like_type == 'up-like'){
                        $response['up_like'] = true;
                        $response['counter'] = $count_like;
                    }else{
                        $response['down_like'] = true;
                        $response['counter'] = $count_like;
                    }
                }
            }else {
                $wpdb->insert($rz_discuss_likes, [
                    'post_id' => $post_id,
                    'user_id' => $user_id,
                    'like_type' => $like_type
                ]);

                $like_id = $wpdb->insert_id;

                if($user_id != $post_author){
                    $message = getUserNameById($user_id).' '.$like_type.' on your post comment <strong>'.get_the_title($post_id).'</strong>';
                    add_notification(get_post_permalink($post_id), $user_id, $post_author, 'discuss-vote', $like_id, $message);
                }

                $get_up_like = $wpdb->get_results("SELECT * FROm {$wpdb->prefix}rz_discuss_likes WHERE post_id = '{$post_id}' AND like_type = 'up-like'", ARRAY_A);
                $get_down_like = $wpdb->get_results("SELECT * FROm {$wpdb->prefix}rz_discuss_likes WHERE post_id = '{$post_id}' AND like_type = 'down-like'", ARRAY_A);
                $count_like = intval($get_up_like) - intval($get_down_like);
                
                if($like_type == 'up-like'){
                    $response['up_like'] = true;
                    $response['counter'] = $count_like;
                }else{
                    $response['down_like'] = true;
                    $response['counter'] = $count_like;
                }
            }
        }

        echo json_encode($response);
    }
    die();
});

/**
 * get discuss and debate posts
 */
add_action('wp_ajax_nopriv_rz_discuss_and_debate_posts', 'imit_rz_discuss_and_debate_posts');
add_action('wp_ajax_rz_discuss_and_debate_posts', 'imit_rz_discuss_and_debate_posts');

function imit_rz_discuss_and_debate_posts(){
    global $wpdb;
    $nonce = $_POST['nonce'];
    if(wp_verify_nonce( $nonce, 'rz-discuss-and-debate-nonce' )){
        $page_number = sanitize_key($_POST['page_num']);
        $discussion_post = new WP_Query([
                'post_type' => 'rz_discussion',
            'posts_per_page' => 10,
            'paged' => $page_number
        ]);
        if($discussion_post->have_posts(  )){
            while($discussion_post->have_posts()):$discussion_post->the_post();

            $user_id = get_the_author_meta('ID');
    
            $get_user_profile_data = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}rz_user_profile_data WHERE user_id = '$user_id'");
            $user_data = get_userdata($user_id);
            ?>
            <li class="blog-list list-unstyled mt-3" id="dis-post<?php echo get_the_ID(); ?>">
                <div class="card rz-border rz-br">
                    <div class="card-body p-4">
                        <div class="blog-list-header d-flex flex-row justify-content-between align-items-center">
                            <div class="user-info d-flex flex-row justify-content-start align-items-center">
                                <div class="profile-image">
                                    <img src="<?php getProfileImageById($user_id); ?>" alt="">
                                </div>
                                <div class="userdetails ms-2">
                                    <?php
                                    if(!empty($user_data->user_firstname) && !empty($user_data->user_lastname)){
                                        ?>
                                        <a href="<?php echo site_url().'/user/'.$user_data->user_login; ?>" class="name imit-font fz-16 text-dark fw-500 d-block"><?php echo ucfirst($user_data->user_firstname).' '.ucfirst($user_data->user_lastname); ?></a>
                                            <?php
                                    }else{
                                        ?>
                                        <a href="<?php echo site_url().'/user/'.$user_data->user_login; ?>" class="name imit-font fz-16 text-dark fw-500 d-block"><?php echo $user_data->display_name; ?></a>
                                        <?php
                                    }
                                    ?>
    
                                    <?php if(!empty($get_user_profile_data->occupation)){
                                       ?>
                                        <p class="mb-0 designation imit-font fw-400 rz-secondary-color fz-12"><?php echo $get_user_profile_data->occupation; ?></p>
                                    <?php
                                    }?>
                                </div>
                            </div>
                            <p class="mb-0 imit-font fz-14 rz-secondary-color fw-400">Posted on: <?php the_time('g a F d, Y'); ?></p>
                        </div>
                        <div class="blog-body">
                            <a href="<?php the_permalink(); ?>" class="my-3 title imit-font text-dark fw-500 d-block"><?php the_title(); ?> </a>
                            <p class="description imit-font fz-14 rz-secondary-color"><?php echo wp_trim_words(get_the_content(), 100, ' ...'); ?></p>
                            <?php if(str_word_count(get_the_content()) > 100){
                                ?>
                                <a href="<?php the_permalink( ); ?>" class="imit-font fz-16 rz-color fw-500 d-block mb-3">Read More</a>
                                <?php
                            } ?>
                            <?php the_post_thumbnail('full', ['class' => 'img-fluid mb-3']); ?>
                            <ul class="tags ps-0 mb-0 d-flex flex-row justify-content-start align-items-center">
                                <?php
                                $tags = wp_get_post_terms(get_the_ID(), 'discussion_tags');
                                foreach($tags as $tag){
                                    echo '<li class="tag-list list-unstyled"><a href="'.get_term_link($tag->term_id, 'discussion_tags').'" class="tag-link imit-font fz-12 d-block me-2 text-dark border px-1">'.$tag->name.'</a></li>';
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                    <div class="card-footer border-top-0 d-flex flex-row justify-content-between align-items-center p-3">
                        <ul class="blog-footer-icons ps-0 mb-0 d-flex flex-row justify-content-start align-items-center" id="discuss-action<?php echo get_the_ID(); ?>">
                            <?php
                            $post_id = get_the_ID();
                            $user_id = get_current_user_id();
                            $get_all_up_like = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_discuss_likes WHERE post_id='$post_id' AND user_id = '$user_id' AND like_type = 'up-like'", ARRAY_A);
                            $get_all_down_like = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_discuss_likes WHERE post_id='$post_id' AND user_id = '$user_id' AND like_type = 'down-like'", ARRAY_A);

                            $count_discuss_up_like = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_discuss_likes WHERE post_id='$post_id' AND like_type = 'up-like'", ARRAY_A);
                            $count_discuss_down_like = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_discuss_likes WHERE post_id='$post_id' AND like_type = 'down-like'", ARRAY_A);
                            ?>
                            <li class="blog-footer-list list-unstyled">
                                <a href="#" class="prev imit-font fz-12 text-dark fw-400 me-3 <?php if(count($get_all_up_like)){echo 'active';} ?>" id="discuss-up-like" data-post_id="<?php echo get_the_ID(); ?>"><i class="fas fa-arrow-up"></i></a>
                            </li>
                            <li class="blog-footer-list list-unstyled">
                                <span class="counter imit-font fz-16 fw-500 me-3 <?php if((count($count_discuss_up_like) - count($count_discuss_down_like)) < 0){echo 'text-danger';}else{echo 'text-success';} ?>" id="discuss-like-counter<?php echo get_the_ID(); ?>"><?php echo count($count_discuss_up_like) - count($count_discuss_down_like); ?></span>
                            </li>
                            <li class="blog-footer-list list-unstyled">
                                <a href="#" class="next imit-font fz-12 text-dark fw-400 me-3 <?php if(count($get_all_down_like)){echo 'active';} ?>" id="discuss-down-like" data-post_id="<?php echo get_the_ID(); ?>"><i class="fas fa-arrow-down"></i></a>
                            </li>
                            <li class="blog-footer-list list-unstyled">
                                <a href="#" class="Visitor imit-font fz-14 text-dark fw-400 me-3"><i class="fas fa-eye"></i> <?php echo getPostViews(get_the_ID()); ?></a>
                            </li>
                            <li class="blog-footer-list list-unstyled">
                                <a href="<?php the_permalink(); ?>" class="comments imit-font fz-14 text-dark fw-400 me-3"><i class="fas fa-comments"></i> Comments</a>
                            </li>
                        </ul>

                        <?php
                        $post_author = get_the_author_meta( 'ID' );
                        if(is_user_logged_in() && $post_author == get_current_user_id() ){
                            ?>
                            <div class="other text-dark d-flex flex-row justify-content-end align-items-center">
                            <a href="#" class="text-dark fz-16 me-3" id="share-post-data" data-post_url="<?php the_permalink(); ?>" data-bs-toggle="modal" data-bs-target="#share-post-modal"><i class="fas fa-share"></i></a>
                                <div class="dropdown">
                                    <a class="text-dark fz-16" href="#" role="button" id="more-option-feed" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-ellipsis-h"></i>
                                    </a>

                                    <ul class="dropdown-menu" aria-labelledby="more-option-feed">
                                        <li><a class="dropdown-item imit-font fz-14 text-dark" href="#" id="delete-discuss-post" data-post_id="<?php echo get_the_ID(); ?>">Delete</a></li>
                                    </ul>
                                </div>
                            </div>
                            <?php
                        } ?>
                    </div>
                </div>
            </li>
            <?php endwhile;
        }else{
            exit('discussAndDebateReachmax');
        }
    }
    die();
}


/**
 * get newest posts
 */
add_action('wp_ajax_nopriv_rz_get_newest_posts', 'get_all_newest_posts');
add_action('wp_ajax_rz_get_newest_posts', 'get_all_newest_posts');


function get_all_newest_posts(){
    global $wpdb;
    $nonce = $_POST['nonce'];
    if(wp_verify_nonce( $nonce, 'rz-newest-posts-nonce' )){
        $page_number = sanitize_key( $_POST['page_num'] );
        $discussion_post  = new WP_Query(array(
            'post_type' => 'rz_discussion',
            'posts_per_page' => 10,
            'paged' => $page_number
        ));
        if($discussion_post->have_posts(  )){
            while($discussion_post->have_posts()):$discussion_post->the_post();

            $user_id = get_the_author_meta('ID');

            $get_user_profile_data = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}rz_user_profile_data WHERE user_id = '$user_id'");
            $user_data = get_userdata($user_id);
            ?>
            <li class="blog-list list-unstyled mt-3" id="dis-post<?php echo get_the_ID(); ?>">
                <div class="card rz-border rz-br">
                    <div class="card-body p-4">
                        <div class="blog-list-header d-flex flex-row justify-content-between align-items-center">
                            <div class="user-info d-flex flex-row justify-content-start align-items-center">
                                <div class="profile-image">
                                    <img src="<?php getProfileImageById($user_id); ?>" alt="">
                                </div>
                                <div class="userdetails ms-2">
                                    <?php
                                    if(!empty($user_data->user_firstname) && !empty($user_data->user_lastname)){
                                        ?>
                                        <a href="<?php echo site_url().'/user/'.$user_data->user_login; ?>" class="name imit-font fz-16 text-dark fw-500 d-block"><?php echo ucfirst($user_data->user_firstname).' '.ucfirst($user_data->user_lastname); ?></a>
                                        <?php
                                    }else{
                                        ?>
                                        <a href="<?php echo site_url().'/user/'.$user_data->user_login; ?>" class="name imit-font fz-16 text-dark fw-500 d-block"><?php echo $user_data->display_name; ?></a>
                                        <?php
                                    }
                                    ?>

                                    <?php if(!empty($get_user_profile_data->occupation)){
                                        ?>
                                        <p class="mb-0 designation imit-font fw-400 rz-secondary-color fz-12"><?php echo $get_user_profile_data->occupation; ?></p>
                                        <?php
                                    }?>
                                </div>
                            </div>
                            <p class="mb-0 imit-font fz-14 rz-secondary-color fw-400">Posted on: <?php the_time('g a F d, Y'); ?></p>
                        </div>
                        <div class="blog-body">
                            <a href="<?php the_permalink(); ?>" class="my-3 title imit-font text-dark fw-500 d-block"><?php the_title(); ?> </a>
                            <p class="description imit-font fz-14 rz-secondary-color"><?php echo wp_trim_words(get_the_content(), 100, false); ?></p>
                            <?php if(str_word_count(get_the_content()) > 100){
                                ?>
                                <a href="<?php the_permalink( ); ?>" class="imit-font fz-16 rz-color fw-500 d-block mb-3">Read More</a>
                                <?php
                            } ?>
                            <?php the_post_thumbnail('full', ['class' => 'img-fluid mb-3']); ?>
                            <ul class="tags ps-0 mb-0 d-flex flex-row justify-content-start align-items-center">
                                <?php
                                $tags = wp_get_post_terms(get_the_ID(), 'discussion_tags');
                                foreach($tags as $tag){
                                    echo '<li class="tag-list list-unstyled"><a href="'.get_term_link($tag->term_id, 'discussion_tags').'" class="tag-link imit-font fz-12 d-block me-2 rz-secondary-color rounded border px-1">'.$tag->name.'</a></li>';
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                    <div class="card-footer border-top-0 d-flex flex-row justify-content-between align-items-center p-3">
                        <ul class="blog-footer-icons ps-0 mb-0 d-flex flex-row justify-content-start align-items-center" id="discuss-action<?php echo get_the_ID(); ?>">
                            <?php
                            $post_id = get_the_ID();
                            $user_id = get_current_user_id();
                            $get_all_up_like = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_discuss_likes WHERE post_id='$post_id' AND user_id = '$user_id' AND like_type = 'up-like'", ARRAY_A);
                            $get_all_down_like = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_discuss_likes WHERE post_id='$post_id' AND user_id = '$user_id' AND like_type = 'down-like'", ARRAY_A);
                            
                            $count_discuss_up_like = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_discuss_likes WHERE post_id='$post_id' AND like_type = 'up-like'", ARRAY_A);
                            $count_discuss_down_like = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_discuss_likes WHERE post_id='$post_id' AND like_type = 'down-like'", ARRAY_A);
                            ?>
                            <li class="blog-footer-list list-unstyled">
                                <a href="#" class="prev imit-font fz-12 text-dark fw-400 me-3 <?php if(count($get_all_up_like)){echo 'active';} ?>" id="discuss-up-like" data-post_id="<?php echo get_the_ID(); ?>"><i class="fas fa-arrow-up"></i></a>
                            </li>
                            <li class="blog-footer-list list-unstyled">
                                <span class="counter imit-font fz-16 fw-500 me-3 <?php if((count($count_discuss_up_like) - count($count_discuss_down_like)) < 0){echo 'text-danger';}else{echo 'text-success';} ?>" id="discuss-like-counter<?php echo get_the_ID(); ?>"><?php echo count($count_discuss_up_like) - count($count_discuss_down_like); ?></span>
                            </li>
                            <li class="blog-footer-list list-unstyled">
                                <a href="#" class="next imit-font fz-12 text-dark fw-400 me-3 <?php if(count($get_all_down_like)){echo 'active';} ?>" id="discuss-down-like" data-post_id="<?php echo get_the_ID(); ?>"><i class="fas fa-arrow-down"></i></a>
                            </li>
                            <li class="blog-footer-list list-unstyled">
                                <a href="#" class="Visitor imit-font fz-14 text-dark fw-400 me-3"><i class="fas fa-eye"></i> <?php echo getPostViews(get_the_ID()); ?></a>
                            </li>
                            <li class="blog-footer-list list-unstyled">
                                <a href="<?php the_permalink(); ?>" class="comments imit-font fz-14 text-dark fw-400 me-3"><i class="fas fa-comments"></i> Comments</a>
                            </li>
                        </ul>

                        <?php
                        $post_author = get_the_author_meta( 'ID' );
                        if(is_user_logged_in() && $post_author == get_current_user_id() ){
                            ?>
                            <div class="other text-dark d-flex flex-row justify-content-end align-items-center">
                            <a href="#" class="text-dark fz-16 me-3" id="share-post-data" data-post_url="<?php the_permalink(); ?>" data-bs-toggle="modal" data-bs-target="#share-post-modal"><i class="fas fa-share"></i></a>
                                <div class="dropdown">
                                    <a class="text-dark fz-16" href="#" role="button" id="more-option-feed" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-ellipsis-h"></i>
                                    </a>

                                    <ul class="dropdown-menu" aria-labelledby="more-option-feed">
                                        <li><a class="dropdown-item imit-font fz-14 text-dark" href="#" id="delete-discuss-post" data-post_id="<?php echo get_the_ID(); ?>">Delete</a></li>
                                    </ul>
                                </div>
                            </div>
                            <?php
                        } ?>
                    </div>
                </div>
            </li>
        <?php endwhile;
        }else{
            exit('newestPostReachmax');
        }
    }
    die();
}

/**
 * get most viewd posts
 */
add_action('wp_ajax_nopriv_rz_most_viewd_posts', 'imit_rz_get_most_viwed_posts');
add_action('wp_ajax_rz_most_viewd_posts', 'imit_rz_get_most_viwed_posts');

function imit_rz_get_most_viwed_posts(){
    global $wpdb;
    $nonce = $_POST['nonce'];
    if(wp_verify_nonce( $nonce, 'rz-most-viewed-posts-nonce' )){
        $page_number = sanitize_key( $_POST['page_num'] );
        $discussion_post  = new WP_Query(array(
            'post_type' => 'rz_discussion',
            'posts_per_page' => 10,
            'meta_key' => 'post_views_count',
            'orderby' => 'meta_value_num',
            'order' => 'DESC',
            'paged' => $page_number
        ));

        if($discussion_post->have_posts(  )){
            while($discussion_post->have_posts()):$discussion_post->the_post();

            $user_id = get_the_author_meta('ID');

            $get_user_profile_data = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}rz_user_profile_data WHERE user_id = '$user_id'");
            $user_data = get_userdata($user_id);
            ?>
            <li class="blog-list list-unstyled mt-3" id="dis-post<?php echo get_the_ID(); ?>">
                <div class="card rz-border rz-br">
                    <div class="card-body p-4">
                        <div class="blog-list-header d-flex flex-row justify-content-between align-items-center">
                            <div class="user-info d-flex flex-row justify-content-start align-items-center">
                                <div class="profile-image">
                                    <img src="<?php getProfileImageById($user_id); ?>" alt="">
                                </div>
                                <div class="userdetails ms-2">
                                    <?php
                                    if(!empty($user_data->user_firstname) && !empty($user_data->user_lastname)){
                                        ?>
                                        <a href="<?php echo site_url().'/user/'.$user_data->user_login; ?>" class="name imit-font fz-16 text-dark fw-500 d-block"><?php echo ucfirst($user_data->user_firstname).' '.ucfirst($user_data->user_lastname); ?></a>
                                        <?php
                                    }else{
                                        ?>
                                        <a href="<?php echo site_url().'/user/'.$user_data->user_login; ?>" class="name imit-font fz-16 text-dark fw-500 d-block"><?php echo $user_data->display_name; ?></a>
                                        <?php
                                    }
                                    ?>

                                    <?php if(!empty($get_user_profile_data->occupation)){
                                        ?>
                                        <p class="mb-0 designation imit-font fw-400 rz-secondary-color fz-12"><?php echo $get_user_profile_data->occupation; ?></p>
                                        <?php
                                    }?>
                                </div>
                            </div>
                            <p class="mb-0 imit-font fz-14 rz-secondary-color fw-400">Posted on: <?php the_time('g a F d, Y'); ?></p>
                        </div>
                        <div class="blog-body">
                            <a href="<?php the_permalink(); ?>" class="my-3 title imit-font text-dark fw-500 d-block"><?php the_title(); ?> </a>
                            <p class="description imit-font fz-14 rz-secondary-color"><?php echo wp_trim_words(get_the_content(), 100, false); ?></p>
                            <?php if(str_word_count(get_the_content()) > 100){
                                ?>
                                <a href="<?php the_permalink( ); ?>" class="imit-font fz-16 rz-color fw-500 d-block mb-3">Read More</a>
                                <?php
                            } ?>
                            <?php the_post_thumbnail('full', ['class' => 'img-fluid mb-3']); ?>
                            <ul class="tags ps-0 mb-0 d-flex flex-row justify-content-start align-items-center">
                                <?php
                                $tags = wp_get_post_terms(get_the_ID(), 'discussion_tags');
                                foreach($tags as $tag){
                                    echo '<li class="tag-list list-unstyled"><a href="'.get_term_link($tag->term_id, 'discussion_tags').'" class="tag-link imit-font fz-12 d-block me-2 rz-secondary-color rounded border px-1">'.$tag->name.'</a></li>';
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                    <div class="card-footer border-top-0 d-flex flex-row justify-content-between align-items-center p-3">
                        <ul class="blog-footer-icons ps-0 mb-0 d-flex flex-row justify-content-start align-items-center" id="discuss-action<?php echo get_the_ID(); ?>">
                            <?php
                            $post_id = get_the_ID();
                            $user_id = get_current_user_id();
                            $get_all_up_like = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_discuss_likes WHERE post_id='$post_id' AND user_id = '$user_id' AND like_type = 'up-like'", ARRAY_A);
                            $get_all_down_like = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_discuss_likes WHERE post_id='$post_id' AND user_id = '$user_id' AND like_type = 'down-like'", ARRAY_A);

                            $count_discuss_up_like = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_discuss_likes WHERE post_id='$post_id' AND like_type = 'up-like'", ARRAY_A);
                            $count_discuss_down_like = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_discuss_likes WHERE post_id='$post_id' AND like_type = 'down-like'", ARRAY_A);
                            ?>
                            <li class="blog-footer-list list-unstyled">
                                <a href="#" class="prev imit-font fz-12 text-dark fw-400 me-3 <?php if(count($get_all_up_like)){echo 'active';} ?>" id="discuss-up-like" data-post_id="<?php echo get_the_ID(); ?>"><i class="fas fa-arrow-up"></i></a>
                            </li>
                            <li class="blog-footer-list list-unstyled">
                                <span class="counter imit-font fz-16 fw-500 me-3 <?php if((count($count_discuss_up_like) - count($count_discuss_down_like)) < 0){echo 'text-danger';}else{echo 'text-success';} ?>" id="discuss-like-counter<?php echo get_the_ID(); ?>"><?php echo count($count_discuss_up_like) - count($count_discuss_down_like); ?></span>
                            </li>
                            <li class="blog-footer-list list-unstyled">
                                <a href="#" class="next imit-font fz-12 text-dark fw-400 me-3 <?php if(count($get_all_down_like)){echo 'active';} ?>" id="discuss-down-like" data-post_id="<?php echo get_the_ID(); ?>"><i class="fas fa-arrow-down"></i></a>
                            </li>
                            <li class="blog-footer-list list-unstyled">
                                <a href="#" class="Visitor imit-font fz-14 text-dark fw-400 me-3"><i class="fas fa-eye"></i> <?php echo getPostViews(get_the_ID()); ?></a>
                            </li>
                            <li class="blog-footer-list list-unstyled">
                                <a href="<?php the_permalink(); ?>" class="comments imit-font fz-14 text-dark fw-400 me-3"><i class="fas fa-comments"></i> Comments</a>
                            </li>
                        </ul>

                        <?php
                        $post_author = get_the_author_meta( 'ID' );
                        if(is_user_logged_in() && $post_author == get_current_user_id() ){
                            ?>
                            <div class="other text-dark d-flex flex-row justify-content-end align-items-center">
                            <a href="#" class="text-dark fz-16 me-3" id="share-post-data" data-post_url="<?php the_permalink(); ?>" data-bs-toggle="modal" data-bs-target="#share-post-modal"><i class="fas fa-share"></i></a>
                                <div class="dropdown">
                                    <a class="text-dark fz-16" href="#" role="button" id="more-option-feed" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-ellipsis-h"></i>
                                    </a>

                                    <ul class="dropdown-menu" aria-labelledby="more-option-feed">
                                        <li><a class="dropdown-item imit-font fz-14 text-dark" href="#" id="delete-discuss-post" data-post_id="<?php echo get_the_ID(); ?>">Delete</a></li>
                                    </ul>
                                </div>
                            </div>
                            <?php
                        } ?>
                    </div>
                </div>
            </li>
        <?php endwhile;
        }else{
            exit('mostViewedPostsReachmax');
        }
    }
    die();
}


/**
 * get most hotely debated posts
 */
add_action('wp_ajax_rz_hotely_debated_posts', 'imit_rz_most_hotely_debated_posts');
add_action('wp_ajax_nopriv_rz_hotely_debated_posts', 'imit_rz_most_hotely_debated_posts');

function imit_rz_most_hotely_debated_posts(){
    global $wpdb;
    $nonce = $_POST['nonce'];
    if(wp_verify_nonce( $nonce, 'rz-most-hotely-debated-posts-nonce' )){
        $page_number = sanitize_key( $_POST['page_num'] );
        $all_discussion_post = $wpdb->get_results("SELECT COUNT(post_id) AS post_count, post_id FROM {$wpdb->prefix}rz_discussion_comments GROUP BY post_id ORDER BY post_count DESC", ARRAY_A);
        $discussion_post_ids = [];
        foreach($all_discussion_post as $discussion_post){
            array_push($discussion_post_ids, $discussion_post['post_id']);
        }
        $discussion_post  = new WP_Query(array(
            'post_type' => 'rz_discussion',
            'posts_per_page' => 10,
            'post__in' => $discussion_post_ids,
            'orderby' => 'post__in',
            'paged' => $page_number
        ));
        if($discussion_post->have_posts(  )){
            while($discussion_post->have_posts()):$discussion_post->the_post();

            $user_id = get_the_author_meta('ID');

            $get_user_profile_data = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}rz_user_profile_data WHERE user_id = '$user_id'");
            $user_data = get_userdata($user_id);
            ?>
            <li class="blog-list list-unstyled mt-3" id="dis-post<?php echo get_the_ID(); ?>">
                <div class="card rz-border rz-br">
                    <div class="card-body p-4">
                        <div class="blog-list-header d-flex flex-row justify-content-between align-items-center">
                            <div class="user-info d-flex flex-row justify-content-start align-items-center" id="discuss-action<?php echo get_the_ID(); ?>">
                                <div class="profile-image">
                                    <img src="<?php getProfileImageById($user_id); ?>" alt="">
                                </div>
                                <div class="userdetails ms-2">
                                    <?php
                                    if(!empty($user_data->user_firstname) && !empty($user_data->user_lastname)){
                                        ?>
                                        <a href="<?php echo site_url().'/user/'.$user_data->user_login; ?>" class="name imit-font fz-16 text-dark fw-500 d-block"><?php echo ucfirst($user_data->user_firstname).' '.ucfirst($user_data->user_lastname); ?></a>
                                        <?php
                                    }else{
                                        ?>
                                        <a href="<?php echo site_url().'/user/'.$user_data->user_login; ?>" class="name imit-font fz-16 text-dark fw-500 d-block"><?php echo $user_data->display_name; ?></a>
                                        <?php
                                    }
                                    ?>

                                    <?php if(!empty($get_user_profile_data->occupation)){
                                        ?>
                                        <p class="mb-0 designation imit-font fw-400 rz-secondary-color fz-12"><?php echo $get_user_profile_data->occupation; ?></p>
                                        <?php
                                    }?>
                                </div>
                            </div>
                            <p class="mb-0 imit-font fz-14 rz-secondary-color fw-400">Posted on: <?php the_time('g a F d, Y'); ?></p>
                        </div>
                        <div class="blog-body">
                            <a href="<?php the_permalink(); ?>" class="my-3 title imit-font text-dark fw-500 d-block"><?php the_title(); ?> </a>
                            <p class="description imit-font fz-14 rz-secondary-color"><?php echo wp_trim_words(get_the_content(), 100, false); ?></p>
                            <?php if(str_word_count(get_the_content()) > 100){
                                ?>
                                <a href="<?php the_permalink( ); ?>" class="imit-font fz-16 rz-color fw-500 d-block mb-3">Read More</a>
                                <?php
                            } ?>
                            <?php the_post_thumbnail('full', ['class' => 'img-fluid mb-3']); ?>
                            <ul class="tags ps-0 mb-0 d-flex flex-row justify-content-start align-items-center">
                                <?php
                                $tags = wp_get_post_terms(get_the_ID(), 'discussion_tags');
                                foreach($tags as $tag){
                                    echo '<li class="tag-list list-unstyled"><a href="'.get_term_link($tag->term_id, 'discussion_tags').'" class="tag-link imit-font fz-12 d-block me-2 rz-secondary-color rounded border px-1">'.$tag->name.'</a></li>';
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                    <div class="card-footer border-top-0 d-flex flex-row justify-content-between align-items-center p-3">
                        <ul class="blog-footer-icons ps-0 mb-0 d-flex flex-row justify-content-start align-items-center" id="discuss-action<?php echo get_the_ID(); ?>">
                            <?php
                            $post_id = get_the_ID();
                            $user_id = get_current_user_id();
                            $get_all_up_like = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_discuss_likes WHERE post_id='$post_id' AND user_id = '$user_id' AND like_type = 'up-like'", ARRAY_A);
                            $get_all_down_like = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_discuss_likes WHERE post_id='$post_id' AND user_id = '$user_id' AND like_type = 'down-like'", ARRAY_A);

                            $count_discuss_up_like = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_discuss_likes WHERE post_id='$post_id' AND like_type = 'up-like'", ARRAY_A);
                            $count_discuss_down_like = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_discuss_likes WHERE post_id='$post_id' AND like_type = 'down-like'", ARRAY_A);
                            ?>
                            <li class="blog-footer-list list-unstyled">
                                <a href="#" class="prev imit-font fz-12 text-dark fw-400 me-3 <?php if(count($get_all_up_like)){echo 'active';} ?>" id="discuss-up-like" data-post_id="<?php echo get_the_ID(); ?>"><i class="fas fa-arrow-up"></i></a>
                            </li>
                            <li class="blog-footer-list list-unstyled">
                                <span class="counter imit-font fz-16 fw-500 me-3 <?php if((count($count_discuss_up_like) - count($count_discuss_down_like)) < 0){echo 'text-danger';}else{echo 'text-success';} ?>" id="discuss-like-counter<?php echo get_the_ID(); ?>"><?php echo count($count_discuss_up_like) - count($count_discuss_down_like); ?></span>
                            </li>
                            <li class="blog-footer-list list-unstyled">
                                <a href="#" class="next imit-font fz-12 text-dark fw-400 me-3 <?php if(count($get_all_down_like)){echo 'active';} ?>" id="discuss-down-like" data-post_id="<?php echo get_the_ID(); ?>"><i class="fas fa-arrow-down"></i></a>
                            </li>
                            <li class="blog-footer-list list-unstyled">
                                <a href="#" class="Visitor imit-font fz-14 text-dark fw-400 me-3"><i class="fas fa-eye"></i> <?php echo getPostViews(get_the_ID()); ?></a>
                            </li>
                            <li class="blog-footer-list list-unstyled">
                                <a href="<?php the_permalink(); ?>" class="comments imit-font fz-14 text-dark fw-400 me-3"><i class="fas fa-comments"></i> Comments</a>
                            </li>
                        </ul>
                        <?php
                        $post_author = get_the_author_meta( 'ID' );
                        if(is_user_logged_in() && $post_author == get_current_user_id() ){
                            ?>
                            <div class="other text-dark d-flex flex-row justify-content-end align-items-center">
                            <a href="#" class="text-dark fz-16 me-3" id="share-post-data" data-post_url="<?php the_permalink(); ?>" data-bs-toggle="modal" data-bs-target="#share-post-modal"><i class="fas fa-share"></i></a>
                                <div class="dropdown">
                                    <a class="text-dark fz-16" href="#" role="button" id="more-option-feed" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-ellipsis-h"></i>
                                    </a>

                                    <ul class="dropdown-menu" aria-labelledby="more-option-feed">
                                        <li><a class="dropdown-item imit-font fz-14 text-dark" href="#" id="delete-discuss-post" data-post_id="<?php echo get_the_ID(); ?>">Delete</a></li>
                                    </ul>
                                </div>
                            </div>
                            <?php
                        } ?>
                    </div>
                </div>
            </li>
        <?php endwhile;
        }else{
            exit('mostHotelyDebatedReachmax');
        }
    }
    die();
}

/**
 * delete discuss post
 */
add_action('wp_ajax_rz_delete_discuss_post', function(){
   global $wpdb;
    $nonce = $_POST['nonce'];
    if(wp_verify_nonce($nonce, 'rz-delete-discuss-post-nonce')){
        $post_id = sanitize_key($_POST['post_id']);
        $user_id = get_current_user_id();

        if(!empty($post_id) && !empty($user_id)){
            $wpdb->query("DELETE FROM {$wpdb->prefix}rz_discuss_reply_likes WHERE reply_id IN (SELECT id FROM {$wpdb->prefix}rz_discuss_comment_replays WHERE comment_id IN (SELECT id FROM {$wpdb->prefix}rz_discussion_comments WHERE post_id = '{$post_id}'))");

            $wpdb->query("DELETE FROM {$wpdb->prefix}rz_discuss_comment_replays WHERE comment_id IN (SELECT id FROM {$wpdb->prefix}rz_discussion_comments WHERE post_id = '{$post_id}')");

            $wpdb->query("SELECT * FROM {$wpdb->prefix}rz_discuss_comment_likes WHERE comment_id IN (SELECT id FROM {$wpdb->prefix}rz_discussion_comments WHERE post_id = '{$post_id}')");

            $wpdb->delete($wpdb->prefix.'rz_discussion_comments', [
                    'post_id' => $post_id
            ]);

            $wpdb->delete($wpdb->prefix.'rz_discuss_likes', [
                    'post_id' => $post_id
            ]);

            wp_delete_attachment($post_id, false);

            wp_delete_post($post_id, false);

            exit('done');
        }
    }
   die();
});