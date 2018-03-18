<?php

    class Session{

        private function __construct(){  }
        private function __clone(){  }

        //beállít egy session változót
        public static function set($key, $value){
            $_SESSION[$key] = $value;
        }

        //visszaad egy session változót, vagy ha nem létezik, akkor null-t
        public static function get($key){
            if( !empty($_SESSION[$key]) )
                return $_SESSION[$key];
            return null;
        }

        //töröl egy session változót
        public static function unset($key){
            unset($_SESSION[$key]);
        }

        //elkezdi a munkamenetet
        public static function start(){
            session_start();
        }

        //törli a munkamenetet
        public static function destroy(){
            unset($_SESSION);
            session_destroy();
        }

    }

?>