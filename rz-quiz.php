<?php


/**
 * direct access not allowed
 */
if (!defined('ABSPATH')) {
    die(__('Direct access not allowed.', 'imit-recozilla'));
}


/**
 * quiez shortcode
 */
add_shortcode('rz-quiz', function () {
    global $wpdb;
    ob_start();

    $get_all_quizes = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_quizzes ORDER BY id DESC LIMIT 0, 10", ARRAY_A);
    $get_quiz = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_quizzes", ARRAY_A);

    $user_id = get_current_user_id();
    $is_user_already_a_partner = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_user_programs WHERE user_id = '{$user_id}' AND status = '1' OR status = '0'", ARRAY_A);
?>
    <div class="rz-mid quiz_main_section">
        <div class="row">
            <div class="<?php echo ((count($is_user_already_a_partner) > 0) ? 'col-12' : 'col-md-8') ?>">
                <!-- quiz colum start -->
                <div class="main-column">
                    <div class="quiz_heading pb-0">
                        <h2 class="text-left imit-font fw-500 rz-color fz-24 fw-500 mb-5">Test your Knowledge</h2>
                    </div>

                    <div id="quiz_data">
                        <!-- quiz close box  -->
                        <?php
                        if (count($get_all_quizes) > 0) {
                            $i = 0;
                            foreach ($get_all_quizes as $quiz) {
                                $quiz_id = $quiz['id'];
                        ?>
                                <!-- quize open box -->
                                <div class="p-0 mt-3 single-quiz <?php echo (($i == 0) ? 'bg-white' : ''); ?>">
                                    <div class="test_heading d-flex flex-row justify-content-between align-items-center p-4" data-quiz_id="<?php echo $quiz['id']; ?>" id="show-quiz" style="cursor: pointer;">
                                        <p class="quiz-text-number imit-font fz-20 p-10 fw-500 mb-0 rz-secondary-color">Quiz 1: <?php echo $quiz['quiz_name']; ?></p>
                                        <p class="time_date float-end imit-font fw-500 fz-14 p-10 mb-0 rz-secondary-color">Added on: <?php echo date('F d, Y', strtotime($quiz['created_at'])); ?></p>
                                    </div>


                                    <form id="quiz-submission-form" data-quiz_id="<?php echo $quiz['id']; ?>">
                                        <div style="<?php echo (($i == 0) ? 'display: block;' : 'display: none;'); ?>" id="quiz-start<?php echo $quiz['id']; ?>" class="show-quiz-form p-4 pt-0">
                                            <?php
                                            $get_all_questions = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_quiz_questions WHERE quiz_id = '{$quiz_id}' ORDER BY id DESC", ARRAY_A);
                                            $q = 0;
                                            foreach ($get_all_questions as $question) {
                                            ?>
                                                <div id="question<?php echo $quiz_id . $q; ?>" <?php if ($q != 0) {
                                                                                                    echo 'style="display: none;"';
                                                                                                } ?> class="question">
                                                    <div class="quiz-question p-10 d-flex flex-row justify-content-start align-items-center">
                                                        <span class="question-img"><img src="<?php echo plugins_url('images/Vector.png', __FILE__); ?>" alt=""></span>
                                                        <h2 class="fw-500 text-dark" style="font-size: 32px;"><span class="rz-color me-2">.</span><?php echo $question['question']; ?></h2>
                                                    </div>
                                                    <p class="imit-font fz-14 rz-secondary-color">Find the correct answer.</p>
                                                    <div id="message-error" class="text-danger"></div>
                                                    <?php
                                                    $answers = json_decode($question['answers']);
                                                    $a = 0;
                                                    foreach ($answers as $answer) {
                                                    ?>
                                                        <div class="form-check mt-2">
                                                            <input class="form-check-input" type="radio" name="answer<?php echo $q; ?>" id="asnwer<?php echo $question['id'] . $a; ?>" value="<?php echo $answer; ?>">
                                                            <label class="form-check-label rz-secondary-color" for="asnwer<?php echo $question['id'] . $a; ?>">
                                                                <?php echo $answer; ?>
                                                            </label>
                                                        </div>
                                                    <?php
                                                        $a++;
                                                    }
                                                    ?>
                                                    <div class="row">
                                                        <div class="col-md-4"></div>
                                                        <div class="col-md-4">
                                                            <p class="text-center achived_number rz-s-p mb-0"><span id="counter">1</span> of <?php echo count($get_all_questions); ?> Questions</p>
                                                        </div>
                                                        <div class="col-md-4 float-end">
                                                            <?php if (($q + 1) == count($get_all_questions)) {
                                                            ?>
                                                                <button type="submit" class="text-center next-number rz-s-p mb-0 btn rz-bg-color text-white w-100 border-0">Submit</button>
                                                            <?php
                                                            } else {
                                                            ?>
                                                                <button type="button" class="text-center next-number rz-s-p mb-0 btn rz-bg-color text-white w-100 border-0" data-target="<?php echo $quiz_id . $q + 1; ?>" data-quiz_id="<?php echo $quiz_id; ?>" id="next-question">Next question</button>
                                                            <?php
                                                            } ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php
                                                $q++;
                                            }
                                            ?>
                                        </div>
                                    </form>
                                </div>
                                <!-- quiz opne box end  -->
                        <?php
                                $i++;
                            }
                        }
                        ?>
                    </div>
                    <?php

                    if (count($get_quiz) > 10) {
                    ?>
                        <button type="button" class="btn rz-bg-color text-white imit-font fz-14 mt-3 d-table mx-auto" id="load-more-quiz">
                            <div class="d-flex flex-row justify-content-center align-items-center">
                                <span>Load more...</span>
                                <div class="spinner-grow" role="status" style="display: none;">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </button>
                    <?php
                    }
                    ?>
                </div>
                <!-- quiz colum end -->
            </div>
            <div class="col-md-4">
                <?php echo do_shortcode('[join-partner-program]'); ?>
            </div>
        </div>
    </div>
<?php
    return ob_get_clean();
});

