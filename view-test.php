<?php
    require_once 'config.php';

    Session::start();
    
    if( Security::checkAccessToken() === false ){
        header('Location: logout');
        exit();
    }

    Security::setAccessToken();

    //hiányzó paraméter
    if( empty($_GET['test_instance']) ){
        errorRedirect('A keresett feladatlap nem található!');
    }

    //feladatlap lekérése
    $test_instance = TestInstance::get($_GET['test_instance']);

    //ha nem található a feladatlap
    if( empty($test_instance->id) ){
        errorRedirect('A keresett feladatlap nem található!');
    }

    //diák nem látogathajta az oldalt
    if( Session::get('user-type') != 1 ){
        errorRedirect('Nincs jogosultságod a feladatlap megtekintéséhez!');
    }

    $test = Test::get($test_instance->test_id);
    
?>
<html>
    <head>
        <title><?= $test->title; ?></title>
        <meta charset="utf-8">
        <link rel="icon" href="<?= PUBLIC_ROOT ?>resources/images/favicon.ico">
        <link rel="stylesheet" href="http://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
        <link rel="stylesheet" href="<?= PUBLIC_ROOT ?>css/style.css">
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

            <?php
                $tasks = $test->getTasks();
                foreach( $tasks as $task ): /* tasks foreach kezdete */ 
                    $options = $task->getTaskOptions();
            ?>                
                <div class="task-box panel">   
                    <header>
                        <h3 class="ion-compose"><?= $task->task_number; ?>. feladat</h3>
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
                        </tr>
                        <?php endforeach; /* options foreach vége */ ?> 
                        </table>

                        <p style="text-align: right; margin-top: 30px;">
                            Elérhető pontszám:<strong class="task-points"><?= $task->max_points ?>p</strong>
                        </p>
                    </section>
                </div> <!-- test-sheet vege -->
                <?php
                    endforeach; /* tasks foreach vége */
                ?>            
        </div>
    </body>
</html>
<script src="<?= PUBLIC_ROOT ?>js/jquery.js"></script>
<script src="<?= PUBLIC_ROOT; ?>js/ajax-settings.js"></script>
<script src="<?= PUBLIC_ROOT ?>js/main.js"></script>
<script src="<?= PUBLIC_ROOT ?>js/ajax.js"></script>