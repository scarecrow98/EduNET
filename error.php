<?php
    session_start();
    if( empty($_SESSION['error-message']) ) exit();

    require_once 'config.php';
?>
<html>
    <head>
        <meta charset="">
        <title>Valami nincs rendben!</title>
        <link rel="icon" href="<?php echo PUBLIC_ROOT; ?>resources/images/favicon.ico">
        <style>
            @import url('https://fonts.googleapis.com/css?family=Montserrat:300,400,600,700&subset=latin-ext');
            *{ padding: 0px; margin: 0px; font-family: 'Montserrat'; }
            body{ background-image: url('public/resources/images/bg2.jpeg'); background-position: center; background-size: cover; height: 100vh; display: flex; justify-content: center; color: #fff; }
            #overlay{ position: absolute; background-color: #354052; top: 0px; left: 0px; bottom: 0px; right: 0px; z-index: 1; opacity: 0.8; }
            #container{ text-align: center; max-width: 60%; position: relative; z-index: 2; }
            img{ width: 400px; opacity: 0.7; }
            h1{ padding: 20px 0px; }
            a{ color: #fff; padding: 4px; border-bottom: 1px solid #fff; text-decoration: none; }
        </style>
    </head>
    <body>
        <div id="overlay"></div>
        <div id="container">
            <img src="public/resources/images/edunet-logo-white.png" alt="">
            <h1><?= $_SESSION['error-message']; ?></h1>
            <a href="home">Ugrás a főoldalra &raquo;</a>
        </div>
    </body>
</html>