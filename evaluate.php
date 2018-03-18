<?php

    require_once('config.php');
    Session::start();
    
    //biztonsági token ellenőzsée
    if( Security::checkAccessToken() === false ){
        header('Location: logout');
        exit();
    }

    //token újragenerálása
    Security::setAccessToken();


    //helyes URL paraméterek ellenőrzése
    if( empty($_GET['test_instance']) || empty($_GET['user'])  ){
        errorRedirect('A megadott paraméterekkel nem létezik feladatlap!');
        exit();
    }

    $user_id = $_GET['user'];
    $test_instance_id = $_GET['test_instance'];

    $test_instance = TestInstance::get($test_instance_id);
    $test = Test::get($test_instance->test_id);
    $answers = Answer::getFileAndTextAnswers($user_id, $test_instance_id);
    $students = $test_instance->getStudents();


    define('IS_EVALUATED', $test_instance->hasEvaluatedInstance($user_id));
    //létező eredmények ellenőrzése
    // if( empty($answers) && empty($file_answers) ){
    //     errorRedirect('Helytelen feladatlap azonosító!');
    //     exit();
    // }

    //tanár ellenőrzése, hogy valóban az ő feladatlapja-e
    if( $test_instance->current_author_id != Session::get('user-id') ){
        errorRedirect('Nincs jogosultságod az oldal megtekintéséhez!');
        exit();
    }

?>
<html>
    <head>
        <title>Feladatlap javítása</title>
        <meta charset="utf-8">
        <link rel="icon" href="<?= PUBLIC_ROOT; ?>resources/images/favicon.ico">
        <link rel="stylesheet" href="http://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
        <link rel="stylesheet" href="<?= PUBLIC_ROOT; ?>css/style.css">
        <style>
            body{ height: 100vh; width: auto; margin: 10px; overflow-y: scroll; }
        </style>
    </head>
    <body class="test-body">
        
        <!-- diák választó -->
        <div id="student-selector">
            <?php foreach( $students as $student ): ?>
            <li class="panel">
                <a href="evaluate.php?test_instance=<?= $test_instance_id ?>&user=<?= $student->id ?>">
                    <img src="<?= SERVER_ROOT ?>uploads/avatars/<?= $student->avatar ?>">
                    <strong for=""><?= $student->name ?></strong>
                </a>
            </li>
            <?php endforeach; ?>
        </div>

        <div class="test-container">
            <!-- feladatlap információi (cím, leírás, szöveg) -->
            <div class="task-box panel">
                <header>
                    <h3 class="ion-compose"><?= $test->title ?></h3>
                </header>
                <section>
                    <label class="label-bold">A feladat leírása</label>
                    <div style="padding: 25px;">
                        <?php 
                        if( !empty($test_instance->description) )
                            echo '<pre>'.$test_instance->description.'</pre>';
                        else
                            echo '<i>A feladatlaphoz nem érhető el leírása.</i>';                            
                        ?>
                    </div>

                    <label class="label-bold">A feladathoz kapcsolódó szöveg</label>
                    <div style="padding: 25px;">
                        <?php 
                        if( !empty($test->text) )
                            echo '<pre class="quote">'.$test->text.'</pre>';
                        else
                            echo '<i>A feladatlaphoz nem érhető el szöveg.</i>';                            
                        ?>
                    </div>
                </section>
            </div>

            <form method="POST" action="<?= SERVER_ROOT; ?>parsers/manual-evaluator.php">
                <!-- rejtett mezőkben tároljuk a diák és a feladatlappéldány azonosítóját, amit elküldünk majd a javítást feldolgozó manual-evaluator.php-nak -->
                <input type="hidden" name="user-id" value="<?= $user_id ?>">
                <input type="hidden" name="test-instance-id" value="<?= $test_instance_id; ?>">

                <?php
                    // foreach ciklussal végigmegyünk a diák válaszain
                    $task_count = 0;
                    foreach($answers as $answer):
                    $task_count++;
                    $task = Task::get($answer->task_id);
                ?>

                <!-- egy 'task-{feladat sorszáma a foreach ciklusban}-id' nevű inputban eltároljuk a feladat azonosítóját -->
                <input type="hidden" name="task-<?= $task_count ?>-id" value='<?= $task->id ?>'>

                <div class="task-box panel">  

                    <header>
                        <h3 class="ion-compose"><?= $task->task_number ?>. feladat</h3>
                    </header>

                    <section>
                        <pre class="task-question"><?= $task->question; ?></pre>

                        <!-- ha van feladatszöveg, megjelenítjük -->
                        <?php if( !empty($task->text) ): ?>
                            <pre class="task-text"><?= $task->text ?></pre>
                        <?php endif; ?>

                        <!-- ha van feladatkép, megjelenítjük -->
                        <?php if( !empty($task->image) ): ?>
                            <div class="task-image">
                                <a  href="<?= SERVER_ROOT ?>uploads/images/<?= $task->image ?>" target="_blank">
                                    <img src="<?= SERVER_ROOT ?>uploads/images/<?= $task->image ?>" title="Kattints a nagyobb méretért!">
                                </a>
                            </div>
                        <?php endif; ?>

                        <label class="label-bold">Diák megoldása:</label>
                        <div class="user-answer">
                        <?php 
                            //ha a feladat fájl típusú akkor letöltséi linket jelenítünk meg, egyébkét pedig a szöveges válaszát
                            if( $task->type == 5 ){
                                echo UIDrawer::fileAnswer($answer->answer);
                            } else{
                                echo UIDrawer::textAnswer($answer->answer);
                            }
                        ?>
                        </div>

                        <?php if( !IS_EVALUATED ): //ha még nincs kijavítva ennek a diáknak a feladatlapja, javítási inputokat megejlenítjük ?>
                        <div class="evaluate-inputs" style="padding-top: 20px;">
                            <li style="margin-bottom: 16px;">
                                <label class="label-bold">Elért pontszám:</label>
                                <input type="number" min="0" max="<?= $task->max_points ?>" value="0" name="points-<?= $task_count ?>">
                            <li>
                            <li style="margin-bottom: 16px;">
                                <label class="label-bold">Megjegyzés:</label>
                                <textarea placeholder="Feladathoz kapcsolódó, diáknak szánt megjegyzés..." style="width: 100%; height: 130px;" name="comment-<?= $task_count ?>"></textarea>
                            <li>
                        </div>
                        
                        <p style="text-align: right; margin-top: 30px;">
                            Elérhető pontszám:<strong class="task-points"><?= $task->max_points ?>p</strong>
                        </p>
                        <?php else: //ha már ki lett javítva a feladatlap, akkor csak a diák elért pontszámát mutatjuk ?>
                        <?php
                            $result = Task::getResult($user_id, $test_instance->id, $task->id);
                        ?>
                        <p style="text-align: right; margin-top: 30px;">
                            Elért pontszám:<strong class="task-points"><?= $result['result']; ?>p</strong>
                        </p>
                        <?php endif; ?>

                    </section>
                </div>
                <?php endforeach; ?> <!-- answers foreach vége -->
                <!-- input, amiben eltároltuk, hogy hány feladatot javítottunk -->
                <input type="hidden" name="task-count" value="<?= $task_count ?>">

                <?php if( !IS_EVALUATED ): ?><button type="submit" class="btn-wide bg-1">Értékelés</button><?php endif; ?>
            </form>
        </div>
    </body>
</html>