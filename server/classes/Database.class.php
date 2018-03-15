<?php

    class Database{
        private static $instance;
		
		//klonozás és példányosítás nem megengedett
        private function __construct(){  }
        private function __clone(){  }
        
        //visszaadja a már megnyitptt DB kapcsolatot, vagy egy újat, ha még nincs megnyitva
        public static function getInstance(){
			//ha nincsen értéke az $instance-nek
			//akkor egy PDO kapcsolatot hozunk létre
            if( !self::$instance ){
                self::$instance = new PDO('mysql:host='.HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD, array(
                    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8 COLLATE utf8_hungarian_ci',
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                ));
            }
			//egyébként a már meglévő $instance tagok adjuk vissza
            return self::$instance;
        }
    }

?>