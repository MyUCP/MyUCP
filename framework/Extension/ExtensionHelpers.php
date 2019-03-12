<?php

namespace MyUCP\Extension;

use MyUCP\Controller\Controller;
use MyUCP\Support\Str;
use MyUCP\Views\View;

class ExtensionHelpers
{
    /**
     * Получить путь к расширению
     *
     * @param $path
     * @return string
     * @throws \ReflectionException
     */
    public function path($path)
    {
        $currentExtensions = basename((new \ReflectionClass($this))->getFileName());

        $directory = Str::replaceLast($currentExtensions, "", (new \ReflectionClass($this))->getFileName());
        $directory = Str::replaceLast("\\", "", $directory);

        return $directory . DIRECTORY_SEPARATOR . ltrim($path, '/');
    }

    /**
     * Предзагрузка контроллера
     *
     * @param $name
     * @param null $path
     */
    public function controller($name, $path = null)
    {
        Controller::alias($name, $path);
    }

    /**
     * Предзагрузка шаблона
     *
     * @param array|string $name
     * @param string|null $path
     */
    public function view($name, $path = null)
    {
        View::preload($name, $path);
    }
}