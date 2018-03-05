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
    $text_answers = Answer::getTextAnswers($user_id, $test_instance_id);
    $file_answers = Answer::getFileAnswers($user_id, $test_instance_id);
    $students = $test_instance->getStudents();

    //print_r($file_answers);

    //létező eredmények ellenőrzése
    if( empty($answers) && empty($file_answers) ){
        errorRedirect('Helytelen feladatlap azonosító!');
        exit();
    }

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

        <div class="test-container">
        <h2>Szöveges válaszok:</h2>
        <form method="POST" action="<?= SERVER_ROOT; ?>parsers/manual-evaluator.php">
            <input type="hidden" name="user-id" value="<?= $user_id ?>">
            <input type="hidden" name="test-instance-id" value="<?= $test_instance_id; ?>">
        <?php
            $task_count = 0;
            foreach($text_answers as $answer):
            $task_count++;
            $task = Task::get($answer->task_id);
            $result = Task::getResult($task->id, $test_instance->id, $user_id);
            $HAS_RESULT = empty($result['result'])?false:true;
        ?>
        <input type="hidden" name="task-id-<?= $task_count; ?>" value="<?= $task->id; ?>">
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
                    <input type="number" min="0" max="<?= $task->max_points ?>" value="0" name="user-points-<?= $task_count; ?>">
                    <textarea placeholder="Feladathoz kapcsolódó, diáknak szánt megjegyzés..." maxlength="10" style="width: 100%;" name="teacher-comment-<?= $task_count; ?>"></textarea>
                </div>
                <?php else: ?>
                    <label for="">Eredmény: <?= $result['result'] ?></label>
                <?php endif; ?>
            </section>
        </div>
    <?php endforeach; ?>




    <h2>Fájl válaszok:</h2>
    <?php
        $task_count = 0;
        foreach($file_answers as $answer):
        $task_count++;
        $task = Task::get($answer->task_id);
        $result = Task::getResult($task->id, $test_instance->id, $user_id);
        $HAS_RESULT = empty($result['result'])?false:true;
    ?>
        <input type="hidden" name="task-id-<?= $task_count; ?>" value="<?= $task->id; ?>">

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
                <i>
                <?php
                    if( empty($answer->file_name) ){
                        echo '<span style="color: red">A felhasználó nem válaszolt erre a kérdésre!</span>';
                    }
                    else{
                        echo '<a href="'.SERVER_ROOT.'uploads/files/'.$answer->file_name.'">Fájl letöltése</a>';
                    }
                ?>
                </i>
                <?php if( !$HAS_RESULT ): ?>
                <div>
                    <input type="number" min="0" max="<?= $task->max_points ?>" value="0" name="user-points-<?= $task_count; ?>">
                    <textarea placeholder="Feladathoz kapcsolódó, diáknak szánt megjegyzés..." maxlength="10" style="width: 100%;" name="teacher-comment-<?= $task_count; ?>"></textarea>
                </div>
                <?php else: ?>
                    <label for="">Eredmény: <?= $result['result'] ?></label>
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