<?php
    require_once 'config.php';

    Session::start();
    
    //hiányzó paraméter
    if( empty($_GET['test_instance']) ){
        errorRedirect('Érvénytelen paraméterek!');
        exit();
    }

    $test_instance = TestInstance::get($_GET['test_instance']);

    //ha nem található a feladatlap
    if( empty($test_instance->id) ){
        errorRedirect('A keresett feladatlap nem található!');
        exit();
    }

    //teszt státuszának ellenőrzése, hogy a diák ne tudja megnézni, ha még nem volt megnyitva
    if( $test_instance->status != 2 && Session::get('user-type') == 0 ){
        errorRedirect('A feladatlap jelenleg nem tekinthető meg!');
        exit();
    }

    //diák ellenőrzése, hogy benne van-e a feladatlap csoportjában
    if( !$test_instance->checkCredentials(Session::get('user-id')) && Session::get('user-type') == 0 ){
        errorRedirect('Nincs jogosultságod a feladatlap megtekintéséhez!');
        exit();
    }

    $test = Test::get($test_instance->test_id);
    
?>
<html>
    <head>
        <title><?= $test->title; ?></title>
        <meta charset="utf-8">
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
            <?php
                $tasks = $test->getTasks();
                foreach( $tasks as $task ): /* tasks foreach kezdete */ 
            ?>
                <div class="test-sheet panel">   
                    <header class="bg-1">
                        <h3 class="ion-compose"><?= $task->task_number; ?>. feladat</h3>
                    </header>
                    <section>
                        <label for="" style="width: auto; font-weight: 600;"><?= $task->question; ?></label>
                        <small>( <?= $task->max_points; ?> pont )</small>

                        <pre><?php if( !empty($task->text) ) echo $task->text; ?></pre>

                        <?php if( !empty($task->image) ): ?>
                            <a href="<?= SERVER_ROOT; ?>uploads/images/<?= $task->image; ?>" target="_blank">
                                <img src="<?= SERVER_ROOT; ?>uploads/images/<?= $task->image; ?>" alt="" style="width: 300px; display: block; margin-bottom: 20px;">
                            </a>
                        <?php endif; ?>

                        <table>
                        <?php
                            $options = $task->getTaskOptions();
                            //ha ütartoznak opciók a feladatlaphoz, megjelenítjük őket
                            if( !empty( $options ) ): 
                        ?>
                            <?php foreach( $options as $option ): ?>
                            <tr>
                                <td>
                                    <li>
                                        <label class="label-small" style="display: inline-block; width: auto;"><?= $option->text; ?></label>
                                    </li>
                                </td>
                            </tr>
                            <?php endforeach; /* options foreach vége */ ?>                           
                            <?php endif; ?>
                        </table>
                    </section>
                </div> 
                <?php endforeach; /* tasks foreach vége */ ?>
        </div>
    </body>
</html>
<script src="js/jquery.js"></script>
<script src="js/main.js"></script>
<script src="js/ajax.js"></script>