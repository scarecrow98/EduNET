<?php

    require_once 'config.php';

    if( empty($_GET['test_instance']) ){
        errorRedirect('Érvénytelen paraméterek');
    }

    Session::start();
    
    if( Security::checkAccessToken() === false ){
        header('Location: logout');
        exit();
    }

    Security::setAccessToken();

    //tanár fiókból nem lehet megoldani a tesztet
    if( Session::get('user-type') == 1 ){
        errorRedirect('A feladatlap megoldása csak diákok számára elérhető!');
    }

    $test_instance_id = $_GET['test_instance'];
    $test_instance = TestInstance::get($test_instance_id);
        
    //ha üres eredményt ad vissza a lekérdezés
    if( empty($test_instance->id) ){
        errorRedirect('A keresett feladatlap nem található!');
    }

    //ha a feladat nem nyitott állapotú
    if( $test_instance->status != 1 ){
        errorRedirect('A feladatlapot jelenleg nem lehet megoldani!');
    }

    //diák ellenőrzése, hogy benne van-e a feladatlap csoportjában
    if( !$test_instance->checkCredentials(Session::get('user-id')) ){
        errorRedirect('Nincs jogosultságod a feladatlap megtekintéséhez!');
    }

    //ellenőrizzük, hogy meg a feladatlap meg lett-e már oldva a diák által
    if( $test_instance->hasResults(Session::get('user-id')) && Session::get('user-type') == 0 ){
        errorRedirect('Ezt a feladatlapod már megoldottad!');
    }


    Session::set('test-instance-id', $test_instance_id);

    $test = Test::get($test_instance->test_id);
    $tasks = $test->getTasks();
    
?>
<html>
    <head>
        <title><?= $test->title ?></title>
        <meta charset="utf-8">
        <link rel="icon" href="<?= PUBLIC_ROOT ?>resources/images/favicon.ico">
        <link rel="icon" href="<?= PUBLIC_ROOT ?>resources/images/favicon.ico">
        <link rel="stylesheet" href="http://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
        <link rel="stylesheet" href="<?= PUBLIC_ROOT ?>css/main.css">
        <link rel="stylesheet" href="<?= PUBLIC_ROOT ?>css/components.css">
        <link rel="stylesheet" href="<?= PUBLIC_ROOT ?>css/content.css">
    </head>
    <body class="test-body">
        <div class="test-container">

            <!-- feladatlap információi (cím, leírás, szöveg) -->
            <div class="task-box panel">
                <header>
                    <h3 class="ion-compose"><?= $test->title ?></h3>
                </header>
                <section>
                    <pre class="task-question">A feladat leírása</pre>
                    <div style="padding: 10px 25px; margin-bottom: 25px;">
                        <?php 
                        if( !empty($test_instance->description) )
                            echo '<pre>'.$test_instance->description.'</pre>';
                        else
                            echo '<i>A feladatlaphoz nem érhető el leírása.</i>';                            
                        ?>
                    </div>

                    <pre class="task-question">A feladathoz kapcsolódó szöveg</pre>
                    <div style="padding: 10px 25px;">
                        <?php 
                        if( !empty($test->text) )
                            echo '<pre class="quote">'.$test->text.'</pre>';
                        else
                            echo '<i>A feladatlaphoz nem érhető el szöveg.</i>';                            
                        ?>
                    </div>
                </section>
            </div>

            <form action="<?= SERVER_ROOT ?>parsers/test-evaluator.php" method="POST" id="submit-test-form" enctype="multipart/form-data">  

            <input type="hidden" name="task-count" id="task-count" value="<?= count($tasks) ?>">
            
            <?php
                $task_counter = 0;
                foreach( $tasks as $task ): /* tasks foreach kezdete */ 
                    $task_counter++;

                    $options = $task->getTaskOptions();
                    $option_data = array();
                    foreach( $options as $option ){ $option_data[] = $option->id; }

                    $task_data = array(
                        'task-id'       => $task->id,
                        'task-type'     => $task->type,
                        'task-options'  => $option_data
                    );
            ?>
                <input type="hidden" name="task-<?= $task_counter ?>-data" id="task-<?= $task_counter ?>-data" value='<?= json_encode($task_data) ?>'>
                
                <div class="task-box panel">   

                    <header>
                        <h3 class="ion-compose"><?= $task->task_number ?>. feladat</h3>
                    </header>

                    <section>
                        <pre class="task-question"><?= $task->question; ?></pre>

                        <?php if( !empty($task->text) ): ?>
                        <pre class="task-text"><?= $task->text ?></pre>
                        <?php endif; ?>

                        <?php if( !empty($task->image) ): ?>
                        <div class="task-image">
                            <a  href="<?= SERVER_ROOT ?>uploads/images/<?= $task->image; ?>" target="_blank">
                                <img src="<?= SERVER_ROOT ?>uploads/images/<?= $task->image; ?>" title="Kattints a nagyobb méretért!">
                            </a>
                        </div>
                        <?php endif; ?>

                        <table class="options-table">
                        <?php
                            foreach( $options as $option ): /* options foreach kezdete */  
                        ?>
                            <tr>
                                <td style="width: 550px;" valign="top">
                                    <label class="option-text"><?= $option->text ?></label>
                                </td>
                                    <?php if( $task->type == 1 ): ?>
                                    <td>
                                        <input type="checkbox" name="option-<?= $option->id; ?>" id="option-<?= $option->id; ?>">
                                    </td>
                                    <?php elseif( $task->type == 3 ): ?>
                                    <td>
                                        <input style="width: 50px;" type="text" maxlength="1" name="option-<?= $option->id; ?>">
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
                            ?>
                                <textarea placeholder="Válaszod helye..." style="width: 100%; height: 120px;" name="textarea-<?= $task->id; ?>"></textarea>
                            <?php
                                elseif( $task->type == 5 ):
                            ?>
                                <td>
                                    <button class="btn-rounded bg-1 btn-open-file-dialog" data-input-id="file-<?= $task->id ?>"><i class="ion-android-upload"></i>ZIP vagy RAR fájl feltöltése</button>
                                    <span class="uploaded-file-name"></span>
                                    <input type="file" name="file-<?= $task->id ?>"  id="file-<?= $task->id ?>" style="display: none;">
                                </td>
                            <?php endif; ?>
                        </table>
                        <p style="text-align: right; margin-top: 30px;">
                            Pontszám: <strong class="task-points"><?= $task->max_points ?>p</strong>
                        </p>
                    </section>
                </div> 
                <?php
                    endforeach; /* tasks foreach vége */
                ?>
                <input type="submit" class="btn-wide bg-1" value="Feladatlap beküldése" id="btn-submit-test">
            </form>
        </div>
    </body>
</html>
<script src="<?= PUBLIC_ROOT ?>js/jquery.js"></script>
<script src="<?= PUBLIC_ROOT; ?>js/ajax-settings.js"></script>
<script src="<?= PUBLIC_ROOT ?>js/main.js"></script>
<script src="<?= PUBLIC_ROOT ?>js/ajax.js"></script>