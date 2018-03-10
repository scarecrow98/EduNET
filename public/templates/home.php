<?php

    $months = array(
        '1'     => 'Jan.',
        '2'     => 'Febr.',
        '3'     => 'Márc.',
        '4'     => 'Ápr.',
        '5'     => 'Máj.',
        '6'     => 'Jún.',
        '7'     => 'Júl.',
        '8'     => 'Aug.',
        '9'     => 'Szept.',
        '10'    => 'Okt.',
        '11'    => 'Nov.',
        '12'    => 'Dec.' 
    );
	
?>
<header class="content-header">
    <h2>Események</h2>
    
    <?php if( IS_ADMIN ): ?>
        <button class="btn-rounded bg-3 modal-opener" data-modal-id="create-notification" style="float: right; margin-right: 8px;">
            <i class="ion-android-notifications"></i>Események kezelése
        </button>
    <?php endif; ?>
</header>
<section class="content-body" id="stats">

    <div class="home-box panel">
        <header>
            <h4 class="ion-calendar">Közelgő dolgozatok</h4>
            <small>Ez elkövetkezendő 30 nap eseményei</small>
        </header>
        <div id="event-calendar">
        <?php

            $today = new DateTime(date('Y-m-d'));    
			
            for( $i = 1; $i <= 30; $i++ ){
                $date = $today->modify('+1 day');
				
				$notifications = Notification::getAllByDate(Session::get('user-id'), Session::get('user-type'), $date->format('Y-m-d'));

				
                $month = $date->format('m');
                $day = $date->format('d');

				$html = '';
                $type = '';
				
                foreach( $notifications as $nt ){
					$subject = Subject::get($nt->subject_id);
					$group = Group::get($nt->group_id);
					
                    switch( $nt->type ){
                        case 1: $type = 'Szóbeli felelet';
                            break;
                        case 2: $type = 'Dolgozat';
                            break;
                        case 3: $type = 'Témazáró dolgozat';
                            break;
                        case 4: $type = 'Egyéb esemény';
                            break;
                    }
                    $html .= '
                        <li class="event">
                            <strong>'.$nt->title.'</strong>
                            <p><i class="ion-information-circled"></i>'.$type.'</p>
                            <p><i class="ion-ios-people"></i>'.$group->name.'</p>
                            <p><i class="ion-university"></i>'.$subject->name.'</p>
                        </li>
                    ';
                }
        ?>            
            <div class="day <?= empty($html) ? '' : 'has-event' ?>" >
                <i class="ion-chevron-down"></i>
                <span class="month-name"><?= $months[(int)$month].' '; ?></span><span class="month-day"><?= $day; ?></span>
                <?php if( $html != '' ): ?>
                <section class="panel">
                    <?= $html; ?>
                </section>
                <?php endif; ?>
            </div>
        <?php } ?> <!-- dátum for vége -->
        </div>
    </div>

    <!-- ha nem tanár a felhaszáló, akkor megjelenítjük az utolsó 3 kijavított dolgozat eredményeit -->
    <?php if( !IS_ADMIN ): ?>
    <div class="home-box panel">
        <header>
            <h4 class="ion-android-checkmark-circle">Kijavított dolgozatok</h4>
            <small>Legutóbb kijavított dolgozataid és eredményeik</small>
        </header>
        <?php
        $data = TestInstance::getEvaluatedInstances(Session::get('user-id'));
        foreach( $data as $d ):
            $test_instance = TestInstance::get($d['test_instance_id']);
            $test = Test::get($test_instance->test_id);
            $tasks = $test->getTasks();
            $max_points = 0;
            $user_points = 0;
        ?>
        <h1><?= $test->title; ?></h1>
            <?php 
                foreach( $tasks as $task ):    
                $result = Task::getResult($task->id, $test_instance->id, Session::get('user-id'));
                $split = explode('/', $result['result']);
                $max_points += $split[0];
                $user_points += $split[1];
            ?>
            <li>
                <strong><?= $task->task_number ?>. feladat</strong>
                <label for=""><?= $result['result']; ?></label>
            </li>
            <?php endforeach; ?>
            <label for="">Végeredmény: <?= $max_points.'/'.$user_points ?></label>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

</section>
