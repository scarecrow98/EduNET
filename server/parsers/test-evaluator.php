<?php


    // if( !empty($_POST['test-submission']) ){
    //     $answers = json_decode($_POST['answers'], true);
    //     print_r($answers[0]);
    //     print_r($_FILES);

    //     exit();
    // }

    require_once '../../config.php';
    Session::start();

    if( empty( $_POST['test-submission'] ) ){
        exit();
    }
    
    $user_id = Session::get('user-id');
    $test_instance_id = Session::get('test-instance-id');
    $test_instance = TestInstance::get($test_instance_id);
    $test = Test::get($test_instance->test_id);

    
    //SESSIONBŐL átvesszük a feladatokat és adataikat tároló tömböt
    $answers = json_decode($_POST['answers'], true);

    foreach( $answers as $answer ){
        $task= Task::get($answer['task-id']);
        $user_task_points = 0; /* ebben lesz tárolva, hogy a diák hány pontot ért el egy feladatban */

        //ha a feladat szöveges válasz típusú
        if( $task->type == 2 ){
            $user_answer = empty($answer['text-answer']) ? null : $answer['text-answer'];

            $data = array(
                'user_id'           => Session::get('user-id'),
                'task_id'           => $task->id,
                'test_instance_id'  => $test_instance_id,
                'answer'            => $user_answer
            );
            Answer::storeText($data); //szöveges válasz eltárolása
        }
        //ha a feladat fájl típusú
        elseif( $task->type == 5 ){
            $file_index = $answer['file-name'];
            $file = $_FILES[$file_index];
            $file_name = null;

            if( !empty($file) ){
                $fu = new FileUploader($file, 'file', 'solution_file');
                $file_name = $fu->checkFile();
            }

            $data = array(
                'user_id'           => Session::get('user-id'),
                'task_id'           => $task->id,
                'test_instance_id'  => $test_instance_id,
                'file_name'         => $file_name
            );
            Answer::storeFile($data); //fájl válasz eltárolása
        }
        //ha opciós típusú a feladat
        else{

            $total_correct_answers = 0; /* ebben tároljuk majd, hogy egy feladathoz hány helyes válasz lehetséges maximum  */
            $quiz_correct_user_answers = 0; /* ebbe tároljuk, hogy a kvízfeladatban mennyit talált el a diák */
            $correct_user_answers = 0; /* ebbe tároljuk, hogy igaz/hamis vagy párosítás feladatokban mennyit talált el a diák */

            for( $i = 0; $i < count($answer['task-options']); $i++ ){
                $option = TaskOption::get($answer['task-options'][$i]['option-id']); /* az opció helyes válaszának lekérése */

                //feladattíustól függően diák válaszainak kialakítása
                //pl.: a nem bepipált checkboxoknak 0 lesz az értéke, a bebipáltnak 1
                switch( $task->type ){
                    case 1:
                        $user_answer = $answer['task-options'][$i]['value'];
                        if( $option->correct_ans == 1 ){ $total_correct_answers++; }
                    break;
                    case 3:
                        $user_answer = !empty($answer['task-options'][$i]['option-id']) ? $answer['task-options'][$i]['value'] : null;
                        $total_correct_answers++;
                    break;
                    case 4:
                        $user_answer = isset($answer['task-options'][$i]['option-id']) ? $answer['task-options'][$i]['value'] : null;
                        $total_correct_answers++;
                    break;
                }

                //kvíz feladatok értkélelése eltér az igaz/hamis és a párosítós feladatoktól
                //kvíznél változó lehet a maximális helyes megoldások száma, míg a többinél akár az összes opció lehet helyes
                
                $is_correct = 0;/* ebben tároljuk, hogy a diák jól válaszolt-e az opcióra */
                if( $option->correct_ans == $user_answer ){
                    $is_correct = 1;

                    if( $option->correct_ans == 1 && $task->type == 1 ){
                        $quiz_correct_user_answers++;
                    }
                    else{
                        $correct_user_answers++;
                    }
                }

                $is_correct = $option->correct_ans == $user_answer ? 1 : 0; /* ellenőrízzük, hogy jó-e a diák válasza */

                $data = array(
                    'user_id'           => Session::get('user-id'),
                    'task_option_id'    => $option->id,
                    'test_instance_id'  => $test_instance_id,
                    'answer'            => $user_answer,
                    'is_correct'        => $is_correct
                );
                Answer::store($data); /* a diák válaszának eltárolása */
            } //options for vége

            $points_per_options = $task->max_points / $total_correct_answers; /* egy opcióra járó pont --> feladatra járó max pont / összes lehetséges jó válasz */
            
            /* felhasználó által elért pontok kiszámítása --> egy opcióra adható pont * ahány opciót eltalált */
            if( $task->type == 1 ){
                $user_task_points = $points_per_options * $quiz_correct_user_answers; 
            }
            else{
                $user_task_points = $points_per_options * $correct_user_answers;
            }

            $data = array(
                'user_id'           => Session::get('user-id'),
                'test_instance_id'  => $test_instance_id,
                'result'            => $task->max_points.'/'.$user_task_points,
                'comment'           => null
            );
            $task->storeResult($data); /* egy feladatra vonatkozó eredmény rögzítése */

        }
    }

    if( !$test->hasFileOrTextTypeTask() ){
        $test_instance->storeEvaluation($user_id, date('Y-m-d H:i:s'));
    }

    Session::unset('task-data');
    Session::unset('test-instance-id');

    echo 'success';
?>