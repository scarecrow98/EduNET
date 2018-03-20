<?php
    //üzenet előnézetek lekérése
    $messages = Message::getPreviews(Session::get('user-id'));

    //megnézzük, hogy van-e olvasatlan üzenet
    $has_new = false;
    foreach( $messages as $msg ){
        //ha az üzenet még olvasatlan és mi vagyunk a fogadók, akkor a $has_new = true
        if( $msg->is_seen == 0 && $msg->sender_id != Session::get('user-id') ){
            $has_new = true;
            break;
        }
    }
?>


<section id="top-bar" class="clear" style="position: relative;">

    <!-- keresés mező -->
	<?php if( isset($_GET['page']) && $_GET['page'] == 'tests' ): ?>
    <button id="btn-show-search" >
        <i class="ion-ios-search-strong"></i>
    </button>

    <div id="search-form-container" style="display: none;">
        <?php require_once 'public/templates/search-form.php'; ?>
    </div>
    <?php endif; ?><!-- keresés mező vége -->
    

    <!-- jobb oldali gombok -->
    <div id="top-bar-buttons-right">
        <!-- profil gomb -->
        <button id="btn-settings" data-modal-id="user-settings" class="modal-opener">
            <img src="<?= SERVER_ROOT ?>/uploads/avatars/<?= Session::get('user-avatar') ?>">
            <strong>Profil</strong>
        </button>

        <!-- üzentek gomb és panel -->
        <?php if( IS_ADMIN ): //üzenetek panelt csak tanároknak jelenítjük meg ?>
        <button id="btn-messages" class="<?= $has_new ? 'has-new-message' : '' ?>" >
            <i class="ion-ios-chatboxes-outline"></i>
        </button>

        <div class="messages-popup panel" style="display: none;">
            <section>
            <?php 
                //beszélgetés előnézetek listázása
                foreach( $messages as $message ){
                    echo UIDrawer::messageItem($message);
                }
            ?>
            </section>
            <button id="create-new-message" class="modal-opener" data-modal-id="create-message">Új üzenet írása</button>
        </div>
        <?php endif; ?>

        <!-- kijelentkezés gomb -->
        <a href="logout">
            <button id="btn-logout">
                <i class="ion-log-out"></i>
            </button>
        </a>
    </div> <!-- jobb oldali gombok vége -->
</section>