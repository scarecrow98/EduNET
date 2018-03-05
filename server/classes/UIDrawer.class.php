<?php

    class UIDrawer{

        private function __construct(){  }
        private function __clone(){  }


        //kvizopciok eredményeit jeleníti meg
        public static function quizResult($correct_ans, $user_ans){
            if( $correct_ans == 1 && $user_ans == 1 ){ //ha be kellett pipálni, és be is pipálta --> jó = zöld
                return '<span class="option-result-icon correct-icon"></span> helyes válasz: jelölendő | te válaszod: jelölted';
            } else if( $correct_ans == 0 && $user_ans == 1 ){ // ha nem kellett bepipálni, de bepipálta --> nem jó = piros
                return '<span class="option-result-icon wrong-icon"></span> helyes válasz: nem jelölendő | te válaszod: jelölted';
            } else if( $correct_ans == 1 && $user_ans == 0 ){ //ha be kellett pipálni, de nem pipálta be --> nem válaszolt = sárga
                return '<span class="option-result-icon missing-icon"></span> helyes válasz: jelölendő | te válaszod: nem jelölted';
            } else {
                return '&nbsp;';
            }
        } 
        
        public static function trueFalseResult($correct_ans, $user_ans){
            if( $correct_ans === $user_ans ){
                return '<span class="option-result-icon correct-icon"></span> helyesen válaszoltál';
            } else if( $correct_ans === 1 && $user_ans === 0 ){
                return '<span class="option-result-icon wrong-icon"></span> helyes válasz: igaz | te válaszod: hamis';              
            } else if( $correct_ans === 0 && $user_ans === 1 ){
                return '<span class="option-result-icon wrong-icon"></span> helyes válasz: hamis | te válaszod: igaz';              
            } else{
                return '<span class="option-result-icon missing-icon"></span> helyes válasz: '.($correct_ans?'igaz':'hamis').' | te válaszod: nem válaszoltál';              
            }
        }

        public static function pairingResult($correct_ans, $user_ans){
            if( !isset($user_ans) ){
                return '<span class="option-result-icon missing-icon"></span> helyes válasz: '.$correct_ans.' | te válaszod: nem válaszoltál';
            } else if( $correct_ans === $user_ans ){
                return '<span class="option-result-icon correct-icon"></span> helyes válasz: '.$correct_ans.' | te válaszod: '.$user_ans;
            } else{
                return '<span class="option-result-icon wrong-icon"></span> helyes válasz: '.$correct_ans.' | te válaszod: '.$user_ans;         
            }
        }

    }

?>