<?php
/*
* MyUCP
*/

class Query
{
    /**
     * @var DB
     */
    protected $db;

    /**
     * @var array
     */
    protected $operators = [
        '=', '<', '>', '<=', '>=', '<>', '!=',
        'like', 'like binary', 'not like', 'between', 'ilike',
        '&', '|', '^', '<<', '>>',
        'rlike', 'regexp', 'not regexp',
        '~', '~*', '!~', '!~*', 'similar to',
        'not similar to',
    ];

    /**
     * @var array
     */
    protected $functions = [
        'NOW()', 'CURDATE()', 'RAND()',
    ];

    /**
     * @var array
     */
    protected $tables = [];

    /**
     * @var integer
     */
    protected $limit = null;

    /**
     * @var integer
     */
    protected $offset = null;

    /**
     * @var array
     */
    protected $groups = [];

    /**
     * @var array
     */
    protected $havings = [];

    /**
     * @var array
     */
    protected $orders = [];

    /**
     * @var array
     */
    protected $columns = [];

    /**
     * @var array
     */
    protected $wheres = [];

    /**
     * @var array
     */
    protected $sets = [];

    /**
     * @var array
     */
    protected $joins = [];

    /**
     * Query constructor.
     * @param null $table
     */
    public function __construct($table = null)
    {
        $this->db = app()->db;

        if(!is_null($table)) {
            $this->from($table);
        }
    }

    /**
     * @param string $table
     * @param array $_
     * @return Query
     */
    public static function table($table, ...$_)
    {
        return (new self())->from($table, ...$_);
    }

    /**
     * @param string $table
     * @param array $_
     * @return Query
     */
    public function from($table, ...$_)
    {
        // Определяем, является ли переданное значение выражением
        // если оно таковым есть, получаем его значение
        if($this->isRaw($table)) {
            $this->tables[] = $this->getValue($table);
        } else {
            $this->tables[] = $table;
        }

        // Если остальные переданные параметры не пусты
        // то добавляем их как отдельные элементы
        if(!empty($_)) {
            foreach ($_ as $table) {
                if($this->isRaw($table)) {
                    $this->tables[] = $this->getValue($table);
                } else {
                    $this->tables[] = $table;
                }
            }
        }

        return $this;
    }

    public function fromAs($table, $as)
    {
        $query = $this->raw('('. $table .') as ' . $as);

        return $this->from($query);
    }

    /**
     * @param array $data
     * @return bool|resource
     */
    public function insert(array $data)
    {
        if(empty($data)) {
            return true;
        }

        $columns = ($this->columnize(array_keys($data)));

        $parameters = $this->parameterize($data);

        $sql = "insert into {$this->getTable(true)} ($columns) values ($parameters)";

        return $this->db->query($sql);
    }

    /**
     * @param array $data
     * @return int
     */
    public function insertGetId(array $data)
    {
        $this->insert($data);

        return $this->db->insertId();
    }

    /**
     * @param string $column
     * @param array $_
     * @return $this
     */
    public function select($column = "*", ...$_)
    {
        if(!in_array($column, $this->columns)) {
            // Определяем, является ли переданное значение выражением
            // если оно таковым есть, получаем его значение
            if($this->isRaw($column)) {
                $this->columns[] = $this->getValue($column);
            } else {
                $this->columns[] = $column;
            }
        }

        // Если остальные переданные параметры не пусты
        // то добавляем их как отдельные элементы
        if(!empty($_)) {
            foreach ($_ as $column) {
                if(!in_array($column, $this->columns)) {
                    // Определяем, является ли переданное значение выражением
                    // если оно таковым есть, получаем его значение
                    if($this->isRaw($column)) {
                        $this->columns[] = $this->getValue($column);
                    } else {
                        $this->columns[] = $column;
                    }
                }
            }
        }

        return $this;
    }

    /**
     * @param $column
     * @param array $_
     * @return Query
     */
    public function addSelect($column, ...$_)
    {
        return $this->select($column, ...$_);
    }

