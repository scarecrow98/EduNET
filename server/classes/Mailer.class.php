<?php

    class Mailer{

        private static $headers = "MIME-Version: 1.0\r\nContent-Type: text/html; charset=UTF-8\r\n";

        public static function sendPassword($name, $login_id, $password, $email){
            $subject = 'EduNET - felhasználói fiók regisztráció';

            $html = '<html><head><meta charset="utf-8"></head><body>';
            $html .= '<h1>Üdvözlünk az EduNET-en!</h1>';
            $html .= '<div style="font-size: 15px;">';
            $html .= '<p>Szia! Ha ezt az emailt olvasod, valószínüleg az iskolád EduNET alkalmazást használ és az ímént regisztráltak a nevedre egy felhaszánlói fiókot. Ebben a levélben küldjük neked a belépési adataidat:</p>';
            $html .= '<ul><li style="padding-left: 15px;"><span style="display: inline-block; width: 150px;">Belépési azonosítód:</span> <strong>'.$login_id.'</strong> (ezt az azonosítót ne felejtsd el, hiszen mindig ezzel fogsz majd tudni belépni)</li>';
            $html .= '<li style="padding-left: 15px;"><span style="display: inline-block; width: 150px;">Jelszavad:</span> <strong>'.$password.'</strong> (lehetőséged van a jelszó megváltoztatására az alkalmazásban)</li></ul>';
            $html .= '</div>';
            $html .= '<h3>Jó tanulást és sikeres dolgozatokat kíván az EduNET csapata! :)</h3>';
            $html .= '</body></html>';

            mail($email, $subject, $html, Mailer::$headers);
        }

    }

?>