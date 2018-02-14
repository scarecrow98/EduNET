<?php

    class User {

		public $id;
		public $login_id;
		public $name;
		public $email;
		public $is_subscribed;
		public $is_online;
		public $avatar;
		public $type;
		public $pass_hash;
		public $pass_salt;


        public function __construct($data){
			$this->id = $data['id'];
			$this->login_id = $data['login_id'];
			$this->name = $data['name'];
			$this->email = $data['email'];
			$this->is_subscribed = $data['is_subscribed'];
			$this->is_online = $data['is_online'];
			$this->avatar = $data['avatar'];
			$this->type = $data['type'];
			$this->pass_salt = $data['pass_salt'];
			$this->pass_hash = $data['pass_hash'];
        }
		
		public static function get($user_id){
			$db = Database::getInstance();
			
			$stmt = $db->prepare(
				"SELECT * FROM users WHERE id = ?"
			);
			$stmt->execute(array($user_id));
			
			return new User($stmt->fetch());
		}
		
		public static function getByLogin($login_id){
			$db = Database::getInstance();
			
			$stmt = $db->prepare(
				"SELECT * FROM users WHERE login_id = ?"
			);
			$stmt->execute(array($login_id));
			
			return new User($stmt->fetch());
		}
		
		public static function updateStatus($user_id, $status){
			$db = Database::getInstance();
			
			$stmt = $db->prepare(
				"UPDATE users SET is_online = ? WHERE id = ?"
			);
			$stmt->execute(array($status, $user_id));
		}
		
		public static function getByType($user_type){
			$db = Database::getInstance();
			
			$stmt = $db->prepare(
				"SELECT * FROM users WHERE type = ?"
			);
			$stmt->execute(array($user_type));
			
            $data = $stmt->fetchAll();

            $list = array();
            foreach( $data as $d ){
                array_push($list, new User($d));
            }

            return $list;
		}
		
		
		
		
		
		
		
		

        public function getAllTeachers(){
            try{
                $result = $this->db->query("SELECT * FROM users WHERE type = '1' ORDER BY name ASC");
                return $result;
            }
            catch(PDOException $e){
                exit($e->getMessage());
            }  
        }


        //csoporttagok listázása
        public function getGroupMembers($group_id){
            try{
                $stmt = $this->db->prepare(
                    "SELECT name, avatar, id FROM users".
                    " INNER JOIN group_members ON group_members.user_id = users.id".
                    " WHERE group_members.group_id = :groupid"
                );
                $stmt->execute(array( ':groupid' => $group_id));
                $result = $stmt->fetchAll();
                return $result;
            }
            catch(PDOException $e){
                exit($e->getMessage());
            }
        }

        //csoporttag törlése
        public function deleteGroupMember($group_id, $user_id){
            try{
                $stmt = $this->db->prepare(
                    "DELETE FROM group_members WHERE group_id = :groupid AND user_id = :userid"
                );
                $stmt->execute(array(':userid' => $user_id, ':groupid' => $group_id));
                echo 'success';
            }
            catch(PDOException $e){
                exit($e->getMessage());
            }
        }

        //egy teszt lekérésée ID alapján, a hozzátartozó feladatokkal együtt
        public function getTestMetaById($test_id){
            try{
                $stmt = $this->db->prepare(
                    "SELECT * FROM test_authors WHERE id = :testid"
                );
                $stmt->execute(array( ':testid' => $test_id ));
                return $stmt->fetch();
            }
            catch(PDOException $e){
                exit($e->getMessage());
            }
        }

        public function getTestById($test_id){
            try{
                $stmt = $this->db->prepare(
                    "SELECT * FROM tests WHERE id = :testid"
                );
                $stmt->execute(array( ':testid' => $test_id ));
                return $stmt->fetch();
            }
            catch(PDOException $e){
                exit($e->getMessage());
            }
        }

        //tanár feladatlapjainak lekérése szerző alapján
        public function getTestsByAuthor(){
            try{
                $stmt = $this->db->prepare(
                    "SELECT DISTINCT * FROM test_authors".
                    " INNER JOIN tests ON test_authors.test_id = tests.id".
                    " INNER JOIN groups ON test_authors.group_id = groups.id".
                    " INNER JOIN subjects ON tests.subject_id = subjects.id".
                    " INNER JOIN users ON test_authors.original_author_id = users.id".
                    " WHERE test_authors.author_id = :uid".
                    " ORDER BY test_authors.creation_date DESC"
                );
                $stmt->execute(array( ':uid' => $this->uid ));
                return $stmt->fetchAll();
            }
            catch(PDOException $e){
                exit($e->getMessage());
            }
        }

        //diákok feladatlapjainak lekérése csoporttagság alapján
        public function getTestsByMembership(){
            try{
                $stmt = $this->db->prepare(
                    "SELECT DISTINCT * FROM test_authors".
                    " INNER JOIN tests ON test_authors.test_id = tests.id".
                    " INNER JOIN groups ON test_authors.group_id = groups.id".
                    " INNER JOIN subjects ON tests.subject_id = subjects.id".
                    " WHERE test_authors.group_id IN (SELECT group_id FROM group_members WHERE user_id = :uid)".
                    " ORDER BY test_authors.creation_date DESC"
                );
                $stmt->execute(array( ':uid' => $this->uid ));
                return $stmt->fetchAll();
            }
            catch(PDOException $e){
                exit($e->getMessage());
            }
        }

        //feladatok listázása feladatlap ID alapján
        public function getTasksByTestId($test_id){
            try{
                $stmt = $this->db->prepare(
                    "SELECT * FROM tasks WHERE test_id = :testid ORDER BY task_number ASC"
                );
                $stmt->execute(array( ':testid' => $test_id ));
                return $stmt->fetchAll();
            }
            catch(PDOException $e){
                exit($e->getMessage());
            }
        }

        //opciók listázása feladat ID alapján
        public function getOptionsByTaskId($task_id){
            try{
                $stmt = $this->db->prepare(
                    "SELECT * FROM options WHERE task_id = :taskid ORDER BY id ASC"
                );
                $stmt->execute(array( ':taskid' => $task_id ));
                return $stmt->fetchAll();
            }
            catch(PDOException $e){
                exit($e->getMessage());
            }
        }

        //visszatér az összes tantárggyal az adatbázisból
        public function getSubjects(){
            try{
                $result = $this->db->query('SELECT * FROM subjects ORDER BY name ASC');
                return $result;
            }
            catch(PDOException $e){
                exit($e->getMessage());
            }
        }

        //feladatlapszűrő a diák fióknál
        public function getTestsByStudentSearc($title, $gid, $sid, $date){
            try{
                $stmt = $this->db->prepare(
                    "SELECT DISTINCT * FROM test_authors".
                    " INNER JOIN tests ON test_authors.test_id = tests.id".
                    " INNER JOIN groups ON test_authors.group_id = groups.id".
                    " INNER JOIN subjects ON tests.subject_id = subjects.id".
                    " INNER JOIN users ON test_authors.original_author_id = users.id".
                    " WHERE test_authors.group_id IN (SELECT group_id FROM group_members WHERE user_id = :uid) AND".
                    " (tests.title LIKE :title OR :title IS NULL) AND".
                    " (test_authors.group_id = :groupid OR :groupid IS NULL) AND".
                    " (tests.subject_id = :subjectid OR :subjectid IS NULL) AND".
                    " (test_authors.creation_date LIKE :date OR :date IS NULL)".
                    " ORDER BY test_authors.creation_date DESC"
                );
                $stmt->execute(array(
                    ':uid'          => $this->uid,
                    ':title'        => '%'.$title.'%',
                    ':groupid'      => $gid,
                    ':subjectid'    => $sid,
                    ':date'         => '%'.$date.'%'
                ));

                return $stmt->fetchAll();
            }
            catch(PDOException $e){
                exit($e->getMessage());
            }
        }

        //feladatlap szűrő a tanári fióknál
        public function getTestsByTeacherSearc($title, $gid, $sid, $date){
            try{
                $stmt = $this->db->prepare(
                    "SELECT DISTINCT * FROM test_authors".
                    " INNER JOIN tests ON test_authors.test_id = tests.id".
                    " INNER JOIN groups ON test_authors.group_id = groups.id".
                    " INNER JOIN subjects ON tests.subject_id = subjects.id".
                    " INNER JOIN users ON test_authors.original_author_id = users.id".
                    " WHERE test_authors.author_id = :uid AND".
                    " (tests.title LIKE :title OR :title IS NULL) AND".
                    " (test_authors.group_id = :groupid OR :groupid IS NULL) AND".
                    " (tests.subject_id = :subjectid OR :subjectid IS NULL) AND".
                    " (test_authors.creation_date LIKE :date OR :date IS NULL)".
                    " ORDER BY test_authors.creation_date DESC"
                );
                $stmt->execute(array(
                    ':title'        => '%'.$title.'%',
                    ':uid'          => $this->uid,
                    ':groupid'      => $gid,
                    ':subjectid'    => $sid,
                    ':date'         => '%'.$date.'%'
                ));

                return $stmt->fetchAll();
            }
            catch(PDOException $e){
                exit($e->getMessage());
            }
        }

        //üzenetek lekérése
        public function getMessages(){
            try{
                $result = $this->db->query(
                    "SELECT * FROM messages".
                    " INNER JOIN users ON users.id = messages.sender_id".
                    " WHERE receiver_id = ".$this->uid." ORDER BY date DESC"
                );
                return $result;
            }
            catch(PDOException $e){
                exit($e->getMessage());
            }
        }

        //üzenetek 'kiküldött'-re állítása
        public function setMessagesToDelivered(){
            try{
                $result = $this->db->query(
                    "UPDATE messages SET is_new = '0' WHERE receiver_id = ".$this->uid
                );
                return $result;
            }
            catch(PDOException $e){
                exit($e->getMessage());
            }
        }





        //=== feltöltött képet ellenőrző függvény ===
        protected function checkUploadedImage($image, $type){
            $allowed_extensions = array(
                "image/jpeg"    =>  "jpg",
                "image/png"     =>  "png",
                "image/gif"     =>  "gif"
            );
            $max_size = 5 * 1024 * 1024;

            $img_size = $image['size'];
            $img_name = $image['name'];
            $img_tmp = $image['tmp_name'];
            $img_ext = strtolower( pathinfo($img_name)['extension'] );
            $img_mime = getimagesize($img_tmp)['mime'];

            if( !is_uploaded_file($img_tmp) ){
                exit('Valami hiba történt a feltöltés során!');
            }

            if( $img_size > $max_size ){
                exit('A kép mérete nem lehet nagyobb, mint 5MB!');
            }

            if( !in_array($img_ext, $allowed_extensions) ){
                exit('Csak png, jpg és gif fájlok megengedettek!');
            }

            if( empty($allowed_extensions[$img_mime]) ){
                exit('A fájlod, amit feltlölteni próbálsz nem képformátum!');
            }

            $new_base_name = hash('md5', $img_tmp.microtime(true) );
            $new_image_name = $new_base_name . '.' . $img_ext;

            switch( $type ){
                case 'avatar':
                    $image_dir = 'avatars/';
                break;
                case 'image':
                    $image_dir = 'images/';
                break;
            }

            if( $type == 'avatar' ){
                $image_dir = 'avatars/';
            }
            else if( $type == 'image' ){
                $image_dir = 'images/';
            }

            move_uploaded_file($img_tmp, $_SERVER['DOCUMENT_ROOT'].'/eTest/uploads/'.$image_dir.$new_image_name);
            return $new_image_name;

        }

        protected function checkUploadedFile($file){
            $allowed_file_types = array(
                'application/octet-stream',
                'application/rar',
                'application/zip',
                'application/x-zip-compressed'
            );

            $allowed_file_extensions = array('zip', 'rar');

            $max_file_size = 5 * 1024 * 1024;

            $file_size = $file['size'];
            $file_name = $file['name'];
            $file_tmp = $file['tmp_name'];
            $file_type = $file['type'];
            $file_ext = strtolower(pathinfo($file_name)['extension']);

            if( !is_uploaded_file($file_tmp) ){
                exit('Valami hiba történt a feltöltés során!');
            }

            if( $file_size > $max_file_size ){
                exit('A kép mérete nem lehet nagyobb, mint 5MB!');
            }

            if( !in_array($file_ext, $allowed_file_extensions) ){
                exit('Csak rar és zip fájlok megengedettek!');
            }

            if( !in_array($file_type, $allowed_file_types) ){
                exit('A fájlod, amit feltlölteni próbálsz nem megfelelő formátumú!'.$file_type);
            }

            $new_base_name = hash('md5', $file_tmp.microtime(true) );
            $new_file_name = $new_base_name . '.' . $file_ext;

            move_uploaded_file($file_tmp, $_SERVER['DOCUMENT_ROOT'].'/eTest/uploads/files/'.$new_file_name);
            return 'uploads/files/'.$new_file_name;

        }

        public function getUser($uid){
            try{
                $stmt = $this->db->prepare(
                    "SELECT * FROM users WHERE id = :uid"
                );
                $stmt->execute(array(':uid' => $uid));

                return $stmt->fetch();
            }
            catch(PDOException $e){
                exit($e->getMessage());
            }
        }
        
        public function getStudentNotifications($date){
            try{
                $stmt = $this->db->prepare(
                    "SELECT notifications.date, notifications.type, notifications.text, subjects.name AS 'subject', groups.name AS 'group'  FROM notifications".
                    " INNER JOIN subjects ON subjects.id = notifications.subject_id".
                    " INNER JOIN groups ON groups.id = notifications.group_id".
                    " WHERE notifications.date = :date AND notifications.group_id IN (SELECT group_id FROM group_members WHERE user_id = :uid)".
                    " ORDER BY notifications.date DESC"
                );
                $stmt->execute(array(':date' => $date, ':uid' => $this->uid));

                return $stmt->fetchAll();
            }
            catch(PDOException $e){
                exit($e->getMessage());
            }
        }

        public function getAdminNotifications($date){
            try{
                $stmt = $this->db->prepare(
                    "SELECT notifications.date, notifications.type, notifications.text, subjects.name AS 'subject', groups.name AS 'group'  FROM notifications".
                    " INNER JOIN subjects ON subjects.id = notifications.subject_id".
                    " INNER JOIN groups ON groups.id = notifications.group_id".
                    " WHERE notifications.date = :date AND notifications.author_id = :uid".
                    " ORDER BY notifications.date DESC"
                );
                $stmt->execute(array(':date' => $date, ':uid' => $this->uid));

                return $stmt->fetchAll();
            }
            catch(PDOException $e){
                exit($e->getMessage());
            }
        }


    }

?>