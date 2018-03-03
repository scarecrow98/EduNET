<?php

    class Security{

        public static function passwordSalt(){
            return hash('md5', bin2hex( random_bytes( 16 ) ));
        }

        public static function hashPassword($password, $salt){
            return hash('sha256', $password.$salt);
        }

        public static function generatePassword(){
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            $password = '';

            for($i = 0; $i <= 7; $i++){
                $index = rand(0, strlen($chars) - 1);
                $password .= $chars[$index];
            }

            return $password;
        }

        public static function securityToken(){
            return hash('sha1', bin2hex( random_bytes(16) ).microtime(true));
        }

        public static function setAccessToken(){
            $token = Security::securityToken();
            //20 perces biztonsági token
            setcookie('access-token', $token, time() + 1200, '/');
            Session::set('access-token', $token);

        }

        public static function checkAccessToken(){
            if( isset($_COOKIE['access-token']) && !empty(Session::get('access-token')) ){
                if( Session::get('access-token') !== $_COOKIE['access-token']  ){
                    return false;
                }
            } else {
                return false;
            }
            return true;
        }

        public static function destroyAccessToken(){
            if( isset($_COOKIE['access-token']) ){
                unset($_COOKIE['access-token']);
                setcookie('access-token', '', time() - 3600, '/');   
            }
        }


    }

?>