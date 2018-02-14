<?php

    class Database{
        private static $instance;

        private function __construct(){  }
        private function __clone(){  }
        
        public static function getInstance(){
            if( !isset(self::$instance) ){
                self::$instance = new PDO('mysql:host='.HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD, array(
                    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8 COLLATE utf8_hungarian_ci',
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                ));
            }

            return self::$instance;
        }
    }

?>