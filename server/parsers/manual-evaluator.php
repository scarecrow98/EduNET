<?php

    if( isset($_POST['test-instance-id']) ){
        require_once('../../config.php');

        $test_instance_id = $_POST['test-instance-id'];
        $task_count = $_POST['task-count'];
        $user_id = $_POST['user-id'];

        $test_instance = TestInstance::get($test_instance_id);

        for( $i = 1; $i <= $task_count; $i++ ){
            $task_id = $_POST['task-'.$i.'-id'];			
            $points = $_POST['points-'.$i];
            $comment = empty($_POST['comment-'.$i]) ? null : $_POST['comment-'.$i];

            $task = Task::get($task_id);

            $result = $task->max_points.'/'.$points;

            $data = array(
                'user_id'           => $user_id,
                'test_instance_id'  => $test_instance->id,
                'result'            => $result,
                'comment'           => $comment
            );
            $task->storeResult($data);
        }

        $test_instance->storeEvaluation($user_id, date('Y-m-d H:i:s'));

        header('Location:'.$_SERVER['HTTP_REFERER']);

    }

?>