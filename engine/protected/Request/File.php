<?php

class File {

	public $originalName;
	public $size;
	public $mimeType;
	public $error;
	public $dirName;
	public $tmpPath;
	public $md5_file;
	public $extension;

	private $file;
	
	public function __construct($name) {

		$this->file = $_FILES[$name];
		$this->getInfoFile();

		return $this;
	}

	public function move($path = null, $name = null) {

		$path = ($path == null) ? "./assets/files/" : $path;
		$name = ($name == null) ? md5($this->getOriginalName()). "." . $this->getExtension() : $name;

		return move_uploaded_file($this->getTmpPath(), $path.$name);
	}
	
	public function getExtension() {
		return $this->extension;
	}

	public function getMd5() {
		return $this->md5_file;
	}

	public function getTmpPath() {
		return $this->tmpPath;
	}

	public function getPath() {
		return $this->dirName;
	}

	public function getError() {
		return $this->error;
	}

	public function getMimeType() {
		return $this->mimeType;
	}

	public function getSize() {
		return $this->size;
	}

	public function getOriginalName() {
		return $this->originalName;
	}

  	private function getInfoFile() {

  		$file = new SplFileInfo($this->file['tmp_name']);
  		$originalFile = new SplFileInfo($this->file['name']);

  		$this->originalName = $originalFile->getFilename();
  		$this->size = $this->file['size'];
  		$this->mimeType = $this->file['type'];
  		$this->error = $this->file['error'];
  		$this->dirName = $file->getPath();
  		$this->tmpPath = $this->file['tmp_name'];
  		$this->md5_file = md5_file($this->file['tmp_name']);
  		$this->extension = $originalFile->getExtension();
  	}
}