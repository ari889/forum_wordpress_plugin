<?php



add_shortcode('imit-rz-search', function () {
    ob_start();
    global $wpdb;
?>
    <div class="rz-mid">
        <?php
        if (is_search()) {
            echo '<h1 class="heading fz-20 imit-font fw-500 mx-lg-0 mx-2">Search Results for "' . esc_html($_GET['s']) . '"</h1>';
        }
        ?>
        <div class="row">
            <div class="col-lg-9">
                <?php
                if (have_posts()) {
                ?>
                    <ul class="imit-news-feed ps-0 mb-0 runded-3 mx-lg-0 mx-2">
                        <?php
                        while (have_posts()) : the_post();



                            if (get_post_type() == 'rz_post_question') {
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
                            <?php


                            } else if (get_post_type() == 'rz_discussion') {
                                $user_id = get_the_author_meta('ID');

                                $get_user_profile_data = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}rz_user_profile_data WHERE user_id = '$user_id'");
                                $user_data = get_userdata($user_id);
                            ?>
                                <li class="blog-list list-unstyled mt-3" id="dis-post<?php echo get_the_ID(); ?>">
                                    <div class="card rz-border rz-br">
                                        <div class="card-body p-4">
                                            <div class="blog-list-header d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center">
                                                <div class="user-info d-flex flex-row justify-content-start align-items-center">
                                                    <div class="profile-image" style="max-width: 42px; max-height: 41px; border-radius: 50%;">
                                                        <img src="<?php echo getProfileImageById($user_id); ?>" alt="" style="width: 42px; height: 41px; border-radius: 50%;">
                                                    </div>
                                                    <div class="userdetails ms-2">
                                                        <a href="<?php echo site_url() . '/user/' . $user_data->user_login; ?>" class="name imit-font fz-16 text-dark fw-500 d-block"><?php echo getUserNameById($user_id); ?></a>

                                                        <?php if (!empty($get_user_profile_data->occupation)) {
                                                        ?>
                                                            <p class="mb-0 designation imit-font fw-400 rz-secondary-color fz-12"><?php echo $get_user_profile_data->occupation; ?></p>
                                                        <?php
                                                        } ?>
                                                    </div>
                                                </div>
                                                <p class="mb-0 imit-font fz-14 rz-secondary-color fw-400 mt-sm-0 mt-2">Posted on: <?php the_time('F d, Y'); ?></p>
                                            </div>
                                            <div class="blog-body">
                                                <a href="<?php the_permalink(); ?>" class="my-3 title imit-font text-dark fw-500 d-block"><?php the_title(); ?> </a>
                                                <p class="description imit-font fz-14 rz-secondary-color"><?php
                                                                                                            $more = '<a href="' . get_the_permalink() . '" class="imit-font fz-16 rz-color fw-500 d-block my-3">Read More</a>';
                                                                                                            echo force_balance_tags(html_entity_decode(wp_trim_words(htmlentities(wpautop(get_the_content())), 100, $more))); ?></p>
                                                <?php the_post_thumbnail('large', ['class' => 'img-fluid mb-3']); ?>
                                                <ul class="tags ps-0 mb-0 d-flex flex-row justify-content-start align-items-center">
                                                    <?php
                                                    $tags = wp_get_post_terms(get_the_ID(), 'discussion_tags');
                                                    foreach ($tags as $tag) {
                                                        echo '<li class="tag-list list-unstyled"><a href="' . get_term_link($tag->term_id, 'discussion_tags') . '" class="tag-link imit-font fz-12 d-block me-2 rz-secondary-color rounded border px-1">' . $tag->name . '</a></li>';
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
                                                ?>
                                                <li class="blog-footer-list list-unstyled">
                                                    <a href="#" class="vote imit-font fz-12 fw-400 me-3 <?php echo ((count($get_all_up_like) > 0) ? 'active' : ''); ?>" <?php echo ((is_user_logged_in()) ? 'id="discuss-up-like" data-post_id="' . get_the_ID() . '"' : 'data-bs-toggle="modal" data-bs-target="#login-modal"'); ?>><i class="fas fa-arrow-up"></i></a>
                                                </li>
                                                <li class="blog-footer-list list-unstyled">
                                                    <span class="counter imit-font fz-16 fw-500 me-3 text-dark" id="discuss-like-counter<?php echo get_the_ID(); ?>"><?php echo count($count_discuss_up_like); ?></span>
                                                </li>
                                                <li class="blog-footer-list list-unstyled">
                                                    <a href="#" class="vote imit-font fz-12 fw-400 me-3 <?php echo ((count($get_all_down_like) > 0) ? 'active' : ''); ?>" <?php echo ((is_user_logged_in()) ? 'id="discuss-down-like" data-post_id="' . get_the_ID() . '"' : 'data-bs-toggle="modal" data-bs-target="#login-modal"'); ?>><i class="fas fa-arrow-down"></i></a>
                                                </li>
                                                <li class="blog-footer-list list-unstyled">
                                                    <a href="#" class="Visitor imit-font fz-14 text-dark fw-400 me-3"><i class="fas fa-eye"></i> <?php echo getPostViews(get_the_ID()); ?> <span class="d-none d-sm-inline-block">Views</span></a>
                                                </li>
                                                <li class="blog-footer-list list-unstyled">
                                                    <?php
                                                    $get_all_comments = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_discussion_comments WHERE post_id = '$post_id'", ARRAY_A);
                                                    ?>
                                                    <a <?php echo ((is_user_logged_in()) ? 'href="' . get_the_permalink() . '#add_comment_discussion"' : 'href="#" data-bs-toggle="modal" data-bs-target="#login-modal"'); ?> class="comments imit-font fz-14 text-dark fw-400 me-3"><i class="fas fa-comments"></i> <span class="d-none d-sm-inline-block">Comments</span> <?php echo ((count($get_all_comments) > 0) ? '(' . count($get_all_comments) . ')' : ''); ?></a>
                                                </li>
                                            </ul>
                                            <div class="other text-dark d-flex flex-row justify-content-end align-items-center mt-sm-0 mt-2">
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
                                                <?php
                                                $post_author = get_the_author_meta('ID');
                                                if (is_user_logged_in() && $post_author == get_current_user_id()) {
                                                ?>
                                                    <div class="dropdown custom-dropdown">
                                                        <a class="text-dark fz-16" href="#" role="button" id="more-option-feed" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="fas fa-ellipsis-h"></i>
                                                        </a>

                                                        <ul class="dropdown-menu" aria-labelledby="more-option-feed">
                                                            <li><a class="dropdown-item imit-font fz-14 text-dark" href="#" id="delete-discuss-post" data-post_id="<?php echo get_the_ID(); ?>">Delete</a></li>
                                                        </ul>
                                                    </div>
                                                <?php
                                                } ?>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                        <?php
                            }




                        endwhile;


                        echo paginate_links();

                        wp_reset_postdata();

                        ?>
                    </ul>
                <?php
                } else {
                ?>
                    <div class="bg-light rz-br rz-border p-5 text-center list-unstyled mt-3">
                        <p class="mb-0 imit-font fz-16 rz-secondary-color">No more posts available to show.</p>
                    </div>
                <?php
                }
                ?>
            </div>
            <div class="col-lg-3 mt-3">
                <div class="mx-lg-0 mx-2">
                    <?php echo do_shortcode('[join-partner-program]'); ?>
                </div>
            </div>
        </div>

    <?php
    return ob_get_clean();
});
