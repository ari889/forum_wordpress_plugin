<?php

/**
 * for register
 */
add_shortcode( 'imit-rz-register', function(){
    ob_start();
        ?>
        <section class="login overflow-hidden" style="background-image: url('<?php echo plugins_url('images/loginbg.png', __FILE__); ?>');">
            <div class="rz-mid">
                <div class="row" style="min-height: 100vh;">
                    <div class="col-lg-6">
                        <h4 class="title rz-color imit-font">Welcome to</h4>
                        <img class="logo" src="<?php echo plugins_url('images/logo.png', __FILE__); ?>" alt="" class="w-100">
                        <p class="mb-0 subtitle imit-font mt-3">A place to learn from knowledge and experiences of others and share yours</p>
                    </div>
                    <div class="col-lg-6">
                        <div class="rz-br bg-white rz-login-card mb-3" style="margin-top: 150px;">
                            <?php
                            if(is_user_logged_in() == false){
                                ?>
                                <h2 class="login-title pt-5 px-4">Sign Up</h2>

                                <form id="rz-register-form" class="mt-4 px-4">
                                    <div class="mb-3">
                                        <label for="" class="imit-font fz-16 fw-16">Enter username</label>
                                        <input name="username" type="text" class="form-control mt-2 imit-font fz-14 rounded" placeholder="Ex: jhondoe">
                                        <div class="invalid-feedback imit-font fz-16" id="reg-username-err"></div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="" class="imit-font fz-16 fw-16">Enter your email</label>
                                        <input name="email" type="text" class="form-control mt-2 imit-font fz-14 rounded" placeholder="Ex: you@email.com">
                                        <div class="invalid-feedback imit-font fz-16" id="reg-email-err"></div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="" class="imit-font fz-16 fw-16">Enter your password</label>
                                        <input name="password" type="password" class="form-control mt-2 imit-font fz-14 rounded" placeholder="************">
                                        <div class="invalid-feedback imit-font fz-16" id="reg-password-err"></div>
                                    </div>

                                    <p class="imit-font fz-14 rz-secondary-color my-3">By continuing you indicate that you agree to Recozilla. <a href="#" class="rz-color text-decoration-none fw-500">Terms of Service and Privacy Policy.</a></p>

                                    <button type="submit" class="btn rz-bg-color text-white imit-font fz-16 mt-3 w-100">Sign Up</button>
                                </form>

                                <p class="mb-0 rz-secondary-color imit-font fz-14 text-center py-4 px-3">Or Continue with</p>

                                <div class="d-flex flex-row justify-content-between align-items-center px-4">
                                    <?php echo do_shortcode('[miniorange_social_login]'); ?>
                                </div>

                                <p class="imit-font fz-14 rz-secondary-color text-center py-5">Already have an account?  <a href="<?php echo site_url(); ?>/login" class="rz-color text-decoration-none fw-500">Login</a></p>
                                    <?php
                            }else{
                                $user_data = get_userdata(get_current_user_id());
                                ?>
                                <p class="logged-in-user-info imit-font rz-secondary-color fz-14 mt-2 px-3 py-4 m-0">Logged in as <a href="#" class="mx-2 text-dark fw-500"><i class="fas fa-user me-1"></i><?php echo ucfirst($user_data->display_name); ?></a> <a href="<?php echo wp_logout_url( get_permalink() ); ?>" class="rz-secondary-color fw-500"><i class="fas fa-sign-out-alt me-1"></i>Log out</a></p>
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
 } );


 /**
  * new user registration
  */
  add_action('wp_ajax_nopriv_imit_new_user_register', function(){
    $nonce = $_POST['nonce'];
    if(wp_verify_nonce( $nonce, 'rz-registe-nonce' )){
        $username = sanitize_text_field( $_POST['username'] );
        $email = sanitize_text_field( $_POST['email'] );
        $password = sanitize_text_field( $_POST['password'] );
        

        if(empty($username) || empty($email) || empty($password)){
            if(empty($username)){
                $response['username_message'] = 'Username required';
                $response['username_error'] = true;
                $response['redirect'] = false;
            }

            if(empty($email)){
                $response['email_message'] = 'Email reuqired';
                $response['email_error'] = true;
                $response['redirect'] = false;
            }

            if(empty($password)){
                $response['password_message'] = 'Password required';
                $response['password_error'] = true;
                $response['redirect'] = false;
            }
        }else{
            if(username_exists( $username ) || filter_var($email, FILTER_VALIDATE_EMAIL) == false || email_exists( $email )){
                if(username_exists( $username )){
                    $response['username_message'] = 'Username exists';
                    $response['username_error'] = true;
                    $response['redirect'] = false;
                }
                if(filter_var($email, FILTER_VALIDATE_EMAIL) == false){
                    $response['email_message'] = 'Invalid email address';
                    $response['email_error'] = true;
                    $response['redirect'] = false;
                }
                if(email_exists( $email )){
                    $response['email_message'] = 'Email exists.';
                    $response['email_error'] = true;
                    $response['redirect'] = false;
                }
            }else if(validate_username($username) === false){
                $response['username_message'] = 'Username contains characters and numbers only.';
                $response['username_error'] = true;
                $response['redirect'] = false;
            }else{

                wp_insert_user( [
                    'user_login' => $username,
                    'user_pass' => $password,
                    'user_nicename' => $username,
                    'user_email' => $email,
                    'first_name'  => $username,
                ] );
                $creds = array(
                    'user_login'    => $username,
                    'user_password' => $password,
                    'remember'      => true
                );
                wp_signon( $creds, false );
                $response['redirect_to'] = site_url();
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