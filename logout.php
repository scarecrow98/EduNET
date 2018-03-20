<?php
    require_once 'config.php';
    Session::start();    
    Security::destroyAccessToken();
	Session::destroy();
    header('Location: login');
?>