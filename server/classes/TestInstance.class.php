<?php

    class TestInstance{

        //adattagok
        public $id;
        public $test_id;
        public $current_author_id;
        public $original_author_id;
        public $group_id;
        public $creation_date;
        public $status;
		public $description;

        //konstruktor
        public function __construct($data){
            $this->id                   = $data['id'];
            $this->test_id              = $data['test_id'];
            $this->current_author_id    = $data['current_author_id'];
            $this->original_author_id   = $data['original_author_id'];
            $this->group_id             = $data['group_id'];
            $this->creation_date        = $data['creation_date'];
            $this->status               = $data['status'];
			$this->description			= empty($data['description']) ? null : $data['description'];
        }

        //visszaad egy feladatlappéldányt id alapján
        public static function get($test_instance_id){
            $db = Database::getInstance();

            $stmt = $db->prepare(
                "SELECT * FROM test_instances WHERE id = ?"
            );
            $stmt->execute(array($test_instance_id));

            return new TestInstance($stmt->fetch());
        }

		//visszaadja egy felhasználó feladatlapjait
        public static function getAll($user_id, $user_type){
            $db = Database::getInstance();
            $stmt;

			//ha tanár, akkor akkor kiválasztom a feladatlapokat,
			//aminek ő a jelenlegi felhasználója
            if( $user_type == 1 ){
                $stmt = $db->prepare(
                    "SELECT * FROM test_instances".
					" WHERE current_author_id = ?".
					" ORDER BY FIELD(status, 1) DESC, creation_date ASC"
                );
			//ha diák, akkor azokat a feladatlapokat választjuk ki,
			//amelyik csoportja megegyezik a diák csoportjaival
            } else {
                $stmt = $db->prepare(
                    "SELECT * FROM test_instances".
					" WHERE group_id IN (SELECT group_id FROM group_members WHERE user_id = ?)".
					" ORDER BY FIELD(status, 1) DESC, creation_date ASC"
                );
            }

            $stmt->execute(array($user_id));
            $data = $stmt->fetchAll();

			//lekért adatokból objektumok készítése
            $list = array();
            foreach( $data as $d ){
                $list[] = new TestInstance($d);
            }

            return $list;
        }
        
		//feladatlappéldányt létrehozó függvény
		public static function create($data){
			$db = Database::getInstance();
			
			//egyszerű INSERT INTO
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

        //feladatlapszűrő függvény
        public static function filter($data, $user_type){
            $db = Database::getInstance();
            $stmt;

            if( $user_type == 1 ){
                $stmt = $db->prepare(
                    "SELECT test_instances.id AS 'id',".
                    " test_instances.test_id AS 'test_id',".
                    " test_instances.original_author_id AS 'original_author_id',".
                    " test_instances.current_author_id AS 'current_author_id',".
                    " test_instances.group_id AS 'group_id',".
                    " test_instances.creation_date AS 'creation_date',".
                    " test_instances.description AS 'description',".
                    " test_instances.status AS 'status'".
                    " FROM test_instances INNER JOIN tests ON test_instances.test_id = tests.id WHERE".
                    " (tests.title LIKE :title OR :title IS NULL) AND".
                    " (tests.subject_id = :subjectid OR :subjectid IS NULL) AND".
                    " (test_instances.group_id = :groupid OR :groupid IS NULL) AND".
                    " (test_instances.creation_date LIKE :date OR :date IS NULL) AND".
                    " test_instances.current_author_id = :uid".
                    " ORDER BY creation_date DESC"
                );
            } else {
                $stmt = $db->prepare(
                    "SELECT test_instances.id AS 'id',".
                    " test_instances.test_id AS 'test_id',".
                    " test_instances.original_author_id AS 'original_author_id',".
                    " test_instances.current_author_id AS 'current_author_id',".
                    " test_instances.group_id AS 'group_id',".
                    " test_instances.creation_date AS 'creation_date',".
                    " test_instances.description AS 'description',".
                    " test_instances.status AS 'status'". 
                    " FROM test_instances INNER JOIN tests ON test_instances.test_id = tests.id WHERE".
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
                $list[] = new TestInstance($d);
            }

            return $list;
        }

        //beállítja a feladatlap státuszát a megadott értékre (0, 1, 2)
        public static function setStatus($test_instance_id, $status){
            $db = Database::getInstance();

            $stmt = $db->prepare(
                "UPDATE test_instances SET status = ? WHERE id = ?"
            );
            $stmt->execute(array($status, $test_instance_id));
        }
        
        //duplikál egy feladatlappéldányt megosztáskor
        public static function duplicate($data){
            $db = Database::getInstance();

            $stmt = $db->prepare(
                "INSERT INTO test_instances(test_id, original_author_id, current_author_id, group_id, creation_date, description)".
                " VALUES(?, ?, ?, ?, ?, ?)"
            );
            $stmt->execute(array(
                $data['test-id'],
                $data['original-author-id'],
                $data['current-author-id'],
                $data['group-id'],
                $data['date'],
                $data['description']
            ));
        }

        //visszaadja azon diákokat, akik benne vannak a feladatlap csoportjában (akik megírták)
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
                $list[] = new User($d);
            }

            return $list;
        }

        //eltárolja a feladatlap végleges kijavítának idejét diákonként
        public function storeEvaluation($user_id, $date){
			$db = Database::getInstance();
			
			$stmt = $db->prepare(
				"INSERT IGNORE INTO evaluated_tests(user_id, test_instance_id, date)".
				" VALUES(?, ?, ?)"
			);
			$stmt->execute(array($user_id, $this->id, $date));
        }

        //visszaadja a diák 3 legfrissebben kijavított feladatlapját az evaluated_tests táblából
        public static function getEvaluatedInstances($user_id){
            $db = Database::getInstance();

            $stmt = $db->prepare(
                "SELECT * FROM evaluated_tests WHERE user_id = ? ORDER BY date DESC LIMIT 3"
            );
            $stmt->execute(array($user_id));

            return $stmt->fetchAll();
        }

        //ellenőrzi, hogy a diáknak van-e jogosultsága a feladatlap megnyitásához
        public function checkCredentials($user_id){
            $students = $this->getStudents();
            foreach( $students as $student ){ 
                if( Session::get('user-id') == $student->id ){
                    return true;
                }
            }
            return false;
        }

        //visszaadja hogy egy diáknak ki lett-e már javítva a megadott feladatlapja
        public function hasEvaluatedInstance($user_id){
            $db = Database::getInstance();

            $stmt = $db->prepare(
                "SELECT * FROM evaluated_tests WHERE user_id = ? AND test_instance_id = ?"
            );
            $stmt->execute(array($user_id, $this->id)); 

            return ($stmt->rowCount() == 1);
        }

        //ellenprzi, hogy a diáknak tartoznak-e már eredményei a feladatlaphoz (hogy megírta-e már)
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