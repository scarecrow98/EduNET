<?php
    //adatbázis
    DEFINE('HOST', 'localhost');
    DEFINE('DB_NAME', 'edunet');
    DEFINE('DB_USER', 'edunet_admin');
    DEFINE('DB_PASSWORD', 'CyrwQsqbykKNgDRE');

    //könyvtárak
    DEFINE('PUBLIC_ROOT', 'http://edunet/public/');
    DEFINE('SERVER_ROOT', 'http://edunet/server/');

    //hónapok magyarul, rövidítéssel
    $months = array(
        '1'     => 'Jan.',
        '2'     => 'Febr.',
        '3'     => 'Márc.',
        '4'     => 'Ápr.',
        '5'     => 'Máj.',
        '6'     => 'Jún.',
        '7'     => 'Júl.',
        '8'     => 'Aug.',
        '9'     => 'Szept.',
        '10'    => 'Okt.',
        '11'    => 'Nov.',
        '12'    => 'Dec.' 
    );

    //osztály autoloader
    spl_autoload_register(function($class_name){
        $file = 'C:xampp/htdocs/EduNET/server/classes/'.$class_name.'.class.php';
        //ha létezik az osztály fájl, akkor behúzzuk
        //azért van erre szükség, mert a Dompdf könyvtár saját loaderrel rendelkezik
        //de a mi loaderünk a Dompdf-et is be akarja húzni a classes mappából, ám az nem ott van elhelyezve
        if( file_exists($file) ){
            require_once $file;
        }else{
            return;
        }
    });

    //hiba oldalra irányítás
    function errorRedirect($message){
        $_SESSION['error-message'] = $message;
        header('Location: http://edunet/error.php');
		exit();
    }
?>