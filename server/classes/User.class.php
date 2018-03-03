<?php

    class User {

		public $id;
		public $login_id;
		public $name;
		public $email;
		public $is_subscribed;
		public $avatar;
		public $type;
		public $pass_hash;
		public $pass_salt;


        public function __construct($data){
			$this->id = $data['id'];
			$this->login_id = $data['login_id'];
			$this->name = $data['name'];
			$this->email = $data['email'];
			$this->is_subscribed = $data['is_subscribed'];
			$this->avatar = $data['avatar'];
			$this->type = $data['type'];
			$this->pass_salt = $data['pass_salt'];
			$this->pass_hash = $data['pass_hash'];
        }
		
		public static function get($user_id){
			$db = Database::getInstance();
			
			$stmt = $db->prepare(
				"SELECT * FROM users WHERE id = ?"
			);
			$stmt->execute(array($user_id));
			
			return new User($stmt->fetch());
		}
		
		public static function getByLogin($login_id){
			$db = Database::getInstance();

			$stmt = $db->prepare(
				"SELECT * FROM users WHERE login_id = ?"
			);
			$stmt->execute(array($login_id));
			
			return new User($stmt->fetch());
		}
		
		
		public static function getByType($user_type){
			$db = Database::getInstance();
			
			$stmt = $db->prepare(
				"SELECT * FROM users WHERE type = ?"
			);
			$stmt->execute(array($user_type));
			
            $data = $stmt->fetchAll();

            $list = array();
            foreach( $data as $d ){
                array_push($list, new User($d));
            }

            return $list;
		}
		
        public static function updatePassword($user_id, $password){
            $salt = Security::passwordSalt();
            $hash = Security::hashPassword($password, $salt);
            
            $db = Database::getInstance();

            $stmt = $db->prepare(
                "UPDATE users SET pass_hash = ?, pass_salt = ? WHERE id = ?"
            );
            $stmt->execute(array($hash, $salt, $user_id));

            session_regenerate_id();
		}

        public static function updateEmail($user_id, $email){
            $db = Database::getInstance();

            $stmt = $db->prepare(
                "UPDATE users SET email = ? WHERE id = ?"
            );
            $stmt->execute(array($email, $user_id));
		}

				
		public static function updateAvatar($user_id, $file_name){
            $db = Database::getInstance();

            $stmt = $db->prepare(
                "UPDATE users SET avatar = ? WHERE id = ?"
            );
            $stmt->execute(array($file_name, $user_id));
		}

		public static function updateSubscription($user_id, $status){
            $db = Database::getInstance();

            $stmt = $db->prepare(
                "UPDATE users SET is_subscribed = ? WHERE id = ?"
            );
            $stmt->execute(array($status, $user_id));
		}
		
		public static function emailExists($email){
			$db = Database::getInstance();

			$stmt = $db->prepare(
				"SELECT id FROM users WHERE email = ?"
			);
			$stmt->execute(array($email));
			
			return ($stmt->rowCount() > 0);
		}


    }

?>