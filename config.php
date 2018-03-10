<?php
    error_reporting(E_ALL);

    //adatbázis
    DEFINE('HOST', 'localhost');
    DEFINE('DB_NAME', 'edunet');
    DEFINE('DB_USER', 'root');
    DEFINE('DB_PASSWORD', '');

    //könyvtárak
    DEFINE('PUBLIC_ROOT', 'http://localhost/EduNET/public/');
    DEFINE('SERVER_ROOT', 'http://localhost/EduNET/server/');

    //dolgozattípusok + classok
    $test_types = array(
        'Házi feladat',
        'Dolgozat',
        'Témazáró dolgozat',
        'Szóbeli felelet',
    );

    //osztály autoloader
    spl_autoload_register(function($class_name){
        $file = $_SERVER['DOCUMENT_ROOT'].'/EduNET/server/classes/'.$class_name.'.class.php';
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
        header('Location: http://'.$_SERVER['SERVER_NAME'].'/EduNET/error.php');
    }

    //hibanaplózó függvény
    function logError($message){
        $date = date('Y-m-d H:i:s');
        $msg = $message.' - '.$date.'\n';
        error_log($msg, 3, 'C:/xampp/htdocs/edunet/admin/errors.log');
    }
?>