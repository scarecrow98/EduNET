<?php
//keresés feldolgozása
if( isset($_GET['search']) ){
    $data = array(
        'title'         => empty($_GET['title'])    ? null : $_GET['title'],
        'group_id'      => empty($_GET['group'])    ? null : $_GET['group'],
        'subject_id'    => empty($_GET['subject'])  ? null : $_GET['subject'],
        'date'          => empty($_GET['date'])     ? null : $_GET['date'],
        'user_id'       => Session::get('user-id')
    );
    
    $search_results = TestInstance::filter($data, Session::get('user-type'));
}

?>
<form action="" method="GET" name="search-test-form" id="search-test-form">
    <input type="hidden" name="search" value="true">
    <li class="input-container">
        <label for="">Csoport:</label>
        <select name="group" class="search-form-input">
            <option value="0">válassz csoportot</option>
            <?php
            //felhasználó csoportjainak listázása
			$groups = Group::getAll(Session::get('user-id'), Session::get('user-type'));
            foreach($groups as $group):
            ?>
                <option value="<?= $group->id ?>"><?= $group->name ?></option>
            <?php endforeach; ?>
        </select>
    </li>
    <li class="input-container">
        <label for="">Tantárgy:</label>
        <select name="subject" class="search-form-input">
            <option value="0">válassz tantárgyat</option>
            <?php
            //tantárgyak listázása
            $subjects = Subject::all();
            foreach($subjects as $subject):
                ?>
                <option value="<?= $subject->id ?>"><?= $subject->name ?></option>
            <?php endforeach; ?>
        </select>
    </li>
    <li class="input-container">
        <label for="">Dátum:</label>
        <input type="date" name="date" class="search-form-input">
    </li>
    <li class="input-container">
        <label for="">Cím:</label>
        <input type="text" name="title" class="search-form-input" placeholder="cím egy része...">
    </li>
    <li class="input-container">
        <input type="submit" value="keresés" class="btn-rounded bg-1">
    </li>
</form>
