<?php

namespace MyUCP\Model;

use MyUCP\Foundation\Application;
use MyUCP\Model\Traits\Builder as ModelBuilder;
use MyUCP\Support\Str;

class Model
{
    use ModelBuilder;

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var null|string
     */
    protected $table = null;

    /**
     * @var string
     */
    protected $primary_key = 'id';

    /**
     * Model constructor.
     *
     * @param Application $app
     */
    public function __construct($app)
    {
        $this->app = $app;

        $this->table = $this->getTable();
    }

    /**
     * @return mixed|null|string
     */
    public function getTable()
    {
        if (is_null($this->table)) {
            return str_replace(
                '\\', '', Str::snake(plural_phrase(
                    str_replace('Model', '', class_basename($this))
                ))
            );
        }

        return $this->table;
    }

    /**
     * @param $key
     *
     * @return bool|mixed
     */
    public function __get($key)
    {
        return $this->app->$key;
    }

    /**
     * @param $key
     * @param $value
     */
    public function __set($key, $value)
    {
        $this->app->$key = $value;
    }
}
