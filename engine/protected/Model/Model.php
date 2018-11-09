<?php
/*
* MyUCP
*/

class Model
{
    /**
     * @var Application
     */
    private $app;

    /**
     * @var null|string
     */
    public $table = null;

    /**
     * @var string
     */
    public $primary_key = "id";

    /**
     * @var Builder
     */
    protected $Builder;

    /**
     * Model constructor.
     * @param $registry
     */
    public function __construct($app)
    {
        $this->app = $app;
        $this->Builder = new Builder();
        $this->table = ($this->table == null) ? mb_strtolower(str_replace("Model", "", get_class($this))."s") : $this->table;
        $this->Builder->from($this->table);
    }

    /**
     * @param $key
     * @return bool|mixed
     */
    public function __get($key)
    {
        return $this->registry->$key;
    }

    /**
     * @param $key
     * @param $value
     */
    public function __set($key, $value)
    {
        $this->registry->$key = $value;
    }

    /**
     * @param $name
     * @return $this
     */
    public function table($name)
    {
        $this->table = $name;
        $this->Builder->from($this->table);

        return $this;
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function create($data = [])
    {
        return $this->Builder->from($this->table)->create($data);
    }

    /**
     * @return $this
     * @throws DebugException
     */
    public function where()
    {
        $this->Builder->where(func_get_args());

        return $this;
    }

    /**
     * @return $this
     * @throws DebugException
     */
    public function orWhere()
    {
        $this->Builder->orWhere(func_get_args());

        return $this;
    }

    /**
     * @param $row
     * @param array $condition
     * @return $this
     * @throws DebugException
     */
    public function whereBetween($row, $condition = [])
    {
        $this->Builder->whereBetween($row, $condition);

        return $this;
    }

    /**
     * @param $row
     * @param array $condition
     * @return $this
     * @throws DebugException
     */
    public function whereNotBetween($row, $condition = [])
    {
        $this->Builder->whereNotBetween($row, $condition);

        return $this;
    }

    /**
     * @param $row
     * @param array $condition
     * @return $this
     * @throws DebugException
     */
    public function whereIn($row, $condition = [])
    {
        $this->Builder->whereIn($row, $condition);

        return $this;
    }

    /**
     * @param $row
     * @param array $condition
     * @return $this
     * @throws DebugException
     */
    public function whereNotIn($row, $condition = [])
    {
        $this->Builder->whereNotIn($row, $condition);

        return $this;
    }

    /**
     * @param null $row
     * @return $this
     * @throws DebugException
     */
    public function whereNull($row = null)
    {
        $this->Builder->whereNull($row);

        return $this;
    }

    /**
     * @param null $row
     * @return $this
     * @throws DebugException
     */
    public function whereNotNull($row = null)
    {
        $this->Builder->whereNotNull($row);

        return $this;
    }

    /**
     * @param $row
     * @param $type
     * @return $this
     */
    public function order($row, $type)
    {
        $this->Builder->order($row, $type);
        return $this;
    }

    /**
     * @param $row
     * @return $this
     */
    public function select($row)
    {
        $this->Builder->select($row);

        return $this;
    }

    /**
     * @param $row
     * @return $this
     */
    public function addSelect($row)
    {
        $this->Builder->addSelect($row);

        return $this;
    }

    /**
     * @return $this
     */
    public function limit()
    {
        $this->Builder->limit(func_get_args());

        return $this;
    }

    /**
     * @return mixed
     */
    public function get()
    {
        return $this->Builder->from($this->table)->get();
    }

    /**
     * @param $key
     * @return mixed
     * @throws DebugException
     */
    public function find($key)
    {
        return $this->Builder->from($this->table)->where($this->primary_key, $key)->first();
    }

    /**
     * @param null $key
     * @return mixed
     * @throws DebugException
     */
    public function first($key = null)
    {
        if($key == null)
            return $this->Builder->from($this->table)->first();

        return $this->Builder->from($this->table)->where($this->primary_key, $key)->first();
    }

    /**
     * @param null $key
     * @return HttpException
     * @throws DebugException
     */
    public function firstOrError($key = null)
    {
        if($key == null)
            return $this->Builder->from($this->table)->firstOrError();

        return $this->Builder->from($this->table)->where($this->primary_key, $key)->firstOrError();
    }

    /**
     * @param $value
     * @return mixed
     */
    public function value($value)
    {
        return $this->Builder->from($this->table)->value($value);
    }

    /**
     * @return mixed
     */
    public function count()
    {
        return $this->Builder->from($this->table)->count();
    }

    /**
     * @param null $row
     * @return mixed
     * @throws DebugException
     */
    public function max($row = null)
    {
        return $this->Builder->from($this->table)->max($row);
    }

    /**
     * @param null $row
     * @return mixed
     * @throws DebugException
     */
    public function min($row = null)
    {
        return $this->Builder->from($this->table)->min($row);
    }

    /**
     * @param null $row
     * @return mixed
     * @throws DebugException
     */
    public function avg($row = null)
    {
        return $this->Builder->from($this->table)->avg($row);
    }

    /**
     * @param null $row
     * @return mixed
     * @throws DebugException
     */
    public function sum($row = null)
    {
        return $this->Builder->from($this->table)->sum($row);
    }

    /**
     * @param $row
     * @return $this
     */
    public function groupBy($row)
    {
        $this->Builder->groupBy($row);

        return $this;
    }

    /**
     * @return $this
     * @throws DebugException
     */
    public function join()
    {
        $this->Builder->join(func_get_args());

        return $this;
    }

    /**
     * @return $this
     * @throws DebugException
     */
    public function leftJoin()
    {
        $this->Builder->leftJoin(func_get_args());

        return $this;
    }

    /**
     * @return $this
     * @throws DebugException
     */
    public function rightJoin()
    {
        $this->Builder->rightJoin(func_get_args());

        return $this;
    }

    /**
     * @return $this
     * @throws DebugException
     */
    public function crossJoin()
    {
        $this->Builder->crossJoin(func_get_args());

        return $this;
    }

    /**
     * @return $this
     */
    public function set()
    {
        $this->Builder->set(func_get_args()[0]);

        return $this;
    }

    /**
     * @return mixed
     */
    public function update()
    {
        return $this->Builder->from($this->table)->update();
    }

    /**
     * @return mixed
     */
    public function delete()
    {
        return $this->Builder->from($this->table)->delete();
    }
}