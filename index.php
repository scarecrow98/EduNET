﻿<?php

    //Internet Explorer detektálása, hibaüzenet
    if( strpos($_SERVER['HTTP_USER_AGENT'], 'Trident') ){
        exit('Sajnos az alkalmazás nem támogatott Internet Explorer böngészőben. Kérlek látogass vissza Mozzila vagy Chrome böngészőből! ');
    }

    require_once 'config.php';

    Session::start();

    if( Security::checkAccessToken() == false ){
        header('Location: logout');
        exit();
    }

    Security::setAccessToken();

    DEFINE('IS_ADMIN', Session::get('user-type'));
    
    //biztonsági token generálása az oldaolon található formokhoz
    $token = Security::securityToken();
    Session::set('security-token', $token);
?>
<html>
    <head>
        <meta charset="utf-8">
        <title>EduNET</title>
        <link rel="icon" href="<?= PUBLIC_ROOT ?>resources/images/favicon.ico">
        <link rel="stylesheet" href="http://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
        <link rel="stylesheet" href="<?= PUBLIC_ROOT ?>css/style.css">
    </head>
    <body>

        <?php require_once 'public/templates/modals.php'; ?>

        <div id="page">
            <?php include 'public/templates/side-menu.php'; ?>
            <?php include 'public/templates/top-bar.php'; ?>

            <section id="content">
                <?php
                    $page = isset($_GET['page'])?$_GET['page']:null;
                    $page = rtrim($page, '/');
                    $page = explode('/', $page);

                    if( !empty($page[0]) ){
                        $file = 'public/templates/'.$page[0].'.php';
                        if( file_exists( $file ) ){
                            require_once $file;
                        }
                    }else{
                        require_once 'public/templates/home.php';
                    }
                ?>
            </section>
        </div>

    </body>
</html>
<script src="<?= PUBLIC_ROOT ?>js/jquery.js"></script>
<script src="<?= PUBLIC_ROOT; ?>js/ajax-settings.js"></script>
<script src="<?= PUBLIC_ROOT ?>js/main.js"></script>
<script src="<?= PUBLIC_ROOT ?>js/ajax.js"></script>
<?= IS_ADMIN ? '<script src="'.PUBLIC_ROOT.'js/message-ajax.js"></script>' : '&nbsp' ?>
<?= IS_ADMIN ? '<script src="'.PUBLIC_ROOT.'js/teacher-ajax.js"></script>' : '&nbsp' ?>
