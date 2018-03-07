<?php

if( isset($_POST['tst-view']) ){
    $data = array(
        'title'         => empty($_POST['tst-title'])?null:$_POST['tst-title'],
        'group_id'      => empty($_POST['tst-group'])?null:$_POST['tst-group'],
        'subject_id'    => empty($_POST['tst-subject'])?null:$_POST['tst-subject'],
        'date'          => empty($_POST['tst-date'])?null:$_POST['tst-date'],
        'user_id'       => Session::get('user-id')
    );
    
    $search_results = TestInstance::filter($data, Session::get('user-type'));
}

?>
<form action="" method="POST" name="search-test-form" id="search-test-form">
    <input type="hidden" name="tst-view" value="1">
    <li class="input-container">
        <label for="">Csoport:</label>
        <select name="tst-group" class="search-form-input">
            <option value="0">válassz csoportot</option>
            <?php
			$groups = Group::getAll(Session::get('user-id'), Session::get('user-type'));
            foreach($groups as $group):
                ?>
                <option value="<?= $group->id ?>"><?= $group->name ?></option>
            <?php endforeach; ?>
        </select>
    </li>
    <li class="input-container">
        <label for="">Tantárgy:</label>
        <select name="tst-subject" class="search-form-input">
            <option value="0">válassz tantárgyat</option>
            <?php
            $subjects = Subject::all();
            foreach($subjects as $subject):
                ?>
                <option value="<?= $subject->id ?>"><?= $subject->name ?></option>
            <?php endforeach; ?>
        </select>
    </li>
    <li class="input-container">
        <label for="">Dátum:</label>
        <input type="date" name="tst-date" class="search-form-input">
    </li>
    <li class="input-container">
        <label for="">Cím:</label>
        <input type="text" name="tst-title" class="search-form-input" placeholder="cím egy része...">
    </li>
    <li class="input-container">
        <input type="submit" value="keresés" class="btn-rounded bg-1">
    </li>
</form>
