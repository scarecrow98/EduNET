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
    <h2>Főoldal</h2>
</header>
<section class="content-body" id="stats">

    <div class="home-box panel">
        <header>
            <h4 class="ion-calendar">Közelgő dolgozatok</h4>
            <small>Feladatlapok, melyek 30 napon belül kerülnek megírásra</small>
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
                $bgcolor = '';
				
                foreach( $notifications as $nt ){
					$subject = Subject::get($nt->subject_id);
					$group = Group::get($nt->group_id);
					
                    switch( $nt->type ){
                        case 0: $bgcolor = 'green';
                            break;
                        case 1: $bgcolor = 'orange';
                            break;
                        case 2: $bgcolor = 'red';
                            break;
                    }
                    $html .= '<li style="border-left: 4px solid '.$bgcolor.'"><h4>'.$nt->title.'</h4><p>'.$group->name.' - '.$subject->name.'</p></li>';
                }
        ?>            
            <div class="day <?php echo empty($html)?'':'has-event' ?>" >
                <i class="ion-chevron-down"></i>
                <span class="month-name"><?php echo $months[(int)$month].' '; ?></span><span class="month-day"><?php echo $day; ?></span>
                <?php if( $html != '' ): ?>
                <section class="panel">
                    <?php echo $html; ?>
                </section>
                <?php endif; ?>
            </div>
        <?php } ?>
        </div>
    </div>

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
        <h1><?php echo $test->title; ?></h1>
            <?php 
                foreach( $tasks as $task ):    
                $result = Task::getResult($task->id, $test_instance->id, Session::get('user-id'));
                $split = explode('/', $result['result']);
                $max_points += $split[0];
                $user_points += $split[1];
            ?>
            <li>
                <strong><?php echo $task->task_number ?>. feladat</strong>
                <label for=""><?php echo $result['result']; ?></label>
            </li>
            <?php endforeach; ?>
            <label for="">Végeredmény: <?php echo $max_points.'/'.$user_points ?></label>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

</section>
