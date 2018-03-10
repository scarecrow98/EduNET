<?php

	class Group{
		
		public $author_id;
		public $name;
		public $id;
		public $description;
		public $avatar;
		public $member_count;
		
		public function __construct($data){
			$this->id = $data['id'];
			$this->name = $data['name'];
			$this->description = $data['description'];
			$this->author_id = $data['author_id'];
			$this->avatar = $data['avatar'];
			$this->members = $this->getMembers();
			$this->member_count = count($this->members);
		}
		
		public static function get($group_id){
			$db = Database::getInstance();
			
			$stmt = $db->prepare(
				"SELECT * FROM groups WHERE id = ?"
			);
			$stmt->execute(array($group_id));
			
			return new Group($stmt->fetch());
		}
		
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
                array_push($list, new Group($d));
            }

            return $list;
		}
		
		public static function all(){
			$db = Database::getInstance();

			$stmt = $db->prepare(
				"SELECT * FROM groups"
			);
            $stmt->execute();
            $data = $stmt->fetchAll();

            $list = array();
            foreach( $data as $d ){
                array_push($list, new Group($d));
            }

            return $list;
		}
		
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
		
		/*public function members(){
			$db = Database::getInstance();
			
			$stmt = $db->prepare(
				"SELECT * FROM group_members".
				" INNER JOIN users ON users.id = group_members.user_id".
				" WHERE group_id = ?"
			);
			$stmt->execute(array($this->id));
			$data = $stmt->fetchAll();
			
			$list = array();
            foreach( $data as $d ){
                array_push($list, new User($d));
            }
		}*/

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
		
		public static function searchUsers($student_name, $group_id){
			$db = Database::getInstance();

			// csak azokat a tanulókat adjuk vissza, amik nem tagjai még a csoportnak
			$stmt = $db->prepare(
				"SELECT * FROM users".
				" WHERE users.name LIKE ?".
				" AND users.type = ?".
				" AND users.id NOT IN (SELECT user_id FROM group_members WHERE group_id = ?)"
			);
			$stmt->execute(array( '%'.$student_name.'%', 0, $group_id ));

			return $stmt->fetchAll();
		}

		public static function addMember($group_id, $user_id){
			$db = Database::getInstance();
			
			$stmt = $db->prepare(
				"INSERT IGNORE INTO group_members(group_id, user_id)".
				" VALUES(?, ?)"
			);
			$stmt->execute(array($group_id, $user_id));
		}

		public static function deleteMember($group_id, $user_id){
			$db = Database::getInstance();
			
			$stmt = $db->prepare(
				"DELETE FROM group_members WHERE group_id = ? AND user_id = ?"
			);
			$stmt->execute(array($group_id, $user_id));
		}
		
	}

?>