/**
 * add quiz add option
 */
function rz_add_quiz()
{
    global $wpdb;
?>
    <div class="card p-0 w-100">
        <div class="card-header">
            <h2 style="font-size: 20px" class="m-0">Add new quiz</h2>
        </div>
        <div class="card-body">
            <form action="<?php echo admin_url('admin-post.php'); ?>" method="POST">
                <?php wp_nonce_field('imit-add-quiz-nonce', 'nonce'); ?>
                <input type="hidden" name="action" value="rz_add_quiz_action">
                <input name="quiz_name" type="text" class="form-control" placeholder="Please enter quiz name...">
                <?php submit_button('Add quiz'); ?>
            </form>
        </div>
    </div>
<?php
    die();
}

/**
 * add quiz action
 */
add_action('admin_post_rz_add_quiz_action', function () {
    global $wpdb;
    $nonce = $_POST['nonce'];
    if (wp_verify_nonce($nonce, 'imit-add-quiz-nonce')) {
        $rz_quizzes = $wpdb->prefix . 'rz_quizzes';
        $quiz_name = sanitize_text_field($_POST['quiz_name']);

        if (!empty($quiz_name)) {
            $wpdb->insert($rz_quizzes, [
                'quiz_name' => $quiz_name,
                'created_at' => wpDateTime(),
                'updated_at' => wpDateTime()
            ]);
            wp_redirect('admin.php?page=rzAddQuiz');
        }
    }
    die();
});


/**
 * manage questions
 */
