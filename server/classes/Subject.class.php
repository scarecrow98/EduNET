<?php

	class Subject{
		
		//adattagok
		public $id;
		public $name;
		
		//konstruktor
		public function __construct($data){
			$this->id = $data['id'];
			$this->name = $data['name'];
		}
		
		//visszaad egy tantárgyobjektumot azonosító alapján
		public static function get($subject_id){
			$db = Database::getInstance();
			
			$stmt = $db->prepare(
				"SELECT * FROM subjects WHERE id = ?"
			);
			$stmt->execute(array($subject_id));
			
			return new Subject($stmt->fetch());
		}

		//visszaadja az összes tantárgyat
		public static function all(){
			$db = Database::getInstance();
			
			$stmt = $db->prepare(
				"SELECT * FROM subjects"
			);
			$stmt->execute();
			
            $data = $stmt->fetchAll();

            $list = array();
            foreach( $data as $d ){
                $list[] = new Subject($d);
            }

            return $list;
		}
	}

?>