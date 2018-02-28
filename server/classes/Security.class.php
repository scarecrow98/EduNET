<?php

    class Security{

        public static function passwordSalt(){
            return hash('md5', bin2hex( random_bytes( 16 ) ));
        }

        public static function hashPassword($password, $salt){
            return hash('sha256', $password.$salt);
        }

        public static function formToken(){
            return hash('sha1', bin2hex( random_bytes(16) ).microtime(true));
        }

    }

?>