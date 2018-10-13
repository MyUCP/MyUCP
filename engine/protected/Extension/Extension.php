<?php
/**
 * MyUCP
 */

namespace MyUCP\Extension;

class Extension
{
    /**
     * @var array
     */
    protected $alias = [];

    /**
     * @var array
     */
    protected $extensions = [];

    /**
     * @var array
     */
    protected $bootedExtensions = [];

    /**
     * @var \Application
     */
    protected $app;

    /**
     * Extension constructor.
     *
     * @param \Application $app
     * @throws \DebugException
     */
    public function __construct(\Application $app)
    {
        $this->app = $app;

        $this->load();
    }

    /**
     * @throws \DebugException
     */
    protected function load()
    {
        $extensions = config()->extensions;

        foreach($extensions['boot'] as $alias => $extension)
        {
            $implements = class_implements($extension);

            if(!in_array(BootExtensionable::class, $implements))
                throw new \DebugException("Расширение {$extension} не реализует интерфейс " . BootExtensionable::class . " для инициализации его при запуске приложения");

            $this->alias[$alias] = $extension;
            $this->bootedExtensions[$extension] = null;
        }

        unset($extensions['boot']);

        foreach ($extensions as $alias => $extension) {
            $this->alias[$alias] = $extension;
            $this->extensions[$extension] = null;
        }
    }

    /**
     * @return void
     */
    public function boot()
    {
        foreach ($this->bootedExtensions as $extension => $instance)
        {
            $this->bootedExtensions[$extension] = (new $extension())->bootstrap($this->app);
            $this->extensions[$extension] = &$this->bootedExtensions[$extension];
        }
    }

    /**
     * @param $alias
     * @param array $args
     * @return Extensionable
     * @throws \DebugException
     */
    public function run($alias, ...$args)
    {
        if(!isset($this->alias[$alias]))
            throw new \DebugException("{$alias} не является расширением");

        $extension = $this->alias[$alias];

        if(!$this->isExtensions($extension))
            throw new \DebugException("Класс {$extension} не является расширением");

        if(!array_key_exists($extension, $this->extensions))
            $this->runNoStack($extension);

        if(!is_null($this->extensions[$extension])) {
            return $this->extensions[$extension];
        }

        $this->extensions[$extension] = new $extension($this->app, ...$args);
        $this->extensions[$extension]->run();

        return $this->extensions[$extension];
    }

    /**
     * @param $alias
     * @param mixed ...$args
     * @return Extensionable
     * @throws \DebugException
     */
    public function reRun($alias, ...$args)
    {
        if(!isset($this->alias[$alias]))
            throw new \DebugException("{$alias} не является расширением");

        $extension = $this->alias[$alias];

        if(!$this->isExtensions($extension))
            throw new \DebugException("Класс {$extension} не является расширением");

        if(!array_key_exists($extension, $this->extensions))
            throw new \DebugException("Класс {$extension} не указан в стэке расширений");

        unset($this->extensions[$extension]);

        $this->extensions[$extension] = null;

        return $this->run($extension, ...$args);
    }

    /**
     * @param Extensionable $extension
     */
    public function runNoStack($extension)
    {
        $this->extensions[$extension] = null;
    }

    /**
     * @param $extension
     * @return bool
     */
    protected function isExtensions($extension)
    {
        $implements = class_implements($extension);

        if(!in_array(Extensionable::class, $implements))
            return false;

        return true;
    }
}