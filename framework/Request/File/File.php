<?php

namespace MyUCP\Request\File;

use SplFileInfo;

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
     *
     * @param array $file
     */
    public function __construct($file)
    {
        $this->file = $file;

        $this->getInfoFile();

        return $this;
    }

    /**
     * @param bool $throw
     *
     * @throws UploadException
     *
     * @return bool
     */
    public function isUploaded($throw = false)
    {
        if ($this->getError() != UPLOAD_ERR_OK) {
            if ($throw) {
                throw new UploadException($this->getError());
            }

            return false;
        }

        return true;
    }

    /**
     * @param null|string $path
     * @param null|string $name
     *
     * @throws UploadException
     *
     * @return bool
     */
    public function move($path = null, $name = null)
    {
        $this->isUploaded(true);

        $defaultPath = 'files/';

        $filePath = ($path == null) ? app()->assetsPath($defaultPath) : $path;

        $name = ($name == null) ? md5($this->getOriginalName().time()).'.'.$this->getExtension() : $name;

        if (copy($this->getTmpPath(), $filePath.$name)) {
            return ($path ?? $defaultPath).$name;
        }

        return false;
    }

    /**
     * Alias: move().
     *
     * @param null $path
     * @param null $name
     *
     * @throws UploadException
     *
     * @return bool
     */
    public function save($path = null, $name = null)
    {
        return $this->move($path, $name);
    }

    /**
     * Save with some name.
     *
     * @param null $name
     *
     * @throws UploadException
     *
     * @return bool
     */
    public function saveAs($name = null)
    {
        return $this->save(null, $name);
    }

    /**
     * @throws UploadException
     *
     * @return string
     */
    public function getExtension()
    {
        $this->isUploaded(true);

        return $this->extension;
    }

    /**
     * @throws UploadException
     *
     * @return string
     */
    public function getMd5()
    {
        $this->isUploaded(true);

        return $this->md5_file;
    }

    /**
     * @throws UploadException
     *
     * @return mixed
     */
    public function getTmpPath()
    {
        $this->isUploaded(true);

        return str_replace('\\\\\\\\', '\\', $this->tmpPath);
    }

    /**
     * @throws UploadException
     *
     * @return string
     */
    public function getPath()
    {
        $this->isUploaded(true);

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
     * @throws UploadException
     *
     * @return string
     */
    public function getMimeType()
    {
        $this->isUploaded(true);

        return $this->mimeType;
    }

    /**
     * @throws UploadException
     *
     * @return int
     */
    public function getSize()
    {
        $this->isUploaded(true);

        return $this->size;
    }

    /**
     * @throws UploadException
     *
     * @return string
     */
    public function getOriginalName()
    {
        $this->isUploaded(true);

        return $this->originalName;
    }

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
