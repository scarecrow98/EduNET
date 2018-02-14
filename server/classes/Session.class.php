<?php

    class Session{

        private function __construct(){  }

        public static function set($key, $value){
            $_SESSION[$key] = $value;
        }

        public static function get($key){
            if( !empty($_SESSION[$key]) ){
                return $_SESSION[$key];
            }else{
                return null;
            }
        }

        public static function unset($key){
            unset($_SESSION[$key]);
        }

        public static function start(){
            session_start();
        }

        public static function destroy(){
            unset($_SESSION);
            session_destroy();
        }

    }

?>