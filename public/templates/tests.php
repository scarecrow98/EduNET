<?php
	$message= ' - keresési eredmények';
	//ha vannak keresési találatok, akkor azokat jelenítjük meg
	//ha nincsenek, akkor az összes feladatlapot
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
	else{
        
        $test_instances = TestInstance::getAll(Session::get('user-id'), Session::get('user-type'));
	}
?>

<header class="content-header clear">
    <h2>Feladatlapok <?= !isset($search_results)?'':$message; ?></h2>

    <?php if( IS_ADMIN ): ?>
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
                <?php if( IS_ADMIN ): ?>
                <td style="width: 10%;"><i class="ion-edit"></i></td>
                <?php endif; ?>
                <td style="width: 10%;"><i class="ion-calendar"></i></td>
                <td style="width: 10%;"><i class="ion-settings"></i></td>
            </thead>
        <?php
            foreach($test_instances as $test_instance):
                $students = $test_instance->getStudents();
                $test = Test::get($test_instance->test_id);
				$group = Group::get($test_instance->group_id);
                $subject = Subject::get($test->subject_id);
                
                $author_name = '';
                $is_original_author = false;
                if( $test_instance->original_author_id == Session::get('user-id') ){
                    $author_name = 'Saját';
                    $is_original_author = true;
                } else {
                    $user = User::get($test_instance->original_author_id);
                    $author_name = $user->name;
                }
        ?>
            <tr>
                <td><h4><?= $test->title;  ?></h4></td>
                <td><p style="width: <?= $is_admin?'300px':'450px' ?>"><?= !empty($test->description)?$test->description:'Nem érhető el leírás' ?></p></td>
                <td><?= $group->name ?></td>
                <td><?= $subject->name ?></td>
                <?php if( IS_ADMIN ): ?>
                <td><?= $author_name ?></td>
                <?php endif; ?>
                <td><?= explode(' ', $test_instance->creation_date)[0]; ?></td>
                <td class="tool-cell">
                    <i class="ion-arrow-down-b open-test-options" data-test-id="<?= $test->id; ?>"></i>
                    <ul class="table-menu panel">

                        <?php if( IS_ADMIN ): ?>
                            <?php if( $test_instance->status != 2 ): ?>
                                <li>
                                    <a href="evaluate.php?test_instance=<?= $test_instance->id ?>&user=<?= $students[0]->id ?>" target="_blank"><i class="ion-checkmark-circled"></i>Javítás</a>
                                </li>
                            <?php endif; ?>
                            <form action="<?= SERVER_ROOT; ?>parsers/main-parser.php" method="POST" class="open-close-test-form">
                                <input type="hidden" name="test-instance-id" value="<?= $test_instance->id; ?>">
                            <?php if( $test_instance->status == 0 ): ?>
                                <input type="hidden" name="test-status" value="1">
                                <li class="btn-open-close-test">
                                    <i class="ion-android-unlock"></i>Megnyitás
                                </li>
                            <?php else: ?>
                                <input type="hidden" name="test-status" value="0">
                                <li class="btn-open-close-test">
                                    <i class="ion-android-lock"></i>Zárás
                                </li>
                            <?php endif; ?>
                            </form>
                            <?php if( $is_original_author ): ?>
                                <li class="modal-opener" data-modal-id="share-test" data-test-id="<?= $test->id; ?>">
                                    <i class="ion-share"></i>Megosztás
                                </li>
                            <?php endif; ?>
                                <li>
                                    <a href="view_test?test_instance=<?= $test_instance->id; ?>" target="_blank"><i class="ion-eye"></i>Megtekintés</a>
                                </li>
                                <li>
                                    <a href="pdf-renderer.php?test_instance=<?= $test_instance->id; ?>" target="_blank"><i class="ion-printer"></i>Konvertálás PDF-be</a>
                                </li>
                        <?php endif; ?>

                        <?php if( !IS_ADMIN && $test_instance->status == 1 ): ?>
                            <li>
                                <a href="test.php?test_instance=<?= $test_instance->id ?>" target="_blank"><i class="ion-eye"></i>Megoldás</a>
                            </li>
                        <?php endif; ?>

                    </ul>
                </td>
            </tr>
        <?php endforeach; ?>
        </table>
    </section>
</section>