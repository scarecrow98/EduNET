<?php

    //üres paraméter
    if( empty($_GET['test_instance']) ){
        exit('Helytelen feladatlapazonosító!');
    }

    require_once 'config.php';

    Session::start();

    if( Security::checkAccessToken() === false ){
        header('Location: logout');
        exit();
    }

    Security::setAccessToken();

    if( Session::get('user-type') != 1 ){
        exit();
    }

    $html = '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><body style="font-family: DejaVu Sans, sans-serif;" >';
    $test_instance = TestInstance::get($_GET['test_instance']); 
    $lines = 15;

    //ha üres eredményhalmazt adott vissza a lekérdezés, nem létezik a feladatlappéldány
    if( empty($test_instance->id) ){
        exit('Helytelen feladatlapazonosító!');
    }

    $test = Test::get($test_instance->test_id); 
    $tasks = $test->getTasks();

    //sorok számának ellenőrzése a paraméterek között
    if( !empty($_GET['lines']) && is_numeric($_GET['lines']) ){
        $lines = $_GET['lines'];
    }

    $html .= '<h1 style="text-align: center;">'.$test->title.'</h1>';

    if( !empty($test->description) ){
        $html .= '<i style="color: #666;">'.$test->description.'</i>';
    }

    foreach( $tasks as $task ){

        $html .= '<div><h3>'.$task->task_number.'.) '.$task->question.'</h3>';

        $html .= '<div style="padding-left: 30px;">';

        if( !empty($task->text) ){
            $html .= '<pre style="color: #666;">'.$task->text.'</pre>';
        }

        if( !empty($task->image) ){
            $html .= '<img src="server/uploads/images/'.$task->image.'" style="max-width: 300px; display: block; margin-bottom: 30px;">';
        }

        $options = $task->getTaskOptions();
        
        //ha nem tartoznak opciók a feladathoz, akkor az szöveges válasz típusú --> vonala rajzolása
        if( empty($options) ){
            for( $i = 1; $i <= $lines; $i++ ){ $html .= '<hr style="margin-top: 30px; width: 100%; border: 1px solid #f0f0f0;">'; }
        }

        $html .= '<table>';
        foreach($options as $option){
            $html .= '<tr><td><p style="color: #666;">'.$option->text.'</p></td>';

                if( $task->type == 1 ){
                    $html .= '<td><div style="width: 15px; height: 15px; border-radius: 3px; border: 1px solid #666;"></div></td>';
                }
                elseif( $task->type == 3 ){
                    $html .= '<td><div style="width: 15px; height: 15px; border-radius: 3px; border: 1px solid #666;"></div></td>';
                }
                elseif( $task->type == 4 ){
                    $html .= '<td><div style="width: 15px; height: 15px; border-radius: 3px; border: 1px solid #666;"></div></td>';
                }

            $html .= '</tr>';
        }

        $html .= '</table></div></div>';
    }
    $html .= '</body></html>';

    require_once 'server/dompdf/autoload.inc.php';
    use Dompdf\Dompdf;

    $pdf = new Dompdf();

    // html szöveg betöltése 
    //$html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
    $pdf->loadHtml($html);

    // php kód engedélyezése html-en belül
    $pdf->set_option('isPhpEnabled', true);
    
    // papírméret és pozíció
    $pdf->setPaper('A4', 'portrait');

    // pdf generálása
    $pdf->render();

    // pdf küldése böngészőnek letöltésre
    $pdf->stream();
?>
