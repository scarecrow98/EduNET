<?php
	$message= ' - keresési eredmények';
	//ha a keresési találatokat tartalmazó tömb létezik:
	if( isset($search_results) ){
		//ha $search_results tömbnek vannak elemei, akkor a keresés sikeres
		if( !empty($search_results) ){
			$test_instances = $search_results;
		}
		//egyébként nincs a keresésnek megfelelő elem
		else{
			$message = ' - nincsen a keresési feltételeidnek megfelelő feladatlap.';
			$test_instances = array();
		}
    }
    //ha nem létezik, akkor lekérjük az összes feladatlapot
	else{
        
        $test_instances = TestInstance::getAll(Session::get('user-id'), Session::get('user-type'));
	}
?>

<header class="content-header clear">
    <h2>Feladatlapok <?= !isset($search_results)?'':$message; //'keresési találatok' szöveg kiírása, ha aktív a keresés ?></h2>

    <?php if( IS_ADMIN ): //ha a user tanár, akkor gomb megjelenítése ?>
        <button class="btn-rounded bg-1 modal-opener" data-modal-id="create-test" style="float: right;">
            <i class="ion-plus-round"></i>Feladatlap létrehozása
        </button>
    <?php endif; ?>
    
</header>
<section class="content-body">
    <section class="tests-container">
        <table class="main-table panel">
            <thead>
                <td style="width: 20%;"><i class="ion-pricetag"></i></td>
                <td style="max-width: 30%"><i class="ion-document-text"></i></td>
                <td style="width: 10%;"><i class="ion-ios-people"></i></td>
                <td style="width: 10%;"><i class="ion-university"></i></td>
                <?php if( IS_ADMIN ): //szerző megjelenítése tanároknak ?>
                <td style="width: 10%;"><i class="ion-edit"></i></td>
                <?php endif; ?>
                <td style="width: 10%;"><i class="ion-calendar"></i></td>
                <td style="width: 10%;"><i class="ion-settings"></i></td>
            </thead>
        <?php
        //végigmegyünk a feladatlapokon, és megjelenítjük őket
        foreach($test_instances as $test_instance):
            $students = $test_instance->getStudents(); //diákok lekérése akik a teszt csoportjában vannak
            $test = Test::get($test_instance->test_id); //bázisfeladatlap lekérése
            $group = Group::get($test_instance->group_id); //csoport adatainak lekérése
            $subject = Subject::get($test->subject_id); //tantárgy adatainak lekérése
            
            $author_name = '';
            $is_original_author = false; //ez tárolja, hogy a belépett tanár-e a feladatlap szerzője
        
            //ha a az eredeti szerző id-je megegyezik a belépett tanár id-jével, akkor
            //az eredeti szerzőnek magunkat tűntejük fel, egyébként pedig az eredeti szerző nevét
            if( $test_instance->original_author_id == Session::get('user-id') ){
                $author_name = 'Saját';
                $is_original_author = true;
            } else {
                $user = User::get($test_instance->original_author_id);
                $author_name = $user->name;
            }
        ?>
            <tr class="<?= $test_instance->status == 1 ? 'is-opened' : '' ?>">
                <td>
                    <h4><?= $test->title  ?></h4>
                </td>
                
                <td>
                    <p style="width: <?= $is_admin?'300px':'450px' ?>"><?= !empty($test_instance->description) ? $test_instance->description : 'Nem érhető el leírás' ?></p>
                </td>
                
                <td>
                    <?= $group->name ?>
                </td>
                
                <td>
                    <?= $subject->name ?>
                </td>
                
                <?php if( IS_ADMIN ): //feladatlap szerzőjének megjelenítése, ha a user tanár ?>
                <td>
                    <?= $author_name ?>
                </td>
                <?php endif; ?>
                
                <td>
                    <?= explode(' ', $test_instance->creation_date)[0] ?>
                </td>
                
                <!-- lenyíló menü kezdete -->
                <td class="tool-cell">
                    <i class="ion-arrow-down-b open-test-options" data-test-id="<?= $test->id; ?>"></i>
                    <ul class="table-menu panel">

                    <?php if( IS_ADMIN ): //ha tanári fiók: ?>
                        <li>
                            <a href="evaluate?test_instance=<?= $test_instance->id ?>&user=<?= $students[0]->id ?>" target="_blank"><i class="ion-checkmark-circled"></i>Javítás</a>
                        </li>

                        <!-- a feladatlap nyitása/zárása egy űrlap, ami küldéskor elküldi a feladatlap id-jét
                        + azt 1-est, ha nyitni kell, 0-ást ha zárni -->
                        <form action="<?= SERVER_ROOT; ?>parsers/main-parser.php" method="POST" class="open-close-test-form">
                            <input type="hidden" name="test-instance-id" value="<?= $test_instance->id; ?>">
                        <?php if( $test_instance->status == 0 ): //ha zárva van a teszt: ?>
                            <input type="hidden" name="test-status" value="1">
                            <li class="btn-open-close-test">
                                <i class="ion-android-unlock"></i>Megnyitás
                            </li>
                        <?php elseif( $test_instance->status == 1 ): //ha nyitva van a teszt: ?>
                            <input type="hidden" name="test-status" value="0">
                            <li class="btn-open-close-test">
                                <i class="ion-android-lock"></i>Zárás
                            </li>
                        <?php endif; ?>
                        </form>

                        <?php if( $is_original_author ): //ha az eredeti szerzője a tesztnek, akkor megosztható ?>
                            <li class="modal-opener" data-modal-id="share-test" data-test-id="<?= $test->id; ?>">
                                <i class="ion-share"></i>Megosztás
                            </li>
                        <?php endif; ?>

                            <li>
                                <a href="view_test?test_instance=<?= $test_instance->id; ?>" target="_blank"><i class="ion-eye"></i>Megtekintés</a>
                            </li>
                            <li>
                                <a href="pdf_renderer?test_instance=<?= $test_instance->id; ?>" target="_blank"><i class="ion-printer"></i>Konvertálás PDF-be</a>
                            </li>
                    <?php endif; //IS_ADMIN if vége ?>

                    <?php if( !IS_ADMIN && $test_instance->status == 1 ): //ha a feladatlap nyiva van és a user diák ?>
                        <li>
                            <a href="test?test_instance=<?= $test_instance->id ?>" target="_blank"><i class="ion-eye"></i>Megoldás</a>
                        </li>
                    <?php elseif( !IS_ADMIN && $test_instance->status == 2 ): //ha a feladatlap már ki van javítva és a user diák ?>
                        <li>
                            <a href="check_test?test_instance=<?= $test_instance->id ?>" target="_blank"><i class="ion-eye"></i>Megtekintés</a>
                        </li>
                    <?php endif; ?>
                                
                    </ul>
                </td>
            </tr>
        <?php endforeach; ?>
        </table>
    </section>
</section>