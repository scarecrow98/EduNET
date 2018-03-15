<?php

    class Test{

        public $id;
        public $title;
        public $text;
        public $subject_id;
        public $task_count;

        public function __construct($data){
            $this->id = $data['id'];
            $this->title = $data['title'];
            $this->text = $data['text'];
            $this->subject_id = $data['subject_id'];
			$this->task_count = $data['task_count'];
		}
		
		public static function get($test_id){
            $db = Database::getInstance();
            $stmt = $db->prepare(
                "SELECT * FROM tests WHERE id = ?"
            );

            $stmt->execute(array($test_id));
            
            return new Test($stmt->fetch());
        }
		
		public function getTasks(){
			$db = Database::getInstance();

			$stmt = $db->prepare(
				"SELECT * FROM tasks WHERE test_id = ?"
			);
			$stmt->execute(array($this->id));

			$data = $stmt->fetchAll();

            $list = array();
            foreach( $data as $d ){
                array_push($list, new Task($d));
            }

            return $list;
		}
		
		//létrehoz egy új bázisfeladatlapot
		public static function create($data){
			$db = Database::getInstance();
			
			//bázisfeladatlap létrehozása egyszerű INSERT INTO utasítással
			$stmt = $db->prepare(
				"INSERT INTO tests(title, text, subject_id, task_count)".
				" VALUES(?, ?, ?, ?)"
			);
			$stmt->execute(array(
				$data['title'],
				$data['text'], 
				$data['subject_id'],
				$data['task_count']
			));
			
			//a létrehozott bázisfeladatlap ID-jének tárolása
			$last_insert_id = $db->lastInsertId();
			
			//session adatok tárolása, amik kellenek a feladatok
			//felvételéhez a feladatlapba
			Session::set('current-test-id', $last_insert_id);
			Session::set('current-task-number', 1);
			Session::set('total-task-count', $data['task_count']);
			
			//feladatlappéldány létrehozása
			$d = array(
				'current_author_id'		=> $data['author_id'],
				'original_author_id'	=> $data['author_id'],
				'test_id'				=> $last_insert_id,
				'group_id'				=> $data['group_id'],
				'creation_date'			=> date('Y-m-d'),
				'description'			=> $data['description']
			);
			
			TestInstance::create($d);
			
		}

		public function hasFileOrTextTypeTask(){
			$db = Database::getInstance();

			$stmt = $db->prepare(
				"SELECT COUNT(id) AS 'count' FROM tasks WHERE type = ? OR type = ? AND test_id = ?"
			);
			$stmt->execute(array(2, 5, $this->id));
			$data = $stmt->fetch();

			if( $data['count'] > 0 ){
				return true;
			}

			return false;
		}

    }

	
?>