<?php

	class Message{
		//adattagok
		public $id;
		public $text;
		public $date;
		public $sender_id;
		public $receiver_id;
		public $is_delivered;
		public $is_seen;
		
		// konstruktor
		public function __construct($data){
			$this->id = $data['id'];
			$this->text = $data['text'];
			$this->date = $data['date'];
			$this->sender_id = $data['sender_id'];
			$this->receiver_id = $data['receiver_id'];
			$this->is_delivered = $data['is_delivered'];
			$this->is_seen = $data['is_seen'];
			
			// ha az üzenet még nem lett kiküldve, akkor az üzenetobjektum létrehozásakor
			// legyen az is_delivered mező 1
			if( $this->is_delivered == 0 ){
				$this->setToDelivered();
			}
		}
		
		// visszaadja az utolsó küldött üzenetet beszélgetésenként
		public static function getPreviews($user_id){
			//lekérjük, hogy a felhasználó kikkel beszélgetett eddig már
			$ids = Message::getPartnerIds($user_id);
			
			//végigmegyünk a partnerazonosítókon, és lekérjük a felhasználó és a partner közti utolsó üzenetet
			$msgs = array();
			foreach( $ids as $partner_id ){
				$msgs[] = Message::getLastConversationMessage($user_id, $partner_id);
			}

			//rendezzük az üzeneteket dátum szerint
			$list = Message::orderByDate($msgs);

            return $list;
		}
		
		//visszaadja az összes üzenetet egy beszélgetésben
		public static function getConversation($user_id, $partner_id){
			$db = Database::getInstance();
			
			$stmt = $db->prepare(
				"SELECT * FROM messages WHERE receiver_id IN (:uid, :pid) AND sender_id IN (:uid, :pid) ORDER BY date ASC"
			);
			$stmt->execute(array(':uid' => $user_id, ':pid' => $partner_id));
								
			$data = $stmt->fetchAll();

            $list = array();
            foreach( $data as $d ){
                $list[] = new Message($d);
            }

            return $list;
		}
		

		//lekéri az új, bejövő üzeneteket, amik még nem lettek kiküldve
		public static function getNews($user_id){
			$db = Database::getInstance();
			
			$stmt = $db->prepare(
				"SELECT * FROM messages WHERE is_delivered = ? AND receiver_id = ?"
			);
			$stmt->execute(array(0, $user_id));
								
			$data = $stmt->fetchAll();

            $list = array();
            foreach( $data as $d ){
                $list[] = new Message($d);
            }

            return $list;
		}
		
		//1-re állítja az is_delivered mezőjét az üzenetnek példányosításkor
		private function setToDelivered(){
			$db = Database::getInstance();
			
			$stmt = $db->prepare(
				"UPDATE messages SET is_delivered = ? WHERE receiver_id = ?"
			);
			$stmt->execute(array(1, $this->receiver_id));
		}
		
		//1-re állítja az is_seen mezőjét egy üzenetnek
		public static function setToSeen($user_id, $partner_id){
			$db = Database::getInstance();
			
			$stmt = $db->prepare(
				"UPDATE messages SET is_seen = :true WHERE is_seen = :false AND receiver_id IN (:uid, :pid) AND sender_id IN (:uid, :pid)"
			);
			$stmt->execute(array(
				':true'		=> 1,
				':false'	=> 0,
				':uid'		=> $user_id,
				':pid'		=> $partner_id
			));
			return $stmt->rowCount();
		}
		
		//létrehoz egy üzenetet az adatbázisban, és visszaadja a létrehozott üzenet azonosítóját
		public static function create($data){
			$db = Database::getInstance();

			$stmt = $db->prepare(
				"INSERT INTO messages(sender_id, receiver_id, text, date)".
				" VALUES(:si, :ri, :t, :d)"
			);
			$stmt->execute(array(
				':si'	=> $data['sender_id'],
				':ri'	=> $data['receiver_id'],
				':t'	=> $data['text'],
				':d'	=> $data['date']
			));

			return $db->lastInsertId();
		}

		//visszaadja azon partnerek azonosítóját, akikkel a tanár beszélgetést folytatott eddig
		public static function getPartnerIds($user_id){
			$db = Database::getInstance();

			$stmt = $db->prepare(
				"SELECT sender_id AS 'partner' FROM messages where receiver_id = :uid".
				" UNION".
				" SELECT receiver_id AS 'partner' FROM messages where sender_id = :uid"
			);
			$stmt->execute(array(':uid' => $user_id));

			$data = $stmt->fetchAll();
			$arr = array();
			foreach( $data as $d ){
				$arr[] = $d['partner'];
			}

			return $arr;
		}
		
		//visszaadja, hogy egy beszélgetésben mi volt az utolsó üzenet
		public static function getLastConversationMessage($user_id, $partner_id){
			$db = Database::getInstance();

			$stmt = $db->prepare(
				"SELECT * FROM messages WHERE".
				" receiver_id IN (:uid, :pid) AND sender_id IN (:uid, :pid)".
				" ORDER BY date DESC LIMIT 1"
			);
			$stmt->execute(array(':uid' => $user_id, ':pid' => $partner_id));

			return new Message($stmt->fetch());	
		}

		//az átadott üzenettömböt rendezi dátum szerint
		public static function orderByDate($arr){
			for($i = 0; $i < count($arr) - 1; $i++){
				for($j = $i + 1; $j < count($arr); $j++){
					$date1 = strtotime($arr[$i]->date);
					$date2 = strtotime($arr[$j]->date);
					if( $date1 < $date2 ){
						$x = $arr[$i];
						$arr[$i] = $arr[$j];
						$arr[$j] = $x;
					}
				}
			}
			return $arr;
		}
			
	}

?>