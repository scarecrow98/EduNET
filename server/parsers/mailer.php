<?php

    if( isset($_POST['closed']) ){
        $subject = 'Dolgozateredmények';
        $to = 'r_ferenc98@onbox.hu';
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";;

        $html = '
            <html>
                <head>
                    <meta charset="utf-8">
                </head>
                <body>
                    <h1>Dolgozat értesítő</h1>
                    <h3>2017. 05. 24-én írt Informatika dolgozat eredményei:</h3>
                    <ul>
                        <li>1. feladat: Jellemezd az alábbi képen lévő támadási formát! <strong>10 / 7 pont</strong></li>
                        <li>2. feladat: Írd le, mik azok a relációk! <strong>6 / 5 pont</strong></li>
                    </ul>
                    <h3>Végleges eredmény: 16 / 12 pont</h3>
                    <h3>Érdemjegy: 5</h3>

                    <p>Üdvözlettel:<i> Rózsa Zoltán</i></p>
                </body>
            </html>
        ';

        try{
            mail($to, $subject, $html, $headers);
        }
        catch(Exception $e){
            exit($e->getMessage());
        }

    }

    // if(mail($to, $subject, $html, $headers)){
    //     echo 'Email kiküldve';
    // }
    // else{
    //     echo 'Email küldése sikertelen!';
    // }

?>