<?php

    class TestInstance{

        public $id;
        public $test_id;
        public $current_author_id;
        public $original_author_id;
        public $group_id;
        public $creation_date;
        public $status;

        public function __construct($data){
            $this->id                   = $data['id'];
            $this->test_id              = $data['test_id'];
            $this->current_author_id    = $data['current_author_id'];
            $this->original_author_id   = $data['original_author_id'];
            $this->group_id             = $data['group_id'];
            $this->creation_date        = $data['creation_date'];
            $this->status               = $data['status'];
        }

        public static function get($test_instance_id){
            $db = Database::getInstance();

            $stmt = $db->prepare(
                "SELECT * FROM test_instances WHERE id = ?"
            );
            $stmt->execute(array($test_instance_id));

            return new TestInstance($stmt->fetch());
        }

        public static function getAll($user_id, $user_type){
            $db = Database::getInstance();
            $stmt;

            if( $user_type == 1 ){
                $stmt = $db->prepare(
                    "SELECT * FROM test_instances WHERE current_author_id = ? ORDER BY creation_date DESC"
                );
            } else {
                $stmt = $db->prepare(
                    "SELECT * FROM test_instances WHERE group_id IN (SELECT group_id FROM group_members WHERE user_id = ?) ORDER BY creation_date DESC"
                );
            }

            $stmt->execute(array($user_id));
            $data = $stmt->fetchAll();

            $list = array();
            foreach( $data as $d ){
                array_push($list, new TestInstance($d));
            }

            return $list;
        }
        
		public static function create($data){
			$db = Database::getInstance();
			
			$stmt = $db->prepare(
				"INSERT INTO test_instances(test_id, original_author_id, current_author_id, group_id, creation_date, description)".
				" VALUES(?, ?, ?, ?, ?, ?)"
			);
			$stmt->execute(array(
				$data['test_id'],
				$data['original_author_id'],
				$data['current_author_id'],
				$data['group_id'],
				$data['creation_date'],
				$data['description'],
			));
        }

        public static function filter($data, $user_type){
            $db = Database::getInstance();
            $stmt;

            if( $user_type == 1 ){
                $stmt = $db->prepare(
                    "SELECT * FROM test_instances INNER JOIN tests ON test_instances.test_id = tests.id WHERE".
                    " (tests.title LIKE :title OR :title IS NULL) AND".
                    " (tests.subject_id = :subjectid OR :subjectid IS NULL) AND".
                    " (test_instances.group_id = :groupid OR :groupid IS NULL) AND".
                    " (test_instances.creation_date LIKE :date OR :date IS NULL) AND".
                    " test_instances.current_author_id = :uid".
                    " ORDER BY creation_date DESC"
                );
            } else {
                $stmt = $db->prepare(
                    "SELECT * FROM test_instances INNER JOIN tests ON test_instances.test_id = tests.id WHERE".
                    " (tests.title LIKE :title OR :title IS NULL) AND".
                    " (tests.subject_id = :subjectid OR :subjectid IS NULL) AND".
                    " (test_instances.group_id = :groupid OR :groupid IS NULL) AND".
                    " (test_instances.creation_date LIKE :date OR :date IS NULL) AND".
                    " test_instances.group_id IN (SELECT group_id FROM group_members WHERE user_id = :uid)".
                    " ORDER BY creation_date DESC"
                );
            }
            $stmt->execute(array(
                ':title'        => '%'.$data['title'].'%',
                ':subjectid'    => $data['subject_id'],
                ':groupid'      => $data['group_id'],
                ':date'         => '%'.$data['date'].'%',
                ':uid'          => $data['user_id']
            ));
            
            $data = $stmt->fetchAll();

            $list = array();
            foreach( $data as $d ){
                array_push($list, new TestInstance($d));
            }

            return $list;
			
        }

        public static function setStatus($test_instance_id, $status){
            $db = Database::getInstance();

            $stmt = $db->prepare(
                "UPDATE test_instances SET status = ? WHERE id = ?"
            );
            $stmt->execute(array($status, $test_instance_id));
        }
        
        public function getStudents(){
            $db = Database::getInstance();

            $stmt = $db->prepare(
                "SELECT * FROM users WHERE id IN".
                " (SELECT user_id FROM group_members WHERE group_id = ?)"
            );
            $stmt->execute(array($this->group_id));
            $data = $stmt->fetchAll();

            $list = array();
            foreach( $data as $d ){
                array_push($list, new User($d));
            }

            return $list;
        }

        public function storeEvaluation($user_id, $date){
			$db = Database::getInstance();
			
			$stmt = $db->prepare(
				"INSERT IGNORE INTO evaluated_tests(user_id, test_instance_id, date)".
				" VALUES(?, ?, ?)"
			);
			$stmt->execute(array($user_id, $this->id, $date));
        }

        public static function getEvaluatedInstances($user_id){
            $db = Database::getInstance();

            $stmt = $db->prepare(
                "SELECT * FROM evaluated_tests WHERE user_id = ? ORDER BY date DESC LIMIT 3"
            );
            $stmt->execute(array($user_id));

            return $stmt->fetchAll();
        }

        public function checkCredentials($user_id){
            $students = $this->getStudents();
            foreach( $students as $student ){ 
                if( Session::get('user-id') == $student->id ){
                    return true;
                }
            }
            return false;
        }

        public function hasEvaluatedInstance($user_id){
            $db = Database::getInstance();

            $stmt = $db->prepare(
                "SELECT * FROM evaluated_tests WHERE user_id = ? AND test_instance_id = ?"
            );
            $stmt->execute(array($user_id, $this->id)); 

            return !empty($stmt->fetch());
        }

        public function hasResults($user_id){
            $db = Database::getInstance();

            $stmt = $db->prepare(
                "SELECT id FROM user_answers WHERE test_instance_id = ? AND user_id = ?".
                " UNION".
                " SELECT id FROM user_text_answers WHERE test_instance_id = ? AND user_id = ?".
                " UNION".
                " SELECT id FROM user_file_answers WHERE test_instance_id = ? AND user_id = ?"
            );
            $stmt->execute(array($this->id, $user_id, $this->id, $user_id, $this->id, $user_id));

            return !empty($stmt->fetch());
        }
    }

?>