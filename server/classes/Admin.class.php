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

        private static function emailExists($email){
            $db = Database::getInstance();

            $stmt = $db->prepare(
                "SELECT * FROM users WHERE email = ?"
            );
            $stmt->execute(array($email));

            return !empty($stmt->fetch());
        }

        public static function registrateUser($data){
            $db = Database::getInstance();
            $error = false;

            if( Admin::emailExists($data['email']) ){
                Session::set('error-message', 'Az emailcím már létezik!');
                $error = true;
            }

            $login_id = Admin::getLoginId();
            $pass_salt = hash( 'md5', bin2hex(random_bytes(16)) );

            $pass_hash = hash('sha256', $data['password'].$pass_salt);

            $stmt = $db->prepare(
                "INSERT INTO users(login_id, name, email, pass_hash, pass_salt, type)".
                " VALUES(?, ?, ?, ?, ?, ?)"
            );

            if( !$error ){

                $stmt->execute(array(
                    $login_id,
                    $data['name'],
                    $data['email'],
                    $pass_hash,
                    $pass_salt,
                    $data['type']
                ));
                
                Mailer::sendPassword($data['name'], $login_id, $data['password'], $data['email']);
            }
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