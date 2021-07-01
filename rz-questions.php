<?php


/**
 * for question page
 */
add_shortcode('imit-questions', function(){
    global $wpdb;
    ob_start();
    if(is_user_logged_in()){
        $rz_user_profile_data = $wpdb->prefix.'rz_user_profile_data';
        $current_user_id = get_current_user_id();
        $profile_data = $wpdb->get_row("SELECT * FROM {$rz_user_profile_data} WHERE user_id = '{$current_user_id}'");

        if(empty($profile_data)){
            $banner_status = 'active';
        }else{
            $banner_status = $profile_data->banner_status;
        }

        if($banner_status == 'active'){
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
            <div class="row">
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

                    <div class="imit-tabs rz-bg-color p-3 rounded mt-3">
                        <div class="d-flex flex-row justify-content-between align-items-center">
                            <ul class="tab-menu ps-0 mb-0 d-flex flex-row justify-content-start align-items-center">
                                <li class="tab-list list-unstyled">
                                    <a href="#" class="tab-link imit-font text-white fz-14 active" data-target="news-feed">Your Feed</a>
                                </li>
                                <li class="tab-list list-unstyled">
                                    <a href="#" class="tab-link imit-font text-white fz-14" data-target="popular-questions">Popular Questions</a>
                                </li>
                                <li class="tab-list list-unstyled">
                                    <a href="#" class="tab-link imit-font text-white fz-14" data-target="most-answered">Most Answered Questions</a>
                                </li>
                            </ul>

                            <div class="dropdown">
                                <a class="see-more text-white fz-16" href="#" role="button" id="feed-more-tabs" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-ellipsis-h"></i>
                                </a>

                                <ul class="dropdown-menu" aria-labelledby="feed-more-tabs">
                                    <li><a class="dropdown-item imit-font fz-14 text-dark" href="#">Action</a></li>
                                    <li><a class="dropdown-item imit-font fz-14 text-dark" href="#">Another action</a></li>
                                    <li><a class="dropdown-item imit-font fz-14 text-dark" href="#">Something else here</a></li>
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
                    if($get_feature_post->have_posts(  )){
                        ?>
                        <ul class="imit-news-feed mb-0 p-4 rz-light-bg rz-br mt-3">
                            <li class="d-flex flex-row justify-content-between align-items-center mb-3">
                                <div class="d-flex flex-row justifu-content-start align-items-center">
                                    <img src="<?php echo plugins_url('images/Group (1).png', __FILE__); ?>" alt="">
                                    <h2 class="imit-font rz-color m-0 ms-2" style="font-size: 24px;">Question of the hour</h2>
                                </div>
                                <p class="mb-0 rz-color imit-font fz-14 fw-500">Next question of the hour in 30 mins 24 secs</p>
                            </li>
                            <?php 
                            while($get_feature_post->have_posts()):$get_feature_post->the_post();
                            $post_id = get_the_ID();
                            $user_id = get_the_author_meta('ID');
                            $answer_count = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_answers WHERE post_id = '$post_id' AND user_id != '$user_id'", ARRAY_A);
                            ?>
                            <li class="news-feed-list mt-3">
                                <div class="card rz-br">
                                    <div class="card-body p-0">
                                        <div class="p-4">
                                            <div class="created-at rz-secondary-color fz-14 imit-font">Asked on: <?php the_time(); ?></div>
                                            <a href="<?php the_permalink(); ?>" class="question-title imit-font fw-500 text-decoration-none text-dark rz-lh-38"><span class="rz-color mr-1">Q.</span> <?php the_title(); ?></a>
                                            <div class="rz-br my-3">
                                                <?php the_post_thumbnail('full', ['img-fluid']); ?>
                                            </div>
                                            <ul class="tags ps-0 d-flex flex-row justify-content-start align-items-center">
                                                <?php
                                                $tags = wp_get_post_terms(get_the_ID(), 'question_tags');
                                                foreach($tags as $tag){
                                                    echo '<li class="tag-list"><a href="'.get_term_link($tag->term_id, 'question_tags').'" class="tag-link imit-font fz-12 fw-500 rounded">'.$tag->name.'</a></li>';
                                                }
                                                ?>
                                            </ul>

                                            <?php
                                            if(is_user_logged_in(  )){
                                                ?>
                                                <div class="d-flex flex-row justify-content-start align-items-center mt-3">
                                                    <a href="#" class="answer-button btn imit-font fz-14 rz-color fw-500 me-2" data-target="most-answer-form<?php echo get_the_ID(); ?>" id="comment-button"><?php if(count($answer_count) <= 0 && $user_id !== get_current_user_id()){echo 'Be first to write answer';}else{echo 'Write an answer';} ?></a>
                                                    <?php
                                                    if($user_id !== get_current_user_id()){
                                                        if(count($answer_count) >= 1){
                                                            ?>
                                                            <span class="point-badge imit-font fz-14 fw-500"><img src="<?php echo plugins_url('images/Group (3).png', __FILE__); ?>" alt=""> <span class=" rz-secondary-color">10 Point</span></span>
                                                            <?php
                                                        }else{
                                                            ?>
                                                            <span class="point-badge imit-font fz-14 fw-500"><img src="<?php echo plugins_url('images/Group.png', __FILE__); ?>" alt=""> <span class=" rz-secondary-color">20 Point</span></span>
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                </div>
                                                <div class="answer-wrapper mt-3" id="most-answer-form<?php echo get_the_ID(); ?>" style="display: none;">
                                                    <form action="" id="answer-form" data-post_id="<?php echo get_the_ID(); ?>">
                                                        <div class="row">
                                                            <div class="col-md-1">
                                                                <div class="profile-image">
                                                                    <img src="<?php getProfileImageById(get_current_user_id()); ?>" alt="">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-11">
                                                                <div class="answer-editor">
                                                                    <textarea name="answer<?php echo get_the_ID(); ?>" class="imit-font fz-14 form-control" id="answer-textarea" cols="30" rows="10" data-post_id="<?php echo get_the_ID(); ?>"></textarea>
                                                                    <ul class="list-group" id="answer_hashtag<?php echo get_the_ID(); ?>">

                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <button type="submit" class="btn rz-bg-color text-white imit-font fz-14 ms-auto d-table mt-2 fw-500">Submit Answer</button>
                                                    </form>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <div class="card-footer p-3 border-top-0 d-flex flex-row justify-content-between align-items-center">
                                        <div class="views text-dark fz-14">
                                            <i class="fas fa-eye"></i>
                                            <span class="counter imit-font fw-500"><span class="counter"><?php echo getPostViews(get_the_ID()) ?></span></span>
                                        </div>
        <!--                                <div class="other text-dark d-flex flex-row justify-content-end align-items-center">-->
        <!--                                    <a href="#" class="fz-14 text-dark me-2"><i class="fas fa-share"></i></a>-->
        <!--                                    <div class="dropdown">-->
        <!--                                        <a class="text-dark fz-16" href="#" role="button" id="more-option-feed" data-bs-toggle="dropdown" aria-expanded="false">-->
        <!--                                            <i class="fas fa-ellipsis-h"></i>-->
        <!--                                        </a>-->
        <!---->
        <!--                                        <ul class="dropdown-menu" aria-labelledby="more-option-feed">-->
        <!--                                            <li><a class="dropdown-item imit-font fz-14 text-dark" href="#">Action</a></li>-->
        <!--                                            <li><a class="dropdown-item imit-font fz-14 text-dark" href="#">Another action</a></li>-->
        <!--                                            <li><a class="dropdown-item imit-font fz-14 text-dark" href="#">Something else here</a></li>-->
        <!--                                        </ul>-->
        <!--                                    </div>-->
        <!--                                </div>-->
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

                    <div class="tab-content" id="popular-questions">
                        <ul class="imit-news-feed ps-0 mb-0 runded-3" id="popular-questions-ul"></ul>
                    </div>

                    <div class="tab-content" id="most-answered" style="display: none;">
                        <ul class="imit-news-feed ps-0 mb-0 runded-3" id="most-answered-ul">

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
                        <h3 class="title m-0 text-white imit-font fz-20 fw-500">Join our Partner Program and earn money on Recozilla</h3>
                        <a href="<?php echo site_url(); ?>/join-partner-program/" class="btn bg-white fz-12 rz-color imit-font fw-500 mt-3">Join Now</a>
                    </div>
                    <div class="card mt-3 rz-border rz-br trending-tags">
                        <div class="card-header rz-bg-color border-bottom-0 p-3">
                            <h3 class="trending-title text-white fz-14 fw-500 m-0"><span class="fz-16 me-2" style="font-size: 25px !important;">#</span> Trending Tags</h3>
                        </div>
                        <div class="card-body p-0">
                            <ul class="hash-tags ps-0 mb-0">

                                <?php
                                $tags = get_terms(array(
                                    'taxonomy' => 'question_tags',
                                    'orderby' => 'count',
                                    'order' => 'DESC',
                                    'number' => 5
                                ));

                                foreach($tags as $tag){
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
                                    ?>
                                    <li class="hash-list list-unstyled m-3">
                                        <div class="hash-top d-flex flex-row justify-content-between align-items-center">
                                            <a href="<?php echo get_term_link($tag->term_id, 'question_tags'); ?>" class="imit-font fw-500 fz-16 text-dark d-block">#<?php echo $tag->name; ?></a>
<!--                                            <button type="button" class="add-post-by-tag p-0 rz-secondary-color bg-transparent fz-14"><i class="fas fa-plus-circle"></i></button>-->
                                        </div>
                                        <div class="d-flex flex-row justify-content-between align-items-center">
                                            <p class="rz-secondary-color imit-font fz-12 fw-400 mb-0"><?php echo $tag->count; ?> Question</p>
                                            <p class="rz-secondary-color imit-font fz-12 fw-400 mb-0"><?php echo $count_answer; ?> Answers</p>
                                            <p class="rz-secondary-color imit-font fz-12 fw-400 mb-0"><?php echo $total_view; ?> Views</p>
                                        </div>
                                    </li>
                                        <?php
                                }
                                ?>
                            </ul>
                        </div>
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
  add_shortcode('rz-question-popup', function(){
    ob_start();
    ?>
    <form id="add-question-form" enctype="multipart/form-data">
        <div id="add_question_error" class="mt-4"></div>
        <div class="d-flex flex-row justifu-content-center align-items-center">
            <label for="question-title" class="imit-font title-text rz-color me-2">Q</label>
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
        <h3 class="imit-font fz-20 text-dark fw-500 mt-5">Do you want to add an answer as well to this question?</h3>
    
        <textarea name="content" id="" cols="30" rows="10" class="form-control imit-font fz-14 text-dark"></textarea>

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
  add_action('wp_ajax_imit_add_question', function(){
    global $wpdb;

    $nonce = $_POST['nonce'];

    if(wp_verify_nonce( $nonce, 'rz-add-question-nonce' )){
        $title = sanitize_text_field( $_POST['title'] );
        $tag = sanitize_text_field( $_POST['tag'] );
        $image = $_FILES['image']['name'];
        $image_tmp = $_FILES['image']['tmp_name'];
        $content = sanitize_text_field( $_POST['content'] );
        $rz_partner_program = $wpdb->prefix.'rz_user_programs';
        $rz_point_table = $wpdb->prefix.'rz_point_table';
        $rz_user_profile_data = $wpdb->prefix.'rz_user_profile_data';
        $user_id = get_current_user_id();


        $is_user_joined_programme = $wpdb->get_results("SELECT * FROM {$rz_partner_program} WHERE user_id = '$user_id' AND status = '1'", ARRAY_A);

        $exp = explode('.', $image);

        $ext = end($exp);

        $unique_name = md5(time().rand()).'.'.$ext;

        $format = ['png', 'jpg', 'gif', 'jpeg'];

        if(empty($title) || empty($tag) || empty($content)){
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

            $my_post = array(
                'post_title'    => wp_strip_all_tags( $title ),
                'post_type'    => 'rz_post_question',
                'post_content'  => $content,
                'post_status'   => 'publish',
                'post_author'   => $user_id,
                'tags_input'   => $tag,
              );
               
            // Insert the post into the database
            $post_id = wp_insert_post( $my_post );

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

            $post_tags = explode(',', $tag);

            wp_set_post_terms( $post_id, $post_tags, 'question_tags');

            // $filename should be the path to a file in the upload directory.
            $filename = $image_data['url'];

            if(!empty($filename) && !empty($post_id)){                            
                // Check the type of file. We'll use this as the 'post_mime_type'.
                $filetype = wp_check_filetype( basename( $filename ), null );

                // Get the path to the upload directory.
                $wp_upload_dir = wp_upload_dir();

                // Prepare an array of post data for the attachment.
                $attachment = array(
                    'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ), 
                    'post_mime_type' => $filetype['type'],
                    'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
                    'post_content'   => '',
                    'post_status'    => 'inherit'
                );

                // Insert the attachment.
                $attach_id = wp_insert_attachment( $attachment, $filename, $post_id );

                // Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
                require_once( ABSPATH . 'wp-admin/includes/image.php' );

                // Generate the metadata for the attachment, and update the database record.
                $attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
                wp_update_attachment_metadata( $attach_id, $attach_data );

                set_post_thumbnail( $post_id, $attach_id );
            }
            echo '<div class="alert imit-font fz-16 alert-success alert-dismissible fade show" role="alert">
            <strong>Success!</strong> Question added successfully.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
        }
    }
    die();
  });

  /**
   * fetch news feed data
   */
  add_action('wp_ajax_nopriv_rz_news_feed_data', 'imit_news_feed_data');
  add_action('wp_ajax_rz_news_feed_data', 'imit_news_feed_data');

  function imit_news_feed_data(){
      global $wpdb;
      $nonce = $_POST['nonce'];
      if(wp_verify_nonce($nonce, 'rz-news-feed-posts-nonce')){
          $page_num = sanitize_key($_POST['page_num']);
              $questions_data = new WP_Query([
                  'post_type' => 'rz_post_question',
                  'posts_per_page' => 10,
                  'paged' => $page_num,
                  'tax_query' =>  array(
                        array(
                            'taxonomy' => 'question_category',
                            'field' => 'slug',
                            'terms' => 'uncategorised'
                        )
                    ),
              ]);
              if($questions_data->have_posts()){
                  while($questions_data->have_posts()):$questions_data->the_post();
                      $post_id = get_the_ID();
                      $user_id = get_the_author_meta('ID');
                      $answer_count = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_answers WHERE post_id = '$post_id' AND user_id != '$user_id'", ARRAY_A);
                      ?>
                      <li class="news-feed-list mt-3">
                          <div class="card rz-br rz-border">
                              <div class="card-body p-0">
                                  <div class="p-4">
                                      <p class="created-at rz-secondary-color fz-14 imit-font mb-0">Asked on: <?php the_time(); ?></p>
                                      <a href="<?php the_permalink(); ?>" class="question-title imit-font fw-500 text-decoration-none text-dark rz-lh-38 d-block"><span class="rz-color mr-1">Q.</span> <?php the_title(); ?></a>
                                      <div class="rz-br">
                                          <?php the_post_thumbnail('full', ['img-fluid']); ?>
                                      </div>
                                      <ul class="tags ps-0 d-flex flex-row justify-content-start align-items-center">
                                          <?php
                                          $tags = wp_get_post_terms(get_the_ID(), 'question_tags');
                                          foreach($tags as $tag){
                                              echo '<li class="tag-list"><a href="'.get_term_link($tag->term_id, 'question_tags').'" class="tag-link imit-font fz-12 fw-500 rounded">'.$tag->name.'</a></li>';
                                          }
                                          ?>
                                      </ul>

                                      <?php
                                      if(is_user_logged_in(  )){
                                          ?>
                                          <div class="d-flex flex-row justify-content-start align-items-center mt-3">
                                              <a href="#" class="answer-button btn imit-font fz-14 rz-color fw-500 me-2" data-target="news-feed-answer<?php echo get_the_ID(); ?>" id="comment-button"><?php if(count($answer_count) <= 0 && $user_id !== get_current_user_id()){echo 'Be first to write answer';}else{echo 'Write an answer';} ?></a>
                                              <?php
                                              if($user_id !== get_current_user_id()){
                                                  if(count($answer_count) >= 1){
                                                      ?>
                                                      <span class="point-badge imit-font fz-14 fw-500"><img src="<?php echo plugins_url('images/Group (3).png', __FILE__); ?>" alt=""> <span class=" rz-secondary-color">10 Point</span></span>
                                                      <?php
                                                  }else{
                                                      ?>
                                                      <span class="point-badge imit-font fz-14 fw-500"><img src="<?php echo plugins_url('images/Group.png', __FILE__); ?>" alt=""> <span class=" rz-secondary-color">20 Point</span></span>
                                                      <?php
                                                  }
                                              }
                                              ?>
                                          </div>
                                          <div class="answer-wrapper mt-3" id="news-feed-answer<?php echo get_the_ID(); ?>" style="display: none;">
                                              <form action="" id="answer-form" data-post_id="<?php echo get_the_ID(); ?>">
                                                  <div class="row">
                                                      <div class="col-md-1">
                                                          <div class="profile-image">
                                                              <img src="<?php getProfileImageById(get_current_user_id()); ?>" alt="">
                                                          </div>
                                                      </div>
                                                      <div class="col-md-11">
                                                          <div class="answer-editor">
                                                              <textarea name="answer<?php echo get_the_ID(); ?>" class="imit-font fz-14 form-control" id="answer-textarea" cols="30" rows="10" data-post_id="<?php echo get_the_ID(); ?>"></textarea>
                                                              <ul class="list-group" id="answer_hashtag<?php echo get_the_ID(); ?>">

                                                              </ul>
                                                          </div>
                                                      </div>
                                                  </div>
                                                  <button type="submit" class="btn rz-bg-color text-white imit-font fz-14 ms-auto d-table mt-2 fw-500">Submit Answer</button>
                                              </form>
                                          </div>
                                          <?php
                                      }
                                      ?>
                                  </div>
                              </div>
                              <div class="card-footer border-top-0 p-3 d-flex flex-row justify-content-between align-items-center">
                                  <div class="views text-dark fz-14">
                                      <i class="fas fa-eye"></i>
                                      <span class="counter imit-font fw-500"><span class="counter"><?php echo getPostViews(get_the_ID()) ?></span></span>
                                  </div>


                                    <?php if(is_user_logged_in(  ) && $user_id === get_current_user_id(  )){
                                        ?>
                                        <div class="other text-dark d-flex flex-row justify-content-end align-items-center">
                                            <div class="dropdown">
                                                <a class="text-dark fz-16" href="#" role="button" id="more-option-feed" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-h"></i>
                                                </a>
                                                <ul class="dropdown-menu" aria-labelledby="more-option-feed">
                                                    <li><a class="dropdown-item imit-font fz-14 text-dark" href="#" id="delete-question" data-post_id="<?php echo $post_id; ?>">Delete</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <?php
                                    } ?>


                              </div>
                          </div>
                      </li>
                  <?php endwhile;

                  wp_reset_postdata();
              }else{
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

  function imit_popular_questions_data(){
      global $wpdb;
      $nonce = $_POST['nonce'];
      if(wp_verify_nonce($nonce, 'rz-popular-question-nonce')){
          $page_num = sanitize_key($_POST['page_num']);
            $popularpost  = new WP_Query(array(
                    'post_type' => 'rz_post_question',
                'posts_per_page' => 10,
                'meta_key' => 'post_views_count',
                'orderby' => 'meta_value_num',
                'order' => 'DESC',
                'paged' => $page_num,
                'tax_query' =>  array(
                    array(
                        'taxonomy' => 'question_category',
                        'field' => 'slug',
                        'terms' => 'uncategorised'
                    )
                ),
            ));
            if($popularpost->have_posts()){
                while($popularpost->have_posts()):$popularpost->the_post();
                    $post_id = get_the_ID();
                    $user_id = get_the_author_meta('ID');
                    $answer_count = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_answers WHERE post_id = '$post_id' AND user_id != '$user_id'", ARRAY_A);
                    ?>
                    <li class="news-feed-list mt-3">
                        <div class="card rz-br">
                            <div class="card-body p-0">
                                <div class="p-4">
                                    <div class="created-at rz-secondary-color fz-14 imit-font">Asked on: <?php the_time(); ?></div>
                                    <a href="<?php the_permalink(); ?>" class="question-title imit-font fw-500 text-decoration-none text-dark rz-lh-38"><span class="rz-color mr-1">Q.</span> <?php the_title(); ?></a>
                                    <div class="rz-br my-3">
                                        <?php the_post_thumbnail('full', ['img-fluid']); ?>
                                    </div>
                                    <ul class="tags ps-0 d-flex flex-row justify-content-start align-items-center">
                                        <?php
                                        $tags = wp_get_post_terms(get_the_ID(), 'question_tags');
                                        foreach($tags as $tag){
                                            echo '<li class="tag-list"><a href="'.get_term_link($tag->term_id, 'question_tags').'" class="tag-link imit-font fz-12 fw-500 rounded">'.$tag->name.'</a></li>';
                                        }
                                        ?>
                                    </ul>

                                    <?php
                                    if(is_user_logged_in(  )){
                                        ?>
                                        <div class="d-flex flex-row justify-content-start align-items-center mt-3">
                                            <a href="#" class="answer-button btn imit-font fz-14 rz-color fw-500 me-2" data-target="popular-question<?php echo get_the_ID(); ?>" id="comment-button"><?php if(count($answer_count) <= 0 && $user_id !== get_current_user_id()){echo 'Be first to write answer';}else{echo 'Write an answer';} ?></a>
                                            <?php
                                            if($user_id !== get_current_user_id()){
                                                if(count($answer_count) >= 1){
                                                    ?>
                                                    <span class="point-badge imit-font fz-14 fw-500"><img src="<?php echo plugins_url('images/Group (3).png', __FILE__); ?>" alt=""> <span class=" rz-secondary-color">10 Point</span></span>
                                                    <?php
                                                }else{
                                                    ?>
                                                    <span class="point-badge imit-font fz-14 fw-500"><img src="<?php echo plugins_url('images/Group.png', __FILE__); ?>" alt=""> <span class=" rz-secondary-color">20 Point</span></span>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </div>
                                        <div class="answer-wrapper mt-3" id="popular-question<?php echo get_the_ID(); ?>" style="display: none;">
                                            <form action="" id="answer-form" data-post_id="<?php echo get_the_ID(); ?>">
                                                <div class="row">
                                                    <div class="col-md-1">
                                                        <div class="profile-image">
                                                            <img src="<?php getProfileImageById(get_current_user_id()); ?>" alt="">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-11">
                                                        <div class="answer-editor">
                                                            <textarea name="answer<?php echo get_the_ID(); ?>" class="imit-font fz-14 form-control" id="answer-textarea" cols="30" rows="10" data-post_id="<?php echo get_the_ID(); ?>"></textarea>
                                                            <ul class="list-group" id="answer_hashtag<?php echo get_the_ID(); ?>">

                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                                <button type="submit" class="btn rz-bg-color text-white imit-font fz-14 ms-auto d-table mt-2 fw-500">Submit Answer</button>
                                            </form>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="card-footer p-3 border-top-0 d-flex flex-row justify-content-between align-items-center">
                                <div class="views text-dark fz-14">
                                    <i class="fas fa-eye"></i>
                                    <span class="counter imit-font fw-500"><span class="counter"><?php echo getPostViews(get_the_ID()) ?></span></span>
                                </div>
<!--                                <div class="other text-dark d-flex flex-row justify-content-end align-items-center">-->
<!--                                    <a href="#" class="fz-14 text-dark me-2"><i class="fas fa-share"></i></a>-->
<!--                                    <div class="dropdown">-->
<!--                                        <a class="text-dark fz-16" href="#" role="button" id="more-option-feed" data-bs-toggle="dropdown" aria-expanded="false">-->
<!--                                            <i class="fas fa-ellipsis-h"></i>-->
<!--                                        </a>-->
<!---->
<!--                                        <ul class="dropdown-menu" aria-labelledby="more-option-feed">-->
<!--                                            <li><a class="dropdown-item imit-font fz-14 text-dark" href="#">Action</a></li>-->
<!--                                            <li><a class="dropdown-item imit-font fz-14 text-dark" href="#">Another action</a></li>-->
<!--                                            <li><a class="dropdown-item imit-font fz-14 text-dark" href="#">Something else here</a></li>-->
<!--                                        </ul>-->
<!--                                    </div>-->
<!--                                </div>-->
                            </div>
                        </div>
                    </li>
                <?php
                endwhile;
            }else{
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

  function imit_get_most_answered_question(){
      global $wpdb;
      $nonce = $_POST['nonce'];
      if(wp_verify_nonce($nonce, 'rz-most-commented-nonce')){
          $page_num = sanitize_key($_POST['page_num']);
            $all_answers_post = $wpdb->get_results("SELECT COUNT(post_id) AS post_count, post_id FROM {$wpdb->prefix}rz_answers GROUP BY post_id ORDER BY post_count DESC", ARRAY_A);
            $answer_post_ids = [];
            foreach($all_answers_post as $answer_post){
                array_push($answer_post_ids, $answer_post['post_id']);
            }
            $most_answered_post = new WP_Query([
                    'post_type' => 'rz_post_question',
                'posts_per_page' => 10,
                'post__in' => $answer_post_ids,
                'orderby' => 'post__in',
                'paged' => $page_num,
                'tax_query' =>  array(
                    array(
                        'taxonomy' => 'question_category',
                        'field' => 'slug',
                        'terms' => 'uncategorised'
                    )
                ),
            ]);
            if($most_answered_post->have_posts()){
                while($most_answered_post->have_posts()):$most_answered_post->the_post();
                    $post_id = get_the_ID();
                    $user_id = get_the_author_meta('ID');
                    $answer_count = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_answers WHERE post_id = '$post_id' AND user_id != '$user_id'", ARRAY_A);
                    ?>
                    <li class="news-feed-list mt-3">
                        <div class="card rz-br">
                            <div class="card-body p-0">
                                <div class="p-4">
                                    <div class="created-at rz-secondary-color fz-14 imit-font">Asked on: <?php the_time(); ?></div>
                                    <a href="<?php the_permalink(); ?>" class="question-title imit-font fw-500 text-decoration-none text-dark rz-lh-38"><span class="rz-color mr-1">Q.</span> <?php the_title(); ?></a>
                                    <div class="rz-br my-3">
                                        <?php the_post_thumbnail('full', ['img-fluid']); ?>
                                    </div>
                                    <ul class="tags ps-0 d-flex flex-row justify-content-start align-items-center">
                                        <?php
                                        $tags = wp_get_post_terms(get_the_ID(), 'question_tags');
                                        foreach($tags as $tag){
                                            echo '<li class="tag-list"><a href="'.get_term_link($tag->term_id, 'question_tags').'" class="tag-link imit-font fz-12 fw-500 rounded">'.$tag->name.'</a></li>';
                                        }
                                        ?>
                                    </ul>

                                    <?php
                                    if(is_user_logged_in(  )){
                                        ?>
                                        <div class="d-flex flex-row justify-content-start align-items-center mt-3">
                                            <a href="#" class="answer-button btn imit-font fz-14 rz-color fw-500 me-2" data-target="most-answer-form<?php echo get_the_ID(); ?>" id="comment-button"><?php if(count($answer_count) <= 0 && $user_id !== get_current_user_id()){echo 'Be first to write answer';}else{echo 'Write an answer';} ?></a>
                                            <?php
                                            if($user_id !== get_current_user_id()){
                                                if(count($answer_count) >= 1){
                                                    ?>
                                                    <span class="point-badge imit-font fz-14 fw-500"><img src="<?php echo plugins_url('images/Group (3).png', __FILE__); ?>" alt=""> <span class=" rz-secondary-color">10 Point</span></span>
                                                    <?php
                                                }else{
                                                    ?>
                                                    <span class="point-badge imit-font fz-14 fw-500"><img src="<?php echo plugins_url('images/Group.png', __FILE__); ?>" alt=""> <span class=" rz-secondary-color">20 Point</span></span>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </div>
                                        <div class="answer-wrapper mt-3" id="most-answer-form<?php echo get_the_ID(); ?>" style="display: none;">
                                            <form action="" id="answer-form" data-post_id="<?php echo get_the_ID(); ?>">
                                                <div class="row">
                                                    <div class="col-md-1">
                                                        <div class="profile-image">
                                                            <img src="<?php getProfileImageById(get_current_user_id()); ?>" alt="">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-11">
                                                        <div class="answer-editor">
                                                            <textarea name="answer<?php echo get_the_ID(); ?>" class="imit-font fz-14 form-control" id="answer-textarea" cols="30" rows="10" data-post_id="<?php echo get_the_ID(); ?>"></textarea>
                                                            <ul class="list-group" id="answer_hashtag<?php echo get_the_ID(); ?>">

                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                                <button type="submit" class="btn rz-bg-color text-white imit-font fz-14 ms-auto d-table mt-2 fw-500">Submit Answer</button>
                                            </form>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="card-footer p-3 border-top-0 d-flex flex-row justify-content-between align-items-center">
                                <div class="views text-dark fz-14">
                                    <i class="fas fa-eye"></i>
                                    <span class="counter imit-font fw-500"><span class="counter"><?php echo getPostViews(get_the_ID()) ?></span></span>
                                </div>
<!--                                <div class="other text-dark d-flex flex-row justify-content-end align-items-center">-->
<!--                                    <a href="#" class="fz-14 text-dark me-2"><i class="fas fa-share"></i></a>-->
<!--                                    <div class="dropdown">-->
<!--                                        <a class="text-dark fz-16" href="#" role="button" id="more-option-feed" data-bs-toggle="dropdown" aria-expanded="false">-->
<!--                                            <i class="fas fa-ellipsis-h"></i>-->
<!--                                        </a>-->
<!---->
<!--                                        <ul class="dropdown-menu" aria-labelledby="more-option-feed">-->
<!--                                            <li><a class="dropdown-item imit-font fz-14 text-dark" href="#">Action</a></li>-->
<!--                                            <li><a class="dropdown-item imit-font fz-14 text-dark" href="#">Another action</a></li>-->
<!--                                            <li><a class="dropdown-item imit-font fz-14 text-dark" href="#">Something else here</a></li>-->
<!--                                        </ul>-->
<!--                                    </div>-->
<!--                                </div>-->
                            </div>
                        </div>
                    </li>
                <?php endwhile;
            }else{
                exit('mostAnsweredPostReachmax');
            }
      }
      die();
  }