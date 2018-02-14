<?php

	class Task{
		
		public $id;
		public $test_id;
		public $task_number;
		public $question;
		public $text;
		public $max_points;
		public $image;
		public $type;

		public $option_count;
		
		
		public function __construct($data){
			$this->id 					= $data['id'];
			$this->test_id 				= $data['test_id'];
			$this->task_number 			= $data['task_number'];
			$this->question 			= $data['question'];
			$this->text 				= $data['text'];
			$this->max_points 			= $data['max_points'];
			$this->image 				= $data['image'];
			$this->type 				= $data['type'];

			$this->option_count			= $this->getOptionCount();
		}

		public function getTaskOptions(){
			$db = Database::getInstance();

			$stmt = $db->prepare(
				"SELECT * FROM task_options WHERE task_id = ?"
			);
			$stmt->execute(array($this->id));

			$data = $stmt->fetchAll();

            $list = array();
            foreach( $data as $d ){
                array_push($list, new TaskOption($d));
            }

            return $list;
		}
		
		public static function get($task_id){
			$db = Database::getInstance();
			
			$stmt = $db->prepare(
				"SELECT * FROM tasks WHERE id = ?"
			);
			$stmt->execute(array($task_id));
			
			return new Task($stmt->fetch());
			
		}
		
		public static function create($data){
			$db = Database::getInstance();
			
			$stmt = $db->prepare(
				"INSERT INTO tasks(test_id, task_number, question, text, max_points, image, type)".
				" VALUES(?, ?, ?, ?, ?, ?, ?)"
			);
			$stmt->execute(array(
				Session::get('current-test-id'),
				Session::get('current-task-number'),
				$data['question'],
				$data['text'],
				$data['max_points'],
				$data['image'],
				$data['type'],
			));
			
			$last_insert_id = $db->lastInsertId();
			
			for( $i = 1; $i < count($data['option_answers']); $i++ ){
				$stmt = $db->prepare(
					"INSERT INTO task_options(task_id, text, correct_ans)".
					" VALUES(?, ?, ?)"
				);
				
				$d = array(
					'task_id'		=> $last_insert_id,
					'text'			=> $data['option_texts'][$i],
					'correct_ans'	=> $data['option_answers'][$i]
				);
				TaskOption::create($d);
			}
		}

		private function getOptionCount(){
			$db = Database::getInstance();

			$stmt = $db->prepare(
				"SELECT COUNT(id) AS 'option_count' FROM task_options WHERE task_id = ?"
			);
			$stmt->execute(array($this->id));

			$data = $stmt->fetch();

			if( empty($data['option_count']) ){
				return 0;
			}

			return $data['option_count'];
		}

		public function storeResult($data){
			$db = Database::getInstance();
			
			$stmt = $db->prepare(
				"INSERT IGNORE INTO task_results(user_id, task_id, test_instance_id, result, comment)".
				" VALUES(?, ?, ?, ?, ?)"
			);
			$stmt->execute(array(
				$data['user_id'],
				$this->id,
				$data['test_instance_id'],
				$data['result'],
				$data['comment']
			));
		}

		public static function getResult($task_id, $test_instance_id, $user_id){
			$db = Database::getInstance();
			
			$stmt = $db->prepare(
				"SELECT * FROM task_results WHERE task_id = ? AND test_instance_id = ? AND user_id = ?"
			);
			$stmt->execute(array($task_id, $test_instance_id, $user_id));
			
            return $stmt->fetch();
		}

		public static function storeComment($data){
			$db = Database::getInstance();
			
			$stmt = $db->prepare(
				"INSERT IGNORE INTO task_results(user_id, task_id, test_instance_id, result)".
				" VALUES(?, ?, ?, ?)"
			);
			$stmt->execute(array(
				$data['user_id'],
				$data['task_id'],
				$data['test_instance_id'],
				$data['result'],
			));
		}
	}

?>