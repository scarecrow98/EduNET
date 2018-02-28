<?php

    require_once 'config.php';

    if( empty($_GET['test_instance']) ){
        errorRedirect('Érvénytelen paraméterek');
        exit();
    }

    Session::start();
    
    //tanár fiókból nem lehet megoldani a tesztet
    if( Session::get('user-type') == 1 ){
        errorRedirect('A feladatlap megoldása csak diákok számára elérhető!');
        exit();
    }

    $test_instance_id = $_GET['test_instance'];
    $test_instance = TestInstance::get($test_instance_id);
        
    //ha üres eredményt ad vissza a lekérdezés
    if( empty($test_instance->id) ){
        errorRedirect('A keresett feladatlap nem található!');
        exit();
    }

    //ha a feladat nem nyitott állapotú
    if( $test_instance->status != 1 ){
        errorRedirect('A feladatlapot jelenleg nem lehet megoldani!');
        exit();
    }

    //diák ellenőrzése, hogy benne van-e a feladatlap csoportjában
    if( !$test_instance->checkCredentials(Session::get('user-id')) ){
        errorRedirect('Nincs jogosultságod a feladatlap megtekintéséhez!');
        exit();
    }

    //ellenőrizzük, hogy meg a feladatlap meg lett-e már oldva a diák által
    if( $test_instance->hasResults(Session::get('user-id')) && Session::get('user-type') == 0 ){
        errorRedirect('Ezt a feladatlapod már megoldottad!');
        exit();
    }


    Session::set('test-instance-id', $test_instance_id);

    $test = Test::get($test_instance->test_id);

    //diák válaszait tároló tömb inicializálása
    $_SESSION['task-data'] = array();
?>
<html>
    <head>
        <title><?= $test->title ?></title>
        <meta charset="utf-8">
        <link rel="icon" href="<?= PUBLIC_ROOT ?>resources/images/favicon.ico">
        <link rel="stylesheet" href="http://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
        <link rel="stylesheet" href="<?= PUBLIC_ROOT ?>css/main.css">
        <link rel="stylesheet" href="<?= PUBLIC_ROOT ?>css/components.css">
        <link rel="stylesheet" href="<?= PUBLIC_ROOT ?>css/content.css">
        <style>
            .test-sheet section{ padding: 15px; }
        </style>
    </head>
    <body class="test-body">
        <div class="test-container">
            <h1><?= $test->title; ?></h1>
            <form action="<?= SERVER_ROOT ?>parsers/test-evaluator.php" method="POST" id="submit-test-form" enctype="multipart/form-data">  

            <?php
                $tasks = $test->getTasks();
                foreach( $tasks as $task ): /* tasks foreach kezdete */ 
            ?>
                <div class="test-sheet panel">   
                    <header class="bg-1">
                        <h3 class="ion-compose"><?= $task->task_number; ?>. feladat</h3>
                    </header>
                    <section>
                        <label for="" style="width: auto;"><?= $task->question; ?></label>
                        <small>( <?= $task->max_points; ?> pont )</small>

                        <?php if( !empty($task->text) ): ?>
                        <pre style="white-space: pre-wrap; color: #b2b2b2; font-style: italic; padding: 15px;"><? $task->text ?></pre>
                        <?php endif; ?>

                        <?php if( !empty($task->image) ): ?>
                            <a href="<?= SERVER_ROOT ?>uploads/images/<?= $task->image; ?>" target="_blank">
                                <img src="<?= SERVER_ROOT ?>uploads/images/<?= $task->image; ?>" alt="" style="width: 300px; display: block; margin-bottom: 20px;">
                            </a>
                        <?php endif; ?>
                        <table>
                        <?php
                            $task_data = array(); /* tömb létrehozása, ami az aktuális feladat adatátit tárolja (típus, opciok száma, opciók id-je) */
                            $task_data['task-type'] = $task->type; /* előbbi tömbbe megadjuk, hogy milyen típusú az adott feladat */
                            $task_data['option-count'] = 0;
                            $task_data['task-id'] = $task->id;

                            $options = $task->getTaskOptions();
                        ?>
                            <?php
                                foreach( $options as $option ): /* options foreach kezdete */  
                                    array_push( $task_data, $option->id ); /* tömbhöz hozzáadjuk a feladathoz tartozó opció id-jét */
                                    $task_data['option-count']++; /* opciószámlálót növeljük */
                            ?>
                            <tr>
                                <td>
                                    <label class="label-small" style="display: inline-block; width: auto;" for="option-<?= $option->id ?>"><?= $option->text; /* opció szövege */ ?></label>
                                </td>
                                    <?php if( $task->type == 1 ): ?>
                                    <td>
                                        <input type="checkbox" name="option-<?= $option->id; ?>" id="option-<?= $option->id; ?>">
                                    </td>
                                    <?php elseif( $task->type == 3 ): ?>
                                    <td>
                                        <input type="text" maxlength="1" name="option-<?= $option->id; ?>">
                                    </td>
                                    <?php elseif( $task->type == 4 ): ?>
                                    <td>
                                        igaz<input type="radio" value="1" name="option-<?= $option->id; ?>">
                                        hamis<input type="radio" value="0" name="option-<?= $option->id; ?>">
                                    </td>
                                    <?php endif; ?>
                            </tr>
                            <?php endforeach; /* options foreach vége */ ?> 
                            
                            <?php
                                if( $task->type == 2 ):
                                array_push( $task_data, $task->id ); /* ha szövegesválasz típusú a feladat, akkor a feladat id-jét adjuk a tömbhöz */
                            ?>
                                <textarea placeholder="Ide írd a válaszod..." style="width: 100%;" name="textarea-<?= $task->id; ?>"></textarea>
                            <?php
                                elseif( $task->type == 5 ):
                                array_push( $task_data, $task->id ); /* ha fájl típusú a feladat, akkor a feladat id-jét adjuk a tömbhöz */
                            ?>
                                <td>
                                    Fájl feltöltése:
                                    <input type="file" name="file-<?= $task->id ?>">
                                </td>
                            <?php endif; ?>
                            </table>
                    </section>
                </div> 
                <?php
                    array_push($_SESSION['task-data'], $task_data); /* a tömböket tároló sessionbe eltároljuk a feladat adatai tároló tömböt */
                    endforeach; /* tasks foreach vége */
                ?>
                <input type="submit" class="btn-wide bg-1" value="Feladatlap beküldése" id="btn-submit-test">
            </form>
        </div>
    </body>
</html>
<script src="<?= PUBLIC_ROOT ?>js/jquery.js"></script>
<script src="<?= PUBLIC_ROOT ?>js/main.js"></script>
<script src="<?= PUBLIC_ROOT ?>js/ajax.js"></script>