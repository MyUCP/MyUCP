<?php

namespace MyUCP\Views;

use MyUCP\Support\App;
use MyUCP\Support\Str;
use MyUCP\Views\Zara\Zara;

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
     * @var Zara
     */
    protected $zara;

    /**
     * ViewCompiler constructor.
     */
    public function __construct()
    {
        $this->zara = App::make(Zara::class);
    }

    /**
     * @param View $view
     * @return string
     */
    public function compile(View $view)
    {
        $path = $view->getPath();
        $compiledPath = $this->getCompiledPath($path);

        App::make(ViewFactory::class)->shareData("_zara", $this->zara);

        if($this->isCached($compiledPath)) {
            $view->setPath($compiledPath);

            return $view->getContents();
        }

        if($this->isZara($path)) {
            $this->zara->compile($view, $compiledPath);
        } else {
            $this->cacheView($path, $compiledPath);
        }

        $view->setPath($compiledPath);

        return $view->getContents();
    }

    /**
     * @param $path
     * @return bool
     */
    protected function isZara($path)
    {
        return Str::contains($path, '.zara.php');
    }

    /**
     * @param $compiled
     *
     * @return bool
     */
    protected function isCached($compiled)
    {
        if(! file_exists($compiled)) {
            return false;
        }

        return true;
    }

    protected function getCompiledPath($path)
    {
        return App::assetsPath('cache/views/' . md5_file($path));
    }

    /**
     * @param $path
     * @param $compiledPath
     *
     * @return bool|int
     */
    protected function cacheView($path, $compiledPath)
    {
        return file_put_contents($compiledPath, file_get_contents($path));
    }
}