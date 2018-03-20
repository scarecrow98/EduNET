<?php

	require_once '../../config.php';
    Session::start();
    
    if( Security::checkAccessToken() === false ){
        exit();
    }
    // ===========================
    // FELHASZNÁLÓI ADATOK LEKÉRÉSE
    // ===========================
    if( !empty($_POST['get-user-data']) ){
        $resp = array(
            'name'      => Session::get('user-name'),
            'email'     => Session::get('user-email'),
            'avatar'    => Session::get('user-avatar'),
        );

        exit(json_encode($resp));
    }
    

    // ===========================
    // ÚJ FELADATLAP LÉTREHOZÁSA
    // ===========================
    if( !empty($_POST['create-new-test']) ){
        
        //ellenőrzések
		//ha nincs megadava leírása és szöveg, akkor az értékük null
        $description = empty($_POST['description']) ? null : $_POST['description'];
        $text = empty($_POST['text']) ? null : $_POST['text'];

        if( empty($_POST['title']) ) exit('A feladatlap címe kötelezően megadandó mező!');
        if( strlen($_POST['title']) > 100 ) exit('A feladatlap címe max. 100 karakter lehet!');
        if( empty($_POST['group-id']) ) exit('A feladatlap csoportja kötelezően megadandó mező!');
        if( empty($_POST['subject-id']) ) exit('A feladatlap tantárgya kötelezően megadandó mező!');
        if( strlen($description) > 255 ) exit('A feladatlap leírása max. 255 karakter lehet!');
        if( $_POST['task-count'] > 30 || $_POST['task-count'] < 1 ) exit('A feladatok száma 1 és 30 között kell legyen!');

        //adattömb készítése a Test osztály metódusának
        $data = array(
			'author_id'		=> Session::get('user-id'),
            'title'         => htmlspecialchars($_POST['title']),
            'description'   => htmlspecialchars($description),
            'text'          => htmlspecialchars($text), 
            'group_id'      => (int)$_POST['group-id'],
            'subject_id'    => (int)$_POST['subject-id'],
            'task_count'    => (int)$_POST['task-count'],
        );
        	
		//bázisfeladatlap létrehozása
		Test::create($data);
		
		//sikeres feldolgozás, válasz a kliensnek
		exit('success');
    }

	// ===========================
    // ÚJ FELADAT LÉTREHOZÁSA
    // ===========================    
    if( !empty($_POST['create-new-task']) ){
		//ellenőrzések
        $image = null;
		if( !empty($_FILES['image']) && $_FILES['image']['name'] != '' ){
			$fu = new FileUploader($_FILES['image'], 'image', 'task_image'); 	
			$image = $fu->checkFile();
		}
        
        if( empty($_POST['question']) ) exit('A feladat kérdése kötelezően megadandó mező!');
        if( empty($_POST['type']) ) exit('A feladat típusa kötelezően megadandó mező!');
        if( $_POST['max_points'] < 1 || $_POST['max_points'] > 127 ) exit('A feladat maxmiális pontszámának 1 és 127 közé kell esnie !');

        //JSON string visszalakítása tömbbé, és eltárolása
        $option_texts = json_decode($_POST['option_texts']);
        $option_answers = json_decode($_POST['option_answers']);

        //0. elemet kitöröljük a tömbböl, mivel az üres,
        //mert az opciók számozása 1-től kezdődött
        //-->így a foreach nem akad majd meg az üres elemnél ellenőrzéskor
        unset($option_texts[0]);
        unset($option_answers[0]);

        foreach( $option_texts as $text ){ if( empty($text) ) exit('Az feladatopciók szövege kötelezően megadandó mező!'); }
        
        //helyes válaszok megléte, max pontok oszétoszhatóságának ellenőrzése

        //ha kvíz típusú a feladat
        if( $_POST['type'] == 1 ){
            //legalább egy helyes megoldás megléte   
            $answer_counter = 0;
            foreach( $option_answers as $answer ){ if( $answer == 1 ) $answer_counter++; }
            if( $answer_counter == 0 ) exit('Legalább egy helyes megoldásnak kell lennie!');

            //lehetséges helyes válaszok megszámolása
            $quiz_correact_answers = 0;
            foreach( $option_answers as $answer ) { if( $answer == 1 ) $quiz_correact_answers++; }

            //ha a max pontok száma % lehetséges helyes megodások száma != 0, akkor hiba
            if( (int)$_POST['max_points'] % $quiz_correact_answers != 0 ){
                exit('Az feladatért kapható pontok száma nem összeegyeztethető a lehetséges helyes megoldások számával!');
            }

        //ha igazhamis vagy párosítás típusú a feladat, akkor az opciók számával osztjuk el a max pontokat
        } elseif( $_POST['type'] == 3 || $_POST['type'] == 4 ){
             //összes megoldások megléte:
             //ha a helyes válasz üres vagy nem is létezik, akkor hiba
            foreach( $option_answers as $answer ){ if( !isset($answer) || $answer == '' )  exit('Minden feladatopcióra add meg a helyes választ!'); }

            //ha a max pontok száma % feladatopciók számával != 0, akkor hiba
            if( (int)$_POST['max_points'] % count($option_answers) != 0 ){
                exit('Az feladatért kapható pontok száma nem összeegyeztethető a lehetséges helyes megoldások számával!');
            }
        }

        $data = array(
            'question'          => $_POST['question'],
            'text'              => htmlspecialchars($_POST['text']),
            'type'              => $_POST['type'],
            'max_points'        => $_POST['max_points'],
            'image'             => $image,
            'option_texts'      => $option_texts,
            'option_answers'    => $option_answers,
        );
        
        Task::create($data);
        
        //növeljük a feladatszámot
		$current_task_number = Session::get('current-task-number');
		$current_task_number++;
		Session::set('current-task-number', $current_task_number);
        
        //ellenőrizzük, hogy elértük-e a feladatlap max feladatainak számát
		if( Session::get('current-task-number') > Session::get('total-task-count') ){ exit('end'); }
		
		exit('success');
    }

    //függvény, amely létrehoz egy új üzenetet az adatbázisban
    function newMessage($receiver_id, $text){
		$data = array(
			'sender_id'		=> Session::get('user-id'),
			'receiver_id'	=> $receiver_id,
            'text'			=> $text,
            'date'          => date('Y-m-d H:i:s')
		);
		Message::create($data);
    }
    
    // ===========================
    // ÜZENET KÜLDÉSE
    // ===========================
	if( isset($_POST['new-message']) ){
        $resp = array();

        //ellenőrzések
        if( empty($_POST['text']) ){
            $resp['status'] = 'Nem írtál be üzenetet!';
            exit(json_encode($resp));
        }
        if( empty($_POST['partner-id']) ){
            $resp['status'] = 'Nem adtad meg a címzettet!';
            exit(json_encode($resp));
        }
        
        $partner_id = (int)$_POST['partner-id'];
        $text = htmlspecialchars($_POST['text']);
        
		//üzenet létrehozása az adatbázisban
        newMessage($partner_id, $text);
		//válasz a kliensnek
        $resp = array(
            'status'    => 'success',
            'message'   => $text
        );
		exit(json_encode($resp));
    }
    
    // ===========================
    // ÜZENET LÉTREHOZÁSA
    // ===========================
	if( isset($_POST['create-message']) ){
        //ellenőrzések
        if( empty($_POST['text']) ){
            $resp['status'] = 'Nem írtál be üzenetet!';
            exit(json_encode($resp));
        }
        if( empty($_POST['partner-id']) ){
            $resp['status'] = 'Nem adtad meg a címzettet!';
            exit(json_encode($resp));
        }

        $partner_id = (int)$_POST['partner-id'];
		$text = htmlspecialchars($_POST['text']);

        //üzenet létrehozása
        newMessage($partner_id, $text);
        //partner adatainak lekérése
        $partner = User::get($partner_id);

        //visszaküldjük a kliensnek partnere nevét, képét, és a escapelt üzenetet
        $resp = array(
            'status'            => 'success',
            'partner_name'      => $partner->name,
            'partner_avatar'    => $partner->avatar,
            'message'           => $text
        );
		exit(json_encode($resp));
	}
	
	// ===========================
    // ÜZENETEK FIGYELÉSE
    // ===========================
	if( isset($_POST['has-new-message']) ){
        //új üzenetek lekérése
		$messages = Message::getNews(Session::get('user-id'));
		$resp = array();
		
		//végigmegyünk az új üzeneteken
		foreach( $messages as $message ){
            //üzenetet küldő felhasználó adatainak lekérése
			$sender = User::get($message->sender_id);
            
            //tömb készítése a bejövő üzenetekből, amit visszaküldünk a felhasználónak
			$resp[] = array(
				'sender_name'	=> $sender->name,
                'sender_avatar'	=> $sender->avatar,
                'sender_id'     => $sender->id,
				'text'			=> $message->text,
				'date'			=> $message->date
			);
		}	
		exit(json_encode($resp));
	}
	
	// ===========================
    // PÁRBESZÉD LEKÉRÉSE (HA KELL, AKKOR ÜZENETEK OLVASOTTÁ ÁLLÍTÁSA)
    // ===========================
	if( isset($_POST['get-conversation']) ){

        //lekérjük a partner és a köztünk lévő eddigi beszélgetést
        $partner_id = $_POST['partner-id'];        
        $messages = Message::getConversation(Session::get('user-id'), $partner_id);


        //ha olyan beszélgetésre kattintott a felhasználó amiben van olvasatlan üzenet,
        //akkor azokat az üzeneteket olvasottra állítjuk
        if( !empty($_POST['set-to-seen']) ) {
            Message::setToSeen(Session::get('user-id'), $partner_id);
        }

        //beszélgetés tömbbé alakítása, hogy tudjuk küldeni a kliensnek
        $resp = array();
        foreach( $messages as $message ){
            $resp[] = array(
                'is_own'    => $message->sender_id == Session::get('user-id') ? 1 : 0, //mutatja, hogy mi írtuk-e az üzenetet vagy úgy kaptuk
                'text'      => $message->text, //üzenet szövege
                'date'      => $message->date //üzenet dátuma
            );
        }

        exit(json_encode($resp));
		
	}
	
	// ===========================
    // ÚJ ÉRTESÍTÉS LÉTREHOZÁSA
    // =========================== 
    if( isset($_POST['create-new-notification']) ){
        
        //ellenőrzések
		foreach( $_POST as $n ){
			if( empty($n) ){ exit('Minden mező kitöltése kötelező!'); }
		}
        
        if( strlen($_POST['text']) > 100 ) exit('Az értesítés címe max. 100 karakter lehet!'); 
        if( date('Y-m-d') > $_POST['date'] ) exit('Érvénytelen kezdési időpontot adtál meg!'); 
				
        $data = array(
			'author_id'		=> Session::get('user-id'),
            'text'     		=> htmlspecialchars($_POST['text']),
            'subject_id'   	=> (int)$_POST['subject_id'],
			'group_id'		=> (int)$_POST['group_id'],
            'date'      	=> $_POST['date'],
            'type'      	=> (int)$_POST['type']
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
    // ÚJ CSOPORT LÉTREHOZÁSA
    // ===========================
    if( !empty($_POST['create-new-group']) ){
        $avatar = 'group-default.png';
        
        //ellenőrzések
		if( !empty($_FILES['avatar']) && $_FILES['avatar']['name'] != '' ){
			$fu = new FileUploader($_FILES['avatar'], 'image', 'avatar'); 	
			$avatar = $fu->checkFile();
        }
        
        if( empty($_POST['name']) ) exit('A csoport neve kötelezően megadanó mező!');
        if( strlen($_POST['name']) > 50 ) exit('A csoport neve max. 50 karakter lehet!');
        if( strlen($_POST['description']) > 255 ) exit('A csoport leírása max. 255 karakter lehet!');
		
        $data = array(
            'name'          => htmlspecialchars($_POST['name']),
            'author_id'     => Session::get('user-id'),
            'description'   => htmlspecialchars($_POST['description']),
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
    // FELADATLAP STÁTUSZÁNAK ÁLLÍTÁSA
    // =========================== 
    if( !empty($_POST['test-instance-id']) && isset($_POST['test-status']) ){
        $test_instance_id = $_POST['test-instance-id'];
        $test_status = $_POST['test-status'];

        TestInstance::setStatus($test_instance_id, $test_status);

        if( $test_status == 2 )
            header('Location: http://edunet/home');
        else
            header('Location: '.$_SERVER['HTTP_REFERER']);
    }


    // ===========================
    // FELHASZNÁLÓI ADATOK FRISSÍTÉSE
    // =========================== 
    if( !empty($_POST['update-user-settings']) ){
		//ez a változó tárolja majd, hogy lett-e frissítve valami
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
			//ellenőrzések
            $regex = '/^[a-zA-Z0-9.!#$%&’*+\/\=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/';
            $email = $_POST['new-email'];

            if( User::emailExists($email) ) exit('A megadott email már használatban van!');
            if( strlen($email) > 255 ) exit('Az email hossza nem lehet több mint 255 karakter!');
            if( !preg_match($regex, $email) ) exit('Az email formátuma nem megfelelő!');

            User::updateEmail(Session::get('user-id'), $email);
            $modification = true;
        }

        if( !empty($_FILES['new-avatar']) ){
			//fájl ellenőrzése és feltöltése
            $fu = new FileUploader($_FILES['new-avatar'], 'image', 'avatar');
            $file_name = $fu->checkFile();

            User::updateAvatar(Session::get('user-id'), $file_name);

			//előző profilkép törlése a szerver mappájából
            $current_avatar = Session::get('user-avatar');
            unlink('C:/xampp/htdocs/EduNET/server/uploads/avatars/'.$current_avatar);
			
			//sessionbe új kép neve kerül
            Session::set('user-avatar', $file_name);

            $modification = true;
        }

        if( isset($_POST['new-email-subscription']) ){
            $status = (int)$_POST['new-email-subscription'];

            User::updateSubscription(Session::get('user-id'), $status);
            Session::set('user-subscription', $status);
            $modification = true;
        }

		//válasz a kliensnek
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

        //ellenőrzések
        if( empty($_POST['group-id']) ) exit('Nem adtad meg, melyik csoportba osztod meg a feladatlapot!');
        if( empty($_POST['original-test-id']) ) exit('Érvénytelen feladatlap-azonosító!');

        $description = empty($_POST['description']) ? null : $_POST['description'];
        $group_id = (int)$_POST['group-id'];
		//csoport adatainak lekérés, hogy tudjuk, ki a csoport létrehozója
        $group = Group::get($group_id);

        $data = array(
            'test-id'               => (int)$_POST['original-test-id'],
            'group-id'              => (int)$_POST['group-id'],
            'current-author-id'     => $group->author_id,
            'original-author-id'    => Session::get('user-id'),
            'description'           => htmlspecialchars($description),
            'date'                  => date('Y-m-d H:i:s')
        );

        TestInstance::duplicate($data);

        exit('success');
    }

    // ===========================
    // DIÁKOK KERESÉSE
    // =========================== 
    if( !empty($_POST['student-name']) ){
		//adatok tárolása
        $student_name = $_POST['student-name'];
        $group_id = (int)$_POST['group-id'];

		//diákok lekérése az adatbázisból,
		//majd adattömb küldése a kliensnek
        $results = Group::searchUsers($student_name, $group_id);
        echo json_encode($results);
    }

?>