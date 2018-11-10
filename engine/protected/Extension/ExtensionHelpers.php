<?php

namespace MyUCP\Extension;

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

        $directory = \Str::replaceLast($currentExtensions, "", (new \ReflectionClass($this))->getFileName());
        $directory = \Str::replaceLast("\\", "", $directory);

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
        \Controller::alias($name, $path);
    }

    /**
     * Предзагрузка шаблона
     */
    public function view($name, $path = null)
    {
        \View::preLoad($name, $path);
    }
}