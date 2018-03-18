<?php
    //felhasználó manuális regisztrálása
    if( isset($_POST['registrate-user']) ){
        $name = $_POST['user-name'];
        $password = $_POST['user-password'];
        $email = $_POST['user-email']; 
        $type = $_POST['user-type'];

        //létező email
        if( User::emailExists($email) ) exit('Az emailcím már létezik!');

        $data = array(
            'name'      => $name,
            'password'  => $password,
            'email'     => $email,
            'type'      => $type
        );
        Admin::registrateUser($data);
    }

    //CSV fájlból történő diákadatok feldolgozása
    if( isset($_FILES['csv']) && is_uploaded_file($_FILES['csv']['tmp_name']) ){
        //CSV fájl ellenőrzése, feldolgozása
		$data = FileUploader::parseCSV($_FILES['csv']);
		//reguláris kifejezés emailre
        $email_regex = '/^[a-zA-Z0-9.!#$%&’*+\/\=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/';


        //hibák keresése a CSV fájlból származó adatokban
        $line_counter = 1;
        foreach( $data as $user ){
            //létező email
            if ( Admin::emailExists($user['email']) ) exit('Hiba a '.$line_counter.'. sorban! Az email cím már létezik!');
            //üres név vagy email
            if( empty($user['name']) || empty($user['email']) ) exit('Hiba a '.$line_counter.'. sorban! A sor üres mezőt tartalmaz!');
            //rossz email formátum
            if( !preg_match($email_regex, $user['email']) ) exit('Hiba a '.$line_counter.'. sorban! Az email cím formátuma érvénytelen!');
            
            $line_counter++;
        }

        //diákok belépési adatainak eltárolása --> PDF generálás
        $user_data = array();

        //diákok regisztrálása
        foreach( $data as $user ){
			//a registrateUser metódus visszaadja a registrált user
			//belépési azonosítóját és a jelszavát egy tömbben
            $login = Admin::registrateUser(array(
                'name'  => utf8_encode($user['name']),
                'email' => utf8_encode($user['email']),
                'type'  => 0
            ));

			//összegyűjtjük a regisztrált diákok nevét, jelszavát és belépési azonosítóját,
			//hogy oda lehessen adni nekik papíron, vagy emailban
            $user_data[] = array(
                'name' 		=> utf8_encode($user['name']),
                'login_id' 	=> $login[0],
				'password'	=> $login[1]
            );
        }
		//regisztrált diákok adatainak eltárolása sessionbe,
		//hogy PDF-et tudjunk csinálni belőle a pdf-generator.php-ban
		Session::set('registrated-users', $user_data);
    }
?>

<div id="left">
    <p style="color: red;"><?= empty(Session::get('error-message'))?'':Session::get('error-message'); ?></p>
    <section>
        <h3>Manuális felvétel</h3>
        <form action="" method="POST" id="registrate-user-form">
            <li>
                <label for="">Felhasználó neve:</label>
                <input type="text" name="user-name" required autocomplete="off" value="<?= !empty($name)?$name:'' ?>">
            </li>
            <li>
                <label for="">Felhasználó email címe:</label>
                <input type="email" name="user-email" required autocomplete="off">
            </li>
            <li>
                <label for="">Felhasználó jelszava:</label>
                <input type="text" name="user-password" required autocomplete="off">
                <button id="btn-generate-pass">Jelszó generálása</button>
            </li>
            <li>
                <label for="">Felhasználó típusa:</label>
                tanár<input type="radio" name="user-type" value="1">
                diák<input type="radio" name="user-type" value="0" checked>
            </li>
            <li>
                <input type="submit" name="registrate-user" value="Felhasználó regisztrálása">
            </li>
        </form>
    </section>
    <section>
        <h3>Diákok felvétele CSV-ből</h3>
        <p style="color: #c3c3c3;">
            A CSV fájl egy sora egy felhasználó adatát tartalmazza! A sor cellái a következők legyen, ebben a sorrendben: <span style="color: #888; font-style: italic;">név;emailcím</span>.
            A típus értéke 0 legyen, ha a felhasználó diák, és 1-es abban az esetben, ha tanár.
            Arra is figyeljóünk, hogy az email cím egyedi legyen minden felhasználónál. Ellenkező esetben a megegyező emailel rendelkező sorok nem lesznek regisztrálva a rendszerben.
        </p>
        <form action="" method="POST" enctype="multipart/form-data">
            <input type="file" name="csv">
            <input type="submit" name="upload-csv">
        </form>
		<?php if( !empty(Session::get('registrated-users')) ): ?>
			<a href="../pdf-renderer.php">Belépési adatok letöltése</a>
		<?php endif; ?>
    </section>
</div>
<div id="right">

</div>