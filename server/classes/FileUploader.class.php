<?php

	class FileUploader{
		
		private $file;
		private $save_path;
		private $file_type; //image vagy file
		private $file_role; //avatar, task_image, solution_file
		private $max_size = 5 * 1024 * 1024; //5MB
		private $save_paths = array(
			'avatar'		=> '../../server/uploads/avatars/',
			'task_image'	=> '../../server/uploads/images/',
			'solution_file'	=> '../../server/uploads/files/'
		);
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
		private $allowed_extensions = array(
			'image'	=> array('jpeg', 'jpg', 'png', 'gif'),
			'file'	=> array('rar', 'zip')
		);
		
		public function __construct($_file, $_file_type, $_file_role){
			$this->file 		= $_file;
			$this->file_type 	= $_file_type;
			$this->file_role	= $_file_role;
		}
		

		public function checkFile(){
			$size = $this->file['size'];
			$name = $this->file['name'];
			$tmp = $this->file['tmp_name'];
			$extension = strtolower( pathinfo($name)['extension'] );
			$mime = $this->file['type'];
			
			if( !is_uploaded_file($tmp) ){ exit('Hiba történt a feltöltés közben!'); }
			
			if( $size > $this->max_size ){ exit('A fájl mérete nem lehet nagyobb, mint 5MB!'); }
			
			if( !in_array($extension, $this->allowed_extensions[$this->file_type] ) ){ exit('A fájl kiterjesztése nem megengedett!'); }
			
			if( !in_array($mime, $this->allowed_mime_types[$this->file_type]) ){ exit('A fájl kiterjesztése nem megengedett!'); }
			
			$new_base_name = hash('md5', $tmp.microtime(true) );
			$new_file_name = $new_base_name.'.'.$extension;
			
			move_uploaded_file($tmp, $this->save_paths[$this->file_role].$new_file_name);
			
			return $new_file_name;

		}
	}

?>