<?php


/**
 * direct access not allowed
 */
if (!defined('ABSPATH')) {
    die(__('Direct access not allowed.', 'imit-recozilla'));
}


/**
 * for register
 */
add_shortcode('imit-rz-register', function () {
    ob_start();
?>
    <section class="login overflow-hidden" style="background-image: url('<?php echo plugins_url('images/loginbg.jpeg', __FILE__); ?>');">
        <div class="rz-mid">
            <div class="row px-sm-0 px-2" style="min-height: 100vh;">
                <div class="col-lg-6">
                    <h4 class="title rz-color imit-font mx-2">Welcome to</h4>
                    <img class="logo mx-2" src="<?php echo plugins_url('images/logo.png', __FILE__); ?>" alt="" class="w-100">
                    <p class="mb-0 subtitle imit-font mt-3 mx-2">A place to learn from knowledge and experiences of others and share yours</p>
                </div>
                <div class="col-lg-6">
                    <div class="rz-br bg-white rz-login-card mb-3" style="margin-top: 52px;">
                        <?php
                        if (is_user_logged_in() == false) {
                        ?>
                            <h2 class="login-title pt-5 px-4">Sign Up</h2>

                            <form id="rz-register-form" class="mt-4 px-4">
                                <div class="row mb-3">
                                    <div class="col-sm-6">
                                        <label for="first_name" class="imit-font fz-16 fw-16">First Name</label>
                                        <input name="first_name" type="text" id="first_name" class="form-control border-0 mt-2 imit-font fz-14 rounded" placeholder="Ex: Jhon">
                                        <div class="invalid-feedback imit-font fz-16" id="first-name-err"></div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="last_name" class="form-label">Last Name</label>
                                        <input name="last_name" type="text" id="last_name" class="form-control border-0 mt-2 imit-font fz-14 rounded" placeholder="Ex: Doe">
                                        <div class="invalid-feedback imit-font fz-16" id="last-name-err"></div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="username" class="imit-font fz-16 fw-16">Enter username</label>
                                    <input name="username" id="username" type="text" class="form-control border-0 mt-2 imit-font fz-14 rounded" placeholder="Ex: jhondoe">
                                    <div class="invalid-feedback imit-font fz-16" id="reg-username-err"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="imit-font fz-16 fw-16">Enter your email</label>
                                    <input name="email" id="email" type="text" class="form-control border-0 mt-2 imit-font fz-14 rounded" placeholder="Ex: you@email.com">
                                    <div class="invalid-feedback imit-font fz-16" id="reg-email-err"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="imit-font fz-16 fw-16">Enter your password</label>
                                    <div class="position-relative">
                                        <input name="password" id="password" type="password" class="form-control border-0 mt-2 imit-font fz-14 rounded password-toggle" placeholder="************">
                                        <div class="invalid-feedback imit-font fz-16" id="reg-password-err"></div>
                                        <i class="fas fa-eye" id="show-password"></i>
                                    </div>
                                </div>

                                <p class="imit-font fz-14 rz-secondary-color my-3">By continuing you indicate that you agree to Recozilla. <a href="<?php echo site_url(); ?>/terms-conditions/" class="rz-color text-decoration-none fw-500">Terms of Service and Privacy Policy.</a></p>

                                <button type="submit" class="btn rz-bg-color text-white imit-font fz-16 mt-3 w-100">Sign Up</button>
                            </form>

                            <p class="mb-0 rz-secondary-color imit-font fz-14 text-center py-4 px-3">Or Continue with</p>

                            <div class="d-flex flex-row justify-content-between align-items-center px-4 recozilla-social-login">
                                <?php echo do_shortcode('[miniorange_social_login]'); ?>
                            </div>

                            <p class="imit-font fz-14 rz-secondary-color text-center py-5">Already have an account? <a href="<?php echo site_url(); ?>/login" class="rz-color text-decoration-none fw-500">Login</a></p>
                        <?php
                        } else {
                            $user_data = get_userdata(get_current_user_id());
                        ?>
                            <p class="logged-in-user-info imit-font rz-secondary-color fz-14 mt-2 px-3 py-4 m-0">Logged in as <a href="#" class="mx-2 text-dark fw-500"><i class="fas fa-user me-1"></i><?php echo ucfirst($user_data->display_name); ?></a> <a href="<?php echo wp_logout_url(get_permalink()); ?>" class="rz-secondary-color fw-500"><i class="fas fa-sign-out-alt me-1"></i>Log out</a></p>
                        <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php
    return ob_get_clean();
});


/**
 * new user registration
 */
add_action('wp_ajax_nopriv_imit_new_user_register', function () {
    global $wpdb;
    $nonce = $_POST['nonce'];
    if (wp_verify_nonce($nonce, 'rz-registe-nonce')) {
        if (!session_id()) {
            session_start();
        }
        $_SESSION['verify_email'] = $_POST['email'];
        $first_name = sanitize_text_field($_POST['first_name']);
        $last_name = sanitize_text_field($_POST['last_name']);
        $username = sanitize_text_field($_POST['username']);
        $email = sanitize_text_field($_POST['email']);
        $password = sanitize_text_field($_POST['password']);


        if (empty($first_name) || empty($last_name) || empty($username) || empty($email) || empty($password)) {
            if (empty($first_name)) {
                $response['first_name_message'] = 'First name required';
                $response['first_name_error'] = true;
                $response['redirect'] = false;
            }

            if (empty($last_name)) {
                $response['last_name_message'] = 'Last name required';
                $response['last_name_error'] = true;
                $response['redirect'] = false;
            }

            if (empty($username)) {
                $response['username_message'] = 'Username required';
                $response['username_error'] = true;
                $response['redirect'] = false;
            }

            if (empty($email)) {
                $response['email_message'] = 'Email reuqired';
                $response['email_error'] = true;
                $response['redirect'] = false;
            }

            if (empty($password)) {
                $response['password_message'] = 'Password required';
                $response['password_error'] = true;
                $response['redirect'] = false;
            }
        } else {
            if (username_exists($username) || filter_var($email, FILTER_VALIDATE_EMAIL) == false || email_exists($email)) {
                if (username_exists($username)) {
                    $response['username_message'] = 'Username exists';
                    $response['username_error'] = true;
                    $response['redirect'] = false;
                }
                if (filter_var($email, FILTER_VALIDATE_EMAIL) == false) {
                    $response['email_message'] = 'Invalid email address';
                    $response['email_error'] = true;
                    $response['redirect'] = false;
                }
                if (email_exists($email)) {
                    $response['email_message'] = 'Email exists.';
                    $response['email_error'] = true;
                    $response['redirect'] = false;
                }
            } else if (preg_match('/^[a-zA-Z]+$/', $first_name) === 0) {
                $response['first_name_message'] = 'First name contains charater only.';
                $response['first_name_error'] = true;
                $response['redirect'] = false;
            } else if (preg_match('/^[a-zA-Z]+$/', $last_name) === 0) {
                $response['last_name_message'] = 'Last name contains charater only.';
                $response['last_name_error'] = true;
                $response['redirect'] = false;
            } else if (preg_match('/^[a-z0-9]+$/', $username) === 0) {
                $response['username_message'] = 'Username contains lower characters and numbers only.';
                $response['username_error'] = true;
                $response['redirect'] = false;
            } else if (strlen($password) < 8) {
                $response['password_message'] = 'Password atleast 8 characters';
                $response['password_error'] = true;
                $response['redirect'] = false;
            } else {

                $user_id = wp_insert_user([
                    'user_login' => $username,
                    'user_pass' => $password,
                    'user_nicename' => $username,
                    'user_email' => $email,
                    'first_name'  => $first_name,
                    'last_name' => $last_name,
                ]);

                $path = make_avatar(strtoupper(substr(trim($first_name), 0, 1)));

                $wpdb->insert($wpdb->prefix . 'rz_user_profile_data', [
                    'user_id' => $user_id,
                    'profile_image' => $path,
                    'created_at' => wpDateTime(),
                    'updated_at' => wpDateTime()
                ]);


                $creds = array(
                    'user_login'    => $username,
                    'user_password' => $password,
                    'remember'      => true
                );
                wp_signon($creds, false);
                $response['redirect_to'] = site_url() . '/email-verification-message';
                $response['redirect'] = true;
                $response['username_error'] = false;
                $response['email_error'] = false;
                $response['password_error'] = false;
            }
        }

        echo json_encode($response);
    }
    die();
});
