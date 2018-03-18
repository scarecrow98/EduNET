<?php

    require_once 'config.php';

    Session::start();

    if( !empty(Session::get('access-token')) ){
        header('Location: home');
    }

    if( !empty($_POST['login-submit']) ) {
        $login_id = $_POST['login-id'];
        $password = $_POST['login-password'];

        //üres mezők vizsgálata
        if (empty($login_id) || empty($password)) {
            Session::set('error-message', 'Nincs kitöltve minden mező!');
            header('Location: login');
            exit();
        }
		
		//user adatok lekérése belépési azonosító alapján
		$user = User::getByLogin($login_id);
		//ha nincs ilyen user, akkor hibaüzenet	
		if( empty($user->id) ){
			Session::set('error-message', 'Helytelen belépési adatok!');
			header('Location: login');
            exit();
		}
		
		//megadott jelszó és az adatábzisban lévő só kódolása
		$pass_hash = hash('sha256', $password.$user->pass_salt);
		
		//ha nem egyezik az adatbázisban lévő értékkel az előbb generált hash, akkor hiba
		if( $pass_hash != $user->pass_hash ){
			Session::set('error-message', 'Helytelen belépési adatok!');
			header('Location: login');
			exit();
        }
        
        //ha 2-es a user típusa, akkor az adminról van szó
        if( $user->type == 2 ){
            Session::set('user-name', $user->name);
		    Session::set('user-type', $user->type);
            Security::setAccessToken(); 		
            header('Location: admin');
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
        //biztonsági token beállítása
        Security::setAccessToken();
		
		header('Location: home');
        exit();
	}
	
	//elfelejtett jelszó kérelem feldolgozása
	if( !empty($_POST['forgotten-password']) ){
		$email = $_POST['email'];

		//megnézzük, hogy létezik-e az email
		if( !User::emailExists($email) ){
			Session::set('error-message', 'A megadott emailcím nem létezik a rendszerben!');
			header('Location: login?form=forgotten_password');
			exit();
		}	

		//felhasználó adatainak lekérése az emailje alapján
		$user = User::getByEmail($email);
		//új jelszó generálása
		$new_password = Security::generatePassword();
		//jelszó frissítése az adatbázisban
		User::updatePassword($user->id, $new_password);

		//keresztnév kinyerése
		$name = explode(' ', $user->name)[1];

		//email kiküldése
		Mailer::newPassword($name, $user->email, $new_password);
		echo '<script>alert("A jelszavad megváltoztattuk. Nézd meg az email címedet!");</script>';
	}
?>
<html>
    <head>
        <title>EduNET - Bejelenkezés</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" href="<?= PUBLIC_ROOT ?>resources/images/favicon.ico">
        <link rel="stylesheet" href="<?= PUBLIC_ROOT ?>css/welcome.css">
    </head>
    <body>

		<!-- oldal hátterei -->
		<section class="page-bg" id="p-1"></section>
		<section class="page-bg" id="p-2" style="display: none;"></section>
		
        <div class="overlay">
			<!-- ha a form paraméterünk a GET tömbben = 'forgotten_password', akkor azt a formot jelenítjük meg -->
			<?php if( !empty($_GET['form']) && $_GET['form'] == 'forgotten_password' ): ?>			
			<div class="form-container">
				<form action="" method="POST">
					<li>
						<h1>Elfelejtett jelszó</h1>
						<small><?= empty(Session::get('error-message')) ? '' : Session::get('error-message') ?></small>
					</li>	
					<li>
						<input type="email" name="email" placeholder="Fiókod email címe" class="input-field" required autocomplete="off">
					</li>
					<li>
						<input type="submit" name="forgotten-password" value="Jelszó igénylése" class="button">
					</li>
					<li>
						<a href="login" class="cta">Bejelentkezés</a>
					</li>
				</form>
			</div>
			<?php else: ?>
            <div class="form-container">
				<form action="" method="POST">
					<li>
						<img src="<?= PUBLIC_ROOT ?>resources/images/edunet-logo-black.png" alt="">
						<h1>Bejelentkezés</h1>
						<small><?= empty(Session::get('error-message')) ? '' : Session::get('error-message') ?></small>
					</li>
					<li>
						<input type="text" name="login-id" placeholder="Felhasználónév" class="input-field" autocomplete="off">
					</li>
					<li>
						<input type="password" name="login-password" placeholder="Jelszó" class="input-field">
					</li>
					<li>
						<input type="hidden" name="form-token" value="">
					</li>
					<li>
						<input type="submit" name="login-submit" value="Bejelentkezés" class="button">
					</li>
					<li>
						<a href="?form=forgotten_password" class="cta">Elfelejtett jelszó</a>
					</li>
				</form>
            </div>
			<?php endif; ?>
        </div>

    </body>
</html>
<script src="<?= PUBLIC_ROOT; ?>js/jquery.js"></script>
<script>

	//háttérképek váltakozása
	let cnt = 1;
	window.setInterval(function(){
		cnt++;
		
		if( cnt > 2 ) cnt = 1;
		
		$('section#p-'+cnt).fadeIn(3000).delay(5000).siblings('section.page-bg').fadeOut(3000).delay(5000);
	}, 1000);

</script>
<?php Session::unset('error-message'); ?>