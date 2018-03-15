<html>
	<head>
		<meta charset="utf-8">
	</head>
	<body>
	<?php

		$str = "Váli Dániel";

		//kicserélendő karakterek
		$chars = array(
			'á'	=> 'a',
			'é'	=> 'e',
			'í'	=> 'i',
			'ó'	=> 'o',
			'ö'	=> 'o',
			'ő'	=> 'o',
			'ú'	=> 'u',
			'ü'	=> 'u',
			'ű'	=> 'u',
			'Á'	=> 'a',
			'É'	=> 'e',
			'Í'	=> 'i',
			'Ó'	=> 'o',
			'Ö'	=> 'o',
			'Ő'	=> 'o',
			'Ú'	=> 'u',
			'Ü'	=> 'u',
			'Ű'	=> 'u'
		);
			
		//kisbetűssé alakítás
		$str = strtolower($str);
		
		//karakterek kicserélése
		$str = str_replace(array_keys($chars), 	$chars, $str);
		
		
		//név szétválasztása szóköz mentén
		$split = explode(' ', $str);
		
		//első névrész + a második névrész 1. betűjének összefűzése
		$name = $split[0].$split[1][0];
		
		echo($name);

	?>
	</body>
</html>