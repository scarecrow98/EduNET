<?php
    //üzenetek lekérése
    $messages = Message::getPreviews(Session::get('user-id'));

    //megnézzük, hogy van-e olvasatlan üzenet
    $has_new = false;
    foreach( $messages as $msg ){
        if( $msg->is_seen == 0 && $msg->sender_id != Session::get('user-id') ){
            $has_new = true;
            break;
        }
    }
?>


<section id="top-bar" class="clear">

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

        <button id="btn-settings" data-modal-id="user-settings" class="modal-opener">
            <img src="<?= SERVER_ROOT ?>/uploads/avatars/<?= Session::get('user-avatar') ?>">
            <strong>Profil</strong>
        </button>


        <?php if( IS_ADMIN ): ?>
        <button id="btn-messages" class="<?= $has_new ? 'has-new-message' : '' ?>" >
            <i class="ion-ios-chatboxes-outline"></i>
        </button>
        <?php endif; ?>

        <a href="logout.php">
            <button id="btn-logout">
                <i class="ion-log-out"></i>
            </button>
        </a>

        <?php if( IS_ADMIN ): ?>
        <div class="messages-popup panel" style="display: none;">
            <section>
                <?php 
                    foreach( $messages as $message ){
                        echo UIDrawer::messageItem($message);
                    }
                ?>
            </section>
            <button id="create-new-message" class="modal-opener" data-modal-id="create-message">Új üzenet írása</button>
        </div>
        <?php endif; ?>
    </div> <!-- jobb oldali gombok vége -->
</section>