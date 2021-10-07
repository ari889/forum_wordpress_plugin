<?php

/**
 * direct access not allowed
 */
if(!defined('ABSPATH')){
    die(__('Direct access not allowed.', 'imit-recozilla'));
}

/**
 * tags archive page
 */
add_shortcode('imit-tags-archive', function(){
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
                                    <a href="<?php echo site_url(  ); ?>" class="imit-font fz-14 rz-secondary-color text-decoration-none">Home<span class="mx-1">/</span></a>
                                </li>
                                <li class="list-unstyled">
                                    <a href="#" class="imit-font fz-14 rz-secondary-color text-decoration-none active">Tags<span class="mx-1">/</span></a>
                                </li>
                            </ul>
                        </div>
                        <div class="user-header-right mt-sm-0 mt-2">
                            <form id="search-terms">
                                <input name="search-terms" type="text" class="form-control imit-font fz-14" placeholder="Search tags">
                                <button type="submit" class="text-dark fz-14 border-0 bg-transparent"><i class="fas fa-search"></i></button>
                            </form>
                        </div>
                    </div>
                    <ul class="hash-tags ps-0 mb-0 row mt-3" id="fetch-all-terms">
                        
                    </ul>
                    <div id="tag-archive-loader" style="display: none;">
                       <div class="d-flex justify-content-center mt-2">
                           <div class="spinner-border" role="status">
                               <span class="visually-hidden">Loading...</span>
                           </div>
                       </div>
                   </div>
                </div>
                <div class="col-lg-3 mt-2 mt-lg-0">
                    <?php echo do_shortcode( '[join-partner-program]' ); ?>
                </div>
            </div>
        </div>
    </section>
    <?php
    return ob_get_clean();
});


/**
 * get all tags
 */
add_action('wp_ajax_nopriv_rz_fetch_tags_archive_data', 'rz_fetch_tags_data');
add_action('wp_ajax_rz_fetch_tags_archive_data', 'rz_fetch_tags_data');

function rz_fetch_tags_data(){
    global $wpdb;
    $nonce = $_POST['nonce'];
    if(wp_verify_nonce($nonce, 'rz-fetch-tags-archive-nonce')){
        $start = sanitize_key($_POST['start']);
        $limit = sanitize_key($_POST['limit']);
        $offset = ( $start-1 ) * $limit;

        /**
         * check current is partner or not
         */
        $user_id = get_current_user_id(  );
        $is_user_already_a_partner = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_user_programs WHERE user_id = '$user_id' AND (status = '1' OR status = '0')", ARRAY_A);

        $tags = get_terms(array(
            'taxonomy' => 'question_tags',
            'orderby' => 'count',
            'order' => 'DESC',
            'number' => $limit,
            'offset' => $offset
        ));

        if(count($tags) > 0){
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
                $term_id = $tag->term_id;
                $user_id = get_current_user_id();
                $rz_following_tags = $wpdb->prefix.'rz_following_tags';
                $is_user_already_followed = $wpdb->get_row("SELECT * FROM {$rz_following_tags} WHERE user_id = '{$user_id}' AND term_id = '{$term_id}'");
                ?>
                <li class="hash-list list-unstyled <?php echo (is_user_logged_in(  ) == false || count($is_user_already_a_partner) < 1) ? 'col-md-6' : 'col-lg-4 col-sm-6 col-12'; ?> my-2">
                    <a href="<?php echo get_term_link($tag->term_id, 'question_tags'); ?>" class="hash-link rz-br rz-border d-block" style="padding: 20px;">
                        <div class="hash-top d-flex flex-row justify-content-between align-items-center" style="margin-bottom: 12px;">
                            <p class="imit-font fw-500 fz-16 text-dark d-block mb-0">#<?php echo $tag->name; ?></p>
                            <button type="button" class="add-post-by-tag p-0 <?php if(!empty($is_user_already_followed)){echo 'rz-color';}else{echo 'rz-secondary-color';} ?> bg-transparent fz-14 border-0" data-term_id="<?php echo $tag->term_id; ?>" id="follow-tag"><?php if(!empty($is_user_already_followed)){echo '<i class="fas fa-check-square"></i>';}else{echo '<i class="fas fa-plus-circle"></i>';} ?></button>
                        </div>
                        <div class="d-flex flex-row justify-content-between align-items-center me-2">
                            <p class="rz-secondary-color imit-font fz-12 fw-400 mb-0"><?php echo $tag->count; ?> Question</p>
                            <p class="rz-secondary-color imit-font fz-12 fw-400 mb-0"><?php echo $count_answer; ?> Answers</p>
                            <p class="rz-secondary-color imit-font fz-12 fw-400 mb-0"><?php echo $total_view; ?> Views</p>
                        </div>
                    </a>
                </li>
                    <?php
            }
        }else{
            exit('tagReachMax');
        }
    }
    die();
}


