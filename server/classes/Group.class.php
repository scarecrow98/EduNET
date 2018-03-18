<?php

	class Group{
		
		//adattagok
		public $author_id;
		public $name;
		public $id;
		public $description;
		public $avatar;
		public $member_count;
		
		// konstruktor
		public function __construct($data){
			$this->id = $data['id'];
			$this->name = $data['name'];
			$this->description = $data['description'];
			$this->author_id = $data['author_id'];
			$this->avatar = $data['avatar'];
			$this->members = $this->getMembers();
			$this->member_count = count($this->members);
		}
		
		// visszaad egy csoportobjektumot id alapján
		public static function get($group_id){
			$db = Database::getInstance();
			
			$stmt = $db->prepare(
				"SELECT * FROM groups WHERE id = ?"
			);
			$stmt->execute(array($group_id));
			
			return new Group($stmt->fetch());
		}
		
		// visszaadja a felhasználó csoportjait
		public static function getAll($user_id, $user_type){
			$db = Database::getInstance();
            $stmt;

            if( $user_type == 1 ){
                $stmt = $db->prepare(
                    "SELECT * FROM groups WHERE author_id = ?"
                );
            }else{
                $stmt = $db->prepare(
                    "SELECT * FROM groups WHERE id IN (SELECT group_id FROM group_members WHERE user_id = ?)"
                );
            }

            $stmt->execute(array($user_id));
            $data = $stmt->fetchAll();

            $list = array();
            foreach( $data as $d ){
                $list[] = new Group($d);
            }

            return $list;
		}
		
		// visszaadja az alkalmazás összes csoportját
		public static function all(){
			$db = Database::getInstance();

			$stmt = $db->prepare(
				"SELECT * FROM groups"
			);
            $stmt->execute();
            $data = $stmt->fetchAll();

            $list = array();
            foreach( $data as $d ){
                $list[] = new Group($d);
            }

            return $list;
		}
		
		// létrehoz egy csoportot
		public static function create($data){
			$db = Database::getInstance();
			
			$stmt = $db->prepare(
				"INSERT INTO groups(name, author_id, description, avatar)".
				" VALUES(?, ?, ?, ?)"
			);
			$stmt->execute(array(
				$data['name'],
				$data['author_id'],
				$data['description'],
				$data['avatar']
			));
		}

		// lekéri a csoport tagjait
		public function getMembers(){
			$db = Database::getInstance();
			
			$stmt = $db->prepare(
				"SELECT * FROM group_members".
				" INNER JOIN users ON users.id = group_members.user_id".
				" WHERE group_id = ?"
			);
			$stmt->execute(array($this->id));
			return $stmt->fetchAll();
		}
		
		//visszaadja a kereséi értéknek megfelelő diákokat, akik még nem a megadott csoport tagjai
		public static function searchUsers($student_name, $group_id){
			$db = Database::getInstance();

			$stmt = $db->prepare(
				"SELECT * FROM users".
				" WHERE users.name LIKE ?".
				" AND users.type = ?".
				" AND users.id NOT IN (SELECT user_id FROM group_members WHERE group_id = ?)"
			);
			$stmt->execute(array( '%'.$student_name.'%', 0, $group_id ));

			return $stmt->fetchAll();
		}

		// felvesz egy tagot a csoportba
		public static function addMember($group_id, $user_id){
			$db = Database::getInstance();
			
			$stmt = $db->prepare(
				"INSERT IGNORE INTO group_members(group_id, user_id)".
				" VALUES(?, ?)"
			);
			$stmt->execute(array($group_id, $user_id));
		}

		// töröl egy tagot a csoportból
		public static function deleteMember($group_id, $user_id){
			$db = Database::getInstance();
			
			$stmt = $db->prepare(
				"DELETE FROM group_members WHERE group_id = ? AND user_id = ?"
			);
			$stmt->execute(array($group_id, $user_id));
		}
		
	}

?>