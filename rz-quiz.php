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

    $rz_quiz_questions = $wpdb->prefix.'rz_quiz_questions';

    $get_all_questions = $wpdb->get_results("SELECT * FROM {$rz_quiz_questions} WHERE quiz_id = (SELECT id FROM {$wpdb->prefix}rz_quizzes ORDER BY id DESC LIMIT 1 ) ", ARRAY_A);

    ?>
    <div class="card p-0 mw-100 me-3">
        <div class="card-header">
            <h2 style="font-size: 20px" class="m-0">Add answer</h2>
            <?php echo $get_all_quizes[0][0]; ?>
        </div>
        <div class="card-body">
            <form id="answer-form">
                <div id="answer-form-error"></div>
                <label for="quiz" class="form-label">Select quiz</label>
                <select name="quiz" id="quiz" class="form-control">
                    <?php
                    foreach($get_all_quizes as $quiz){
                        echo '<option value="'.$quiz['id'].'">'.$quiz['quiz_name'].'</option>';
                    }
                    ?>
                </select>
                <div class="invalid-feedback" id="quiz"></div>
                <ul class="questions ps-0 mb-0 list-group" id="add-question-wrapper">
                    <li class="question-list list-group-item mt-3">
                        <div class="d-flex flex-row justify-content-between align-items-center">
                            <input type="text" class="form-control" name="question" placeholder="Add question...">
                            <div class="btn-group ms-2">
                                <button type="button" class="btn btn-info d-flex flex-row justify-content-start align-items-center" id="add-answer"><span class="me-1">Add</span> <span>answer</span></button>
                            </div>
                        </div>
                        <ul class="list-group my-2" id="answer-wrapper">

                        </ul>
                    </li>
                </ul>

                <button type="submit" class="btn btn-success mt-3">Add answer</button>
            </form>
        </div>
    </div>


    <div class="card p-0 mw-100 me-3">
        <div class="card-header">
            <select name="quiz_name" class="form-control mt-3">
                <?php
                foreach($get_all_quizes as $quiz){
                    echo '<option value="'.$quiz['id'].'">'.$quiz['quiz_name'].'</option>';
                }
                ?>
            </select>
        </div>
        <div class="card-body">
            <table class="table table-responsive table-striped">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Quiz name</th>
                    <th scope="col">Question</th>
                    <th scope="col">Answers</th>
                    <th scope="col">Correct Answer</th>
                    <th scope="col">Action</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $i = 1;
                foreach ($get_all_questions as $question){
                    $quiz_id = $question['quiz_id'];
                    $get_quiz_info = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}rz_quizzes WHERE id = '{$quiz_id}'");
                    ?>
                    <tr>
                        <th scope="row"><?php echo $i; ?></th>
                        <td><?php echo $get_quiz_info->quiz_name; ?></td>
                        <td><?php echo $question['question']; ?></td>
                        <td>
                            <?php
                            $all_answers = json_decode($question['answers']);
                            echo implode(' | ', $all_answers);
                            ?>
                        </td>
                        <td><?php echo $question['correct_answer']; ?></td>
                        <td>
                            <div class="btn-group">
                                <button type="button" class="btn btn-info">View</button>
                                <button type="button" class="btn btn-danger">Delete</button>
                            </div>
                        </td>
                    </tr>
                        <?php
                    $i++;
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php
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

/**
 * submit admin answers
 */
add_action('wp_ajax_rz_submit_user_answer', function(){
    global $wpdb;
    $nonce = $_POST['nonce'];
    if(wp_verify_nonce($nonce, 'rz-admin-submit-answer-nonce')){
        $quiz_id = sanitize_key($_POST['quiz']);
        $question = sanitize_text_field($_POST['question']);
        $answer = $_POST['answer'];
        $correct_answer_id = sanitize_key($_POST['correct-answer']);
        $rz_quiz_questions = $wpdb->prefix.'rz_quiz_questions';

        if(empty($quiz_id)){
            $response['message'] = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                  <strong>Wait!</strong> Please select a quiz.
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
            $response['error'] = true;
        }else if(empty($question)){
            $response['message'] = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                  <strong>Wait!</strong> Please enter a question.
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
        }else if(count($answer) < 1 || in_array('', $answer)){
            $response['message'] = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                  <strong>Wait!</strong> Please add an answer.
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
            $response['error'] = true;
        }else if($correct_answer_id == ''){
            $response['message'] = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                  <strong>Wait!</strong> Please select a correct answer.
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
            $response['error'] = true;
        }else{
            $correct_answer = sanitize_text_field($answer[$correct_answer_id]);

            $wpdb->insert($rz_quiz_questions, [
                    'quiz_id' => $quiz_id,
                'question' => $question,
                'answers' => json_encode($answer),
                'correct_answer' => $correct_answer
            ]);

            $response['message'] = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                  <strong>Success!</strong> Question added successful.
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';

            $response['error'] = false;
        }

        echo json_encode($response);
    }
    die();
});