<?php

    require_once('config.php');
    Session::start();
    

    //helyes URL paraméterek ellenőrzése
    if( empty($_GET['test_instance']) || empty($_GET['user'])  ){
        errorRedirect('Helytelen feladatlap azonosító!');
        exit();
    }

    $user_id = $_GET['user'];
    $test_instance_id = $_GET['test_instance'];

    $test_instance = TestInstance::get($test_instance_id);

    $test = Test::get($test_instance->test_id);
    $answers = Answer::getTextAnswers($user_id, $test_instance_id);
    $students = $test_instance->getStudents();

    //létező eredmények ellenőrzése
    if( empty($answers) ){
        errorRedirect('Helytelen feladatlap azonosító!');
        exit();
    }

    //tanár ellenőrzése, hogy valóban az ó feladatlapja-e
    if( $test_instance->current_author_id != Session::get('user-id') ){
        errorRedirect('Nincs jogosultságod az oldal megtekintéséhez!');
        exit();
    }
?>
<html>
    <head>
        <title>Feladatlap javítása</title>
        <meta charset="utf-8">
        <link rel="icon" href="<?php echo PUBLIC_ROOT; ?>resources/images/favicon.ico">
        <link rel="stylesheet" href="http://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
        <link rel="stylesheet" href="<?php echo PUBLIC_ROOT; ?>css/main.css">
        <link rel="stylesheet" href="<?php echo PUBLIC_ROOT; ?>css/components.css">
        <style>
            body{ height: 100vh; width: auto; margin: 10px; overflow-y: scroll; }
        </style>
    </head>
    <body class="test-body">

        <div style="margin: 30px 0px; width: 100%; overflow-x:" class="clear">
            <?php foreach( $students as $student ): ?>
            <li style="float: left; padding: 10px; list-style: none;">
                <a href="evaluate.php?test_instance=<?php echo $test_instance_id ?>&user=<?php echo $student->id ?>">
                    <img src="<?php echo SERVER_ROOT ?>uploads/avatars/<?php echo $student->avatar ?>" style="width: 50px; display: block;">
                    <h4 for=""><?php echo $student->name ?></h4>
                </a>
            </li>
            <?php endforeach; ?>
        </div>

        <div class="test-container">
        <form method="POST" action="<?php echo SERVER_ROOT; ?>parsers/manual-evaluator.php">
            <input type="hidden" name="user-id" value="<?php echo $user_id ?>">
            <input type="hidden" name="test-instance-id" value="<?php echo $test_instance_id; ?>">
            <input type="hidden" name="task-count" value="<?php echo count($answers); ?>">
    <?php
        $task_count = 0;
        foreach($answers as $answer):
        $task_count++;
        $task = Task::get($answer->task_id);
        $result = Task::getResult($task->id, $test_instance->id, $user_id);
        $HAS_RESULT = empty($result['result'])?false:true;
    ?>
            <input type="hidden" name="task-id-<?php echo $task_count; ?>" value="<?php echo $task->id; ?>">

        <div class="test-sheet panel">   
            <header class="bg-1">
                <h3 class="ion-compose"><?php echo $task->task_number;/* feladat száma */ ?>. feladat</h3>
            </header>
            <section>
                <label for="" style="width: auto;"><?php echo $task->question; ?></label>
                <small>( <?php echo $task->max_points; /* feladat pontszáma */?> pont )</small>

                <pre style="white-space: pre-wrap; color: #b2b2b2; font-style: italic; padding: 15px;"><?php if( !empty($task->text) ){ echo $task->text;  } /* feladat szövege (ha létezik) */ ?></pre>


                <?php if( !empty($task->image) ): /* feladat képe (ha létezik) */ ?>
                    <a href="<?php echo SERVER_ROOT; ?>uploads/images/<?php echo $task->image; ?>" target="_blank">
                        <img src="<?php echo SERVER_ROOT; ?>uploads/images/<?php echo $task->image; ?>" alt="" style="width: 300px; display: block; margin-bottom: 20px;">
                    </a>
                <?php endif; ?>

                <h4>A felhasználó válasza:</h4>
                <i>
                <?php
                    if( empty($answer->answer) ){
                        echo '<span style="color: red">A felhasználó nem válaszolt erre a kérdésre!</span>';
                    }
                    else{
                        echo '<pre style="white-space: pre-wrap;">'.$answer->answer.'</pre>';
                    }
                ?>
                </i>
                <?php if( !$HAS_RESULT ): ?>
                <div>
                    <input type="number" min="0" max="<?php echo $task->max_points ?>" value="0" name="user-points-<?php echo $task_count; ?>">
                    <textarea placeholder="Feladathoz kapcsolódó, diáknak szánt megjegyzés..." maxlength="10" style="width: 100%;" name="teacher-comment-<?php echo $task_count; ?>"></textarea>
                </div>
                <?php else: ?>
                    <label for="">Eredmény: <?php echo $result['result'] ?></label>
                <?php endif; ?>
            </section>
        </div>
    <?php endforeach; ?>
        <?php if( !$HAS_RESULT ): ?>
        <input type="submit" value="Értékelés" class="cta-button color-2">
        <?php endif; ?>
        </form>
        </div>
    </body>
</html>