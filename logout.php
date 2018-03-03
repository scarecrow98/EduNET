<?php
    require_once 'config.php';

    Session::start();
    Session::destroy();
    
    Security::destroyAccessToken();

    header('Location: login');
?>