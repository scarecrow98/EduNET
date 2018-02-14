<?php

	class Subject{
		
		public $id;
		public $name;
		
		public function __construct($data){
			$this->id = $data['id'];
			$this->name = $data['name'];
		}
		
		public static function get($subject_id){
			$db = Database::getInstance();
			
			$stmt = $db->prepare(
				"SELECT * FROM subjects WHERE id = ?"
			);
			$stmt->execute(array($subject_id));
			
			return new Subject($stmt->fetch());
		}
		
		public static function all(){
			$db = Database::getInstance();
			
			$stmt = $db->prepare(
				"SELECT * FROM subjects"
			);
			$stmt->execute();
			
            $data = $stmt->fetchAll();

            $list = array();
            foreach( $data as $d ){
                array_push($list, new Subject($d));
            }

            return $list;
		}
	}

?>