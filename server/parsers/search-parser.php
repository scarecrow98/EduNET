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










    
    if( !empty($_POST['teacher-name']) ){
        $db = newDatabaseConnection();
        $teacher_name = $_POST['teacher-name'];

        $stmt = $db->prepare(
            "SELECT id, name FROM users".
            " WHERE users.name LIKE :teachername".
            " AND users.type = :usertype".
            " AND users.id != :uid"
        );
        $stmt->execute(array( ':teachername' => '%'.$teacher_name.'%', ':usertype' => 1, ':uid' => $_SESSION['user-id'] ));
        $db = null;

        $teachers = $stmt->fetchAll();
        echo json_encode($teachers);
    }

?>