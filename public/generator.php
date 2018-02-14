<?php

    $id = 'az99';

    function incrementId($id){
        $output = '';
        $num_part = (int)$id[2].$id[3];
        $c1 = $id[0];
        $c2 = $id[1];

        $ascii1 = ord($c1);
        $ascii2 = ord($c2);

        if( $num_part == 99){
            $num_part = 0;

            if( $ascii2 == 122 ){
                $c2 = chr(97);
                $c1 = chr( ++$ascii1 );
            }
            else{
                $c2 = chr( ++$ascii2 );
            }

        }else{
            $num_part++;
        }

        $output = $c1.$c2.sprintf("%02d", $num_part);
        return $output;
    }

    $i = 0;


    while ($i < 1000) {
        $i++;

        if( empty($r) ){
            $r = incrementId('aa00');
        }
        else{
           $r = incrementId($r); 
        }
        echo $r.'<br>';
    }
    
?>