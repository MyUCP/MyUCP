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
     * Query constructor.
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
     * @param string[] ...$tables
     * @return Query
     */
    public static function table($table, ...$tables)
    {
        return (new self())->from($table, ...$tables);
    }

    /**
     * @param string $table
     * @param string[] ...$tables
     * @return Query
     */
    public function from($table, ...$tables)
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
        if(!empty($tables)) {
            foreach ($tables as $table) {
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
        $query = new RawQuery('('. $table .') as ' . $as);

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
     * @param  array|mixed $columns
     * @return $this
     */
    public function select($column = "*", ...$columns)
    {
        // Определяем, является ли переданное значение выражением
        // если оно таковым есть, получаем его значение
        if($this->isRaw($column)) {
            $this->columns[] = $this->getValue($column);
        } else {
            $this->columns[] = $column;
        }

        // Если остальные переданные параметры не пусты
        // то добавляем их как отдельные элементы
        if(!empty($columns)) {
            foreach ($columns as $column) {
                if($this->isRaw($column)) {
                    $this->columns[] = $this->getValue($column);
                } else {
                    $this->columns[] = $column;
                }
            }
        }

        return $this;
    }

    /**
     * @param $column
     * @param mixed ...$columns
     * @return Query
     */
    public function addSelect($column, ...$columns)
    {
        return $this->select($column, ...$columns);
    }

    /**
     * @param string $column
     * @param string $as
     * @return Query
     */
    public function selectAs($column, $as)
    {
        $query = new RawQuery('('. $column .') as ' . $as);

        return $this->select($query);
    }

    /**
     * @param string $raw
     * @return Query
     */
    public function selectRaw($raw)
    {
        return $this->select(new RawQuery($raw));
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
            $value = new RawQuery($value ? 'true' : 'false');
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
     * @return string
     */
    public function toSql()
    {

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
     * @param array $columns
     * @return string
     */
    public function columnize(array $columns)
    {
        return implode(', ', $columns);
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
}