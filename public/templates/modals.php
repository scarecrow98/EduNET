
<input type="hidden" id="security-token" value="<?= $token ?>">
<div class="page-overlay" style="display: none;">

    <?php if( IS_ADMIN ): ?>

    <!-- FELADATLAP LÉTREHOZÁSA -->
    <div class="modal" style="width: 700px; height: 600px; display: none;" id="create-test">
        <header>
            <h3>Feladatlap létrehozása</h3>
            <i class="ion-close-round close-modal"></i>
        </header>
        <section class="modal-body">

            <form action="<?php echo SERVER_ROOT; ?>parsers/main-parser.php" method="POST" id="create-test-form">
                <li class="input-container">
                    <label for="test-title">Feladatlap címe:</label>
                    <input type="text" placeholder="A feladatlap rövid neve" name="test-title" id="test-title" required>
                </li>
                <li class="input-container">
                    <label for="test-description">Feladatlap leírása:</label>
                    <textarea placeholder="Kiegészítő információ a feladatlaphoz" name="test-description" id="test-description"></textarea>
                </li>
                <li class="input-container">
                    <label for="test-text">Feladatlap szövege:</label>
                    <textarea style="vertical-align: top;" placeholder="A feladatokhoz kötődő szöveg adható meg (pl.: szövegértési feladathoz)" name="test-text" id="test-text"></textarea>
                </li>
                <li class="input-container">
                    <label for="test-group">Csoport:</label>
                    <select name="test-group" id="test-group" required>
                        <option value="">Válassz csoportot</option>
                        <?php
                            $groups = Group::getAll(Session::get('user-id'), Session::get('user-type'));
                            foreach($groups as $group):
                        ?>
                        <option value="<?php echo $group->id ?>"><?php echo $group->name ?></option>
                        <?php endforeach; ?>
                    </select>
                </li>
                <li class="input-container">
                    <label for="test-subject">Tantárgy:</label>
                    <select name="test-subject" id="test-subject" required>
                        <option value="">Válassz tantárgyat</option>
                        <?php
                            $subjects = Subject::all();
                            foreach($subjects as $subject):
                        ?>
                            <option value="<?php echo $subject->id ?>"><?php echo $subject->name ?></option>
                        <?php endforeach; ?>
                    </select>
                </li>
                <li class="input-container">
                    <label for="test-tasknumber">Feladatok száma:</label>
                    <input type="number" name="test-taskcount" id="test-taskcount" placeholder="Ennyi feladatot fog tartalmazni a feladatlap" value="1" min="1" max="30">
                </li>
                <li class="input-container">
                    <input type="submit" value="Feladatlap létrehozása" id="create-test" name="create-test" class="btn-wide bg-1">
                </li>
            </form>

        </section>
    </div>

    <!-- CSOPORT LÉTREHOZÁSA -->
    <div class="modal" style="width: 650px; height: 430px; display: none;" id="create-group">
        <header>
            <h3>Csoport létrehozása</h3>
            <i class="ion-close-round close-modal"></i>
        </header>
        <section class="modal-body">
            <form action="<?php echo SERVER_ROOT; ?>parsers/main-parser.php" method="POST" id="create-group-form" enctype="multipart/form-data">
                <li class="input-container">
                    <label for="">Csoport neve:</label>
                    <input type="text" placeholder="A csoport nevét adhatod meg (pl.: 9B - Matek)" name="group-name" id="group-name" required>
                </li>
                <li class="input-container">
                    <label for="">Csoport leírása:</label>
                    <textarea placeholder="A csoport leírását adhatod meg" name="group-description" id="group-description"></textarea>
                </li>
                <li class="input-container">
                    <label for="">Kép beállítása:</label>
                    <input type="file" name="group-avatar" id="group-avatar" style="display: none;">
                    <button class="btn-rounded bg-2" id="select-group-avatar"><i class="ion-images"></i>Kép feltöltése</button>
                    <span class="uploaded-file-name">&nbsp;</span>
                </li>
                <li class="input-container">
                    <input type="submit" value="Csoport létrehozása" id="create-group" name="create-group" class="btn-wide bg-2">
                </li>
            </form>
        </section>
    </div>

    <!-- CSOPORTTAGOK FELVÉTELE -->
    <div class="modal" style="width: 500px; height: 400px; display: none;" id="add-members">
        <header>
            <h3>Csoport kezelése</h3>
            <i class="ion-close-round close-modal"></i>
        </header>
        <section class="modal-body" id="adding-members">
            <form action="<?php echo SERVER_ROOT; ?>parsers/main-parser.php" name="add-group-member-form" id="add-group-member-form">
                <li class="input-container">
					<input type="text" name="student-name" id="student-name" placeholder="Diák neve">
                <li>
				<ul class="student-results">
                    <!-- ide töltődnek a találatok -->
                </ul>
            </form>
        </section>
    </div>

    <!-- FELADATLAP MEGOSZTÁSA -->
    <div class="modal" style="width: 500px; height: 300px; display: none;" id="share-test">
        <header>
            <h3>Feladatlap megosztása</h3>
            <i class="ion-close-round close-modal"></i>
        </header>
        <section class="modal-body">
            <form method="POST" action="parsers/main-parser.php" name="share-test-form" id="share-test-form">
                <li class="input-container">
                    <label for="new-test-author">Akivel megosztod:</label>
                    <select name="new-test-author" id="new-test-author">
                        <option value="0">Válassz tanárt:</option>
                        <?php
                            $teachers = User::getByType(1);
                            foreach($teachers as $teacher):
                        ?>
                        <option value="<?php echo $teacher->id; ?>"><?php echo $teacher->name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </li>
                <li class="input-container">
                    <label for="new-test-group">Ahová megosztod:</label>
                    <select name="new-test-group" id="new-test-group">
                        <option value="0">Válassz csoportot</option>
                        <?php
                            $groups = Group::all();
                            foreach($groups as $group):
								$admin = User::get($group->author_id);
                        ?>
                        <option value="<?php echo $group->id; ?>"><?php echo $group->name.' ('.$admin->name.')' ?></option>
                        <?php endforeach; ?>
                    </select>
                </li>
                <li class="input-container">
                    <input type="submit" class="btn-wide bg-1" name="share-test" value="Feladatlap megosztása">
                </li>
            </form>
        </section>
    </div>

	<!-- ÉRTESÍTÉS LÉTREHOZÁSA -->
    <div class="modal" style="width: 500px; height: 500px; display: none;" id="create-notification" >
        <header>
            <h3>Értesítések</h3>
            <i class="ion-close-round close-modal"></i>
        </header>

        <div class="section-switcher">
            <button class="active-section" data-section-id="new-notification" onclick="$('#new-notification').show(); $('#list-notifications').hide();">Új értesítés</button>
            <button date-section-id="list-notifications" onclick="$('#list-notifications').show(); $('#new-notification').hide();">Eddigi értesítéseim</button>
        </div>

        <section class="modal-body" id="new-notification" style="height: calc(100% - 125px)">
            <form action="<?php echo SERVER_ROOT; ?>parsers/main-parser.php" id="create-notification-form">
                <li class="input-container">
                    <label for="nt-text">Értesítés szövege:</label>
                    <input type="text" id="nt-text" required>
                </li>
                <li class="input-container">
                    <label for="nt-group-id">Értesítés csoportja:</label>
                    <select id="nt-group-id" required>
                        <option value="">Válassz csoportot</option>
                        <?php
                            $groups = Group::getAll(Session::get('user-id'), Session::get('user-type'));
                            foreach($groups as $group):
                        ?>
                        <option value="<?php echo $group->id ?>"><?php echo $group->name ?></option>
                        <?php endforeach; ?>
                    </select>
                </li>
                <li class="input-container">
                    <label for="nt-subject-id">Dolgozat tantárgya:</label>
                    <select id="nt-subject-id" required>
                        <option value="">Válassz tantárgyat</option>
                        <?php
                            $subjects = Subject::all();
                            foreach($subjects as $subject):
                        ?>
                            <option value="<?php echo $subject->id ?>"><?php echo $subject->name ?></option>
                        <?php endforeach; ?>
                    </select>
                </li>
                <li class="input-container">
                    <label for="nt-date">Dolgozat időpontja:</label>
                    <input type="date" id="nt-date" required>
                </li>
                <li class="input-container">
                    <label for="nt-type">Dolgozat szint:</label>
                    <select id="nt-type" required>
                        <option value="">Válassz szintet</option>
                            <option value="1">Röpdolgozat</option>
                            <option value="2">Nagydolgozat</option>
                            <option value="3">Témazáró</option>
                    </select>
                </li>
                <li class="input-container">
                    <input type="submit" value="Értesítés létrehozása" class="btn-wide bg-1">
                </li>
            </form>
        </section>
        <section class="modal-body" style="display: none; height: calc( 100% - 125px )" id="list-notifications">
            <?php
                $notifications = Notification::getAll(Session::get('user-id'), Session::get('user-type'));
                //print_r($nts);
                foreach( $notifications as $nt ):
				$subject = Subject::get($nt->subject_id);
				$group = Group::get($nt->group_id);
            ?>
            <li class="notification-list-item">
                <h4><?php echo $nt->title ?></h4>
                <span><?php echo $group->name.' - '.$subject->name?></span>
                <time><?php echo $nt->date ?></time>
                <i class="ion-close-round btn-delete-notification" style="padding: 4px; cursor: pointer;" data-notification-id="<?php echo $nt->id ?>"></i>
            </li>
            <?php endforeach; ?>
        </section>
    </div>
    <?php endif; ?>

    <!-- ÜZENET KÜLDÉSE -->
    <div class="modal" style="width: 500px; height: 400px; display: none;" id="create-message">
        <header>
            <h3>Üzenet küldése</h3>
            <i class="ion-close-round close-modal"></i>
        </header>
        <section class="modal-body">
            <form action="<?php echo SERVER_ROOT; ?>parsers/main-parser.php" method="POST" id="create-message-form">
                <li class="input-container">
                    <label for="message-partner">Üzenet címzettje:</label>
                    <select name="message-receiver" id="message-receiver" required>
                        <option value="">Válassz címzettet</option>
                        <?php
                            $teachers = User::getByType(1);
                            foreach($teachers as $teacher):
                                if( $teacher->id == Session::get('user-id') ) continue;
                        ?>
                        <option value="<?php echo $teacher->id; ?>"><?php echo $teacher->name; ?> (<?php echo $teacher->is_online==0?'Nem elérhető':'Elérhető' ?>)</option>
                        <?php endforeach; ?>             
                    </select>
                </li>
                <li class="input-container">
                    <textarea name="message-text" id="message-text" placeholder="Üzenet szövege..." style="height: 140px;" required></textarea>
                </li>
                <li class="input-container">
                    <input type="submit" value="Üzenet küldése" class="btn-wide bg-1">
                </li>
            </form>
        </section>
    </div>

    <!-- MEGNYITOTT ÜZENET -->
    <div class="modal" style="width: 500px; height: 400px; display: none;" id="read-message">
        <header>
            <h3>Üzenet megtekintése</h3>
            <i class="ion-close-round close-modal"></i>
        </header>
        <section class="modal-body">
            <h4></h4>
            <small></small>
            <p></p>
        </section>
    </div>

    <!-- BEÁLLÍTÁSOK -->
    <div class="modal" style="width: 600px; height: 400px; display: none;" id="user-settings">
        <header>
            <h3>Beállítások</h3>
            <i class="ion-close-round close-modal"></i>
        </header>
        <section class="modal-body">
            <form action="parsers/main-parser.php" method="POST" enctype="multipart/form-data" id="user-settings-form">
                <li class="input-container">
                    <label for="select-profile-pic">Új profilkép:</label>
                    <button class="btn-rounded bg-2" id="select-profile-pic"><i class="ion-upload"></i>Profilkép kiválasztása</button>
                    <span class="uploaded-file-name"></span>
                    <input type="file" name="new-avatar" id="new-avatar" style="display: none;">                    
                </li>
                <li class="input-container">
                    <label for="new-password1">Új jelszó:</label>
                    <input type="password" name="new-password1" id="new-password1" placeholder="Új jelszó">
                </li>
                <li class="input-container">
                    <label for="new-password2">Jelsző megerősítése:</label>
                    <input type="password" name="new-password2" id="new-password2" placeholder="Új jelszó megerősítése">
                </li>
                <li class="input-container">
                    <label for="new-email">Email megváltoztatása:</label>
                    <input type="email" name="new-email" id="new-email" placeholder="Új email cím">
                </li>
                <li class="input-container">
                    <button id="btn-reset-settings" class="btn-rounded bg-2" style="margin-right: 6px;">Mégse</button>
                    <button id="btn-save-settings" class="btn-rounded bg-2">Beállítások mentése</button>
                </li>
            </form>
        </section>
    </div>

    <!-- CSOPORTTAGOK MODAL -->
    <div class="modal" style="width: 500px; height: 400px; display: none;" id="view-members">
        <header>
            <h3>Csoporttagok</h3>
            <i class="ion-close-round close-modal"></i>
        </header>
        <section class="modal-body">
            <div id="group-members">
                <!-- csoport tagjainak lilstája -->
            </div>
        </section>
    </div>

</div>