<?php
    require_once('../../config.php');
    Session::start();

    //csoporttagok felvételekor diákok listázása
    if( !empty($_POST['student-name']) ){
        $student_name = $_POST['student-name'];
        $group_id = $_POST['group-id'];

        $results = Group::searchUsers($student_name, $group_id);
        echo json_encode($results);
    }

?>