<?php
    require_once 'config.php';

    User::updateStatus(Session::get('user-id'), 0);
	
	Session::start();
    Session::destroy();
	
    header('Location: login.php');
?>