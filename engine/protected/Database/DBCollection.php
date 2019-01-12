<?php
/*
* MyUCP
*/

class DBCollection extends Collection
{
    /**
     * @var Query
     */
    protected $query;

    /**
     * DBCollection constructor.
     *
     * @param array $items
     * @param Query $query
     */
    public function __construct($items = [], Query $query)
    {
        parent::__construct($items);

        $this->query = $query;
    }

    /**
     * @return string
     */
    public function toSql()
    {
        return $this->query->toSql();
    }

    /**
     * @return Query
     */
    public function getQuery()
    {
        return $this->query;
    }
}