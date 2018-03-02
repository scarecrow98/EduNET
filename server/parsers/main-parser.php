<?php

	require_once '../../config.php';
    Session::start();
    
    if( Security::checkAccessToken() === false ){
        exit();
    }
    
    // ===========================
    // ÜZENET KÜLDÉSE
    // ===========================
	if( isset($_POST['new-message']) ){
		$partner_id = $_POST['partner-id'];
		$text = $_POST['text'];
		
		if( empty($partner_id) ){	exit('Az üzenet címzettje nincs kiválasztva!'); }
		if( !is_numeric($partner_id) ){ exit('A megadott címzett nem található!'); }
		if( empty($text) ){	exit('Az üzenet szövege nincs megadva!'); }
		
		$data = array(
			'sender_id'		=> Session::get('user-id'),
			'receiver_id'	=> $partner_id,
            'text'			=> $text,
            'date'          => date('Y-m-d H:i:s')
		);
		
		Message::create($data);
		exit('success');
		
	}
	
	// ===========================
    // ÜZENETEK FIGYELÉSE
    // ===========================
	if( isset($_POST['has-new-message']) ){
		$messages = Message::getNews(Session::get('user-id'));
		$resp = array();
		
		foreach( $messages as $message ){
			$sender = User::get($message->sender_id);
			
			$resp[] = array(
				'sender_name'	=> $sender->name,
                'sender_avatar'	=> $sender->avatar,
                'sender_id'     => $sender->id,
				'text'			=> $message->text,
				'id'			=> $message->id,
				'date'			=> $message->date
			);
		}
		
		exit(json_encode($resp));
		
	}
	
	// ===========================
    // ÜZENET 'OLVASOTTÁ' ÁLLÍTÁSA, ÉS PÁRBESZÉD LEKÉRÉSE
    // ===========================
	if( isset($_POST['get-conversation']) ){

        //lekérjük a partner és a köztünk lévő pádbeszédet
        $partner_id = $_POST['partner-id'];        
        $messages = Message::getConversation(Session::get('user-id'), $partner_id);


        //ha olvasattá kell állítani üzeneteket
        $affected_messages = 0;
        if( !empty($_POST['set-to-seen']) ) {
            $affected_messages = Message::setToSeen(Session::get('user-id'), $partner_id);
        }

        $msgs = array();
        foreach( $messages as $message ){
            $msgs[] = array(
                'is_own'    => $message->sender_id == Session::get('user-id') ? 1 : 0,
                'text'      => $message->text,
                'date'      => $message->date
            );
        }

        $resp = array(
            'messages'          => json_encode($msgs),
            'affected_messages' => $affected_messages
        );

        exit(json_encode($resp));
		
	}
	
    // ===========================
    // ÚJ FELADATLAP LÉTREHOZÁSA
    // ===========================
    if( !empty($_POST['create-new-test']) ){
		
        if( empty($_POST['title']) ){ exit('A feladatlap címe kötelezően megadandó mező!'); }
        if( empty($_POST['group-id']) ){ exit('A feladatlap csoportja kötelezően megadandó mező!'); }
        if( empty($_POST['subject-id']) ){ exit('A feladatlap tantárgya kötelezően megadandó mező!'); }
		if( strlen($_POST['title']) > 100 ){ exit('A feladatlap címe max. 100 karakter lehet!'); }
        if( strlen($_POST['description']) > 255 ){ exit('A feladatlap leírása max. 255 karakter lehet!'); }
        if( $_POST['task-count'] > 30 || $_POST['task-count'] < 1 ){ exit('A feladatok száma 1 és 30 között kell legyen!'); }
				
        $data = array(
			'author_id'		=> Session::get('user-id'),
            'title'         => $_POST['title'],
            'description'   => $_POST['description'],
            'text'          => $_POST['text'],
            'group_id'      => (int)$_POST['group-id'],
            'subject_id'    => (int)$_POST['subject-id'],
            'task_count'    => (int)$_POST['task-count'],
        );
        		
		Test::create($data);
		
		exit('success');
    }
	
	
	// ===========================
    // ÚJ ÉRTESÍTÉS LÉTREHOZÁSA
    // =========================== 
    if( isset($_POST['create-new-notification']) ){
		
		foreach( $_POST as $n ){
			if( empty($n) ){ exit('Minden mező kitöltése kötelező!'); }
		}
		
        if( strlen($_POST['text']) > 100 ){ exit('Az értesítés címe max. 100 karakter lehet!'); }
        if( date('Y-m-d') > $_POST['date'] ){ exit('Érvénytelen kezdési időpontot adtál meg!'); }
				
        $data = array(
			'author_id'		=> Session::get('user-id'),
            'text'     		=> $_POST['text'],
            'subject_id'   	=> $_POST['subject_id'],
			'group_id'		=> $_POST['group_id'],
            'text'      	=> $_POST['text'],
            'date'      	=> $_POST['date'],
            'type'      	=> $_POST['type']
        );

        Notification::create($data);
		exit('success');
    }

    // ===========================
    // ÉRTESÍTÉS TÖRLÉSE
    // =========================== 
    if( !empty($_POST['delete-notification']) ){
        $notification_id = $_POST['notification-id'];

        Notification::delete($notification_id);
        exit('success');
    }
    
	// ===========================
    // ÚJ FELADAT LÉTREHOZÁSA
    // ===========================    
    if( !empty($_POST['create-new-task']) ){
		
        $image = null;
		
		if( !empty($_FILES['image']) && $_FILES['image']['name'] != '' ){
			$fu = new FileUploader($_FILES['image'], 'image', 'task_image'); 	
			$image = $fu->checkFile();
		}
		
		
        $data = array(
            'question'          => $_POST['question'],
            'text'              => $_POST['text'],
            'type'              => $_POST['type'],
            'max_points'        => $_POST['max_points'],
            'image'             => $image,
            'option_texts'      => json_decode($_POST['option_texts']),
            'option_answers'    => json_decode($_POST['option_answers']),
        );
        
		Task::create($data);
		
		$current_task_number = Session::get('current-task-number');
		$current_task_number++;
		Session::set('current-task-number', $current_task_number);
		
		if( Session::get('current-task-number') > Session::get('total-task-count') ){ exit('end'); }
		
		exit('success');
    }

    // ===========================
    // ÚJ CSOPORT LÉTREHOZÁSA
    // ===========================
    if( !empty($_POST['create-new-group']) ){
        $avatar = 'group-default.png';
        
		if( !empty($_FILES['avatar']) && $_FILES['avatar']['name'] != '' ){
			$fu = new FileUploader($_FILES['avatar'], 'image', 'avatar'); 	
			$avatar = $fu->checkFile();
		}
		
        $data = array(
            'name'          => $_POST['name'],
            'author_id'     => Session::get('user-id'),
            'description'   => $_POST['description'],
            'avatar'        => $avatar
        );
        
        Group::create($data);
        exit('success');
    }

    // ===========================
    // CSOPORTTAG FELVÉTELE
    // ===========================    
    if( !empty($_POST['add-group-member']) ){
        $student_id = $_POST['student-id'];
        $group_id = $_POST['group-id'];

        Group::addMember($group_id, $student_id);
        exit('success');
    }

    // ===========================
    // CSOPORTTGATOK LEKÉRÉSE
    // =========================== 
    if( !empty($_POST['list-group-members']) ){
        $group_id = $_POST['group-id'];

        $group = Group::get($group_id);
        $members = $group->getMembers();
        exit(json_encode($members));
    }

    // ===========================
    // CSOPORTTAG TÖRLÉSE
    // =========================== 
    if( !empty($_POST['delete-group-member']) ){
        $group_id = $_POST['group-id'];
        $user_id = $_POST['user-id'];

        Group::deleteMember($group_id, $user_id);
        exit('success');
    }

    // ===========================
    // FELADATLAP NYITÁSA/ZÁRÁSA
    // =========================== 
    if( !empty($_POST['test-instance-id']) && isset($_POST['test-status']) ){
        $test_instance_id = $_POST['test-instance-id'];
        $test_status = $_POST['test-status'];

        TestInstance::setStatus($test_instance_id, $test_status);
        header('Location: '.$_SERVER['HTTP_REFERER']);
    }


    // ===========================
    // FELHASZNÁLÓI ADATOK FRISSÍTÉSE
    // =========================== 
    if( !empty($_POST['update-user-settings']) ){

        $modification = false;

        if( !empty($_POST['new-password1']) && !empty($_POST['new-password2']) ){
            $pass1 = $_POST['new-password1'];
            $pass2 = $_POST['new-password2'];

            if( $pass1 !== $pass2 ) exit('A két jelszó nem egyezik!'); 
            if( strlen($pass1) < 8 ) exit('A jelszónak legalabb 8 karakter hosszúságúnak kell lennie!');

            User::updatePassword(Session::get('user-id'), $pass1);
            $modification = true;
        }

        if( !empty($_POST['new-email']) ){
            $regex = '/^[a-zA-Z0-9.!#$%&’*+\/\=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/';
            $email = $_POST['new-email'];


            if( User::emailExists($email) ) exit('A megadott email már használatban van!');
            if( strlen($email) > 255 ) exit('Az email hossza nem lehet több mint 255 karakter!');
            if( !preg_match($regex, $email) ) exit('Az email formátuma nem megfelelő!');

            User::updateEmail(Session::get('user-id'), $email);
            $modification = true;
        }

        if( !empty($_FILES['new-avatar']) ){
            $fu = new FileUploader($_FILES['new-avatar'], 'image', 'avatar');
            $file_name = $fu->checkFile();

            User::updateAvatar(Session::get('user-id'), $file_name);

            $current_avatar = Session::get('user-avatar');
            unlink('C:/xampp/htdocs/EduNET/server/uploads/avatars/'.$current_avatar);
            Session::set('user-avatar', $file_name);

            $modification = true;
        }

        if( isset($_POST['new-email-subscription']) ){
            $status = (int)$_POST['new-email-subscription'];

            User::updateSubscription(Session::get('user-id'), $status);
            Session::set('user-subscription', $status);
            $modification = true;
        }

        if( $modification ){
            exit('success');
        } else{
            exit('Nem változtattál semmin!');
        }
    }






    // ===========================
    // FELADATLAP MEGOSZTÁSA
    // ===========================    
    if( !empty($_POST['share-test']) ){
        $new_test_author = $_POST['new-test-author'];
        $new_test_group = $_POST['new-test-group'];
        $original_test = $_POST['original-test'];

        $data = array(

        );
        TestInstance::duplicate();
    }

?>