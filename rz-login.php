<?php


/**
 * direct access not allowed
 */
if (!defined('ABSPATH')) {
    die(__('Direct access not allowed.', 'imit-recozilla'));
}

/**
 * login page
 */
add_shortcode('imit-rz-login', function () {
    ob_start();
    if (is_user_logged_in()) {
?>
        <script>
            window.location.href = '<?php echo site_url() ?>/questions'
        </script>
    <?php
    }
    ?>
    <section class="login overflow-hidden" style="background-image: url('<?php echo plugins_url('images/loginbg.jpeg', __FILE__) ?>');">
        <div class="rz-mid">
            <div class="row px-sm-0 px-2" style="min-height: 100vh;">
                <div class="col-lg-6">
                    <h3 class="title rz-color imit-font mx-2">Welcome to</h3>
                    <img class="logo mx-2" src="<?php echo plugins_url('images/logo.png', __FILE__); ?>" alt="">
                    <p class="mb-0 subtitle imit-font mt-3 mx-2">A place to learn from knowledge and experiences of others and share yours</p>
                </div>
                <div class="col-lg-6">
                    <div class="rz-br bg-white rz-login-card mb-3" style="margin-top: 52px;">
                        <?php
                        if (is_user_logged_in() == false) {
                        ?>
                            <div class="d-flex flex-row justify-content-between align-items-center pt-5 px-5">
                                <h2 class="login-title">Login</h2>
                                <a href="<?php echo site_url(); ?>/questions" class="d-block rz-color fz-15 imit-font fw-500">Skip without Login</a>
                            </div>
                            <form id="rz-login" class="mt-4 px-5 login-form">
                                <div id="login_error" class="imit-font fz-14 text-danger"></div>
                                <div class="mb-3">
                                    <label for="" class="imit-font fz-16 fw-16">Enter your email</label>
                                    <input name="email" type="text" class="form-control border-0 rounded mt-2 imit-font fz-14" placeholder="you@email.com">
                                    <div class="invalid-feedback imit-font fz-14" id="login-email"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="" class="imit-font fz-16 fw-16">Enter your password</label>
                                    <div class="position-relative">
                                        <input name="password" type="password" class="form-control border-0 rounded mt-2 imit-font fz-14 password-toggle" placeholder="Valid password">
                                        <i class="fas fa-eye" id="show-password"></i>
                                    </div>
                                    <div class="invalid-feedback imit-font fz-14" id="login-password"></div>
                                </div>
                                <div class="d-flex flex-sm-row flex-column justify-content-between aligm-items-start align-items-sm-center">
                                    <div class="form-check ps-0 d-flex flex-row justify-content-start align-items-center">
                                        <input name="remember-me" class="form-check-input m-0" type="checkbox" value="yes" id="remember">
                                        <label class="form-check-label imit-font fz-14 ms-2" for="remember">
                                            Remember Me!
                                        </label>
                                    </div>
                                    <a href="<?php echo site_url(); ?>/reset-password" class="imit-font fz-14 text-dark text-decoration-none mt-sm-0 mt-2">Forgot Password?</a>
                                </div>
                                <button type="submit" class="btn rz-bg-color text-white imit-font fz-16 mt-3 w-100">Login</button>
                            </form>

                            <p class="mb-0 rz-secondary-color imit-font fz-14 text-center py-4 px-3">Or Login with</p>

                            <div class="d-flex flex-row justify-content-between align-items-center px-5 recozilla-social-login">
                                <?php echo do_shortcode('[miniorange_social_login]'); ?>
                            </div>
                            <p class="imit-font fz-14 rz-secondary-color text-center my-4 px-5">Don't have an account? <a href="<?php echo site_url(); ?>/register" class="rz-color text-decoration-none fw-500">Create now</a></p>
                        <?php
                        } else {
                            $user_data = get_userdata(get_current_user_id());
                        ?>
                            <p class="logged-in-user-info imit-font rz-secondary-color fz-14 mt-2 px-5 py-4 m-0">Logged in as <a href="#" class="mx-2 text-dark fw-500"><i class="fas fa-user me-1"></i><?php echo ucfirst($user_data->display_name); ?></a> <a href="<?php echo wp_logout_url(get_permalink()); ?>" class="rz-secondary-color fw-500"><i class="fas fa-sign-out-alt me-1"></i>Log out</a></p>
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
});


/**
 * login action
 */
add_action('wp_ajax_nopriv_imit_recozilla_login', 'rz_login');

function rz_login()
{
    global $wpdb;

    $nonce = $_POST['nonce'];

    if (wp_verify_nonce($nonce, 'rz-login-nonce')) {
        $email = sanitize_text_field($_POST['email']);
        $password = sanitize_text_field($_POST['password']);
        $remember_response = sanitize_text_field($_POST['remember']);
        $remember = '';

        if ($remember_response == 'yes') {
            $remember = true;
        } else {
            $remember = false;
        }

        if (empty($email) || empty($password)) {
            if (empty($email)) {
                $response['email_message'] = 'Email or username required.';
                $response['email'] = true;
                $response['redirect'] = false;
            }

            if (empty($password)) {
                $response['password_message'] = 'Password required.';
                $response['password'] = true;
                $response['redirect'] = false;
            }
        } else {
            $creds = array(
                'user_login'    => $email,
                'user_password' => $password,
                'remember'      => $remember
            );

            $user = wp_signon($creds, true);


            if (is_wp_error($user)) {
                $response['error_message'] = $user->get_error_message();
                $response['error'] = true;
                $response['redirect'] = false;
            } else {
                $response['redirect_to'] = site_url() . '/questions';
                $response['redirect'] = true;
                $response['error'] = false;
            }

            $response['email'] = false;
            $response['password'] = false;
        }

        echo json_encode($response);
    }

    die();
}

/**
 * login using modal
 */
add_action('wp_ajax_nopriv_rz_login_form_using_modal', function () {
    $nonce = $_POST['nonce'];
    if (wp_verify_nonce($nonce, 'rz-login-using-modal-nonce')) {
        $email = sanitize_text_field($_POST['email']);
        $password = sanitize_text_field($_POST['password']);
        $remember_response = sanitize_text_field($_POST['remember']);
        $remember = '';

        if ($remember_response == 'yes') {
            $remember = true;
        } else {
            $remember = false;
        }

        if (empty($email) || empty($password)) {
            if (empty($email)) {
                $response['email_message'] = 'Email or username required.';
                $response['email'] = true;
                $response['redirect'] = false;
            }

            if (empty($password)) {
                $response['password_message'] = 'Password required.';
                $response['password'] = true;
                $response['redirect'] = false;
            }
        } else {
            $creds = array(
                'user_login'    => $email,
                'user_password' => $password,
                'remember'      => $remember
            );

            $user = wp_signon($creds, true);


            if (is_wp_error($user)) {
                $response['error_message'] = $user->get_error_message();
                $response['error'] = true;
                $response['redirect'] = false;
            } else {
                $response['redirect'] = true;
                $response['error'] = false;
            }

            $response['email'] = false;
            $response['password'] = false;
        }

        echo json_encode($response);
    }
    die();
});
