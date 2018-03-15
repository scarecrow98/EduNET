<?php

    class Admin{


        //visszaadja az összes usert, amely nem admin
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

        //visszaadja, hogy létezik e-már ilyen email
        public static function emailExists($email){
            $db = Database::getInstance();

            $stmt = $db->prepare(
                "SELECT * FROM users WHERE email = ?"
            );
            $stmt->execute(array($email));

            return !empty($stmt->fetch());
        }

        //user regisztálása
        public static function registrateUser($data){
            $db = Database::getInstance();

            $login_id = Admin::getLoginId();
            $pass_salt = Security::passwordSalt();

            //ha van a paraméter tömbben ($data) jelszó akkor azt használjuk, egyébként generálunk egyet
            $password;
            if( isset($data['password']) )
                $password = $data['password'];
            else
                $password = Security::generatePassword();

            $pass_hash = Security::hashPassword($password, $pass_salt);

            $stmt = $db->prepare(
                "INSERT IGNORE INTO users(login_id, name, email, pass_hash, pass_salt, type)".
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
            //visszadjuk a belépési ID-t és a jelszót
            return array($login_id, $password);            
        }

        //visszaadja a soron következő belépési id-t
        private static function getLoginId(){
            $db = Database::getInstance();

            //utolsó login id lekérése
            $stmt = $db->prepare(
                "SELECT * FROM users ORDER BY id DESC LIMIT 1"
            );
            $stmt->execute();
            $login_id = $stmt->fetch()['login_id'];

            //login id generálása (előző növelése egyel) pl.: ab01 --> ab02
            $output = '';
			//az előző azonosító számrészének eltárolása (utolsó 2 karakter)
            $num_part = (int)$login_id[2].$login_id[3];
			//első betű karakter
            $c1 = $login_id[0];
			//második betű karakter
            $c2 = $login_id[1];

			//két betű ASCII kódjának tárolása
            $ascii1 = ord($c1);
            $ascii2 = ord($c2);

			//ha a szám rész 99, akkor nullázuk
            if( $num_part == 99){
                $num_part = 0;

				//ha a második betű kódja 122 (azaz 'z'), akkor a viszzaállítjuk 'a'-ra
				//+ az első betűt pedig léptetjük egyel az ABC-ben
                if( $ascii2 == 122 ){
                    $c2 = chr(97);
                    $c1 = chr( ++$ascii1 );
                }
				//ellenkező esetben a második betűt léptetjük egyel az ABC-ben
                else{
                    $c2 = chr( ++$ascii2 );
                }

            }
			//ellenkező esetben csak a szám részt növeljük egyel
			else{ 
                $num_part++;
            }

			//egyel növelt azonosító visszaadása
            $output = $c1.$c2.sprintf("%02d", $num_part);
            return $output;
        }

    }

?>