    /**
     * @param string $column
     * @param string $as
     * @return Query
     */
    public function selectAs($column, $as)
    {
        $query = $this->raw('('. $column .') as ' . $as);

        return $this->select($query);
    }

    /**
     * @param string $raw
     * @return Query
     */
    public function selectRaw($raw)
    {
        return $this->select($this->raw($raw));
    }

    /**
     * @param int $value
     * @return Query
     */
    public function take($value)
    {
        return $this->limit($value);
    }

    /**
     * @param int $value
     * @return Query
     */
    public function skip($value)
    {
        return $this->offset($value);
    }

    /**
     * @param int $value
     * @return Query
     */
    public function offset($value)
    {
        return $this->limit($value, max(0, $value));
    }

    /**
     * @param int $offset
     * @param int|null $limit
     * @return $this
     */
    public function limit($offset, $limit = null)
    {
        if(is_null($limit)) {
            $this->limit = $offset;
        } else {
            $this->offset = $offset;
            $this->limit = $limit;
        }

        return $this;
    }

    /**
     * @param mixed ...$groups
     * @return $this
     */
    public function groupBy(...$groups)
    {
        foreach ($groups as $group) {
            $this->groups[] = $group;
        }

        return $this;
    }

    /**
     * @param  string  $column
     * @param  string  $direction
     * @return $this
     */
    public function orderBy($column, $direction = 'asc')
    {
        $this->orders[] = [
            'column' => $column,
            'direction' => strtolower($direction) === 'asc' ? 'asc' : 'desc',
        ];

        return $this;
    }

    /**
     * @param  string  $column
     * @return $this
     */
    public function orderByDesc($column)
    {
        return $this->orderBy($column, 'desc');
    }

    /**
     * @param  string  $column
     * @return Query
     */
    public function latest($column)
    {
        return $this->orderBy($column, 'desc');
    }

    /**
     * @param  string  $column
     * @return Query
     */
    public function oldest($column)
    {
        return $this->orderBy($column, 'asc');
    }

    /**
     * @param string $column
     * @param null|mixed $operator
     * @param null|mixed $value
     * @param string $boolean
     * @return $this
     */
    public function having($column, $operator = null, $value = null, $boolean = 'and')
    {
        $type = 'Basic';

        [$value, $operator] = $this->prepareValueAndOperator(
            $value, $operator, func_num_args() === 2
        );

        // If the given operator is not found in the list of valid operators we will
        // assume that the developer is just short-cutting the '=' operators and
        // we will set the operators to '=' and set the values appropriately.
        if ($this->invalidOperator($operator)) {
            [$value, $operator] = [$operator, '='];
        }

        $this->havings[] = compact('type', 'column', 'operator', 'value', 'boolean');

        return $this;
    }

    /**
     * @param $column
     * @param null $operator
     * @param null $value
     * @return Query
     */
    public function orHaving($column, $operator = null, $value = null)
    {
        [$value, $operator] = $this->prepareValueAndOperator(
            $value, $operator, func_num_args() === 2
        );

        return $this->having($column, $operator, $value, 'or');
    }

    /**
     * @param $column
     * @param array $values
     * @param string $boolean
     * @param bool $not
     * @return $this
     */
    public function havingBetween($column, array $values, $boolean = 'and', $not = false)
    {
        $type = 'between';

        $this->havings[] = compact('type', 'column', 'values', 'boolean', 'not');

        return $this;
    }

    /**
     * @param string|RawQuery $sql
     * @param string $boolean
     * @return $this
     */
    public function havingRaw($sql, $boolean = 'and')
    {
        $type = 'Raw';

        $this->havings[] = compact('type', 'sql', 'boolean');

        return $this;
    }

    /**
     * @param string|RawQuery $sql
     * @return Query
     */
    public function orHavingRaw($sql)
    {
        return $this->havingRaw($sql, 'or');
    }

