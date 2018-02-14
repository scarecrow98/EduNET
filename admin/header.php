<?php
    require_once('../settings.php');
    $pdo = newDataBaseConnection();

    
?>
<html>
    <head>
        <meta charset="utf-8">
        <link href="main.css" rel="stylesheet">
        <link href="http://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css" rel="stylesheet">
    </head>
    <body>
        <header id="page-header">
            <ul class="clear">
                <li>
                    <a href="add-user.php" class="ion-person-add">Felhasználó felvétele</a>
                </li>
                <li>
                    <a href="stats.php" class="ion-stats-bars">Rendszerstatisztika</a>
                </li>
                <li>
                    <a href="uploads.php" class="ion-android-upload">Feltöltések</a>
                </li>
            </ul>
        </header>