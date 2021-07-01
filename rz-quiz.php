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
            <select name="quiz_name" class="form-control mt-3" id="get_quiz_name">
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
                <tbody id="fetch-quiz-questions">
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
                                <button type="button" class="btn btn-info" data-question_id="<?php echo $question['id'] ?>" id="view-question">View</button>
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

    <div class="modal fade" id="question-view-modal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 style="font-size: 20px;">Quiz name "PHP"</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="edit-answer-form">
                        
                    </form>
                </div>
            </div>
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


/**
 * get answer based on quiz
 */
add_action('wp_ajax_rz_get_answer_based_on_quiz', function(){
    global $wpdb;
    $nonce = $_POST['nonce'];
    if(wp_verify_nonce( $nonce, 'rz-answer-using-quiz-nonce' )){
        $quiz_id = sanitize_key($_POST['quiz_id']);

        $all_questions = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_quiz_questions WHERE quiz_id = '{$quiz_id}'", ARRAY_A);
        $i = 1;
        foreach($all_questions as $question){
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
                        <button type="button" class="btn btn-info" data-question_id="<?php echo $question['id']; ?>" id="view-question">View</button>
                        <button type="button" class="btn btn-danger">Delete</button>
                    </div>
                </td>
            </tr>
                <?php
            $i++;
        }
    }
    die();
});

/**
 * get question by id
 */
add_action('wp_ajax_rz_view_questions', function(){
    global $wpdb;
    $nonce = $_POST['nonce'];
    if(wp_verify_nonce($nonce, 'rz-view-question-nonce')){
        $question_id = sanitize_key( $_POST['question_id'] );

        $get_question = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}rz_quiz_questions WHERE id = '{$question_id}'");
        $all_answers = json_decode($get_question->answers);
        ?>
        <div id="edit-question-message"></div>
        <label for="question-edit" class="mb-1">Question</label>
        <input type="text" name="question-edit" id="question-edit" class="form-control" value="<?php echo $get_question->question; ?>" placeholder="Enter question...">

        <label for="" class="mt-3 mb-1">All answers</label>
        <ul class="list-group" id="answer-list">
            <?php 
            $i = 0;
            foreach($all_answers as $answer){
                ?>
                <li class="list-group-item d-flex flex-row justify-content-center align-items-center" id="answer-list<?php echo $i; ?>"><input name="answer-edit[]" type="text" class="form-control" value="<?php echo $answer; ?>" placeholder="Enter answers" id="answer-edit<?php echo $i; ?>"> <input type="radio" name="correct-answer" value="<?php echo $answer; ?>" <?php if($get_question->correct_answer == $answer){echo 'checked';} ?>></li>
                <?php
                $i++;
            }
            ?>
        </ul>

        <label for="status" class="mt-3 mb-1">Question status</label>
        <select name="status" id="status" class="form-control">
            <option value="1" <?php if($get_question->status == '1'){echo 'selected';} ?>>Published</option>
            <option value="0" <?php if($get_question->status == '0'){echo 'selected';} ?>>Denied</option>
        </select>

        <input type="hidden" name="id" value="<?php echo $question_id; ?>">

        <button type="submit" class="btn btn-success mt-3">Save</button>
        <?php
    }
    die();
});


/**
 * edit qeustions 
 */
add_action('wp_ajax_rz_edit_questions_and_answers', function(){
    global $wpdb;
    $nonce = $_POST['nonce'];
    if(wp_verify_nonce( $nonce, 'rz-edit-question-nonce' )){
        $id = sanitize_key( $_POST['id'] );
        $question = sanitize_text_field( $_POST['question-edit'] );
        $answers = $_POST['answer-edit'];
        $correct_answer = sanitize_text_field( $_POST['correct-answer'] );
        $status = sanitize_text_field( $_POST['status'] );

        if(!empty($id) && !empty($question) && !empty($answers) && !empty($correct_answer)){
            $wpdb->update($wpdb->prefix.'rz_quiz_questions', [
                'question' => $question,
                'answers' => json_encode($answers),
                'correct_answer' => $correct_answer,
                'status' => $status
            ], ['id' => $id]);

            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Done!</strong> Data updated.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
        }
    }
    die();
});

/**
 * get quiz result
 */
add_action('wp_ajax_rz_submit_quiz_for_result', function(){
    global $wpdb;
    $nonce = $_POST['nonce'];
    if(wp_verify_nonce($nonce, 'rz-get-quiz-result-nonce')){
        $quiz_id = sanitize_key($_POST['quiz_id']);
        $get_all_answers = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_quiz_questions WHERE quiz_id = '{$quiz_id}'", ARRAY_A);

        $questions_count = count($get_all_answers);
        $correct_answer = 0;
        $i = 0;
        foreach($get_all_answers as $answer){
            if($answer['correct_answer'] == $_POST['answer'.$i]){
                $correct_answer = $correct_answer+1;
            }
            $i++;
        }

        $result = ($correct_answer / $questions_count) * 10;
    }
    die();
});