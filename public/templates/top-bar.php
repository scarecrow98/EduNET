<?php
    //üzenetek lekérése
    $messages = Message::getAll(Session::get('user-id'));
	
    //megsázmoljuk hány olvasatlan üzenet van
    $unread_messages = 0;
    foreach( $messages as $message ){
        if( $message->is_seen == 0 ) $unread_messages++;
    }

?>


<section id="top-bar">
	<?php if( isset($_GET['page']) && $_GET['page'] == 'tests' ): ?>
    <button id="btn-show-search"><i class="ion-ios-search-strong"></i></button>

    <div class="" id="search-form-container">
        <?php require_once 'public/templates/search-form.php'; ?>
    </div>
	<?php endif; ?>
    <div id="top-bar-buttons-right">
        <button id="btn-settings" data-modal-id="user-settings" class="modal-opener"><i class="ion-gear-a"></i></button><button id="btn-messages"><i class="ion-ios-chatboxes-outline"></i><span id="message-counter"><?php echo $unread_messages; ?></span></button><a href="logout.php"><button id="btn-logout"><i class="ion-log-out"></i></button></a>
        <div class="messages-popup panel" style="display: none;">
            <section>
                <?php 
					foreach( $messages as $message ):
					$sender = User::get($message->sender_id);
                ?>
                    <li class="message-item <?php echo $message->is_seen==0?'unread-message':'' ?>" data-message-id="<?php echo $message->id; ?>">
                        <div>   
                            <span style="background-image: url('<?php echo AVATAR_DIR.$sender->avatar; ?>')"></span>
                            <h4 style="inline"><?php echo $sender->name; ?></h4>
                        </div> 
                        <time><?php echo $message->date; ?></time>
                        <p><?php echo $message->text; ?></p>
                    </li>
                <?php endforeach; ?>
            </section>
            <button id="create-new-message" class="modal-opener" data-modal-id="create-message">Új üzenet írása</button>
        </div>
    </div>
</section>