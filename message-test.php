<pre>
<?php

    require_once 'config.php';
    Session::start();
    $user_id = Session::get('user-id');

    $ids = Message::getPartnerIds($user_id);

    $msgs = array();
    foreach( $ids as $partner_id ){
        $msgs[] = Message::getLastConversationMessage($user_id, $partner_id);
    }

    print_r($msgs);

    $msgs = Message::orderByDate($msgs);
    print_r($msgs);
?>
<script>console.log(JSON.parse('<?= json_encode($msgs); ?>'));</script>
</pre>