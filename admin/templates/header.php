<?php
    require_once '../config.php';
    Session::start();

    if( Security::checkAccessToken() == false || Session::get('user-type') != 2 ){
        header('Location: ../logout');
        exit();
    }

    Security::setAccessToken();
?>
<html>
    <head>
        <meta charset="utf-8">
        <link href="css/main.css" rel="stylesheet">
        <script src="<?= PUBLIC_ROOT ?>js/jquery.js"></script>
        <script src="js/main.js"></script>
        <link href="http://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css" rel="stylesheet">
    </head>
    <body>
        <header id="page-header">
            <ul class="clear">
                <li>
                    <a href="users" class="ion-person-add">Felhasználók kezelése</a>
                </li>
                <li style="float: right;" title="Kijelentkezés">
                    <a href="../logout"><i class="ion-log-out" style="font-size: 22px;"></i></a>
                </li>
            </ul>
        </header>
        <div id="page">