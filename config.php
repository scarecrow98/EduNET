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

    //input adatok hossza
    $data_req = array(
        'test_title'    => 100,
        'test_desc'     => 255,
        'user_name'     => 50,
        'user_email'    => 255,
        'subject_name'  => 100,
        'ntf_text'      => 100,
        'group_name'    => 100,
        'group_desc'    => 255,
        'task_comment'  => 255
    );

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