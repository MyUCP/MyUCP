<?php

namespace MyUCP\Views;

use MyUCP\Support\Str;

class ViewCompiler
{
    /**
     * @var string
     */
    protected $filename;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * ViewCompiler constructor.
     */
    public function __construct()
    {
        //
    }

    /**
     * @return string
     */
    public function getFilePath()
    {
        return app()->viewsPath($this->normalize($this->filename));
    }

    /**
     * @param $name
     * @return mixed
     */
    public function normalize($name)
    {
        if(! Str::contains($this->filename, '/')) {
            $name = str_replace('/', '.', $name);
        }

        return $name;
    }
}