<?php

	class Message{
		
		public $id;
		public $text;
		public $date;
		public $sender_id;
		public $receiver_id;
		public $is_delivered;
		public $is_seen;
		
		public function __construct($data){
			$this->id = $data['id'];
			$this->text = $data['text'];
			$this->date = $data['date'];
			$this->sender_id = $data['sender_id'];
			$this->receiver_id = $data['receiver_id'];
			$this->is_delivered = $data['is_delivered'];
			$this->is_seen = $data['is_seen'];
			
			if( $this->is_seen == 0 ){
				$this->setToDelivered();
			}
		}
		
		public static function getAll($user_id){
			$db = Database::getInstance();
			
			$stmt = $db->prepare(
				"SELECT * FROM messages WHERE receiver_id = ? ORDER BY date DESC"
			);
			$stmt->execute(array($user_id));
			
			$data = $stmt->fetchAll();

            $list = array();
            foreach( $data as $d ){
                array_push($list, new Message($d));
            }

            return $list;
		}
		
		public static function getNews($user_id){
			$db = Database::getInstance();
			
			$stmt = $db->prepare(
				"SELECT * FROM messages WHERE is_delivered = ? AND receiver_id = ?"
			);
			$stmt->execute(array(0, $user_id));
								
			$data = $stmt->fetchAll();

            $list = array();
            foreach( $data as $d ){
                array_push($list, new Message($d));
            }

            return $list;
		}
		
		private function setToDelivered(){
			$db = Database::getInstance();
			
			$stmt = $db->prepare(
				"UPDATE messages SET is_delivered = ? WHERE receiver_id = ?"
			);
			$stmt->execute(array(1, $this->receiver_id));
		}
		
		public static function setToSeen($message_id){
			$db = Database::getInstance();
			
			$stmt = $db->prepare(
				"SELECT * FROM messages WHERE id = ?"
			);
			$stmt->execute(array($message_id));
			$msg = new Message($stmt->fetch());
			
			if( $msg->is_seen == 1 ){
				return false;
			}
			
			$stmt = $db->prepare(
				"UPDATE messages SET is_seen = ? WHERE id = ?"
			);
			$stmt->execute(array(1, $message_id));
			
			return true;
		}
		
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
		}
			
	}

?>