<?php

class File
{
    /**
     * @var string
     */
	protected $originalName;

    /**
     * @var int
     */
	protected $size;

    /**
     * @var string
     */
	protected $mimeType;

    /**
     * @var int
     */
	protected $error;

    /**
     * @var string
     */
	protected $dirName;

    /**
     * @var string
     */
	protected $tmpPath;

    /**
     * @var string
     */
	protected $md5_file;

    /**
     * @var string
     */
	protected $extension;

    /**
     * @var array
     */
	protected $file;

    /**
     * File constructor.
     * @param array $file
     * @throws UploadException
     */
	public function __construct($file)
    {
		$this->file = $file;

		$this->getInfoFile();

		if($this->getError() != UPLOAD_ERR_OK)
		    throw new UploadException($this->getError());

		return $this;
	}

    /**
     * @param null|string $path
     * @param null|string $name
     * @return bool
     */
	public function move($path = null, $name = null)
    {
		$path = ($path == null) ? app()->assetsPath('files/') : $path;
		$name = ($name == null) ? md5($this->getOriginalName()). "." . $this->getExtension() : $name;

		if(copy($this->getTmpPath(), $path.$name))
		    return $path.$name;

		return false;
	}

    /**
     * Alias: move()
     *
     * @param null $path
     * @param null $name
     * @return bool
     */
	public function save($path = null, $name = null)
    {
        return $this->move($path, $name);
    }

    /**
     * @return string
     */
	public function getExtension()
    {
		return $this->extension;
	}

    /**
     * @return string
     */
	public function getMd5()
    {
		return $this->md5_file;
	}

    /**
     * @return mixed
     */
	public function getTmpPath()
    {
		return str_replace('\\\\\\\\', "\\", $this->tmpPath);
	}

    /**
     * @return string
     */
	public function getPath()
    {
		return $this->dirName;
	}

    /**
     * @return int
     */
	public function getError()
    {
		return $this->error;
	}

    /**
     * @return string
     */
	public function getMimeType()
    {
		return $this->mimeType;
	}

    /**
     * @return int
     */
	public function getSize()
    {
		return $this->size;
	}

    /**
     * @return string
     */
	public function getOriginalName()
    {
		return $this->originalName;
	}

    /**
     *
     */
  	protected function getInfoFile()
    {
  		$file = new SplFileInfo($this->file['tmp_name']);
  		$originalFile = new SplFileInfo($this->file['name']);

  		$this->originalName = $originalFile->getFilename();
  		$this->size = $this->file['size'];
  		$this->mimeType = $this->file['type'];
  		$this->error = $this->file['error'];
  		$this->dirName = $file->getPath();
  		$this->tmpPath = $this->file['tmp_name'];
  		$this->md5_file = md5($this->file['tmp_name']);
  		$this->extension = $originalFile->getExtension();
  	}
}