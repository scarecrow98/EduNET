<?php

	class TaskOption{
		
		public $id;
		public $task_id;
		public $text;
		public $correct_ans;

		public function __construct($data){
			$this->id 			= $data['id'];
			$this->task_id 		= $data['task_id'];
			$this->text		 	= $data['text'];
			$this->correct_ans 	= $data['correct_ans'];
		}

		
		public static function get($option_id){
			$db = Database::getInstance();
			
			$stmt = $db->prepare(
				"SELECT * FROM task_options WHERE id = ?"
			);
			$stmt->execute(array($option_id));

			return new TaskOption($stmt->fetch());
		}

		public static function create($data){
			$db = Database::getInstance();
			
			$stmt = $db->prepare(
				"INSERT INTO task_options(task_id, text, correct_ans)".
				" VALUES(?, ?, ?)"
			);
			$stmt->execute(array(
				$data['task_id'],
				$data['text'],
				$data['correct_ans']
			));
		}
	}

?>