<?php

    class Mailer{
        private function __construct(){  }
        private function __clone(){  }

        //emailok header-je --> meghatározza a tartalom típusát, ésa karakterkódolást
        private static $headers = "MIME-Version: 1.0\r\nContent-Type: text/html; charset=UTF-8\r\n";

        //elfelejtett jelszó kiküldése
        public static function newPassword($name, $email, $new_pass){
            $html = '<html><head><meta charset="utf-8"></head><body>';
            $html .= '<h2>Szia, '.$name.'!</h2>';
            $html .= '<p>Értesültünk róla, hogy új jelszót igényeltél az EduNET fiókodhoz, mivel a régit valószínüleg elfelejtetted.</p>';
            $html .= '<p>A jelszavad a következőre változott: <strong>'.$new_pass.'</strong>.</p>';
            $html .= '<p><a href="http://localhost/EduNET/login">Lépj be</a>, és változtasd meg minél előbb a jelszavad!</p><br>';
            $html .= '<p>További szép időtöltést és jó tanulást kíván az EduNET csapata! :)</p>';
            $html .= '</body></html>';

            return mail($email, 'EduNET - Elfelejtett jelszó', $html, Mailer::$headers);
        }

    }

?>