    /**
     * @param $column
     * @param null $operator
     * @param null $value
     * @param string $boolean
     * @return $this
     */
    public function where($column, $operator = null, $value = null, $boolean = 'and')
    {
        // TODO: $column is array

        [$value, $operator] = $this->prepareValueAndOperator(
            $value, $operator, func_num_args() === 2
        );

        if ($this->invalidOperator($operator)) {
            [$value, $operator] = [$operator, '='];
        }

        if (is_null($value)) {
            return $this->whereNull($column, $boolean, $operator !== '=');
        }

        if (Str::contains($column, '->') && is_bool($value)) {
            $value = $this->raw($value ? 'true' : 'false');
        }

        $type = 'Basic';

        $this->wheres[] = compact(
            'type', 'column', 'operator', 'value', 'boolean'
        );

        return $this;
    }

    /**
     * @param $column
     * @param null $operator
     * @param null $value
     * @return Query
     */
    public function orWhere($column, $operator = null, $value = null)
    {
        [$value, $operator] = $this->prepareValueAndOperator(
            $value, $operator, func_num_args() === 2
        );

        return $this->where($column, $operator, $value, 'or');
    }

    /**
     * @param $column
     * @param array $values
     * @param string $boolean
     * @param bool $not
     * @return $this
     */
    public function whereBetween($column, array $values, $boolean = 'and', $not = false)
    {
        $type = 'between';

        $this->wheres[] = compact('type', 'column', 'values', 'boolean', 'not');

        return $this;
    }

    /**
     * @param $column
     * @param array $values
     * @return Query
     */
    public function orWhereBetween($column, array $values)
    {
        return $this->whereBetween($column, $values, 'or');
    }

    /**
     * @param $column
     * @param array $values
     * @param string $boolean
     * @return Query
     */
    public function whereNotBetween($column, array $values, $boolean = 'and')
    {
        return $this->whereBetween($column, $values, $boolean, true);
    }

    /**
     * @param $column
     * @param array $values
     * @return Query
     */
    public function orWhereNotBetween($column, array $values)
    {
        return $this->whereNotBetween($column, $values, 'or');
    }

    /**
     * @param $column
     * @param string $boolean
     * @param bool $not
     * @return $this
     */
    public function whereNull($column, $boolean = 'and', $not = false)
    {
        $type = $not ? 'NotNull' : 'Null';

        $this->wheres[] = compact('type', 'column', 'boolean');

        return $this;
    }

    /**
     * @param $column
     * @param string $boolean
     * @return mixed
     */
    public function whereNotNull($column, $boolean = 'and')
    {
        return $this->whereNull($column, $boolean, true);
    }

    /**
     * @param  string  $column
     * @return Query
     */
    public function orWhereNotNull($column)
    {
        return $this->whereNotNull($column, 'or');
    }

    /**
     * @param $column
     * @param $values
     * @param string $boolean
     * @param bool $not
     * @return $this
     */
    public function whereIn($column, $values, $boolean = 'and', $not = false)
    {
        $type = $not ? 'NotIn' : 'In';

        if ($values instanceof Arrayable) {
            $values = $values->toArray();
        }
        $this->wheres[] = compact('type', 'column', 'values', 'boolean');

        return $this;
    }

    /**
     * @param $column
     * @param $values
     * @return Query
     */
    public function orWhereIn($column, $values)
    {
        return $this->whereIn($column, $values, 'or');
    }

    /**
     * @param $column
     * @param $values
     * @param string $boolean
     * @return Query
     */
    public function whereNotIn($column, $values, $boolean = 'and')
    {
        return $this->whereIn($column, $values, $boolean, true);
    }

    /**
     * @param $column
     * @param $values
     * @return Query
     */
    public function orWhereNotIn($column, $values)
    {
        return $this->whereNotIn($column, $values, 'or');
    }

    /**
     * @param $type
     * @param $column
     * @param $operator
     * @param $value
     * @param string $boolean
     * @return $this
     */
    protected function addDateBasedWhere($type, $column, $operator, $value, $boolean = 'and')
    {
        $this->wheres[] = compact('column', 'type', 'boolean', 'operator', 'value');

        return $this;
    }

