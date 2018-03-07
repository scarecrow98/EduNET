<?php

    class UIDrawer{

        private function __construct(){  }
        private function __clone(){  }

        private static $span_wrong = '<span class="option-result-icon wrong-icon"></span>';
        private static $span_correct = '<span class="option-result-icon correct-icon"></span>';
        private static $span_missing = '<span class="option-result-icon missing-icon"></span>';


        //kvizopciok eredményeit jeleníti meg
        public static function quizResult($correct_ans, $user_ans){
            if( $correct_ans == 1 && $user_ans == 1 ){ //ha be kellett pipálni, és be is pipálta --> jó = zöld
                return UIDrawer::$span_correct.' helyes válasz: jelölendő';
            } else if( $correct_ans == 0 && $user_ans == 1 ){ // ha nem kellett bepipálni, de bepipálta --> nem jó = piros
                return UIDrawer::$span_wrong.' helyes válasz: nem jelölendő';
            } else if( $correct_ans == 1 && $user_ans == 0 ){ //ha be kellett pipálni, de nem pipálta be --> nem válaszolt = sárga
                return UIDrawer::$span_missing.' helyes válasz: jelölendő';
            } else {
                return '&nbsp;';
            }
        } 
        
        // igaz/hamis opciók eredményeit jeleníti meg
        public static function trueFalseResult($correct_ans, $user_ans){
            if( $correct_ans === $user_ans ){
                return UIDrawer::$span_correct.' helyesen válaszoltál';
            } else if( $correct_ans == 1 && $user_ans === 0 ){
                return UIDrawer::span_wrong.' helyes válasz: igaz';              
            } else if( $correct_ans == 0 && $user_ans == 1 ){
                return UIDrawer::$span_wrong.' helyes válasz: hamis';              
            } else{
                return UIDrawer::$span_missing.' helyes válasz: '.($correct_ans?'igaz':'hamis');              
            }
        }

        // párosítós feladatok eredményeit jeleníti meg
        public static function pairingResult($correct_ans, $user_ans){
            if( !isset($user_ans) ){
                return UIDrawer::$span_missing.' helyes válasz: '.$correct_ans;
            } else if( $correct_ans == $user_ans ){
                return UIDrawer::$span_correct.' helyes válasz: '.$correct_ans;
            } else{
                return UIDrawer::$span_wrong.' helyes válasz: '.$correct_ans;         
            }
        }

        public static function messageItem($message){
            $partner_id = $message->sender_id==Session::get('user-id') ? $message->receiver_id : $message->sender_id;
            $partner = User::get($partner_id);
            $is_unread = ( $message->is_seen == 0 && $message->sender_id != Session::get('user-id') ) ? true : false;

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

        public static function fileAnswer($file_name){
            if( empty($file_name) ){
                return '<p class="no-answer">Erre a feladatra nem érkezett válasz.</p>';
            } else {
                return '<a class="btn-download-file" href="'.SERVER_ROOT.'uploads/files/'.$file_name.'">Diák megoldásának letöltése</a>';
            }
        }

        public static function textAnswer($text_answer){
            if( empty($text_answer) ){
                return '<p class="no-answer">Erre a feladatra nem érkezett válasz.</p>';
            } else {
                return '<pre class="text-answer">'.$text_answer.'</pre>';
            }
        }

    }

?>