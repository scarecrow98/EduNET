<?php

    require_once '../../config.php';
    Session::start();

    if( empty( $_POST['test-submission'] ) ){
        exit();
    }
    
    $user_id = Session::get('user-id');
	//az aktuálisan megoldásra kerülő feladatlappéldány azonosítója
    $test_instance_id = Session::get('test-instance-id');
	//feladatlappéldány és a bázisfeladatlap objektumainak lekérése
    $test_instance = TestInstance::get($test_instance_id);
    $test = Test::get($test_instance->test_id);

    
    //POST kérésből átvesszük a feladatokat és adataikat tároló tömböt
	//visszalakítjuk PHP számára is érthető adatszerkezetté (több dimenziós tömb lesz)
    $answers = json_decode($_POST['answers'], true);

    foreach( $answers as $answer ){
		//feladat adataink lekérése
        $task= Task::get($answer['task-id']);
		//ebben lesz tárolva, hogy a feladatban hány pontot ért el a diák
        $user_task_points = 0;

        //ha a feladat szöveges válasz típusú
        if( $task->type == 2 ){
			//válasz változóba rakása ha van, ha nincs akkor a válasz null
            $user_answer = empty($answer['text-answer']) ? null : $answer['text-answer'];

			//adattöümb elkészítése a storeText metódusnak
            $data = array(
                'user_id'           => Session::get('user-id'),
                'task_id'           => $task->id,
                'test_instance_id'  => $test_instance_id,
                'answer'            => htmlspecialchars($user_answer)
            );
            Answer::storeText($data); //szöveges válasz eltárolása
        }
        //ha a feladat fájl típusú
        elseif( $task->type == 5 ){
			//FILES tömbbeli neve a fájlnak
            $file_index = $answer['file-name'];
			//fájl eltárolása
            $file = $_FILES[$file_index];
			
			//fájl feldolgozása
            $file_name = null;
            if( !empty($file) ){
                $fu = new FileUploader($file, 'file', 'solution_file');
                $file_name = $fu->checkFile();
            }

			//adattömb készítése a storeFile metódusnak
            $data = array(
                'user_id'           => Session::get('user-id'),
                'task_id'           => $task->id,
                'test_instance_id'  => $test_instance_id,
                'file_name'         => $file_name
            );
            Answer::storeFile($data); //fájl válasz eltárolása
        }
        //egyébként opciókat tartalmaz a feladat, tehát megvizsgáljuk az opciókat
        else{

            $total_correct_answers = 0; /* ebben tároljuk majd, hogy egy feladathoz hány helyes válasz lehetséges maximum  */
            $quiz_correct_user_answers = 0; /* ebbe tároljuk, hogy a kvízfeladatban mennyit talált el a diák */
            $correct_user_answers = 0; /* ebbe tároljuk, hogy igaz/hamis vagy párosítás feladatokban mennyit talált el a diák */

            for( $i = 0; $i < count($answer['task-options']); $i++ ){
                $option = TaskOption::get($answer['task-options'][$i]['option-id']); /* az opció helyes válaszának lekérése */

                //feladattíustól függően diák válaszainak kialakítása
                //pl.: a nem bepipált checkboxoknak 0 lesz az értéke, a bebipáltnak 1
                switch( $task->type ){
                    case 1: // kvíz
						//diák által adott válasz eltárolása (ha bejelölte 1-es lesz, ha nem akkor 0)
                        $user_answer = $answer['task-options'][$i]['value'];
						//lehetséges jó válasznak csak az 1-esek, tehát a bejelölendő opciók számítanak
                        if( $option->correct_ans == 1 ){ $total_correct_answers++; }
                    break;
                    case 3: //párosítása
						//diák által adott válasz eltárolása (ha beírt valamilyen karaktert, akkor az a válasz, ha nem akkor null )
                        $user_answer = !empty($answer['task-options'][$i]['option-id']) ? $answer['task-options'][$i]['value'] : null;
                        //a lehetséges jó válaszok száma itt annyi, ahány feladatopció van
						$total_correct_answers++;
                    break;
                    case 4://igaz/hamis
						//diák által adott válasz eltárolása (ha igazat jelölt akkor 1, ha hamisat akkor 0, ha egyiket sem akkor null)
                        $user_answer = isset($answer['task-options'][$i]['option-id']) ? $answer['task-options'][$i]['value'] : null;
                        //a lehetséges jó válaszok száma itt annyi, ahány feladatopció van
						$total_correct_answers++;
                    break;
                }

                //kvíz feladatok értkélelése eltér az igaz/hamis és a párosítós feladatoktól
                //kvíznél változó lehet a maximális helyes megoldások száma, míg a többinél akár az összes opció lehet helyes
                
				//ez tárolja, hogy a feladatopcióra helyesen válaszolt-e
				$is_correct = 0
				//ha a diák válasza megegyezik az adatbázisban lévő helyes értékkel
                if( $option->correct_ans == $user_answer ){
					//1-re állítjuk
					$is_correct = 1;
					
					//ha az adatbázisban lévő helyes megoláds az 1-es és a feladat típusa kvíz,
					//akkor a $quiz_correct_user_answers-t növeljük,
					//mivel kvíz feladatoknál jó válasznak csak az számit, ami 1-es (arra nem kap pontot, ha ne jelölte be azt, amit amúgy sem kellene)
                    if( $option->correct_ans == 1 && $task->type == 1 ){
                        $quiz_correct_user_answers++;
                    }
					//egyébként egyszerűen növeljük a $correct_user_answers értékét, mert eltalált egy feladatopciót
                    else{
                        $correct_user_answers++;
                    }
                }

				//adattömb készítése a store metódusnak
                $data = array(
                    'user_id'           => Session::get('user-id'),
                    'task_option_id'    => $option->id,
                    'test_instance_id'  => $test_instance_id,
                    'answer'            => $user_answer,
                    'is_correct'        => $is_correct
                );
                Answer::store($data); /* a diák válaszának eltárolása */
            } //options for vége

			
			//pontok összesítése, feladatban elért eredmény kiszámítása
			
			//egy opcióra járó pont kiszámítása --> feladatra járó max pont / összes lehetséges jó válasz
            $points_per_options = $task->max_points / $total_correct_answers; 
            
            //felhasználó által elért pontok kiszámítása --> egy opcióra adható pont * ahány opciót eltalált 
            //ha kvíz, akkor a diák által helyesen bejelölt opciók számával szorozzuk az egy opcióra járó pontot
			if( $task->type == 1 ){
                $user_task_points = $points_per_options * $quiz_correct_user_answers; 
            }
			//egyéb esetben a helyesen megválaszolt opciók számával szorozzuk az egy opcióra járó pontok számát
            else{
                $user_task_points = $points_per_options * $correct_user_answers;
            }

			//adattömb készítése a storeResult metódusnak
            $data = array(
                'user_id'           => Session::get('user-id'),
                'test_instance_id'  => $test_instance_id,
                'result'            => $task->max_points.'/'.$user_task_points,
                'comment'           => null
            );
            $task->storeResult($data); /* egy feladatra vonatkozó eredmény rögzítése */

        }
    }

    //ha nincs a feladatlapnak szöveges vagy fájl típusú feladata, ami emberi ellenőrzésre szorulna,
    //akkor eltárolhatjuk a diák feladatlapját kijavítottként
    if( !$test->hasFileOrTextTypeTask() ){
        $test_instance->storeEvaluation($user_id, date('Y-m-d H:i:s'));
    }

	//session adatok törlése
    Session::unset('task-data');
    Session::unset('test-instance-id');

    echo 'success';
	
?>