    /**
     * @param $column
     * @param $operator
     * @param null $value
     * @param string $boolean
     * @return Query
     */
    public function whereDate($column, $operator, $value = null, $boolean = 'and')
    {
        [$value, $operator] = $this->prepareValueAndOperator(
            $value, $operator, func_num_args() === 2
        );

        if ($value instanceof DateTimeInterface) {
            $value = $value->format('Y-m-d');
        }

        return $this->addDateBasedWhere('Date', $column, $operator, $value, $boolean);
    }

    /**
     * @param $column
     * @param $operator
     * @param null $value
     * @return Query
     */
    public function orWhereDate($column, $operator, $value = null)
    {
        [$value, $operator] = $this->prepareValueAndOperator(
            $value, $operator, func_num_args() === 2
        );

        return $this->whereDate($column, $operator, $value, 'or');
    }

    /**
     * @param $column
     * @param $operator
     * @param null $value
     * @param string $boolean
     * @return Query
     */
    public function whereTime($column, $operator, $value = null, $boolean = 'and')
    {
        [$value, $operator] = $this->prepareValueAndOperator(
            $value, $operator, func_num_args() === 2
        );

        if ($value instanceof DateTimeInterface) {
            $value = $value->format('H:i:s');
        }

        return $this->addDateBasedWhere('Time', $column, $operator, $value, $boolean);
    }

    /**
     * @param $column
     * @param $operator
     * @param null $value
     * @return Query
     */
    public function orWhereTime($column, $operator, $value = null)
    {
        [$value, $operator] = $this->prepareValueAndOperator(
            $value, $operator, func_num_args() === 2
        );

        return $this->whereTime($column, $operator, $value, 'or');
    }

    /**
     * @param $column
     * @param $operator
     * @param null $value
     * @param string $boolean
     * @return Query
     */
    public function whereDay($column, $operator, $value = null, $boolean = 'and')
    {
        [$value, $operator] = $this->prepareValueAndOperator(
            $value, $operator, func_num_args() === 2
        );

        if ($value instanceof DateTimeInterface) {
            $value = $value->format('d');
        }

        return $this->addDateBasedWhere('Day', $column, $operator, $value, $boolean);
    }

    /**
     * @param  string  $column
     * @param  string  $operator
     * @param  \DateTimeInterface|string  $value
     * @return Query|static
     */
    public function orWhereDay($column, $operator, $value = null)
    {
        [$value, $operator] = $this->prepareValueAndOperator(
            $value, $operator, func_num_args() === 2
        );
        return $this->addDateBasedWhere('Day', $column, $operator, $value, 'or');
    }

    /**
     * @param  string  $column
     * @param  string  $operator
     * @param  \DateTimeInterface|string  $value
     * @param  string  $boolean
     * @return Query|static
     */
    public function whereMonth($column, $operator, $value = null, $boolean = 'and')
    {
        [$value, $operator] = $this->prepareValueAndOperator(
            $value, $operator, func_num_args() === 2
        );

        if ($value instanceof DateTimeInterface) {
            $value = $value->format('m');
        }

        return $this->addDateBasedWhere('Month', $column, $operator, $value, $boolean);
    }

    /**
     * @param  string  $column
     * @param  string  $operator
     * @param  \DateTimeInterface|string  $value
     * @return Query|static
     */
    public function orWhereMonth($column, $operator, $value = null)
    {
        [$value, $operator] = $this->prepareValueAndOperator(
            $value, $operator, func_num_args() === 2
        );

        return $this->addDateBasedWhere('Month', $column, $operator, $value, 'or');
    }

    /**
     * @param  string  $column
     * @param  string  $operator
     * @param  \DateTimeInterface|string|int  $value
     * @param  string  $boolean
     * @return Query|static
     */
    public function whereYear($column, $operator, $value = null, $boolean = 'and')
    {
        [$value, $operator] = $this->prepareValueAndOperator(
            $value, $operator, func_num_args() === 2
        );

        if ($value instanceof DateTimeInterface) {
            $value = $value->format('Y');
        }

        return $this->addDateBasedWhere('Year', $column, $operator, $value, $boolean);
    }

