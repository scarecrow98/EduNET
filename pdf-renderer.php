<?php
	
    require_once 'config.php';

    Session::start();
	
	//biztonsági token ellenőrzése
    if( Security::checkAccessToken() === false ){
        header('Location: logout');
        exit();
    }
    Security::setAccessToken();
	
	//html szerkezet fejléce, és vízjel
	$html = '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><body style="font-family: DejaVu Sans, sans-serif;" >';
	$watermark = '<img src="public/resources/images/edunet-logo-black.png" style="position: absolute; top: -45px; right: -25px; width: 60px; opacity: 0.2;">';
	$html .= $watermark;
	
	//ha van $_GET['test'] paraméterünk, akkor feladatlap PDF-et kell generálnunk
	if( isset( $_GET['test_instance'] ) ){
		
		//ha nem tanár nyitja meg az oldalt
		if( Session::get('user-type') != 1 ) errorRedirect('Nincs jogosultságod az oldal megtekintéséhez!');

		//feladatlappéldány
		$test_instance = TestInstance::get($_GET['test_instance']);
		if( empty($test_instance->id) ) errorRedirect('A keresett feladatlap nem található!');	
		
		//bázisfeladatlap lekérése
		$test = Test::get($test_instance->test_id); 

		//feladatok lekérése
		$tasks = $test->getTasks();

		$lines = 15;
		
		//sorok számának ellenőrzése a paraméterek között
		if( !empty($_GET['lines']) && is_numeric($_GET['lines']) ){
			$lines = $_GET['lines'];
		}
		
		//html előállítása
		
		//cím
		$html .= '<h1 style="text-align: center;">'.$test->title.'</h1>';

		//leírás
		if( !empty($test_instance->description) ){
			$html .= '<i style="color: #666;">'.$test_instance->description.'</i>';
		}

		//végigmegyünk a feladatokon
		foreach( $tasks as $task ){
			//kis jelölő négyzetek stílusa
			$html .= '<style>.rect{ width: 15px; height: 15px; border-radius: 3px; border: 1px solid #666; }</style>';
		
			//kérdése
			$html .= '<div><strong>'.$task->task_number.'.) '.$task->question.'</strong>';

			$html .= '<div style="padding-left: 30px;">';

			//szöveg
			if( !empty($task->text) ){
				$html .= '<pre style="color: #666;">'.$task->text.'</pre>';
			}

			//kép
			if( !empty($task->image) ){
				$html .= '<img src="server/uploads/images/'.$task->image.'" style="max-width: 330px; display: block; margin-bottom: 30px;">';
			}

			//feladatopciók lekérése
			$options = $task->getTaskOptions();
			
			//ha nem tartoznak opciók a feladathoz, akkor az szöveges válasz típusú --> vonalak rajzolása
			if( empty($options) ){
				for( $i = 1; $i <= $lines; $i++ ){ $html .= '<hr style="margin-top: 30px; width: 100%; border: 1px solid #f0f0f0;">'; }
			}

			//feladatopciók megjelenítése
			$html .= '<table>';
			foreach($options as $option){
				$html .= '<tr><td style="color: #666;">'.$option->text.'</td>';

					if( $task->type == 1 ){
						$html .= '<td><div class="rect"></div></td>';
					}
					elseif( $task->type == 3 ){
						$html .= '<td><div class="rect"></div></td>';
					}
					elseif( $task->type == 4 ){
						$html .= '<td><div class="rect"></div></td>';
					}

				$html .= '</tr>';
			}

			$html .= '</table></div></div>';
		}
		
	//egyébként, ha van a sessionbe 'registrated-users' elem,
	//akkor a regisztrált felhasználók adataiból csinálunk PDF-et
	} else if( !empty( Session::get('registrated-users') ) ){
		$users = Session::get('registrated-users');
		
		//html táblázat előállítása
		$html .= '<h3>Új diákok belépési adatai</h3>';
		$html .= '
			<table style="width: 100%;">
				<tr>
					<td style="font-weight: bold;" align="center">Név</td>
					<td style="font-weight: bold;" align="center">Belépési azonosító</td>
					<td style="font-weight: bold;" align="center">Jelszó</td>
				</tr>
		';
		
		foreach( $users as $user ){
			$html .= '
				<tr>
					<td align="center">'.$user['name'].'</td>
					<td align="center">'.$user['login_id'].'</td>
					<td align="center">'.$user['password'].'</td>
				</tr>
			';
		}
		
		$html .= '</table>';
		
		//session adat törlése
		//Session::unset('registrated-users');
	} else {
		errorRedirect('A keresett feladatlap nem található!');
	}
	
	$html .= '</body></html>';

	
	//dompdf használata
    require_once 'server/dompdf/autoload.inc.php';
    use Dompdf\Dompdf;

    $pdf = new Dompdf();

    // html szöveg betöltése 
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
