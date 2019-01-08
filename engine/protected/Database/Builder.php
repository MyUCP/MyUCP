<?php
/*
* MyUCP
*/

class Builder
{
    /**
     * @var DB
     */
    protected $db;

    /**
     * @var Query
     */
    protected $lastQuery;

    /**
     * @return Builder
     */
    public static function getInstance()
    {
        return app()->make(Builder::class);
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if($name == 'lastQuery')
            return $this->lastQuery($arguments);

        return $this;
    }

    /**
     * @param $name
     * @param $arguments
     * @return Builder|mixed
     */
    public static function __callStatic($name, $arguments = null)
    {
        $instance = self::getInstance();

        if($name == 'lastQuery')
            return $instance->lastQuery(empty($arguments) ? null : $arguments);

        return $instance;
    }

    /**
     * @param $table
     * @return Query
     */
    public static function table($table)
    {
        $instance = self::getInstance();

        return $instance->query($table);
    }

    /**
     * @param null|string|RawQuery $table
     * @return Query
     */
    public static function query($table = null)
    {
        $instance = self::getInstance();

        return $instance->lastQuery(new Query($table));
    }

    /**
     * @param string|RawQuery $table
     * @param array $data
     * @return bool|resource
     */
    public static function insert($table, array $data)
    {
        return Query::table($table)->insert($data);
    }

    /**
     * @param Query $query
     * @return Query
     */
    protected function lastQuery(Query $query = null)
    {
        if(is_null($query))
            return $this->lastQuery;

        return $this->lastQuery = $query;
    }
}