/**
 * get terms by name
 */
add_action('wp_ajax_nopriv_rz_search_terms_by_name', 'rz_get_terms_by_name');
add_action('wp_ajax_rz_search_terms_by_name', 'rz_get_terms_by_name');

function rz_get_terms_by_name(){
    global $wpdb;
    $nonce = $_POST['nonce'];
    if(wp_verify_nonce( $nonce, 'rz-search-terms-nonce' )){
        $search_terms = sanitize_text_field( $_POST['search-terms'] );

        /**
         * check current is partner or not
         */
        $user_id = get_current_user_id(  );
        $is_user_already_a_partner = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_user_programs WHERE user_id = '$user_id' AND (status = '1' OR status = '0')", ARRAY_A);


        if(!empty($search_terms)){
            $args = array(
                'taxonomy'      => ['question_tags', 'discussion_tags'], // taxonomy name
                'orderby'       => 'count', 
                'order'         => 'DESC',
                'hide_empty'    => true,
                'fields'        => 'all',
                'name__like'    => $search_terms,
                'number'        => 20
            ); 
            
            $terms = get_terms( $args );
            foreach($terms as $tag){
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

                $term_id = $tag->term_id;
                $user_id = get_current_user_id();
                $rz_following_tags = $wpdb->prefix.'rz_following_tags';
                $is_user_already_followed = $wpdb->get_row("SELECT * FROM {$rz_following_tags} WHERE user_id = '{$user_id}' AND term_id = '{$term_id}'");
                ?>
                <li class="hash-list list-unstyled <?php echo (is_user_logged_in(  ) == false || count($is_user_already_a_partner) < 1) ? 'col-md-4' : 'col-md-3'; ?> my-2">
                        <div class="bg-white py-2 px-3 rounded border">
                            <div class="hash-top d-flex flex-row justify-content-between align-items-center">
                                <a href="<?php echo get_term_link($tag->term_id, $tag->taxonomy); ?>" class="imit-font fw-500 fz-16 text-dark d-block">#<?php echo $tag->name; ?></a>
                                <button type="button" class="add-post-by-tag p-0 <?php if(!empty($is_user_already_followed)){echo 'rz-color';}else{echo 'rz-secondary-color';} ?> bg-transparent fz-14 border-0" data-term_id="<?php echo $tag->term_id; ?>" id="follow-tag"><?php if(!empty($is_user_already_followed)){echo '<i class="fas fa-check-square"></i>';}else{echo '<i class="fas fa-plus-circle"></i>';} ?></button>
                            </div>
                            <div class="d-flex flex-row justify-content-between align-items-center me-2">
                                <p class="rz-secondary-color imit-font fz-12 fw-400 mb-0"><?php echo $tag->count; ?> Question</p>
                                <p class="rz-secondary-color imit-font fz-12 fw-400 mb-0"><?php echo $count_answer; ?> Answers</p>
                                <p class="rz-secondary-color imit-font fz-12 fw-400 mb-0"><?php echo $total_view; ?> Views</p>
                            </div>
                        </div>
                    </li>
                <?php
            }
        }
    }
    die();
}
