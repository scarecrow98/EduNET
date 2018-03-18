<?php
    require_once 'templates/header.php';

    if( isset($_GET['page']) ){
        $file = 'templates/'.$_GET['page'].'.php';
        require_once $file;
    } else {
        require_once 'templates/users.php';
    }

    require_once 'templates/footer.php';
?>