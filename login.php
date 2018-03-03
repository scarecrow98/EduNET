<?php

    require_once 'config.php';

    Session::start();

    if( !empty(Session::get('user-id')) ){
        header('Location: home');
    }

    if( !empty($_POST['login-submit']) ) {
        $login_id = $_POST['login-id'];
        $password = $_POST['login-password'];

        //user mezők vizsgálata
        if (empty($login_id) || empty($password)) {
            Session::set('error-message', 'Nincs kitöltve minden mező!');
            header('Location: login');
            exit();
        }

		$user = User::getByLogin($login_id);
				
		if( empty($user->id) ){
			Session::set('error-message', 'Helytelen belépési adatok!');
			header('Location: login');
            exit();
		}
		
		$pass_hash = hash('sha256', $password.$user->pass_salt);
		
		if( $pass_hash != $user->pass_hash ){
			Session::set('error-message', 'Helytelen belépési adatok!');
			header('Location: home');
			exit();
		}

        //ha minden rendben volt, beléptetjük a felhasználót
		Session::unset('error-message');

		Session::set('user-id', $user->id);
		Session::set('user-name', $user->name);
		Session::set('user-type', $user->type);
		Session::set('user-avatar', $user->avatar);
        Session::set('user-email', $user->email);
        Session::set('user-subscription', $user->is_subscribed);
        
        Security::setAccessToken();
		
		header('Location: home');
        exit();
    }
?>
<html>
    <head>
        <title>EduNET - Bejelenkezés</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" href="<?= PUBLIC_ROOT ?>resources/images/favicon.ico">
        <link rel="stylesheet" href="http://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
        <link rel="stylesheet" href="<?= PUBLIC_ROOT ?>css/welcome.css">
    </head>
    <body>

		
		<section class="page-bg" id="p-1"></section>
		<section class="page-bg" id="p-2" style="display: none;"></section>
		
        <div class="overlay">

            <div id="login-window">
                <div class="left-col">
                    <form action="" method="POST">
                        <li>
                            <h1>Bejelentkezés</h1>
                            <small><?= empty(Session::get('error-message')) ? '' : Session::get('error-message') ?></small>
                        </li>
                        <li>
                            <input type="text" name="login-id" placeholder="Felhasználónév" class="input-field">
                        </li>
                        <li>
                            <input type="password" name="login-password" placeholder="Jelszó" class="input-field">
                        </li>
                        <li>
                            <input type="hidden" name="form-token" value="">
                        </li>
                        <li>
                            <input type="submit" name="login-submit" value="Bejelentkezés" class="login-button">
                        </li>
                    </form>
                </div>
                <div class="right-col">
                    <div id="logo-container">
                        <img src="<?= PUBLIC_ROOT; ?>resources/images/edunet-logo-white.png" alt="">
                    </div>
                    <p>
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce tincidunt, est euismod tincidunt aliquam, urna libero molestie lectus, quis sodales dui lorem eleifend nulla. Quisque fermentum nisl mi, non feugiat velit accumsan dapibus. 
                    </p>
                </div>
            </div>

        </div>

    </body>
</html>
<script src="<?= PUBLIC_ROOT; ?>js/jquery.js"></script>
<script>
	let cnt = 1;

	window.setInterval(function(){
		cnt++;
		
		if( cnt > 2 ){
			cnt = 1;
		}
		
		$('section#p-'+cnt).fadeIn(3000).delay(5000).siblings('section.page-bg').fadeOut(3000).delay(5000);
	}, 1000);


</script>