    /**
     * @param  string  $column
     * @param  string  $operator
     * @param  \DateTimeInterface|string|int  $value
     * @return Query
     */
    public function orWhereYear($column, $operator, $value = null)
    {
        [$value, $operator] = $this->prepareValueAndOperator(
            $value, $operator, func_num_args() === 2
        );

        return $this->addDateBasedWhere('Year', $column, $operator, $value, 'or');
    }

    /**
     * @param $raw
     * @param string $boolean
     * @return $this
     */
    public function whereRaw($raw, $boolean = 'and')
    {
        $this->wheres[] = ['type' => 'raw', 'raw' => $raw, 'boolean' => $boolean];

        return $this;
    }

    /**
     * @param $sql
     * @return Query
     */
    public function orWhereRaw($sql)
    {
        return $this->whereRaw($sql, 'or');
    }

    /**
     * @param $table
     * @param $first
     * @param null $operator
     * @param null $second
     * @param string $type
     * @return $this
     */
    public function join($table, $first, $operator = null, $second = null, $type = 'inner')
    {
        if(is_null($second)) {
            $second = $operator;
            $operator = "=";
        }

        if($this->invalidOperator($operator)) {
            $operator = "=";
        }

        $this->joins[] = compact(
            'type', 'table', 'first', 'operator', 'second'
        );

        return $this;
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
        return $this->join($table, $first, $operator, $second, 'left');
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
        return $this->join($table, $first, $operator, $second, 'right');
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
        return $this->join($table, $first, $operator, $second, 'cross');
    }

    /**
     * @return string
     */
    public function toSql()
    {
        if(!empty($this->sets)) {
            return $this->getUpdateSql();
        }

        return $this->getSelectSql();
    }

    /**
     * @param array $values
     * @return Query
     */
    public function set(array $values)
    {
        foreach ($values as $key => $value) {
            $this->sets[$key] = $value;
        }

        return $this;
    }

    /**
     * @param array $values
     * @return string
     */
    public function update(array $values = [])
    {
        if(!empty($values))
            $this->set($values);

        return $this->db->query($this->getUpdateSql());
    }

    /**
     * @param array $columns
     * @return DBCollection
     */
    public function get(array $columns = ['*'])
    {
        if($columns != ["*"]) {
            $this->select(...$columns);
        }

        $sql = $this->getSelectSql();

        return Builder::collection($this->db->getAll($sql), $this);
    }

    /**
     * @param array $columns
     * @return array|FALSE
     */
    public function first(array $columns = ['*'])
    {
        if($columns != ["*"]) {
            $this->select(...$columns);
        }

        $sql = $this->getSelectSql();

        return $this->db->getRow($sql);
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
        $result = $this->first($columns);

        if($this->db->affectedRows() == 0)
            throw new $exception($code, $message);

        return $result;
    }

    /**
     * @param $column
     * @return mixed|null
     */
    public function value($column)
    {
        $this->columns = [];

        $result = (array) $this->first([$column]);

        return count($result) > 0 ? reset($result) : null;
    }

    /**
     * @param string $column
     * @return FALSE|int
     */
    public function count($column = "*")
    {
        return (int) $this->getAggregate($column, __FUNCTION__);
    }

    /**
     * @param $column
     * @return FALSE|mixed
     */
    public function max($column)
    {
        return $this->getAggregate($column, __FUNCTION__);
    }

    /**
     * @param $column
     * @return FALSE|mixed
     */
    public function min($column)
    {
        return $this->getAggregate($column, __FUNCTION__);
    }

    /**
     * @param $column
     * @return FALSE|mixed
     */
    public function avg($column)
    {
        return $this->getAggregate($column, __FUNCTION__);
    }

    /**
     * Alias for the "avg" method.
     *
     * @param $column
     * @return FALSE|mixed
     */
    public function average($column)
    {
        return $this->avg($column);
    }

    /**
     * @param $column
     * @return FALSE|mixed
     */
    public function sum($column)
    {
        return $this->getAggregate($column, __FUNCTION__);
    }

    /**
     * @return FALSE|resource
     */
    public function delete()
    {
        $wheres = is_array($this->wheres) ? $this->getWheres() : '';

        $limit = $this->getLimit();

        $sql = trim("delete from {$this->getTable(true)} $wheres $limit");

        return $this->db->query($sql);
    }

