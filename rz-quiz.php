<?php



/**
 * add quiz add option
 */
function rz_add_quiz(){
    global $wpdb;
    ?>
    <div class="card p-0 w-100">
        <div class="card-header">
            <h2 style="font-size: 20px" class="m-0">Add new quiz</h2>
        </div>
        <div class="card-body">
            <form action="<?php echo admin_url('admin-post.php'); ?>" method="POST">
                <?php wp_nonce_field( 'imit-add-quiz-nonce', 'nonce' ); ?>
                <input type="hidden" name="action" value="rz_add_quiz_action">
                <input name="quiz_name" type="text" class="form-control" placeholder="Please enter quiz name...">
                <?php submit_button( 'Add quiz' ); ?>
            </form>
        </div>
    </div>
    <?php
    die();
}

/**
 * add quiz action
 */
add_action('admin_post_rz_add_quiz_action', function(){
    global $wpdb;
    $nonce = $_POST['nonce'];
    if(wp_verify_nonce( $nonce, 'imit-add-quiz-nonce' )){
        $rz_quizzes = $wpdb->prefix.'rz_quizzes';
        $quiz_name = sanitize_text_field( $_POST['quiz_name'] );

        if(!empty($quiz_name)){
            $wpdb->insert($rz_quizzes, [
                'quiz_name' => $quiz_name
            ]);
            wp_redirect( 'admin.php?page=rzAddQuiz' );
        }
    }
    die();
});


/**
 * manage questions
 */
function rz_manage_questions(){
    global $wpdb;
    $get_all_quizes = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_quizzes ORDER BY id DESC", ARRAY_A);
    ?>
    <div class="card p-0 mw-100 me-3">
        <div class="card-header">
            <h2 style="font-size: 20px" class="m-0">Add answer</h2>
        </div>
        <div class="card-body">
            <form action="">
                <label for="quiz" class="form-label"></label>
                <select name="quiz" id="quiz" class="form-control">
                    <?php 
                    foreach($get_all_quizes as $quiz){
                        echo '<option value="'.$quiz['id'].'">'.$quiz['quiz_name'].'</option>';
                    }
                    ?>
                </select>
                <ul class="questions ps-0 mb-0 list-group" id="add-qnswers-wrapper">
                    <li class="question-list list-group-item mt-3">
                        <div class="d-flex flex-row justify-content-between align-items-center">
                            <input type="text" class="form-control" name="question[]" placeholder="Add question...">
                            <div class="btn-group ms-2">
                                <button type="button" class="btn btn-info d-flex flex-row justify-content-start align-items-center"><span class="me-1">Add</span> <span>answer</span></button>
                                <button type="button" class="btn btn-danger">X</button>
                            </div>
                        </div>
                        <ul class="list-group my-2">
                            <li class="list-group-item d-flex flex-row justify-content-between align-items-center">
                                <input type="text" class="form-control" placeholder="Add answer answer">
                                <input type="checkbox" class="mx-2">
                                <button type="button" class="btn btn-danger">X</button>
                            </li>
                        </ul>
                    </li>
                </ul>
                <button type="button" class="btn btn-success d-table mx-auto mt-3" id="add-question">Add question</button>
            </form>
        </div>
    </div>
    <?php
    die();
}

add_action('admin_menu', function(){
    /**
     * add main menu
     */
    add_menu_page('Quiz', 'Quiz', 'menage_options', 'rzQuizManager', 'rz_manage_quiz', 'dashicons-welcome-write-blog');

    /**
     * add league submenu
     */
    add_submenu_page('rzQuizManager', 'Add quiz', 'Add quiz', 'manage_options', 'rzAddQuiz', 'rz_add_quiz');

    /**
     * add league submenu
     */
    add_submenu_page('rzQuizManager', 'Manage questions', 'Manage Questions', 'manage_options', 'rzmanageQuestions', 'rz_manage_questions');
});