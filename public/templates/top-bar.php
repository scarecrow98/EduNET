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


<section id="top-bar">

    <!-- keresés mező -->
	<?php if( isset($_GET['page']) && $_GET['page'] == 'tests' ): ?>
    <button id="btn-show-search"><i class="ion-ios-search-strong"></i></button>

    <div id="search-form-container">
        <?php require_once 'public/templates/search-form.php'; ?>
    </div>
    <?php endif; ?>
    
    <!-- jobb oldali gombok -->
    <div id="top-bar-buttons-right">
        <button id="btn-settings" data-modal-id="user-settings" class="modal-opener"><i class="ion-gear-a"></i></button><button id="btn-messages" class="<?= $has_new ? 'has-new-message' : '' ?>"><i class="ion-ios-chatboxes-outline"></i></button><a href="logout.php"><button id="btn-logout"><i class="ion-log-out"></i></button></a>
        <div class="messages-popup panel" style="display: none;">
            <section>
                <?php 
                    foreach( $messages as $message ):
                        $partner_id = $message->sender_id==Session::get('user-id') ? $message->receiver_id : $message->sender_id;
                        $partner = User::get($partner_id);
                        $partner_name = explode(' ', $partner->name)[1];

                        $is_unread = ( $message->is_seen == 0 && $message->sender_id != Session::get('user-id') ) ? true : false;
                ?>
                        <li class="message-item <?= $is_unread ? 'unread-message' : '' ?>" data-message-id="<?= $message->id; ?>" data-partner-id="<?= $partner_id ?>" id="partner-<?= $partner_id ?>">
                            <div>   
                                <span style="background-image: url('<?= SERVER_ROOT.'uploads/avatars/'.$partner->avatar; ?>')"></span>
                                <h4 style="inline"><?= $partner->name; ?></h4>
                            </div> 
                            <time><?= $message->date; ?></time>
                            <p><?= $message->text; ?></p>
                        </li>
                <?php endforeach; ?>
            </section>
            <button id="create-new-message" class="modal-opener" data-modal-id="create-message">Új üzenet írása</button>
        </div>
    </div>
</section>