<?php
    require_once 'config.php';
	Session::start();
?>
<html>
    <head>
        <title>Feladatok létrehozása</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="http://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
        <link rel="stylesheet" href="<?php echo PUBLIC_ROOT; ?>css/main.css">
        <link rel="stylesheet" href="<?php echo PUBLIC_ROOT; ?>css/components.css">
		<link rel="stylesheet" href="<?php echo PUBLIC_ROOT; ?>css/content.css">
    </head>
    <body class="test-body">

		<div class="test-container">

			<div class="test-sheet panel">
				<header class="bg-1">
					<h3><?php echo Session::get('current-task-number') ?>. feladat</h3>	
				</header>
				
				<form action="<?php echo SERVER_ROOT; ?>parsers/main-parser.php" method="POST" id="create-task-form" enctype="multipart/form-data">
					<li class="input-container">
						<label for="">Feladat kérdése:</label>
						<input type="text" name="task-question" id="task-question" placeholder="A feladathoz tartozó kérdés, utasítás">
					</li>
					<li class="input-container">
						<label for="">Feladat szövege:</label>
						<textarea name="task-text" id="task-text" placeholder="Szöveg adható meg (pl.: hianyzó szavas feladat)"></textarea>
					</li>
					<li class="input-container">
						<label for="">Kép hozzáadása:</label>
						<input type="file" name="task-image" id="task-image" style="display: none;">
						<button class="btn-rect bg-1" id="select-task-image"><i class="ion-images"></i>Kép feltöltése</button>
						<span class="uploaded-file-name">&nbsp</span>
					</li>
					<li class="input-container">
						<label for="">Feladat típusa:</label>
						<select name="task-type" id="task-type">
							<option value="0">Válassz feladattípust</option>
							<option value="1">Kvíz</option>
							<option value="2">Szöveges válasz</option>
							<option value="3">Párosítás</option>
							<option value="4">Igaz/hamis</option>
						</select>
					</li>
					<!-- ide jön a szekció, amiben a feladatopciókat lehet kezelni  -->
					<?php require_once 'public/templates/task-options.php'; ?>
					<li class="input-container" style="justify-content: right;">
						<button class="btn-add-option btn-rounded bg-1" onclick="return false;">Opció hozzáadása</button>
					</li>
					<li class="input-container">
						<label for="">Feladat pontszáma:</label>
						<input type="number" name="task-points" id="task-points" placeholder="Feladatért járó maximális pontszám" value="1" step="0.5">
					</li>
					<li class="input-container">
						<input type="submit" name="submit-add-task" id="submit-add-task" value="Feladat hozzáadása" class="btn-wide bg-1" style="color: #fff; border: none; flex: none;">
					</li>
				</form>
			</div>
		</div>		
    </body>
</html>
<script src="<?php echo PUBLIC_ROOT; ?>js/jquery.js"></script>
<script src="<?php echo PUBLIC_ROOT; ?>js/main.js"></script>
<script src="<?php echo PUBLIC_ROOT; ?>js/ajax.js"></script>