function rz_manage_questions()
{
    global $wpdb;
    $get_all_quizes = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_quizzes ORDER BY id DESC", ARRAY_A);

    $rz_quiz_questions = $wpdb->prefix . 'rz_quiz_questions';

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
                    foreach ($get_all_quizes as $quiz) {
                        echo '<option value="' . $quiz['id'] . '">' . $quiz['quiz_name'] . '</option>';
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
                foreach ($get_all_quizes as $quiz) {
                    echo '<option value="' . $quiz['id'] . '">' . $quiz['quiz_name'] . '</option>';
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
                    foreach ($get_all_questions as $question) {
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

add_action('admin_menu', function () {
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
add_action('wp_ajax_rz_submit_user_answer', function () {
    global $wpdb;
    $nonce = $_POST['nonce'];
    if (wp_verify_nonce($nonce, 'rz-admin-submit-answer-nonce')) {
        $quiz_id = sanitize_key($_POST['quiz']);
        $question = sanitize_text_field($_POST['question']);
        $answer = $_POST['answer'];
        $correct_answer_id = sanitize_key($_POST['correct-answer']);
        $rz_quiz_questions = $wpdb->prefix . 'rz_quiz_questions';

        if (empty($quiz_id)) {
            $response['message'] = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                  <strong>Wait!</strong> Please select a quiz.
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
            $response['error'] = true;
        } else if (empty($question)) {
            $response['message'] = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                  <strong>Wait!</strong> Please enter a question.
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
        } else if (count($answer) < 1 || in_array('', $answer)) {
            $response['message'] = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                  <strong>Wait!</strong> Please add an answer.
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
            $response['error'] = true;
        } else if ($correct_answer_id == '') {
            $response['message'] = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                  <strong>Wait!</strong> Please select a correct answer.
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
            $response['error'] = true;
        } else {
            $correct_answer = sanitize_text_field($answer[$correct_answer_id]);

            $wpdb->insert($rz_quiz_questions, [
                'quiz_id' => $quiz_id,
                'question' => $question,
                'answers' => json_encode($answer),
                'correct_answer' => $correct_answer,
                'created_at' => wpDateTime(),
                'updated_at' => wpDateTime()
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
add_action('wp_ajax_rz_get_answer_based_on_quiz', function () {
    global $wpdb;
    $nonce = $_POST['nonce'];
    if (wp_verify_nonce($nonce, 'rz-answer-using-quiz-nonce')) {
        $quiz_id = sanitize_key($_POST['quiz_id']);

        $all_questions = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_quiz_questions WHERE quiz_id = '{$quiz_id}'", ARRAY_A);
        $i = 1;
        foreach ($all_questions as $question) {
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
add_action('wp_ajax_rz_view_questions', function () {
    global $wpdb;
    $nonce = $_POST['nonce'];
    if (wp_verify_nonce($nonce, 'rz-view-question-nonce')) {
        $question_id = sanitize_key($_POST['question_id']);

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
            foreach ($all_answers as $answer) {
            ?>
                <li class="list-group-item d-flex flex-row justify-content-center align-items-center" id="answer-list<?php echo $i; ?>"><input name="answer-edit[]" type="text" class="form-control" value="<?php echo $answer; ?>" placeholder="Enter answers" id="answer-edit<?php echo $i; ?>"> <input type="radio" name="correct-answer" value="<?php echo $answer; ?>" <?php if ($get_question->correct_answer == $answer) {
                                                                                                                                                                                                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                                                                                                                                                                                                            } ?>></li>
            <?php
                $i++;
            }
            ?>
        </ul>

        <label for="status" class="mt-3 mb-1">Question status</label>
        <select name="status" id="status" class="form-control">
            <option value="1" <?php if ($get_question->status == '1') {
                                    echo 'selected';
                                } ?>>Published</option>
            <option value="0" <?php if ($get_question->status == '0') {
                                    echo 'selected';
                                } ?>>Denied</option>
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
add_action('wp_ajax_rz_edit_questions_and_answers', function () {
    global $wpdb;
    $nonce = $_POST['nonce'];
    if (wp_verify_nonce($nonce, 'rz-edit-question-nonce')) {
        $id = sanitize_key($_POST['id']);
        $question = sanitize_text_field($_POST['question-edit']);
        $answers = $_POST['answer-edit'];
        $correct_answer = sanitize_text_field($_POST['correct-answer']);
        $status = sanitize_text_field($_POST['status']);

        if (!empty($id) && !empty($question) && !empty($answers) && !empty($correct_answer)) {
            $wpdb->update($wpdb->prefix . 'rz_quiz_questions', [
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
add_action('wp_ajax_rz_submit_quiz_for_result', function () {
    global $wpdb;
    $nonce = $_POST['nonce'];
    if (wp_verify_nonce($nonce, 'rz-get-quiz-result-nonce')) {
        $quiz_id = sanitize_key($_POST['quiz_id']);
        $timeSepnt = sanitize_key($_POST['timeSpent']);
        $rz_quiz_result = $wpdb->prefix . 'rz_quiz_result';
        $get_all_answers = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_quiz_questions WHERE quiz_id = '{$quiz_id}' ORDER BY id DESC", ARRAY_A);

        $questions_count = count($get_all_answers);
        $correct_answer = 0;
        $i = 0;
        foreach ($get_all_answers as $answer) {
            if ($answer['correct_answer'] == $_POST['answer' . $i]) {
                $correct_answer++;
            }
            $i++;
        }


        $result = ($correct_answer / $questions_count) * 10;

        $score = number_format($result, 2, '.', '');
        $user_id = get_current_user_id();

        if (!empty($quiz_id) && !empty($score) && !empty($user_id) && !empty($timeSepnt)) {
            $wpdb->insert($rz_quiz_result, [
                'quiz_id' => $quiz_id,
                'user_id' => $user_id,
                'score' => $score,
                'timeSpent' => $timeSepnt,
                'created_at' => wpDateTime(),
                'updated_at' => wpDateTime()
            ]);

            $quiz_id = $wpdb->insert_id;

            $all_result = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_quiz_result", ARRAY_A);

            $top_10_limit = round((count($all_result) * 10) / 100);
            $top_20_limit = round((count($all_result) * 20) / 100);
            $top_30_limit = round((count($all_result) * 30) / 100);
            $top_10 = $wpdb->get_results("SELECT id FROM {$wpdb->prefix}rz_quiz_result ORDER BY score DESC LIMIT 0, {$top_10_limit}", ARRAY_A);
            $top_20 = $wpdb->get_results("SELECT id FROM {$wpdb->prefix}rz_quiz_result ORDER BY score DESC LIMIT 0, {$top_20_limit}", ARRAY_A);
            $top_30 = $wpdb->get_results("SELECT id FROM {$wpdb->prefix}rz_quiz_result ORDER BY score DESC LIMIT 0, {$top_30_limit}", ARRAY_A);


            $top_10_ids = [];
            foreach ($top_10 as $quiz) {
                array_push($top_10_ids, $quiz['id']);
            }

            $top_20_ids = [];
            foreach ($top_20 as $quiz) {
                array_push($top_20_ids, $quiz['id']);
            }

            $top_30_ids = [];
            foreach ($top_30 as $quiz) {
                array_push($top_30_ids, $quiz['id']);
            }

            $quiz_status = '';

            if (in_array($quiz_id, $top_10_ids)) {
                $quiz_status = 'Top 10%';
            } else if (in_array($quiz_id, $top_20_ids)) {
                $quiz_status = 'Top 20%';
            } else if (in_array($quiz_id, $top_30_ids)) {
                $quiz_status = 'Top 30%';
            } else {
                $quiz_status = 'You are failed';
            }

            $wpdb->update($rz_quiz_result, [
                'quiz_status' => $quiz_status
            ], ['id' => $quiz_id]);
        }
    }
    die();
});


/**
 * get more quiz
 */
add_action('wp_ajax_nopriv_rz_get_more_quiz', 'imit_rz_get_quiz');
add_action('wp_ajax_rz_get_more_quiz', 'imit_rz_get_quiz');

function imit_rz_get_quiz()
{
    global $wpdb;
    $nonce = $_POST['nonce'];
    if (wp_verify_nonce($nonce, 'rz-get-more-quiz-nonce')) {
        $start = sanitize_key($_POST['start']);
        $get_all_quizes = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_quizzes ORDER BY id DESC LIMIT {$start}, 10", ARRAY_A);
        $get_quiz = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_quizzes", ARRAY_A);

        $response['html'] = '';
        if (count($get_all_quizes) > 0) {

            if (count($get_all_quizes) < 10 || ($start + 10) >= count($get_quiz)) {
                $response['quizReachMax'] = true;


                foreach ($get_all_quizes as $quiz) {

                    $quiz_id = $quiz['id'];

                    $get_all_questions = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_quiz_questions WHERE quiz_id = '{$quiz_id}' ORDER BY id DESC", ARRAY_A);

                    $q = 0;
                    foreach ($get_all_questions as $question) {
                        $answers = json_decode($question['answers']);

                        $answer_html = '';
                        $a = 0;
                        foreach ($answers as $answer) {
                            $answer_html .= '
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="radio" name="answer' . $q . '" id="asnwer' . $question['id'] . $a . '" value="' . $answer . '">
                                <label class="form-check-label rz-secondary-color" for="asnwer' . $question['id'] . $a . '">
                                    ' . $answer . '
                                </label>
                            </div>';
                            $a++;
                        }

                        $question_html .= '
                        <div id="question' . $quiz_id . $q . '" ' . (($q != 0) ? 'style="display: none;"' : '') . ' class="question">
                            <div class="quiz-question p-10 d-flex flex-row justify-content-start align-items-center">
                                <span class="question-img"><img src="' . plugins_url('images/Vector.png', __FILE__) . '" alt=""></span>
                                <h2 class="fw-500 text-dark" style="font-size: 32px;"><span class="rz-color me-2">.</span>' . $question['question'] . '</h2>
                            </div>
                            <p class="imit-font fz-14 rz-secondary-color">Find the correct answer.</p>
                            <div id="message-error" class="text-danger"></div>
                            ' . $answer_html . '
                            <div class="row">
                                <div class="col-md-4"></div>
                                <div class="col-md-4"><p class="text-center achived_number rz-s-p mb-0"><span id="counter">1</span> of ' . count($get_all_questions) . ' Questions</p></div>
                                <div class="col-md-4 float-end">
                                    ' . ((($q + 1) == count($get_all_questions)) ? '<button type="submit" class="text-center next-number rz-s-p mb-0 btn rz-bg-color text-white w-100 border-0">Submit</button>' : '<button type="button" class="text-center next-number rz-s-p mb-0 btn rz-bg-color text-white w-100 border-0" data-target="' . $quiz_id . ($q + 1) . '" data-quiz_id="' . $quiz_id . '" id="next-question">Next question</button>') . '
                                </div>
                            </div>
                        </div>
                        ';
                        $q++;
                    }

                    $response['html'] .= '<div class="p-0 mt-3 single-quiz">
                    <div class="test_heading d-flex flex-row justify-content-between align-items-center p-4" data-quiz_id="' . $quiz['id'] . '" id="show-quiz" style="cursor: pointer;">
                        <p class="quiz-text-number imit-font fz-20 p-10 fw-500 mb-0 rz-secondary-color">Quiz 1: ' . $quiz['quiz_name'] . '</p>
                        <p class="time_date float-end imit-font fw-500 fz-14 p-10 mb-0 rz-secondary-color">Added on: ' . date('F d, Y', strtotime($quiz['created_at'])) . '</p>
                    </div>
    
    
                    <form id="quiz-submission-form" data-quiz_id="' . $quiz['id'] . '">
                        <div style="display: none;" id="quiz-start' . $quiz['id'] . '" class="show-quiz-form p-4 pt-0">
                            ' . $question_html . '
                        </div>
                    </form>
                </div>';
                }
            } else {
                $response['quizReachMax'] = false;


                foreach ($get_all_quizes as $quiz) {

                    $get_all_questions = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rz_quiz_questions WHERE quiz_id = '{$quiz_id}' ORDER BY id DESC", ARRAY_A);

                    $q = 0;
                    foreach ($get_all_questions as $question) {
                        $answers = json_decode($question['answers']);

                        $a = 0;
                        foreach ($answers as $answer) {
                            $answer_html .= '
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="radio" name="answer' . $q . '" id="asnwer' . $question['id'] . $a . '" value="' . $answer . '">
                                <label class="form-check-label rz-secondary-color" for="asnwer' . $question['id'] . $a . '">
                                    ' . $answer . '
                                </label>
                            </div>';
                            $a++;
                        }

                        $question_html .= '
                        <div id="question' . $quiz_id . $q . '" ' . (($q != 0) ? 'style="display: none;"' : '') . ' class="question">
                            <div class="quiz-question p-10 d-flex flex-row justify-content-start align-items-center">
                                <span class="question-img"><img src="' . plugins_url('images/Vector.png', __FILE__) . '" alt=""></span>
                                <h2 class="fw-500 text-dark" style="font-size: 32px;"><span class="rz-color me-2">.</span>' . $question['question'] . '</h2>
                            </div>
                            <p class="imit-font fz-14 rz-secondary-color">Find the correct answer.</p>
                            <div id="message-error" class="text-danger"></div>
                            ' . $answer_html . '
                            <div class="row">
                                <div class="col-md-4"></div>
                                <div class="col-md-4"><p class="text-center achived_number rz-s-p mb-0"><span id="counter">1</span> of ' . count($get_all_questions) . ' Questions</p></div>
                                <div class="col-md-4 float-end">
                                    ' . ((($q + 1) == count($get_all_questions)) ? '<button type="submit" class="text-center next-number rz-s-p mb-0 btn rz-bg-color text-white w-100 border-0">Submit</button>' : '<button type="button" class="text-center next-number rz-s-p mb-0 btn rz-bg-color text-white w-100 border-0" data-target="' . $quiz_id . ($q + 1) . '" data-quiz_id="' . $quiz_id . '" id="next-question">Next question</button>') . '
                                </div>
                            </div>
                        </div>
                        ';
                        $q++;
                    }

                    $response['html'] .= '<div class="p-0 mt-3 single-quiz">
                    <div class="test_heading d-flex flex-row justify-content-between align-items-center p-4" data-quiz_id="' . $quiz['id'] . '" id="show-quiz" style="cursor: pointer;">
                        <p class="quiz-text-number imit-font fz-20 p-10 fw-500 mb-0 rz-secondary-color">Quiz 1: ' . $quiz['quiz_name'] . '</p>
                        <p class="time_date float-end imit-font fw-500 fz-14 p-10 mb-0 rz-secondary-color">Added on: ' . date('F d, Y', strtotime($quiz['created_at'])) . '</p>
                    </div>
    
    
                    <form id="quiz-submission-form" data-quiz_id="' . $quiz['id'] . '">
                        <div style="display: none;" id="quiz-start' . $quiz['id'] . '" class="show-quiz-form p-4 pt-0">
                            ' . $question_html . '
                        </div>
                    </form>
                </div>';
                }
            }
        } else {
            $response['quizReachMax'] = true;
        }

        echo json_encode($response);
    }
    die();
}
