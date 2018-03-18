<?php

	class Notification{
	
		//adattagok
		public $id;
		public $title;
		public $date;
		public $author_id;
		public $group_id;
		public $subject_id;
		public $type;
		
		//konstruktor
		public function __construct($data){
			$this->id = $data['id'];
			$this->title = $data['text'];
			$this->date = $data['date'];
			$this->author_id = $data['author_id'];
			$this->group_id = $data['group_id'];
			$this->subject_id = $data['subject_id'];
			$this->type = $data['type'];
		}
		
		//visszaad egy értesítést id alapján
		public static function get($notification_id){
			$db = Database::getInstance();
			
			$stmt = $db->prepare(
				"SELECT * FROM notifications WHERE id = ?"
			);
			$stmt->execute(array($notification_id));
			
			return new Notification($stmt->fetch());
		}

		//visszaadja a felhasználó értesítéseit
		public static function getAll($user_id, $user_type){
			$db = Database::getInstance();
			
			if( $user_type == 1 ){
				$stmt = $db->prepare(
					"SELECT * FROM notifications WHERE author_id = ?"
				);
			} else{
				$stmt = $db->prepare(
					"SELECT * FROM notifications WHERE group_id IN (SELECT group_id FROM group_members WHERE user_id = ?)"
				);
			}
			
			$stmt->execute(array($user_id));
            $data = $stmt->fetchAll();

            $list = array();
            foreach( $data as $d ){
                $list[] = new Notification($d);
            }

            return $list;
		}
		
		//létrehoz egy értesítést az adattbázisban
		public static function create($data){
			$db = Database::getInstance();
			
			$stmt = $db->prepare(
				"INSERT INTO notifications(author_id, group_id, subject_id, text, date, type)".
				" VALUES(?, ?, ?, ?, ?, ?)"
			);
			$stmt->execute(array(
				$data['author_id'],
				$data['group_id'],
				$data['subject_id'],
				$data['text'],
				$data['date'],
				$data['type']
			));
		}
		
		//felhasználó alapján kivalasztja a megadott dátummal egyező értesítéseket
		public static function getAllByDate($user_id, $user_type, $date){
			$db = Database::getInstance();
			
			if( $user_type == 1 ){
				$stmt = $db->prepare(
					"SELECT * FROM notifications WHERE author_id = ? AND date = ?"
				);
			} else{
				$stmt = $db->prepare(
					"SELECT * FROM notifications WHERE group_id IN (SELECT group_id FROM group_members WHERE user_id = ?) AND date = ?"
				);
			}
			
			$stmt->execute(array($user_id, $date));
            $data = $stmt->fetchAll();

            $list = array();
            foreach( $data as $d ){
                $list[] = new Notification($d);
            }
            return $list;
		}

		//törli a megadott értesítést
		public static function delete($notification_id){
			$db = Database::getInstance();

			$stmt = $db->prepare(
				"DELETE FROM notifications WHERE id = ?"
			);
			$stmt->execute(array($notification_id));
		}
	
	}

?>