<?php
	class UploadFiles{
		public $acceptedTypes = array();
		public $uploadFile = false;
		public $fileType = '';
		public $fileName = '';
		public $fileExt = '';
		public $fileDir = '';

		function __construct($acceptedTypes=false){
			if($acceptedTypes !== false){$this->acceptedTypes = $acceptedTypes;}

			$this->uploadFile = isset($_FILES['file']);
			if($this->uploadFile){
				if($_FILES['file']['name'] != ''){
					if(isset($this->acceptedTypes[$_FILES['file']['type']])){
						$this->fileName = $_FILES['file']['name'];
						$this->fileType = $_FILES['file']['type'];
						$this->fileExt = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
					}
				}
			}
		}

		function save_with_new_name($name){
			$this->fileName = $name.'.'.$this->fileExt;
			return $this->save();
		}

		function save(){
			$fileDirExist = file_exists($this->fileDir);

			if(!$fileDirExist){
				if(mkdir($this->fileDir,0755,true)){$fileDirExist = true;}
			}

			if($fileDirExist){
				$fileDir = $this->fileDir.$this->fileName;
				if(file_exists($fileDir)){unlink($fileDir);}
				$fileDirExist = move_uploaded_file($_FILES['file']['tmp_name'],$fileDir);
			}

			return $fileDirExist;
		}

		function delete(){
			$delete = false;
			$fileDir = $this->fileDir.$this->fileName;
			if(file_exists($fileDir)){
				$delete = unlink($fileDir);
			}
			return $delete;
		}
	}
?>