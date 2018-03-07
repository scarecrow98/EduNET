<?php

    require_once('config.php');
    Session::start();
    
    if( Security::checkAccessToken() === false ){
        header('Location: logout');
        exit();
    }

    Security::setAccessToken();


    //helyes URL paraméterek ellenőrzése
    if( empty($_GET['test_instance']) || empty($_GET['user'])  ){
        errorRedirect('Helytelen feladatlap azonosító!');
        exit();
    }

    $user_id = $_GET['user'];
    $test_instance_id = $_GET['test_instance'];

    $test_instance = TestInstance::get($test_instance_id);

    $test = Test::get($test_instance->test_id);
    $answers = Answer::getFileAndTextAnswers($user_id, $test_instance_id);
    $students = $test_instance->getStudents();

    //print_r($file_answers);

    //létező eredmények ellenőrzése
    // if( empty($answers) && empty($file_answers) ){
    //     errorRedirect('Helytelen feladatlap azonosító!');
    //     exit();
    // }

    //tanár ellenőrzése, hogy valóban az ó feladatlapja-e
    if( $test_instance->current_author_id != Session::get('user-id') ){
        errorRedirect('Nincs jogosultságod az oldal megtekintéséhez!');
        exit();
    }

    $HAS_RESULT = null;
?>
<html>
    <head>
        <title>Feladatlap javítása</title>
        <meta charset="utf-8">
        <link rel="icon" href="<?= PUBLIC_ROOT; ?>resources/images/favicon.ico">
        <link rel="stylesheet" href="http://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
        <link rel="stylesheet" href="<?= PUBLIC_ROOT; ?>css/main.css">
        <link rel="stylesheet" href="<?= PUBLIC_ROOT; ?>css/components.css">
        <style>
            body{ height: 100vh; width: auto; margin: 10px; overflow-y: scroll; }
        </style>
    </head>
    <body class="test-body">

        <!-- diák választó -->
        <div style="margin: 30px 0px; width: 100%; overflow-x:" class="clear">
            <?php foreach( $students as $student ): ?>
            <li style="float: left; padding: 10px; list-style: none;">
                <a href="evaluate.php?test_instance=<?= $test_instance_id ?>&user=<?= $student->id ?>">
                    <img src="<?= SERVER_ROOT ?>uploads/avatars/<?= $student->avatar ?>" style="width: 50px; display: block;">
                    <h4 for=""><?= $student->name ?></h4>
                </a>
            </li>
            <?php endforeach; ?>
        </div>

        <pre><?= print_r($answers) ?></pre>

        <div class="test-container">
        <form method="POST" action="<?= SERVER_ROOT; ?>parsers/manual-evaluator.php">
            <input type="hidden" name="user-id" value="<?= $user_id ?>">
            <input type="hidden" name="test-instance-id" value="<?= $test_instance_id; ?>">
        <?php
            $task_count = 0;
            foreach($answers as $answer):
            $task_count++;
            $task = Task::get($answer->task_id);

            $task_data = array(
                'task-id'   => $task->id,
                'task-type' => $task->type,
            );
        ?>
        <input type="hidden" name="task-<?= $task_count ?>-data" value='<?= json_encode($task_data)?>'>
        <div class="test-sheet panel">   
            <header class="bg-1">
                <h3 class="ion-compose"><?= $task->task_number;/* feladat száma */ ?>. feladat</h3>
            </header>
            <section>
                <label for="" style="width: auto;"><?= $task->question; ?></label>
                <small>( <?= $task->max_points; /* feladat pontszáma */?> pont )</small>

                <pre style="white-space: pre-wrap; color: #b2b2b2; font-style: italic; padding: 15px;"><?php if( !empty($task->text) ){ echo $task->text;  } /* feladat szövege (ha létezik) */ ?></pre>


                <?php if( !empty($task->image) ): /* feladat képe (ha létezik) */ ?>
                    <a href="<?= SERVER_ROOT; ?>uploads/images/<?= $task->image; ?>" target="_blank">
                        <img src="<?= SERVER_ROOT; ?>uploads/images/<?= $task->image; ?>" alt="" style="width: 300px; display: block; margin-bottom: 20px;">
                    </a>
                <?php endif; ?>

                <h4>A felhasználó válasza:</h4>

                <?php 
                    if( $task->type == 5 ){
                        echo UIDrawer::fileAnswer($answer->answer);
                    } else{
                        echo UIDrawer::textAnswer($answer->answer);
                    }
                ?>

                <div>
                    <input type="number" min="0" max="<?= $task->max_points ?>" value="0" name="points-<?= $task->id ?>">
                    <textarea placeholder="Feladathoz kapcsolódó, diáknak szánt megjegyzés..." maxlength="10" style="width: 100%;" name="comment-<?= $task->id ?>"></textarea>
                </div>

            </section>
        </div>
        <?php endforeach; ?> <!-- answers foreach vége -->

        <input type="submit" value="Értékelés" class="cta-button color-2">
        </form>
        </div>
    </body>
</html>