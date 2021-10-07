<?php

if(!defined('ABSPATH')){
    die(__('You aren\'t allowed to do this.', 'imit-recozilla'));
}

/**
 * login page
 */
add_shortcode( 'imit-rz-reset-password', function(){
    ob_start();
    ?>
    <section class="login overflow-hidden" style="background-image: url('<?php echo plugins_url( 'images/loginbg.jpeg', __FILE__ ) ?>');">
        <div class="rz-mid">
            <div class="row" style="min-height: 100vh;">
                <div class="col-lg-6">
                    <h3 class="title rz-color imit-font">Welcome to</h3>
                    <img class="logo" src="<?php echo plugins_url('images/logo.png', __FILE__); ?>" alt="">
                    <p class="mb-0 subtitle imit-font mt-3">A place to learn from knowledge and experiences of others and share yours</p>
                </div>
                <div class="col-lg-6">
                    <div class="rz-br bg-white rz-login-card mb-3" style="margin-top: 150px;">
                        <?php
                        if(is_user_logged_in() == false){
                            ?>
                            <div class="p-4">
                                <?php echo do_shortcode( '[reset_password]' ); ?>
                            </div>
                            <?php
                        }else{
                            $user_data = get_userdata(get_current_user_id());
                            ?>
                            <p class="logged-in-user-info imit-font rz-secondary-color fz-14 mt-2 px-5 py-4 m-0">Logged in as <a href="#" class="mx-2 text-dark fw-500"><i class="fas fa-user me-1"></i><?php echo ucfirst($user_data->display_name); ?></a> <a href="<?php echo wp_logout_url( get_permalink() ); ?>" class="rz-secondary-color fw-500"><i class="fas fa-sign-out-alt me-1"></i>Log out</a></p>
                            <?php
                        } ?>
                        <div class="join rz-bg-color p-5" style="background-image: url('<?php echo plugins_url('images/Group 237.png', __FILE__); ?>');min-height: auto !important;">
                            <h3 class="title m-0 text-white imit-font fw-500" style="font-size: 24px;text-transform: none;">Write answers or create posts on Recozilla and earn Money</h3>
                            <a href="<?php echo site_url(); ?>/join-partner-program/" class="btn bg-white fz-14 rz-color imit-font fw-500 mt-3 py-2 px-4">Join our Partner Program </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php
    return ob_get_clean();
} );