    /**
     * @param $column
     * @param int $amount
     * @param array $extra
     * @return string
     */
    public function increment($column, $amount = 1, array $extra = [])
    {
        if (! is_numeric($amount)) {
            throw new InvalidArgumentException('Передано не числовое значение в метод инкремента.');
        }

        $wrapped = $this->getColumn($column);

        $columns = array_merge([$column => $this->raw("$wrapped + $amount")], $extra);

        return $this->update($columns);
    }

    /**
     * @param $column
     * @param int $amount
     * @param array $extra
     * @return string
     */
    public function decrement($column, $amount = 1, array $extra = [])
    {
        if (! is_numeric($amount)) {
            throw new InvalidArgumentException('Передано не числовое значение в метод декрмента.');
        }

        $wrapped = $this->getColumn($column);

        $columns = array_merge([$column => $this->raw("$wrapped - $amount")], $extra);

        return $this->update($columns);
    }

    /**
     * @return FALSE|resource
     */
    public function truncate()
    {
        $tables = $this->getTable();

        $sql = "truncate $tables";

        return $this->db->query($sql);
    }

    /**
     * @param $value
     * @return RawQuery
     */
    public function raw($value)
    {
        return RawQuery::raw($value);
    }

    /**
     * @return string
     */
    protected function getJoins()
    {
        return collect($this->joins)->map(function ($join) {
            $table = $this->isRaw($join['table']) ? $this->getValue($join['table']) : $join['table'];

            $first = $this->isRaw($join['first']) ? $this->getValue($join['first']) : $this->getColumn($join['first']);

            $second = $this->isRaw($join['second']) ? $this->getValue($join['second']) : $this->getColumn($join['second']);

            return trim("{$join['type']} join {$table} on {$first} {$join['operator']} {$second}");
        })->implode(' ');
    }

    /**
     * @param $column
     * @param $function
     * @return FALSE|mixed
     */
    protected function getAggregate($column, $function)
    {
        $column = $this->getColumn($column);

        $this->select($this->raw($function . "({$column}) as aggregate"));

        $sql = $this->getSelectSql();

        return $this->db->getOne($sql);
    }

    /**
     * @return string
     */
    protected function getUpdateSql()
    {
        $table = $this->getTable(true);

        $columns = collect($this->sets)->map(function ($value, $key) {
            return $this->getColumn($key).' = '.$this->getParameter($value);
        })->implode(', ');

        $wheres = $this->getWheres();

        $limit = $this->getLimit();

        return trim("update {$table} set $columns $wheres $limit");
    }

    /**
     * @return string
     */
    protected function getSelectSql()
    {
        $columns = $this->columnize($this->columns);

        $columns = empty($columns) ? "*" : $columns;

        $table = $this->getTable();

        $joins = $this->getJoins();

        $wheres = $this->getWheres();

        $groups = $this->columnize($this->groups);

        $having = $this->getHavings();

        $orders = $this->getOrders();

        $limit = $this->getLimit();

        return trim("select {$columns} from {$table} $joins $wheres $groups $having $orders $limit");
    }

