<?php

    class Answer{
        public $id;
        public $user_id;
        public $test_instance_id;

        //opcionális adattagok, nem minden válasznak van egységesen
        public $answer;
        public $is_correct;
        public $task_id;
        public $task_option_id;
        public $file_name;

        public function __construct($data){
            $this->id               = $data['id'];
            $this->user_id          = $data['user_id'];
            $this->test_instance_id = $data['test_instance_id'];

            $this->answer           = empty($data['answer'])?null:$data['answer'];
            $this->is_correct       = empty($data['is_correct'])?null:$data['is_correct'];
            $this->task_id          = empty($data['task_id'])?null:$data['task_id'];
            $this->task_option_id   = empty($data['task_option_id'])?null:$data['task_option_id'];
            $this->file_name        = empty($data['file_name'])?null:$data['file_name'];
        }

        //user válaszának lekérése opció id alapján
        public static function getByOptionId($user_id, $test_instance_id, $option_id){
            $db = Database::getInstance();

            $stmt = $db->prepare(
                "SELECT * FROM user_answers WHERE user_id = ? AND test_instance_id = ? AND task_option_id = ?"
            );
            $stmt->execute(array($user_id, $test_instance_id, $option_id));

            return new Answer($stmt->fetch());     
        }

        //user szöveges válaszának lekérése feladat alapján
        public static function getTextAnswer($user_id, $test_instance_id, $task_id){
            $db = Database::getInstance();

            $stmt = $db->prepare(
                "SELECT * FROM user_text_answers WHERE user_id = ? AND test_instance_id = ? AND task_id = ?"
            );
            $stmt->execute(array($user_id, $test_instance_id, $task_id));

            return new Answer($stmt->fetch());
        }

        //user fájl válaszának lekérése feladat alapján
        public static function getFileAnswer($user_id, $test_instance_id, $task_id){
            $db = Database::getInstance();

            $stmt = $db->prepare(
                "SELECT * FROM user_text_answers WHERE user_id = ? AND test_instance_id = ? AND task_id = ?"
            );
            $stmt->execute(array($user_id, $test_instance_id, $task_id));

            return new Answer($stmt->fetch());
        }

        // //user összes fájl válaszának lekérése feladatlap alapján
        // public static function getFileAnswers($user_id, $test_instance_id){
        //     $db = Database::getInstance();

        //     $stmt = $db->prepare(
        //         "SELECT * FROM user_file_answers WHERE user_id = ? AND test_instance_id = ?"
        //     );
        //     $stmt->execute(array($user_id, $test_instance_id));

        //     $data = $stmt->fetchAll();

        //     $list = array();
        //     foreach( $data as $d ){
        //         array_push($list, new Answer($d));
        //     }

        //     return $list;
        // }

        // //user összes szöevegs válaszának lekérése feladatlap alapján
        // public static function getTextAnswers($user_id, $test_instance_id){
        //     $db = Database::getInstance();

        //     $stmt = $db->prepare(
        //         "SELECT * FROM user_text_answers WHERE user_id = ? AND test_instance_id = ?"
        //     );
        //     $stmt->execute(array($user_id, $test_instance_id));

        //     $data = $stmt->fetchAll();

        //     $list = array();
        //     foreach( $data as $d ){
        //         array_push($list, new Answer($d));
        //     }

        //     return $list;
        // }

        public static function getFileAndTextAnswers($user_id, $test_instance_id){
            $db = Database::getInstance();

            $stmt = $db->prepare(
                "SELECT id, user_id, test_instance_id, task_id, file_name AS 'answer' FROM user_file_answers WHERE user_id = :uid AND test_instance_id = :tid".
                " UNION ALL".
                " SELECT id, user_id, test_instance_id, task_id, answer AS 'answer' FROM user_text_answers WHERE user_id = :uid AND test_instance_id = :tid"
            );
            $stmt->execute(array(':uid' => $user_id, ':tid' => $test_instance_id));

            $data = $stmt->fetchAll();

            $list = array();
            foreach( $data as $d ){
                array_push($list, new Answer($d));
            }

            return $list;
        }

        //opcióra adott válasz eltárolása
        public static function store($data){
			$db = Database::getInstance();
			
			$stmt = $db->prepare(
				"INSERT IGNORE INTO user_answers(user_id, task_option_id, test_instance_id, answer, is_correct)".
				" VALUES(?, ?, ?, ?, ?)"
			);
			$stmt->execute(array(
				$data['user_id'],
				$data['task_option_id'],
                $data['test_instance_id'],
                $data['answer'],
                $data['is_correct']
			));
        }

        //szöveges válasz eltárolása
        public static function storeText($data){
			$db = Database::getInstance();
			
			$stmt = $db->prepare(
				"INSERT IGNORE INTO user_text_answers(user_id, task_id, test_instance_id, answer)".
				" VALUES(?, ?, ?, ?)"
			);
			$stmt->execute(array(
				$data['user_id'],
				$data['task_id'],
                $data['test_instance_id'],
                $data['answer']
			));
        }

        //fájl válasz eltárolása
        public static function storeFile($data){
			$db = Database::getInstance();
			
			$stmt = $db->prepare(
				"INSERT IGNORE INTO user_file_answers(user_id, task_id, test_instance_id, file_name)".
				" VALUES(?, ?, ?, ?)"
			);
			$stmt->execute(array(
				$data['user_id'],
				$data['task_id'],
                $data['test_instance_id'],
                $data['file_name']
			));
        }


    }

?>