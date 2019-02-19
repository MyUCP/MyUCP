<?php

namespace MyUCP\Model;

use Exception;
use MyUCP\Application;
use MyUCP\Database\Builder;
use MyUCP\Database\DBCollection;
use MyUCP\Database\Query;
use MyUCP\Routing\HttpException;
use MyUCP\Support\Str;

class Model
{
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
    protected $primary_key = "id";

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
                    str_replace("Model", "", class_basename($this))
                ))
            );
        }

        return $this->table;
    }

    /**
     * @param $key
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

    /**
     * @param $name
     * @return $this
     */
    public function table($name)
    {
        $this->table = $name;

        return $this;
    }

    /**
     * @param array $data
     * @return bool|resource
     */
    public function create(array $data)
    {
        return $this->query()->insertGetId($data);
    }

    /**
     * @param $column
     * @param null $operator
     * @param null $value
     * @param string $boolean
     * @return Query
     */
    public function where($column, $operator = null, $value = null, $boolean = 'and')
    {
        return $this->query()->where($column, $operator, $value, $boolean);
    }

    /**
     * @param $column
     * @param null $operator
     * @param null $value
     * @return Query
     */
    public function orWhere($column, $operator = null, $value = null)
    {
        return $this->query()->orWhere($column, $operator, $value);
    }

    /**
     * @param $column
     * @param array $values
     * @param string $boolean
     * @param bool $not
     * @return Query
     */
    public function whereBetween($column, array $values, $boolean = 'and', $not = false)
    {
        return $this->query()->whereBetween($column, $values, $boolean, $not);
    }

    /**
     * @param $column
     * @param array $values
     * @param string $boolean
     * @return Query
     */
    public function whereNotBetween($column, array $values, $boolean = 'and')
    {
        return $this->query()->whereNotBetween($column, $values, $boolean);
    }

    /**
     * @param $column
     * @param $values
     * @param string $boolean
     * @param bool $not
     * @return Query
     */
    public function whereIn($column, $values, $boolean = 'and', $not = false)
    {
        return $this->query()->whereIn($column, $values, $boolean, $not);
    }

    /**
     * @param $column
     * @param $values
     * @param string $boolean
     * @return Query
     */
    public function whereNotIn($column, $values, $boolean = 'and')
    {
        return $this->query()->whereNotIn($column, $values, $boolean);
    }

    /**
     * @param $column
     * @param string $boolean
     * @param bool $not
     * @return Query
     */
    public function whereNull($column, $boolean = 'and', $not = false)
    {
        return $this->query()->whereNull($column, $boolean, $not);
    }

    /**
     * @param $column
     * @param string $boolean
     * @return mixed
     */
    public function whereNotNull($column, $boolean = 'and')
    {
        return $this->query()->whereNotNull($column, $boolean);
    }

    /**
     * @param $column
     * @param string $direction
     * @return Query
     */
    public function orderBy($column, $direction = 'asc')
    {
        return $this->query()->orderBy($column, $direction);
    }

    /**
     * @param $column
     * @param string $direction
     * @return Query
     */
    public function order($column, $direction = 'asc')
    {
        return $this->orderBy($column, $direction);
    }

    /**
     * @param  string  $column
     * @return Query
     */
    public function orderByDesc($column)
    {
        return $this->query()->orderByDesc($column);
    }

    /**
     * @param  string  $column
     * @return Query
     */
    public function latest($column)
    {
        return $this->query()->latest($column);
    }

    /**
     * @param  string  $column
     * @return Query
     */
    public function oldest($column)
    {
        return $this->query()->oldest($column);
    }

    /**
     * @param string $column
     * @param array $_
     * @return Query
     */
    public function select($column = "*", ...$_)
    {
        return $this->query()->select($column, ...$_);
    }

    /**
     * @param string $column
     * @param array $_
     * @return Query
     */
    public function addSelect($column = "*", ...$_)
    {
        return $this->query()->addSelect($column, ...$_);
    }

    /**
     * @param $offset
     * @param null $limit
     * @return Query
     */
    public function limit($offset, $limit = null)
    {
        return $this->query()->limit($offset, $limit);
    }

    /**
     * @param array $columns
     * @return DBCollection
     */
    public function get(array $columns = ['*'])
    {
        return $this->query()->get($columns);
    }

    /**
     * @param $id
     * @param array $columns
     * @return mixed
     */
    public function find($id, $columns = ['*'])
    {
        return $this->query()->where($this->primary_key, "=", $id)->first($columns);
    }

    /**
     * @param array $columns
     * @return mixed
     */
    public function first($columns = ['*'])
    {
        return $this->query()->first($columns);
    }

    /**
     * @param array $columns
     * @param string $exception
     * @param int $code
     * @param string $message
     * @return array|FALSE
     * @throws Exception|HttpException
     */
    public function firstOrError(array $columns = ['*'], $exception = HttpException::class, $code = 404, $message = "Страница не найдена")
    {
        return $this->query()->firstOrError($columns , $exception, $code, $message);
    }

    /**
     * @param $id
     * @param array $columns
     * @return array|FALSE
     * @throws Exception|HttpException
     */
    public function findOrError($id, $columns = ['*'])
    {
        return $this->query()->where($this->primary_key, '=', $id)->firstOrError($columns);
    }

    /**
     * @param $column
     * @return mixed
     */
    public function value($column)
    {
        return $this->query()->value($column);
    }

    /**
     * @param string $column
     * @return int
     */
    public function count($column = "*")
    {
        return $this->query()->count($column);
    }

    /**
     * @param $column
     * @return mixed
     */
    public function max($column)
    {
        return $this->query()->max($column);
    }

    /**
     * @param $column
     * @return mixed
     */
    public function min($column)
    {
        return $this->query()->min($column);
    }

    /**
     * @param $column
     * @return mixed
     */
    public function avg($column)
    {
        return $this->query()->avg($column);
    }

    /**
     * @param $column
     * @return mixed
     */
    public function sum($column)
    {
        return $this->query()->sum($column);
    }

    /**
     * @param array $groups
     * @return Query
     */
    public function groupBy(...$groups)
    {
        return $this->query()->groupBy(...$groups);
    }

    /**
     * @param $table
     * @param $first
     * @param null $operator
     * @param null $second
     * @param string $type
     * @return Query
     */
    public function join($table, $first, $operator = null, $second = null, $type = 'inner')
    {
        return $this->query()->join($table, $first, $operator, $second, $type);
    }

    /**
     * @param $table
     * @param $first
     * @param null $operator
     * @param null $second
     * @return Query
     */
    public function leftJoin($table, $first, $operator = null, $second = null)
    {
        return $this->query()->leftJoin($table, $first, $operator, $second);
    }

    /**
     * @param $table
     * @param $first
     * @param null $operator
     * @param null $second
     * @return Query
     */
    public function rightJoin($table, $first, $operator = null, $second = null)
    {
        return $this->query()->rightJoin($table, $first, $operator, $second);
    }

    /**
     * @param $table
     * @param $first
     * @param null $operator
     * @param null $second
     * @return Query
     */
    public function crossJoin($table, $first, $operator = null, $second = null)
    {
        return $this->query()->crossJoin($table, $first, $operator, $second);
    }

    /**
     * @param array $values
     * @return Query
     */
    public function set(array $values)
    {
        return $this->query()->set($values);
    }

    /**
     * @param array $values
     * @return mixed
     */
    public function update(array $values = [])
    {
        return $this->query()->update($values);
    }

    /**
     * @return FALSE|resource
     */
    public function delete()
    {
        return $this->query()->delete();
    }

    /**
     * @return Query
     */
    public function query()
    {
        return Builder::query($this->table);
    }
}