    /**
     * @param bool $first
     * @return string
     */
    public function getTable($first = false)
    {
        if($first === true) {
            return Arr::first($this->tables);
        }

        return implode(", ", $this->tables);
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function isRaw($value)
    {
        return $value instanceof RawQuery;
    }

    /**
     * @param RawQuery $raw
     * @return string
     */
    public function getValue($raw)
    {
        return $raw->getQuery();
    }

    /**
     * @param $value
     * @return string
     */
    public function getParameter($value)
    {
        if (is_array($value)) {
            return implode(', ', array_map([__CLASS__, __FUNCTION__], $value));
        }

        if($this->isRaw($value)) {
            return $this->getValue($value);
        }

        return "'$value'";
    }

    /**
     * @param $value
     * @return string
     */
    public function getColumn($value)
    {
        if($this->isRaw($value)) {
            return $this->getValue($value);
        }

        if(false !== mb_stripos($value, ".")) {
            [$table, $column] = explode(".", $value);

            return $table . "." . $this->getColumn($column);
        }

        return $value;
    }

    /**
     * @param array $columns
     * @return string
     */
    public function columnize(array $columns)
    {
        return implode(', ',  array_map([$this, 'getColumn'], $columns));
    }

    /**
     * @param array $values
     * @return string
     */
    public function parameterize(array $values)
    {
        return implode(', ', array_map([$this, 'getParameter'], $values));
    }

    /**
     * @return string
     */
    protected function getOrders()
    {
        if (! empty($this->orders)) {
            return 'order by '.implode(', ', $this->getOrdersToArray($this->orders));
        }

        return '';
    }

    /**
     * @param  array $orders
     * @return array
     */
    protected function getOrdersToArray($orders)
    {
        return array_map(function ($order) {
            return ! isset($order['sql'])
                ? $this->getColumn($order['column']).' '.$order['direction']
                : $order['sql'];
        }, $orders);
    }

    /**
     * @param $having
     * @return string
     */
    protected function getBasicHaving($having)
    {
        $column = $this->getColumn($having['column']);

        $parameter = $this->getParameter($having['value']);

        return $having['boolean'].' '.$column.' '.$having['operator'].' '.$parameter;
    }

    /**
     * @param $having
     * @return string
     */
    protected function getHavingBetween($having)
    {
        $between = $having['not'] ? 'not between' : 'between';

        $column = $this->getColumn($having['column']);

        $min = $this->getParameter(reset($having['values']));

        $max = $this->getParameter(end($having['values']));

        return $having['boolean'].' '.$column.' '.$between.' '.$min.' and '.$max;
    }

    /**
     * @param array $having
     * @return string
     */
    protected function getHaving(array $having)
    {
        if ($having['type'] === 'Raw') {
            return $having['boolean'].' '.$having['sql'];
        } elseif ($having['type'] === 'between') {
            return $this->getHavingBetween($having);
        }

        return $this->getBasicHaving($having);
    }

    /**
     * @return string
     */
    protected function getHavings()
    {
        if(empty($this->havings))
            return '';

        $sql = implode(' ', array_map([$this, 'getHaving'], $this->havings));

        return 'having '. $this->removeLeadingBoolean($sql);
    }

    /**
     * @return string
     */
    protected function getLimit()
    {
        if(is_null($this->limit) && is_null($this->offset))
            return '';

        if(is_null($this->offset) && !is_null($this->limit))
            return 'limit '. $this->limit;

        if(is_null($this->limit) && !is_null($this->offset))
            return 'offset '. $this->offset;

        return 'limit'. $this->offset .' '. $this->limit;
    }

    /**
     * @param  string  $value
     * @param  string  $operator
     * @param  bool  $useDefault
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    public function prepareValueAndOperator($value, $operator, $useDefault = false)
    {
        if ($useDefault) {
            return [$operator, '='];
        } elseif ($this->invalidOperatorAndValue($operator, $value)) {
            throw new InvalidArgumentException('Недопустимая комбинация оператора и значения.');
        }

        return [$value, $operator];
    }

    /**
     * @param  string  $operator
     * @param  mixed  $value
     * @return bool
     */
    protected function invalidOperatorAndValue($operator, $value)
    {
        return is_null($value) && in_array($operator, $this->operators) &&
                !in_array($operator, ['=', '<>', '!=']);
    }

    /**
     * @param  string  $operator
     * @return bool
     */
    protected function invalidOperator($operator)
    {
        return !in_array(strtolower($operator), $this->operators, true) &&
                !in_array(strtolower($operator), $this->operators, true);
    }

    /**
     * @param $wheres
     * @return array
     */
    protected function getWheresToArray($wheres)
    {
        return collect($wheres)->map(function ($where) {
            return $where['boolean'].' '.$this->{"getWhere{$where['type']}"}($where);
        })->all();
    }

    /**
     * @param $value
     * @return null|string|string[]
     */
    protected function removeLeadingBoolean($value)
    {
        return preg_replace('/and |or /i', '', $value, 1);
    }

    /**
     * @param $sql
     * @return string
     */
    protected function concatenateWhereClauses($sql)
    {
        return 'where '.$this->removeLeadingBoolean(implode(' ', $sql));
    }

    /**
     * @return string
     */
    protected function getWheres()
    {
        if (empty($this->wheres)) {
            return '';
        }

        if (count($sql = $this->getWheresToArray($this->wheres)) > 0) {
            return $this->concatenateWhereClauses($sql);
        }

        return '';
    }

    /**
     * @param  array  $where
     * @return string
     */
    protected function getWhereRaw($where)
    {
        return $where['sql'];
    }

    /**
     * @param  array  $where
     * @return string
     */
    protected function getWhereBasic($where)
    {
        $value = $this->getParameter($where['value']);

        return $this->getColumn($where['column']).' '.$where['operator'].' '.$value;
    }

    /**
     * @param  array  $where
     * @return string
     */
    protected function getWhereIn($where)
    {
        if (!empty($where['values'])) {
            return $this->getColumn($where['column']).' in ('.$this->parameterize($where['values']).')';
        }

        return '0 = 1';
    }

    /**
     * @param  array  $where
     * @return string
     */
    protected function getWhereNotIn($where)
    {
        if (! empty($where['values'])) {
            return $this->getColumn($where['column']).' not in ('.$this->parameterize($where['values']).')';
        }

        return '1 = 1';
    }

    /**
     * @param  array  $where
     * @return string
     */
    protected function getWhereNotInRaw($where)
    {
        if (! empty($where['values'])) {
            return $this->getColumn($where['column']).' not in ('.implode(', ', $where['values']).')';
        }

        return '1 = 1';
    }

    /**
     * @param  array  $where
     * @return string
     */
    protected function getWhereInRaw($where)
    {
        if (!empty($where['values'])) {
            return $this->getColumn($where['column']).' in ('.implode(', ', $where['values']).')';
        }

        return '0 = 1';
    }

    /**
     * @param  array  $where
     * @return string
     */
    protected function getWhereNull($where)
    {
        return $this->getColumn($where['column']).' is null';
    }

    /**
     * @param  array  $where
     * @return string
     */
    protected function getWhereNotNull($where)
    {
        return $this->getColumn($where['column']).' is not null';
    }

    /**
     * @param  array  $where
     * @return string
     */
    protected function getWhereBetween($where)
    {
        $between = $where['not'] ? 'not between' : 'between';

        $min = $this->getParameter(reset($where['values']));

        $max = $this->getParameter(end($where['values']));

        return $this->getColumn($where['column']).' '.$between.' '.$min.' and '.$max;
    }

    /**
     * @param  array  $where
     * @return string
     */
    protected function getWhereDate($where)
    {
        return $this->getDateBasedWhere('date', $where);
    }

    /**
     * @param  array  $where
     * @return string
     */
    protected function getWhereTime($where)
    {
        return $this->getDateBasedWhere('time', $where);
    }
    /**
     * Compile a "where day" clause.
     *
     * @param  array  $where
     * @return string
     */
    protected function getWhereDay($where)
    {
        return $this->getDateBasedWhere('day', $where);
    }

    /**
     * @param  array  $where
     * @return string
     */
    protected function getWhereMonth($where)
    {
        return $this->getDateBasedWhere('month', $where);
    }

    /**
     * @param  array  $where
     * @return string
     */
    protected function getWhereYear($where)
    {
        return $this->getDateBasedWhere('year', $where);
    }

    /**
     * @param  string  $type
     * @param  array  $where
     * @return string
     */
    protected function getDateBasedWhere($type, $where)
    {
        $value = $this->getParameter($where['value']);

        return $type.'('.$this->getColumn($where['column']).') '.$where['operator'].' '.$value;
    }

    /**
     * @param  array  $where
     * @return string
     */
    protected function whereColumn($where)
    {
        return $this->getColumn($where['first']).' '.$where['operator'].' '.$this->getColumn($where['second']);
    }
}