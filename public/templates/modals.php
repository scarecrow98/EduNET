
<div class="page-overlay" style="display: none;">

    <?php if( IS_ADMIN ): ?>

    <!-- FELADATLAP LÉTREHOZÁSA -->
    <div class="modal" style="width: 700px; height: 600px; display: none;" id="create-test">
        <header>
            <h3>Feladatlap létrehozása</h3>
            <i class="ion-close-round close-modal"></i>
        </header>
        <section class="modal-body">

            <form action="<?= SERVER_ROOT; ?>parsers/main-parser.php" method="POST" id="create-test-form" novalidate>
                <li class="input-container">
                    <label for="test-title">Feladatlap címe: *</label>
                    <input type="text" placeholder="max. 100 karakter" name="test-title" id="test-title" maxlength="100">
                </li>
                <li class="input-container">
                    <label for="test-description">Feladatlap leírása:</label>
                    <textarea placeholder="max. 255 karakter" name="test-description" id="test-description" maxlength="255"></textarea>
                </li>
                <li class="input-container">
                    <label for="test-text">Feladatlap szövege:</label>
                    <textarea style="vertical-align: top;" placeholder="A feladatokhoz kötődő szöveg adható meg (pl.: szövegértési feladathoz)" name="test-text" id="test-text"></textarea>
             <li class="input-container">
                    <label for="test-subject">Tantárgy: *</label>
                    <select name="test-subject" id="test-subject">
                        <option value="">Válassz tantárgyat</option>
                        <?php
							//lekérem az összes csoportot objektumként,
							//majd végigiterálok az objektumokon foreach ciklussal
                            $subjects = Subject::all();
                            foreach($subjects as $subject):
                        ?>
                            <option value="<?= $subject->id ?>"><?= $subject->name ?></option>
                        <?php endforeach; //befejezem a foreach-t ?>
                    </select>
                </li>   </li>
                <li class="input-container">
                    <label for="test-group">Csoport: *</label>
                    <select name="test-group" id="test-group">
                        <option value="">Válassz csoportot</option>
                        <?php
                            $groups = Group::getAll(Session::get('user-id'), Session::get('user-type'));
                            foreach($groups as $group):
                        ?>
                        <option value="<?= $group->id ?>"><?= $group->name ?></option>
                        <?php endforeach; ?>
                    </select>
                </li>
                
                <li class="input-container">
                    <label for="test-tasknumber">Feladatok száma: *</label>
                    <input type="number" name="test-taskcount" id="test-taskcount" value="1" min="1" max="30">
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
            <form action="<?= SERVER_ROOT; ?>parsers/main-parser.php" method="POST" id="create-group-form" enctype="multipart/form-data" novalidate>
                <li class="input-container">
                    <label for="">Csoport neve: *</label>
                    <input type="text" placeholder="max. 50 karakter" name="group-name" id="group-name" maxlength="50">
                </li>
                <li class="input-container">
                    <label for="">Csoport leírása:</label>
                    <textarea placeholder="max. 255 karakter" name="group-description" id="group-description" maxlength="255"></textarea>
                </li>
                <li class="input-container">
                    <label for="">Csoport képe:</label>
                    <input type="file" name="group-avatar" id="group-avatar" style="display: none;">
                    <button class="btn-rounded bg-2 btn-open-file-dialog" data-input-id="group-avatar"><i class="ion-images"></i>Feltöltés</button>
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
            <form action="<?= SERVER_ROOT; ?>parsers/main-parser.php" name="add-group-member-form" id="add-group-member-form">
                <li class="input-container">
					<input type="text" name="student-name" id="student-name" placeholder="Diák neve">
                <li>
				<ul id="student-results">
                    <!-- ide töltődnek a találatok -->
                </ul>
            </form>
        </section>
    </div>

    <!-- FELADATLAP MEGOSZTÁSA -->
    <div class="modal" style="width: 630px; height: 360px; display: none;" id="share-test">
        <header>
            <h3>Feladatlap megosztása</h3>
            <i class="ion-close-round close-modal"></i>
        </header>
        <section class="modal-body">
            <form method="POST" action="parsers/main-parser.php" name="share-test-form" id="share-test-form">
                <li class="input-container">
                    <label for="new-test-group">Feladatlap csoportja: *</label>
                    <select name="new-test-group" id="new-test-group">
                        <option value="0">Válassz csoportot</option>
                        <?php
                            $groups = Group::all();
                            foreach($groups as $group):
								$teacher = User::get($group->author_id);
                        ?>
                        <option value="<?= $group->id; ?>"><?= $group->name.' ('.($teacher->id == Session::get('user-id') ? 'saját' : $teacher->name).')' ?></option>
                        <?php endforeach; ?>
                    </select>
                </li>
                <li class="input-container">
                    <label for="">Feladatlap leírása:</label>
                    <textarea name="new-test-description" id="new-test-description" placeholder="max. 255 karakter" maxlength="255"></textarea>
                </li>
                <li class="input-container">
                    <input type="submit" class="btn-wide bg-1" name="share-test" value="Feladatlap megosztása">
                </li>
            </form>
        </section>
    </div>

	<!-- ÉRTESÍTÉS LÉTREHOZÁSA -->
    <div class="modal" style="width: 600px; height: 500px; display: none;" id="create-notification" >
        <header>
            <h3>Értesítések</h3>
            <i class="ion-close-round close-modal"></i>
        </header>

        <div class="section-switcher">
            <button class="active-section" data-section-id="new-notification" onclick="$('#new-notification').show(); $('#list-notifications').hide();">Új értesítés</button>
            <button date-section-id="list-notifications" onclick="$('#list-notifications').show(); $('#new-notification').hide();">Eddigi értesítéseim</button>
        </div>

        <section class="modal-body" id="new-notification" style="height: calc(100% - 125px)">
            <form action="<?= SERVER_ROOT; ?>parsers/main-parser.php" id="create-notification-form" novalidate>
                <li class="input-container">
                    <label for="nt-text">Értesítés szövege: *</label>
                    <input type="text" id="nt-text" placeholder="max. 100 karakter" >
                </li>
                <li class="input-container">
                    <label for="nt-group-id">Értesítés csoportja: *</label>
                    <select id="nt-group-id" required>
                        <option value="">Válassz csoportot</option>
                        <?php
                            $groups = Group::getAll(Session::get('user-id'), Session::get('user-type'));
                            foreach($groups as $group):
                        ?>
                        <option value="<?= $group->id ?>"><?= $group->name ?></option>
                        <?php endforeach; ?>
                    </select>
                </li>
                <li class="input-container">
                    <label for="nt-subject-id">Dolgozat tantárgya: *</label>
                    <select id="nt-subject-id" required>
                        <option value="">Válassz tantárgyat</option>
                        <?php
                            $subjects = Subject::all();
                            foreach($subjects as $subject):
                        ?>
                            <option value="<?= $subject->id ?>"><?= $subject->name ?></option>
                        <?php endforeach; ?>
                    </select>
                </li>
                <li class="input-container">
                    <label for="nt-date">Dolgozat időpontja: *</label>
                    <input type="date" id="nt-date">
                </li>
                <li class="input-container">
                    <label for="nt-type">Dolgozat szint: *</label>
                    <select id="nt-type">
                        <option value="">Válassz szintet</option>
                            <option value="1">Szóbeli felelet</option>
                            <option value="2">Dolgozat</option>
                            <option value="3">Témazáró dolgozat</option>
                            <option value="4">Egyéb esmemény</option>
                    </select>
                </li>
                <li class="input-container">
                    <input type="submit" value="Értesítés létrehozása" class="btn-wide bg-3">
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

                $type = '';
                switch( $nt->type ){
                    case 1: $type = 'Szóbeli felelet';
                        break;
                    case 2: $type = 'Dolgozat';
                        break;
                    case 3: $type = 'Témazáró dolgozat';
                        break;
                    case 4: $type = 'Egyéb esemény';
                        break;
                }

                $split = explode('-', $nt->date);
                $month = (int)$split[1];
                $day = (int)$split[2];
            ?>
            <li class="notification-list-item">
                <div class="notification-date">
                    <p class="notification-date-day"><?= $day ?>.</p>
                    <p class="notification-date-month"><?= $months[$month] ?></p>
                </div>
                <div class="notification-body">
                    <i class="ion-close-round btn-delete-notification" style="padding: 4px; cursor: pointer;" data-notification-id="<?=  $nt->id ?>"></i>
                    <strong><?= $nt->title ?></strong>
                    <p><?= $type.' - '.$subject->name ?></p>
                    <p><?= $group->name ?></p>
                </div>
            </li>
            <?php endforeach; ?>
        </section>
    </div>
    
    <!-- ÜZENET LÉTREHOZÁSA -->
    <div class="modal" style="width: 500px; height: 400px; display: none;" id="create-message">
        <header>
            <h3>Üzenet küldése</h3>
            <i class="ion-close-round close-modal"></i>
        </header>
        <section class="modal-body">
            <form action="<?=  SERVER_ROOT; ?>parsers/main-parser.php" method="POST" id="create-message-form" novalidate>
                <li class="input-container">
                    <label for="message-partner">Üzenet címzettje: *</label>
                    <select name="message-receiver" id="message-receiver">
                        <option value="">Válassz címzettet *</option>
                        <?php
                            $teachers = User::getByType(1);
                            foreach($teachers as $teacher):
                                if( $teacher->id == Session::get('user-id') ) continue;
                        ?>
                        <option value="<?= $teacher->id; ?>"><?= $teacher->name; ?></option>
                        <?php endforeach; ?>             
                    </select>
                </li>
                <li class="input-container">
                    <textarea name="message-text" id="message-text" placeholder="Üzenet szövege..." style="height: 140px;"></textarea>
                </li>
                <li class="input-container">
                    <input type="submit" value="Üzenet küldése" class="btn-wide bg-1">
                </li>
            </form>
        </section>
    </div>

    <!-- CHATABLAK -->
    <div class="modal" style="width: 500px; height: 570px; display: none;" id="read-message">
        <header>
            <h3>Beszélgetés</h3>
            <i class="ion-close-round close-modal"></i>
        </header>
        <section class="modal-body" id="conversation" style="height: calc(100% - 170px);">
        </section>
        <div id="message-controls">
            <textarea placeholder="Üzeneted helye..." id="message"></textarea>
            <button id="btn-send-message">Küldés</button>
        </div>
    </div>
    <?php endif; ?>

    <!-- BEÁLLÍTÁSOK -->
    <div class="modal" style="width: 600px; height: 520px; display: none;" id="user-settings">
        <header>
            <h3>Beállítások</h3>
            <i class="ion-close-round close-modal"></i>
        </header>
        <section class="modal-body">
            <form action="<?= SERVER_ROOT ?>parsers/main-parser.php" method="POST" enctype="multipart/form-data" id="user-settings-form">
                <li class="input-container">
                    <label for="select-profile-pic">Új profilkép:</label>
                    <button class="btn-rounded bg-2 btn-open-file-dialog" data-input-id="new-avatar"><i class="ion-upload"></i>Feltöltés</button>
                    <span class="uploaded-file-name"></span>
                    <input type="file" name="new-avatar" id="new-avatar" style="display: none;">                    
                </li>
                <li class="input-container" style="border-top: 1px solid var(--theme-grey);">
                    <label for="new-password1">Új jelszó:</label>
                    <input type="password" name="new-password1" id="new-password1" placeholder="Új jelszó">
                </li>
                <li class="input-container">
                    <label for="new-password2">Jelsző megerősítése:</label>
                    <input type="password" name="new-password2" id="new-password2" placeholder="Új jelszó megerősítése">
                </li>
                <li class="input-container" style="border-top: 1px solid var(--theme-grey);">
                    <label for="new-email">Email megváltoztatása:</label>
                    <input type="email" name="new-email" id="new-email" placeholder="<?= Session::get('user-email') ?>">
                </li>
                <li class="input-container">
                    <label for="new-email-subscription">Email értesítések:</label>
                    <div>
                        <input type="checkbox" name="new-email-subscription" id="new-email-subscription" <?= Session::get('user-subscription')?'checked':'' ?>>
                        <span id="email-subscription-status" style="font-size: 14px;">Értesítések <?= Session::get('user-subscription')?' bekapcsolva':' kikapcsolva' ?></span>
                    </div>
                </li>
                <li class="input-container">
                    <button id="btn-reset-settings" class="btn-rounded bg-2" style="margin-right: 6px;">Mégse</button>
                    <button id="btn-save-settings" class="btn-rounded bg-2">Beállítások mentése</button>
                </li>
            </form>
        </section>
    </div>

    <!-- CSOPORTTAGOK MODAL -->
    <div class="modal <?= IS_ADMIN?'teacher-true':'' ?>" style="width: 500px; height: 400px; display: none;" id="view-members">
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