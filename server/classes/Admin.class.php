<?php

    class Admin{


        public static function getAllUsers(){
            $db = Database::getInstance();

            $stmt = $db->prepare(
                "SELECT * FROM users WHERE type != ? ORDER BY type DESC, name ASC"
            );
            $stmt->execute(array(2));

            $data = $stmt->fetchAll();

            $list = array();
            foreach( $data as $d ){
                array_push($list, new User($d));
            }

            return $list;
        }

        public static function emailExists($email){
            $db = Database::getInstance();

            $stmt = $db->prepare(
                "SELECT * FROM users WHERE email = ?"
            );
            $stmt->execute(array($email));

            return !empty($stmt->fetch());
        }

        public static function registrateUser($data){
            $db = Database::getInstance();

            $login_id = Admin::getLoginId();
            $pass_salt = Security::passwordSalt();

            $password;
            if( isset($data['password']) )
                $password = $data['password'];
            else
                $password = Security::generatePassword();

            $pass_hash = Security::hashPassword($password, $pass_salt);

            $stmt = $db->prepare(
                "INSERT INTO users(login_id, name, email, pass_hash, pass_salt, type)".
                " VALUES(?, ?, ?, ?, ?, ?)"
            );

            $stmt->execute(array(
                $login_id,
                $data['name'],
                $data['email'],
                $pass_hash,
                $pass_salt,
                $data['type']
            ));
            return $login_id.' - '.$password;            
        }

        private function getLoginId(){
            $db = Database::getInstance();

            $stmt = $db->prepare(
                "SELECT * FROM users ORDER BY id DESC LIMIT 1"
            );
            $stmt->execute();
            $login_id = $stmt->fetch()['login_id'];

            $output = '';
            $num_part = (int)$login_id[2].$login_id[3];
            $c1 = $login_id[0];
            $c2 = $login_id[1];

            $ascii1 = ord($c1);
            $ascii2 = ord($c2);

            if( $num_part == 99){
                $num_part = 0;

                if( $ascii2 == 122 ){
                    $c2 = chr(97);
                    $c1 = chr( ++$ascii1 );
                }
                else{
                    $c2 = chr( ++$ascii2 );
                }

            }else{
                $num_part++;
            }

            $output = $c1.$c2.sprintf("%02d", $num_part);
            return $output;
        }

    }

?>