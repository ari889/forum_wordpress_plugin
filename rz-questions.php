<?php


/**
 * direct access not allowed
 */
if (!defined('ABSPATH')) {
    die(__('Direct access not allowed.', 'imit-recozilla'));
}


/**
 * for question page
 */
add_shortcode('imit-questions', function () {
    global $wpdb;
    ob_start();



    if (is_user_logged_in()) {
        $rz_user_profile_data = $wpdb->prefix . 'rz_user_profile_data';
        $current_user_id = get_current_user_id();
        $profile_data = $wpdb->get_row("SELECT * FROM {$rz_user_profile_data} WHERE user_id = '{$current_user_id}'");

        if (empty($profile_data)) {
            $banner_status = 'active';
        } else {
            $banner_status = $profile_data->banner_status;
        }

        if ($banner_status == 'active') {
?>
            <section class="imit-home-banner py-4" style="background-image: url('<?php echo plugins_url('images/Rectangle 94.png', __FILE__); ?>')">
                <div class="rz-mid position-relative">
                    <button type="button" class="dismiss-button rz-color fz-20 p-0" id="dismiss-banner"><i class="fas fa-times-circle"></i></button>
                    <p class="title imit-font fw-400 fz-20 mb-0">Hello</p>
                    <p class="description imit-font fw-400 fz-20 mb-0">Sodales elit ac dui integer ut. Bibendum fusce sed mauris ullamcorper. Dolor ut eu pretium faucibus. Dui magna quis neque gravida fames risus. Eu in nunc eu, tristique. Arcu eget ornare auctor nec faucibus sed dui ornare. Blandit sit etiam sed pharetra. Non in pharetra, massa, nisi, tellus sit porttitor. Ultrices tristique sem enim cum in. Et condimentum enim massa in.</p>
                    <p class="signature text-end rz-color">-Team Recozilla</p>
                </div>
            </section>
    <?php
        }
    }
    ?>

    <section class="imit-questions py-4">
        <div class="rz-mid">
            <div class="row mx-lg-0 mx-1">
                <div class="col-lg-9">
                    <div class="card activity-card rounded-3 rz-br">
                        <div class="card-header border-0">
                            <h3 class="rz-color fz-20 m-0 imit-font fw-500">Activity Wall</h3>
                        </div>
                        <div class="card-body p-0">
                            <ul class="mb-0 ps-0 activity-list" id="show_user_activity">

                            </ul>
                        </div>
                    </div>

                    <div class="hidden-partner-program mt-3 d-lg-none d-block">
                        <?php echo do_shortcode('[join-partner-program]'); ?>
                    </div>


                    <div class="imit-tabs rz-bg-color p-3 rounded mt-3">
                        <div class="d-flex flex-row justify-content-between align-items-center">
                            <ul class="tab-menu ps-0 mb-0 d-flex flex-row justify-content-between align-items-center">
                                <li class="tab-list list-unstyled">
                                    <a href="#" class="tab-link imit-font text-white fz-14 active d-flex flex-row justify-content-center align-items-center" data-target="news-feed"><span class="me-1">Your</span> <span>Feed</span></a>
                                </li>
                                <li class="tab-list list-unstyled">
                                    <a href="#" class="tab-link imit-font text-white fz-14 d-flex flex-row justify-content-center align-items-center" data-target="new-questions"><span class="me-1">New</span> <span>Questions</span></a>
                                </li>
                                <li class="tab-list list-unstyled">
                                    <a href="#" class="tab-link imit-font text-white fz-14 d-flex flex-row justify-content-center align-items-center" data-target="popular-questions"><span class="me-1">Popular</span> <span>Questions</span></a>
                                </li>
                                <li class="tab-list list-unstyled">
                                    <a href="#" class="tab-link imit-font text-white fz-14 d-flex flex-row justify-content-center align-items-center" data-target="most-answered"><span class="me-1">Most</span> <span class="me-1">Answered</span> <span>Questions</span></a>
                                </li>
                                <?php
                                $tab_tags = get_terms(array(
                                    'taxonomy' => 'question_tags',
                                    'orderby' => 'count',
                                    'order' => 'DESC',
                                    'number' => 10
                                ));
                                foreach ($tab_tags as $tag) {
                                ?>
                                    <li class="tab-list list-unstyled">
                                        <a href="#" class="tab-link imit-font text-white fz-14 d-flex flex-row justify-content-center align-items-center" data-target="<?php echo $tag->slug; ?>"><?php echo ucfirst($tag->name); ?></a>
                                    </li>
                                <?php
                                }


                                ?>
                            </ul>
                            <div class="dropdown custom-dropdown">
                                <button class="p-0 bg-transparent border-0 text-white fz-16" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-ellipsis-h"></i>
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                    <li class="imit-dropdown-tabs">
                                        <a href="#" class="dropdown-item tab-link" data-target="new-questions">New Questions</a>
                                    </li>
                                    <li class="imit-dropdown-tabs">
                                        <a href="#" class="dropdown-item tab-link" data-target="popular-questions">Popular Questions</a>
                                    </li>
                                    <li class="imit-dropdown-tabs">
                                        <a href="#" class="dropdown-item tab-link" data-target="most-answered">Most Answered Questions</a>
                                    </li>
                                    <?php
                                    $tab_tags = get_terms(array(
                                        'taxonomy' => 'question_tags',
                                        'orderby' => 'count',
                                        'order' => 'DESC',
                                        'number' => 10
                                    ));
                                    $qt = 0;
                                    foreach ($tab_tags as $tag) {
                                    ?>
                                        <li class="imit-dropdown-tabs"><a class="dropdown-item tab-link <?php echo (($qt == 0 || $qt == 1) ? 'question-dropdown-tab' : ''); ?>" href="#" data-target="<?php echo $tag->slug; ?>"><?php echo ucfirst($tag->name); ?></a></li>
                                    <?php
                                        $qt++;
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="tab-content" id="news-feed">
                        <?php
                        $get_feature_post = new WP_Query([
                            'post_type' => 'rz_post_question',
                            'posts_per_page' => 1,
                            'tax_query' =>  array(
                                array(
                                    'taxonomy' => 'question_category',
                                    'field' => 'slug',
                                    'terms' => 'feature-post'
                                )
                            ),
                            'orderby' => 'ID',
                            'order' => 'ASC'
                        ]);
                        if ($get_feature_post->have_posts()) {
                        ?>
                            <ul class="imit-news-feed mb-0 p-4 rz-light-bg rz-br mt-3">
                                <li class="d-flex flex-row justify-content-between align-items-center mb-3">
                                    <div class="d-flex flex-row justifu-content-start align-items-center">
                                        <img src="<?php echo plugins_url('images/Group (1).png', __FILE__); ?>" alt="">
                                        <h2 class="imit-font rz-color m-0 ms-2" style="font-size: 24px;">Question of the hour</h2>
                                    </div>
                                    <p class="mb-0 rz-color imit-font fz-14 fw-500">Next question of the hour in <?php echo human_time_diff(wp_next_scheduled('um_hourly_scheduled_events'), time()); ?></p>
                                </li>
                                <?php
                                while ($get_feature_post->have_posts()) : $get_feature_post->the_post();
                                    $post_id = get_the_ID();
                                    $user_id = get_the_author_meta('ID');
                                    $answer_count = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_answers WHERE post_id = '$post_id' AND user_id != '$user_id'", ARRAY_A);
                                ?>
                                    <li class="news-feed-list mt-3">
                                        <div class="card rz-br">
                                            <div class="card-body p-0">
                                                <div class="p-4">
                                                    <div class="created-at rz-secondary-color fz-14 imit-font">Asked on: <?php the_time('g:i a F d, Y'); ?></div>
                                                    <a href="<?php the_permalink(); ?>" class="question-title imit-font fw-500 text-decoration-none text-dark rz-lh-38"><span class="rz-color mr-1">Q.</span> <?php the_title(); ?></a>
                                                    <div class="rz-br my-3">
                                                        <?php the_post_thumbnail('full', ['img-fluid']); ?>
                                                    </div>
                                                    <ul class="tags ps-0">
                                                        <?php
                                                        $tags = wp_get_post_terms(get_the_ID(), 'question_tags');
                                                        foreach ($tags as $tag) {
                                                            echo '<li class="tag-list d-inline-block"><a href="' . get_term_link($tag->term_id, 'question_tags') . '" class="tag-link imit-font fz-12 fw-500 rounded">' . $tag->name . '</a></li>';
                                                        }
                                                        ?>
                                                    </ul>
                                                    <div class="d-flex flex-sm-row flex-column justify-content-start align-items-start align-items-sm-center mt-3">
                                                        <a <?php echo ((is_user_logged_in()) ? 'href="' . get_the_permalink() . '?ref=answer#answer-form"' : 'href="#" data-bs-toggle="modal" data-bs-target="#login-modal"'); ?> class="answer-button imit-font fz-14 rz-color fw-500" id="comment-button"><?php echo ((count($answer_count) <= 0) ? 'Be first to write answer' : 'Write answer'); ?></a>
                                                        <?php
                                                        if (isUserAlreadyPartner(get_current_user_id())) {
                                                            if (count($answer_count) >= 1) {
                                                        ?>
                                                                <span class="point-badge imit-font fz-14 fw-500 mt-sm-0 mt-2"><img src="<?php echo plugins_url('images/Group (3).png', __FILE__); ?>" alt=""> <span class="rz-secondary-color">10 Points</span></span>
                                                            <?php
                                                            } else {
                                                            ?>
                                                                <span class="point-badge imit-font fz-14 fw-500 mt-sm-0 mt-2"><img src="<?php echo plugins_url('images/Group.png', __FILE__); ?>" alt=""> <span class="rz-secondary-color">20 Points</span></span>
                                                        <?php
                                                            }
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-footer p-3 border-top-0 d-flex flex-row justify-content-between align-items-center">
                                                <div class="views text-dark fz-14">
                                                    <i class="fas fa-eye"></i>
                                                    <span class="counter imit-font fw-500"><span class="counter"><?php echo getPostViews(get_the_ID()) ?></span></span>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                <?php endwhile; ?>
                            </ul>
                        <?php
                        }
                        ?>
                        <ul class="imit-news-feed ps-0 mb-0 runded-3" id="news-feed-ul"></ul>
                    </div>

                    <!-- for new questions -->
                    <div class="tab-content" id="new-questions">
                        <ul class="imit-news-feed ps-0 mb-0 runded-3" id="new-questions-ul"></ul>
                    </div>

                    <!-- for popular questions -->
                    <div class="tab-content" id="popular-questions">
                        <ul class="imit-news-feed ps-0 mb-0 runded-3" id="popular-questions-ul"></ul>
                    </div>

                    <!-- for most answered -->
                    <div class="tab-content" id="most-answered" style="display: none;">
                        <ul class="imit-news-feed ps-0 mb-0 runded-3" id="most-answered-ul">

                        </ul>
                    </div>


                    <!-- for tag questions -->
                    <?php
                    foreach ($tab_tags as $tag) {
                    ?>
                        <div class="tab-content" id="<?php echo $tag->slug; ?>" style="display: none;">
                            <ul class="imit-news-feed ps-0 mb-0 runded-3" id="<?php echo $tag->slug; ?>-ul">

                            </ul>
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
                <div class="col-lg-3 mt-lg-0 mt-3">

                    <div class="d-lg-block d-none">
                        <?php echo do_shortcode('[join-partner-program]'); ?>
                    </div>


                    <?php

                    $user_id = get_current_user_id();
                    $is_user_already_a_partner = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_user_programs WHERE user_id = '$user_id' AND (status = '1' OR status = '0')", ARRAY_A);
                    ?>
                    <div class="card <?php echo (is_user_logged_in() == false || count($is_user_already_a_partner) < 1) ? 'mt-3' : ''; ?> rz-border rz-br trending-tags">
                        <div class="card-header rz-bg-color border-bottom-0 p-3">
                            <a href="<?php echo site_url(); ?>/tags" class="trending-title text-white fz-14 fw-500 m-0 d-flex flex-row justify-content-start align-items-center"><span class="fz-16 me-2" style="font-size: 25px !important;line-height: 0;">#</span> <span>Trending Tags</span></a>
                        </div>
                        <div class="card-body p-0">
                            <ul class="hash-tags ps-0 mb-0">

                                <?php
                                $tags = get_terms(array(
                                    'taxonomy' => 'question_tags',
                                    'orderby' => 'count',
                                    'order' => 'DESC',
                                    'number' => 7
                                ));

                                foreach ($tags as $tag) {
                                    $posts_array = get_posts(
                                        array(
                                            'posts_per_page' => -1,
                                            'post_type' => 'rz_post_question',
                                            'fields' => 'ids',
                                            'tax_query' => array(
                                                array(
                                                    'taxonomy' => 'question_tags',
                                                    'field' => 'term_id',
                                                    'terms' => $tag->term_id,
                                                )
                                            )
                                        )
                                    );

                                    $count_answer = 0;
                                    $total_view = 0;
                                    foreach ($posts_array as $post_ids) {
                                        $all_answers = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_answers WHERE post_id = '$post_ids'", ARRAY_A);

                                        $count_answer += count($all_answers);

                                        $count_key = 'post_views_count';
                                        $count = get_post_meta($post_ids, $count_key, true);
                                        if ($count == '') {
                                            delete_post_meta($post_ids, $count_key);
                                            add_post_meta($post_ids, $count_key, '0');
                                            $total_view += 0;
                                        }
                                        $total_view += $count;
                                    }
                                    $term_id = $tag->term_id;
                                    $user_id = get_current_user_id();
                                    $rz_following_tags = $wpdb->prefix . 'rz_following_tags';
                                    $is_user_already_followed = $wpdb->get_row("SELECT * FROM {$rz_following_tags} WHERE user_id = '{$user_id}' AND term_id = '{$term_id}'");
                                ?>
                                    <li class="hash-list list-unstyled m-3">
                                        <div class="hash-top d-flex flex-row justify-content-between align-items-center">
                                            <a href="<?php echo get_term_link($tag->term_id, 'question_tags'); ?>" class="imit-font fw-500 fz-16 text-dark d-block">#<?php echo $tag->name; ?></a>
                                            <button type="button" class="add-post-by-tag p-0 <?php if (!empty($is_user_already_followed)) {
                                                                                                    echo 'rz-color';
                                                                                                } else {
                                                                                                    echo 'rz-secondary-color';
                                                                                                } ?> bg-transparent fz-14 border-0" data-term_id="<?php echo $tag->term_id; ?>" id="follow-tag"><?php if (!empty($is_user_already_followed)) {
                                                                                                                                                                                                    echo '<i class="fas fa-check-square"></i>';
                                                                                                                                                                                                } else {
                                                                                                                                                                                                    echo '<i class="fas fa-plus-circle"></i>';
                                                                                                                                                                                                } ?></button>
                                        </div>
                                        <div class="d-flex flex-row justify-content-between align-items-center">
                                            <p class="rz-secondary-color imit-font fz-12 fw-400 mb-0"><?php echo $tag->count; ?> <?php echo (($tag->count > 1) ? 'Questions' : 'Question'); ?></p>
                                            <p class="rz-secondary-color imit-font fz-12 fw-400 mb-0"><?php echo $count_answer; ?> <?php echo (($count_answer > 1) ? 'Answers' : 'Answer'); ?></p>
                                            <p class="rz-secondary-color imit-font fz-12 fw-400 mb-0"><?php echo $total_view; ?> <?php echo (($total_view > 1) ? 'Views' : 'View'); ?></p>
                                        </div>
                                    </li>
                                <?php
                                }
                                ?>
                            </ul>
                            <a href="<?php echo site_url(); ?>/tags" class="d-block mb-3 text-center imit-font fz-16 fw-500 rz-color">See all</a>
                        </div>
                    </div>

                    <!-- footer -->
                    <div class="footer mt-3">
                        <ul class="footer-aside ps-0 mb-0">
                            <li class="footer-list list-unstyled">
                                <a href="<?php echo site_url(); ?>/about-us/" class="footer-link fz-14 imit-font">About</a>
                            </li>
                            <li class="footer-list list-unstyled">
                                <a href="<?php echo site_url(); ?>/contact-us/" class="footer-link fz-14 imit-font">Contact us</a>
                            </li>
                            <li class="footer-list list-unstyled">
                                <a href="<?php echo site_url(); ?>/support/" class="footer-link fz-14 imit-font">Supports</a>
                            </li>
                            <li class="footer-list list-unstyled">
                                <a href="<?php echo site_url(); ?>/terms-conditions/" class="footer-link fz-14 imit-font">Terms of Use</a>
                            </li>
                            <li class="footer-list list-unstyled">
                                <a href="<?php echo site_url(); ?>/privacy-policy/" class="footer-link fz-14 imit-font">Privacy Policy</a>
                            </li>
                        </ul>
                        <p class="mb-0 mt-2 footer-text fz-16 rz-secondary-color imit-font">&copy; 2021 Recozilla. All Rights Reserved</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php
    return ob_get_clean();
});

/**
 * question add popup
 */
add_shortcode('rz-question-popup', function () {
    ob_start();
?>
    <form id="add-question-form" enctype="multipart/form-data">
        <div id="add_question_error" class="mt-4"></div>
        <div class="d-flex flex-row justifu-content-center align-items-center">
            <label for="question-title" class="question-title title-text rz-color me-2">Q</label>
            <input type="text" id="question-title" name="title" class="form-control fz-14 rounded text-dark imit-font" placeholder="Please choose an appropriate title for the question so it can be answered easily.">
        </div>
        <div class="d-flex flex-row justifu-content-center align-items-center mt-5">
            <label for="question-tag" class="imit-font title-text rz-color me-2"><i class="fas fa-tag"></i></label>
            <input type="text" name="tag" id="question-tag" class="form-control fz-14 rounded text-dark imit-font" placeholder="Please choose suitable Keywords Ex: Yoga , travel">
        </div>
        <div class="d-flex flex-row justifu-content-center align-items-center mt-5">
            <label for="question-image" class="imit-font title-text rz-color me-2"><i class="fas fa-image"></i></label>
            <input type="file" name="image" id="question-image" class="form-control fz-14 rounded text-dark imit-font">
        </div>
        <!-- <h3 class="imit-font fz-20 text-dark fw-500 mt-5">Do you want to add an answer as well to this question?</h3>
    
        <textarea name="content" id="" cols="30" rows="10" class="form-control imit-font fz-14 text-dark"></textarea> -->

        <ul class="list-group hashbox" id="rz-hashbox">

        </ul>

        <button type="submit" class="btn rz-bg-color text-white imit-font fz-14 mt-2 ms-auto d-table">Submit</button>
    </form>
    <?php
    return ob_get_clean();
});

/**
 * add question
 */
add_action('wp_ajax_imit_add_question', function () {
    global $wpdb;

    $nonce = $_POST['nonce'];

    if (wp_verify_nonce($nonce, 'rz-add-question-nonce')) {
        $title = sanitize_text_field($_POST['title']);
        $tag = sanitize_text_field($_POST['tag']);
        $image = $_FILES['image']['name'];
        $image_tmp = $_FILES['image']['tmp_name'];
        // $content = sanitize_text_field( $_POST['content'] );
        $user_id = get_current_user_id();

        $exp = explode('.', $image);

        $ext = end($exp);

        $unique_name = md5(time() . rand()) . '.' . $ext;

        $format = ['png', 'jpg', 'gif', 'jpeg'];

        if (empty($title)) {
            $response['message'] = '<div class="alert imit-font fz-16 alert-warning alert-dismissible fade show" role="alert">
            <strong>Warning!</strong> Title required.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
            $response['error'] = true;
        } else if (!empty($image) && in_array($ext, $format) == false) {
            $response['message'] = '<div class="alert imit-font fz-16 alert-danger alert-dismissible fade show" role="alert">
            <strong>Stop!</strong> Invalid Image format.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
            $response['error'] = true;
        } else {
            $image_data = wp_upload_bits($unique_name, null, file_get_contents($image_tmp));

            $image_resize = wp_get_image_editor($image_data['file']);
            if (!is_wp_error($image_resize)) {
                $image_resize->resize(1200, 1200, false);
                $image_resize->save($image_data['file']);
            }

            $my_post = array(
                'post_title'    => wp_strip_all_tags($title),
                'post_type'    => 'rz_post_question',
                'post_status'   => 'publish',
                'post_author'   => $user_id,
                'tags_input'   => $tag,
            );

            // Insert the post into the database
            $post_id = wp_insert_post($my_post);

            $post_tags = explode(',', $tag);

            wp_set_post_terms($post_id, $post_tags, 'question_tags');

            // $filename should be the path to a file in the upload directory.
            $filename = $image_data['url'];

            if (!empty($filename) && !empty($post_id)) {
                // Check the type of file. We'll use this as the 'post_mime_type'.
                $filetype = wp_check_filetype(basename($filename), null);

                // Get the path to the upload directory.
                $wp_upload_dir = wp_upload_dir();

                // Prepare an array of post data for the attachment.
                $attachment = array(
                    'guid'           => $wp_upload_dir['url'] . '/' . basename($filename),
                    'post_mime_type' => $filetype['type'],
                    'post_title'     => preg_replace('/\.[^.]+$/', '', basename($filename)),
                    'post_content'   => '',
                    'post_status'    => 'inherit'
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
            $response['error'] = true;
            '<div class="alert imit-font fz-16 alert-success alert-dismissible fade show" role="alert">
            <strong>Success!</strong> Question added successfully.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
            $response['activity_id'] = 'question-' . $post_id;
            $response['error'] = false;
            $response['redirect'] = get_permalink($post_id, false);
            $get_post_data = get_post($post_id);
            $response['text_message'] = 'New question <span class="imit-font rz-color fw-500">' . $get_post_data->post_title . '</span> asked </span>';
            $response['image_url'] = getProfileImageById(get_current_user_id());
        }

        echo json_encode($response);
    }
    die();
});

/**
 * fetch news feed data
 */
add_action('wp_ajax_nopriv_rz_news_feed_data', 'imit_news_feed_data');
add_action('wp_ajax_rz_news_feed_data', 'imit_news_feed_data');

function imit_news_feed_data()
{
    global $wpdb;
    $nonce = $_POST['nonce'];
    if (wp_verify_nonce($nonce, 'rz-news-feed-posts-nonce')) {
        $page_num = sanitize_key($_POST['page_num']);
        $questions_data = new WP_Query([
            'post_type' => 'rz_post_question',
            'posts_per_page' => 20,
            'paged' => $page_num,
            'tax_query' =>  array(
                array(
                    'taxonomy' => 'question_category',
                    'field' => 'slug',
                    'operator' => 'NOT IN',
                    'terms' => 'feature-post'
                )
            ),
        ]);
        if ($questions_data->have_posts()) {
            while ($questions_data->have_posts()) : $questions_data->the_post();
                $post_id = get_the_ID();
                $user_id = get_the_author_meta('ID');
                $answer_count = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_answers WHERE post_id = '$post_id'", ARRAY_A);
                $current_user = get_current_user_id();
                $rz_following_questions = $wpdb->prefix . 'rz_following_questions';
                $is_user_already_followed_question = $wpdb->get_row("SELECT * FROM {$rz_following_questions} WHERE user_id = '{$current_user}' AND question_id = '{$post_id}'");
    ?>
                <li class="news-feed-list mt-3" id="question<?php echo $post_id; ?>">
                    <div class="card rz-br rz-border">
                        <div class="card-body p-0">
                            <div class="p-4">
                                <div class="d-flex flex-row justify-content-between align-items-center">
                                    <div class="created-at rz-secondary-color fz-14 imit-font">Asked on: <?php the_time('g:i a F d, Y'); ?></div>
                                </div>
                                <a href="<?php the_permalink(); ?>" class="question-title imit-font fw-500 text-decoration-none text-dark rz-lh-38 d-block"><span class="rz-color mr-1">Q.</span> <?php the_title(); ?></a>
                                <div class="rz-br">
                                    <?php the_post_thumbnail('full', ['img-fluid']); ?>
                                </div>
                                <ul class="tags ps-0">
                                    <?php
                                    $tags = wp_get_post_terms(get_the_ID(), 'question_tags');
                                    foreach ($tags as $tag) {
                                        echo '<li class="tag-list d-inline-block"><a href="' . get_term_link($tag->term_id, 'question_tags') . '" class="tag-link imit-font fz-12 fw-500 rounded">' . $tag->name . '</a></li>';
                                    }
                                    ?>
                                </ul>

                                <div class="d-flex flex-sm-row flex-column justify-content-start align-items-start align-items-sm-center mt-3">
                                    <a <?php echo ((is_user_logged_in()) ? 'href="' . get_the_permalink() . '?ref=answer#answer-form"' : 'href="#" data-bs-toggle="modal" data-bs-target="#login-modal"'); ?> class="answer-button imit-font fz-14 rz-color fw-500" id="comment-button"><?php echo ((count($answer_count) <= 0) ? 'Be first to write answer' : 'Write answer'); ?></a>
                                    <?php
                                    if (isUserAlreadyPartner(get_current_user_id())) {
                                        if (count($answer_count) >= 1) {
                                    ?>
                                            <span class="point-badge imit-font fz-14 fw-500 mt-sm-0 mt-2"><img src="<?php echo plugins_url('images/Group (3).png', __FILE__); ?>" alt=""> <span class="rz-secondary-color">10 Points</span></span>
                                        <?php
                                        } else {
                                        ?>
                                            <span class="point-badge imit-font fz-14 fw-500 mt-sm-0 mt-2"><img src="<?php echo plugins_url('images/Group.png', __FILE__); ?>" alt=""> <span class="rz-secondary-color">20 Points</span></span>
                                    <?php
                                        }
                                    }
                                    ?>
                                </div>
                            </div>

                            <?php
                            $all_answers = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_answers WHERE post_id = '$post_id' AND status = '1'", ARRAY_A);
                            if (count($all_answers) > 0) {
                                $get_first_answer = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}rz_answers WHERE post_id = '$post_id' ORDER BY id ASC LIMIT 1");
                                $get_profile_data = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}rz_user_profile_data WHERE user_id = '{$get_first_answer->user_id}'");
                                $get_user = get_userdata($get_first_answer->user_id);
                            ?>
                                <div class="question-first-answer">
                                    <div class="answer-info d-flex flex-row justify-content-between align-items-center py-2 px-4">
                                        <a href="<?php the_permalink(); ?>" class="mb-0 rz-color imit-font count-answer rz-color fw-500"><?php echo count($all_answers); ?> <?php echo ((count($all_answers) > 1) ? 'Answers' : 'Answer'); ?></a>
                                        <a href="<?php the_permalink(); ?>" class="imit-font rz-color fw-500 fz-16">See all</a>
                                    </div>
                                    <ul class="answers py-3 px-4 mb-0">
                                        <li class="answer-list list-unstyled">
                                            <div class="answer-header border-bottom-0 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center align-items-start">
                                                <div class="user-data d-flex flex-row justify-content-start align-items-center">
                                                    <a href="<?php echo site_url() . '/user/' . $get_user->user_login; ?>" class="profile-image d-block">
                                                        <img src="<?php echo getProfileImageById($get_first_answer->user_id); ?>" alt="">
                                                    </a>
                                                    <div class="user-info ms-2">
                                                        <a href="<?php echo site_url() . '/user/' . $get_user->user_login; ?>" class="imit-font fz-16"><?php echo getUserNameById($get_first_answer->user_id); ?></a>
                                                        <p class="mb-0 rz-secondary-color imit-font fz-12"><?php echo $get_profile_data->occupation; ?></p>
                                                    </div>
                                                </div>
                                                <p class="rz-secondary-color imit-font fz-14 mb-0 mt-sm-0 mt-2">Answered: <?php echo date('g:i a F d, Y', strtotime($get_first_answer->created_at)); ?></p>
                                            </div>
                                            <div class="answer-body mt-3 d-flex flex-row justify-content-start align-items-start">
                                                <?php
                                                $current_user_id = get_current_user_id();
                                                $answer_id = $get_first_answer->id;
                                                $get_upvote = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_vote WHERE user_id = '$current_user_id' AND answer_id='$answer_id' AND vote_type='up-vote'", ARRAY_A);
                                                $get_downvote = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_vote WHERE user_id = '$current_user_id' AND answer_id='$answer_id' AND vote_type='down-vote'", ARRAY_A);

                                                $count_upvote = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_vote WHERE answer_id='$answer_id' AND vote_type='up-vote'", ARRAY_A);
                                                ?>
                                                <ul class="body-left d-flex flex-column justify-content-start align-items-center ps-0 mb-0" id="vote<?php echo $answer_id; ?>">
                                                    <li class="list-unstyled">
                                                        <a href="#" class="vote d-block <?php echo ((count($get_upvote) > 0) ? 'active' : ''); ?>" <?php echo ((is_user_logged_in()) ? 'data-answer_id="' . $answer_id . '" id="up-vote"' : 'data-bs-toggle="modal" data-bs-target="#login-modal"'); ?>><i class="fas fa-arrow-up"></i></a>
                                                    </li>
                                                    <li class="list-unstyled">
                                                        <p class="counter fz-16 imit-font my-1 text-dark" id="counter<?php echo $answer_id; ?>"><?php echo count($count_upvote); ?></p>
                                                    </li>
                                                    <li class="list-unstyled">
                                                        <a href="#" class="vote d-block <?php echo ((count($get_downvote) > 0) ? 'active' : ''); ?>" <?php echo ((is_user_logged_in()) ? 'data-answer_id="' . $answer_id . '" id="down-vote"' : 'data-bs-toggle="modal" data-bs-target="#login-modal"'); ?>><i class="fas fa-arrow-down"></i></a>
                                                    </li>
                                                </ul>
                                                <div class="answer-details ms-3">
                                                    <?php
                                                    if (str_word_count($get_first_answer->answer_text) > 40) {
                                                    ?>
                                                        <p class="imit-font fz-16 answer-text" style="line-height: 30px;" id="answer-short-text<?php echo $get_first_answer->id; ?>"><?php echo wp_trim_words($get_first_answer->answer_text, 40, ' ...'); ?></p>
                                                        <a href="<?php the_permalink(); ?>" class="rz-color fw-500 imit-font fz-16">Read More</a>
                                                    <?php
                                                    } else {
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
                        <div class="card-footer border-top-0 p-3 d-flex flex-row justify-content-between align-items-center">
                            <div class="views text-dark fz-14">
                                <i class="fas fa-eye"></i>
                                <span class="counter imit-font fw-500"><span class="counter"><?php echo getPostViews(get_the_ID()) ?></span></span>
                            </div>
                            <div class="other text-dark d-flex flex-row justify-content-end align-items-center">
                                <div class="dropdown share-dropdown">
                                    <a href="#" class="text-dark fz-16 me-3" data-bs-toggle="dropdown" aria-expanded="false" id="share-post-data"><i class="fas fa-share"></i></a>
                                    <div class="dropdown-menu border-0 p-0 shadow" aria-labelledby="share-post-data">
                                        <ul class="ps-0 mb-0 d-flex flex-row justify-content-center align-items-center">
                                            <li class="list-unstyled"><a href="#" class="share-link" id="share-facebook" data-shareurl="<?php the_permalink(); ?>"><i class="fab fa-facebook-f"></i></a></li>
                                            <li class="list-unstyled"><a href="#" class="share-link" id="tweet-data" data-shareurl="<?php the_permalink(); ?>"><i class="fab fa-twitter"></i></a></li>
                                            <li class="list-unstyled"><a href="#" class="share-link" id="share-linkedin" data-shareurl="<?php the_permalink(); ?>"><i class="fab fa-linkedin"></a></i></li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="dropdown custom-dropdown">
                                    <a class="text-dark fz-16" href="#" role="button" id="more-option-feed" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-ellipsis-h"></i>
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="more-option-feed">
                                        <?php if (is_user_logged_in() && $user_id === get_current_user_id()) { ?>
                                            <li><a class="dropdown-item imit-font fz-14 text-dark" href="#" id="delete-question" data-question_id="<?php echo $post_id; ?>"><i class="fas fa-trash me-1"></i>Delete</a></li>
                                        <?php } ?>
                                        <li><a class="dropdown-item imit-font fz-14 text-dark <?php echo ((!empty($is_user_already_followed_question)) ? 'active' : ''); ?>" href="#" <?php echo (is_user_logged_in()) ? 'id="follow-question" data-question_id="' . $post_id . '"' : 'data-bs-toggle="modal" data-bs-target="#login-modal"' ?>><?php echo ((!empty($is_user_already_followed_question)) ? '<i class="fas fa-check-square me-1"></i> Following' : '<i class="fas fa-plus-circle me-1"></i> Follow'); ?></a></li>
                                    </ul>
                                </div>

                            </div>


                        </div>
                    </div>
                </li>
            <?php endwhile;

            wp_reset_postdata();
        } else {
            exit('newsReachmax');
        }
    }
    die();
}

/**
 * new questions data
 */
add_action('wp_ajax_rz_get_new_question_data', 'get_all_new_questions_filter');
add_action('wp_ajax_nopriv_rz_get_new_question_data', 'get_all_new_questions_filter');

function get_all_new_questions_filter()
{
    global $wpdb;
    $nonce = $_POST['nonce'];
    if (wp_verify_nonce($nonce, 'rz-get-new-questions-nonce')) {
        $get_all_answers_posts_ids = $wpdb->get_results("SELECT DISTINCT post_id FROM {$wpdb->prefix}rz_answers", ARRAY_A);

        $post_ids = [];
        foreach ($get_all_answers_posts_ids as $post_data) {
            array_push($post_ids, $post_data['post_id']);
        }


        $page_num = sanitize_key($_POST['page_num']);
        $questions_data = new WP_Query([
            'post_type' => 'rz_post_question',
            'posts_per_page' => 20,
            'paged' => $page_num,
            'post__not_in' => $post_ids,
            'tax_query' =>  array(
                array(
                    'taxonomy' => 'question_category',
                    'field' => 'slug',
                    'operator' => 'NOT IN',
                    'terms' => 'feature-post'
                )
            ),
        ]);
        if ($questions_data->have_posts()) {
            while ($questions_data->have_posts()) : $questions_data->the_post();
                $post_id = get_the_ID();
                $user_id = get_the_author_meta('ID');
                $answer_count = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_answers WHERE post_id = '$post_id' AND status = '1'", ARRAY_A);
                $current_user = get_current_user_id();
                $rz_following_questions = $wpdb->prefix . 'rz_following_questions';
                $is_user_already_followed_question = $wpdb->get_row("SELECT * FROM {$rz_following_questions} WHERE user_id = '{$current_user}' AND question_id = '{$post_id}'");
            ?>
                <li class="news-feed-list mt-3" id="question<?php echo $post_id; ?>">
                    <div class="card rz-br rz-border">
                        <div class="card-body p-0">
                            <div class="p-4">
                                <div class="d-flex flex-row justify-content-between align-items-center">
                                    <div class="created-at rz-secondary-color fz-14 imit-font">Asked on: <?php the_time('g:i a F d, Y'); ?></div>
                                    <?php if (is_user_logged_in()) { ?>
                                        <button type="button" class="border-0 <?php echo ((!empty($is_user_already_followed_question)) ? 'rz-color' : 'rz-secondary-color'); ?> fz-14 p-0 bg-transparent" id="follow-question" data-question_id="<?php echo $post_id; ?>">
                                            <?php echo ((!empty($is_user_already_followed_question)) ? '<i class="fas fa-check-square"></i>' : '<i class="fas fa-plus-circle"></i>'); ?>
                                        </button>
                                    <?php } ?>
                                </div>
                                <a href="<?php the_permalink(); ?>" class="question-title imit-font fw-500 text-decoration-none text-dark rz-lh-38 d-block"><span class="rz-color mr-1">Q.</span> <?php the_title(); ?></a>
                                <div class="rz-br">
                                    <?php the_post_thumbnail('full', ['img-fluid']); ?>
                                </div>
                                <ul class="tags ps-0">
                                    <?php
                                    $tags = wp_get_post_terms(get_the_ID(), 'question_tags');
                                    foreach ($tags as $tag) {
                                        echo '<li class="tag-list d-inline-block"><a href="' . get_term_link($tag->term_id, 'question_tags') . '" class="tag-link imit-font fz-12 fw-500 rounded">' . $tag->name . '</a></li>';
                                    }
                                    ?>
                                </ul>

                                <div class="d-flex flex-sm-row flex-column justify-content-start align-items-start align-items-sm-center mt-3">
                                    <a <?php echo ((is_user_logged_in()) ? 'href="' . get_the_permalink() . '?ref=answer#answer-form"' : 'href="#" data-bs-toggle="modal" data-bs-target="#login-modal"'); ?> class="answer-button imit-font fz-14 rz-color fw-500" id="comment-button"><?php echo ((count($answer_count) <= 0) ? 'Be first to write answer' : 'Write answer'); ?></a>
                                    <?php
                                    if (isUserAlreadyPartner(get_current_user_id())) {
                                        if (count($answer_count) >= 1) {
                                    ?>
                                            <span class="point-badge imit-font fz-14 fw-500 mt-sm-0 mt-2"><img src="<?php echo plugins_url('images/Group (3).png', __FILE__); ?>" alt=""> <span class="rz-secondary-color">10 Points</span></span>
                                        <?php
                                        } else {
                                        ?>
                                            <span class="point-badge imit-font fz-14 fw-500 mt-sm-0 mt-2"><img src="<?php echo plugins_url('images/Group.png', __FILE__); ?>" alt=""> <span class="rz-secondary-color">20 Points</span></span>
                                    <?php
                                        }
                                    }
                                    ?>
                                </div>
                            </div>

                            <?php
                            $all_answers = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_answers WHERE post_id = '$post_id' AND status = '1'", ARRAY_A);
                            if (count($all_answers) > 0) {
                                $get_first_answer = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}rz_answers WHERE post_id = '$post_id' ORDER BY id ASC LIMIT 1");
                                $get_profile_data = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}rz_user_profile_data WHERE user_id = '{$get_first_answer->user_id}'");
                                $get_user = get_userdata($get_first_answer->user_id);
                            ?>
                                <div class="question-first-answer">
                                    <div class="answer-info d-flex flex-row justify-content-between align-items-center py-2 px-4">
                                        <a href="<?php the_permalink(); ?>" class="mb-0 rz-color imit-font count-answer rz-color fw-500"><?php echo count($all_answers); ?> <?php echo ((count($all_answers) > 1) ? 'Answers' : 'Answer'); ?></a>
                                        <a href="<?php the_permalink(); ?>" class="imit-font rz-color fw-500 fz-16">See all</a>
                                    </div>
                                    <ul class="answers py-3 px-4 mb-0">
                                        <li class="answer-list list-unstyled">
                                            <div class="answer-header border-bottom-0 d-flex flex-row justify-content-between align-items-center">
                                                <div class="user-data d-flex flex-row justify-content-start align-items-center">
                                                    <a href="<?php echo site_url() . '/user/' . $get_user->user_login; ?>" class="profile-image">
                                                        <img src="<?php echo getProfileImageById($get_first_answer->user_id); ?>" alt="">
                                                    </a>
                                                    <div class="user-info ms-2">
                                                        <a href="<?php echo site_url() . '/user/' . $get_user->user_login; ?>" class="imit-font fz-16"><?php echo getUserNameById($get_first_answer->user_id); ?></a>
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
                                                ?>
                                                <ul class="body-left d-flex flex-column justify-content-start align-items-center ps-0 mb-0" id="vote<?php echo $answer_id; ?>">
                                                    <li class="list-unstyled">
                                                        <a href="#" class="vote d-block <?php echo ((count($get_upvote) > 0) ? 'active' : ''); ?>" <?php echo ((is_user_logged_in()) ? 'data-answer_id="' . $answer_id . '" id="up-vote"' : 'data-bs-toggle="modal" data-bs-target="#login-modal"'); ?>><i class="fas fa-arrow-up"></i></a>
                                                    </li>
                                                    <li class="list-unstyled">
                                                        <p class="counter fz-16 imit-font my-1 text-dark" id="counter<?php echo $answer_id; ?>"><?php echo count($count_upvote); ?></p>
                                                    </li>
                                                    <li class="list-unstyled">
                                                        <a href="#" class="vote d-block <?php echo ((count($get_downvote) > 0) ? 'active' : ''); ?>" <?php echo ((is_user_logged_in()) ? 'data-answer_id="' . $answer_id . '" id="down-vote"' : 'data-bs-toggle="modal" data-bs-target="#login-modal"'); ?>><i class="fas fa-arrow-down"></i></a>
                                                    </li>
                                                </ul>
                                                <div class="answer-details ms-3">
                                                    <?php
                                                    if (str_word_count($get_first_answer->answer_text) > 40) {
                                                    ?>
                                                        <p class="imit-font fz-16 answer-text" style="line-height: 30px;" id="answer-short-text<?php echo $get_first_answer->id; ?>"><?php echo wp_trim_words($get_first_answer->answer_text, 40, ' ...'); ?></p>
                                                        <a href="<?php the_permalink(); ?>" class="rz-color fw-500 imit-font fz-16">Read More</a>
                                                    <?php
                                                    } else {
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
                        <div class="card-footer border-top-0 p-3 d-flex flex-row justify-content-between align-items-center">
                            <div class="views text-dark fz-14">
                                <i class="fas fa-eye"></i>
                                <span class="counter imit-font fw-500"><span class="counter"><?php echo getPostViews(get_the_ID()) ?></span></span>
                            </div>
                            <div class="other text-dark d-flex flex-row justify-content-end align-items-center">
                                <div class="dropdown share-dropdown">
                                    <a href="#" class="text-dark fz-16 me-3" data-bs-toggle="dropdown" aria-expanded="false" id="share-post-data"><i class="fas fa-share"></i></a>
                                    <div class="dropdown-menu border-0 p-0 shadow" aria-labelledby="share-post-data">
                                        <ul class="ps-0 mb-0 d-flex flex-row justify-content-center align-items-center">
                                            <li class="list-unstyled"><a href="#" class="share-link" id="share-facebook" data-shareurl="<?php the_permalink(); ?>"><i class="fab fa-facebook-f"></i></a></li>
                                            <li class="list-unstyled"><a href="#" class="share-link" id="tweet-data" data-shareurl="<?php the_permalink(); ?>"><i class="fab fa-twitter"></i></a></li>
                                            <li class="list-unstyled"><a href="#" class="share-link" id="share-linkedin" data-shareurl="<?php the_permalink(); ?>"><i class="fab fa-linkedin"></a></i></li>
                                        </ul>
                                    </div>
                                </div>
                                <?php if (is_user_logged_in() && $user_id === get_current_user_id()) { ?>
                                    <div class="dropdown">
                                        <a class="text-dark fz-16" href="#" role="button" id="more-option-feed" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-h"></i>
                                        </a>
                                        <ul class="dropdown-menu" aria-labelledby="more-option-feed">
                                            <li><a class="dropdown-item imit-font fz-14 text-dark" href="#" id="delete-question" data-question_id="<?php echo $post_id; ?>">Delete</a></li>
                                        </ul>
                                    </div>
                                <?php } ?>
                            </div>


                        </div>
                    </div>
                </li>
            <?php endwhile;

            wp_reset_postdata();
        } else {
            exit('newsReachmax');
        }
    }
    die();
}

/**
 * popular question
 */
add_action('wp_ajax_nopriv_rz_popular_question_data', 'imit_popular_questions_data');
add_action('wp_ajax_rz_popular_question_data', 'imit_popular_questions_data');

function imit_popular_questions_data()
{
    global $wpdb;
    $nonce = $_POST['nonce'];
    if (wp_verify_nonce($nonce, 'rz-popular-question-nonce')) {
        $page_num = sanitize_key($_POST['page_num']);
        $popularpost  = new WP_Query(array(
            'post_type' => 'rz_post_question',
            'posts_per_page' => 20,
            'meta_key' => 'post_views_count',
            'orderby' => 'meta_value_num',
            'order' => 'DESC',
            'paged' => $page_num,
            'tax_query' =>  array(
                array(
                    'taxonomy' => 'question_category',
                    'field' => 'slug',
                    'operator' => 'NOT IN',
                    'terms' => 'feature-post'
                )
            ),
        ));
        if ($popularpost->have_posts()) {
            while ($popularpost->have_posts()) : $popularpost->the_post();
                $post_id = get_the_ID();
                $user_id = get_the_author_meta('ID');
                $answer_count = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_answers WHERE post_id = '$post_id'", ARRAY_A);

                $current_user = get_current_user_id();
                $rz_following_questions = $wpdb->prefix . 'rz_following_questions';
                $is_user_already_followed_question = $wpdb->get_row("SELECT * FROM {$rz_following_questions} WHERE user_id = '{$current_user}' AND question_id = '{$post_id}'");
            ?>
                <li class="news-feed-list mt-3" id="question<?php echo $post_id; ?>">
                    <div class="card rz-br">
                        <div class="card-body p-0">
                            <div class="p-4">
                                <div class="d-flex flex-row justify-content-between align-items-center">
                                    <div class="created-at rz-secondary-color fz-14 imit-font">Asked on: <?php the_time('g:i a F d, Y'); ?></div>
                                </div>
                                <a href="<?php the_permalink(); ?>" class="question-title imit-font fw-500 text-decoration-none text-dark rz-lh-38"><span class="rz-color mr-1">Q.</span> <?php the_title(); ?></a>
                                <div class="rz-br my-3">
                                    <?php the_post_thumbnail('full', ['img-fluid']); ?>
                                </div>
                                <ul class="tags ps-0">
                                    <?php
                                    $tags = wp_get_post_terms(get_the_ID(), 'question_tags');
                                    foreach ($tags as $tag) {
                                        echo '<li class="tag-list d-inline-block"><a href="' . get_term_link($tag->term_id, 'question_tags') . '" class="tag-link imit-font fz-12 fw-500 rounded">' . $tag->name . '</a></li>';
                                    }
                                    ?>
                                </ul>

                                <div class="d-flex flex-sm-row flex-column justify-content-start align-items-start align-items-sm-center mt-3">
                                    <a <?php echo ((is_user_logged_in()) ? 'href="' . get_the_permalink() . '?ref=answer#answer-form"' : 'href="#" data-bs-toggle="modal" data-bs-target="#login-modal"'); ?> class="answer-button imit-font fz-14 rz-color fw-500" id="comment-button"><?php echo ((count($answer_count) <= 0) ? 'Be first to write answer' : 'Write answer'); ?></a>
                                    <?php
                                    if (isUserAlreadyPartner(get_current_user_id())) {
                                        if (count($answer_count) >= 1) {
                                    ?>
                                            <span class="point-badge imit-font fz-14 fw-500 mt-sm-0 mt-2"><img src="<?php echo plugins_url('images/Group (3).png', __FILE__); ?>" alt=""> <span class="rz-secondary-color">10 Points</span></span>
                                        <?php
                                        } else {
                                        ?>
                                            <span class="point-badge imit-font fz-14 fw-500 mt-sm-0 mt-2"><img src="<?php echo plugins_url('images/Group.png', __FILE__); ?>" alt=""> <span class="rz-secondary-color">20 Points</span></span>
                                    <?php
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                            <?php
                            $all_answers = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_answers WHERE post_id = '$post_id' AND status = '1'", ARRAY_A);
                            if (count($all_answers) > 0) {
                                $get_first_answer = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}rz_answers WHERE post_id = '$post_id' ORDER BY id ASC LIMIT 1");
                                $get_profile_data = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}rz_user_profile_data WHERE user_id = '{$get_first_answer->user_id}'");
                                $get_user = get_userdata($get_first_answer->user_id);
                            ?>
                                <div class="question-first-answer">
                                    <div class="answer-info d-flex flex-row justify-content-between align-items-center py-2 px-4">
                                        <a href="<?php the_permalink(); ?>" class="mb-0 rz-color imit-font count-answer rz-color fw-500"><?php echo count($all_answers); ?> <?php echo ((count($all_answers) > 1) ? 'Answers' : 'Answer'); ?></a>
                                        <a href="<?php the_permalink(); ?>" class="imit-font rz-color fw-500 fz-16">See all</a>
                                    </div>
                                    <ul class="answers py-3 px-4 mb-0">
                                        <li class="answer-list list-unstyled">
                                            <div class="answer-header border-bottom-0 d-flex flex-row justify-content-between align-items-center">
                                                <div class="user-data d-flex flex-row justify-content-start align-items-center">
                                                    <a href="<?php echo site_url() . '/user/' . $get_user->user_login; ?>" class="profile-image">
                                                        <img src="<?php echo getProfileImageById($get_first_answer->user_id); ?>" alt="">
                                                    </a>
                                                    <div class="user-info ms-2">
                                                        <a href="<?php echo site_url() . '/user/' . $get_user->user_login; ?>" class="imit-font fz-16"><?php echo getUserNameById($get_first_answer->user_id); ?></a>
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
                                                ?>
                                                <ul class="body-left d-flex flex-column justify-content-start align-items-center ps-0 mb-0" id="vote<?php echo $answer_id; ?>">
                                                    <li class="list-unstyled">
                                                        <a href="#" class="vote d-block <?php echo ((count($get_upvote) > 0) ? 'active' : ''); ?>" <?php echo ((is_user_logged_in()) ? 'data-answer_id="' . $answer_id . '" id="up-vote"' : 'data-bs-toggle="modal" data-bs-target="#login-modal"'); ?>><i class="fas fa-arrow-up"></i></a>
                                                    </li>
                                                    <li class="list-unstyled">
                                                        <p class="counter fz-16 imit-font my-1 text-dark" id="counter<?php echo $answer_id; ?>"><?php echo count($count_upvote); ?></p>
                                                    </li>
                                                    <li class="list-unstyled">
                                                        <a href="#" class="vote d-block <?php echo ((count($get_downvote) > 0) ? 'active' : ''); ?>" <?php echo ((is_user_logged_in()) ? 'data-answer_id="' . $answer_id . '" id="down-vote"' : 'data-bs-toggle="modal" data-bs-target="#login-modal"'); ?>><i class="fas fa-arrow-down"></i></a>
                                                    </li>
                                                </ul>
                                                <div class="answer-details ms-3">
                                                    <?php
                                                    if (str_word_count($get_first_answer->answer_text) > 40) {
                                                    ?>
                                                        <p class="imit-font fz-16 answer-text" style="line-height: 30px;" id="answer-short-text<?php echo $get_first_answer->id; ?>"><?php echo wp_trim_words($get_first_answer->answer_text, 40, ' ...'); ?></p>
                                                        <a href="<?php the_permalink(); ?>" class="rz-color fw-500 imit-font fz-16">Read More</a>
                                                    <?php
                                                    } else {
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
                        <div class="card-footer p-3 border-top-0 d-flex flex-row justify-content-between align-items-center">
                            <div class="views text-dark fz-14">
                                <i class="fas fa-eye"></i>
                                <span class="counter imit-font fw-500"><span class="counter"><?php echo getPostViews(get_the_ID()) ?></span></span>
                            </div>
                            <div class="other text-dark d-flex flex-row justify-content-end align-items-center">
                                <div class="dropdown share-dropdown">
                                    <a href="#" class="text-dark fz-16 me-3" data-bs-toggle="dropdown" aria-expanded="false" id="share-post-data"><i class="fas fa-share"></i></a>
                                    <div class="dropdown-menu border-0 p-0 shadow" aria-labelledby="share-post-data">
                                        <ul class="ps-0 mb-0 d-flex flex-row justify-content-center align-items-center">
                                            <li class="list-unstyled"><a href="#" class="share-link" id="share-facebook" data-shareurl="<?php the_permalink(); ?>"><i class="fab fa-facebook-f"></i></a></li>
                                            <li class="list-unstyled"><a href="#" class="share-link" id="tweet-data" data-shareurl="<?php the_permalink(); ?>"><i class="fab fa-twitter"></i></a></li>
                                            <li class="list-unstyled"><a href="#" class="share-link" id="share-linkedin" data-shareurl="<?php the_permalink(); ?>"><i class="fab fa-linkedin"></a></i></li>
                                        </ul>
                                    </div>
                                </div>
                                <?php if (is_user_logged_in() && $user_id === get_current_user_id()) { ?>
                                    <div class="dropdown">
                                        <a class="text-dark fz-16" href="#" role="button" id="more-option-feed" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-h"></i>
                                        </a>
                                        <ul class="dropdown-menu" aria-labelledby="more-option-feed">
                                            <li><a class="dropdown-item imit-font fz-14 text-dark" href="#" id="delete-question" data-question_id="<?php echo $post_id; ?>">Delete</a></li>
                                        </ul>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </li>
            <?php
            endwhile;
        } else {
            exit('popularPostReachmax');
        }
    }
    die();
}

/**
 * most answered
 */
add_action('wp_ajax_nopriv_rz_most_answered_data', 'imit_get_most_answered_question');
add_action('wp_ajax_rz_most_answered_data', 'imit_get_most_answered_question');

function imit_get_most_answered_question()
{
    global $wpdb;
    $nonce = $_POST['nonce'];
    if (wp_verify_nonce($nonce, 'rz-most-commented-nonce')) {
        $page_num = sanitize_key($_POST['page_num']);
        $all_answers_post = $wpdb->get_results("SELECT COUNT(post_id) AS post_count, post_id FROM {$wpdb->prefix}rz_answers GROUP BY post_id ORDER BY post_count DESC", ARRAY_A);
        $answer_post_ids = [];
        foreach ($all_answers_post as $answer_post) {
            array_push($answer_post_ids, $answer_post['post_id']);
        }
        $most_answered_post = new WP_Query([
            'post_type' => 'rz_post_question',
            'posts_per_page' => 20,
            'post__in' => $answer_post_ids,
            'orderby' => 'post__in',
            'paged' => $page_num,
            'tax_query' =>  array(
                array(
                    'taxonomy' => 'question_category',
                    'field' => 'slug',
                    'operator' => 'NOT IN',
                    'terms' => 'feature-post'
                )
            ),
        ]);
        if ($most_answered_post->have_posts()) {
            while ($most_answered_post->have_posts()) : $most_answered_post->the_post();
                $post_id = get_the_ID();
                $user_id = get_the_author_meta('ID');
                $answer_count = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_answers WHERE post_id = '$post_id'", ARRAY_A);

                $current_user = get_current_user_id();
                $rz_following_questions = $wpdb->prefix . 'rz_following_questions';
                $is_user_already_followed_question = $wpdb->get_row("SELECT * FROM {$rz_following_questions} WHERE user_id = '{$current_user}' AND question_id = '{$post_id}'");
            ?>
                <li class="news-feed-list mt-3" id="question<?php echo $post_id; ?>">
                    <div class="card rz-br">
                        <div class="card-body p-0">
                            <div class="p-4">
                                <div class="d-flex flex-row justify-content-between align-items-center">
                                    <div class="created-at rz-secondary-color fz-14 imit-font">Asked on: <?php the_time('g:i a F d, Y'); ?></div>
                                </div>
                                <a href="<?php the_permalink(); ?>" class="question-title imit-font fw-500 text-decoration-none text-dark rz-lh-38"><span class="rz-color mr-1">Q.</span> <?php the_title(); ?></a>
                                <div class="rz-br my-3">
                                    <?php the_post_thumbnail('full', ['img-fluid']); ?>
                                </div>
                                <ul class="tags ps-0">
                                    <?php
                                    $tags = wp_get_post_terms(get_the_ID(), 'question_tags');
                                    foreach ($tags as $tag) {
                                        echo '<li class="tag-list d-inline-block"><a href="' . get_term_link($tag->term_id, 'question_tags') . '" class="tag-link imit-font fz-12 fw-500 rounded">' . $tag->name . '</a></li>';
                                    }
                                    ?>
                                </ul>

                                <div class="d-flex flex-sm-row flex-column justify-content-start align-items-start align-items-sm-center mt-3">
                                    <a <?php echo ((is_user_logged_in()) ? 'href="' . get_the_permalink() . '?ref=answer#answer-form"' : 'href="#" data-bs-toggle="modal" data-bs-target="#login-modal"'); ?> class="answer-button imit-font fz-14 rz-color fw-500" id="comment-button"><?php echo ((count($answer_count) <= 0) ? 'Be first to write answer' : 'Write answer'); ?></a>
                                    <?php
                                    if (isUserAlreadyPartner(get_current_user_id())) {
                                        if (count($answer_count) >= 1) {
                                    ?>
                                            <span class="point-badge imit-font fz-14 fw-500 mt-sm-0 mt-2"><img src="<?php echo plugins_url('images/Group (3).png', __FILE__); ?>" alt=""> <span class="rz-secondary-color">10 Points</span></span>
                                        <?php
                                        } else {
                                        ?>
                                            <span class="point-badge imit-font fz-14 fw-500 mt-sm-0 mt-2"><img src="<?php echo plugins_url('images/Group.png', __FILE__); ?>" alt=""> <span class="rz-secondary-color">20 Points</span></span>
                                    <?php
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                            <?php
                            $all_answers = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_answers WHERE post_id = '$post_id' AND status = '1'", ARRAY_A);
                            if (count($all_answers) > 0) {
                                $get_first_answer = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}rz_answers WHERE post_id = '$post_id' ORDER BY id ASC LIMIT 1");
                                $get_profile_data = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}rz_user_profile_data WHERE user_id = '{$get_first_answer->user_id}'");
                                $get_user = get_userdata($get_first_answer->user_id);
                            ?>
                                <div class="question-first-answer">
                                    <div class="answer-info d-flex flex-row justify-content-between align-items-center py-2 px-4">
                                        <a href="<?php the_permalink(); ?>" class="mb-0 rz-color imit-font count-answer rz-color fw-500"><?php echo count($all_answers); ?> <?php echo ((count($all_answers) > 1) ? 'Answers' : 'Answer'); ?></a>
                                        <a href="<?php the_permalink(); ?>" class="imit-font rz-color fw-500 fz-16">See all</a>
                                    </div>
                                    <ul class="answers py-3 px-4 mb-0">
                                        <li class="answer-list list-unstyled">
                                            <div class="answer-header border-bottom-0 d-flex flex-row justify-content-between align-items-center">
                                                <div class="user-data d-flex flex-row justify-content-start align-items-center">
                                                    <a href="<?php echo site_url() . '/user/' . $get_user->user_login; ?>" class="profile-image d-block">
                                                        <img src="<?php echo getProfileImageById($get_first_answer->user_id); ?>" alt="">
                                                    </a>
                                                    <div class="user-info ms-2">
                                                        <a href="<?php echo site_url() . '/user/' . $get_user->user_login; ?>" class="imit-font fz-16"><?php echo getUserNameById($get_first_answer->user_id); ?></a>
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
                                                ?>
                                                <ul class="body-left d-flex flex-column justify-content-start align-items-center ps-0 mb-0" id="vote<?php echo $answer_id; ?>">
                                                    <li class="list-unstyled">
                                                        <a href="#" class="vote d-block <?php echo ((count($get_upvote) > 0) ? 'active' : ''); ?>" <?php echo ((is_user_logged_in()) ? 'data-answer_id="' . $answer_id . '" id="up-vote"' : 'data-bs-toggle="modal" data-bs-target="#login-modal"') ?>><i class="fas fa-arrow-up"></i></a>
                                                    </li>
                                                    <li class="list-unstyled">
                                                        <p class="counter fz-16 imit-font my-1 text-dark" id="counter<?php echo $answer_id; ?>"><?php echo count($count_upvote); ?></p>
                                                    </li>
                                                    <li class="list-unstyled">
                                                        <a href="#" class="vote d-block <?php echo ((count($get_downvote) > 0) ? 'active' : ''); ?>" <?php echo ((is_user_logged_in()) ? 'data-answer_id="' . $answer_id . '" id="down-vote"' : 'data-bs-toggle="modal" data-bs-target="#login-modal"'); ?>><i class="fas fa-arrow-down"></i></a>
                                                    </li>
                                                </ul>
                                                <div class="answer-details ms-3">
                                                    <?php
                                                    if (str_word_count($get_first_answer->answer_text) > 40) {
                                                    ?>
                                                        <p class="imit-font fz-16 answer-text" style="line-height: 30px;" id="answer-short-text<?php echo $get_first_answer->id; ?>"><?php echo wp_trim_words($get_first_answer->answer_text, 40, ' ...'); ?></p>
                                                        <a href="<?php the_permalink(); ?>" class="rz-color fw-500 imit-font fz-16">Read More</a>
                                                    <?php
                                                    } else {
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
                        <div class="card-footer p-3 border-top-0 d-flex flex-row justify-content-between align-items-center">
                            <div class="views text-dark fz-14">
                                <i class="fas fa-eye"></i>
                                <span class="counter imit-font fw-500"><span class="counter"><?php echo getPostViews(get_the_ID()) ?></span></span>
                            </div>
                            <div class="other text-dark d-flex flex-row justify-content-end align-items-center">
                                <div class="dropdown share-dropdown">
                                    <a href="#" class="text-dark fz-16 me-3" data-bs-toggle="dropdown" aria-expanded="false" id="share-post-data"><i class="fas fa-share"></i></a>
                                    <div class="dropdown-menu border-0 p-0 shadow" aria-labelledby="share-post-data">
                                        <ul class="ps-0 mb-0 d-flex flex-row justify-content-center align-items-center">
                                            <li class="list-unstyled"><a href="#" class="share-link" id="share-facebook" data-shareurl="<?php the_permalink(); ?>"><i class="fab fa-facebook-f"></i></a></li>
                                            <li class="list-unstyled"><a href="#" class="share-link" id="tweet-data" data-shareurl="<?php the_permalink(); ?>"><i class="fab fa-twitter"></i></a></li>
                                            <li class="list-unstyled"><a href="#" class="share-link" id="share-linkedin" data-shareurl="<?php the_permalink(); ?>"><i class="fab fa-linkedin"></a></i></li>
                                        </ul>
                                    </div>
                                </div>
                                <?php if (is_user_logged_in() && $user_id === get_current_user_id()) { ?>
                                    <div class="dropdown">
                                        <a class="text-dark fz-16" href="#" role="button" id="more-option-feed" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-h"></i>
                                        </a>
                                        <ul class="dropdown-menu" aria-labelledby="more-option-feed">
                                            <li><a class="dropdown-item imit-font fz-14 text-dark" href="#" id="delete-question" data-question_id="<?php echo $post_id; ?>">Delete</a></li>
                                        </ul>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </li>
            <?php endwhile;
        } else {
            exit('mostAnsweredPostReachmax');
        }
    }
    die();
}


/**
 * delete question
 */
add_action('wp_ajax_rz_delete_question', function () {
    global $wpdb;
    $nonce = $_POST['nonce'];
    if (wp_verify_nonce($nonce, 'rz-delete-question-nonce')) {
        $post_id = sanitize_key($_POST['question_id']);
        $user_id = get_current_user_id();

        if (!empty($post_id) && !empty($user_id)) {
            $wpdb->query("DELETE FROM {$wpdb->prefix}rz_comment_reply_likes WHERE reply_id IN (SELECT id FROM {$wpdb->prefix}rz_comment_replays WHERE comment_id IN (SELECT id FROM {$wpdb->prefix}rz_answer_comments WHERE answer_id IN (SELECT id FROM {$wpdb->prefix}rz_answers WHERE post_id = '{$post_id}')))");

            $wpdb->query("DELETE FROM {$wpdb->prefix}rz_comment_replays WHERE comment_id IN (SELECT id FROM {$wpdb->prefix}rz_answer_comments WHERE answer_id IN (SELECT id FROM {$wpdb->prefix}rz_answers WHERE post_id = '{$post_id}'))");

            $wpdb->query("DELETE FROM {$wpdb->prefix}rz_answer_comment_votes WHERE comment_id IN (SELECT id FROM {$wpdb->prefix}rz_answer_comments WHERE answer_id IN (SELECT id FROM {$wpdb->prefix}rz_answers WHERE post_id = '{$post_id}'))");

            $wpdb->query("DELETE FROM {$wpdb->prefix}rz_answer_comments WHERE answer_id IN (SELECT id FROM {$wpdb->prefix}rz_answers WHERE post_id = '{$post_id}')");


            $wpdb->query("SELECT * FROM {$wpdb->prefix}rz_vote WHERE answer_id IN (SELECT id FROM {$wpdb->prefix}rz_answers WHERE post_id = '{$post_id}')");


            $wpdb->delete($wpdb->prefix . 'rz_answers', [
                'post_id' => $post_id
            ]);

            wp_delete_attachment($post_id, false);

            wp_delete_post($post_id, false);

            exit('done');
        }
    }
    die();
});


/**
 * follow question
 */
add_action('wp_ajax_rz_follow_question', function () {
    global $wpdb;
    $nonce = $_POST['nonce'];
    if (wp_verify_nonce($nonce, 'rz-follow-question-nonce')) {
        $question_id = sanitize_key($_POST['question_id']);
        $user_id = get_current_user_id();
        $rz_following_questions = $wpdb->prefix . 'rz_following_questions';

        $is_user_already_followed = $wpdb->get_row("SELECT * FROM {$rz_following_questions} WHERE user_id = '{$user_id}' AND question_id = '{$question_id}'");

        if (!empty($is_user_already_followed)) {
            $wpdb->delete($rz_following_questions, [
                'user_id' => $user_id,
                'question_id' => $question_id
            ]);
            $response['response'] = false;
        } else {
            $wpdb->insert($rz_following_questions, [
                'user_id' => $user_id,
                'question_id' => $question_id,
                'created_at' => wpDateTime(),
                'updated_at' => wpDateTime()
            ]);
            $response['response'] = true;
        }

        echo json_encode($response);
    }
    die();
});



/**
 * get posts by tag
 */
add_action('wp_ajax_nopriv_rz_get_post_using_tags', 'imit_get_post_using_tags');
add_action('wp_ajax_rz_get_post_using_tags', 'imit_get_post_using_tags');

function imit_get_post_using_tags()
{
    global $wpdb;
    $nonce = $_POST['nonce'];
    if (wp_verify_nonce($nonce, 'rz-get-post-using-tags-nonce')) {
        $tag = sanitize_text_field($_POST['tag']);
        $page_number = sanitize_key($_POST['page_num']);

        $args = array(
            'post_type'  => 'rz_post_question',
            'posts_per_page' => 20,
            'paged' => $page_number,
            'tax_query'  => array(
                array(
                    'taxonomy'  => 'question_tags',
                    'field'     => 'slug',
                    'terms'     =>  $tag
                ),
                array(
                    'taxonomy' => 'question_category',
                    'field' => 'slug',
                    'operator' => 'NOT IN',
                    'terms' => 'feature-post'
                )
            ),
        );

        $posts_array = new WP_Query($args);


        if ($posts_array->have_posts()) {
            while ($posts_array->have_posts()) : $posts_array->the_post();
                $post_id = get_the_ID();
                $user_id = get_the_author_meta('ID');
                $answer_count = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_answers WHERE post_id = '$post_id'", ARRAY_A);

                $current_user = get_current_user_id();
                $rz_following_questions = $wpdb->prefix . 'rz_following_questions';
                $is_user_already_followed_question = $wpdb->get_row("SELECT * FROM {$rz_following_questions} WHERE user_id = '{$current_user}' AND question_id = '{$post_id}'");
            ?>
                <li class="news-feed-list mt-3" id="question<?php echo $post_id; ?>">
                    <div class="card rz-br">
                        <div class="card-body p-0">
                            <div class="p-4">
                                <div class="d-flex flex-row justify-content-between align-items-center">
                                    <div class="created-at rz-secondary-color fz-14 imit-font">Asked on: <?php the_time('g:i a F d, Y'); ?></div>
                                </div>
                                <a href="<?php the_permalink(); ?>" class="question-title imit-font fw-500 text-decoration-none text-dark rz-lh-38"><span class="rz-color mr-1">Q.</span> <?php the_title(); ?></a>
                                <div class="rz-br my-3">
                                    <?php the_post_thumbnail('full', ['img-fluid']); ?>
                                </div>
                                <ul class="tags ps-0">
                                    <?php
                                    $tags = wp_get_post_terms(get_the_ID(), 'question_tags');
                                    foreach ($tags as $tag) {
                                        echo '<li class="tag-list d-inline-block"><a href="' . get_term_link($tag->term_id, 'question_tags') . '" class="tag-link imit-font fz-12 fw-500 rounded">' . $tag->name . '</a></li>';
                                    }
                                    ?>
                                </ul>

                                <div class="d-flex flex-sm-row flex-column justify-content-start align-items-start align-items-sm-center mt-3">
                                    <a <?php echo ((is_user_logged_in()) ? 'href="' . get_the_permalink() . '?ref=answer#answer-form"' : 'href="#" data-bs-toggle="modal" data-bs-target="#login-modal"'); ?> class="answer-button imit-font fz-14 rz-color fw-500" id="comment-button"><?php echo ((count($answer_count) <= 0) ? 'Be first to write answer' : 'Write answer'); ?></a>
                                    <?php
                                    if (isUserAlreadyPartner(get_current_user_id())) {
                                        if (count($answer_count) >= 1) {
                                    ?>
                                            <span class="point-badge imit-font fz-14 fw-500 mt-sm-0 mt-2"><img src="<?php echo plugins_url('images/Group (3).png', __FILE__); ?>" alt=""> <span class="rz-secondary-color">10 Points</span></span>
                                        <?php
                                        } else {
                                        ?>
                                            <span class="point-badge imit-font fz-14 fw-500 mt-sm-0 mt-2"><img src="<?php echo plugins_url('images/Group.png', __FILE__); ?>" alt=""> <span class="rz-secondary-color">20 Points</span></span>
                                    <?php
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                            <?php
                            $all_answers = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_answers WHERE post_id = '$post_id' AND status = '1'", ARRAY_A);
                            if (count($all_answers) > 0) {
                                $get_first_answer = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}rz_answers WHERE post_id = '$post_id' ORDER BY id ASC LIMIT 1");
                                $get_profile_data = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}rz_user_profile_data WHERE user_id = '{$get_first_answer->user_id}'");
                                $get_user = get_userdata($get_first_answer->user_id);
                            ?>
                                <div class="question-first-answer">
                                    <div class="answer-info d-flex flex-row justify-content-between align-items-center py-2 px-4">
                                        <a href="<?php the_permalink(); ?>" class="mb-0 rz-color imit-font count-answer rz-color fw-500"><?php echo count($all_answers); ?> <?php echo ((count($all_answers) > 1) ? 'Answers' : 'Answer'); ?></a>
                                        <a href="<?php the_permalink(); ?>" class="imit-font rz-color fw-500 fz-16">See all</a>
                                    </div>
                                    <ul class="answers py-3 px-4 mb-0">
                                        <li class="answer-list list-unstyled">
                                            <div class="answer-header border-bottom-0 d-flex flex-row justify-content-between align-items-center">
                                                <div class="user-data d-flex flex-row justify-content-start align-items-center">
                                                    <a href="<?php echo site_url() . '/user/' . $get_user->user_login; ?>" class="profile-image d-block">
                                                        <img src="<?php echo getProfileImageById($get_first_answer->user_id); ?>" alt="">
                                                    </a>
                                                    <div class="user-info ms-2">
                                                        <a href="<?php echo site_url() . '/user/' . $get_user->user_login; ?>" class="imit-font fz-16"><?php echo getUserNameById($get_first_answer->user_id); ?></a>
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
                                                ?>
                                                <ul class="body-left d-flex flex-column justify-content-start align-items-center ps-0 mb-0" id="vote<?php echo $answer_id; ?>">
                                                    <li class="list-unstyled">
                                                        <a href="#" class="vote d-block <?php echo ((count($get_upvote) > 0) ? 'active' : ''); ?>" <?php echo ((is_user_logged_in()) ? 'data-answer_id="' . $answer_id . '" id="up-vote"' : 'data-bs-target="#login-modal" data-bs-toggle="modal"'); ?>><i class="fas fa-arrow-up"></i></a>
                                                    </li>
                                                    <li class="list-unstyled">
                                                        <p class="counter fz-16 imit-font my-1 text-dark" id="counter<?php echo $answer_id; ?>"><?php echo count($count_upvote); ?></p>
                                                    </li>
                                                    <li class="list-unstyled">
                                                        <a href="#" class="vote d-block <?php echo ((count($get_downvote) > 0) ? 'active' : ''); ?>" <?php echo ((is_user_logged_in()) ? 'data-answer_id="' . $answer_id . '" id="down-vote"' : 'data-bs-target="#login-modal" data-bs-toggle="modal"'); ?>><i class="fas fa-arrow-down"></i></a>
                                                    </li>
                                                </ul>
                                                <div class="answer-details ms-3">
                                                    <?php
                                                    if (str_word_count($get_first_answer->answer_text) > 40) {
                                                    ?>
                                                        <p class="imit-font fz-16 answer-text" style="line-height: 30px;" id="answer-short-text<?php echo $get_first_answer->id; ?>"><?php echo wp_trim_words($get_first_answer->answer_text, 40, ' ...'); ?></p>
                                                        <a href="<?php the_permalink(); ?>" class="rz-color fw-500 imit-font fz-16">Read More</a>
                                                    <?php
                                                    } else {
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
                        <div class="card-footer p-3 border-top-0 d-flex flex-row justify-content-between align-items-center">
                            <div class="views text-dark fz-14">
                                <i class="fas fa-eye"></i>
                                <span class="counter imit-font fw-500"><span class="counter"><?php echo getPostViews(get_the_ID()) ?></span></span>
                            </div>
                            <div class="other text-dark d-flex flex-row justify-content-end align-items-center">
                                <div class="dropdown share-dropdown">
                                    <a href="#" class="text-dark fz-16 me-3" data-bs-toggle="dropdown" aria-expanded="false" id="share-post-data"><i class="fas fa-share"></i></a>
                                    <div class="dropdown-menu border-0 p-0 shadow" aria-labelledby="share-post-data">
                                        <ul class="ps-0 mb-0 d-flex flex-row justify-content-center align-items-center">
                                            <li class="list-unstyled"><a href="#" class="share-link" id="share-facebook" data-shareurl="<?php the_permalink(); ?>"><i class="fab fa-facebook-f"></i></a></li>
                                            <li class="list-unstyled"><a href="#" class="share-link" id="tweet-data" data-shareurl="<?php the_permalink(); ?>"><i class="fab fa-twitter"></i></a></li>
                                            <li class="list-unstyled"><a href="#" class="share-link" id="share-linkedin" data-shareurl="<?php the_permalink(); ?>"><i class="fab fa-linkedin"></a></i></li>
                                        </ul>
                                    </div>
                                </div>
                                <?php if (is_user_logged_in() && $user_id === get_current_user_id()) { ?>
                                    <div class="dropdown">
                                        <a class="text-dark fz-16" href="#" role="button" id="more-option-feed" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-h"></i>
                                        </a>
                                        <ul class="dropdown-menu" aria-labelledby="more-option-feed">
                                            <li><a class="dropdown-item imit-font fz-14 text-dark" href="#" id="delete-question" data-question_id="<?php echo $post_id; ?>">Delete</a></li>
                                        </ul>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </li>
<?php
            endwhile;
        } else {
            exit('tagPostReachMax');
        }
    }
    die();
}
