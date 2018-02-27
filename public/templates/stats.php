<?php
/*
    Csoport szerinti statisztika
        Csoporttagok száma
        legjobban sikerült dolgozat
        legrosszabbul sikerült dolgozat
        általános átlag



    Feladatlap szerinti statisztika
        Hányan írták meg
        Legjobban átlagú feladat
        Legjobban sikerült
        Legrosszabbul sikerült
        Átlag

    $data = array(
        'average_results'       => 15.6/23,
        'total_submissions'     => 21,
        'best_result'           => 23/23, 
        'worst_result'          => 3/23,
        'avreage_task_results'  => array(
            1   => 3.5/4,
            2   => 1/2,
            3   => 4.6/8,
            4   => 4/9
        )
    );


    

    Általános
        legjobb átlagú csoport
        legjobb átlagú feladatlap
        legjobb átlagú diák
    Rendszer
        össz. feladatlapok száma
        össz. feladatok száma
        össz. diák száma
        össz. tanár száma
        össz. csoportok száma
*/ 

?>


<header class="content-header">
    <h2>Statisztikák</h2>
</header>
<section class="content-body">
    <canvas width="1000" height="300" id="canvas1" style="margin: 0px auto; display: block;"></canvas>
    <canvas width="700" height="700" id="canvas2"></canvas>

    <input type="hidden" class="data-holder" value="32">
    <input type="hidden" class="data-holder" value="24">
    <input type="hidden" class="data-holder" value="87">
    <input type="hidden" class="data-holder" value="53">
    <input type="hidden" class="data-holder" value="98">


    <div class="home-box panel">
        <header>
            <h4 class="ion-arrow-graph-up-right">Általános statisztika</h4>
            <small>Statisztikákat láthatsz mely a rád vonatkozó, mindenkori adatokból lett kialakítva</small>
        </header>
    </div>

    <div class="home-box panel">
        <header>
            <h4 class="ion-ios-pie-outline">Rendszerstatisztika</h4>
            <small>Statisztika a rendszerből összegyűjtött minden adatból</small>
        </header>
    </div>

    <div class="home-box panel">
        <header>
            <h4 class="ion-ios-people">Csoport szerinti statisztika</h4>
            <small>Részletes statisztikát láthatsz a kiválasztott osztályodról</small>
            <select name="" id="">
                <option value="">Válassz csoportot</option>
                <?php
                    $groups = Group::getAll(Session::get('user-id'), Session::get('user-type'));
                    foreach( $groups as $group ):
                ?>
                <option value="<?= $group->id ?>"><?= $group->name ?></option>
                <?php endforeach; ?>
            </select>
        </header>
    </div>

    <div class="home-box panel">
        <header>
            <h4 class="ion-document-text">Feladatlap szerinti statisztika</h4>
            <small>Részletes statisztikát láthatsz a kiválasztott feladatlapról</small>
            <select name="" id="">
                <option value="">Válassz feladatlapot</option>
                <?php
                    $test_instances = TestInstance::getAll(Session::get('user-id'), Session::get('user-type'));
                    foreach( $test_instances as $test_instance ):
                    $test = Test::get($test_instance->test_id);
                    $group = Group::get($test_instance->group_id);
                ?>
                <option value="<?= $test_instance->id ?>"><?= $test->title ?> (<?= $group->name ?>)</option>
                <?php endforeach; ?>
            </select>
        </header>
    </div>
    
</section>