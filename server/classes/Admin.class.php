<?php
    require_once('user.class.php');

    class Admin extends User{

        //konstuktor
        public function __construct($pdo, $uid){
            parent::__construct($pdo, $uid);
        }

        //=== csoportot (osztályt) létrehozó függvény ===
        public function createGroup($data){
            //csoport nevének megléte
            if( empty($data['name']) ){
                exit('A csoport nevét kötelező megadni!');
            }

            //csoport nevének hossza
            if( strlen($data['name']) > 50 ){
                exit('A csoport neve max. 50 karakter lehet!');
            }

            //csoport leírásának hossza
            if( strlen($data['description']) > 255 ){
                exit('A csoport leírása max. 255 karakter lehet!');
            }

            //feltöltött csoportkép ellenőrzése
            $image = 'group_default.png';
            if( !empty($data['image']) ){
                $image = $this->checkUploadedImage($data['image'], 'avatar');
            }

            //adatok beszúrása
            try{
                $stmt = $this->db->prepare(
                    "INSERT INTO groups (name,description,author,image) VALUES (:name,:description,:author, :image)"
                );
                $stmt->execute(array(
                    ':name'         => $data['name'],
                    ':description'  => $data['description'],
                    ':author'       => $this->uid,
                    ':image'        => $image
                ));
                $_SESSION['current-group-name'] = $data['name'];
                //válaszüzenet a kliensnek
                echo 'success';
            }
            catch(PDOException $e){
                exit($e->getMessage());
            }
        }

        // === csoportag hozzáadása ===
        public function addGroupMember($student_id, $group_id){
            try{
                //mivel a user_id és a group_id mezők UNIQE INDEX-xel rendelkeznek,
                //használhatjuk az IGNORE parancsot, hogy ne lehessen megduplázni a rekordot
                //abban az esetben, ha valaki többször kattint a Tanuló felvétele gombra
                $stmt = $this->db->prepare(
                    "INSERT IGNORE INTO group_members(user_id,group_id) VALUES(:userid,:groupid)"
                );
                $stmt->execute(array(
                    ':userid'    => $student_id,
                    ':groupid'      => $group_id
                ));
                echo 'success';
            }
            catch(PDOException $e){
                exit($e->getMessage());   
            }
        }

        //=== feladatlapot létrehozó függvény ===
        public function createTest($data){

            //kötelező mezők meglétének ellenőrzése (cím, osztály, tantárgy)
            if( empty($data['title']) || $data['class'] < 1 || $data['subject'] < 1 || empty($data['startdate']) ){
                exit('A feladatlap címe, csoportja, tantárgya és kezdési időpontja kötelezően megadandó mezők!');
            }

            //feladatok számának ellenőrzése (szám legyen és 1-nél nagyobb)
            if( !is_int($data['tasknumber']) || $data['tasknumber'] < 1 ){
                exit('A feladatok száma csak pozitív, 1-nél nagyobb szám lehet');
            }

            if( strlen($data['title']) > 50 ){
                exit('A feladatlap címe max. 50 karakter lehet');
            }

            if( strlen($data['description']) > 255 ){
                exit('A feladatlap leírása max. 255 karakter lehet');
            }

            //dátum ellenőrzése, hogy nem-e egy múltbéli időpont van megadva
            if( date('Y-m-d') > $data['startdate'] ){
                exit('Érvénytelen kezdési időpontot adtál meg!');
            }

            try{
                //tests táblához adás
                $stmt = $this->db->prepare(
                    "INSERT INTO tests (subject_id,title,description,text,tasks_num) VALUES (:subject_id,:title,:description,:text,:tasks_num)"
                );
                $stmt->execute(array(
                    ':subject_id'   => $data['subject'],
                    ':title'        => $data['title'],
                    ':description'  => $data['description'],
                    ':text'         => $data['text'],
                    ':tasks_num'    => $data['tasknumber'],
                ));
                $last_test_id = $this->db->lastInsertId();

                //test_authors táblához adás
                $stmt = $this->db->prepare(
                    "INSERT INTO test_authors (test_id,author_id,group_id,original_author_id,creation_date,start_date, status) VALUES (:testid,:authorid,:groupid,:originalauthorid,:creationdate,:startdate,:status)"
                );
                $stmt->execute(array(
                    ':testid'       => $last_test_id,
                    ':authorid'     => $this->uid,
                    ':groupid'      => $data['class'],
                    ':originalauthorid'   => $this->uid,
                    ':creationdate' => date("Y-m-d H:i:s"),
                    ':startdate'    => $data['startdate'],
                    ':status'       => '0'
                ));

                echo 'success';
            }
            catch(PDOException $e){
                exit($e->getMessage());
            }
        }

        //=== feladatlap megosztása függvény ===
        public function shareTest($test_id, $new_author_id, $group_id){
            
            if( $new_author_id == 0 ){
                exit('Válaszd ki, hogy kivel akarod megosztani a feladatlapot!');
            }

            if( $group_id == 0 ){
                exit('Válaszd ki, hogy melyik csoportba akarod megosztani a feladatlapot!');
            }

            
            try{
                $stmt = $this->db->prepare(
                    "INSERT INTO test_authors (test_id,author_id,group_id,original_author_id,creation_date,status) VALUES (:testid,:authorid,:groupid,:originalauthorid,:creationdate,:status)"
                );
                $stmt->execute(array(
                    ':testid'               => $test_id,
                    ':authorid'             => $new_author_id,
                    ':groupid'              => $group_id,
                    ':originalauthorid'     => $this->uid,
                    ':creationdate'         => date("Y-m-d H:i:s"),
                    ':status'               => '0'
                ));

                echo 'A feladatlap sikeresen megosztva!';
            }
            catch(PDOException $e){
                exit($e->getMessage());
            }
        }

        //=== feladatlap megnyitása ===
        public function setTestStatus($test_meta_id, $test_status){
            try{
                $stmt = $this->db->prepare(
                    "UPDATE test_authors SET status = :status WHERE id = :id"
                );
                $stmt->execute(array(
                    ':status'   => $test_status,
                    ':id'       => $test_meta_id
                ));
            }
            catch(PDOException $e){
                exit($e->getMessage());
            }
        }

        //=== feladatot létrehozó függvény ===
        public function createTask($data){

            //válaszok száma
            $answer_counter = count($data['option-answers']);

            //kérdés és feladatípus mező meglétének ellenőrzése
            if( empty($data['question']) || $data['type'] == 0 ){
                exit('A feladat kérdése és a típusa kötelezően megadandó mezők!');
            }

            //kérdés hosszának ellenőrzése
            if( strlen($data['question']) > 255 ){
                exit('A kérdés szövege max. 255 karakter lehet!');
            }

            //opciók hosszának ellenőrzése
            for( $i = 1; $i < $answer_counter; $i++ ){
                if( strlen($data['option-texts'][$i]) > 255 ){
                    exit('A feladatopciók szövege max. 255 karakter lehet!');
                }
                if( empty($data['option-texts'][$i]) ){
                    exit('Nem adtad meg az összes opció szövegét!');
                }
            }

            //kép ellenőrzése
            $image = '';
            if( !empty($data['image']) ){
                $image = $this->checkUploadedImage($data['image'], 'image');
            }

            //adatok beszúrása a 'test' és az 'options' táblába
            try{
                $stmt = $this->db->prepare(
                    "INSERT INTO tasks(task_number,question,text,test_id,max_points,correct_ans_num,type,image) VALUES(:tasknumber,:question,:text,:testid,:maxpoints,:correctansnum,:type,:image)"
                );
                $stmt->execute(array(
                    ':tasknumber'       => $_SESSION['current-task-number'],
                    ':question'         => $data['question'],
                    ':text'             => $data['text'],
                    ':testid'           => $_SESSION['current-test-id'],
                    ':maxpoints'        => $data['points'],
                    ':correctansnum'    => $answer_counter,
                    ':type'             => $data['type'],
                    ':image'            => $image
                ));
                //jelenlegi task id-jének lekérése
                $current_task_id = $this->db->lastInsertId();

                $stmt = $this->db->prepare(
                    "INSERT INTO options(task_id,text,correct_ans) VALUES(:taskid,:text,:correctans)"
                );
                for( $i = 1; $i < $answer_counter; $i++ ){
                    $stmt->execute(array(
                        ':taskid'       => $current_task_id,
                        ':text'         => $data['option-texts'][$i],
                        ':correctans'   => $data['option-answers'][$i]
                    ));
                }

                //feladat számának növelése
                $_SESSION['current-task-number']++;

                //ha a feladat száma nagyobb lett, mint a feladatlapnak megadott feladatszám
                //akkor feladatlap lezárása, ésvisszatérés az alkalmazásba
                if( $_SESSION['current-task-number'] > $_SESSION['total-task-number'] ){
                    $_SESSION['current-task-number'] = null;
                    $_SESSION['current-test-id'] = null;
                    $_SESSION['total-task-number'] = null;
                    exit('limit');
                }

                //válasz a kliensnek
                echo 'success';
            }
            catch(PDOException $e){
                exit($e->getMessage());
            }
        }

        public function createNotification($data){

            foreach( $data as $d ){
                if( empty($d) ){ exit('Minden mező kitöltése kötelező!'); }
            }

            if( !is_numeric($data['group']) || !is_numeric($data['subject']) ){
                exit('Érvénytelen adatok!');
            }

            if( date('Y-m-d') > $data['date'] ){
                exit('Érvénytelen kezdési időpontot adtál meg!');
            }

            try{
                $stmt = $this->db->prepare(
                    "INSERT INTO notifications(text, group_id, subject_id, type, author_id, date)".
                    " VALUES(:t, :g, :s, :type, :a, :d)"
                );
                $stmt->execute(array(
                    ':t'    => $data['text'],
                    ':g'    => $data['group'],
                    ':s'    => $data['subject'],
                    ':type' => $data['type'],
                    ':a'    => $this->uid,
                    ':d'    => $data['date']
                ));
            }
            catch(PDOException $e){
                exit($e->getMessage());
            }

            echo 'success';
        }

    }

?>