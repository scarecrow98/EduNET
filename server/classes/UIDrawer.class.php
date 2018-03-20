<?php

    class UIDrawer{

        private function __construct(){  }
        private function __clone(){  }

		//gyakran használt html szerkezetek
        private static $span_wrong = '<span class="option-result-icon wrong-icon"></span>';
        private static $span_correct = '<span class="option-result-icon correct-icon"></span>';
        private static $span_missing = '<span class="option-result-icon missing-icon"></span>';


        //kvizopciok eredményeit jeleníti meg
        public static function quizResult($correct_ans, $user_ans){
            if( $correct_ans == 1 && $user_ans == 1 ){ //ha be kellett pipálni, és be is pipálta --> jó = zöld
                return '<td>jelölendő</td><td>'.UIDrawer::$span_correct.'</td>';
            } else if( $correct_ans == 0 && $user_ans == 1 ){ // ha nem kellett bepipálni, de bepipálta --> nem jó = piros
                return '<td>nem jelölendő</td><td>'.UIDrawer::$span_wrong.'</td>';
            } else if( $correct_ans == 1 && $user_ans == 0 ){ //ha be kellett pipálni, de nem pipálta be --> nem válaszolt = sárga
                return '<td>jelölendő</td><td>'.UIDrawer::$span_correct.'</td>';
            } else {
                return '<td>nem jelölendő</td><td>&nbsp;</td>';
            }
        } 
        
        //igaz/hamis opciók eredményeit jeleníti meg
        public static function trueFalseResult($correct_ans, $user_ans){
            if( $correct_ans === $user_ans ){ //ha jó a válasz
                return '<td>'.($correct_ans == 1 ? 'igaz' : 'hamis').'</td><td>'.UIDrawer::$span_correct.'</td>';
            } else if( $correct_ans == 1 && $user_ans === 0 ){ //ha a megoldás igaz, de a válasz hamis
                return '<td>igaz</td><td>'.UIDrawer::$span_wrong.'</td>';
            } else if( $correct_ans == 0 && $user_ans == 1 ){ //ha a megoldás hamis, de a válasz igaz
                return '<td>hamis</td><td>'.UIDrawer::$span_wrong.'</td>';
            } else{ //egyéb eset, ha nincs megadva válasz
                return '<td>'.($correct_ans == 1 ? 'igaz' : 'hamis').'</td><td>'.UIDrawer::$span_missing.'.</td>';
            }
        }

        //párosítós feladatok eredményeit jeleníti meg
        public static function pairingResult($correct_ans, $user_ans){
            if( empty($user_ans) ){
                return '<td>'.$correct_ans.'</td><td>'.UIDrawer::$span_missing.'</td>';//ha hiányzik a válasz
            } else if( $correct_ans == $user_ans ){ //ha jó a válasz
                return '<td>'.UIDrawer::$span_correct.'</td><td>'.UIDrawer::$span_correct.'</td>';
            } else{//ha nem jó a válasz
                return '<td>'.$correct_ans.'</td><td>'.UIDrawer::$span_wrong.'</td>';
            }
        }

		//a függvény egy listaelemet csinál a lenyíló üzenetek ablakban
        public static function messageItem($message){
			//partnerazonosító meghatározása-->ha a sender_id azonos a saját azonosítónkkal, akkor a partner azonosító a receiver_id-lesz
            $partner_id = $message->sender_id == Session::get('user-id') ? $message->receiver_id : $message->sender_id;
            //partner adatainak lekérése
			$partner = User::get($partner_id);
			//változóba eltároljuk, hogy az üzenetet MI elolvastuk-e már
            $is_unread = ( $message->is_seen == 0 && $message->sender_id != Session::get('user-id') ) ? true : false;

			//html generálása
            return '
                <li class="message-item'.($is_unread ? ' unread-message' : '').'" data-message-id="'.$message->id.'" data-partner-id="'.$partner_id.'" id="partner-'.$partner_id.'">
                    <div>
                        <span style="background-image: url('.SERVER_ROOT.'uploads/avatars/'.$partner->avatar.'"></span>
                        <h4 style="inline">'.$partner->name.'</h4>
                    </div>
                    <time>'.$message->date.'</time>
                    <p>'.$message->text.'</p>
                </li>
            ';
        }

		//megjeleníti a diák fájlválaszának letöltési linkjét, ha létezik
        public static function fileAnswer($file_name){
            if( empty($file_name) ){
                return '<p class="no-answer">Erre a feladatra nem érkezett válasz.</p>';
            } else {
                return '
                    <a href="'.SERVER_ROOT.'uploads/files/'.$file_name.'">
                        <button class="btn-rounded bg-1">
                            <i class="ion-android-download"></i>Megoldás letöltése
                        </button>
                    </a>
                ';
            }
        }

		//megjeleníti a diák szöveges válaszát, ha létezik
        public static function textAnswer($text_answer){
            if( empty($text_answer) ){
                return '<p class="no-answer">Erre a feladatra nem érkezett válasz.</p>';
            } else {
                return '<pre class="quote">'.$text_answer.'</pre>';
            }
        }

    }

?>