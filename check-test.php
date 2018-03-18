<?php

    require_once 'config.php';
    Session::start();

    $test_instance = TestInstance::get($_GET['test_instance']);
    $test = Test::get($test_instance->test_id);
    $tasks = $test->getTasks();
    $user_id = Session::get('user-id');
?>
<html>
    <head>
        <title><?= $test->title ?></title>
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

            <?php
                foreach( $tasks as $task ): /* tasks foreach kezdete */ 
                    $options = $task->getTaskOptions();
                    $result = Task::getResult($user_id, $test_instance->id, $task->id);
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

                        <?php if( $task->option_count > 0 ): ?>
                        <table class="options-table">
                            <tr>
                                <td></td>
                                <td>Helyes válasz</td>
                                <td>Eredményed</td>
                            </tr>
                            <?php
                            foreach( $options as $option ): /* options foreach kezdete */  
                                $user_ans = Answer::getByOptionId($user_id, $test_instance->id, $option->id);
                            ?>
                            <tr>
                                <td style="width: 550px;" valign="top">
                                    <label class="option-text"><?= $option->text ?></label>
                                </td>
                                <?php 
                                if( $task->type == 1 )
                                    echo UIDrawer::quizResult($option->correct_ans, $user_ans->answer);
                                elseif( $task->type == 3 )
                                    echo UIDrawer::pairingResult($option->correct_ans, $user_ans->answer);
                                elseif( $task->type == 4 )
                                    echo UIDrawer::trueFalseResult($option->correct_ans, $user_ans->answer);
                                ?>
                            </tr>
                            <?php endforeach; /* options foreach vége */ ?> 
                        </table>
                        <?php endif; ?>

                        <div class="user-answer">
                        <?php 
                            //ha a feladat fájl típusú akkor letöltséi linket jelenítünk meg, egyébkét pedig a szöveges válaszát
                            if( $task->type == 5 ){
                                $answer = Answer::getFileAnswer($user_id, $test_instance->id, $task->id);
                                echo UIDrawer::fileAnswer($answer->answer);
                            } else if( $task->type == 2 ){
                                $answer = Answer::getTextAnswer($user_id, $test_instance->id, $task->id);
                                echo UIDrawer::textAnswer($answer->answer);
                            }
                        ?>
                        </div>

                        <p style="text-align: right; margin-top: 30px;">
                            Eredmény:<strong class="task-points"><?= $result['result'] ?>p</strong>
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