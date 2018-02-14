<section id="side-menu">
    <div id="logo-container">
        <img src="<?php echo PUBLIC_ROOT ?>/resources/images/edunet-logo-white.png" alt="">
    </div>
    <div id="profile-container">
        <div id="profile-picture" style="background-image: url(<?php echo SERVER_ROOT.'uploads/avatars/'.Session::get('user-avatar');?>)">
            <div class="overlay"></div>
        </div>
        <span><?php echo Session::get('user-name'); ?></span>
        <small>
            <?php echo IS_ADMIN?'mint tanár':'mint diák';?>
        </small>
    </div>

    <ul id="main-menu">
        <li <?php if(!isset($_GET['page']) || $_GET['page'] == 'home'){ echo 'class="active-item"'; } ?>>
            <a href="home">
                <span class="ion-android-notifications">Események</span>
            </a>
        </li>
		<li <?php if(isset($_GET['page']) && $_GET['page'] == 'tests'){ echo 'class="active-item"'; } ?>>
            <a href="tests">
                <span class="ion-document-text">Feladatlapok</span>
            </a>
        </li>
        <li <?php if(isset($_GET['page']) && $_GET['page'] == 'groups' || isset($_GET['page']) && $_GET['page'] == 'create-group'){ echo 'class="active-item"'; } ?>>
            <a href="groups">
                <span class="ion-person-stalker">Csoportok</span>
            </a>
        </li>
        <li <?php if(isset($_GET['page']) && $_GET['page'] == 'stats'){ echo 'class="active-item"'; } ?>>
            <a href="stats">
                <span class="ion-arrow-graph-up-right">Statisztikák</span>
            </a>
        </li>
    </ul>
</section>