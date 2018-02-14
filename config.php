<?php
    //adatbázis
    DEFINE('HOST', 'localhost');
    DEFINE('DB_NAME', 'edunet');
    DEFINE('DB_USER', 'root');
    DEFINE('DB_PASSWORD', '');

    //könyvtárak
    DEFINE('PUBLIC_ROOT', 'http://localhost/EduNET/public/');
    DEFINE('SERVER_ROOT', 'http://localhost/EduNET/server/');

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

    //biztonsági token generáló formokhoz
    function generateToken(){
        $string = bin2hex( random_bytes(16) ).microtime(true);
        return hash('sha1', $string);
    }
?>