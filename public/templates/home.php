
<header class="content-header">
    <h2>Események</h2>
    
    <?php if( IS_ADMIN ): ?>
        <button class="btn-rounded bg-3 modal-opener" data-modal-id="create-notification" style="float: right; margin-right: 8px;">
            <i class="ion-android-notifications"></i>Események kezelése
        </button>
    <?php endif; ?>
</header>
<section class="content-body" id="stats">

    <div class="content-box panel">
        <header>
            <h4 class="ion-calendar">Közelgő dolgozatok</h4>
            <small>Ez elkövetkezendő 30 nap eseményei</small>
        </header>
        <div id="event-calendar">
        <?php
			//mai nap lekérése
            $today = new DateTime(date('Y-m-d'));    
			
			//for ciklus, 1-től 30-ig --> ebben rajzoljuk ki a naptár napjait + értesítések megjelenítése
            for( $i = 1; $i <= 30; $i++ ){
                //a mindig egy nappal növelt dátum
				$date = $today->modify('+1 day');
		
				//a dátumhoz tartozó értesítések lekérése felhasználó szerint
				$notifications = Notification::getAllByDate(Session::get('user-id'), Session::get('user-type'), $date->format('Y-m-d'));

				//dátum hónapja (számmal)
                $month = $date->format('m');
				
				//dátum napja (számmal)
                $day = $date->format('d');

				//generált html ebben lesz tárolva
				$html = '';
				//értesítés típusa szövegesen ebbe lesz tárolva
                $type = '';
				
				//lekért értesítéseken végigiterálunk
                foreach( $notifications as $nt ){
					//tantárgy és a csoport adatainak lekérése
					$subject = Subject::get($nt->subject_id);
					$group = Group::get($nt->group_id);
					
					//értesítés típusának meghatározása
                    switch( $nt->type ){
                        case 1: $type = 'Szóbeli felelet';
                            break;
                        case 2: $type = 'Dolgozat';
                            break;
                        case 3: $type = 'Témazáró dolgozat';
                            break;
                    }
					//html listaelem generálása
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
		<!--  -->
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
    <div class="content-box panel">
        <header>
            <h4 class="ion-android-checkmark-circle">Kijavított dolgozatok</h4>
            <small>Legutóbb kijavított dolgozataid és eredményeik</small>
        </header>
        <div id="result-box-container">
        <?php
        $data = TestInstance::getEvaluatedInstances(Session::get('user-id'));
        foreach( $data as $d ):
            $test_instance = TestInstance::get($d['test_instance_id']);
            $test = Test::get($test_instance->test_id);
            $tasks = $test->getTasks();
            $max_points = 0;
            $user_points = 0;
        ?>
        <div class="result-box">
        <h2><?= $test->title; ?></h2>
            <?php 
                foreach( $tasks as $task ):    
                $result = Task::getResult(Session::get('user-id'), $test_instance->id, $task->id);
                $split = explode('/', $result['result']);
                $max_points += $split[0];
                $user_points += $split[1];
            ?>
            <li>
                <span><?= $task->task_number ?>. feladat</span>
                <strong class="task-points" style="font-size: 14px;"><?= $result['result']; ?></strong>
            </li>
            <?php endforeach; ?>
            <li style="border-top: 1px solid var(--theme-grey-dark)">
                <span>Végeredmény:</span>
                <strong class="task-points" style="font-size: 14px;"><?= $max_points.'/'.$user_points ?></strong>
            </li>
            <li>
                <button class="btn-wide bg-5">
                    <a href="check-test.php?test_instance=<?= $test_instance->id ?>" target="_blank">Feladatlap megtekintése &raquo;</a>
                </button>
            </li>
        <?php endforeach; ?>
        <div>
        </div>
    </div>
    <?php endif; ?>

</section>
