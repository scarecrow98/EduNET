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
                return '<td>'.UIDrawer::$span_correct.'</td><td>'.UIDrawer::$span_correct.'</td>';
            } else if( $correct_ans == 0 && $user_ans == 1 ){ // ha nem kellett bepipálni, de bepipálta --> nem jó = piros
                return '<td>'.UIDrawer::$span_wrong.'</td><td>&nbsp;</td>';
            } else if( $correct_ans == 1 && $user_ans == 0 ){ //ha be kellett pipálni, de nem pipálta be --> nem válaszolt = sárga
                return '<td>'.UIDrawer::$span_missing.'</td><td>'.UIDrawer::$span_correct.'</td>';
            } else {
                return '<td>&nbsp;</td><td>&nbsp;</td>';
            }
        } 
        
        // igaz/hamis opciók eredményeit jeleníti meg
        public static function trueFalseResult($correct_ans, $user_ans){
            if( $correct_ans === $user_ans ){
                return '<td>'.UIDrawer::$span_correct.'</td><td>'.UIDrawer::$span_correct.'</td>';
            } else if( $correct_ans == 1 && $user_ans === 0 ){
                return '<td>'.UIDrawer::$span_wrong.'</td><td>'.UIDrawer::$span_true.'</td>';
            } else if( $correct_ans == 0 && $user_ans == 1 ){
                return '<td>'.UIDrawer::$span_wrong.'</td><td>'.UIDrawer::$span_wrong.'</td>';
            } else{
                return '<td>'.UIDrawer::$span_missing.'</td><td>'.($correct_ans == 0 ? UIDrawer::$span_wrong : UIDrawer::$span_correct).'</td>';
            }
        }

        // párosítós feladatok eredményeit jeleníti meg
        public static function pairingResult($correct_ans, $user_ans){
            if( !isset($user_ans) ){
                return '<td>'.UIDrawer::$span_missing.'</td><td><strong>'.$correct_ans.'<strong></td>';
            } else if( $correct_ans == $user_ans ){
                return '<td>'.UIDrawer::$span_correct.'</td><td><strong>'.$correct_ans.'<strong></td>';
            } else{
                return '<td>'.UIDrawer::$span_wrong.'</td><td><strong>'.$correct_ans.'<strong></td>';
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
                return '
                    <a href="'.SERVER_ROOT.'uploads/files/'.$file_name.'">Megoldás letöltése</a>
                ';
            }
        }

        public static function textAnswer($text_answer){
            if( empty($text_answer) ){
                return '<p class="no-answer">Erre a feladatra nem érkezett válasz.</p>';
            } else {
                return '<pre class="quote">'.$text_answer.'</pre>';
            }
        }

    }

?>