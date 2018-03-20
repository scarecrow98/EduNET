<?php

	class FileUploader{
		
		//maga fájl a $_FILES tömbböl
		private $file;
		//fájl típusa (image vagy file)
		private $file_type;
		//fájl szerepe (avatar, task_image, solution_file)
		private $file_role;
		//maximális fájlméret (5MB)
		private $max_size = 5 * 1024 * 1024;
		
		//feltöltési könytárak
		private $upload_paths = array(
			'avatar'		=> '../../server/uploads/avatars/',
			'task_image'	=> '../../server/uploads/images/',
			'solution_file'	=> '../../server/uploads/files/'
		);
		
		//megengedett mime típusok
		private $allowed_mime_types = array(
			'image' => array(
				'image/jpeg',
				'image/jpg',
                'image/png',
                'image/gif'
			),
			//csak zip és rar fájlok
			'file'	=> array(
				'application/x-zip-compressed',
				'application/x-rar-compressed',
				'application/zip',
				'application/octet-stream'
			)
		);
		
		//megengedett kiterjesztések
		private $allowed_extensions = array(
			'image'	=> array('jpeg', 'jpg', 'png', 'gif'),
			'file'	=> array('rar', 'zip')
		);
		
		//konstruktor
		public function __construct($_file, $_file_type, $_file_role){
			$this->file 		= $_file;
			$this->file_type 	= $_file_type;
			$this->file_role	= $_file_role;
		}
		

		//feltöltött fájl ellenőrzése, áthelyezése a megfelelő mappába
		public function checkFile(){
			//fájl adatatinak eltárolása lokákis változókba
			$size = $this->file['size'];
			$name = $this->file['name'];
			$tmp = $this->file['tmp_name'];
			$errors = $this->file['error'];
			$extension = strtolower( pathinfo($name)['extension'] );
			$mime = $this->file['type'];
			
			//ellenőrzések
			//fel lett-e töltve fájl az átmenetei szerver mappába
			if( !is_uploaded_file($tmp) ) exit('Hiba történt a feltöltés közben!'); 
			//hiba feltöltés közben
			if( $errors > 0 ) exit('Hiba történt a feltöltés közben!');
			//méret
			if( $size > $this->max_size ) exit('A fájl mérete nem lehet nagyobb, mint 5MB!'); 
			//kiterjesztés
			if( !in_array($extension, $this->allowed_extensions[$this->file_type] ) ) exit('A fájl kiterjesztése nem megengedett!'); 
			//mime típus
			if( !in_array($mime, $this->allowed_mime_types[$this->file_type]) ) exit('A fájl kiterjesztése nem megengedett!'); 
			
			//fájlnév generálása, áthelyezése
			//átnevezéshez a tmp nevet használjuk hashelve
			$new_base_name = hash('md5', $tmp);
			$new_file_name = $new_base_name.'.'.$extension;
			
			move_uploaded_file($tmp, $this->upload_paths[$this->file_role].$new_file_name);
			
			//feltöltött fájlnév vissszaadása
			return $new_file_name;

		}

		//megadott CSV fájl feldolgozása (diákok regisztrálásakor használt CSV)
		public static function parseCSV($_file){
			//fájl adatainak kigyűjtése a lokális változókba
			$file = $_file;
			$file_name = $file['name'];
			$file_tmp = $file['tmp_name'];
			$file_ext = strtolower( pathinfo($file_name)['extension'] );
			$allowed_formats = array('application/vnd.ms-excel','text/plain','text/csv','text/tsv');

			//feltöltési hiba
			if( $file['error'] > 0 ) exit('Hiba történt a feltöltés közben!');
			//kiterjesztés
			if( $file_ext != 'csv' ) exit('Csak CSV fájl feltöltése megengedett!');
			//mime típus
			if( !in_array($file['type'], $allowed_formats) ) exit('Csak CSV fájl feltöltése megengedett!');
			
			//az array_map a $data tömbbe helyezi a feltöltött fájl sorait
			$data = array_map('str_getcsv', file($file_tmp));
			$output = array();

			//adatok feldarabolása és kimenet készítése
			foreach( $data as $d ){
				$s = explode(';', $d[0]);

				$output[] = array(
					'name'	=> $s[0],
					'email'	=> $s[1]
				);
			}

			return $output;
		}
	}

?>


<?php if( Session::get('user-type') === 1 ): ?>
	<button class="btn-rect bg-2">Feladatlap létrehozása</button>
<?php endif; ?>

